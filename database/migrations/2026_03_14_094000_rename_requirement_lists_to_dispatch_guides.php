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
        Schema::rename('requirement_lists', 'dispatch_guides');

        Schema::table('project_requirements', function (Blueprint $table) {
            $table->renameColumn('requirement_list_id', 'dispatch_guide_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_requirements', function (Blueprint $table) {
            $table->renameColumn('dispatch_guide_id', 'requirement_list_id');
        });

        Schema::rename('dispatch_guides', 'requirement_lists');
    }
};
