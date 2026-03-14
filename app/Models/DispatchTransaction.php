<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo DispatchTransaction - Transacción Individual de Entrega
 *
 * Representa una transacción individual de entrega de un requerimiento de proyecto.
 * Un ProjectRequirement puede tener múltiples DispatchTransactions si su cantidad se
 * atiende desde diferentes fuentes (almacén, proveedor, externo).
 *
 * Casos de uso:
 * - Requerimiento de 60 unidades:
 *   • Transacción 1: 40 unidades del almacén A (location X → Y)
 *   • Transacción 2: 20 unidades de proveedor B (location P → Q)
 * - Cada transacción con sus propios costos y ubicaciones
 *
 * @property int $id Identificador único de la transacción
 * @property int $project_requirement_id ID del requerimiento de proyecto asociado
 * @property int|null $quote_warehouse_id ID de la atención de almacén (LEGACY, considerar deprecación)
 * @property int|null $employee_id ID del empleado que ejecutó la entrega
 * @property decimal $quantity Cantidad entregada en ESTA transacción específica
 * @property int|null $location_origin_id ID del lugar de origen
 * @property int|null $location_destination_id ID del lugar de destino
 * @property decimal|null $additional_cost Costo adicional específico de esta transacción
 * @property string|null $cost_description Descripción detallada del costo adicional
 * @property string|null $comment Comentarios sobre la ejecución
 * @property int|null $tool_unit_id ID de la unidad de herramienta utilizada (si aplica)
 * @property string $source_type Tipo de fuente: 'warehouse', 'provider', 'external'
 * @property string|null $source_reference Referencia adicional según source_type (quote_warehouse_id, vendor_id, etc.)
 * @property \Illuminate\Support\Carbon|null $dispatch_date Fecha y hora de ejecución de la entrega
 * @property \Illuminate\Support\Carbon|null $created_at Fecha de creación del registro
 * @property \Illuminate\Support\Carbon|null $updated_at Fecha de última actualización del registro
 *
 * @property-read \App\Models\ProjectRequirement $projectRequirement El requerimiento asociado
 * @property-read \App\Models\QuoteWarehouse|null $quoteWarehouse La atención de almacén asociada (Legacy)
 * @property-read \App\Models\User|null $employee El empleado que ejecutó la entrega
 * @property-read \App\Models\Location|null $originLocation Ubicación de origen
 * @property-read \App\Models\Location|null $destinationLocation Ubicación de destino
 * @property-read \App\Models\ToolUnit|null $toolUnit Unidad de herramienta utilizada
 */
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
        'tool_unit_id',
        'source_type',
        'source_reference',
        'dispatch_date',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'additional_cost' => 'decimal:2',
        'dispatch_date' => 'datetime',
        'source_type' => \App\Enums\DispatchSourceType::class,
    ];

    /**
     * Obtiene la atención de almacén asociada (Legacy).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function quoteWarehouse(): BelongsTo
    {
        return $this->belongsTo(QuoteWarehouse::class);
    }

    /**
     * Obtiene el requerimiento de proyecto asociado.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function projectRequirement(): BelongsTo
    {
        return $this->belongsTo(ProjectRequirement::class);
    }

    /**
     * Obtiene el empleado que ejecutó la entrega.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * Obtiene la ubicación de origen.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function originLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_origin_id');
    }

    /**
     * Obtiene la ubicación de destino.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function destinationLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_destination_id');
    }

    /**
     * Obtiene la unidad de herramienta utilizada.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function toolUnit(): BelongsTo
    {
        return $this->belongsTo(ToolUnit::class, 'tool_unit_id');
    }

    /**
     * Valida que la cantidad total despachada no exceda la cantidad requerida.
     *
     * Lanza excepción si se intenta despatchar más unidades de las que solicita
     * el ProjectRequirement asociado.
     *
     * @return void
     * @throws \Exception
     */
    public function validateQuantity(): void
    {
        $projectRequirement = $this->projectRequirement;
        if (!$projectRequirement) {
            throw new \Exception('ProjectRequirement not found for this dispatch transaction.');
        }

        // Calcular total despachado incluyendo esta transacción
        $totalDispatched = $projectRequirement
            ->dispatchTransactions()
            ->sum('quantity');

        if ($totalDispatched > $projectRequirement->quantity) {
            throw new \Exception(
                "Cantidad excedida. Se intentó despatchar {$totalDispatched} unidades " .
                "pero solo se requieren {$projectRequirement->quantity}."
            );
        }
    }
}
