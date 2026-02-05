<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo ToolBrand - Catálogo de Marcas de Herramientas
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class ToolBrand extends Model
{
    use HasFactory;

    protected $table = 'tool_brands';

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Relación: Una marca tiene muchas herramientas.
     */
    public function tools(): HasMany
    {
        return $this->hasMany(Tool::class, 'tool_brand_id');
    }
}
