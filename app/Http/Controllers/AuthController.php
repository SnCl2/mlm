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
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|string|email|max:255|unique:users',
            'phone'       => 'nullable|string|max:15',
            'place_under' => 'required|exists:users,referral_code',
            'side'        => 'nullable|in:left,right',
            'referred_by' => 'required|string|exists:users,referral_code',
            'product_id'  => 'required|exists:products,id',
        ]);
    
        DB::beginTransaction();
    
        try {
            // Find referrer by referral code
            $referrer = User::where('referral_code', $request->referred_by)->first();
            $parent = User::where('referral_code', $request->place_under)->first();
            // Generate a secure password
            $generatedPassword = Str::random(10);
    
            // Create the new user
            $user = User::create([
                'name'            => $request->name,
                'email'           => $request->email,
                'phone'           => $request->phone,
                'password'        => Hash::make($generatedPassword),
                'referred_by'     => $referrer?->id,
                'place_under'     => $parent?->id,
                'placement_leg'   => $request->side,
                'is_kyc_verified' => false,
                'is_active'       => false,
                'product_id'      => $request->product_id,
            ]);
    
            // Generate referral code after getting ID
            $user->referral_code = 'DCM_' . str_pad($user->id, 3, '0', STR_PAD_LEFT);
            $user->save();
    
            // Create binary node if needed
            if ($request->place_under && $request->side) {
                BinaryNode::create([
                    'user_id'   => $user->id,
                    'parent_id' => $parent?->id,
                    'position'  => $request->side,
                ]);
            }
    
            // Send email
            Mail::raw(
                "Welcome to Digital Care MLM!\n\n" .
                "Your account has been successfully created.\n" .
                "Referral Code: {$user->referral_code}\n" .
                "Password: {$generatedPassword}\n\n" .
                "Please login and change your password.\n" .
                "Click the link to login: https://dcmlm.in/login",
                function ($message) use ($user) {
                    $message->to($user->email)
                            ->subject('Welcome to Digital Care MLM');
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
                            ->subject('OTP for Password Reset - Digital Care MLM');
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
