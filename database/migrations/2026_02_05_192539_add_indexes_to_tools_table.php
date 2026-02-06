<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración para añadir índices a las tablas tools y tool_units.
 * Optimiza las búsquedas por código, nombre, estado y relaciones.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Índices para la tabla 'tools' (Catálogo)
        Schema::table('tools', function (Blueprint $table) {
            // Índice para búsqueda por nombre
            $table->index('name', 'idx_tools_name');

            // Índice para búsqueda por categoría
            $table->index('tool_category_id', 'idx_tools_category');
        });

        // Índices para la tabla 'tool_units' (Unidades individuales)
        Schema::table('tool_units', function (Blueprint $table) {
            // Índice para búsqueda rápida por código interno (único y frecuente)
            $table->index('internal_code', 'idx_tool_units_internal_code');

            // Índice para búsqueda por número de serie
            $table->index('serial_number', 'idx_tool_units_serial');

            // Índice para filtrado por estado (muy frecuente)
            $table->index('status', 'idx_tool_units_status');

            // Índice para relación con tools
            $table->index('tool_id', 'idx_tool_units_tool_id');

            // Índice para certificaciones que están por vencer
            $table->index('certification_expiry', 'idx_tool_units_cert_expiry');
        });
    }

    public function down(): void
    {
        Schema::table('tool_units', function (Blueprint $table) {
            $table->dropIndex('idx_tool_units_internal_code');
            $table->dropIndex('idx_tool_units_serial');
            $table->dropIndex('idx_tool_units_status');
            $table->dropIndex('idx_tool_units_tool_id');
            $table->dropIndex('idx_tool_units_cert_expiry');
        });

        Schema::table('tools', function (Blueprint $table) {
            $table->dropIndex('idx_tools_name');
            $table->dropIndex('idx_tools_category');
        });
    }
};
