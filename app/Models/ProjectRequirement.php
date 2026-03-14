<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Modelo ProjectRequirement - Requerimiento de Proyecto
 *
 * Representa un requerimiento individual dentro de un proyecto.
 * Puede ser:
 * - Un suministro específico (vinculado a Requirement)
 * - Una especificación del presupuesto (vinculada a QuoteDetail)
 * - Una herramienta de trabajo (vinculada a Tool)
 *
 * NOTA: Este modelo CENTRALIZA la demanda de items. Las TRANSACCIONES DE ENTREGA
 * se registran en DispatchTransaction, permitiendo múltiples entregas desde diferentes
 * fuentes para un mismo requerimiento.
 *
 * Ejemplo de uso:
 * - ProjectRequirement: 60 unidades de tubería requeridas
 *   • DispatchTransaction 1: 40 unidades del almacén (date: 2026-03-14, cost: $100)
 *   • DispatchTransaction 2: 20 unidades de proveedor externo (date: 2026-03-15, cost: $150)
 *
 * @property int $id Identificador único del requerimiento
 * @property int $project_id ID del proyecto asociado
 * @property int $requirementable_id ID del objeto vinculado (Requirement, QuoteDetail, Tool)
 * @property string $requirementable_type Tipo del objeto vinculado (polimórfico)
 * @property int|null $dispatch_guide_id ID de la guía de despacho asociada
 * @property \App\Enums\RequirementType $type Tipo de requerimiento (consumable, reusable)
 * @property decimal $quantity Cantidad total requerida (PLAN)
 * @property decimal $price_unit Precio unitario
 * @property string|null $product_name Descripción del producto (snapshot)
 * @property string|null $unit_name Nombre de la unidad (snapshot)
 * @property string|null $requirement_type Tipo de requerimiento (Suministro, Herramienta, etc.)
 * @property string|null $comments Comentarios adicionales
 * @property \Illuminate\Support\Carbon|null $created_at Fecha de creación del registro
 * @property \Illuminate\Support\Carbon|null $updated_at Fecha de última actualización del registro
 *
 * @property-read \App\Models\Project $project El proyecto asociado
 * @property-read \Illuminate\Database\Eloquent\Model $requirementable El objeto vinculado (polymorphic)
 * @property-read \App\Models\DispatchGuide|null $dispatchGuide La guía de despacho asociada
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DispatchTransaction> $dispatchTransactions Las transacciones de entrega asociadas
 *
 * @property-read float $subtotal Subtotal calculado (quantity * price_unit)
 * @property-read string $consumable_type_name Nombre legible del tipo de consumible
 * @property-read float $total_dispatched Total de cantidad entregada (suma de DispatchTransaction)
 * @property-read float $remaining_quantity Cantidad pendiente de entregar (quantity - total_dispatched)
 * @property-read float $total_cost Costo total en entregas (suma de additional_cost en DispatchTransaction)
 */
class ProjectRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'requirementable_id',
        'requirementable_type',
        'dispatch_guide_id',
        'type',
        'quantity',
        'price_unit',
        'product_name',
        'unit_name',
        'requirement_type',
        'comments',
    ];

    protected $casts = [
        'project_id' => 'integer',
        'requirementable_id' => 'integer',
        'dispatch_guide_id' => 'integer',
        'quantity' => 'decimal:2',
        'price_unit' => 'decimal:2',
        'type' => \App\Enums\RequirementType::class,
    ];

    protected $appends = ['subtotal', 'consumable_type_name', 'total_dispatched', 'remaining_quantity', 'total_cost'];

    protected static function booted()
    {
        static::creating(function ($projectRequirement) {
            // Capture snapshots from requirementable relation if they are not already set
            if ($projectRequirement->requirementable) {
                if (empty($projectRequirement->product_name)) {
                    $projectRequirement->product_name = $projectRequirement->product_name_calculated;
                }
                if (empty($projectRequirement->unit_name)) {
                    $projectRequirement->unit_name = $projectRequirement->unit_name_calculated;
                }
                if (empty($projectRequirement->requirement_type)) {
                    $projectRequirement->requirement_type = $projectRequirement->requirement_type_calculated;
                }
            }
        });
    }



    /**
     * Get the consolidated product name (Snapshot or Calculated).
     */
    public function getProductNameAttribute(): string
    {
        return $this->attributes['product_name'] ?? $this->product_name_calculated;
    }

    /**
     * Internal logic for calculating product name from relationships.
     */
    public function getProductNameCalculatedAttribute(): string
    {
        if ($this->requirementable instanceof Requirement) {
            return $this->requirementable->product_description ?? 'N/A';
        } elseif ($this->requirementable instanceof QuoteDetail) {
            return $this->requirementable->pricelist->sat_description ?? 'N/A';
        } elseif ($this->requirementable instanceof Tool) {
            return $this->requirementable->name ?? 'N/A';
        }
        return 'N/A';
    }

    /**
     * Get the consolidated unit name (Snapshot or Calculated).
     */
    public function getUnitNameAttribute(): string
    {
        return $this->attributes['unit_name'] ?? $this->unit_name_calculated;
    }

    /**
     * Internal logic for calculating unit name from relationships.
     */
    public function getUnitNameCalculatedAttribute(): string
    {
        if ($this->requirementable instanceof Requirement) {
            return $this->requirementable->unit->name ?? 'N/A';
        } elseif ($this->requirementable instanceof QuoteDetail) {
            return $this->requirementable->pricelist->unit->name ?? 'N/A';
        } elseif ($this->requirementable instanceof Tool) {
            return 'UND'; // Default for tools
        }
        return 'N/A';
    }

    /**
     * Internal logic for calculating requirement type from relationships.
     */
    public function getRequirementTypeCalculatedAttribute(): string
    {
        if ($this->requirementable instanceof Requirement) {
            return $this->requirementable->requirementType->name ?? 'Suministro';
        } elseif ($this->requirementable instanceof QuoteDetail) {
            return 'Suministro';
        } elseif ($this->requirementable instanceof Tool) {
            return $this->requirementable->type?->value ?? 'Herramienta';
        }
        return 'Suministro';
    }

    /**
     * Get the consolidated consumable type name.
     */
    public function getConsumableTypeNameAttribute(): string
    {
        return $this->type?->getLabel() ?? 'N/A';
    }

    /**
     * Calculate the subtotal attribute.
     *
     * @return float
     */
    public function getSubtotalAttribute(): float
    {
        return round((float)$this->quantity * (float)$this->price_unit, 2);
    }

    /**
     * Obtiene el total de cantidad entregada (suma de DispatchTransactions).
     *
     * @return float
     */
    public function getTotalDispatchedAttribute(): float
    {
        return (float) ($this->dispatchTransactions()->sum('quantity') ?? 0);
    }

    /**
     * Obtiene la cantidad pendiente de entregar.
     *
     * @return float
     */
    public function getRemainingQuantityAttribute(): float
    {
        return round(floatval($this->quantity) - $this->total_dispatched, 2);
    }

    /**
     * Obtiene el costo total de todas las transacciones de entrega.
     *
     * @return float
     */
    public function getTotalCostAttribute(): float
    {
        return round((float) $this->dispatchTransactions()
            ->sum('additional_cost'), 2);
    }

    /**
     * Verifica si el requerimiento ha sido completamente entregado.
     *
     * @return bool
     */
    public function isFullyDispatched(): bool
    {
        return $this->total_dispatched >= floatval($this->quantity);
    }

    public function project(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function requirementable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    public function dispatchGuide(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DispatchGuide::class);
    }

    /**
     * Obtiene todas las transacciones de entrega asociadas a este requerimiento.
     *
     * Una transacción representa una entrega individual de una parte o la totalidad
     * de la cantidad requerida, potencialmente desde diferentes fuentes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dispatchTransactions(): HasMany
    {
        return $this->hasMany(DispatchTransaction::class);
    }
}
