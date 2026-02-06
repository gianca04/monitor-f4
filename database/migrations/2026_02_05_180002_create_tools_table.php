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
        // 1. Catalog Table (Que es)
        Schema::create('tools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // Moved specific attributes to tool_units
            $table->foreignId('tool_brand_id')->nullable()
                ->constrained('tool_brands')->nullOnDelete();
            $table->foreignId('tool_category_id')->nullable()
                ->constrained('tool_categories')->nullOnDelete();
            $table->string('model')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Physical Units Table (Cual es)
        Schema::create('tool_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tool_id')->constrained('tools')->cascadeOnDelete();
            $table->string('internal_code')->nullable(); // Ex-code
            $table->string('serial_number')->nullable();
            $table->string('certification_document')->nullable();
            $table->date('certification_expiry')->nullable();
            $table->string('status')->default('Disponible');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tool_units');
        Schema::dropIfExists('tools');
    }
};
