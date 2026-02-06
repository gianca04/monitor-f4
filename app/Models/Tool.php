<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Modelo Tool - Catálogo de Herramientas
 *
 * @property int $id
 * @property string $name
 * @property string|null $code
 * @property int|null $tool_brand_id
 * @property int|null $tool_category_id
 * @property string|null $model
 * @property string|null $serial_number
 * @property string|null $description
 * @property string|null $certification_document
 * @property \Illuminate\Support\Carbon|null $certification_expiry
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\ToolBrand|null $brand
 * @property-read \App\Models\ToolCategory|null $category
 */
class Tool extends Model
{
    use HasFactory;

    protected $table = 'tools';

    protected $fillable = [
        'name',
        'tool_brand_id',
        'tool_category_id',
        'model',
        'description',
    ];

    protected $casts = [
        'tool_brand_id' => 'integer',
        'tool_category_id' => 'integer',
    ];

    protected $appends = ['units_in_stock', 'total_units'];

    /**
     * Relación: Una herramienta pertenece a una marca.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(ToolBrand::class, 'tool_brand_id');
    }

    /**
     * Relación: Una herramienta pertenece a una categoría.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ToolCategory::class, 'tool_category_id');
    }

    /**
     * Relación: Una herramienta tiene muchas unidades físicas.
     */
    public function units(): HasMany
    {
        return $this->hasMany(ToolUnit::class);
    }

    public function getTotalUnitsAttribute(): int
    {
        return $this->units()->count();
    }

    public function getUnitsInStockAttribute(): int
    {
        return $this->units()->where('status', 'Disponible')->count();
    }
}
