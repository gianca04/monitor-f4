<?php

namespace App\Filament\Resources\Employees\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('MainTabs')
                    ->tabs([
                        Tab::make('Información del Empleado')
                            ->icon('heroicon-m-user')

                            ->columns(2)
                            ->schema([

                                Select::make('document_type')
                                    ->label('Tipo de Documento')
                                    ->options([
                                        'DNI' => 'DNI',
                                        'PASAPORTE' => 'Pasaporte',
                                        'CARNET DE EXTRANJERIA' => 'Carnet de Extranjería',
                                    ])
                                    ->required()
                                    ->searchable()
                                    ->placeholder('Seleccionar tipo de documento'),

                                TextInput::make('document_number')
                                    ->label('Número de Documento')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(12)
                                    ->numeric()
                                    ->placeholder('Ingresar número de documento'),

                                TextInput::make('first_name')
                                    ->label('Nombres')
                                    ->required()
                                    ->maxLength(40)
                                    ->placeholder('Ingresar primer nombre')
                                    ->autocomplete('given-name'),

                                TextInput::make('last_name')
                                    ->label('Apellido')
                                    ->required()
                                    ->maxLength(40)
                                    ->placeholder('Ingresar apellido')
                                    ->autocomplete('family-name'),

                                TextInput::make('address')
                                    ->label('Dirección')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ingresar dirección'),

                                DatePicker::make('date_contract')
                                    ->label('Fecha de Contrato')
                                    ->required()
                                    ->maxDate(now())
                                    ->placeholder('Seleccionar fecha de contrato'),

                                DatePicker::make('date_birth')
                                    ->label('Fecha de Nacimiento')
                                    ->required()
                                    ->maxDate(now()->subYears(18))
                                    ->placeholder('Seleccionar fecha de nacimiento'),

                                Select::make('sex')
                                    ->label('Sexo del colaborador')
                                    ->required()
                                    ->native(false)
                                    ->options([
                                        'male' => 'Masculino',
                                        'female' => 'Femenino',
                                        'other' => 'No específicado',
                                    ]),

                                Select::make('position_id')
                                    ->label('Cargo')
                                    ->relationship('position', 'name')
                                    ->required()
                                    ->preload()
                                    ->searchable()
                                    ->placeholder('Seleccionar cargo')

                                    ->createOptionForm([
                                        Section::make('Información del cargo')
                                            ->description('Datos generales del cargo')
                                            ->icon('heroicon-o-identification')
                                            ->schema([
                                                TextInput::make('name')
                                                    ->required(),
                                            ])
                                            ->columns(2),
                                    ]),

                                Toggle::make('active')
                                    ->label('Activo')
                                    ->helperText('Marca esta opción para activar al colaborador y permitirle iniciar sesión.')
                                    ->default(true)
                                    ->live() // Hace que el formulario reaccione al cambio de este toggle
                            ])
                            ->columnSpan('full'),

                        Tab::make('Información del Usuario')
                            ->icon('heroicon-m-lock-closed')
                            ->schema([
                                Section::make('Configuración de Acceso') // Un título ayuda a separar visualmente
                                    ->description('Gestione las credenciales y permisos de acceso al sistema.')
                                    ->relationship('user')
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->label('Habilitar Acceso al Sistema')
                                            ->helperText('Permite al empleado iniciar sesión.')
                                            ->live()
                                            ->default(false)
                                            ->columnSpanFull(), // El toggle queda mejor solo arriba

                                        TextInput::make('name')
                                            ->label('Nombre de Usuario')
                                            ->required(fn(Get $get): bool => $get('is_active'))
                                            ->unique(ignoreRecord: true)
                                            ->visible(fn(Get $get): bool => $get('is_active')),

                                        TextInput::make('email')
                                            ->label('Correo Electrónico')
                                            ->email()
                                            ->required(fn(Get $get): bool => $get('is_active'))
                                            ->unique(ignoreRecord: true)
                                            ->visible(fn(Get $get): bool => $get('is_active')),

                                        Select::make('roles')
                                            ->relationship('roles', 'name')
                                            ->multiple()
                                            ->preload()
                                            ->required(fn(Get $get): bool => $get('is_active'))
                                            ->visible(fn(Get $get): bool => $get('is_active'))
                                            ->columnSpanFull(), // Los roles múltiples suelen requerir más espacio

                                        TextInput::make('password')
                                            ->label('Contraseña')
                                            ->password()
                                            ->dehydrateStateUsing(fn(string $state): string => Hash::make($state))
                                            ->dehydrated(fn(?string $state): bool => filled($state))
                                            ->required(fn(string $operation, Get $get): bool => $operation === 'create' && $get('is_active'))
                                            ->visible(fn(Get $get): bool => $get('is_active'))
                                            ->confirmed(),

                                        TextInput::make('password_confirmation')
                                            ->password()
                                            ->label('Confirmar Contraseña')
                                            ->required(fn(string $operation, Get $get): bool => $operation === 'create' && $get('is_active'))
                                            ->visible(fn(Get $get): bool => $get('is_active')),
                                    ])
                                    ->columns(2) // Esto organiza los inputs internos en 2 columnas
                                    ->columnSpanFull(), // Esto hace que la SECCIÓN use todo el ancho del TAB
                            ])
                    ])
                    ->columnSpan('full'),
            ]);
    }
}
