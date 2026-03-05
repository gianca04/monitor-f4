<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quote_warehouse_details', function (Blueprint $table) {
            $table->decimal('additional_cost', 12, 2)->nullable()->default(0)->after('location_destination_id');
            $table->string('cost_description')->nullable()->after('additional_cost');
        });
    }

    public function down(): void
    {
        Schema::table('quote_warehouse_details', function (Blueprint $table) {
            $table->dropColumn(['additional_cost', 'cost_description']);
        });
    }
};
