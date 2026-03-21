<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo para los requerimientos de proyecto.
 *
 * Representa un requerimiento específico dentro de un proyecto, que puede estar asociado
 * a diferentes tipos de entidades (requerimientos del catálogo, detalles de cotización, herramientas, etc.)
 * a través de una relación polimórfica.
 *
 * @property int $id Identificador único del requerimiento.
 * @property int $project_id ID del proyecto al que pertenece el requerimiento.
 * @property int $requirementable_id ID de la entidad relacionada (polimórfica).
 * @property string $requirementable_type Tipo de la entidad relacionada (polimórfica).
 * @property string $type Tipo de requerimiento (Material, Servicio, etc.).
 * @property int|null $unit_id ID de la unidad de medida asociada.
 * @property string|null $name Nombre descriptivo de la unidad o del requerimiento.
 * @property float $quantity Cantidad requerida.
 * @property float $price_unit Precio unitario.
 * @property string|null $comments Comentarios adicionales.
 * @property \Carbon\Carbon $created_at Fecha de creación.
 * @property \Carbon\Carbon $updated_at Fecha de actualización.
 * @property-read float $subtotal Subtotal calculado (quantity * price_unit).
 * @property-read \App\Models\Project $project Proyecto al que pertenece.
 * @property-read \App\Models\Unit|null $unit Unidad de medida asociada.
 * @property-read mixed $requirementable Entidad relacionada polimórficamente.
 */
class ProjectRequirement extends Model
{
    use HasFactory;

    /**
     * Campos que pueden ser asignados masivamente.
     *
     * @var array<string>
     */
    protected $fillable = [
        'project_id',
        'requirementable_id',
        'requirementable_type',
        'type',
        'unit_id',
        'name',
        'quantity',
        'price_unit',
        'comments',
    ];

    /**
     * Conversiones de tipos para los atributos del modelo.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'project_id' => 'integer',
        'requirementable_id' => 'integer',
        'unit_id' => 'integer',
        'quantity' => 'decimal:2',
        'price_unit' => 'decimal:2',
        'type' => \App\Enums\RequirementType::class,
    ];

    /**
     * Atributos calculados que se incluyen en el array/JSON del modelo.
     *
     * @var array<string>
     */
    protected $appends = ['subtotal'];

    /**
     * Calcula el subtotal del requerimiento.
     *
     * @return float Subtotal redondeado a 2 decimales.
     */
    public function getSubtotalAttribute(): float
    {
        return round($this->quantity * $this->price_unit, 2);
    }

    /**
     * Relación con el proyecto.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Relación con la unidad de medida.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function unit(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Unit::class);
    }

    /**
     * Relación polimórfica con la entidad requerida.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function requirementable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }
}
