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
        Schema::table('quotes', function (Blueprint $table) {
            $table->foreign(['employee_id'])->references(['id'])->on('employees')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['project_id'])->references(['id'])->on('projects')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['quote_category_id'])->references(['id'])->on('quote_categories')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['sub_client_id'])->references(['id'])->on('sub_clients')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropForeign('quotes_employee_id_foreign');
            $table->dropForeign('quotes_project_id_foreign');
            $table->dropForeign('quotes_quote_category_id_foreign');
            $table->dropForeign('quotes_sub_client_id_foreign');
        });
    }
};
