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
        Schema::table('quote_warehouse_details', function (Blueprint $table) {
            $table->dropForeign('quote_warehouse_details_location_id_foreign');
            $table->dropForeign(['location_destination_id']);
            $table->dropColumn(['location_origin_id', 'location_destination_id']);
            $table->boolean('is_external_purchase')->default(false)->after('attended_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quote_warehouse_details', function (Blueprint $table) {
            $table->dropColumn('is_external_purchase');
            $table->foreignId('location_origin_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('location_destination_id')->nullable()->constrained('locations')->nullOnDelete();
        });
    }
};
