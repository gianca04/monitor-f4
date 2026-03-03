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
            $table->text('comment')->nullable()->after('attended_quantity');
            $table->foreignId('location_id')->nullable()->after('comment')->constrained('locations')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quote_warehouse_details', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropColumn(['comment', 'location_id']);
        });
    }
};
