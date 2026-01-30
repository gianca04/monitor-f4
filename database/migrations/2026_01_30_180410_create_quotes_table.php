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
        Schema::create('quotes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('project_id')->nullable()->index('quotes_project_id_foreign');
            $table->string('request_number')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable()->index('quotes_employee_id_foreign');
            $table->unsignedBigInteger('sub_client_id')->nullable()->index('quotes_sub_client_id_foreign');
            $table->unsignedBigInteger('quote_category_id')->nullable()->index('quotes_quote_category_id_foreign');
            $table->string('energy_sci_manager')->nullable();
            $table->string('ceco')->nullable();
            $table->enum('status', ['Pendiente', 'Enviado', 'Aprobado', 'Anulado']);
            $table->date('quote_date')->nullable();
            $table->date('execution_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
