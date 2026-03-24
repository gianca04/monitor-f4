<?php

namespace App\Services;

use App\Models\ProjectRequirement;
use App\Models\Requirement;
use App\Models\QuoteDetail;
use App\Models\Tool;
use App\DTOs\ProjectRequirementDto;
use App\Enums\RequirementType;
use App\Enums\ToolType;

class ProjectRequirementService
{
    /**
     * Calcula los datos clave de un ProjectRequirement para poblar su respectivo DTO.
     * 
     * @param ProjectRequirement $req El requerimiento de proyecto a ser evaluado.
     * @return ProjectRequirementDto Data Transfer Object tipado con la información esencial.
     */
    public function calculateDetails(ProjectRequirement $req): ProjectRequirementDto
    {
        $dto = new ProjectRequirementDto();
        $dto->productName = $this->getProductName($req);
        $dto->unitName = $this->getUnitName($req);
        
        // Se valida que el tipo sea un Enum instanciado para prevenir errores (Expected object, found string).
        $typeName = 'N/A';
        if ($req->type instanceof RequirementType) {
            $typeName = $req->type->getLabel();
        } elseif (is_string($req->type) && class_exists(RequirementType::class)) {
            $enumType = RequirementType::tryFrom($req->type);
            $typeName = $enumType ? $enumType->getLabel() : $req->type;
        }
        $dto->consumableTypeName = $typeName;
        
        $dto->subtotal = round($req->quantity * $req->price_unit, 2);
        return $dto;
    }

    /**
     * Mapea y extrae los datos básicos dependiendo del tipo Entidad requerida. 
     * Lógica polimórfica (Requirement, QuoteDetail o Tool).
     * 
     * @param string $type El FQCN de la clase polimórfica (ej. App\Models\Requirement).
     * @param int $id El ID del registro de dicha clase.
     * @return array
     */
    public function mapFromRequirementable(string $type, int $id): array
    {
        $data = [];

        if ($type === Requirement::class) {
            $requirement = Requirement::with('unit', 'requirementType')->find($id);
            if ($requirement) {
                $data['name'] = $requirement->product_description;
                $data['unit_id'] = $requirement->unit_id;
                $data['unit_symbol'] = $requirement->unit?->symbol ?? 'UND';
                $data['requirement_type'] = $requirement->requirement_type_id;
                $data['type'] = $this->mapRequirementType($requirement->requirementType?->name ?? '');
            }
        } elseif ($type === QuoteDetail::class) {
            $quoteDetail = QuoteDetail::with('pricelist.unit')->find($id);
            if ($quoteDetail) {
                $data['name'] = $quoteDetail->name;
                $data['unit_id'] = $quoteDetail->pricelist?->unit?->id;
                $data['unit_symbol'] = $quoteDetail->pricelist?->unit?->symbol ?? 'UND';
                $data['requirement_type'] = null;
                $data['price_unit'] = $quoteDetail->unit_price;
                $data['quantity'] = $quoteDetail->quantity;
                $data['subtotal'] = round((float)$quoteDetail->quantity * (float)$quoteDetail->unit_price, 2);
                $data['type'] = RequirementType::CONSUMIBLE;
            }
        } elseif ($type === Tool::class) {
            $tool = Tool::find($id);
            if ($tool) {
                $data['name'] = $tool->name;
                $data['unit_id'] = 3; // 'Unidad'
                $data['unit_symbol'] = 'UND';
                $data['requirement_type'] = null;
                $data['type'] = $tool->type === ToolType::HERRAMIENTA ? RequirementType::HERRAMIENTA : RequirementType::EQUIPO;
            }
        }

        return $data;
    }

    private function getProductName(ProjectRequirement $req): string
    {
        if ($req->requirementable instanceof Requirement) {
            return $req->requirementable->product_description ?? 'N/A';
        } elseif ($req->requirementable instanceof QuoteDetail) {
            return $req->requirementable->pricelist->sat_description ?? 'N/A';
        } elseif ($req->requirementable instanceof Tool) {
            return $req->requirementable->name ?? 'N/A';
        }
        return 'N/A';
    }

    private function getUnitName(ProjectRequirement $req): string
    {
        if ($req->requirementable instanceof Requirement) {
            return $req->requirementable->unit->name ?? 'N/A';
        } elseif ($req->requirementable instanceof QuoteDetail) {
            return $req->requirementable->pricelist->unit->name ?? 'N/A';
        } elseif ($req->requirementable instanceof Tool) {
            return 'UND';
        }
        return 'N/A';
    }

    private function mapRequirementType(string $reqTypeName): RequirementType
    {
        $reqTypeName = strtolower($reqTypeName);
        if (str_contains($reqTypeName, 'material')) {
            return RequirementType::MATERIAL;
        } elseif (str_contains($reqTypeName, 'consumible') || str_contains($reqTypeName, 'suministro')) {
            return RequirementType::CONSUMIBLE;
        } elseif (str_contains($reqTypeName, 'herramienta')) {
            return RequirementType::HERRAMIENTA;
        } elseif (str_contains($reqTypeName, 'equipo')) {
            return RequirementType::EQUIPO;
        }
        return RequirementType::MATERIAL;
    }

    /**
     * Prepara y unifica los requerimientos de un proyecto con sus detalles de atención en almacén.
     * Este método centraliza la lógica de presentación (DTO) para la vista del editor de almacén,
     * determinando líneas SAT, asociando unidades de herramientas si aplica, y verificando cantidades atendidas.
     * 
     * Razonamiento arquitectónico: Movimos esta lógica fuera del Page/Controller de Filament para delegar
     * las reglas de negocio y estructuración de datos a la capa de Servicio, haciendo el código más testable y limpio.
     *
     * @param \Illuminate\Support\Collection $projectRequirements Colección de requerimientos base (ProjectRequirement).
     * @param \Illuminate\Support\Collection $warehouseDetailsCollection Requerimientos previamente ya procesados (QuoteWarehouseDetail).
     * @return \Illuminate\Support\Collection Colección final estructurada para enviar a la capa de UI.
     */
    public function getFormattedWarehouseDetails(\Illuminate\Support\Collection $projectRequirements, \Illuminate\Support\Collection $warehouseDetailsCollection): \Illuminate\Support\Collection
    {
        // Indexar por ID de requerimiento para que las búsquedas internas sean O(1) en lugar de O(n)
        $detailsByReqId = $warehouseDetailsCollection->whereNotNull('project_requirement_id')->keyBy('project_requirement_id');
        
        $details = [];

        foreach ($projectRequirements as $req) {
            $attended = 0;

            if (isset($detailsByReqId[$req->id])) {
                $attended = $detailsByReqId[$req->id]->attended_quantity;
            }

            $isTool = false;
            $toolId = null;
            $availableUnits = [];
            $satLine = '-';

            // Polimorfismo: Dependiendo de la entidad origen del requerimiento, obtenemos los datos relevantes
            if ($req->requirementable instanceof QuoteDetail) {
                $satLine = $req->requirementable->pricelist->sat_line ?? '-';
            } elseif ($req->requirementable instanceof Tool) {
                $satLine = 'HERRAMIENTAS';
                $isTool = true;
                $toolId = $req->requirementable->id;
                
                // Si el requerimiento es una herramienta, obtenemos dinámicamente sus unidades serializadas disponibles
                $availableUnits = \App\Models\ToolUnit::where('tool_id', $toolId)
                    ->where('status', 'Disponible')
                    ->get(['id', 'internal_code', 'serial_number'])
                    ->toArray();
            }

            $details[] = [
                'project_requirement_id'  => $req->id,
                'sat_line'                => $satLine,
                'product_name'            => $req->name ?? '—',
                'quantity'                => $req->quantity,
                'unit_price'              => $req->price_unit,
                'subtotal'                => $req->subtotal,
                'unit_name'               => $req->unit->name ?? 'UND',
                'entregado'               => $attended,
                'type_name'               => $req->type instanceof RequirementType ? $req->type->getLabel() : ($req->type ? \App\Enums\RequirementType::tryFrom($req->type)?->getLabel() : 'Suministro'),
                'comment'                 => $detailsByReqId[$req->id]->comment ?? '',
                'location_origin_id'      => $detailsByReqId[$req->id]->location_origin_id ?? null,
                'location_destination_id' => $detailsByReqId[$req->id]->location_destination_id ?? null,
                'additional_cost'         => $detailsByReqId[$req->id]->additional_cost ?? 0,
                'cost_description'        => $detailsByReqId[$req->id]->cost_description ?? '',
                'tool_unit_id'            => $detailsByReqId[$req->id]->tool_unit_id ?? null,
                'is_tool'                 => $isTool,
                'available_units'         => $availableUnits,
            ];
        }

        return collect($details);
    }
}
