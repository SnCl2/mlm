<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\Models\Management;

class CreateManagementUser extends Command
{
    protected $signature = 'make:management-user 
                            {--name= : Name of the management user}
                            {--email= : Email of the management user}
                            {--password= : Password (optional, default: 12345678)}
                            {--phone= : Phone number (optional)}';

    protected $description = 'Create a new active management user';

    public function handle()
    {
        $name = $this->option('name') ?? $this->ask('Enter name');
        $email = $this->option('email') ?? $this->ask('Enter email');
        $password = $this->option('password') ?? $this->ask('Enter email');
        $phone = $this->option('phone') ?? null;

        // Check if user already exists
        if (Management::where('email', $email)->exists()) {
            $this->error("A management user with email {$email} already exists.");
            return 1;
        }

        // Create management user
        $user = Management::create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => Hash::make($password),
            'is_active' => true,
        ]);

        $this->info("✅ Management user created successfully!");
        $this->line("Name: {$user->name}");
        $this->line("Email: {$user->email}");
        $this->line("Password: {$password} (hashed in DB)");
        $this->line("Status: Active");

        return 0;
    }
}
