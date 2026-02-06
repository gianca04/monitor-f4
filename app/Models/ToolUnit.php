<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ToolUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'tool_id',
        'internal_code',
        'serial_number',
        'status',
        'certification_document',
        'certification_expiry',
    ];

    protected $casts = [
        'certification_expiry' => 'date',
        'tool_id' => 'integer',
    ];

    public function tool(): BelongsTo
    {
        return $this->belongsTo(Tool::class);
    }

    public function projectTools(): HasMany
    {
        return $this->hasMany(ProjectTool::class, 'tool_unit_id');
    }

    public static function generateNextInternalCode(): string
    {
        $last = self::where('internal_code', 'like', 'HRR-%')
            ->selectRaw('MAX(CAST(SUBSTRING_INDEX(internal_code, "-", -1) AS UNSIGNED)) as max_num')
            ->first();

        $number = ($last->max_num ?? 0) + 1;

        return 'HRR-' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
}
