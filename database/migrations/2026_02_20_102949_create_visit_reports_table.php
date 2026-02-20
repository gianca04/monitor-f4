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
        Schema::create('visit_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->text('suggestions')->nullable();
            $table->text('conclusions')->nullable();
            $table->text('work_to_do')->nullable();        // Trabajos a realizar
            $table->time('start_time')->nullable();        // Hora de inicio del trabajo
            $table->time('end_time')->nullable();          // Hora de finalización del trabajo
            $table->date('report_date')->nullable();       // Fecha del reporte (solo fecha)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visit_reports');
    }
};
