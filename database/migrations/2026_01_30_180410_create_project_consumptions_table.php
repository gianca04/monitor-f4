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
        Schema::create('project_consumptions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('project_id')->index('project_consumptions_project_id_foreign');
            $table->unsignedBigInteger('quote_warehouse_detail_id')->index('project_consumptions_quote_warehouse_detail_id_foreign');
            $table->unsignedBigInteger('work_report_id')->nullable()->index('project_consumptions_work_report_id_foreign');
            $table->decimal('quantity', 10);
            $table->date('consumed_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_consumptions');
    }
};
