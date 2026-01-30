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
        Schema::create('contact_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sub_client_id')->index('contact_data_sub_client_id_foreign');
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('contact_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_data');
    }
};
