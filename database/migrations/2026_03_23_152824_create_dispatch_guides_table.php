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
        Schema::create('dispatch_guides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_warehouse_id')->constrained('quote_warehouse')->cascadeOnDelete();
            $table->string('guide_number');
            $table->foreignId('location_origin_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('location_destination_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->timestamp('transfer_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispatch_guides');
    }
};
