<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Soluciona problemas de índices en SQLite cuando se borran columnas con unique constraint
     */
    public function up(): void
    {
        // SQLite no maneja bien el drop de columnas con índices únicos
        // Esta migración es un placeholder para tests con RefreshDatabase
        if (Schema::hasTable('clients') && Schema::hasColumn('clients', 'ruc')) {
            // Si la columna aún existe, seguimos con normalidad
            return;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nada que hacer en reverso
    }
};
