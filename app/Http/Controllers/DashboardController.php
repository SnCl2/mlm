<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\BinaryNode;
use App\Models\ShopTransaction;
use App\Models\ReferralWallet;
use App\Models\BinaryWallet;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    public function showEarnings()
    {
        $user = Auth::user();
    
        // Get wallet and income data
        $balanceData = $user->getTotalWithdrawableBalance();
        
        $mainWallet = $balanceData['main_wallet'];
        $referralIncome = $balanceData['referral_income'];
        $binaryIncome = $balanceData['binary_income'];
        $cashbackIncome = $balanceData['cashback_income'];
        $totalWithdrawn = $balanceData['total_withdrawn'];
        $pendingWithdrawn = $balanceData['pending_withdrawn'];
        $availableBalance = $balanceData['available_balance'];
    
        // Binary Points
        $binaryNode = $user->binaryNode;
        $leftPoints = optional($binaryNode)->left_points ?? 0;
        $rightPoints = optional($binaryNode)->right_points ?? 0;
        $leftPoints_C = optional($binaryNode)->cb_left ?? 0;
        $rightPoints_C = optional($binaryNode)->cb_right ?? 0;
    
        // Optionally, recent transactions or payouts
        $transactions = $user->shopTransactions()->latest()->take(5)->get();
    
        return view('dashboard.dashboard_earnings', compact(
            'user',
            'mainWallet',
            'cashbackIncome',
            'totalWithdrawn',
            'pendingWithdrawn',
            'availableBalance',
            'transactions',
            'leftPoints',
            'rightPoints',
            'leftPoints_C',
            'rightPoints_C',
        ));
    }


    public function tree()
    {
        $user = Auth::user();
        $maxDepth = 12;

        // Pre-load all data for the entire tree to avoid N+1 queries
        $treeData = $this->buildBinaryTreeOptimized($user->id, $maxDepth);
        $availableSpots = $this->getAvailableUplineOptions();

        return view('dashboard.tree', [
            'user' => $user,
            'treeData' => $treeData,
            'availableUplines' => $availableSpots,
        ]);
    }

    /**
     * Optimized version that loads all tree data in batches
     */
    private function buildBinaryTreeOptimized($userId, $maxDepth = 12)
    {
        // Step 1: Collect all user IDs in the tree (BFS approach)
        $userIds = $this->collectTreeUserIds($userId, $maxDepth);
        
        if (empty($userIds)) {
            return null;
        }

        // Step 2: Load all users with relationships in one query
        $users = User::with(['binaryNode', 'binaryWallet', 'kyc'])
            ->whereIn('id', $userIds)
            ->get()
            ->keyBy('id');

        // Step 3: Load all binary nodes in one query
        $binaryNodes = BinaryNode::whereIn('user_id', $userIds)
            ->get()
            ->keyBy('user_id');

        // Step 4: Build parent-child map for quick lookup
        // Get ALL children of nodes in the tree, even if child user isn't in userIds
        // Exclude self-referencing nodes (where user_id = parent_id) - these are root nodes
        $allChildNodes = BinaryNode::whereIn('parent_id', $userIds)
            ->whereColumn('user_id', '!=', 'parent_id') // Exclude self-referencing root nodes
            ->get();
        
        $childrenMap = $allChildNodes->groupBy('parent_id')
            ->map(function ($nodes) {
                // If multiple nodes exist for same position, get the oldest one (first created)
                // This handles data integrity issues where multiple children were added
                // For display, we show the oldest child, but we'll load all children to ensure they're in the tree
                $leftNodes = $nodes->where('position', 'left')->sortBy('id');
                $rightNodes = $nodes->where('position', 'right')->sortBy('id');
                
                // Get the oldest child for each position (for tree structure)
                $leftChild = $leftNodes->first();
                $rightChild = $rightNodes->first();
                
                // Collect ALL child user IDs (even duplicates) to ensure they're loaded
                $allChildUserIds = $nodes->pluck('user_id')->unique()->toArray();
                
                return [
                    'left' => $leftChild, // Oldest left child for tree structure
                    'right' => $rightChild, // Oldest right child for tree structure
                    'all_children' => $allChildUserIds, // All children to ensure they're loaded
                ];
            });
        
        // Also collect any child user IDs that weren't in the original collection
        // This includes ALL children, even if there are duplicates in the same position
        $childUserIds = $allChildNodes->pluck('user_id')
            ->unique()
            ->diff($userIds)
            ->toArray();
        
        // Also get all children from the childrenMap to ensure we load them
        foreach ($childrenMap as $parentId => $children) {
            if (isset($children['all_children'])) {
                foreach ($children['all_children'] as $childUserId) {
                    if (!in_array($childUserId, $userIds) && !in_array($childUserId, $childUserIds)) {
                        $childUserIds[] = $childUserId;
                    }
                }
            }
        }
        
        // If there are child users not in the collection, add them
        if (!empty($childUserIds)) {
            $additionalUsers = User::with(['binaryNode', 'kyc'])
                ->whereIn('id', $childUserIds)
                ->get()
                ->keyBy('id');
            $users = $users->merge($additionalUsers);
            
            $additionalBinaryNodes = BinaryNode::whereIn('user_id', $childUserIds)
                ->get()
                ->keyBy('user_id');
            $binaryNodes = $binaryNodes->merge($additionalBinaryNodes);
            
            // Update userIds to include the new users for name cache
            $userIds = array_merge($userIds, $childUserIds);
        }

        // Step 5: Cache user names for referred_by and place_under
        // Update userIds if we added more users
        $finalUserIds = $userIds;
        if (!empty($childUserIds)) {
            $finalUserIds = array_merge($userIds, $childUserIds);
        }
        
        $referrerIds = $users->pluck('referred_by')->filter()->unique();
        $placeUnderIds = $users->pluck('place_under')->filter()->unique();
        $allRelatedIds = $referrerIds->merge($placeUnderIds)->unique();
        
        $nameCache = User::whereIn('id', $allRelatedIds)
            ->pluck('name', 'id')
            ->toArray();

        // Step 6: Pre-calculate user counts for all nodes
        $userCountsCache = $this->calculateUserCountsOptimized($userIds, $users, $binaryNodes, $childrenMap);

        // Step 7: Build tree recursively using cached data
        return $this->buildTreeFromCache(
            $userId,
            $users,
            $binaryNodes,
            $childrenMap,
            $nameCache,
            $userCountsCache,
            $maxDepth
        );
    }

    /**
     * Collect all user IDs in the tree using BFS
     */
    private function collectTreeUserIds($rootUserId, $maxDepth)
    {
        $userIds = [];
        $queue = [[$rootUserId, 0]];
        $visited = [];

        while (!empty($queue)) {
            [$currentUserId, $depth] = array_shift($queue);

            if ($depth >= $maxDepth || isset($visited[$currentUserId])) {
                continue;
            }

            $visited[$currentUserId] = true;
            $userIds[] = $currentUserId;

            // Get children IDs directly from binary_nodes table
            // If multiple children exist for same position, get the oldest one (first created)
            // This handles data integrity issues where multiple children were added to same position
            // Exclude self-referencing nodes (where user_id = parent_id) - these are root nodes
            $childNodes = BinaryNode::where('parent_id', $currentUserId)
                ->whereColumn('user_id', '!=', 'parent_id') // Exclude self-referencing root nodes
                ->orderBy('id', 'asc') // Get oldest first
                ->get()
                ->groupBy('position')
                ->map(function ($positionNodes) {
                    // For each position, return the oldest node's user_id
                    return $positionNodes->first()->user_id;
                })
                ->values()
                ->filter()
                ->toArray();

            foreach ($childNodes as $childId) {
                if ($childId && !isset($visited[$childId])) {
                    $queue[] = [$childId, $depth + 1];
                }
            }
        }

        return $userIds;
    }

    /**
     * Calculate user counts for all nodes using optimized approach
     */
    private function calculateUserCountsOptimized($userIds, $users, $binaryNodes, $childrenMap)
    {
        $countsCache = [];

        // Process nodes from bottom to top (reverse order by depth)
        $processed = [];
        
        foreach ($userIds as $userId) {
            $this->calculateCountForNode($userId, $users, $binaryNodes, $childrenMap, $countsCache, $processed);
        }

        return $countsCache;
    }

    /**
     * Recursively calculate count for a single node
     */
    private function calculateCountForNode($userId, $users, $binaryNodes, $childrenMap, &$countsCache, &$processed)
    {
        if (isset($countsCache[$userId])) {
            return $countsCache[$userId];
        }

        if (isset($processed[$userId])) {
            return ['active' => 0, 'inactive' => 0];
        }

        $processed[$userId] = true;
        $user = $users[$userId] ?? null;

        if (!$user) {
            $countsCache[$userId] = ['active' => 0, 'inactive' => 0];
            return $countsCache[$userId];
        }

        $statusKey = $user->is_active ? 'active' : 'inactive';
        $children = $childrenMap[$userId] ?? ['left' => null, 'right' => null];

        $leftCounts = ['active' => 0, 'inactive' => 0];
        $rightCounts = ['active' => 0, 'inactive' => 0];

        if ($children['left']) {
            $leftChildId = $children['left']->user_id;
            $leftCounts = $this->calculateCountForNode($leftChildId, $users, $binaryNodes, $childrenMap, $countsCache, $processed);
        }

        if ($children['right']) {
            $rightChildId = $children['right']->user_id;
            $rightCounts = $this->calculateCountForNode($rightChildId, $users, $binaryNodes, $childrenMap, $countsCache, $processed);
        }

        $countsCache[$userId] = [
            'active' => ($statusKey === 'active' ? 1 : 0) + $leftCounts['active'] + $rightCounts['active'],
            'inactive' => ($statusKey === 'inactive' ? 1 : 0) + $leftCounts['inactive'] + $rightCounts['inactive'],
        ];

        return $countsCache[$userId];
    }

    /**
     * Build tree structure from cached data
     */
    private function buildTreeFromCache($userId, $users, $binaryNodes, $childrenMap, $nameCache, $userCountsCache, $depth)
    {
        if ($depth <= 0 || !isset($users[$userId])) {
            return null;
        }

        $user = $users[$userId];
        $binaryNode = $binaryNodes[$userId] ?? null;
        $children = $childrenMap[$userId] ?? ['left' => null, 'right' => null];

        // Use the children from the map (which now includes all children from DB)
        $leftChildId = $children['left']?->user_id ?? null;
        $rightChildId = $children['right']?->user_id ?? null;

        return [
            'id' => $user->id,
            'name' => $user->name,
            'referral_code' => $user->referral_code,
            'referred_by' => $nameCache[$user->referred_by] ?? 'N/A',
            'parent' => $nameCache[$user->place_under] ?? 'N/A',
            'image' => $user->kyc?->profile_image ?? null,
            'status' => $user->is_active ? 'active' : 'inactive',
            'leftPoints' => $binaryNode?->left_points ?? 0,
            'rightPoints' => $binaryNode?->right_points ?? 0,
            'leftUsers' => $userCountsCache[$leftChildId] ?? ['active' => 0, 'inactive' => 0],
            'rightUsers' => $userCountsCache[$rightChildId] ?? ['active' => 0, 'inactive' => 0],
            'children' => array_values([
                $leftChildId ? $this->buildTreeFromCache($leftChildId, $users, $binaryNodes, $childrenMap, $nameCache, $userCountsCache, $depth - 1) : null,
                $rightChildId ? $this->buildTreeFromCache($rightChildId, $users, $binaryNodes, $childrenMap, $nameCache, $userCountsCache, $depth - 1) : null,
            ]),
        ];
    }

    /**
     * Legacy method kept for backward compatibility (deprecated - use buildBinaryTreeOptimized)
     */
    public function buildBinaryTree($userId, $depth = 12)
    {
        return $this->buildBinaryTreeOptimized($userId, $depth);
    }


    private function getAvailableUplineOptions()
    {
        $nodes = BinaryNode::all();
        $options = [];

        foreach ($nodes as $node) {
            $hasLeft = BinaryNode::where('parent_id', $node->user_id)->where('position', 'left')->exists();
            $hasRight = BinaryNode::where('parent_id', $node->user_id)->where('position', 'right')->exists();

            if (!$hasLeft) {
                $options[] = [
                    'user_id' => $node->user_id,
                    'position' => 'left',
                    'label' => "User {$node->user->name} (Left)"
                ];
            }

            if (!$hasRight) {
                $options[] = [
                    'user_id' => $node->user_id,
                    'position' => 'right',
                    'label' => "User {$node->user->name} (Right)"
                ];
            }
        }

        return $options;
    }

    public function cashbackOverview()
    {
        $user = Auth::user();
    
        $transactions = $user->shopTransactions()->latest()->get();
        $totalTransactions = $transactions->count();
        $totalCashback = $user->getTotalCashback();
    
        $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($user->referral_code );
    
        return view('dashboard.shop_cashback', [
            'transactions' => $transactions,
            'totalCashback' => $totalCashback,
            'totalTransactions' => $totalTransactions,
            'qrCodeUrl' => $qrCodeUrl,
        ]);
    }
    
    /**
     * @deprecated This method is no longer used by the optimized buildBinaryTree.
     * Kept for backward compatibility. Use calculateUserCountsOptimized instead.
     */
    private function countUsersUnder($userId)
    {
        if (!$userId) {
            return ['active' => 0, 'inactive' => 0];
        }
    
        $user = User::with('binaryNode')->find($userId);
        if (!$user) {
            return ['active' => 0, 'inactive' => 0];
        }
    
        $statusKey = $user->is_active ? 'active' : 'inactive';
    
        // Get children
        $leftChild = User::whereHas('binaryNode', function ($q) use ($userId) {
            $q->where('parent_id', $userId)->where('position', 'left');
        })->first();
    
        $rightChild = User::whereHas('binaryNode', function ($q) use ($userId) {
            $q->where('parent_id', $userId)->where('position', 'right');
        })->first();
    
        $leftCounts = $this->countUsersUnder($leftChild?->id);
        $rightCounts = $this->countUsersUnder($rightChild?->id);
    
        return [
            'active' => ($statusKey === 'active' ? 1 : 0) + $leftCounts['active'] + $rightCounts['active'],
            'inactive' => ($statusKey === 'inactive' ? 1 : 0) + $leftCounts['inactive'] + $rightCounts['inactive'],
        ];
    }
    
    public function table()
    {
        $user = Auth::user();
        $tableData = $this->flattenBinaryTree($this->buildBinaryTree($user->id));
        
        return view('dashboard.table', [
            'user' => $user,
            'tableData' => $tableData,
        ]);
    }
    
    private function flattenBinaryTree($node, &$result = [], $level = 0)
    {
        if (!$node) {
            return $result;
        }
    
        $result[] = [
            'id' => $node['id'],
            'name' => $node['name'],
            'referral_code' => $node['referral_code'],
            'referred_by' => $node['referred_by'],
            'image' => $node['image'],
            'status' => $node['status'],
            'leftPoints' => $node['leftPoints'],
            'rightPoints' => $node['rightPoints'],
            'leftUsers' => $node['leftUsers'],
            'rightUsers' => $node['rightUsers'],
            'level' => $level,
            'parent'=> $node['parent'],
        ];
    
        if (!empty($node['children'])) {
            foreach ($node['children'] as $child) {
                $this->flattenBinaryTree($child, $result, $level + 1);
            }
        }
    
        return $result;
    }

    /**
     * Generate comprehensive income report for referral and binary matching income
     */
    public function incomeReport(Request $request)
    {
        $user = Auth::user();
        $dateFrom = $request->input('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        // ========== REFERRAL INCOME ANALYSIS ==========
        $referralIncome = ReferralWallet::where('user_id', $user->id)
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->get();

        $referralStats = [
            'total_amount' => $referralIncome->sum('amount'),
            'total_transactions' => $referralIncome->count(),
            'average_per_transaction' => $referralIncome->count() > 0 ? $referralIncome->sum('amount') / $referralIncome->count() : 0,
            'daily_breakdown' => $referralIncome->groupBy(function($item) {
                return $item->created_at->format('Y-m-d');
            })->map(function($group) {
                return [
                    'date' => $group->first()->created_at->format('Y-m-d'),
                    'amount' => $group->sum('amount'),
                    'count' => $group->count()
                ];
            })->values(),
            'monthly_breakdown' => $referralIncome->groupBy(function($item) {
                return $item->created_at->format('Y-m');
            })->map(function($group) {
                return [
                    'month' => $group->first()->created_at->format('M Y'),
                    'amount' => $group->sum('amount'),
                    'count' => $group->count()
                ];
            })->values(),
        ];

        // ========== BINARY MATCHING INCOME ANALYSIS ==========
        $binaryWallet = BinaryWallet::where('user_id', $user->id)->first();
        $binaryNode = BinaryNode::where('user_id', $user->id)->first();

        // Get binary matching income history (we'll need to track this, but for now use current wallet)
        $binaryStats = [
            'total_matching_amount' => $binaryWallet ? $binaryWallet->matching_amount : 0,
            'current_left_points' => $binaryNode ? $binaryNode->left_points : 0,
            'current_right_points' => $binaryNode ? $binaryNode->right_points : 0,
            'potential_matches' => $binaryNode ? min(floor($binaryNode->left_points / 100), floor($binaryNode->right_points / 100)) : 0,
            'potential_income' => $binaryNode ? min(floor($binaryNode->left_points / 100), floor($binaryNode->right_points / 100)) * 200 : 0,
            'unmatched_left' => $binaryNode ? $binaryNode->left_points % 100 : 0,
            'unmatched_right' => $binaryNode ? $binaryNode->right_points % 100 : 0,
        ];

        // Calculate estimated matches based on points (assuming 100 points = 1 match = ₹200)
        $estimatedMatches = $binaryStats['potential_matches'];
        $estimatedIncome = $binaryStats['potential_income'];

        // ========== COMBINED STATISTICS ==========
        $combinedStats = [
            'total_income' => $referralStats['total_amount'] + $binaryStats['total_matching_amount'],
            'referral_percentage' => ($referralStats['total_amount'] + $binaryStats['total_matching_amount']) > 0 
                ? ($referralStats['total_amount'] / ($referralStats['total_amount'] + $binaryStats['total_matching_amount']) * 100) 
                : 0,
            'binary_percentage' => ($referralStats['total_amount'] + $binaryStats['total_matching_amount']) > 0 
                ? ($binaryStats['total_matching_amount'] / ($referralStats['total_amount'] + $binaryStats['total_matching_amount']) * 100) 
                : 0,
        ];

        // ========== TOP EARNERS (if admin view) ==========
        $topReferralEarners = null;
        $topBinaryEarners = null;
        
        if ($request->has('admin_view') && Auth::guard('management')->check()) {
            $topReferralEarners = User::select('users.id', 'users.name', 'users.referral_code', 
                    DB::raw('SUM(referral_wallets.amount) as total_referral_income'))
                ->join('referral_wallets', 'users.id', '=', 'referral_wallets.user_id')
                ->whereBetween('referral_wallets.created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->groupBy('users.id', 'users.name', 'users.referral_code')
                ->orderBy('total_referral_income', 'desc')
                ->limit(10)
                ->get();

            $topBinaryEarners = User::select('users.id', 'users.name', 'users.referral_code',
                    DB::raw('COALESCE(SUM(binary_wallets.matching_amount), 0) as total_binary_income'))
                ->leftJoin('binary_wallets', 'users.id', '=', 'binary_wallets.user_id')
                ->groupBy('users.id', 'users.name', 'users.referral_code')
                ->orderBy('total_binary_income', 'desc')
                ->limit(10)
                ->get();
        }

        return view('dashboard.income_report', compact(
            'user',
            'dateFrom',
            'dateTo',
            'referralStats',
            'binaryStats',
            'combinedStats',
            'topReferralEarners',
            'topBinaryEarners',
            'referralIncome'
        ));
    }

    
    

}
