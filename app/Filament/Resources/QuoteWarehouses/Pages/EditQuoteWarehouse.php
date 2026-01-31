<?php

namespace App\Filament\Resources\QuoteWarehouses\Pages;

use App\Filament\Resources\QuoteWarehouses\QuoteWarehouseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditQuoteWarehouse extends EditRecord
{
    protected static string $resource = QuoteWarehouseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
