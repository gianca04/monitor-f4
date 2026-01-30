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
        Schema::create('quote_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('quote_id');
            $table->integer('line')->nullable();
            $table->unsignedBigInteger('pricelist_id')->nullable()->index('quote_details_pricelist_id_foreign');
            $table->enum('item_type', ['VIATICOS', 'SUMINISTRO', 'MANO DE OBRA'])->default('VIATICOS');
            $table->decimal('quantity', 10)->default(0);
            $table->decimal('unit_price', 12)->default(0);
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->decimal('subtotal', 12)->default(0);

            $table->index(['quote_id', 'pricelist_id'], 'quote_details_quote_id_line_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_details');
    }
};
