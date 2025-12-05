<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopsTable extends Migration
{
    public function up(): void
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Shop name
            $table->string('category')->nullable(); // e.g. Grocery, Electronics
            $table->string('owner_name');
            $table->string('phone');

            $table->text('address'); // Full address

            $table->string('aadhar_number', 12);
            $table->string('pan_number', 10);

            $table->string('aadhar_image_path')->nullable(); // Uploaded file path
            $table->string('pan_image_path')->nullable();    // Uploaded file path

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            
            $table->string('email')->nullable()->unique(); // make it unique
            $table->string('password')->nullable(); // for login
            $table->boolean('is_active')->default(false);
            $table->rememberToken(); // for remember me functionality

            $table->decimal('commission_rate', 5, 2)->default(10.00); // default 10%
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
}
