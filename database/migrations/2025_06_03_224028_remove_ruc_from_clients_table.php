<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Primero dropear el índice unique para evitar conflictos en SQLite
            if (DB::getDriverName() === 'sqlite') {
                // SQLite: eliminar el índice manualmente
                DB::statement('DROP INDEX IF EXISTS clients_ruc_unique');
            } else {
                // MySQL/PostgreSQL: dropear la constante
                $table->dropUnique('clients_ruc_unique');
            }
            
            $table->dropColumn('ruc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('ruc')->nullable();
        });
    }
};
