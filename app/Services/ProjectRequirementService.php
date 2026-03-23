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
    public function calculateDetails(ProjectRequirement $req): ProjectRequirementDto
    {
        $dto = new ProjectRequirementDto();
        $dto->productName = $this->getProductName($req);
        $dto->unitName = $this->getUnitName($req);
        $dto->consumableTypeName = $req->type?->getLabel() ?? 'N/A';
        $dto->subtotal = round($req->quantity * $req->price_unit, 2);
        return $dto;
    }

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
}
