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
            // Make quote_detail_id nullable
            $table->unsignedBigInteger('quote_detail_id')->nullable()->change();

            // Add project_requirement_id as nullable foreign key
            $table->foreignId('project_requirement_id')
                ->nullable()
                ->after('quote_detail_id')
                ->constrained('project_requirements')
                ->onDelete('cascade'); // Or set null depending on business logic, cascade is typical if parent deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quote_warehouse_details', function (Blueprint $table) {
            // Revert changes
            $table->unsignedBigInteger('quote_detail_id')->nullable(false)->change();
            $table->dropForeign(['project_requirement_id']);
            $table->dropColumn('project_requirement_id');
        });
    }
};
