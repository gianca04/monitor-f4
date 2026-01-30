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
        Schema::create('visits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('project_id')->nullable()->index('visits_project_id_foreign');
            $table->unsignedBigInteger('quote_id')->nullable()->index('visits_quote_id_foreign');
            $table->unsignedBigInteger('inspector_id')->nullable()->index('visits_employee_id_foreign');
            $table->unsignedBigInteger('quoted_by_id')->nullable()->index('visits_quoted_by_id_foreign');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->time('entry_time')->nullable();
            $table->time('exit_time')->nullable();
            $table->decimal('amount', 10)->nullable();
            $table->date('visit_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
