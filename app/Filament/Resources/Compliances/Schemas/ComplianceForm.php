<?php

namespace App\Filament\Resources\Compliances\Schemas;

use App\Models\Project;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;

class ComplianceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ═══════════════════════════════════════════════════════════════
                // SECCIÓN A: INFORMACIÓN GENERAL
                // ═══════════════════════════════════════════════════════════════
                Section::make('Información General')
                    ->description('Seleccione el proyecto para cargar los datos automáticamente')
                    ->icon('heroicon-o-building-office-2')
                    ->collapsible()
                    ->collapsed()
                    ->schema([

                        Select::make('project_id')
                            ->label('Proyecto')
                            ->required()
                            ->prefixIcon('heroicon-m-briefcase')
                            ->helperText('Solo se listan proyectos asignados y en estado "Aprobado".')
                            ->searchable()
                            ->preload()
                            ->live()
                            // 1. LÓGICA DE BÚSQUEDA PERSONALIZADA
                            ->getSearchResultsUsing(function (string $search) {
                                $isNumeric = is_numeric($search);

                                return Project::query()
                                    ->where('status', 'Aprobado')
                                    ->whereDoesntHave('compliance')
                                    ->where(function ($query) use ($search, $isNumeric) {
                                        $query->where('name', 'like', "%{$search}%")
                                            ->orWhere('service_code', 'like', "%{$search}%");

                                        if ($isNumeric) {
                                            $query->orWhere('service_code', 'like', "%COT-{$search}%");
                                        }
                                    })
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(fn($project) => [
                                        $project->id => "{$project->service_code} - {$project->name}"
                                    ]);
                            })
                            // 2. LÓGICA PARA MOSTRAR LA OPCIÓN SELECCIONADA (incluye proyecto actual al editar)
                            ->getOptionLabelUsing(function ($value): ?string {
                                $project = Project::find($value);
                                return $project
                                    ? "{$project->service_code} - {$project->name}"
                                    : null;
                            })
                            // 3. EVENTOS - Hidratar datos del proyecto
                            ->afterStateHydrated(function ($state, $set) {
                                if ($state) {
                                    $project = Project::find($state);
                                    if ($project) {
                                        $set('project_name', $project->name);
                                    }
                                }
                            })
                            ->columnSpanFull(),
                        // ACA EL ESTADO DEL PROYECTO
                        Select::make('state')
                            ->label('Estado')
                            ->default('En Ejecución')
                            ->options([
                                'En Ejecución' => 'En Ejecución',
                                'Completado'   => 'Completado',
                            ])
                            ->rules(['in:En Ejecución,Completado'])
                            ->native(false)
                            ->columnSpanFull(),
                    ]),
                // ═══════════════════════════════════════════════════════════════
                // SECCIÓN B2: OBSERVACIONES GENERALES
                // ═══════════════════════════════════════════════════════════════
                Section::make('Sección B2: Observaciones de Mantenimiento')
                    ->description('Registre las observaciones generales del mantenimiento realizado')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        RichEditor::make('maintenance_observations')
                            ->label('')
                            ->placeholder('Escriba aquí las observaciones generales del mantenimiento...')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'bulletList',
                                'orderedList',
                                'h2',
                                'h3',
                                'blockquote',
                                'redo',
                                'undo',
                            ])
                            ->columnSpanFull(),
                    ]),

                // ═══════════════════════════════════════════════════════════════
                // SECCIÓN B1: ACTIVOS INTERVENIDOS
                // ═══════════════════════════════════════════════════════════════
                Section::make('Sección B1: Disposición de Activos Intervenidos')
                    ->description('Seleccione y detalle todos los activos intervenidos durante la actividad ejecutada')
                    ->icon('heroicon-o-cube')
                    ->columnSpanFull()
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        // Grid de activos - 2 columnas
                        Grid::make(2)
                            ->schema([
                                // Columna 1: Tableros
                                Section::make('Tableros Eléctricos')
                                    ->icon('heroicon-o-square-3-stack-3d')
                                    ->compact()
                                    ->columnSpanFull()
                                    ->schema([
                                        // 1. Tablero Autosoportado
                                        Grid::make(3)
                                            ->schema([
                                                Toggle::make('assets.tablero_autosoportado.selected')
                                                    ->label('Tablero Autosoportado')
                                                    ->live()
                                                    ->onColor('success')
                                                    ->offColor('gray')
                                                    ->inline(false),
                                                TextInput::make('assets.tablero_autosoportado.quantity')
                                                    ->label('Cantidad')
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->suffix('unid.')
                                                    ->default(0)
                                                    ->visible(fn(Get $get) => $get('assets.tablero_autosoportado.selected')),
                                                TextInput::make('assets.tablero_autosoportado.comments')
                                                    ->label('Comentarios')
                                                    ->placeholder('Observaciones...')
                                                    ->visible(fn(Get $get) => $get('assets.tablero_autosoportado.selected')),
                                            ]),

                                        // 2. Tablero Adosados
                                        Grid::make(3)
                                            ->schema([
                                                Toggle::make('assets.tablero_adosados.selected')
                                                    ->label('Tablero Adosados')
                                                    ->live()
                                                    ->onColor('success')
                                                    ->offColor('gray')
                                                    ->inline(false),
                                                TextInput::make('assets.tablero_adosados.quantity')
                                                    ->label('Cantidad')
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->suffix('unid.')
                                                    ->default(0)
                                                    ->visible(fn(Get $get) => $get('assets.tablero_adosados.selected')),
                                                TextInput::make('assets.tablero_adosados.comments')
                                                    ->label('Comentarios')
                                                    ->placeholder('Observaciones...')
                                                    ->visible(fn(Get $get) => $get('assets.tablero_adosados.selected')),
                                            ]),
                                    ]),

                                // Columna 2: Otros equipos
                                Section::make('Equipos y Pozos a Tierra')
                                    ->icon('heroicon-o-bolt')
                                    ->compact()
                                    ->columnSpanFull()
                                    ->schema([
                                        // 3. Banco de Condensadores
                                        Grid::make(3)
                                            ->schema([
                                                Toggle::make('assets.banco_condensadores.selected')
                                                    ->label('Banco de Condensadores')
                                                    ->live()
                                                    ->onColor('success')
                                                    ->offColor('gray')
                                                    ->inline(false),
                                                TextInput::make('assets.banco_condensadores.quantity')
                                                    ->label('Cantidad')
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->suffix('unid.')
                                                    ->default(0)
                                                    ->visible(fn(Get $get) => $get('assets.banco_condensadores.selected')),
                                                TextInput::make('assets.banco_condensadores.comments')
                                                    ->label('Comentarios')
                                                    ->placeholder('Observaciones...')
                                                    ->visible(fn(Get $get) => $get('assets.banco_condensadores.selected')),
                                            ]),

                                        // 4. Pozos a Tierra Baja Tensión
                                        Grid::make(3)
                                            ->schema([
                                                Toggle::make('assets.pozos_baja_tension.selected')
                                                    ->label('Pozos Tierra (Baja Tensión)')
                                                    ->live()
                                                    ->onColor('warning')
                                                    ->offColor('gray')
                                                    ->inline(false),
                                                TextInput::make('assets.pozos_baja_tension.quantity')
                                                    ->label('Cantidad')
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->suffix('unid.')
                                                    ->default(0)
                                                    ->visible(fn(Get $get) => $get('assets.pozos_baja_tension.selected')),
                                                TextInput::make('assets.pozos_baja_tension.comments')
                                                    ->label('Comentarios')
                                                    ->placeholder('Observaciones...')
                                                    ->visible(fn(Get $get) => $get('assets.pozos_baja_tension.selected')),
                                            ]),

                                        // 5. Pozos a Tierra Media Tensión
                                        Grid::make(3)
                                            ->schema([
                                                Toggle::make('assets.pozos_media_tension.selected')
                                                    ->label('Pozos Tierra (Media Tensión)')
                                                    ->live()
                                                    ->onColor('danger')
                                                    ->offColor('gray')
                                                    ->inline(false),
                                                TextInput::make('assets.pozos_media_tension.quantity')
                                                    ->label('Cantidad')
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->suffix('unid.')
                                                    ->default(0)
                                                    ->visible(fn(Get $get) => $get('assets.pozos_media_tension.selected')),
                                                TextInput::make('assets.pozos_media_tension.comments')
                                                    ->label('Comentarios')
                                                    ->placeholder('Observaciones...')
                                                    ->visible(fn(Get $get) => $get('assets.pozos_media_tension.selected')),
                                            ]),
                                    ]),
                            ]),
                    ]),

                Section::make('Datos del Cliente')
                    ->icon('heroicon-o-user')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextInput::make('fullname_cliente')
                            ->label('Nombre Completo')
                            ->prefixIcon('heroicon-o-user-circle')
                            ->placeholder('Ingrese nombre completo')
                            ->maxLength(255),

                        Grid::make(2)
                            ->schema([
                                Select::make('document_type')
                                    ->label('Tipo de Documento')
                                    ->options([
                                        'DNI' => 'DNI',
                                        'CARNET DE EXTRANJERIA' => 'Carnet de Extranjería',
                                        'PASAPORTE' => 'Pasaporte',
                                    ])
                                    ->default('DNI')
                                    ->nullable()
                                    ->native(false)
                                    ->live(),

                                TextInput::make('document_number')
                                    ->label('N° de documento')
                                    ->numeric()
                                    ->nullable()
                                    ->minLength(fn(Get $get) => $get('document_type') === 'DNI' ? 8 : 9)
                                    ->maxLength(fn(Get $get) => $get('document_type') === 'DNI' ? 8 : 12)
                                    ->hint(fn(Get $get) => match ($get('document_type')) {
                                        'DNI' => '8 dígitos',
                                        'CARNET DE EXTRANJERIA' => '9-12 dígitos',
                                        'PASAPORTE' => '9-12 dígitos',
                                        default => ''
                                    })
                                    ->hintColor('primary'),
                            ]),

                        SignaturePad::make('client_signature')
                            ->label('Firma del Cliente')
                            ->columnSpanFull()
                            ->backgroundColor('white')
                            ->penColor('black')
                            ->lineMinWidth(1.5)
                            ->lineMaxWidth(4.0)
                            ->dotSize(2.5)
                            ->dehydrated(true)
                            ->default(null),
                    ]),

                Section::make('Datos del Empleado')
                    ->icon('heroicon-o-identification')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Placeholder::make('employee_info')
                            ->label('Responsable del contratista')
                            ->content(function () {
                                $employee = Auth::user()?->employee;

                                if (!$employee) {
                                    return view('filament.components.employee-not-found');
                                }

                                return view('filament.components.employee-info', ['employee' => $employee]);
                            }),

                        SignaturePad::make('employee_signature')
                            ->label('Firma del Supervisor / Técnico')
                            ->columnSpanFull()
                            ->backgroundColor('white')
                            ->penColor('black')
                            ->lineMinWidth(1.5)
                            ->lineMaxWidth(4.0)
                            ->dotSize(2.5)
                            ->dehydrated(true)
                            ->default(null),
                    ]),

            ]);
    }
}
