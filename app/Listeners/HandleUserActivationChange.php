<?php
namespace App\Listeners;

use App\Events\UserActivationStatusChanged;
use App\Models\ReferralWallet;
use App\Models\ReferralIncome;
use App\Models\MainWallet;
use App\Models\BinaryNode;
use App\Models\IncomeSetting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HandleUserActivationChange
{
    public function handle(UserActivationStatusChanged $event): void
    {
        $user = $event->user;
        $referrerId = $user->referred_by;

        if (!$referrerId) return;

        // Get configurable values
        $referralAmount = IncomeSetting::getValue('referral_income_amount', 300);
        $pointsPerActivation = (int) IncomeSetting::getValue('points_per_activation', 100);
        $uplineLevels = (int) IncomeSetting::getValue('upline_chain_levels', 15);

        if ($event->isNowActive && !$event->wasActive) {
            // ✅ Step 1: Add referral income to main wallet and create income record
            DB::transaction(function () use ($referrerId, $referralAmount, $user) {
                // Create income record
                ReferralIncome::create([
                    'user_id' => $referrerId,
                    'new_user_id' => $user->id,
                    'amount' => $referralAmount,
                    'description' => "Referral income from user activation: {$user->name}",
                ]);
                
                // Add to main wallet
                $mainWallet = MainWallet::firstOrCreate(['user_id' => $referrerId], ['balance' => 0]);
                $mainWallet->balance += $referralAmount;
                $mainWallet->save();
            });

            // ✅ Step 2: Add points to left/right of BinaryNode
            $chain = BinaryNode::getUplineChainToLevel($user->id, $uplineLevels);

            foreach ($chain as $upline) {
                $uplineNode = BinaryNode::where('user_id', $upline['user_id'])->first();
                if (!$uplineNode) continue;
            
                $uplineUser = User::find($upline['user_id']);
                $uplineUserName = $uplineUser ? $uplineUser->name : "User #{$upline['user_id']}";
                
                if ($upline['child_position'] === 'left') {
                    $oldPoints = $uplineNode->left_points;
                    $uplineNode->left_points += $pointsPerActivation;
                    Log::channel('binary_points')->info("LEFT_POINTS | User: {$uplineUserName} (ID: {$upline['user_id']}) | Level: {$upline['level']} | {$oldPoints} → {$uplineNode->left_points} (+{$pointsPerActivation}) | Triggered by: {$user->name} (ID: {$user->id})");
                } elseif ($upline['child_position'] === 'right') {
                    $oldPoints = $uplineNode->right_points;
                    $uplineNode->right_points += $pointsPerActivation;
                    Log::channel('binary_points')->info("RIGHT_POINTS | User: {$uplineUserName} (ID: {$upline['user_id']}) | Level: {$upline['level']} | {$oldPoints} → {$uplineNode->right_points} (+{$pointsPerActivation}) | Triggered by: {$user->name} (ID: {$user->id})");
                }
                $uplineNode->save();
            }

        } elseif (!$event->isNowActive && $event->wasActive) {
            // ✅ Remove referral income from main wallet (reverse the transaction)
            DB::transaction(function () use ($referrerId, $referralAmount) {
                $mainWallet = MainWallet::where('user_id', $referrerId)->first();
                if ($mainWallet && $mainWallet->balance >= $referralAmount) {
                    $mainWallet->balance -= $referralAmount;
                    $mainWallet->save();
                }
                
                // Remove the latest referral income record for this user
                $incomeRecord = ReferralIncome::where('user_id', $referrerId)
                    ->where('new_user_id', $user->id)
                    ->latest()
                    ->first();
                if ($incomeRecord) {
                    $incomeRecord->delete();
                }
            });

            // ✅ Optionally: Reverse points if deactivating
            $position = BinaryNode::getPositionInChain($referrerId, $user->id);
            if ($position && in_array($position, ['left', 'right'])) {
                $referrerNode = BinaryNode::where('user_id', $referrerId)->first();
                if ($referrerNode) {
                    if ($position === 'left' && $referrerNode->left_points >= $pointsPerActivation) {
                        $referrerNode->left_points -= $pointsPerActivation;
                    } elseif ($position === 'right' && $referrerNode->right_points >= $pointsPerActivation) {
                        $referrerNode->right_points -= $pointsPerActivation;
                    }
                    $referrerNode->save();
                }
            }
        }
    }
}
