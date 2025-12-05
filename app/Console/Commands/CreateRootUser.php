<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\BinaryNode;
use App\Models\Wallet;
use Illuminate\Support\Facades\Hash;

class CreateRootUser extends Command
{
    protected $signature = 'mlm:create-root-user';
    protected $description = 'Create the root user for the binary MLM tree';

    public function handle()
    {
        $email = $this->ask('Enter root user email');
        $phone = $this->ask('Enter phone number');
        $password = $this->secret('Enter password');

        if (User::where('email', $email)->exists()) {
            $this->error('User with this email already exists!');
            return;
        }

        $user = User::create([
            'name' => 'Root User',
            'email' => $email,
            'phone' => $phone,
            'referral_code' => strtoupper(uniqid('ROOT')), // <-- THIS IS REQUIRED
            'password' => Hash::make($password),
            'referred_by' => null,
            'placement_leg' => null,
            'is_kyc_verified' => true,
        ]);

        // Create binary node (no parent)
        BinaryNode::create([
            'user_id' => $user->id,
            'parent_id' => $user->id,
            'position' => null,
            'left_points' => 0,
            'right_points' => 0,
        ]);

        // Create wallets
        // foreach (['referral', 'binary', 'cashback'] as $type) {
        //     Wallet::create([
        //         'user_id' => $user->id,
        //         'type' => $type,
        //         'balance' => 0,
        //     ]);
        // }

        $this->info('✅ Root user created successfully!');
        $this->info("Login Email: {$email}");
        $this->info("Referral Code: {$user->referral_code}");
    }
}
