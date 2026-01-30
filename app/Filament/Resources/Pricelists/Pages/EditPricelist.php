<?php

namespace App\Filament\Resources\Pricelists\Pages;

use App\Filament\Resources\Pricelists\PricelistResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPricelist extends EditRecord
{
    protected static string $resource = PricelistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
