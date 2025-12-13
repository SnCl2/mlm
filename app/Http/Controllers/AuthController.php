<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Cache;

use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; 
use App\Models\BinaryNode;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use App\Models\UserKyc;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
    
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();
    
            // // 1. Check if user is inactive
            // if (!$user->is_active) {
            //     Auth::logout();
            //     return back()->withErrors([
            //         'email' => 'Your account is inactive. Please contact support.',
            //     ])->onlyInput('email');
            // }
    
            // 2. Check if user has KYC
            // $kyc = $user->kyc;
    
            // if (!$kyc) {
            //     return redirect()->route('kyc.create');
            // }
    
            // // 3. Check if KYC is not approved
            // if ($kyc->status !== 'approved') {
            //     return redirect()->route('kyc.edit');
            // }
    
            // 4. All good → redirect to dashboard
            return redirect()->intended('/showEarnings');
        }
    
        // Invalid login
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        $products = Product::all();
        return view('auth.register', compact('products'));
    }


    public function register(Request $request)
    {
        // Handle position parameter (from tree modal) and map to side
        if ($request->has('position') && !$request->has('side')) {
            $request->merge(['side' => $request->position]);
        }

        $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|string|email|max:255|unique:users',
            'email_confirmation' => 'required|same:email',
            'phone'             => 'nullable|string|max:15',
            'place_under'       => 'required|exists:users,referral_code',
            'side'              => 'required|in:left,right',
            'referred_by'       => 'required|string|exists:users,referral_code',
            'product_id'        => 'required|exists:products,id',
        ], [
            'email_confirmation.same' => 'Email confirmation does not match.',
            'place_under.exists' => 'The placement referral code does not exist.',
            'referred_by.exists' => 'The referral code does not exist.',
            'side.required' => 'Please select a placement side (Left or Right).',
            'side.in' => 'Placement side must be either left or right.',
        ]);
    
        DB::beginTransaction();
    
        try {
            // Find referrer by referral code
            $referrer = User::where('referral_code', $request->referred_by)->first();
            if (!$referrer) {
                throw new \Exception('Referrer not found with code: ' . $request->referred_by);
            }

            $parent = User::where('referral_code', $request->place_under)->first();
            if (!$parent) {
                throw new \Exception('Parent user not found with code: ' . $request->place_under);
            }

            // Check if position is already taken
            $existingNode = BinaryNode::where('parent_id', $parent->id)
                ->where('position', $request->side)
                ->first();
            
            if ($existingNode) {
                // Get the user who occupies this position
                $existingUser = User::find($existingNode->user_id);
                $existingUserName = $existingUser ? $existingUser->name : 'Unknown User';
                
                // Check which side is available
                $leftTaken = BinaryNode::where('parent_id', $parent->id)
                    ->where('position', 'left')
                    ->exists();
                $rightTaken = BinaryNode::where('parent_id', $parent->id)
                    ->where('position', 'right')
                    ->exists();
                
                $availableSide = !$leftTaken ? 'left' : (!$rightTaken ? 'right' : null);
                
                if ($availableSide) {
                    throw new \Exception("Position '{$request->side}' is already taken by '{$existingUserName}'. Please choose the '{$availableSide}' side instead.");
                } else {
                    throw new \Exception("Both positions under this parent are already taken. Please choose a different parent.");
                }
            }

            // Generate a secure password
            $generatedPassword = Str::random(10);
    
            // Create the new user
            $user = User::create([
                'name'            => $request->name,
                'email'           => $request->email,
                'phone'           => $request->phone,
                'password'        => Hash::make($generatedPassword),
                'referred_by'     => $referrer->id,
                'place_under'     => $parent->id,
                'placement_leg'   => $request->side,
                'is_kyc_verified' => false,
                'is_active'       => false,
                'product_id'      => $request->product_id,
            ]);
    
            // Generate referral code after getting ID
            $user->referral_code = 'DLM' . $user->id  . str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $user->save();
    
            // Create binary node - this is required when place_under and side are provided
            BinaryNode::create([
                'user_id'   => $user->id,
                'parent_id' => $parent->id,
                'position'  => $request->side,
                'left_points' => 0,
                'right_points' => 0,
                'cb_left' => 0,
                'cb_right' => 0,
            ]);
    
            // Send email
            Mail::raw(
                "Welcome to Dream Life Management!\n\n" .
                "Your account has been successfully created.\n" .
                "Referral Code: {$user->referral_code}\n" .
                "Password: {$generatedPassword}\n\n" .
                "Please login and change your password.\n" .
                "Click the link to login: https://dreamlifemanagement.in/login",
                function ($message) use ($user) {
                    $message->to($user->email)
                            ->subject('Welcome to Dream Life Management');
                }
            );

    
            if (!Auth::check()) {
                Auth::login($user);
            }
            
            DB::commit();
    
            return redirect('/showEarnings')->with('success', 'Registration successful! Login details sent to your email.');
    
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()]);
        }
    }
    
    public function resendPasswordform()
    {
        return view('auth.resendPassword');
    }
    
    public function resendPassword(Request $request)
    {
        // STEP 1: Sending OTP
        if ($request->has(['referral_code', 'email']) && !$request->has('otp')) {
            $request->validate([
                'referral_code' => 'required|string|exists:users,referral_code',
                'email'         => 'required|email',
            ]);
    
            $user = User::where('referral_code', $request->referral_code)
                        ->where('email', $request->email)
                        ->first();
    
            if (!$user) {
                return back()->withErrors(['error' => 'User not found with given Referral ID and Email.']);
            }
    
            $otp = rand(100000, 999999);
            Cache::put("otp_{$user->id}", $otp, now()->addMinutes(10));
    
            Mail::raw(
                "Hello {$user->name},\n\nYour OTP for password reset is: {$otp}\nIt is valid for 10 minutes.",
                function ($message) use ($user) {
                    $message->to($user->email)
                            ->subject('OTP for Password Reset - Dream Life Management');
                }
            );
            session()->flash('otp_sent', true);
            return back()->with('success', 'OTP sent to your email.');
        }
    
        // STEP 2: Validating OTP and Updating Password
        if ($request->has(['referral_code', 'email', 'otp', 'password', 'password_confirmation'])) {
            $request->validate([
                'referral_code'         => 'required|string|exists:users,referral_code',
                'email'                 => 'required|email',
                'otp'                   => 'required|numeric',
                'password'              => 'required|min:8|confirmed',
            ]);
    
            $user = User::where('referral_code', $request->referral_code)
                        ->where('email', $request->email)
                        ->first();
    
            if (!$user) {
                return back()->withErrors(['error' => 'User not found.']);
            }
    
            $cachedOtp = Cache::get("otp_{$user->id}");
    
            if (!$cachedOtp || $cachedOtp != $request->otp) {
                return back()->withErrors(['error' => 'Invalid or expired OTP.']);
            }
    
            // OTP valid — update password
            $user->password = Hash::make($request->password);
            $user->save();
            Cache::forget("otp_{$user->id}");
    
            return redirect()->route('login')->with('success', 'Password reset successfully.');
        }
    
        return back()->withErrors(['error' => 'Invalid request.']);
    }

    
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
    
    
}
