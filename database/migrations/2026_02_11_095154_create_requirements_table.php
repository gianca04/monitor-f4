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
        Schema::create('requirements', function (Blueprint $table) {
            $table->id();

            $table->string('product_description')->index(); // Index for faster searching

            $table->foreignId('requirement_type_id')
                ->constrained('requirement_types')
                ->cascadeOnDelete(); // Maintain referential integrity

            $table->foreignId('unit_id')
                ->constrained('units')
                ->cascadeOnDelete();

            $table->timestamps();

            // Composite index for common filtering
            $table->index(['requirement_type_id', 'product_description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requirements');
    }
};
