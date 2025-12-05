<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivationKeysTable extends Migration
{
    public function up()
    {
        Schema::create('activation_keys', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->enum('status', ['fresh', 'used'])->default('fresh');
            $table->unsignedBigInteger('assigned_to')->nullable(); // user who will use it
            $table->unsignedBigInteger('assigned_by')->nullable(); // admin who assigned
            $table->timestamp('used_at')->nullable();
            $table->unsignedBigInteger('used_for')->nullable(); // for what user (if different)

            $table->timestamps();

            $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
            $table->foreign('assigned_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('used_for')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('activation_keys');
    }
}
