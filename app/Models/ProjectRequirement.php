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

    protected $appends = ['subtotal'];

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
