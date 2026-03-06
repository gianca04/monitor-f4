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
        Schema::table('quote_details', function (Blueprint $table) {
            $table->foreignId('quote_group_id')->nullable()->after('quote_id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quote_details', function (Blueprint $table) {
            $table->dropForeign(['quote_group_id']);
            $table->dropColumn('quote_group_id');
        });
    }
};
