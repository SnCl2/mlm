<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Shop;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\ShopCommission;
use Illuminate\Support\Facades\Mail;

class ShopController extends Controller
{
    public function showLoginForm()
    {
        return view('shop.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $credentials['is_active'] = true;
    
        // Find user record by email
        $user = \App\Models\Shop::where('email', $request->email)->first();
    
        if (!$user) {
            dd('Email does not exist');
        }
    
        if (!$user->is_active) {
            dd('Shop is not active');
        }
    
        if (!\Hash::check($request->password, $user->password)) {
            dd('Password does not match');
        }
    
        // Now credentials should be correct, attempt to login
        if (Auth::guard('shop')->attempt($credentials)) {
            return redirect()->intended('/shop/dashboard');
        }
    
        return back()->withErrors(['error' => 'Unknown authentication error']);
    }


    public function logout()
    {
        Auth::guard('shop')->logout();
        return redirect()->route('shop.login');
    }

    public function dashboard()
    {
        return view('shop.dashboard');
    }
    
    public function create()
    {
        return view('shop.create'); // Create form view
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'owner_name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'email' => 'required|email|unique:shops,email',
            'address' => 'required|string',
            'aadhar_number' => 'required|digits:12',
            'pan_number' => 'required|alpha_num|size:10',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'aadhar_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'pan_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
    
        DB::beginTransaction();
    
        try {
            // Generate random password
            $generatedPassword = Str::random(10);
            $validated['password'] = Hash::make($generatedPassword);
            $validated['is_active'] = !true;
    
            // Handle Aadhar image upload
            if ($request->hasFile('aadhar_image')) {
                $validated['aadhar_image_path'] = $request->file('aadhar_image')->store('uploads/shops/aadhar', 'public');
            }
    
            // Handle PAN image upload
            if ($request->hasFile('pan_image')) {
                $validated['pan_image_path'] = $request->file('pan_image')->store('uploads/shops/pan', 'public');
            }
    
            // Create shop
            $shop = Shop::create($validated);
    
            // // Generate shop code like SHOP_001
            // $shop->shop_code = 'SHOP_' . str_pad($shop->id, 3, '0', STR_PAD_LEFT);
            $shop->save();
    
            // Send welcome email with generated credentials
            Mail::raw(
                "Welcome to the platform!\n\n" .
                "Your shop account has been created.\n" .
                "Email: {$shop->email}\n" .
                "Password: {$generatedPassword}\n\n" .
                "Login URL: https://dreamlifemanagrmrnt.in/shop/login\n" .
                "Please login and change your password after first login.",
                function ($message) use ($shop) {
                    $message->to($shop->email)
                            ->subject('Welcome to the Shop Platform');
                }
            );
    
            DB::commit();
    
            return back()->with('success', 'Shop user created successfully! Login details sent to email.');
        } catch (\Exception $e) {
            DB::rollBack();
    
            Log::error('Shop Store Error: '.$e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
    
            return back()->withErrors('Something went wrong. Please try again.')->withInput();
        }
    }


    
    public function index()
    {
        $shops = Shop::with('commission')->latest()->get();
        return view('shop.index', compact('shops'));
    }

    
    // ShopController.php

    public function edit($id)
    {
        $shop = Shop::findOrFail($id);
        return view('shop.create', compact('shop'));
    }
    
    public function update(Request $request, $id)
    {
        $shop = Shop::findOrFail($id);
    
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'owner_name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'address' => 'required|string',
            'aadhar_number' => 'required|digits:12',
            'pan_number' => 'required|alpha_num|size:10',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'aadhar_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'pan_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
    
        if ($request->hasFile('aadhar_image')) {
            $validated['aadhar_image_path'] = $request->file('aadhar_image')->store('uploads/shops/aadhar', 'public');
        }
    
        if ($request->hasFile('pan_image')) {
            $validated['pan_image_path'] = $request->file('pan_image')->store('uploads/shops/pan', 'public');
        }
    
        $shop->update($validated);
    
        return redirect()->route('management.shops.index')->with('success', 'Shop updated successfully!');
    }

    public function destroy(Shop $shop)
    {
        $shop->delete();
        return redirect()->route('management.shops.index')->with('success', 'Shop deleted successfully.');
    }
    
    public function changePassword(Request $request, Shop $shop)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);
    
        $shop->password = Hash::make($request->password);
        $shop->save();
    
        return redirect()->route('management.shops.index')->with('success', 'Password updated successfully.');
    }

    public function toggleStatus(Shop $shop)
    {
        try {
            $shop->is_active = !$shop->is_active;
            $shop->save();
    
            $status = $shop->is_active ? 'activated' : 'deactivated';
    
            return back()->with('success', "Shop has been {$status} successfully.");
        } catch (\Exception $e) {
            return back()->withErrors('Failed to update shop status.');
        }
    }
    
    

    public function deductCommission(Request $request, $shopId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'confirm_amount' => 'required|numeric|same:amount',
        ]);
    
        $amount = $request->amount;
    
        $shopCommission = ShopCommission::where('shop_id', $shopId)->first();
    
        if (!$shopCommission) {
            return redirect()->back()->with('error', 'Commission record not found.');
        }
    
        $newTotal = max(0, $shopCommission->total_commission - $amount);
        $shopCommission->total_commission = $newTotal;
        $shopCommission->save();
    
        $shop = $shopCommission->shop; // assuming relation exists
    
        // Send email to the shop
        if ($shop && $shop->email) {
            Mail::raw(
                "Dear {$shop->owner_name},\n\n" .
                "A commission payment of ₹" . number_format($amount, 2) . " has been deducted from your account.\n" .
                "Remaining Commission Balance: ₹" . number_format($newTotal, 2) . "\n\n" .
                "If you have any questions, please contact management.",
                function ($message) use ($shop) {
                    $message->to($shop->email)
                            ->subject('Commission Payment Confirmation');
                }
            );
        }
    
        return redirect()->back()->with('success', '₹' . number_format($amount, 2) . ' commission paid successfully and email sent to the shop.');
    }



}
