<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quote_warehouse_details', function (Blueprint $table) {
            $table->renameColumn('location_id', 'location_origin_id');
        });

        Schema::table('quote_warehouse_details', function (Blueprint $table) {
            $table->foreignId('location_destination_id')
                ->nullable()
                ->after('location_origin_id')
                ->constrained('locations')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('quote_warehouse_details', function (Blueprint $table) {
            $table->dropForeign(['location_destination_id']);
            $table->dropColumn('location_destination_id');
        });

        Schema::table('quote_warehouse_details', function (Blueprint $table) {
            $table->renameColumn('location_origin_id', 'location_id');
        });
    }
};
