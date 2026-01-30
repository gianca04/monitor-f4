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
        Schema::create('work_reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('employee_id')->index('work_reports_employee_id_foreign');
            $table->unsignedBigInteger('project_id')->index('work_reports_project_id_foreign');
            $table->unsignedBigInteger('compliance_id')->nullable()->index('work_reports_compliance_id_foreign');
            $table->string('name');
            $table->longText('work_to_do')->nullable();
            $table->longText('supervisor_signature')->nullable();
            $table->longText('manager_signature')->nullable();
            $table->longText('suggestions')->nullable();
            $table->timestamps();
            $table->json('tools')->nullable();
            $table->text('conclusions')->nullable();
            $table->json('personnel')->nullable();
            $table->json('materials')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->date('report_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_reports');
    }
};
