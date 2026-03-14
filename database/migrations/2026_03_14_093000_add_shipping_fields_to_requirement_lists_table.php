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
        Schema::table('requirement_lists', function (Blueprint $table) {
            $table->foreignId('dispatcher_id')->nullable()->after('project_id')->constrained('users')->nullOnDelete();
            $table->string('status')->default('pending')->after('name');
            $table->timestamp('attended_at')->nullable()->after('status');
            $table->text('observations')->nullable()->after('attended_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requirement_lists', function (Blueprint $table) {
            $table->dropForeign(['dispatcher_id']);
            $table->dropColumn(['dispatcher_id', 'status', 'attended_at', 'observations']);
        });
    }
};
