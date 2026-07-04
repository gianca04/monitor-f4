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
        Schema::table('project_requirements', function (Blueprint $table) {
            $table->string('requirementable_type')->nullable()->change();
            $table->unsignedBigInteger('requirementable_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_requirements', function (Blueprint $table) {
            $table->string('requirementable_type')->nullable(false)->change();
            $table->unsignedBigInteger('requirementable_id')->nullable(false)->change();
        });
    }
};
