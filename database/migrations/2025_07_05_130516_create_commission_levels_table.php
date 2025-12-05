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
        Schema::create('commission_levels', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('level')->unique();  // e.g., 1, 2, 3
            $table->decimal('percentage', 5, 2);         // e.g., 10.00%
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission_levels');
    }
};
