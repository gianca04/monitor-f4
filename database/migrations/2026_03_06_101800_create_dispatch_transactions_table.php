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
        Schema::create('dispatch_transactions', function (Blueprint $table) {
            $table->id();

            // Relaciones principales
            $table->foreignId('quote_warehouse_id')->constrained('quote_warehouse')->cascadeOnDelete();
            $table->foreignId('project_requirement_id')->constrained('project_requirements')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();

            // Detalles del movimiento
            $table->decimal('quantity', 10, 2);
            $table->foreignId('location_origin_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('location_destination_id')->nullable()->constrained('locations')->nullOnDelete();

            // Costos y extras
            $table->decimal('additional_cost', 10, 2)->default(0);
            $table->string('cost_description')->nullable();
            $table->text('comment')->nullable();

            // Trazabilidad temporal
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispatch_transactions');
    }
};
