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
        Schema::table('projects', function (Blueprint $table) {
            $table->double('emergency_response_time_hrs')->nullable()->after('end_date');
            $table->double('emergency_attendance_time_hrs')->nullable()->after('emergency_response_time_hrs');
            $table->double('corrective_quote_upload_time_hrs')->nullable()->after('emergency_attendance_time_hrs');
            $table->double('corrective_execution_time_hrs')->nullable()->after('corrective_quote_upload_time_hrs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'emergency_response_time_hrs',
                'emergency_attendance_time_hrs',
                'corrective_quote_upload_time_hrs',
                'corrective_execution_time_hrs',
            ]);
        });
    }
};
