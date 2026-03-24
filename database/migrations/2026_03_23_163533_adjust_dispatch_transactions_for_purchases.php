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
        Schema::table('dispatch_transactions', function (Blueprint $table) {
            $table->dropForeign(['location_origin_id']);
            $table->dropForeign(['location_destination_id']);
            $table->dropColumn(['location_origin_id', 'location_destination_id']);
            $table->boolean('is_external_purchase')->default(false)->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dispatch_transactions', function (Blueprint $table) {
            $table->dropColumn('is_external_purchase');
            $table->foreignId('location_origin_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('location_destination_id')->nullable()->constrained('locations')->nullOnDelete();
        });
    }
};
