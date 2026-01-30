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
        Schema::table('quote_warehouse', function (Blueprint $table) {
            $table->foreign(['quote_id'])->references(['id'])->on('quotes')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['employee_id'], 'quote_warehouse_user_id_foreign')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quote_warehouse', function (Blueprint $table) {
            $table->dropForeign('quote_warehouse_quote_id_foreign');
            $table->dropForeign('quote_warehouse_user_id_foreign');
        });
    }
};
