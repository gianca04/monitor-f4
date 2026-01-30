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
            $table->foreign(['quote_detail_id'])->references(['id'])->on('quote_details')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['quote_warehouse_id'])->references(['id'])->on('quote_warehouse')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quote_warehouse_details', function (Blueprint $table) {
            $table->dropForeign('quote_warehouse_details_quote_detail_id_foreign');
            $table->dropForeign('quote_warehouse_details_quote_warehouse_id_foreign');
        });
    }
};
