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
        Schema::table('dispatch_transactions', function (Blueprint $table) {
            $table->foreignId('dispatch_guide_id')->nullable()->constrained('dispatch_guides')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dispatch_transactions', function (Blueprint $table) {
            $table->dropForeign(['dispatch_guide_id']);
            $table->dropColumn('dispatch_guide_id');
        });
    }
};
