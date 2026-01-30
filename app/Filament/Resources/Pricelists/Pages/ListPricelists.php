<?php

namespace App\Filament\Resources\Pricelists\Pages;

use App\Filament\Resources\Pricelists\PricelistResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPricelists extends ListRecords
{
    protected static string $resource = PricelistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
