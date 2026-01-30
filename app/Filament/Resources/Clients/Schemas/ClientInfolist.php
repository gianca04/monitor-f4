<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ClientInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('document_type'),
                TextEntry::make('document_number'),
                TextEntry::make('person_type'),
                TextEntry::make('business_name'),
                TextEntry::make('address'),
                TextEntry::make('contact_phone'),
                TextEntry::make('contact_email'),
                TextEntry::make('logo'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
