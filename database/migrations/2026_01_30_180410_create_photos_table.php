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
        Schema::create('photos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('work_report_id')->index('photos_work_report_id_foreign');
            $table->string('photo_path')->nullable();
            $table->string('before_work_photo_path')->nullable();
            $table->longText('descripcion')->nullable();
            $table->timestamps();
            $table->longText('before_work_descripcion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};
