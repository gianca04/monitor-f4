<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Observers\QuoteDetailObserver;
use App\Enums\QuoteItemType;

class QuoteDetail extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::observe(QuoteDetailObserver::class);
    }

    protected $table = 'quote_details';

    protected $fillable = [
        'quote_id',
        'quote_group_id',
        'pricelist_id',
        'subtotal',
        'item_type',
        'quantity',
        'unit_price',
        'comment',
        'line',
    ];

    protected $casts = [
        'item_type' => QuoteItemType::class,
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = ['subtotal'];

    public function getSubtotalAttribute(): float
    {
        return round((float) $this->quantity * (float) $this->unit_price, 2);
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class, 'quote_id');
    }

    public function quoteGroup(): BelongsTo
    {
        return $this->belongsTo(QuoteGroup::class, 'quote_group_id');
    }

    public function pricelist(): BelongsTo
    {
        return $this->belongsTo(Pricelist::class, 'pricelist_id');
    }

    public function projectRequirements(): HasMany
    {
        return $this->hasMany(ProjectRequirement::class);
    }
}
