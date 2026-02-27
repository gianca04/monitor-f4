<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo RequirementType (Tipos de Requerimiento)
 *
 * Clasifica los requerimientos (ej: Herramientas, Materiales, EPPs, etc.)
 *
 * @property int $id Identificador único
 * @property string $name Nombre del tipo de requerimiento
 * @property bool $is_reusable Indica si los requerimientos de este tipo son reutilizables
 *                             (no se descuentan del stock al ser utilizados, ej: herramientas)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Requirement[] $requirements
 */
class RequirementType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_reusable',
    ];

    protected $casts = [
        'is_reusable' => 'boolean',
    ];

    /**
     * Relación: Un tipo de requerimiento tiene muchos requerimientos.
     */
    public function requirements(): HasMany
    {
        return $this->hasMany(Requirement::class);
    }
}
