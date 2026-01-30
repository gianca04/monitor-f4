<?php

namespace App\Filament\Resources\Quotes\Pages;

use App\Filament\Resources\Quotes\QuoteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQuotes extends ListRecords
{
    protected static string $resource = QuoteResource::class;
    // Si borras esto, Filament usará la vista por defecto de la tabla
    // En ListQuotes.php
    protected string $view = 'filament.resources.quote-resource.pages.list-quotes';
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
