<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BinaryNode;
use App\Models\BinaryIncome;
use App\Models\MainWallet;
use App\Models\IncomeSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BinaryMatchingCron extends Command
{
    protected $signature = 'binary:match';
    protected $description = 'Match left and right points and credit wallet';

    public function handle()
    {
        Log::info("🔁 Binary matching started at " . now());
        $nodes = BinaryNode::all();

        // Get configurable values
        $pointsPerMatch = (int) IncomeSetting::getValue('points_per_match', 100);
        $incomePerMatch = IncomeSetting::getValue('binary_matching_income', 200);

        foreach ($nodes as $node) {
            $left = $node->left_points;
            $right = $node->right_points;

            $matchCount = min(floor($left / $pointsPerMatch), floor($right / $pointsPerMatch));

            if ($matchCount > 0) {
                $matchAmount = $matchCount * $incomePerMatch;

                DB::transaction(function () use ($node, $matchCount, $matchAmount, $pointsPerMatch, $incomePerMatch) {
                    // Create income record
                    BinaryIncome::create([
                        'user_id' => $node->user_id,
                        'amount' => $matchAmount,
                        'matches' => $matchCount,
                        'description' => "Binary matching income: {$matchCount} matches × ₹{$incomePerMatch}",
                    ]);
                    
                    // Add to main wallet
                    $mainWallet = MainWallet::firstOrCreate(['user_id' => $node->user_id], ['balance' => 0]);
                    $mainWallet->balance += $matchAmount;
                    $mainWallet->save();

                    $node->left_points -= $matchCount * $pointsPerMatch;
                    $node->right_points -= $matchCount * $pointsPerMatch;
                    $node->save();
                });

                $msg = "✅ User ID {$node->user_id}: ₹{$matchAmount} credited, {$matchCount} matches.";
                $this->info($msg);
                Log::info($msg);
            } else {
                $msg = "ℹ️ User ID {$node->user_id}: No matches (L: {$left}, R: {$right})";
                $this->info($msg);
                Log::info($msg);
            }
        }

        Log::info("✅ Binary matching job completed at " . now());
        $this->info("Binary matching job completed successfully.");
    }
}
