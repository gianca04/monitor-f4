<?php

namespace App\Filament\Resources\Tools\Schemas;

use App\Models\ToolBrand;
use App\Models\ToolCategory;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
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
                // Sección: Información General
                Section::make('Información General')
                    ->description('Datos básicos de la herramienta')
                    ->icon('heroicon-o-wrench-screwdriver')

                    ->schema([
                        Grid::make(1)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nombre de la Herramienta')
                                    ->placeholder('Ej: Multímetro Digital')

                                    ->columnSpan(2)
                                    ->maxLength(255),

                                TextInput::make('code')
                                    ->label('Código Interno')
                                    ->placeholder('Ej: HRR-001')
                                    ->prefixIcon('heroicon-m-qr-code')
                                    ->maxLength(50)
                                    ->columnSpan(1),
                            ]),

                        Grid::make(1)
                            ->schema([
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
                                        Textarea::make('description')
                                            ->label('Descripción')
                                            ->rows(2),
                                    ])
                                    ->createOptionUsing(function (array $data): int {
                                        return ToolCategory::create($data)->id;
                                    }),

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
                                        Textarea::make('description')
                                            ->label('Descripción')
                                            ->rows(2),
                                    ])
                                    ->createOptionUsing(function (array $data): int {
                                        return ToolBrand::create($data)->id;
                                    }),
                            ]),
                    ]),

                // Sección: Especificaciones Técnicas
                Section::make('Especificaciones Técnicas')
                    ->description('Modelo, serie y descripción del equipo')
                    ->icon('heroicon-o-cog-6-tooth')


                    ->schema([
                        Grid::make(1)
                            ->schema([
                                TextInput::make('model')
                                    ->label('Modelo')
                                    ->placeholder('Ej: DT-830B')
                                    ->maxLength(100),

                                TextInput::make('serial_number')
                                    ->label('Número de Serie')
                                    ->placeholder('Ej: SN-2024-001234')
                                    ->maxLength(100),
                            ]),

                        Textarea::make('description')
                            ->label('Descripción')
                            ->placeholder('Descripción detallada de la herramienta, características especiales, accesorios incluidos, etc.')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                // Sección: Certificación y Calibración
                Section::make('Certificación y Calibración')
                    ->description('Documentos de certificación y fecha de vencimiento')
                    ->icon('heroicon-o-document-check')


                    ->schema([
                        Grid::make(1)
                            ->schema([
                                FileUpload::make('certification_document')
                                    ->label('Documento de Certificación')
                                    ->helperText('Sube el certificado de calibración o certificación del equipo (PDF, JPG, PNG)')
                                    ->disk('public')
                                    ->directory('tool-certifications')
                                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                    ->maxSize(10240) // 10MB
                                    ->downloadable()
                                    ->openable()
                                    ->previewable()
                                    ->columnSpan(1),

                                DatePicker::make('certification_expiry')
                                    ->label('Fecha de Vencimiento')
                                    ->helperText('Fecha en que expira la certificación')
                                    ->prefixIcon('heroicon-m-calendar-days')
                                    ->displayFormat('d/m/Y')
                                    ->native(false)
                                    ->columnSpan(1),
                            ]),
                    ]),

                // Sección: Estado
                Section::make('Estado de la Herramienta')
                    ->description('Estado actual y disponibilidad')
                    ->icon('heroicon-o-signal')

                    ->schema([
                        ToggleButtons::make('status')
                            ->label('Estado')
                            ->options([
                                'Disponible' => 'Disponible',
                                'En Uso' => 'En Uso',
                                'En Mantenimiento' => 'En Mantenimiento',
                                'Dañado' => 'Dañado',
                                'Baja' => 'Dado de Baja',
                            ])
                            ->icons([
                                'Disponible' => 'heroicon-m-check-circle',
                                'En Uso' => 'heroicon-m-wrench-screwdriver',
                                'En Mantenimiento' => 'heroicon-m-cog-6-tooth',
                                'Dañado' => 'heroicon-m-x-circle',
                                'Baja' => 'heroicon-m-archive-box-x-mark',
                            ])
                            ->colors([
                                'Disponible' => 'success',
                                'En Uso' => 'info',
                                'En Mantenimiento' => 'warning',
                                'Dañado' => 'danger',
                                'Baja' => 'gray',
                            ])
                            ->default('Disponible')
                            ->inline()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
