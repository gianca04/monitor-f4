<?php

namespace App\Filament\Resources\DispatchGuides\Pages;

use App\Filament\Resources\DispatchGuides\DispatchGuideResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDispatchGuides extends ListRecords
{
    protected static string $resource = DispatchGuideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
