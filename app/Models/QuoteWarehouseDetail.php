<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo QuoteWarehouseDetail - Detalles de Atención de Almacén (REFACTORIZADO)
 *
 * IMPORTANTE: Este modelo ha sido refactorizado para actuar como REGISTRO CONSOLIDADO
 * de la atención del almacén. Los detalles granulares de CADA entrega individual
 * se registran ahora en DispatchTransaction.
 *
 * Estructura anterior (problemática):
 * - Un QuoteWarehouseDetail = una atención completa
 * - Imposible manejar entregas parciales de múltiples fuentes
 * - Dificultad para rastrear costos específicos
 *
 * Nueva estructura (actual):
 * - QuoteWarehouseDetail = Referencia a la solicitud de almacén
 * - DispatchTransaction = Cada transacción de entrega individual
 * - ProjectRequirement → DispatchTransactions (1 → *)
 *
 * Relación: Un requerimiento puede tener múltiples entregas desde almacén,
 * y potencialmente desde otras fuentes (proveedor, externo).
 *
 * @property int $id Identificador único del detalle
 * @property int $project_requirement_id ID del requerimiento de proyecto asociado
 * @property int|null $quote_warehouse_id ID de la atención de almacén
 * @property float|null $attended_quantity Cantidad total atendida por almacén para este ítem
 * @property float|null $additional_cost Costo adicional de atención (DEPRECATED: usar DispatchTransaction)
 * @property string|null $cost_description Descripción del costo (DEPRECATED: usar DispatchTransaction)
 * @property string|null $comment Comentario sobre la atención
 * @property int|null $location_origin_id ID del lugar de origen
 * @property int|null $location_destination_id ID del lugar de destino
 * @property int|null $tool_unit_id ID de la unidad de herramienta
 * @property \Illuminate\Support\Carbon|null $created_at Fecha de creación del registro
 * @property \Illuminate\Support\Carbon|null $updated_at Fecha de última actualización del registro
 *
 * @property-read \App\Models\QuoteWarehouse $quoteWarehouse La atención de almacén asociada
 * @property-read \App\Models\ProjectRequirement $projectRequirement El requerimiento de proyecto asociado
 * @property-read \App\Models\Location|null $locationOrigin Lugar de origen
 * @property-read \App\Models\Location|null $locationDestination Lugar de destino
 * @property-read \App\Models\ToolUnit|null $toolUnit Unidad de herramienta vinculada
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DispatchTransaction> $dispatchTransactions Las transacciones de entrega desde almacén
 *
 * DEPRECACIÓN: Se recomienda migrar a usar directamente DispatchTransaction
 * para nuevas funcionalidades. Este modelo se mantiene por compatibilidad hacia atrás.
 */
class QuoteWarehouseDetail extends Model
{
    use HasFactory;

    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'quote_warehouse_details';

    /**
     * Los atributos que pueden ser asignados masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'quote_warehouse_id',
        'project_requirement_id',
        'attended_quantity',
        'comment',
        'location_origin_id',
        'location_destination_id',
        'additional_cost',
        'cost_description',
        'tool_unit_id',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'attended_quantity' => 'decimal:2',
        'additional_cost' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Obtiene la atención de almacén asociada.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function quoteWarehouse(): BelongsTo
    {
        return $this->belongsTo(QuoteWarehouse::class, 'quote_warehouse_id');
    }

    /**
     * Obtiene el requerimiento de proyecto asociado.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function projectRequirement(): BelongsTo
    {
        return $this->belongsTo(ProjectRequirement::class, 'project_requirement_id');
    }

    /**
     * Obtiene el lugar de origen asociado.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function locationOrigin(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_origin_id');
    }

    /**
     * Obtiene el lugar de destino asociado.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function locationDestination(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_destination_id');
    }

    /**
     * Obtiene las transacciones de entrega desde almacén.
     *
     * Helper que filtra las transacciones de entrega de este requerimiento cuando
     * la fuente es almacén. Use DispatchTransaction directamente para nuevas funcionalidades.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function dispatchTransactions()
    {
        if (!$this->projectRequirement) {
            return DispatchTransaction::whereNull('id'); // Empty query
        }
        
        return $this->projectRequirement
            ->dispatchTransactions()
            ->where('source_type', 'warehouse');
    }

    /**
     * Relación: Un detalle de almacén puede tener muchos consumos traza.
     */
    public function projectConsumptions()
    {
        return $this->hasMany(ProjectConsumption::class, 'quote_warehouse_detail_id');
    }

    /**
     * Scope para incluir el registro de requerimiento de proyecto.
     */
    public function scopeWithProjectRequirement($query)
    {
        return $query->with(['projectRequirement.quoteDetail.pricelist', 'projectRequirement.requirement']);
    }

    public function toolUnit(): BelongsTo
    {
        return $this->belongsTo(ToolUnit::class, 'tool_unit_id');
    }
}
