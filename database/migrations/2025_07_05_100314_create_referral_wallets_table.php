<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('referral_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // who gets the income
            $table->foreignId('new_user_id')->nullable()->constrained('users')->onDelete('cascade'); // who joined
            $table->foreignId('parent_id')->nullable()->constrained('users')->onDelete('cascade'); // tree parent
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_wallets');
    }
};
