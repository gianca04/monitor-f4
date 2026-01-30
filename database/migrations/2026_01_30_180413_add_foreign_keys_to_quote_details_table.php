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
        Schema::table('quote_details', function (Blueprint $table) {
            $table->foreign(['pricelist_id'])->references(['id'])->on('pricelists')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['quote_id'])->references(['id'])->on('quotes')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quote_details', function (Blueprint $table) {
            $table->dropForeign('quote_details_pricelist_id_foreign');
            $table->dropForeign('quote_details_quote_id_foreign');
        });
    }
};
