<?php

namespace App\Filament\Resources\DispatchGuides\Pages;

use App\Filament\Resources\DispatchGuides\DispatchGuideResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDispatchGuide extends EditRecord
{
    protected static string $resource = DispatchGuideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
