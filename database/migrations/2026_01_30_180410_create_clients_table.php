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
        Schema::create('clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('document_type', 20)->comment('RUC, DNI, FOREIGN_CARD, PASSPORT');
            $table->string('document_number', 11)->comment('Document number');
            $table->string('person_type', 20)->comment('Natural Person, Legal Entity');
            $table->string('business_name')->comment('Client business name');
            $table->text('description')->nullable()->comment('Description of the client');
            $table->string('address')->nullable();
            $table->string('contact_phone')->nullable()->comment('Contact phone number');
            $table->string('contact_email')->nullable()->comment('Contact email address');
            $table->string('logo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
