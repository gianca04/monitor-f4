<?php

namespace App\Filament\Resources\RequirementLists\Pages;

use App\Filament\Resources\RequirementLists\RequirementListResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRequirementLists extends ListRecords
{
    protected static string $resource = RequirementListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
