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
        Schema::create('pricelists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sat_line')->index();
            $table->text('sat_description')->fulltext();
            $table->unsignedBigInteger('unit_id')->index('pricelists_unit_id_foreign');
            $table->unsignedBigInteger('price_type_id')->nullable()->index('pricelists_price_type_id_foreign');
            $table->decimal('unit_price', 10)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricelists');
    }
};
