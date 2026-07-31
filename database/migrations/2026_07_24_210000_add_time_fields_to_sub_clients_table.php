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
            $table->double('arrival_time_hrs')->nullable()->after('ceco');
            $table->double('corrective_quote_time_hrs')->nullable()->after('arrival_time_hrs');
            $table->double('corrective_execution_time_hrs')->nullable()->after('corrective_quote_time_hrs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_clients', function (Blueprint $table) {
            $table->dropColumn([
                'arrival_time_hrs',
                'corrective_quote_time_hrs',
                'corrective_execution_time_hrs',
            ]);
        });
    }
};
