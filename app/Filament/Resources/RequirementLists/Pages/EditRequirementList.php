<?php

namespace App\Filament\Resources\RequirementLists\Pages;

use App\Filament\Resources\RequirementLists\RequirementListResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRequirementList extends EditRecord
{
    protected static string $resource = RequirementListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
