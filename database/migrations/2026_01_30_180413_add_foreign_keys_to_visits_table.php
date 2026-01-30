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
        Schema::table('visits', function (Blueprint $table) {
            $table->foreign(['inspector_id'], 'visits_employee_id_foreign')->references(['id'])->on('employees')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['project_id'])->references(['id'])->on('projects')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['quoted_by_id'])->references(['id'])->on('employees')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['quote_id'])->references(['id'])->on('quotes')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropForeign('visits_employee_id_foreign');
            $table->dropForeign('visits_project_id_foreign');
            $table->dropForeign('visits_quoted_by_id_foreign');
            $table->dropForeign('visits_quote_id_foreign');
        });
    }
};
