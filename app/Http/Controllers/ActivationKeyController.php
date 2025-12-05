<?php


namespace App\Http\Controllers;

use App\Models\ActivationKey;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivationKeyTransfer;


class ActivationKeyController extends Controller
{
    // List all activation keys
    public function index()
    {
        $keys = ActivationKey::with(['assignedTo', 'assignedBy', 'usedFor'])
            ->latest()
            ->paginate(20);
    
        $transfers = ActivationKeyTransfer::with(['activationKey', 'fromUser', 'toUser'])
            ->latest('transferred_at')
            ->get();
    
        return view('admin.activation_keys.index', compact('keys', 'transfers'));
    }

    // Show create key form
    public function create()
    {
        $users = User::all();
        return view('admin.activation_keys.create', compact('users'));
    }

    // Store new activation key
    public function assignToUser(Request $request)
    {
        $request->validate([
            'referral_code' => 'required|string|exists:users,referral_code',
            'quantity' => 'required|integer|min:1',
        ]);
    
        // Find the user by referral code
        $user = User::where('referral_code', $request->referral_code)->first();
    
        if (!$user) {
            return back()->withErrors(['referral_code' => 'Invalid referral code.']);
        }
    
        $adminId = auth('management')->id();
    
        for ($i = 0; $i < $request->quantity; $i++) {
            ActivationKey::create([
                'key' => strtoupper(Str::random(16)),
                'status' => 'fresh',
                'assigned_to' => $user->id,
                'assigned_by' => $adminId,
            ]);
        }
    
        return back()->with('success', "{$request->quantity} activation keys assigned to {$user->name}.");
    }


    public function userIndex()
    {
        $user = auth()->user();
    
        // ✅ Keys assigned to this user
        $activationKeys = ActivationKey::with(['assignedBy', 'usedFor'])
            ->where('assigned_to', $user->id)
            ->latest()
            ->paginate(20); // use pagination just like admin view
    
        // 🔁 Keys transferred by this user
        $transfers = ActivationKeyTransfer::with(['activationKey', 'toUser'])
            ->where('from_user_id', $user->id)
            ->latest('transferred_at')
            ->get(); // or paginate if needed
    
        return view('admin.activation_keys.user_index', compact('activationKeys', 'transfers'));
    }




    public function useKey(Request $request)
    {
        $request->validate([
            'referral_code' => 'required|string|exists:users,referral_code',
            'key' => 'required|string|exists:activation_keys,key',
        ]);
    
        $currentUser = auth()->user();
    
        $targetUser = User::where('referral_code', $request->referral_code)->first();
    
        if (!$targetUser) {
            return back()->withErrors(['referral_code' => 'Target user not found.']);
        }
    
        $activationKey = ActivationKey::where('key', $request->key)
            ->where('assigned_to', $currentUser->id)
            ->where('status', 'fresh')
            ->first();
    
        if (!$activationKey) {
            return back()->withErrors(['key' => 'Invalid key or not assigned to you.']);
        }
    
        // Mark key as used
        $activationKey->update([
            'status' => 'used',
            'used_at' => now(),
            'used_for' => $targetUser->id,
        ]);
    
        // Update user activation status
        $targetUser->update([
            'is_active' => true,
            'is_kyc_verified' => true,
        ]);
    
        return back()->with('success', 'Activation key successfully used.');
    }
    
    public function transferKey(Request $request)
    {
        $request->validate([
            'key' => 'required|string|exists:activation_keys,key',
            'to_referral_code' => 'required|string|exists:users,referral_code',
        ]);
    
        $fromUser = auth()->user();
    
        $toUser = User::where('referral_code', $request->to_referral_code)->first();
    
        if (!$toUser) {
            return back()->withErrors(['to_referral_code' => 'Target user not found.']);
        }
    
        $activationKey = ActivationKey::where('key', $request->key)
            ->where('assigned_to', $fromUser->id)
            ->where('status', 'fresh')
            ->first();
    
        if (!$activationKey) {
            return back()->withErrors(['key' => 'Key not found or already used.']);
        }
    
        // Log the transfer before updating the key
        ActivationKeyTransfer::create([
            'activation_key_id' => $activationKey->id,
            'from_user_id' => $fromUser->id,
            'to_user_id' => $toUser->id,
            'transferred_at' => now(),
        ]);
    
        // Update key assignment
        $activationKey->update([
            'assigned_to' => $toUser->id,
        ]);
    
        return back()->with('success', 'Key successfully transferred.');
    }
    
    public function getUserByReferral($code)
    {
        $user = \App\Models\User::where('referral_code', $code)->first();
    
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
    
        return response()->json(['name' => $user->name]);
    }




    // Delete key
    public function destroy(ActivationKey $activationKey)
    {
        $activationKey->delete();
        return back()->with('success', 'Key deleted.');
    }
}
