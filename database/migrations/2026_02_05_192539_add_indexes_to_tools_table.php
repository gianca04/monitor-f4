<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración para añadir índices a la tabla tools.
 * Optimiza las búsquedas por código, nombre, estado y relaciones.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            // Índice para búsqueda rápida por código (único y frecuente)
            $table->index('code', 'idx_tools_code');

            // Índice para búsqueda por nombre
            $table->index('name', 'idx_tools_name');

            // Índice para filtrado por estado (muy frecuente)
            $table->index('status', 'idx_tools_status');

            // Índice compuesto para búsqueda de herramientas disponibles por categoría
            $table->index(['status', 'tool_category_id'], 'idx_tools_status_category');

            // Índice para búsqueda por número de serie
            $table->index('serial_number', 'idx_tools_serial');

            // Índice para certificaciones que están por vencer
            $table->index('certification_expiry', 'idx_tools_cert_expiry');
        });
    }

    public function down(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            $table->dropIndex('idx_tools_code');
            $table->dropIndex('idx_tools_name');
            $table->dropIndex('idx_tools_status');
            $table->dropIndex('idx_tools_status_category');
            $table->dropIndex('idx_tools_serial');
            $table->dropIndex('idx_tools_cert_expiry');
        });
    }
};
