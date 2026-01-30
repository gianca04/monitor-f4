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
        Schema::create('compliance', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('state')->default('pendiente');
            $table->json('assets')->nullable();
            $table->text('maintenance_observations')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('project_id')->nullable()->index('compliance_project_id_foreign');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('fullname_cliente')->nullable();
            $table->string('document_type')->nullable();
            $table->string('document_number')->nullable();
            $table->text('client_signature')->nullable();
            $table->text('employee_signature')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance');
    }
};
