<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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

    public function projectRequirements(): MorphMany
    {
        return $this->morphMany(ProjectRequirement::class, 'requirementable');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'Disponible');
    }
}
