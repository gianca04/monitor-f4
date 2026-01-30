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
        Schema::table('contact_data', function (Blueprint $table) {
            $table->foreign(['sub_client_id'])->references(['id'])->on('sub_clients')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_data', function (Blueprint $table) {
            $table->dropForeign('contact_data_sub_client_id_foreign');
        });
    }
};
