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
        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable()->comment('Project name');
            $table->timestamps();
            $table->date('requested_at')->nullable();
            $table->date('end_date')->nullable();
            $table->string('location')->nullable();
            $table->unsignedBigInteger('sub_client_id')->nullable()->index('projects_sub_client_id_foreign');
            $table->string('service_code')->nullable();
            $table->string('request_number')->nullable();
            $table->text('comment')->nullable();
            $table->string('work_order_number')->nullable()->comment('OT / Orden de Trabajo');
            $table->date('service_start_date')->nullable();
            $table->date('service_end_date')->nullable();
            $table->integer('service_days')->nullable()->comment('Días calculados');
            $table->string('task_type')->nullable()->comment('OPEX / CAPEX');
            $table->string('has_quote')->nullable()->default('NO');
            $table->string('has_report')->nullable()->default('NO');
            $table->string('fracttal_status')->nullable();
            $table->string('purchase_order')->nullable();
            $table->string('migo_code')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('quote_sent_at')->nullable();
            $table->timestamp('quote_approved_at')->nullable();
            $table->timestamp('wo_review_at')->nullable();
            $table->timestamp('wo_completed_at')->nullable();
            $table->integer('days_to_completion')->nullable();
            $table->text('final_comments')->nullable();
            $table->string('supervisor_name')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable()->index('projects_employee_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
