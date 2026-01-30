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
        Schema::table('project_consumptions', function (Blueprint $table) {
            $table->foreign(['project_id'])->references(['id'])->on('projects')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['quote_warehouse_detail_id'])->references(['id'])->on('quote_warehouse_details')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['work_report_id'])->references(['id'])->on('work_reports')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_consumptions', function (Blueprint $table) {
            $table->dropForeign('project_consumptions_project_id_foreign');
            $table->dropForeign('project_consumptions_quote_warehouse_detail_id_foreign');
            $table->dropForeign('project_consumptions_work_report_id_foreign');
        });
    }
};
