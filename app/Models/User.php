<?php


namespace App\Models;
use App\Models\CommissionLevel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Events\UserActivationStatusChanged;


class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'password',
        'referral_code', 'referred_by', 'placement_leg', 'is_kyc_verified', 'place_under','is_active'
    ];

    protected $hidden = ['password', 'remember_token'];

    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    public function referredBy()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function binaryNode()
    {
        return $this->hasOne(BinaryNode::class);
    }



    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function shopTransactions(): HasMany
    {
        return $this->hasMany(ShopTransaction::class);
    }

    public function payoutRequests(): HasMany
    {
        return $this->hasMany(PayoutRequest::class);
    }
    
    public function cashbackWallets()
    {
        return $this->hasMany(CashbackWallet::class);
    }
    
    public function referralWallets()
    {
        return $this->hasMany(ReferralWallet::class);
    }
    
    public function newReferrals()
    {
        return $this->hasMany(ReferralWallet::class, 'new_user_id');
    }
    
    public function parentReferrals()
    {
        return $this->hasMany(ReferralWallet::class, 'parent_id');
    }
    
    public function binaryWallet()
    {
        return $this->hasOne(BinaryWallet::class);
    }
    
    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }
    
    public function getUplineChain()
    {
        $chain = [];
        $visited = [];
    
        $current = $this;
    
        // Get commission levels
        $levels = \App\Models\CommissionLevel::all()->keyBy('level');
        $maxLevel = $levels->count();
    
        for ($level = 1; $level <= $maxLevel; $level++) {
            // Level 1: buyer (no position needed)
            if ($level === 1) {
                $chain[$level] = [
                    'user_id'    => $current->id,
                    'percentage' => $levels[$level]->percentage ?? 0,
                    'position'   => null,
                ];
                continue;
            }
    
            // Stop if no binaryNode or no parent
            if (!$current->binaryNode || !$current->binaryNode->parent_id) break;
    
            $parentId = $current->binaryNode->parent_id;
            $position = $current->binaryNode->position; // left/right of the parent
    
            // Prevent loops
            if (in_array($parentId, $visited)) break;
    
            $parent = static::find($parentId);
            if (!$parent) break;
    
            $visited[] = $parentId;
    
            $chain[$level] = [
                'user_id'    => $parent->id,
                'percentage' => $levels[$level]->percentage ?? 0,
                'position'   => $position,
            ];
    
            // Move up the chain
            $current = $parent;
        }
    
        return $chain;
    }


    public function kyc()
    {
        return $this->hasOne(UserKyc::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'recipient_id')
                    ->where('recipient_type', 'user');
    }

    public function unreadNotifications()
    {
        return $this->notifications()->unread();
    }

    public function getTotalCashback()
    {
        // Get all shop transactions
        $transactions = $this->shopTransactions;
    
        // Get commission level 1 percentage (e.g., 5 means 5%)
        $level1 = CommissionLevel::where('level', 1)->first();
        $percentage = $level1 ? ($level1->percentage / 100) : 0;
    
        // Calculate total cashback from transactions
        $cashbackFromTransactions = $transactions->sum(function ($transaction) use ($percentage) {
            return $transaction->commission_amount * $percentage;
        });
    
        // Add CashbackWallet total
        $cashbackWalletSum = $this->cashbackWallets()->sum('cashback_amount');
    
        // Total Cashback
        return $cashbackFromTransactions + $cashbackWalletSum;
    }
    
    public function getTotalWithdrawableBalance()
    {
        $cashback = $this->cashbackWallets()->sum('cashback_amount');
        $referral = $this->referralWallets()->sum('amount');
        $binary   = $this->binaryWallet ? $this->binaryWallet->matching : 0;
    
        return [
            'cashback' => $cashback,
            'referral' => $referral,
            'binary'   => $binary,
            'total'    => $cashback + $referral + $binary,
        ];
    }


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

    

    protected static function booted()
    {
        static::updating(function ($user) {
            if ($user->isDirty('is_active')) {
                $originalStatus = $user->getOriginal('is_active');
                $newStatus = $user->is_active;
    
                if ($originalStatus !== $newStatus) {
                    event(new UserActivationStatusChanged($user, $originalStatus, $newStatus));
                }
            }
        });
    }
    
    public function getDownlineCounts($position)
    {
        if (!$this->binaryNode) {
            return ['total' => 0, 'active' => 0, 'inactive' => 0];
        }
    
        // Find the child node in the specified position
        $childNode = BinaryNode::where('parent_id', $this->binaryNode->id)
            ->where('position', $position)
            ->first();
    
        if (!$childNode) {
            return ['total' => 0, 'active' => 0, 'inactive' => 0];
        }
    
        // Recursively count all descendants (full downline)
        $descendantIds = $this->getDescendantIds($childNode->id);
    
        // Count totals with activity breakdown
        return User::whereIn('id', $descendantIds)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive
            ')
            ->first()
            ->toArray();
    }

    // Helper: Get all descendant node user_ids recursively
    protected function getDescendantIds($nodeId)
    {
        $ids = [];
        $nodes = BinaryNode::where('parent_id', $nodeId)->get();
    
        foreach ($nodes as $node) {
            $ids[] = $node->user_id;
            $ids = array_merge($ids, $this->getDescendantIds($node->id));
        }
    
        return $ids;
    }
    
    public function keyTransfersSent()
    {
        return $this->hasMany(ActivationKeyTransfer::class, 'from_user_id');
    }
    
    public function keyTransfersReceived()
    {
        return $this->hasMany(ActivationKeyTransfer::class, 'to_user_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }



}
