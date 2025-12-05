<?php
namespace App\Listeners;

use App\Events\UserActivationStatusChanged;
use App\Models\ReferralWallet;
use App\Models\BinaryNode;
use App\Models\IncomeSetting;

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
            // ✅ Step 1: Add referral income to referral wallet
            ReferralWallet::create([
                'user_id' => $referrerId,
                'amount'  => $referralAmount,
            ]);

            // ✅ Step 2: Add points to left/right of BinaryNode
            $chain = BinaryNode::getUplineChainToLevel($user->id, $uplineLevels);

            foreach ($chain as $upline) {
                $uplineNode = BinaryNode::where('user_id', $upline['user_id'])->first();
                if (!$uplineNode) continue;
            
                if ($upline['child_position'] === 'left') {
                    $uplineNode->left_points += $pointsPerActivation;
                } elseif ($upline['child_position'] === 'right') {
                    $uplineNode->right_points += $pointsPerActivation;
                }
                $uplineNode->save();
            }

        } elseif (!$event->isNowActive && $event->wasActive) {
            // ✅ Remove referral income from referral wallet
            $walletEntry = ReferralWallet::where('user_id', $referrerId)
                ->where('amount', $referralAmount)
                ->latest()
                ->first();

            if ($walletEntry) {
                $walletEntry->delete(); // Or use soft-delete logic if needed
            }

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
