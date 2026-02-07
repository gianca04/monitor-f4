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
        Schema::table('quotes', function (Blueprint $table) {
            // Eliminar la clave foránea existente (que tiene set null)
            // Laravel asume el nombre 'quotes_project_id_foreign'
            $table->dropForeign(['project_id']);

            // Crear la nueva clave foránea con cascade
            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropForeign(['project_id']);

            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onDelete('set null');
        });
    }
};
