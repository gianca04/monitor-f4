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
        Schema::create('quote_warehouse_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('quote_warehouse_id')->index('quote_warehouse_details_quote_warehouse_id_foreign');
            $table->unsignedBigInteger('quote_detail_id')->index('quote_warehouse_details_quote_detail_id_foreign');
            $table->decimal('attended_quantity', 10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_warehouse_details');
    }
};
