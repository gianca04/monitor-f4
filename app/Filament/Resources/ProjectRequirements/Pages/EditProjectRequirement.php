<?php

namespace App\Filament\Resources\ProjectRequirements\Pages;

use App\Filament\Resources\ProjectRequirements\ProjectRequirementResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProjectRequirement extends EditRecord
{
    protected static string $resource = ProjectRequirementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
