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
        Schema::dropIfExists('notifications'); // Drop if exists to handle migration issues
        
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->enum('type', ['info', 'success', 'warning', 'error', 'announcement']);
            $table->enum('recipient_type', ['user', 'shop', 'all']);
            $table->unsignedBigInteger('recipient_id')->nullable(); // null for 'all' type
            $table->unsignedBigInteger('created_by'); // admin who sent the notification
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->json('metadata')->nullable(); // for additional data like links, actions
            $table->timestamps();

            // Indexes for better performance
            $table->index(['recipient_type', 'recipient_id']);
            $table->index(['is_read', 'created_at']);
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};