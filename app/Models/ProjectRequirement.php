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
        'type',
        'quantity',
        'price_unit',
        'comments',
    ];

    protected $casts = [
        'project_id' => 'integer',
        'requirementable_id' => 'integer',
        'quantity' => 'decimal:2',
        'price_unit' => 'decimal:2',
        'type' => \App\Enums\RequirementType::class,
    ];

    protected $appends = ['subtotal', 'product_name', 'unit_name', 'consumable_type_name'];

    /**
     * Get the consolidated product name.
     */
    public function getProductNameAttribute(): string
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
     * Get the consolidated unit name.
     */
    public function getUnitNameAttribute(): string
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
}
