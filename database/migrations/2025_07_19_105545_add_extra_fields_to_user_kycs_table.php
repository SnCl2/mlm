<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_kycs', function (Blueprint $table) {
            $table->string('aadhar_number')->nullable()->after('aadhar_card_image');
            $table->string('pan_card')->nullable()->after('aadhar_number');
            $table->string('bank_name')->nullable()->after('bank_account_number');
            $table->string('country')->nullable()->after('bank_name');
            $table->string('state')->nullable()->after('country');
            $table->string('city')->nullable()->after('state');
            $table->string('pincode')->nullable()->after('city');
            $table->text('address')->nullable()->after('pincode');
        });
    }

    public function down(): void
    {
        Schema::table('user_kycs', function (Blueprint $table) {
            $table->dropColumn([
                'aadhar_number',
                'pan_card',
                'bank_name',
                'country',
                'state',
                'city',
                'pincode',
                'address'
            ]);
        });
    }
};
