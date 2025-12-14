<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\BinaryNode;
use App\Models\BinaryIncome;
use App\Models\ReferralIncome;
use App\Models\CashbackIncome;
use App\Models\IncomeSetting;
use App\Models\MainWallet;
use Illuminate\Support\Facades\DB;

class AnalyzeBinaryTree extends Command
{
    protected $signature = 'mlm:analyze-binary 
                            {--user-id= : Analyze specific user by ID}
                            {--user-code= : Analyze specific user by referral code}
                            {--depth=5 : Maximum depth to display (default: 5)}
                            {--show-all : Show all users, not just tree structure}
                            {--summary : Show only summary statistics}';
    
    protected $description = 'Analyze binary matching and commission distribution throughout the tree';

    private $pointsPerMatch;
    private $incomePerMatch;
    private $referralIncome;

    public function handle()
    {
        // Load income settings
        $this->pointsPerMatch = (int) IncomeSetting::getValue('points_per_match', 100);
        $this->incomePerMatch = IncomeSetting::getValue('binary_matching_income', 200);
        $this->referralIncome = IncomeSetting::getValue('referral_income', 300);

        $this->info('═══════════════════════════════════════════════════════════════');
        $this->info('           BINARY MATCHING & COMMISSION ANALYSIS');
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->newLine();

        // Show income settings
        $this->displayIncomeSettings();

        // Find root or specific user
        $rootUser = $this->findUser();
        
        if (!$rootUser) {
            $this->error('User not found!');
            return 1;
        }

        if ($this->option('summary')) {
            $this->displaySummary($rootUser);
            return 0;
        }

        // Display tree structure
        $this->displayTreeStructure($rootUser);

        // Display detailed analysis
        $this->displayDetailedAnalysis($rootUser);

        return 0;
    }

    private function findUser()
    {
        if ($userId = $this->option('user-id')) {
            return User::with('binaryNode')->find($userId);
        }

        if ($userCode = $this->option('user-code')) {
            return User::with('binaryNode')->where('referral_code', $userCode)->first();
        }

        // Find root user (self-referencing node)
        $rootNode = BinaryNode::whereColumn('parent_id', 'user_id')->first();
        return $rootNode ? $rootNode->user : User::with('binaryNode')->first();
    }

    private function displayIncomeSettings()
    {
        $this->info('📊 INCOME SETTINGS');
        $this->info('─────────────────────────────────────────────────────────────');
        $this->line("Points per Match:     {$this->pointsPerMatch}");
        $this->line("Income per Match:     ₹{$this->incomePerMatch}");
        $this->line("Referral Income:      ₹{$this->referralIncome}");
        $this->newLine();
    }

    private function displaySummary($user)
    {
        $this->info('📈 SYSTEM SUMMARY');
        $this->info('─────────────────────────────────────────────────────────────');

        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $totalNodes = BinaryNode::count();

        // Binary matching stats
        $totalBinaryIncome = BinaryIncome::sum('amount');
        $totalMatches = BinaryIncome::sum('matches');
        $totalReferralIncome = ReferralIncome::sum('amount');
        $totalCashbackIncome = CashbackIncome::sum('amount');

        // Points statistics
        $totalLeftPoints = BinaryNode::sum('left_points');
        $totalRightPoints = BinaryNode::sum('right_points');
        $potentialMatches = min(floor($totalLeftPoints / $this->pointsPerMatch), floor($totalRightPoints / $this->pointsPerMatch));
        $potentialIncome = $potentialMatches * $this->incomePerMatch;

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Users', number_format($totalUsers)],
                ['Active Users', number_format($activeUsers)],
                ['Binary Nodes', number_format($totalNodes)],
                ['', ''],
                ['Total Left Points', number_format($totalLeftPoints)],
                ['Total Right Points', number_format($totalRightPoints)],
                ['Potential Matches', number_format($potentialMatches)],
                ['Potential Income', '₹' . number_format($potentialIncome, 2)],
                ['', ''],
                ['Total Binary Income', '₹' . number_format($totalBinaryIncome, 2)],
                ['Total Matches Made', number_format($totalMatches)],
                ['Total Referral Income', '₹' . number_format($totalReferralIncome, 2)],
                ['Total Cashback Income', '₹' . number_format($totalCashbackIncome, 2)],
            ]
        );
    }

    private function displayTreeStructure($user, $depth = 0, $maxDepth = 5, $prefix = '', $isLast = true)
    {
        if ($depth > $maxDepth) {
            return;
        }

        $node = $user->binaryNode;
        if (!$node) {
            return;
        }

        // Calculate matches
        $leftPoints = $node->left_points ?? 0;
        $rightPoints = $node->right_points ?? 0;
        $matchCount = min(floor($leftPoints / $this->pointsPerMatch), floor($rightPoints / $this->pointsPerMatch));
        $potentialIncome = $matchCount * $this->incomePerMatch;

        // Get income stats
        $binaryIncome = BinaryIncome::where('user_id', $user->id)->sum('amount');
        $referralIncome = ReferralIncome::where('user_id', $user->id)->sum('amount');
        $mainWallet = MainWallet::where('user_id', $user->id)->first();
        $walletBalance = $mainWallet ? $mainWallet->balance : 0;

        // Display user info
        $connector = $isLast ? '└── ' : '├── ';
        $name = $user->name . ' (' . $user->referral_code . ')';
        $status = $user->is_active ? '✓' : '✗';
        
        $this->line($prefix . $connector . $status . ' ' . $name);
        
        // Display details
        $detailPrefix = $prefix . ($isLast ? '    ' : '│   ');
        $this->line($detailPrefix . "ID: {$user->id} | Position: " . ($node->position ?? 'ROOT'));
        $this->line($detailPrefix . "Left: {$leftPoints} | Right: {$rightPoints} | Matches: {$matchCount} | Income: ₹{$potentialIncome}");
        $this->line($detailPrefix . "Binary Income: ₹" . number_format($binaryIncome, 2) . " | Referral: ₹" . number_format($referralIncome, 2) . " | Wallet: ₹" . number_format($walletBalance, 2));

        // Get children
        $children = BinaryNode::where('parent_id', $user->id)
            ->whereColumn('parent_id', '!=', 'user_id') // Exclude self-referencing
            ->with('user')
            ->orderBy('position')
            ->get();

        if ($children->count() > 0) {
            $this->newLine();
        }

        foreach ($children as $index => $childNode) {
            $isLastChild = $index === $children->count() - 1;
            $newPrefix = $prefix . ($isLast ? '    ' : '│   ');
            $this->displayTreeStructure($childNode->user, $depth + 1, $maxDepth, $newPrefix, $isLastChild);
        }
    }

    private function displayDetailedAnalysis($user)
    {
        $this->newLine();
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->info('           DETAILED BINARY MATCHING ANALYSIS');
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->newLine();

        $maxDepth = (int) $this->option('depth');
        $users = $this->getUsersInTree($user->id, $maxDepth);

        $data = [];
        foreach ($users as $u) {
            $node = $u->binaryNode;
            if (!$node) continue;

            $leftPoints = $node->left_points ?? 0;
            $rightPoints = $node->right_points ?? 0;
            $matchCount = min(floor($leftPoints / $this->pointsPerMatch), floor($rightPoints / $this->pointsPerMatch));
            $potentialIncome = $matchCount * $this->incomePerMatch;
            $binaryIncome = BinaryIncome::where('user_id', $u->id)->sum('amount');
            $referralIncome = ReferralIncome::where('user_id', $u->id)->sum('amount');
            $cashbackIncome = CashbackIncome::where('user_id', $u->id)->sum('amount');
            $mainWallet = MainWallet::where('user_id', $u->id)->first();
            $walletBalance = $mainWallet ? $mainWallet->balance : 0;

            $data[] = [
                'ID' => $u->id,
                'Name' => $u->name,
                'Code' => $u->referral_code,
                'Status' => $u->is_active ? 'Active' : 'Inactive',
                'Position' => $node->position ?? 'ROOT',
                'Left Pts' => number_format($leftPoints),
                'Right Pts' => number_format($rightPoints),
                'Matches' => $matchCount,
                'Pot. Income' => '₹' . number_format($potentialIncome, 2),
                'Bin. Earned' => '₹' . number_format($binaryIncome, 2),
                'Ref. Earned' => '₹' . number_format($referralIncome, 2),
                'CB Earned' => '₹' . number_format($cashbackIncome, 2),
                'Wallet' => '₹' . number_format($walletBalance, 2),
            ];
        }

        $this->table([
            'ID', 'Name', 'Code', 'Status', 'Position', 'Left Pts', 'Right Pts',
            'Matches', 'Pot. Income', 'Bin. Earned', 'Ref. Earned', 'CB Earned', 'Wallet'
        ], $data);

        // Show top earners
        $this->newLine();
        $this->info('🏆 TOP 10 EARNERS (Binary Income)');
        $this->info('─────────────────────────────────────────────────────────────');
        
        $topEarners = BinaryIncome::select('user_id', DB::raw('SUM(amount) as total_income'), DB::raw('SUM(matches) as total_matches'))
            ->groupBy('user_id')
            ->orderBy('total_income', 'desc')
            ->limit(10)
            ->get();

        $topData = [];
        foreach ($topEarners as $earner) {
            $u = User::find($earner->user_id);
            if ($u) {
                $topData[] = [
                    'User' => $u->name . ' (' . $u->referral_code . ')',
                    'Total Income' => '₹' . number_format($earner->total_income, 2),
                    'Total Matches' => number_format($earner->total_matches),
                ];
            }
        }

        if (!empty($topData)) {
            $this->table(['User', 'Total Income', 'Total Matches'], $topData);
        } else {
            $this->line('No binary income records found.');
        }
    }

    private function getUsersInTree($userId, $maxDepth, $currentDepth = 0, &$visited = [])
    {
        if ($currentDepth > $maxDepth || in_array($userId, $visited)) {
            return collect();
        }

        $visited[] = $userId;
        $user = User::with('binaryNode')->find($userId);
        
        if (!$user) {
            return collect();
        }

        $users = collect([$user]);

        $children = BinaryNode::where('parent_id', $userId)
            ->whereColumn('parent_id', '!=', 'user_id')
            ->get();

        foreach ($children as $childNode) {
            $users = $users->merge($this->getUsersInTree($childNode->user_id, $maxDepth, $currentDepth + 1, $visited));
        }

        return $users;
    }
}

