<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ShopTransaction;
use App\Models\ShopCommission;
use App\Models\BinaryNode;
use App\Models\BinaryWallet;
use App\Models\CashbackWallet;
use App\Models\CashbackRecord;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ShopDashboardController extends Controller
{
    public function index()
    {
        $shop = Auth::guard('shop')->user();

        $transactions = ShopTransaction::where('shop_id', $shop->id)->latest()->get();
        $commission = ShopCommission::where('shop_id', $shop->id)->get();
        $todayTotal = $transactions->where('created_at', '>=', now()->startOfDay())->sum('purchase_amount');
        $commissionEarned = $transactions->sum('commission_amount');
        $totalSubmitted = $transactions->sum('purchase_amount');

        return view('shop.dashboard', compact('transactions', 'todayTotal', 'commissionEarned', 'totalSubmitted','commission'));
    }

    public function storeTransaction(Request $request)
    {
        $shop = Auth::guard('shop')->user();
        // dd($shop->id);

        $request->validate([
            'referral_code' => 'required|string|exists:users,referral_code',
            'amount' => 'required|numeric|min:1',
        ]);
        
        $user = User::where('referral_code', $request->referral_code)->first();

        
        DB::transaction(function () use ($request, $shop, $user) {
            $purchaseAmount = $request->amount;
            $commissionRate = $shop->commission_rate ?? 10;
            $commissionAmount = ($purchaseAmount * $commissionRate) / 100;

            Log::info("🛒 New Purchase", [
                'customer_id' => $user->id,
                'amount' => $purchaseAmount,
                'commissionRate' => $commissionRate,
                'commissionAmount' => $commissionAmount,
            ]);

            ShopTransaction::create([
                'user_id' => $user->id,
                'shop_id' => $shop->id,
                'purchase_amount' => $purchaseAmount,
                'commission_amount' => $commissionAmount,
            ]);

            // $user = User::find($user->id);
            if (!$user) {
                Log::warning("❌ User not found for ID: " . $user->id);
                return;
            }

            $chain = $user->getUplineChain();
            Log::info("🧬 Upline Chain", $chain);

            foreach ($chain as $level => $upline) {
                $uplineId = $upline['user_id'];
                $percent = $upline['percentage'] ?? 0;
                $amount = ($commissionAmount * $percent) / 100;

                if ($amount <= 0) continue;

                Log::info("💸 Level {$level} Commission", [
                    'to_user' => $uplineId,
                    'percentage' => $percent,
                    'amount' => $amount
                ]);

                if ($level === 1) {
                    $wallet = CashbackWallet::firstOrCreate([
                        'user_id' => $uplineId,
                    ]);
                    // dd($wallet);
                
                    $wallet->cashback_amount += $amount;
                    $wallet->shop_id = $shop->id;
                    $wallet->save();
                
                    // Create a cashback record entry
                    CashbackRecord::create([
                        'user_id' => $uplineId,
                        'shop_id' => $shop->id,  // Make sure $shop is available in this scope
                        'amount'  => $amount,
                    ]);
                    ShopCommission::updateOrCreate(
                        ['shop_id' => $shop->id],
                        ['total_commission' => DB::raw('total_commission + ' . $commissionAmount)]
                    );
                
                    Log::info("✅ Cashback added", [
                        'user_id' => $uplineId,
                        'amount'  => $amount,
                        'shop_id' => $shop->id ?? null,
                    ]);
                } else {
                    $this->distributeBinaryPoints($uplineId, $amount, $upline['position']);
                }
            }
        });

        return redirect()->back()->with('success', 'Purchase recorded and commission distributed!');
    }

    public function distributeBinaryPoints($userId, $points, $position = null)
    {
        $node = BinaryNode::where('user_id', $userId)->first();

        if (!$node || !$position) {
            Log::warning("⚠️ Skipping binary point distribution - missing node or position", [
                'user_id' => $userId,
                'position' => $position,
            ]);
            return;
        }

        Log::info("🪙 Adding binary points", [
            'to_user' => $userId,
            'position' => $position,
            'points' => $points,
        ]);

        if ($position === 'left') {
            $node->cb_left += $points;
        } elseif ($position === 'right') {
            $node->cb_right += $points;
        }

        $node->save();

        $this->calculateMatchingBonus($node);
    }

    public function calculateMatchingBonus(BinaryNode $node)
    {
        $left = $node->cb_left;
        $right = $node->cb_right;

        $matchCount = min(floor($left / 100), floor($right / 100));

        if ($matchCount > 0) {
            $matchAmount = $matchCount * 100;

            $wallet = CashbackWallet::firstOrCreate(['user_id' => $node->user_id]);
            $wallet->cashback_amount += $matchAmount*2;
            $wallet->save();

            $node->cb_left -= $matchAmount;
            $node->cb_right -= $matchAmount;
            $node->save();

            Log::info("🎯 Matching Bonus Applied", [
                'user_id' => $node->user_id,
                'matchCount' => $matchCount,
                'matchAmount' => $matchAmount,
            ]);
        } else {
            Log::info("ℹ️ No matching bonus for user_id: " . $node->user_id);
        }
    }
    
    public function deductCommission($shopId, $amount)
    {
        if ($amount <= 0) {
            return false; // Don't deduct zero or negative amounts
        }
    
        $shopCommission = ShopCommission::where('shop_id', $shopId)->first();
    
        if (!$shopCommission) {
            return false; // No commission record found
        }
    
        // Prevent total_commission from going below zero
        $newTotal = max(0, $shopCommission->total_commission - $amount);
    
        $shopCommission->total_commission = $newTotal;
        $shopCommission->save();
    
        return true;
    }
}
