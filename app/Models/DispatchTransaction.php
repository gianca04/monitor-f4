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
        'location_origin_id',
        'location_destination_id',
        'additional_cost',
        'cost_description',
        'comment',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
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
}
