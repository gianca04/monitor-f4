<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\ToolUnit;
use App\Models\Tool;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For each ProjectRequirement where requirementable_type is ToolUnit::class
        // change it to Tool::class and update the ID to the tool_id of that unit
        $requirements = DB::table('project_requirements')
            ->where('requirementable_type', ToolUnit::class)
            ->get();

        foreach ($requirements as $req) {
            $unit = DB::table('tool_units')->where('id', $req->requirementable_id)->first();
            if ($unit) {
                DB::table('project_requirements')
                    ->where('id', $req->id)
                    ->update([
                        'requirementable_type' => Tool::class,
                        'requirementable_id' => $unit->tool_id,
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is data-only and generally irreversible safely without a backup
    }
};
