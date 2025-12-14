<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\BinaryNode;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CreateSequentialUsers extends Command
{
    protected $signature = 'mlm:create-sequential-users 
                            {count : Number of users to create}
                            {--product-id= : Product ID (optional, will use first product if not provided)}
                            {--password= : Default password for all users (default: password123)}
                            {--prefix= : Name prefix (default: User)}';
    
    protected $description = 'Create n number of users sequentially. First user is root, each subsequent user is child of previous user, placed randomly left or right.';

    public function handle()
    {
        $count = (int) $this->argument('count');
        
        if ($count < 1) {
            $this->error('Count must be at least 1');
            return 1;
        }

        // Get or create product
        $productId = $this->option('product-id');
        if ($productId) {
            $product = Product::find($productId);
            if (!$product) {
                $this->error("Product with ID {$productId} not found!");
                return 1;
            }
        } else {
            $product = Product::first();
            if (!$product) {
                $this->error('No product found! Please create a product first.');
                return 1;
            }
        }

        $defaultPassword = $this->option('password') ?? 'password123';
        $namePrefix = $this->option('prefix') ?? 'User';

        $this->info("Creating {$count} users sequentially...");
        $this->info("Product: {$product->name} (ID: {$product->id})");
        $this->info("Default Password: {$defaultPassword}");
        $this->newLine();

        DB::beginTransaction();

        try {
            $users = [];
            $parentUser = null;

            for ($i = 1; $i <= $count; $i++) {
                if ($i === 1) {
                    // First user is root
                    $parentUser = $this->createRootUser($i, $namePrefix, $defaultPassword, $product);
                    $users[] = $parentUser;
                    $this->info("✅ [{$i}/{$count}] Created ROOT user: {$parentUser->name} (ID: {$parentUser->id}, Code: {$parentUser->referral_code})");
                } else {
                    // Subsequent users are children of previous user
                    $position = $this->getAvailablePosition($parentUser->id);
                    $user = $this->createChildUser($i, $namePrefix, $defaultPassword, $parentUser, $product, $position);
                    $users[] = $user;
                    $this->info("✅ [{$i}/{$count}] Created user: {$user->name} (ID: {$user->id}, Code: {$user->referral_code}, Position: {$position}, Parent: {$parentUser->name})");
                    
                    // Next user's parent is this user
                    $parentUser = $user;
                }
            }

            DB::commit();
            
            $this->newLine();
            $this->info("✅ Successfully created {$count} users!");
            $this->newLine();
            $this->table(
                ['#', 'Name', 'Email', 'Referral Code', 'Parent', 'Position', 'Status'],
                array_map(function ($user, $index) use ($users) {
                    $parentName = 'ROOT';
                    $position = 'ROOT';
                    
                    if ($index > 0) {
                        $parent = $users[$index - 1];
                        $parentName = $parent->name;
                        $node = BinaryNode::where('user_id', $user->id)->first();
                        $position = $node ? $node->position : 'N/A';
                    }
                    
                    return [
                        $index + 1,
                        $user->name,
                        $user->email,
                        $user->referral_code,
                        $parentName,
                        $position,
                        $user->is_active ? 'Active' : 'Inactive'
                    ];
                }, $users, array_keys($users))
            );

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Error: " . $e->getMessage());
            $this->error("Transaction rolled back. No users were created.");
            return 1;
        }
    }

    private function createRootUser($index, $prefix, $password, $product)
    {
        // Check if root already exists
        $rootNode = BinaryNode::whereColumn('parent_id', 'user_id')->first();
        
        if ($rootNode) {
            $this->warn("⚠️  Root user already exists: {$rootNode->user->name} (ID: {$rootNode->user->id})");
            if (!$this->confirm('Do you want to use existing root user?', true)) {
                throw new \Exception('Operation cancelled. Root user already exists.');
            }
            return $rootNode->user;
        }

        // Create root user
        $name = $index === 1 ? 'Root User' : "{$prefix} {$index}";
        $email = strtolower(str_replace(' ', '', $name)) . '_' . time() . '_' . $index . '@example.com';
        
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'phone' => '123456789' . str_pad($index, 2, '0', STR_PAD_LEFT),
            'password' => Hash::make($password),
            'referred_by' => null,
            'place_under' => null,
            'placement_leg' => null,
            'is_kyc_verified' => true,
            'is_active' => true,
            'product_id' => $product->id,
        ]);

        // Generate referral code
        $user->referral_code = 'ROOT' . str_pad($user->id, 6, '0', STR_PAD_LEFT);
        $user->save();

        // Create binary node (self-referencing for root)
        BinaryNode::create([
            'user_id' => $user->id,
            'parent_id' => $user->id,
            'position' => null,
            'left_points' => 0,
            'right_points' => 0,
            'cb_left' => 0,
            'cb_right' => 0,
        ]);

        return $user;
    }

    private function createChildUser($index, $prefix, $password, $parentUser, $product, $position)
    {
        $name = "{$prefix} {$index}";
        $email = strtolower(str_replace(' ', '', $name)) . '_' . time() . '_' . $index . '@example.com';
        
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'phone' => '123456789' . str_pad($index, 2, '0', STR_PAD_LEFT),
            'password' => Hash::make($password),
            'referred_by' => $parentUser->id,
            'place_under' => $parentUser->id,
            'placement_leg' => $position,
            'is_kyc_verified' => false,
            'is_active' => true,
            'product_id' => $product->id,
        ]);

        // Generate referral code
        $user->referral_code = 'DLM' . $user->id . str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->save();

        // Create binary node
        BinaryNode::create([
            'user_id' => $user->id,
            'parent_id' => $parentUser->id,
            'position' => $position,
            'left_points' => 0,
            'right_points' => 0,
            'cb_left' => 0,
            'cb_right' => 0,
        ]);

        return $user;
    }

    private function getAvailablePosition($parentId)
    {
        // Check which positions are available
        $leftTaken = BinaryNode::where('parent_id', $parentId)
            ->where('position', 'left')
            ->exists();
        
        $rightTaken = BinaryNode::where('parent_id', $parentId)
            ->where('position', 'right')
            ->exists();

        // If both available, choose randomly
        if (!$leftTaken && !$rightTaken) {
            return rand(0, 1) === 0 ? 'left' : 'right';
        }

        // If only one available, use that
        if (!$leftTaken) {
            return 'left';
        }

        if (!$rightTaken) {
            return 'right';
        }

        // If both taken, this shouldn't happen in sequential creation
        // but if it does, we'll need to find next available parent
        throw new \Exception("Both positions under parent ID {$parentId} are already taken. Cannot create sequential users.");
    }
}

