<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\BinaryNode;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AddUsersUnderRoot extends Command
{
    protected $signature = 'mlm:add-users-under-root';
    protected $description = 'Add 2 users under the root user (left and right positions)';

    public function handle()
    {
        // Find root user (user with parent_id = user_id in BinaryNode)
        $rootNode = BinaryNode::whereColumn('parent_id', 'user_id')->first();
        
        if (!$rootNode) {
            $this->error('Root user not found! Please create a root user first using: php artisan mlm:create-root-user');
            return 1;
        }

        $rootUser = $rootNode->user;
        $this->info("Found root user: {$rootUser->name} (ID: {$rootUser->id}, Code: {$rootUser->referral_code})");

        // Check if root already has children
        $leftChild = BinaryNode::where('parent_id', $rootUser->id)->where('position', 'left')->first();
        $rightChild = BinaryNode::where('parent_id', $rootUser->id)->where('position', 'right')->first();

        if ($leftChild || $rightChild) {
            $this->warn('Root user already has children:');
            if ($leftChild) {
                $this->line("  - Left: {$leftChild->user->name} (ID: {$leftChild->user_id})");
            }
            if ($rightChild) {
                $this->line("  - Right: {$rightChild->user->name} (ID: {$rightChild->user_id})");
            }
            
            if (!$this->confirm('Do you want to add new users anyway?', false)) {
                return 0;
            }
        }

        // Get a product (required for user creation)
        $product = Product::first();
        if (!$product) {
            $this->error('No product found! Please create a product first.');
            return 1;
        }

        DB::beginTransaction();

        try {
            // Create Left User
            if (!$leftChild) {
                $leftUser = $this->createUser('Left User', 'left', $rootUser, $product);
                $this->info("✅ Created left user: {$leftUser->name} (ID: {$leftUser->id}, Code: {$leftUser->referral_code})");
            } else {
                $this->info("⏭️  Left position already occupied by: {$leftChild->user->name}");
            }

            // Create Right User
            if (!$rightChild) {
                $rightUser = $this->createUser('Right User', 'right', $rootUser, $product);
                $this->info("✅ Created right user: {$rightUser->name} (ID: {$rightUser->id}, Code: {$rightUser->referral_code})");
            } else {
                $this->info("⏭️  Right position already occupied by: {$rightChild->user->name}");
            }

            DB::commit();
            $this->info("\n✅ Successfully added users under root!");
            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
    }

    private function createUser($name, $position, $rootUser, $product)
    {
        // Generate unique email
        $email = strtolower(str_replace(' ', '', $name)) . '_' . time() . '@example.com';
        
        // Create user
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'phone' => '1234567890',
            'password' => Hash::make('password123'),
            'referred_by' => $rootUser->id,
            'place_under' => $rootUser->id,
            'placement_leg' => $position,
            'is_kyc_verified' => false,
            'is_active' => true,
            'product_id' => $product->id,
        ]);

        // Generate referral code
        $user->referral_code = 'DCM_' . str_pad($user->id, 3, '0', STR_PAD_LEFT);
        $user->save();

        // Create binary node
        BinaryNode::create([
            'user_id' => $user->id,
            'parent_id' => $rootUser->id,
            'position' => $position,
            'left_points' => 0,
            'right_points' => 0,
            'cb_left' => 0,
            'cb_right' => 0,
        ]);

        return $user;
    }
}






