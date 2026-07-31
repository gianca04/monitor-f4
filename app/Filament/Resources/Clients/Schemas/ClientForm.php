<?php

namespace App\Filament\Resources\Clients\Schemas;

use App\Forms\Components\ClientMainInfo;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ClientMainInfo::make(),
            ]);
    }
}
