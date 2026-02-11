<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'requirement_id',
        'quote_detail_id',
        'quantity',
        'price_unit',
        'comments',
    ];

    protected $casts = [
        'project_id' => 'integer',
        'requirement_id' => 'integer',
        'quantity' => 'decimal:2',
        'price_unit' => 'decimal:2',
    ];

    protected $appends = ['subtotal', 'product_name', 'unit_name', 'consumable_type_name'];

    /**
     * Get the consolidated product name.
     */
    public function getProductNameAttribute(): string
    {
        return $this->requirement->product_description
            ?? $this->quoteDetail->pricelist->sat_description
            ?? 'N/A';
    }

    /**
     * Get the consolidated unit name.
     */
    public function getUnitNameAttribute(): string
    {
        return $this->requirement->unit->name
            ?? $this->quoteDetail->pricelist->unit->name
            ?? 'N/A';
    }

    /**
     * Get the consolidated consumable type name.
     */
    public function getConsumableTypeNameAttribute(): string
    {
        return $this->requirement->consumableType->name
            ?? 'Suministro'; // Default for QuoteDetail items
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

    public function requirement(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Requirement::class);
    }

    public function quoteDetail(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(QuoteDetail::class);
    }
}
