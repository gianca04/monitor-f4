<?php

namespace App\Filament\Resources\QuoteWarehouses\Pages;

use App\Filament\Resources\QuoteWarehouses\QuoteWarehouseResource;
use App\Models\QuoteWarehouse;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQuoteWarehouses extends ListRecords
{
    protected static string $resource = QuoteWarehouseResource::class;
    protected string $view = 'filament.pages.warehouse-kanban';
    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
    public function getViewData(): array
    {
        $statuses = [
            'pending' => 'Pendiente',
            'partial' => 'Parcial',
            'attended' => 'Atendido',
        ];

        // Traemos los registros paginados (12 por página)
        $quoteWarehouses = QuoteWarehouse::with(['quote.subClient', 'details'])
            ->whereHas('quote', function ($q) {
                $q->where('status', 'Aprobado');
            })
            ->latest()
            ->paginate(12);

        // Agregamos el progreso calculado a cada elemento de la colección paginada
        $quoteWarehouses->getCollection()->transform(function ($qw) {
            $qw->progress = $qw->calculateProgress();
            return $qw;
        });

        return [
            'records' => $quoteWarehouses,
        ];
    }

    private function getSpanishStatus(string $statusKey): string
    {
        return match ($statusKey) {
            'pending' => 'Pendiente',
            'partial' => 'Parcial',
            'attended' => 'Atendido',
            default => $statusKey,
        };
    }
}
