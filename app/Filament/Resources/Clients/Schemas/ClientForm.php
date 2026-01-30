<?php

namespace App\Filament\Resources\Clients\Schemas;

use App\Forms\Components\ClientMainInfo;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ClientMainInfo::make(),
                Repeater::make('subClients')
                    ->itemLabel(fn(array $state): ?string => $state['name'] ?? null)
                    ->label('Subclientes')
                    ->collapsed()
                    ->relationship('subClients')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre del subcliente')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-user'),

                        TextInput::make('ceco')
                            ->label('CECO')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('address')
                            ->label('Dirección')
                            ->columnSpanFull()
                            ->placeholder('Dirección del subcliente')
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-map-pin'),

                        Textarea::make('description')
                            ->label('Descripción')
                            ->maxLength(500)
                            ->autosize()
                            ->columnSpanFull(),

                        Repeater::make('contactData')
                            ->itemLabel(fn(array $state): ?string => $state['contact_name'] ?? null)
                            ->collapsed()
                            ->label('Datos de contacto')
                            ->relationship('contactData')
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('email')
                                    ->label('Correo electrónico')
                                    ->email()
                                    ->maxLength(255)
                                    ->placeholder('correo@ejemplo.com'),

                                TextInput::make('phone_number')
                                    ->label('Teléfono')
                                    ->tel()
                                    ->maxLength(15)
                                    ->placeholder('Ej: +51 999 999 999'),

                                TextInput::make('contact_name')
                                    ->label('Nombre de contacto')
                                    ->maxLength(255)
                                    ->placeholder('Nombre del contacto'),
                            ]),
                    ])
                    ->createItemButtonLabel('Agregar subcliente')
                    ->columns(2)
                    ->collapsible()
                    ->grid(2)
                    ->columnSpanFull()
                    ->addActionLabel('Nuevo'),

            ]);
    }
}
