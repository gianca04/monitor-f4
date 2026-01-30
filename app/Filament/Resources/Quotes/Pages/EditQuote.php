<?php

namespace App\Filament\Resources\Quotes\Pages;

use App\Filament\Resources\Quotes\QuoteResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Models\Client;
use App\Models\PriceType;
use App\Models\Quote;
use App\Models\QuoteCategory;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Collection;

class EditQuote extends EditRecord
{
    protected static string $resource = QuoteResource::class;

    // EN V4: La vista NO es estática
    protected string $view = 'filament.resources.quote-resource.pages.manage-quote';

    // EN V4: El título SÍ debe ser estático para no chocar con la clase base
    protected static ?string $title = 'Editar Cotización';

    // No declares $record como array, Filament lo maneja internamente como Modelo
    // public $record = null; <-- ELIMINADO PARA EVITAR ERROR DE TIPADO

    // Datos pasados a la vista desde PHP
    public Collection $quoteCategories;
    public Collection $clients;
    public Collection $priceTypes;
    public ?string $projectUrl = null;

    public function mount(int | string $record): void
    {
        // 1. Inicializa el formulario nativo de Filament
        parent::mount($record);

        // 2. Cargar datos adicionales para tu interfaz rápida
        // Filament ya cargó el modelo en $this->record gracias al parent::mount()
        // Pero necesitamos cargar las relaciones específicas que usas en tu vista
        $this->record->load([
            'subClient',
            'quoteDetails' => fn($query) => $query->orderBy('line', 'asc'),
            'quoteDetails.pricelist.unit',
            'quoteDetails.pricelist.priceType',
            'project'
        ]);

        if ($this->record->project_id) {
            $this->projectUrl = ProjectResource::getUrl('edit', ['record' => $this->record->project_id]);
        }

        // Cargar colecciones para los selectores
        $this->quoteCategories = QuoteCategory::select('id', 'name')->orderBy('name')->get();
        $this->clients = Client::select('id', 'business_name', 'document_number')
            ->orderBy('business_name')
            ->get();
        $this->priceTypes = PriceType::select('id', 'name')->orderBy('id')->get();
    }

    public function getTitle(): string
    {
        // Usamos $this->record que ya es la instancia del modelo Quote
        return "Editar Cotización #{$this->record->id}";
    }

    public function getBreadcrumbs(): array
    {
        $breadcrumbs = [];
        $breadcrumbs[QuoteResource::getUrl()] = QuoteResource::getBreadcrumb();

        if ($this->record->project) {
            $breadcrumbs[ProjectResource::getUrl('edit', ['record' => $this->record->project])] = $this->record->project->name;
        }

        $breadcrumbs[] = $this->getBreadcrumb();

        return $breadcrumbs;
    }
}
