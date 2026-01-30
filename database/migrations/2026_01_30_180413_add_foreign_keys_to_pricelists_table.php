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
        Schema::table('pricelists', function (Blueprint $table) {
            $table->foreign(['price_type_id'])->references(['id'])->on('price_types')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['unit_id'])->references(['id'])->on('units')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pricelists', function (Blueprint $table) {
            $table->dropForeign('pricelists_price_type_id_foreign');
            $table->dropForeign('pricelists_unit_id_foreign');
        });
    }
};
