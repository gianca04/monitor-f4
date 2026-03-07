<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuoteGroup extends Model
{
    use HasFactory;

    /**
     * Los atributos que pueden ser asignados masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'quote_id',
        'name',
        'order',
    ];

    /**
     * Obtiene la cotización a la que pertenece este grupo.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Quote, \App\Models\QuoteGroup>
     */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    /**
     * Obtiene los detalles que pertenecen a este grupo.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\QuoteDetail>
     */
    public function quoteDetails(): HasMany
    {
        return $this->hasMany(QuoteDetail::class, 'quote_group_id');
    }
}
