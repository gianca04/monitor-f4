<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('document_type')
                    ->required(),
                TextInput::make('document_number')
                    ->required(),
                TextInput::make('person_type')
                    ->required(),
                TextInput::make('business_name')
                    ->required(),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('address')
                    ->default(null),
                TextInput::make('contact_phone')
                    ->tel()
                    ->default(null),
                TextInput::make('contact_email')
                    ->email()
                    ->default(null),
                TextInput::make('logo')
                    ->default(null),
            ]);
    }
}
