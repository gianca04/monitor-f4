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
        Schema::table('sub_clients', function (Blueprint $table) {
            $table->dropColumn('is_emergency_requested');
            $table->double('emergency_response_time_hrs')->nullable()->default(0.33)->after('ceco'); // 20 mins = 0.333 hrs
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_clients', function (Blueprint $table) {
            $table->dropColumn('emergency_response_time_hrs');
            $table->boolean('is_emergency_requested')->default(false)->after('ceco');
        });
    }
};
