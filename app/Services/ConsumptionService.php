<?php

namespace App\Services;

use App\Models\ProjectConsumption;
use App\Models\QuoteWarehouseDetail;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Servicio centralizado para la lógica de consumo de materiales.
 *
 * Gestiona el stock, la validación de consumo y la distinción
 * entre requerimientos reutilizables (herramientas) y consumibles.
 */
class ConsumptionService
{
    /**
     * Determina si un detalle de almacén corresponde a un requerimiento reutilizable.
     *
     * Navega: QuoteWarehouseDetail → ProjectRequirement → Requirement → RequirementType
     */
    public function isReusable(int $quoteWarehouseDetailId): bool
    {
        $detail = QuoteWarehouseDetail::with(
            'projectRequirement.requirement.requirementType'
        )->find($quoteWarehouseDetailId);

        if (!$detail) {
            return false;
        }

        return (bool) $detail->projectRequirement
            ?->requirement
            ?->requirementType
            ?->is_reusable;
    }

    /**
     * Calcula la cantidad total consumida de un detalle en un proyecto.
     */
    public function getTotalConsumed(int $projectId, int $quoteWarehouseDetailId): float
    {
        return (float) ProjectConsumption::where('project_id', $projectId)
            ->where('quote_warehouse_detail_id', $quoteWarehouseDetailId)
            ->sum('quantity');
    }

    /**
     * Calcula el stock restante de un detalle de almacén para un proyecto.
     *
     * Para requerimientos reutilizables, retorna la cantidad atendida original
     * ya que las herramientas no se "gastan".
     */
    public function getRemainingStock(int $projectId, int $quoteWarehouseDetailId): float
    {
        $detail = QuoteWarehouseDetail::find($quoteWarehouseDetailId);

        if (!$detail) {
            return 0;
        }

        // Los reutilizables siempre tienen stock completo
        if ($this->isReusable($quoteWarehouseDetailId)) {
            return (float) $detail->attended_quantity;
        }

        $totalConsumed = $this->getTotalConsumed($projectId, $quoteWarehouseDetailId);

        return max(0, (float) $detail->attended_quantity - $totalConsumed);
    }

    /**
     * Valida si se puede consumir una cantidad de un material.
     *
     * Los requerimientos reutilizables siempre pueden ser consumidos
     * (registramos uso, pero no descontamos stock).
     */
    public function canConsume(int $quoteWarehouseDetailId, float $quantity, ?int $projectId = null): bool
    {
        $detail = QuoteWarehouseDetail::find($quoteWarehouseDetailId);

        if (!$detail) {
            return false;
        }

        // Los reutilizables siempre se pueden "consumir" (registrar uso)
        if ($this->isReusable($quoteWarehouseDetailId)) {
            return true;
        }

        // Para consumibles, verificar stock
        $totalConsumed = $projectId
            ? $this->getTotalConsumed($projectId, $quoteWarehouseDetailId)
            : (float) ProjectConsumption::where('quote_warehouse_detail_id', $quoteWarehouseDetailId)->sum('quantity');

        return ($totalConsumed + $quantity) <= (float) $detail->attended_quantity;
    }

    /**
     * Obtiene la información de un material para el formulario,
     * incluyendo stock restante y si es reutilizable.
     *
     * @return array{remaining: float, is_reusable: bool, unit_name: string, sat_line: string, description: string}
     */
    public function getMaterialInfo(int $quoteWarehouseDetailId, int $projectId): array
    {
        $material = QuoteWarehouseDetail::with([
            'projectRequirement.quoteDetail.pricelist.unit',
            'projectRequirement.requirement.unit',
            'projectRequirement.requirement.requirementType',
        ])->find($quoteWarehouseDetailId);

        if (!$material || !$material->projectRequirement) {
            return [
                'remaining' => 0,
                'is_reusable' => false,
                'unit_name' => 'N/A',
                'sat_line' => 'N/A',
                'description' => 'N/A',
            ];
        }

        $req = $material->projectRequirement;
        $isReusable = (bool) $req->requirement?->requirementType?->is_reusable;

        $remaining = $isReusable
            ? (float) $material->attended_quantity
            : $this->getRemainingStock($projectId, $quoteWarehouseDetailId);

        return [
            'remaining' => $remaining,
            'is_reusable' => $isReusable,
            'unit_name' => $req->unit_name,
            'sat_line' => $req->quoteDetail?->pricelist?->sat_line ?? 'SUMINISTRO',
            'description' => $req->product_name,
        ];
    }

    /**
     * Obtiene los materiales disponibles para un proyecto.
     * (Centralizado desde WorkReport::getAvailableMaterials)
     * Refactorizado para usar Eloquent y Morphs (ToolUnit, Requirement, QuoteDetail).
     */
    public function getAvailableMaterials(int $projectId): Collection
    {
        $warehouseDetails = QuoteWarehouseDetail::with(['projectRequirement.requirementable.unit'])
            ->whereHas('projectRequirement', function ($query) use ($projectId) {
                $query->where('project_id', $projectId);
            })
            ->where('attended_quantity', '>', 0)
            ->get();

        return $warehouseDetails->map(function ($qwd) {
            $req = $qwd->projectRequirement;
            $type = $req->requirementable;

            $isReusable = 0;
            $satLine = 'SUMINISTRO';
            $satDescription = $req->comments ?? 'N/A';
            $unitName = 'Unid';

            if ($type instanceof \App\Models\Requirement) {
                $satDescription = $type->product_description;
                $unitName = $type->unit->name ?? 'Unid';
                $isReusable = $type->requirementType->is_reusable ? 1 : 0;
            } elseif ($type instanceof \App\Models\QuoteDetail) {
                $satDescription = $type->pricelist->sat_description ?? $req->comments;
                $satLine = $type->pricelist->sat_line ?? 'SUMINISTRO';
                $unitName = $type->pricelist->unit->name ?? 'Unid';
            } elseif ($type instanceof \App\Models\ToolUnit) {
                $satDescription = $type->tool->name ?? 'Herramienta';
                $satLine = 'HERRAMIENTAS';
                $unitName = 'UND';
                $isReusable = 1; // Tools are always reusable
            }

            return (object) [
                'id' => $qwd->id,
                'sat_description' => $satDescription,
                'sat_line' => $satLine,
                'unit_name' => $unitName,
                'attended_quantity' => $qwd->attended_quantity,
                'is_reusable' => $isReusable,
            ];
        });
    }

    /**
     * Sincroniza los materiales del formulario a la tabla project_consumptions.
     *
     * Elimina los consumos previos del work_report y crea los nuevos.
     * Usa transacción para garantizar integridad.
     *
     * @param \App\Models\WorkReport $workReport El reporte de trabajo
     * @param array $materials Array de materiales del formulario (cada uno con material_id y used_quantity)
     * @return void
     */
    public function syncConsumptions(\App\Models\WorkReport $workReport, array $materials): void
    {
        DB::transaction(function () use ($workReport, $materials) {
            // Eliminar consumos previos de este reporte
            ProjectConsumption::where('work_report_id', $workReport->id)->delete();

            // Crear nuevos registros de consumo
            foreach ($materials as $material) {
                $materialId = $material['material_id'] ?? null;
                $usedQuantity = (float) ($material['used_quantity'] ?? 0);

                if (!$materialId || $usedQuantity <= 0) {
                    continue;
                }

                ProjectConsumption::create([
                    'project_id' => $workReport->project_id,
                    'quote_warehouse_detail_id' => $materialId,
                    'work_report_id' => $workReport->id,
                    'quantity' => $usedQuantity,
                    'consumed_at' => $workReport->report_date ?? now(),
                ]);
            }
        });
    }
}
