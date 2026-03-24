<?php

namespace App\Filament\Resources\QuoteWarehouses\Pages;

use App\Filament\Resources\QuoteWarehouses\QuoteWarehouseResource;
use App\Models\Location;
use App\Models\QuoteWarehouseDetail;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;
use Illuminate\Support\Collection;
use App\Services\ProjectRequirementService;

/**
 * Clase EditQuoteWarehouse
 * 
 * Controlador de la vista (Filament Page) encargada de la atención de despachos del Almacén.
 * Aplica principios arquitectónicos delegando procesamiento pesados a la capa de Servicios.
 */
class EditQuoteWarehouse extends EditRecord
{
    protected static string $resource = QuoteWarehouseResource::class;

    protected string $view = 'filament.resources.quote-warehouse-resource.pages.edit-warehouse';

    protected static ?string $title = 'Atender Despacho';

    // Datos públicos para la vista blade
    public Collection $detailsCollection;
    public Collection $locationsCollection;
    public string $clientName = '';

    /**
     * Inicializa el componente, resolviendo el modelo subyacente y preparando el estado inicial.
     * 
     * @param int|string $record ID del registro (QuoteWarehouse)
     * @return void
     */
    public function mount(int|string $record): void
    {
        // 1. Inicializa el record de Filament (resuelve el {record} de la ruta)
        parent::mount($record);

        // 2. Cargar relaciones necesarias de manera adelantada (Eager Loading) 
        // para prevenir el problema de N+1 consultas (N+1 queries problem).
        $this->record->load('employee.employee');
        $this->record->quote->load([
            'subClient',
            'project.projectRequirements.requirementable',
        ]);

        // 3. Preparar datos base requeridos por la vista compilada
        $this->clientName = $this->record->quote->subClient->name ?? '';
        $this->locationsCollection = Location::where('is_active', true)->get();
        $this->detailsCollection = $this->buildDetails();
    }

    /**
     * Define el título dinámico de la página basado en el Proyecto de la Cotización.
     * 
     * @return string
     */
    public function getTitle(): string
    {
        $quote = $this->record->quote;
        return 'Lista de requerimientos: ' . ($quote->project->name ?? 'Sin Proyecto');
    }

    /**
     * Proporciona un arreglo de datos (ViewModel) hacia la vista Blade principal.
     *
     * @return array
     */
    protected function getViewData(): array
    {
        $dispatchGuides = \App\Models\DispatchGuide::with(['originLocation', 'destinationLocation', 'dispatchTransactions'])
            ->where('quote_warehouse_id', $this->record->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $transactions = \App\Models\DispatchTransaction::with([
            'projectRequirement.project.subClient.client',
            'projectRequirement.unit',
            'dispatchGuide'
        ])
            ->where('quote_warehouse_id', $this->record->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $users = \App\Models\User::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return [
            'quoteWarehouse' => $this->record,
            'quote'          => $this->record->quote,
            'client'         => $this->clientName,
            'details'        => $this->detailsCollection->toArray(),
            'locations'      => $this->locationsCollection,
            'dispatchGuides' => $dispatchGuides,
            'transactions'   => $transactions,
            'users'          => $users,
        ];
    }

    /**
     * Sobrescribe el ancho máximo del contenido permitiendo usar todo el ancho de la pantalla.
     * Útil en vistas con tablas complejas o muchas columnas.
     * 
     * @return Width|string|null
     */
    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }

    /**
     * Construye y estructura la colección de requerimientos.
     * Delega la lógica de negocio al servicio correspondiente siguiendo reglas de arquitectura limpia.
     * 
     * @return \Illuminate\Support\Collection
     */
    private function buildDetails(): Collection
    {
        $quoteWarehouse = $this->record;
        $quote = $quoteWarehouse->quote;

        $warehouseDetailsCollection = QuoteWarehouseDetail::where('quote_warehouse_id', $quoteWarehouse->id)->get();
        $projectRequirements = $quote->project->projectRequirements ?? collect();

        // Resolución de la dependencia (Contenedor de Inversión de Control de Laravel)
        /** @var ProjectRequirementService $service */
        $service = app(ProjectRequirementService::class);

        return $service->getFormattedWarehouseDetails($projectRequirements, $warehouseDetailsCollection);
    }
}
