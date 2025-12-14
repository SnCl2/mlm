<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * NOTE: This migration is kept for backward compatibility.
     * The wallets table was replaced by separate tables:
     * - cashback_wallets
     * - referral_wallets
     * - binary_wallets
     * - main_wallets
     */
    public function up(): void
    {
        // This migration is deprecated and does nothing
        // The wallet functionality has been moved to separate tables
        // This file exists only to allow proper rollback of migrations
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No action needed - table was never created in this migration
    }
};


