<?php

namespace App\Filament\Resources\Quotes\Pages;

use App\Filament\Resources\Quotes\QuoteResource;
use App\Models\Quote;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Builder;

class ListQuotes extends ListRecords
{
    protected static string $resource = QuoteResource::class;

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nueva Cotización')
                ->icon('heroicon-m-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'todos' => Tab::make('Todos'),
            'pendiente' => Tab::make('Pendientes')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Pendiente'))
                ->badge(Quote::where('status', 'Pendiente')->count())
                ->badgeColor('warning'),
            'enviado' => Tab::make('Enviadas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Enviado'))
                ->badge(Quote::where('status', 'Enviado')->count())
                ->badgeColor('info'),
            'aprobado' => Tab::make('Aprobadas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Aprobado'))
                ->badge(Quote::where('status', 'Aprobado')->count())
                ->badgeColor('success'),
            'anulado' => Tab::make('Anuladas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Anulado'))
                ->badge(Quote::where('status', 'Anulado')->count())
                ->badgeColor('danger'),
        ];
    }
}
