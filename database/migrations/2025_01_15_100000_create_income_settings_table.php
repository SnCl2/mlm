<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('income_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('label');
            $table->text('description')->nullable();
            $table->decimal('value', 10, 2);
            $table->string('type')->default('amount'); // amount, points, level
            $table->timestamps();
        });

        // Insert default values
        DB::table('income_settings')->insert([
            [
                'key' => 'referral_income_amount',
                'label' => 'Referral Income Amount',
                'description' => 'Amount credited to referrer when a user activates (in ₹)',
                'value' => 300.00,
                'type' => 'amount',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'binary_matching_income',
                'label' => 'Binary Matching Income per Match',
                'description' => 'Income earned per binary match (100 left + 100 right points = 1 match)',
                'value' => 200.00,
                'type' => 'amount',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'points_per_activation',
                'label' => 'Points per Activation',
                'description' => 'Points added to upline binary tree when a user activates',
                'value' => 100.00,
                'type' => 'points',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'upline_chain_levels',
                'label' => 'Upline Chain Levels',
                'description' => 'Maximum number of levels in upline chain for point distribution',
                'value' => 15.00,
                'type' => 'level',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'points_per_match',
                'label' => 'Points Required per Match',
                'description' => 'Points required from each side (left and right) to create one match',
                'value' => 100.00,
                'type' => 'points',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('income_settings');
    }
};

