<?php

namespace App\Filament\Resources\Quotes\Pages;

use App\Filament\Resources\Quotes\QuoteResource;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Projects\ProjectResource;
use App\Models\Client;
use App\Models\PriceType;
use App\Models\QuoteCategory;
use App\Models\Project;
use App\Models\Quote;
use Illuminate\Support\Collection;

class CreateQuote extends CreateRecord
{
    protected static string $resource = QuoteResource::class;

    // La vista NO es estática en CreateRecord (v3/v4)
    protected string $view = 'filament.resources.quote-resource.pages.manage-quote';

    // EL CAMBIO: El título SÍ debe ser estático para no chocar con BasePage
    protected static ?string $title = 'Nueva Cotización';

    // Propiedades públicas para la vista
    public Collection $quoteCategories;
    public Collection $clients;
    public Collection $priceTypes;

    public ?int $projectId = null;
    public ?int $subClientId = null;
    public ?string $serviceCode = null;
    public ?object $project = null;
    public ?string $suggestedRequestNumber = null;
    public ?int $suggestedProjectId = null;

    public function mount(): void
    {
        parent::mount(); // Inicializa el formulario nativo

        $this->quoteCategories = QuoteCategory::select('id', 'name')->orderBy('name')->get();
        $this->clients = Client::select('id', 'business_name', 'document_number')
            ->orderBy('business_name')
            ->get();
        $this->priceTypes = PriceType::select('id', 'name')->orderBy('id')->get();

        $projectId = request()->query('project_id');
        $this->project = $projectId ? Project::find($projectId) : null;
        $this->subClientId = request()->query('sub_client_id');
        $this->serviceCode = request()->query('service_code');

        if ($projectId) {
            $this->suggestedRequestNumber = Quote::generateNextRequestNumber($projectId);
            $this->suggestedProjectId = (int) $projectId;
        } else {
            $lastProject = Project::orderByDesc('id')->first();
            $nextId = $lastProject ? $lastProject->id + 1 : 1;
            $this->suggestedRequestNumber = "COT-{$nextId}-A";
            $this->suggestedProjectId = $nextId;
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Asegura que el project_id se guarde si viene por URL
        if ($projectId = request()->query('project_id')) {
            $data['project_id'] = $projectId;
        }
        return $data;
    }

    public function getBreadcrumbs(): array
    {
        $breadcrumbs = [];
        $breadcrumbs[QuoteResource::getUrl()] = QuoteResource::getBreadcrumb();

        if ($this->project) {
            $breadcrumbs[ProjectResource::getUrl('edit', ['record' => $this->project])] = $this->project->name;
        }

        $breadcrumbs[] = $this->getBreadcrumb();

        return $breadcrumbs;
    }
}
