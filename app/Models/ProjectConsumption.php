<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo ProjectConsumption
 *
 * Representa el consumo de materiales en un proyecto.
 *
 * @property int $id Identificador único
 * @property int $project_id ID del proyecto asociado
 * @property int $quote_warehouse_detail_id ID del detalle de almacén (trazabilidad)
 * @property int|null $work_report_id ID del reporte de trabajo (opcional)
 * @property float $quantity Cantidad realmente instalada/gastada
 * @property \Illuminate\Support\Carbon $consumed_at Fecha del consumo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read bool $is_reusable Indica si el requerimiento asociado es reutilizable (accessor virtual)
 * @property-read \App\Models\Project $project
 * @property-read \App\Models\QuoteWarehouseDetail $quoteWarehouseDetail
 * @property-read \App\Models\WorkReport|null $workReport
 *
 * @method static \Illuminate\Database\Eloquent\Builder reusable() Solo consumos de requerimientos reutilizables
 * @method static \Illuminate\Database\Eloquent\Builder notReusable() Solo consumos de requerimientos consumibles
 * @method static \Illuminate\Database\Eloquent\Builder withPricelist() Eager-load con pricelist y requirement
 */
class ProjectConsumption extends Model
{
    use HasFactory;

    protected $table = 'project_consumptions';

    protected $fillable = [
        'project_id',
        'quote_warehouse_detail_id',
        'work_report_id',
        'quantity',
        'consumed_at',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'consumed_at' => 'date',
        'work_report_id' => 'integer',
        'project_id' => 'integer',
        'quote_warehouse_detail_id' => 'integer',
    ];

    /**
     * Relación: Un consumo pertenece a un proyecto.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
    public function quoteWarehouseDetail(): BelongsTo
    {
        return $this->belongsTo(QuoteWarehouseDetail::class, 'quote_warehouse_detail_id');
    }

    public function workReport(): BelongsTo
    {
        return $this->belongsTo(WorkReport::class);
    }


    public function scopeWithPricelist($query)
    {
        return $query->with(['quoteWarehouseDetail.projectRequirement.quoteDetail.pricelist', 'quoteWarehouseDetail.projectRequirement.requirement']);
    }

    /**
     * Determina si el consumo corresponde a un tipo de requerimiento reutilizable.
     * Navega: quoteWarehouseDetail → projectRequirement → requirement → requirementType
     */
    public function getIsReusableAttribute(): bool
    {
        return (bool) $this->quoteWarehouseDetail
            ?->projectRequirement
            ?->requirement
            ?->requirementType
            ?->is_reusable;
    }

    /**
     * Scope: solo consumos de requerimientos reutilizables.
     */
    public function scopeReusable($query)
    {
        return $query->whereHas('quoteWarehouseDetail.projectRequirement.requirement.requirementType', function ($q) {
            $q->where('is_reusable', true);
        });
    }

    /**
     * Scope: solo consumos de requerimientos NO reutilizables (consumibles).
     */
    public function scopeNotReusable($query)
    {
        return $query->whereHas('quoteWarehouseDetail.projectRequirement.requirement.requirementType', function ($q) {
            $q->where('is_reusable', false);
        });
    }

    /**
     * Valida si se puede consumir una cantidad de un material.
     * Delega la lógica al ConsumptionService.
     */
    public static function canConsume(int $quoteWarehouseDetailId, float $quantity, ?int $projectId = null): bool
    {
        return app(\App\Services\ConsumptionService::class)
            ->canConsume($quoteWarehouseDetailId, $quantity, $projectId);
    }
}
