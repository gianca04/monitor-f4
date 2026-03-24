<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo DispatchTransaction
 * 
 * Representa una transacción de despacho individual de un item.
 * Cada transacción registra detalles específicos de cómo se despachó un producto,
 * incluyendo cantidad, precio, costos adicionales y información de la compra.
 * 
 * @property int $id
 * @property int $quote_warehouse_id - ID del almacén de cotización
 * @property int $project_requirement_id - ID del requerimiento del proyecto
 * @property int $employee_id - ID del empleado que registró la transacción
 * @property float $quantity - Cantidad despachada
 * @property bool $is_external_purchase - ¿Es compra externa/tercerizada?
 * @property float $price_unit - Precio unitario del producto
 * @property string $supplier_name - Nombre del proveedor (si es compra externa)
 * @property string $receipt_number - Número de comprobante/factura
 * @property float $additional_cost - Costos adicionales (flete, etc)
 * @property string $cost_description - Descripción del costo adicional
 * @property string $comment - Observaciones del despacho
 * @property int $tool_unit_id - ID de la unidad de herramienta (si aplica)
 * @property int $dispatch_guide_id - ID de la guía de despacho
 * @property timestamp $created_at
 * @property timestamp $updated_at
 */
class DispatchTransaction extends Model
{
    use HasFactory;

    /**
     * Campos no necesarios en la tabla dispatch_transactions
     * 
     * Estos campos pueden ser asignados en masa sin temor a inyecciones masivas.
     */
    protected $fillable = [
        'quote_warehouse_id',        // ID del almacén de cotización
        'project_requirement_id',    // ID del requerimiento del proyecto
        'employee_id',               // ID del empleado registrador
        'quantity',                  // Cantidad a despachar
        'is_external_purchase',      // ¿Compra externa? (bool)
        'price_unit',                // Precio unitario del producto
        'supplier_name',             // Nombre del proveedor externo
        'receipt_number',            // Número de comprobante/factura
        'additional_cost',           // Costos adicionales (flete, movilidad, etc)
        'cost_description',          // Descripción del costo adicional
        'comment',                   // Nota/observación del despacho
        'tool_unit_id',              // ID de la unidad de herramienta específica
        'dispatch_guide_id',         // ID de la guía de despacho asociada
    ];

    /**
     * Conversión de tipos para valores almacenados
     * 
     * Convierte automáticamente valores a los tipos especificados
     * al acceder o guardar en la base de datos.
     */
    protected $casts = [
        'quantity' => 'decimal:2',           // Cantidad con 2 decimales
        'is_external_purchase' => 'boolean', // Compra externa sí/no
        'price_unit' => 'decimal:2',         // Precio unitario con 2 decimales
        'additional_cost' => 'decimal:2',    // Costo adicional con 2 decimales
    ];

    // ===== RELACIONES =====

    /**
     * Relación: Transacción → Almacén de Cotización
     * Una transacción pertenece a un almacén de cotización
     */
    public function quoteWarehouse(): BelongsTo
    {
        return $this->belongsTo(QuoteWarehouse::class);
    }

    /**
     * Relación: Transacción → Requerimiento del Proyecto
     * Una transacción está vinculada a un requerimiento específico del proyecto
     */
    public function projectRequirement(): BelongsTo
    {
        return $this->belongsTo(ProjectRequirement::class);
    }

    /**
     * Relación: Transacción → Empleado/Usuario
     * Una transacción es registrada por un empleado específico
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * Relación: Transacción → Ubicación Origen
     * Ubicación de donde se despachó el producto
     */
    public function originLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_origin_id');
    }

    /**
     * Relación: Transacción → Ubicación Destino
     * Ubicación a donde se despachó el producto
     */
    public function destinationLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_destination_id');
    }

    /**
     * Relación: Transacción → Unidad de Herramienta
     * Si el producto es una herramienta, referencia a su unidad específica
     */
    public function toolUnit(): BelongsTo
    {
        return $this->belongsTo(ToolUnit::class, 'tool_unit_id');
    }

    /**
     * Relación: Transacción → Guía de Despacho
     * La transacción pertenece a una guía de despacho específica
     */
    public function dispatchGuide(): BelongsTo
    {
        return $this->belongsTo(DispatchGuide::class);
    }

    // ===== ATRIBUTOS CALCULADOS =====

    /**
     * Calcula el subtotal de la transacción
     * 
     * Fórmula: Subtotal = (cantidad × precio_unitario) + costo_adicional
     * 
     * @return float Subtotal redondeado a 2 decimales
     * 
     * @example
     * $transaction->subtotal  // 150.50
     */
    public function getSubtotalAttribute(): float
    {
        $itemTotal = ($this->quantity ?? 0) * ($this->price_unit ?? 0);
        return round($itemTotal + ($this->additional_cost ?? 0), 2);
    }

    /**
     * Calcula el total del item SIN incluir costos adicionales
     * 
     * Fórmula: Total del Item = cantidad × precio_unitario
     * 
     * Este valor NO incluye flete, movilidad u otros costos adicionales.
     * Útil para cálculos de costo del producto sin gastos logísticos.
     * 
     * @return float Total del item redondeado a 2 decimales
     * 
     * @example
     * $transaction->item_total  // 120.00 (sin incluir costo adicional)
     */
    public function getItemTotalAttribute(): float
    {
        return round(($this->quantity ?? 0) * ($this->price_unit ?? 0), 2);
    }
}
