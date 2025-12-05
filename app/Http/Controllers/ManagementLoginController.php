<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagementLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('management.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $credentials['is_active'] = true; // Only allow active managers

        if (Auth::guard('management')->attempt($credentials)) {
            return redirect()->route('management.dashboard');

        }

        return back()->withErrors(['error' => 'Invalid credentials or inactive account']);
    }

    public function logout()
    {
        Auth::guard('management')->logout();
        return redirect()->route('management.login');
    }
    
    public function index()
    {
        return view('management.dashboard', [
            'userCount' => \App\Models\User::count(),
            'shopCount' => \App\Models\Shop::count(),
            // 'walletBalance' => \App\Models\Wallet::sum('balance'),
            'transactionTotal' => \App\Models\Transaction::sum('amount'),
        ]);
    }
    
    public function create()
    {
        return view('management.create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:management,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|min:6',
        ]);
    
        \App\Models\Management::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => bcrypt($validated['password']),
            'is_active' => $request->has('is_active'),
        ]);
    
        return redirect()->back()->with('success', 'Management user created successfully!');
    }


}
