<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserKyc;
use Illuminate\Http\Request;
use App\Models\ReferralWallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\BinaryNode;
use App\Models\BinaryWallet;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    /**
     * Toggle KYC status: approve <-> pending
     */
    public function toggleKyc($kycId)
    {
        $kyc = UserKyc::findOrFail($kycId);
        $user = $kyc->user;

        if ($kyc->status === 'approved') {
            $kyc->status = 'pending';
            $user->is_kyc_verified = false;
        } else {
            $kyc->status = 'approved';
            $user->is_kyc_verified = true;
        }

        $kyc->save();
        $user->save();

        return redirect()->back()->with('success', 'KYC status toggled successfully.');
    }

    /**
     * Toggle user active <-> inactive
     */
    public function toggleActive($userId)
    {
        DB::transaction(function () use ($userId) {
            $user = User::findOrFail($userId);
    
            // Toggle activation
            $user->is_active = !$user->is_active;
            $user->save();
    
            // === Referral Wallet Update === //
            $referrerId = $user->referred_by;
            if ($referrerId) {
                $wallet = ReferralWallet::firstOrNew(['user_id' => $referrerId]);
                if (!$wallet->exists) {
                    $wallet->amount = 0;
                }
                $wallet->amount += $user->is_active ? 300 : -300;
                $wallet->save();
            }
    
            // === Binary Node Update & Matching Bonus === //
            $binaryNode = BinaryNode::where('user_id', $user->id)->first();
    
            if ($binaryNode && $binaryNode->parent_id && $binaryNode->position) {
                $parentNode = BinaryNode::where('user_id', $binaryNode->parent_id)->first();
    
                if ($parentNode) {
                    // Add/subtract 200 points
                    if ($binaryNode->position === 'left') {
                        $parentNode->left_points += $user->is_active ? 200 : -200;
                    } elseif ($binaryNode->position === 'right') {
                        $parentNode->right_points += $user->is_active ? 200 : -200;
                    }
    
                    $parentNode->save();
    
                    // === Matching Bonus Logic === //
                    $left = $parentNode->left_points;
                    $right = $parentNode->right_points;
    
                    $matchCount = min(floor($left / 100), floor($right / 100));
    
                    if ($matchCount > 0) {
                        $matchAmount = $matchCount * 100;
    
                        $wallet = BinaryWallet::firstOrCreate(['user_id' => $parentNode->user_id]);
                        $wallet->matching_amount += $matchAmount;
                        $wallet->save();
    
                        $parentNode->left_points -= $matchAmount;
                        $parentNode->right_points -= $matchAmount;
                        $parentNode->save();
    
                        Log::info("🎯 Matching Bonus Applied", [
                            'user_id' => $parentNode->user_id,
                            'matchCount' => $matchCount,
                            'matchAmount' => $matchAmount,
                        ]);
                    } else {
                        Log::info("ℹ️ No matching bonus for user_id: " . $parentNode->user_id);
                    }
                }
            }
        });
    
        return redirect()->back()->with('success', 'User status updated, wallet adjusted, and matching bonus applied.');
    }
    
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }
    
    


    public function index(Request $request)
    {
        $users = User::with(['referredBy', 'binaryNode', 'product'])
            ->paginate(20); // Your existing pagination
    
        return view('admin.users.index', compact('users'));
    }


public function getDownlineInfo($userId)
{
    $user = User::with('binaryNode')->findOrFail($userId);
    
    $leftDownline = $user->getDownlineCounts('left');
    $rightDownline = $user->getDownlineCounts('right');
    
    return response()->json([
        'left' => $leftDownline,
        'right' => $rightDownline,
    ]);
}

/**
 * Show the form for editing the specified user
 */
public function edit(User $user)
{
    $products = Product::all();
    $users = User::where('id', '!=', $user->id)->get(['id', 'name', 'referral_code']);
    
    return view('admin.users.edit', compact('user', 'products', 'users'));
}

/**
 * Update the specified user in storage
 */
public function update(Request $request, User $user)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        'phone' => 'nullable|string|max:15',
        'password' => 'nullable|string|min:8|confirmed',
        'referred_by' => 'nullable|exists:users,id',
        'place_under' => 'nullable|exists:users,id',
        'placement_leg' => 'nullable|in:left,right',
        'product_id' => 'nullable|exists:products,id',
        'is_active' => 'boolean',
        'is_kyc_verified' => 'boolean',
    ]);

    DB::beginTransaction();

    try {
        // Update basic user information
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->referred_by = $request->referred_by;
        $user->place_under = $request->place_under;
        $user->placement_leg = $request->placement_leg;
        $user->product_id = $request->product_id;
        $user->is_active = $request->has('is_active');
        $user->is_kyc_verified = $request->has('is_kyc_verified');

        // Update password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Update binary node if placement information changed
        if ($request->place_under && $request->placement_leg) {
            $binaryNode = BinaryNode::where('user_id', $user->id)->first();
            
            if ($binaryNode) {
                $binaryNode->parent_id = $request->place_under;
                $binaryNode->position = $request->placement_leg;
                $binaryNode->save();
            } else {
                // Create new binary node if it doesn't exist
                BinaryNode::create([
                    'user_id' => $user->id,
                    'parent_id' => $request->place_under,
                    'position' => $request->placement_leg,
                ]);
            }
        }

        DB::commit();

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully!');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error updating user: ' . $e->getMessage());
        
        return back()->withErrors(['error' => 'Failed to update user: ' . $e->getMessage()])
            ->withInput();
    }
}



}
