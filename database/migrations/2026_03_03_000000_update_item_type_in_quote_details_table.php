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
        // En MySQL, para cambiar un ENUM, necesitamos usar DB::statement o recrear la columna si usamos SQLite/Postgres.
        // Dado que el sistema parece usar MySQL (XAMPP), usaremos DB::statement.

        DB::statement("ALTER TABLE quote_details MODIFY COLUMN item_type ENUM('SERVICIO', 'VIATICOS', 'SUMINISTRO', 'MANO DE OBRA', 'CONSUMIBLE', 'TRANSPORTE', 'OTROS') DEFAULT 'SERVICIO'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE quote_details MODIFY COLUMN item_type ENUM('SERVICIO', 'VIATICOS', 'SUMINISTRO', 'MANO DE OBRA', 'OTROS') DEFAULT 'SERVICIO'");
    }
};
