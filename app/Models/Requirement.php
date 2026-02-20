<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo Requirement (Lista maestra de requerimientos y consumibles utilziados)
 *
 * @property int $id
 * @property string $product_description Descripción del Producto
 * @property int $consumable_type_id ID Tipo Consumible
 * @property int $unit_id ID Medida

 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\Project $project
 * @property-read \App\Models\RequirementType $requirementType
 * @property-read \App\Models\Unit $unit
 */
class Requirement extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_description',
        'requirement_type_id',
        'unit_id',
    ];

    protected $casts = [
        'requirement_type_id' => 'integer',
        'unit_id' => 'integer',
    ];

    public function requirementType(): BelongsTo
    {
        return $this->belongsTo(RequirementType::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function projectRequirements(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProjectRequirement::class);
    }
}
