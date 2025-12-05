<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('activation_key_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activation_key_id');
            $table->unsignedBigInteger('from_user_id')->nullable();
            $table->unsignedBigInteger('to_user_id');
            $table->timestamp('transferred_at')->useCurrent();

            $table->foreign('activation_key_id')->references('id')->on('activation_keys')->cascadeOnDelete();
            $table->foreign('from_user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('to_user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activation_key_transfers');
    }
};
