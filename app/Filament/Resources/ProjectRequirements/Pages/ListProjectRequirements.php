<?php

namespace App\Filament\Resources\ProjectRequirements\Pages;

use App\Filament\Resources\ProjectRequirements\ProjectRequirementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProjectRequirements extends ListRecords
{
    protected static string $resource = ProjectRequirementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
