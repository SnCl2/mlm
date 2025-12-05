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
        Schema::table('binary_nodes', function (Blueprint $table) {
            $table->unsignedInteger('cb_left')->default(0)->after('left_points');
            $table->unsignedInteger('cb_right')->default(0)->after('right_points');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('binary_nodes', function (Blueprint $table) {
            $table->dropColumn(['cb_left', 'cb_right']);
        });
    }
};
