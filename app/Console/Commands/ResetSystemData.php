<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\BinaryNode;
use App\Models\MainWallet;
use App\Models\ReferralWallet;
use App\Models\BinaryWallet;
use App\Models\CashbackWallet;
use App\Models\ReferralIncome;
use App\Models\BinaryIncome;
use App\Models\CashbackIncome;
use App\Models\WalletTransaction;
use App\Models\Withdrawal;
use App\Models\Transaction;
use App\Models\ShopTransaction;
use App\Models\ShopCommission;
use App\Models\Referral;
use App\Models\PayoutRequest;
use App\Models\UserKyc;
use App\Models\ActivationKey;
use App\Models\ActivationKeyTransfer;
use App\Models\Notification;
use App\Models\CashbackRecord;
use Illuminate\Support\Facades\DB;

class ResetSystemData extends Command
{
    protected $signature = 'mlm:reset-data 
                            {--force : Force the operation without confirmation}
                            {--dry-run : Show what would be deleted without actually deleting}';
    
    protected $description = 'Delete all users except root, and delete all transactional data (wallets, incomes, withdrawals, transactions, commissions). Keep root user, management records, and system configuration.';

    public function handle()
    {
        // Find root user
        $rootNode = BinaryNode::whereColumn('parent_id', 'user_id')->first();
        
        if (!$rootNode) {
            $this->error('❌ Root user not found! Please create a root user first using: php artisan mlm:create-root-user');
            return 1;
        }

        $rootUser = $rootNode->user;
        $this->info("📍 Found root user: {$rootUser->name} (ID: {$rootUser->id}, Email: {$rootUser->email})");

        // Count what will be deleted
        $stats = $this->getDeletionStats($rootUser->id);

        // Show summary
        $this->displaySummary($stats, $rootUser);

        // Confirmation
        if (!$this->option('force') && !$this->option('dry-run')) {
            if (!$this->confirm('⚠️  Are you sure you want to proceed? This action cannot be undone!', false)) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        if ($this->option('dry-run')) {
            $this->info("\n🔍 DRY RUN MODE - No data will be deleted.");
            return 0;
        }

        // Perform deletion
        $this->info("\n🔄 Starting data deletion...");
        
        DB::beginTransaction();
        
        try {
            $this->deleteTransactionalData($rootUser->id);
            $this->deleteNonRootUsers($rootUser->id);
            $this->resetRootUserData($rootUser);
            
            DB::commit();
            
            $this->info("\n✅ System reset completed successfully!");
            $this->info("   Root user preserved: {$rootUser->name} (ID: {$rootUser->id})");
            $this->info("   Management records preserved");
            $this->info("   System configuration preserved");
            
            return 0;
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("\n❌ Error during reset: " . $e->getMessage());
            $this->error("   Transaction rolled back. No data was deleted.");
            return 1;
        }
    }

    private function getDeletionStats($rootUserId)
    {
        return [
            'users' => User::where('id', '!=', $rootUserId)->count(),
            'binary_nodes' => BinaryNode::where('user_id', '!=', $rootUserId)->count(),
            'main_wallets' => MainWallet::where('user_id', '!=', $rootUserId)->count(),
            'referral_wallets' => ReferralWallet::count(),
            'binary_wallets' => BinaryWallet::count(),
            'cashback_wallets' => CashbackWallet::count(),
            'referral_incomes' => ReferralIncome::count(),
            'binary_incomes' => BinaryIncome::count(),
            'cashback_incomes' => CashbackIncome::count(),
            'wallet_transactions' => WalletTransaction::count(),
            'withdrawals' => Withdrawal::count(),
            'transactions' => Transaction::count(),
            'shop_transactions' => ShopTransaction::count(),
            'shop_commissions' => ShopCommission::count(),
            'referrals' => Referral::count(),
            'payout_requests' => PayoutRequest::count(),
            'user_kycs' => UserKyc::where('user_id', '!=', $rootUserId)->count(),
            'activation_keys' => ActivationKey::count(),
            'activation_key_transfers' => ActivationKeyTransfer::count(),
            'notifications' => Notification::count(),
            'cashback_records' => CashbackRecord::count(),
        ];
    }

    private function displaySummary($stats, $rootUser)
    {
        $this->info("\n📊 DELETION SUMMARY:");
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("Users (except root):              " . $stats['users']);
        $this->info("Binary Nodes (except root):       " . $stats['binary_nodes']);
        $this->info("Main Wallets:                     " . $stats['main_wallets']);
        $this->info("Referral Wallets:                 " . $stats['referral_wallets']);
        $this->info("Binary Wallets:                    " . $stats['binary_wallets']);
        $this->info("Cashback Wallets:                  " . $stats['cashback_wallets']);
        $this->info("Referral Incomes:                  " . $stats['referral_incomes']);
        $this->info("Binary Incomes:                    " . $stats['binary_incomes']);
        $this->info("Cashback Incomes:                  " . $stats['cashback_incomes']);
        $this->info("Wallet Transactions:               " . $stats['wallet_transactions']);
        $this->info("Withdrawals:                       " . $stats['withdrawals']);
        $this->info("Transactions:                      " . $stats['transactions']);
        $this->info("Shop Transactions:                 " . $stats['shop_transactions']);
        $this->info("Shop Commissions:                  " . $stats['shop_commissions']);
        $this->info("Referrals:                         " . $stats['referrals']);
        $this->info("Payout Requests:                   " . $stats['payout_requests']);
        $this->info("User KYCs (except root):           " . $stats['user_kycs']);
        $this->info("Activation Keys:                   " . $stats['activation_keys']);
        $this->info("Activation Key Transfers:          " . $stats['activation_key_transfers']);
        $this->info("Notifications:                     " . $stats['notifications']);
        $this->info("Cashback Records:                  " . $stats['cashback_records']);
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("\n✅ WILL BE PRESERVED:");
        $this->info("   • Root User: {$rootUser->name} (ID: {$rootUser->id})");
        $this->info("   • Management/Admin records");
        $this->info("   • Products");
        $this->info("   • Commission Levels (configuration)");
        $this->info("   • Income Settings (configuration)");
        $this->info("   • Shops (shop accounts)");
    }

    private function deleteTransactionalData($rootUserId)
    {
        $this->info("\n🗑️  Deleting transactional data...");

        // Delete in order to respect foreign key constraints
        
        // 1. Delete notifications (no user dependency)
        $count = Notification::count();
        Notification::truncate();
        $this->line("   ✓ Deleted {$count} notifications");

        // 2. Delete activation key transfers
        $count = ActivationKeyTransfer::count();
        ActivationKeyTransfer::truncate();
        $this->line("   ✓ Deleted {$count} activation key transfers");

        // 3. Delete activation keys
        $count = ActivationKey::count();
        ActivationKey::truncate();
        $this->line("   ✓ Deleted {$count} activation keys");

        // 4. Delete user KYCs (except root)
        $count = UserKyc::where('user_id', '!=', $rootUserId)->count();
        UserKyc::where('user_id', '!=', $rootUserId)->delete();
        $this->line("   ✓ Deleted {$count} user KYCs");

        // 5. Delete payout requests
        $count = PayoutRequest::count();
        PayoutRequest::truncate();
        $this->line("   ✓ Deleted {$count} payout requests");

        // 6. Delete referrals
        $count = Referral::count();
        Referral::truncate();
        $this->line("   ✓ Deleted {$count} referrals");

        // 7. Delete shop commissions
        $count = ShopCommission::count();
        ShopCommission::truncate();
        $this->line("   ✓ Deleted {$count} shop commissions");

        // 8. Delete shop transactions
        $count = ShopTransaction::count();
        ShopTransaction::truncate();
        $this->line("   ✓ Deleted {$count} shop transactions");

        // 9. Delete transactions
        $count = Transaction::count();
        Transaction::truncate();
        $this->line("   ✓ Deleted {$count} transactions");

        // 10. Delete withdrawals
        $count = Withdrawal::count();
        Withdrawal::truncate();
        $this->line("   ✓ Deleted {$count} withdrawals");

        // 11. Delete wallet transactions
        $count = WalletTransaction::count();
        WalletTransaction::truncate();
        $this->line("   ✓ Deleted {$count} wallet transactions");

        // 12. Delete cashback records
        $count = CashbackRecord::count();
        CashbackRecord::truncate();
        $this->line("   ✓ Deleted {$count} cashback records");

        // 13. Delete cashback incomes
        $count = CashbackIncome::count();
        CashbackIncome::truncate();
        $this->line("   ✓ Deleted {$count} cashback incomes");

        // 14. Delete binary incomes
        $count = BinaryIncome::count();
        BinaryIncome::truncate();
        $this->line("   ✓ Deleted {$count} binary incomes");

        // 15. Delete referral incomes
        $count = ReferralIncome::count();
        ReferralIncome::truncate();
        $this->line("   ✓ Deleted {$count} referral incomes");

        // 16. Delete cashback wallets
        $count = CashbackWallet::count();
        CashbackWallet::truncate();
        $this->line("   ✓ Deleted {$count} cashback wallets");

        // 17. Delete binary wallets
        $count = BinaryWallet::count();
        BinaryWallet::truncate();
        $this->line("   ✓ Deleted {$count} binary wallets");

        // 18. Delete referral wallets
        $count = ReferralWallet::count();
        ReferralWallet::truncate();
        $this->line("   ✓ Deleted {$count} referral wallets");

        // 19. Delete main wallets (except root)
        $count = MainWallet::where('user_id', '!=', $rootUserId)->count();
        MainWallet::where('user_id', '!=', $rootUserId)->delete();
        $this->line("   ✓ Deleted {$count} main wallets");

        $this->info("   ✅ All transactional data deleted!");
    }

    private function deleteNonRootUsers($rootUserId)
    {
        $this->info("\n👥 Deleting non-root users...");

        // Delete binary nodes first (except root)
        $count = BinaryNode::where('user_id', '!=', $rootUserId)->count();
        BinaryNode::where('user_id', '!=', $rootUserId)->delete();
        $this->line("   ✓ Deleted {$count} binary nodes");

        // Delete users (except root)
        // Note: This will cascade delete related records due to foreign keys
        $count = User::where('id', '!=', $rootUserId)->count();
        User::where('id', '!=', $rootUserId)->delete();
        $this->line("   ✓ Deleted {$count} users");

        $this->info("   ✅ All non-root users deleted!");
    }

    private function resetRootUserData($rootUser)
    {
        $this->info("\n🔄 Resetting root user data...");

        // Reset root user's binary node points
        $rootNode = BinaryNode::where('user_id', $rootUser->id)->first();
        if ($rootNode) {
            $rootNode->update([
                'left_points' => 0,
                'right_points' => 0,
                'cb_left' => 0,
                'cb_right' => 0,
            ]);
            $this->line("   ✓ Reset root user binary points to 0");
        }

        // Reset root user's main wallet balance
        $mainWallet = MainWallet::where('user_id', $rootUser->id)->first();
        if ($mainWallet) {
            $mainWallet->update(['balance' => 0]);
            $this->line("   ✓ Reset root user main wallet balance to 0");
        } else {
            // Create main wallet if it doesn't exist
            MainWallet::create([
                'user_id' => $rootUser->id,
                'balance' => 0,
            ]);
            $this->line("   ✓ Created root user main wallet with balance 0");
        }

        // Reset root user's status
        $rootUser->update([
            'is_active' => true,
            'is_kyc_verified' => true,
        ]);
        $this->line("   ✓ Reset root user status");

        $this->info("   ✅ Root user data reset complete!");
    }
}

