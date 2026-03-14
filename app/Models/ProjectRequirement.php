<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'requirementable_id',
        'requirementable_type',
        'requirement_list_id',
        'type',
        'quantity',
        'price_unit',
        'product_name',
        'unit_name',
        'requirement_type',
        'comments',
    ];

    protected $casts = [
        'project_id' => 'integer',
        'requirementable_id' => 'integer',
        'requirement_list_id' => 'integer',
        'quantity' => 'decimal:2',
        'price_unit' => 'decimal:2',
        'type' => \App\Enums\RequirementType::class,
    ];

    protected $appends = ['subtotal', 'consumable_type_name'];

    protected static function booted()
    {
        static::creating(function ($projectRequirement) {
            // Capture snapshots from requirementable relation if they are not already set
            if ($projectRequirement->requirementable) {
                if (empty($projectRequirement->product_name)) {
                    $projectRequirement->product_name = $projectRequirement->product_name_calculated;
                }
                if (empty($projectRequirement->unit_name)) {
                    $projectRequirement->unit_name = $projectRequirement->unit_name_calculated;
                }
                if (empty($projectRequirement->requirement_type)) {
                    $projectRequirement->requirement_type = $projectRequirement->requirement_type_calculated;
                }
            }
        });
    }



    /**
     * Get the consolidated product name (Snapshot or Calculated).
     */
    public function getProductNameAttribute(): string
    {
        return $this->attributes['product_name'] ?? $this->product_name_calculated;
    }

    /**
     * Internal logic for calculating product name from relationships.
     */
    public function getProductNameCalculatedAttribute(): string
    {
        if ($this->requirementable instanceof Requirement) {
            return $this->requirementable->product_description ?? 'N/A';
        } elseif ($this->requirementable instanceof QuoteDetail) {
            return $this->requirementable->pricelist->sat_description ?? 'N/A';
        } elseif ($this->requirementable instanceof Tool) {
            return $this->requirementable->name ?? 'N/A';
        }
        return 'N/A';
    }

    /**
     * Get the consolidated unit name (Snapshot or Calculated).
     */
    public function getUnitNameAttribute(): string
    {
        return $this->attributes['unit_name'] ?? $this->unit_name_calculated;
    }

    /**
     * Internal logic for calculating unit name from relationships.
     */
    public function getUnitNameCalculatedAttribute(): string
    {
        if ($this->requirementable instanceof Requirement) {
            return $this->requirementable->unit->name ?? 'N/A';
        } elseif ($this->requirementable instanceof QuoteDetail) {
            return $this->requirementable->pricelist->unit->name ?? 'N/A';
        } elseif ($this->requirementable instanceof Tool) {
            return 'UND'; // Default for tools
        }
        return 'N/A';
    }

    /**
     * Internal logic for calculating requirement type from relationships.
     */
    public function getRequirementTypeCalculatedAttribute(): string
    {
        if ($this->requirementable instanceof Requirement) {
            return $this->requirementable->requirementType->name ?? 'Suministro';
        } elseif ($this->requirementable instanceof QuoteDetail) {
            return 'Suministro';
        } elseif ($this->requirementable instanceof Tool) {
            return $this->requirementable->type?->value ?? 'Herramienta';
        }
        return 'Suministro';
    }

    /**
     * Get the consolidated consumable type name.
     */
    public function getConsumableTypeNameAttribute(): string
    {
        return $this->type?->getLabel() ?? 'N/A';
    }

    /**
     * Calculate the subtotal attribute.
     *
     * @return float
     */
    public function getSubtotalAttribute(): float
    {
        return round($this->quantity * $this->price_unit, 2);
    }

    public function project(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function requirementable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    public function requirementList(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(RequirementList::class);
    }
}
