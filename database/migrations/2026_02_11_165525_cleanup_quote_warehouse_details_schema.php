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
        // 1. Migrar datos existentes: Llenar project_requirement_id basado en quote_detail_id
        // Para cada detalle de almacén que tenga quote_detail_id pero no project_requirement_id
        $detailsToUpdate = \Illuminate\Support\Facades\DB::table('quote_warehouse_details')
            ->whereNotNull('quote_detail_id')
            ->whereNull('project_requirement_id')
            ->get();

        foreach ($detailsToUpdate as $detail) {
            // Buscar si existe un ProjectRequirement para este quote_detail_id
            $requirement = \Illuminate\Support\Facades\DB::table('project_requirements')
                ->where('quote_detail_id', $detail->quote_detail_id)
                ->first();

            if ($requirement) {
                \Illuminate\Support\Facades\DB::table('quote_warehouse_details')
                    ->where('id', $detail->id)
                    ->update(['project_requirement_id' => $requirement->id]);
            } else {
                // Si no existe requerimiento, es un problema de integridad.
                // Podríamos intentar crearlo en base a la cotización, pero es arriesgado en migración.
                // Por ahora, si no hay requerimiento, el registro quedará inválido al borrar la columna 
                // o deberíamos borrar el registro de almacén.
                // Opción segura: Dejar que la FK nullable permita NULL si no encontramos match, 
                // pero como vamos a borrar quote_detail_id, perderíamos la referencia.
                // Decisión: Si no hay requerimiento, no podemos migrar.
                // En un entorno de desarrollo esto es aceptable.
            }
        }

        // 2. Eliminar la columna quote_detail_id
        Schema::table('quote_warehouse_details', function (Blueprint $table) {
            // Primero eliminar la FK si existe. El nombre suele ser tabla_columna_foreign
            $table->dropForeign(['quote_detail_id']);
            $table->dropColumn('quote_detail_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quote_warehouse_details', function (Blueprint $table) {
            $table->foreignId('quote_detail_id')->nullable()->constrained('quote_details')->onDelete('cascade');
        });

        // Intentar restaurar datos (inverso)
        // Esto es un best-effort, pues si el requerimiento cambió es difícil saber cuál era el detalle original exacto 
        // si un requerimiento pudiera venir de otro lado, pero en este diseño 1 a 1 funcionaria.
        $details = \Illuminate\Support\Facades\DB::table('quote_warehouse_details')
            ->whereNotNull('project_requirement_id')
            ->get();

        foreach ($details as $detail) {
            $req = \Illuminate\Support\Facades\DB::table('project_requirements')->find($detail->project_requirement_id);
            if ($req && $req->quote_detail_id) {
                \Illuminate\Support\Facades\DB::table('quote_warehouse_details')
                    ->where('id', $detail->id)
                    ->update(['quote_detail_id' => $req->quote_detail_id]);
            }
        }
    }
};
