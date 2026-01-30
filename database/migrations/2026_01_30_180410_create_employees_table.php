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
        Schema::create('employees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('document_type', ['DNI', 'PASAPORTE', 'CARNET DE EXTRANJERIA'])->default('DNI');
            $table->string('document_number', 12);
            $table->string('first_name', 40);
            $table->string('last_name', 40);
            $table->string('address', 40)->nullable();
            $table->date('date_contract')->nullable();
            $table->date('date_birth')->nullable();
            $table->enum('sex', ['male', 'female', 'other'])->nullable();
            $table->unsignedBigInteger('position_id')->nullable()->index('employees_position_id_foreign');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
