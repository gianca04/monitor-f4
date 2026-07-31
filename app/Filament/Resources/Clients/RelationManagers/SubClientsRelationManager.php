<?php

namespace App\Filament\Resources\Clients\RelationManagers;

use App\Models\Department;
use App\Models\District;
use App\Models\Province;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubClientsRelationManager extends RelationManager
{
    protected static string $relationship = 'subClients';
    protected static ?string $title = 'Subclientes / Tiendas';
    protected static ?string $pluralModelLabel = 'Subclientes';
    protected static ?string $modelLabel = 'Subcliente';
    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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

                Grid::make(3)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('arrival_time_hrs')
                            ->label('Llegada (Hrs)')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->suffix('Hrs')
                            ->placeholder('Ej: 24'),

                        TextInput::make('corrective_quote_time_hrs')
                            ->label('Cotización correctivos (Hr)')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->suffix('Hrs')
                            ->placeholder('Ej: 72'),

                        TextInput::make('corrective_execution_time_hrs')
                            ->label('Ejecución correctivos (Hr)')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->suffix('Hrs')
                            ->placeholder('Ej: 120'),
                    ]),

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
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre del subcliente')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('ceco')
                    ->label('CECO')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('district.province.name')
                    ->label('Provincia')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('district.name')
                    ->label('Distrito')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('address')
                    ->label('Dirección')
                    ->limit(35)
                    ->placeholder('-'),

                TextColumn::make('arrival_time_hrs')
                    ->label('Llegada')
                    ->suffix(' Hrs')
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('corrective_quote_time_hrs')
                    ->label('Cotización')
                    ->suffix(' Hrs')
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('corrective_execution_time_hrs')
                    ->label('Ejecución')
                    ->suffix(' Hrs')
                    ->sortable()
                    ->placeholder('-'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Agregar subcliente'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
