<?php

namespace App\Filament\Resources\Clients\Schemas;

use App\Forms\Components\ClientMainInfo;
use App\Models\Department;
use App\Models\District;
use App\Models\Province;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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
                            ->maxLength(255),

                        Grid::make(3)
                            ->columnSpanFull()
                            ->schema([
                                Select::make('department_id')
                                    ->label('Región')
                                    ->placeholder('Seleccione región')
                                    ->options(fn () => Department::orderBy('name')->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->dehydrated(false)
                                    ->afterStateHydrated(function (Get $get, Set $set) {
                                        $districtId = $get('district_id');
                                        if ($districtId) {
                                            $district = District::with('province')->find($districtId);
                                            if ($district?->province) {
                                                $set('department_id', $district->province->department_id);
                                            }
                                        }
                                    })
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('province_id', null);
                                        $set('district_id', null);
                                    }),

                                Select::make('province_id')
                                    ->label('Provincia')
                                    ->placeholder('Seleccione provincia')
                                    ->options(function (Get $get) {
                                        $departmentId = $get('department_id');
                                        if (!$departmentId) {
                                            $districtId = $get('district_id');
                                            if ($districtId) {
                                                $departmentId = District::find($districtId)?->province?->department_id;
                                            }
                                        }
                                        if (!$departmentId) {
                                            return [];
                                        }
                                        return Province::where('department_id', $departmentId)->orderBy('name')->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->dehydrated(false)
                                    ->afterStateHydrated(function (Get $get, Set $set) {
                                        $districtId = $get('district_id');
                                        if ($districtId) {
                                            $district = District::find($districtId);
                                            if ($district) {
                                                $set('province_id', $district->province_id);
                                            }
                                        }
                                    })
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('district_id', null);
                                    }),

                                Select::make('district_id')
                                    ->label('Distrito')
                                    ->placeholder('Seleccione distrito')
                                    ->options(function (Get $get) {
                                        $provinceId = $get('province_id');
                                        if (!$provinceId) {
                                            $districtId = $get('district_id');
                                            if ($districtId) {
                                                $provinceId = District::find($districtId)?->province_id;
                                            }
                                        }
                                        if (!$provinceId) {
                                            return [];
                                        }
                                        return District::where('province_id', $provinceId)->orderBy('name')->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->live(),
                            ]),

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

