<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DispatchTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_warehouse_id',
        'project_requirement_id',
        'employee_id',
        'quantity',
        'is_external_purchase',
        'price_unit',
        'supplier_name',
        'receipt_number',
        'additional_cost',
        'cost_description',
        'comment',
        'tool_unit_id',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'is_external_purchase' => 'boolean',
        'price_unit' => 'decimal:2',
        'additional_cost' => 'decimal:2',
    ];

    public function quoteWarehouse(): BelongsTo
    {
        return $this->belongsTo(QuoteWarehouse::class);
    }

    public function projectRequirement(): BelongsTo
    {
        return $this->belongsTo(ProjectRequirement::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function originLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_origin_id');
    }

    public function destinationLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_destination_id');
    }

    public function toolUnit(): BelongsTo
    {
        return $this->belongsTo(ToolUnit::class, 'tool_unit_id');
    }

    /**
     * Get the dispatch guide associated with this transaction.
     */
    public function dispatchGuide(): BelongsTo
    {
        return $this->belongsTo(DispatchGuide::class);
    }
}
