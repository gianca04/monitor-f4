<?php

namespace App\Filament\Resources\QuoteWarehouses\Pages;

use App\Filament\Resources\QuoteWarehouses\QuoteWarehouseResource;
use App\Models\Location;
use App\Models\QuoteWarehouseDetail;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Collection;

class EditQuoteWarehouse extends EditRecord
{
    protected static string $resource = QuoteWarehouseResource::class;

    protected string $view = 'filament.resources.quote-warehouse-resource.pages.edit-warehouse';

    protected static ?string $title = 'Atender Despacho';

    // Datos públicos para la vista blade
    public Collection $detailsCollection;
    public Collection $locationsCollection;
    public string $clientName = '';

    public function mount(int|string $record): void
    {
        // 1. Inicializa el record de Filament (resolve {record} de la ruta)
        parent::mount($record);

        // 2. Cargar relaciones necesarias
        $this->record->load('employee.employee');
        $this->record->quote->load([
            'subClient',
            'project.projectRequirements.requirementable',
        ]);

        // 3. Preparar datos
        $this->clientName = $this->record->quote->subClient->name ?? '';
        $this->locationsCollection = Location::where('is_active', true)->get();
        $this->detailsCollection = $this->buildDetails();
    }

    public function getTitle(): string
    {
        $quote = $this->record->quote;
        return 'Atender: ' . ($quote->request_number ?? 'COT-' . str_pad($quote->id, 5, '0', STR_PAD_LEFT));
    }

    protected function getViewData(): array
    {
        return [
            'quoteWarehouse' => $this->record,
            'quote'          => $this->record->quote,
            'client'         => $this->clientName,
            'details'        => $this->detailsCollection->toArray(),
            'locations'      => $this->locationsCollection,
        ];
    }

    private function buildDetails(): Collection
    {
        $quoteWarehouse = $this->record;
        $quote = $quoteWarehouse->quote;

        $warehouseDetailsCollection = QuoteWarehouseDetail::where('quote_warehouse_id', $quoteWarehouse->id)->get();
        $detailsByReqId = $warehouseDetailsCollection->whereNotNull('project_requirement_id')->keyBy('project_requirement_id');

        $projectRequirements = $quote->project->projectRequirements ?? collect();

        $details = [];

        foreach ($projectRequirements as $req) {
            $attended = 0;

            if (isset($detailsByReqId[$req->id])) {
                $attended = $detailsByReqId[$req->id]->attended_quantity;
            }

            $satLine = '-';
            if ($req->requirementable instanceof \App\Models\QuoteDetail) {
                $satLine = $req->requirementable->pricelist->sat_line ?? '-';
            } elseif ($req->requirementable instanceof \App\Models\ToolUnit) {
                $satLine = 'HERRAMIENTAS';
            }

            $details[] = [
                'project_requirement_id' => $req->id,
                'sat_line'         => $satLine,
                'product_name'     => $req->product_name,
                'quantity'         => $req->quantity,
                'unit_price'       => $req->price_unit,
                'subtotal'         => $req->subtotal,
                'unit_name'        => $req->unit_name,
                'entregado'        => $attended,
                'type_name'        => $req->consumable_type_name,
                'comment'          => $detailsByReqId[$req->id]->comment ?? '',
                'location_id'      => $detailsByReqId[$req->id]->location_id ?? null,
            ];
        }

        return collect($details);
    }
}
