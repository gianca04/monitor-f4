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
            // Agregar source_type para identificar de dónde proviene la entrega
            $table->string('source_type')
                ->default('warehouse')
                ->comment('Tipo de fuente: warehouse, provider, external')
                ->after('tool_unit_id');

            // Agregar source_reference para tracking adicional según source_type
            // Puede ser quote_warehouse_id, vendor_id, etc.
            $table->string('source_reference')
                ->nullable()
                ->comment('Referencia adicional según source_type')
                ->after('source_type');

            // Agregar dispatch_date para registrar cuándo se ejecutó la transacción
            $table->timestamp('dispatch_date')
                ->nullable()
                ->comment('Fecha y hora en que se ejecutó la entrega')
                ->after('source_reference');

            // Agregar índice para búsquedas por source_type
            $table->index('source_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dispatch_transactions', function (Blueprint $table) {
            $table->dropIndex(['source_type']);
            $table->dropColumn(['source_type', 'source_reference', 'dispatch_date']);
        });
    }
};
