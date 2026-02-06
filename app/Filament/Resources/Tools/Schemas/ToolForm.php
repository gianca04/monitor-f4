<?php

namespace App\Filament\Resources\Tools\Schemas;

use App\Models\ToolBrand;
use App\Models\ToolCategory;
use App\Models\ToolUnit;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ToolForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Sección: Información General (Catálogo)
                Section::make('Información del Catálogo')
                    ->description('Definición de la herramienta (Qué es)')
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nombre de la Herramienta')
                                    ->placeholder('Ej: Multímetro Digital')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(1), // Ocupa todo el ancho si es corto
                                TextInput::make('model')
                                    ->label('Modelo')
                                    ->placeholder('Ej: DT-830B')
                                    ->maxLength(100),
                                Select::make('tool_category_id')
                                    ->label('Categoría')
                                    ->placeholder('Seleccionar categoría')
                                    ->prefixIcon('heroicon-m-tag')
                                    ->options(fn() => ToolCategory::orderBy('name')->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->label('Nombre de la Categoría')
                                            ->required()
                                            ->maxLength(255),
                                        Textarea::make('description')->label('Descripción')->rows(2),
                                    ])
                                    ->createOptionUsing(fn(array $data) => ToolCategory::create($data)->id),

                                Select::make('tool_brand_id')
                                    ->label('Marca')
                                    ->placeholder('Seleccionar marca')
                                    ->prefixIcon('heroicon-m-building-storefront')
                                    ->options(fn() => ToolBrand::orderBy('name')->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->label('Nombre de la Marca')
                                            ->required()
                                            ->maxLength(255),
                                        Textarea::make('description')->label('Descripción')->rows(2),
                                    ])
                                    ->createOptionUsing(fn(array $data) => ToolBrand::create($data)->id),


                            ]),

                        Textarea::make('description')
                            ->label('Descripción General')
                            ->placeholder('Descripción técnica, características, etc.')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Unidades Físicas')
                    ->collapsible()
                    ->columnSpanFull()
                    ->headerActions([
                        Action::make('bulk_add')
                            ->label('Generar Lote')
                            ->icon('heroicon-o-plus-circle')
                            ->form([
                                TextInput::make('quantity')
                                    ->label('Cantidad de Unidades')
                                    ->numeric()
                                    ->default(3)
                                    ->minValue(1)
                                    ->maxValue(50)
                                    ->required(),
                                Select::make('status')
                                    ->label('Estado Inicial')
                                    ->options([
                                        'Disponible' => 'Disponible',
                                        'En Mantenimiento' => 'En Mantenimiento',
                                    ])
                                    ->default('Disponible')
                                    ->required(),
                            ])
                            ->action(function (array $data, callable $get, callable $set) {
                                $quantity = (int) $data['quantity'];
                                $initialStatus = $data['status'];
                                $existingState = $get('units') ?? [];

                                // 1. Determine the starting number
                                // Get max from DB
                                $lastDb = ToolUnit::where('internal_code', 'like', 'HRR-%')
                                    ->selectRaw('MAX(CAST(SUBSTRING_INDEX(internal_code, "-", -1) AS UNSIGNED)) as max_num')
                                    ->first();
                                $maxDb = $lastDb->max_num ?? 0;

                                // Get max from current form state
                                $maxForm = 0;
                                foreach ($existingState as $item) {
                                    if (isset($item['internal_code']) && preg_match('/HRR-(\d+)/', $item['internal_code'], $matches)) {
                                        $maxForm = max($maxForm, (int)$matches[1]);
                                    }
                                }

                                $startNumber = max($maxDb, $maxForm) + 1;

                                // 2. Generate new items
                                $newItems = [];
                                for ($i = 0; $i < $quantity; $i++) {
                                    $currentNum = $startNumber + $i;
                                    $code = 'HRR-' . str_pad($currentNum, 3, '0', STR_PAD_LEFT);

                                    // UUID for repeater key
                                    $uuid = (string) \Illuminate\Support\Str::uuid();

                                    $newItems[$uuid] = [
                                        'internal_code' => $code,
                                        'status' => $initialStatus,
                                        'certification_expiry' => null,
                                        'serial_number' => null,
                                    ];
                                }

                                // 3. Push to state
                                $set('units', array_merge($existingState, $newItems));
                            })
                    ])
                    ->schema([
                        Repeater::make('units')
                            ->relationship()
                            ->label('Unidades')
                            ->hiddenLabel()
                            ->itemLabel(fn(array $state): ?string => $state['internal_code'] ?? null)
                            ->extraItemActions([
                                // Actions specific to each item if needed
                            ])
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('internal_code')
                                            ->label('Código Interno')
                                            ->placeholder('Ej: HRR-001')
                                            ->required()
                                            ->default(fn() => ToolUnit::generateNextInternalCode())
                                            ->maxLength(50),

                                        TextInput::make('serial_number')
                                            ->label('Número de Serie')
                                            ->placeholder('Ej: SN-2024-123')
                                            ->maxLength(100),

                                        Select::make('status')
                                            ->label('Estado')
                                            ->options([
                                                'Disponible' => 'Disponible',
                                                'En Uso' => 'En Uso',
                                                'En Mantenimiento' => 'En Mantenimiento',
                                                'Dañado' => 'Dañado',
                                                'Baja' => 'Dado de Baja',
                                            ])
                                            ->default('Disponible')
                                            ->required(),

                                        DatePicker::make('certification_expiry')
                                            ->label('Vencimiento Certificación')
                                            ->displayFormat('d/m/Y')
                                            ->native(false),

                                        FileUpload::make('certification_document')
                                            ->label('Certificado')
                                            ->disk('public')
                                            ->directory('tool-certifications')
                                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                            ->maxSize(10240)
                                            ->downloadable()
                                            ->openable()
                                            ->columnSpanFull(),
                                    ])
                            ])
                            ->defaultItems(1)
                            ->addActionLabel('Agregar Nueva Unidad')
                            ->collapsible()
                            ->cloneable()
                            ->columns(1)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
