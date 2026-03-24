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
            $table->decimal('price_unit', 10, 2)->nullable()->comment('Costo real unitario de compra');
            $table->string('supplier_name')->nullable()->comment('Nombre del comercio/proveedor de la compra');
            $table->string('receipt_number')->nullable()->comment('N° Boleta/Factura si aplica');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dispatch_transactions', function (Blueprint $table) {
            $table->dropColumn(['price_unit', 'supplier_name', 'receipt_number']);
        });
    }
};
