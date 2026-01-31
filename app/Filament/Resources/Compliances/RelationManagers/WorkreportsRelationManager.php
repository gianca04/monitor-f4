<?php

namespace App\Filament\Resources\Compliances\RelationManagers;

use App\Models\Employee;
use App\Models\Position;
use App\Models\Project;
use App\Models\QuoteWarehouseDetail;
use App\Models\WorkReport;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class WorkreportsRelationManager extends RelationManager
{
    protected static string $relationship = 'workreports';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('MainTabs')
                    ->tabs([
                        // INICIO DE TAB DE INFORMACIÓN GENERAL
                        Tab::make('Información general')
                            ->icon('heroicon-o-information-circle')
                            ->columns(2)
                            ->schema([

                                Hidden::make('employee_id')
                                    ->default(fn() => Auth::user()?->employee_id)
                                    ->required()
                                    ->label('Supervisor / Técnico'),

                                Select::make('project_id')
                                    ->hidden()
                                    ->dehydrated() // También añadir aquí
                                    ->default(fn() => $this->ownerRecord->project_id)
                                    ->helperText('Proyecto asociado a este reporte.'), // FIN DE SELECT DE EMPLEADO
                                //ACA EL ID DE ACTA : COMPLIANCE_ID
                                Hidden::make('compliance_id')
                                    ->default(function () {
                                        // Intenta ownerRecord, si no, usa la URL
                                        return $this->ownerRecord->id ?? request()->route('record');
                                    })
                                    ->dehydrated(),
                                // INICIO DE SELECT DE PROYECTO
                                Select::make('project_id')
                                    ->hidden()
                                    // 1. Preselecciona el ID del registro padre (Compliance/Proyecto)
                                    ->default(fn() => $this->ownerRecord->project_id)

                                    ->helperText('Proyecto asociado a este reporte.'),

                                // FIN DE SELECT DE PROYECTO

                                // INICIO DE INPUT DE NOMBRE DEL REPORTE
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->label('Nombre del reporte'),
                                // FIN DE INPUT DE NOMBRE DEL REPORTE

                                // INICIO DE INPUT DE FECHA
                                DatePicker::make('report_date')
                                    ->label('Fecha')
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->required()
                                    ->helperText('Selecciona la fecha del trabajo o presiona el botón para establecer hoy')
                                    ->suffixAction(
                                        Action::make('set_today')
                                            ->icon('heroicon-o-calendar')
                                            ->tooltip('Establecer fecha de hoy')
                                            ->color('primary')
                                            ->action(function (callable $set) {
                                                $set('report_date', now()->format('Y-m-d'));
                                            })
                                    ),
                                // FIN DE INPUT DE FECHA

                                // INICIO DE INPUT DE HORA DE INICIO
                                TimePicker::make('start_time')
                                    ->label('Hora de inicio')

                                    ->seconds(false)
                                    ->displayFormat(format: 'H:i')
                                    ->helperText('Selecciona la hora de inicio del trabajo'),
                                // FIN DE INPUT DE HORA DE INICIO

                                // INICIO DE INPUT DE HORA DE FINALIZACIÓN
                                TimePicker::make('end_time')
                                    ->label('Hora de finalización')
                                    ->seconds(false)
                                    ->displayFormat(format: 'H:i')
                                    ->helperText('Selecciona la hora de finalización del trabajo')
                                // Usamos afterStateUpdated para validar y limpiar el campo

                                // FIN DE INPUT DE HORA DE FINALIZACIÓN
                            ]),

                        // FIN DE TAB DE INFORMACIÓN GENERAL

                        // FIN DE TAB DE ORDEN DE TRABAJO

                        // INICIO TAB ACTIVIDADES DEL REPORTE
                        Tabs\Tab::make('Actividades')
                            ->icon('heroicon-o-clipboard-document-list')
                            ->columns(2)
                            ->schema([

                                RichEditor::make('work_to_do')
                                    ->label('Trabajos a realizar')
                                    ->helperText('Proporciona sugerencias o comentarios adicionales sobre el trabajo realizado.')
                                    ->maxLength(5000)
                                    ->columnSpanFull()
                                    ->toolbarButtons([

                                        'bold',
                                        'bulletList',
                                        'h2',
                                        'h3',
                                        'italic',
                                        'orderedList',
                                        'redo',
                                        'strike',
                                        'underline',
                                        'undo',
                                    ]),
                            ]),
                        // FIN TAB ACTIVIDADES DEL REPORTE

                        // INICIO DEL TAB DE HERRAMIENTAS Y MATERIALES
                        Tabs\Tab::make('Herramientas y materiales')
                            ->icon('heroicon-o-wrench')
                            ->columns(2)
                            ->schema([
                                Repeater::make('tools')
                                    ->label('Herramientas')
                                    ->helperText('Agrega las herramientas utilizadas durante el trabajo.')
                                    ->schema([
                                        TextInput::make('herramienta')
                                            ->label('Herramienta')
                                            ->placeholder('Ej: Taladro')
                                            ->required(),
                                        TextInput::make('unidad')
                                            ->label('Unidad')
                                            ->placeholder('Ej: Unidad'),
                                        TextInput::make('cantidad')
                                            ->label('Cantidad')
                                            ->placeholder('Ej: 2'),
                                    ])
                                    ->columns(3)
                                    ->columnSpanFull()
                                    ->defaultItems(0)
                                    ->reorderable(false)
                                    ->addActionLabel('Agregar herramienta')
                                    ->disabled(fn(string $operation): bool => $operation === 'view'),

                                Repeater::make('materials')
                                    ->label('Materiales Utilizados')
                                    ->schema([
                                        Grid::make(4)
                                            ->schema([
                                                // Selector de material usando tu función del modelo
                                                Select::make('material_id')
                                                    ->label('Descripción del Material / Insumo')
                                                    ->options(function () {
                                                        // Usamos la función del modelo pasándole el project_id del dueño (Compliance)
                                                        $project = Project::find($this->getOwnerRecord()->project_id);
                                                        if (!$project) return [];

                                                        // Instanciamos un modelo temporal para usar tu función getAvailableMaterials
                                                        $reportModel = new WorkReport(['project_id' => $project->id]);

                                                        return $reportModel->getAvailableMaterials()
                                                            ->pluck('sat_description', 'id');
                                                    })
                                                    ->searchable()
                                                    ->preload()
                                                    ->live()
                                                    ->required()
                                                    ->columnSpanFull()
                                                    ->afterStateUpdated(function ($state, callable $set) {
                                                        if ($state) {
                                                            // Buscamos los datos técnicos para llenar los campos informativos
                                                            $material = QuoteWarehouseDetail::with(['quoteDetail.pricelist.unit'])->find($state);
                                                            if ($material && $material->quoteDetail->pricelist) {
                                                                $pricelist = $material->quoteDetail->pricelist;
                                                                $set('sat_line', $pricelist->sat_line);
                                                                $set('unit_name', $pricelist->unit->name ?? 'N/A');
                                                                // Calcular el total consumido en el proyecto actual para este material
                                                                $totalConsumed = \App\Models\ProjectConsumption::where('project_id', $this->getOwnerRecord()->project_id)
                                                                    ->where('quote_warehouse_detail_id', $state)
                                                                    ->sum('quantity');
                                                                $remaining = $material->attended_quantity - $totalConsumed;
                                                                if ($remaining <= 0) {
                                                                    Notification::make()->title('El almacén o los materiales entregados se han acabado. Vuelve a pedir a almacén.')->danger()->send();
                                                                    $set('material_id', null);
                                                                    $set('sat_line', null);
                                                                    $set('unit_name', null);
                                                                    $set('attended_quantity', null);
                                                                    $set('used_quantity', null);
                                                                } else {
                                                                    $set('attended_quantity', $remaining);
                                                                    $set('used_quantity', $remaining);
                                                                }
                                                            }
                                                        }
                                                    }),

                                                TextInput::make('sat_line')
                                                    ->label('Línea SAT')
                                                    ->readOnly()
                                                    ->columnSpan(1),

                                                TextInput::make('unit_name')
                                                    ->label('Unidad')
                                                    ->readOnly()
                                                    ->columnSpan(1),

                                                TextInput::make('attended_quantity')
                                                    ->label('En stock')
                                                    ->numeric()
                                                    ->readOnly()
                                                    ->extraAttributes(['class' => 'bg-gray-50 font-bold text-primary-600'])
                                                    ->columnSpan(1),

                                                TextInput::make('used_quantity')
                                                    ->label('Cant. a Reportar')
                                                    ->numeric()
                                                    ->required()
                                                    ->live(onBlur: true)
                                                    ->suffix(fn($get) => $get('unit_name'))
                                                    ->columnSpan(1)
                                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                        $max = (float) $get('attended_quantity');
                                                        if ((float)$state > $max) {
                                                            $set('used_quantity', $max);
                                                            Notification::make()->title('Ajustado al máximo disponible')->warning()->send();
                                                        }
                                                    }),
                                            ])
                                    ])
                                    ->columnSpanFull()
                                    ->defaultItems(0)
                                    ->addActionLabel('Agregar material')
                                    ->reorderable(false)
                                    ->disabled(fn(string $operation): bool => $operation === 'view'),
                            ]),
                        // FIN DEL TAB DE HERRAMIENTAS Y MATERIALES


                        // INICIO DEL TAB DE LISTA DE PERSONAL
                        Tabs\Tab::make('Personal')
                            ->icon('heroicon-o-user-group')
                            ->columns(1)
                            ->schema([
                                Repeater::make('personnel')
                                    ->label('Personal que realizó el trabajo')
                                    ->helperText('Agrega el personal que participó en el trabajo y las horas hombre.')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([


                                                // Select para empleado (visible cuando is_not_registered = false)
                                                Select::make('employee_id')
                                                    ->label('Empleado')
                                                    ->placeholder('Seleccionar empleado...')
                                                    ->options(fn() => Employee::where('active', true)
                                                        ->orderBy('first_name')
                                                        ->get()
                                                        ->mapWithKeys(fn($e) => [$e->id => $e->first_name . ' ' . $e->last_name]))
                                                    ->searchable()
                                                    ->preload()
                                                    ->live()
                                                    ->visible(fn(callable $get) => !$get('is_not_registered'))
                                                    ->afterStateUpdated(function ($state, callable $set) {
                                                        if ($state) {
                                                            $employee = Employee::with('position')->find($state);
                                                            if ($employee) {
                                                                $set('employee_name', $employee->first_name . ' ' . $employee->last_name);
                                                                $set('position_id', $employee->position_id);
                                                                $set('position_name', $employee->position?->name);
                                                            }
                                                        } else {
                                                            $set('employee_name', null);
                                                            $set('position_id', null);
                                                            $set('position_name', null);
                                                        }
                                                    })
                                                    ->createOptionForm([
                                                        Section::make('Nuevo Empleado')
                                                            ->description('Datos básicos del empleado')
                                                            ->schema([
                                                                TextInput::make('first_name')
                                                                    ->label('Nombres')
                                                                    ->required()
                                                                    ->maxLength(255),
                                                                TextInput::make('last_name')
                                                                    ->label('Apellidos')
                                                                    ->required()
                                                                    ->maxLength(255),
                                                                Select::make('document_type')
                                                                    ->label('Tipo de documento')
                                                                    ->options([
                                                                        'DNI' => 'DNI',
                                                                        'PASAPORTE' => 'Pasaporte',
                                                                        'CARNET DE EXTRANJERIA' => 'Carné de Extranjería',
                                                                    ])
                                                                    ->default('DNI'),
                                                                TextInput::make('document_number')
                                                                    ->label('Número de documento')
                                                                    ->required()
                                                                    ->maxLength(20),
                                                                Select::make('position_id')
                                                                    ->label('Cargo')
                                                                    ->options(fn() => Position::orderBy('name')->pluck('name', 'id'))
                                                                    ->searchable()
                                                                    ->preload(),
                                                            ])
                                                            ->columns(2),
                                                    ])
                                                    ->createOptionUsing(function (array $data): int {
                                                        $data['active'] = true;
                                                        $employee = Employee::create($data);
                                                        return $employee->id;
                                                    })
                                                    ->createOptionAction(function (Action $action) {
                                                        return $action
                                                            ->modalHeading('Crear nuevo empleado')
                                                            ->modalButton('Crear empleado')
                                                            ->modalWidth('2xl');
                                                    })
                                                    ->columnSpan(1),

                                                // TextInput para nombre manual (visible cuando is_not_registered = true)
                                                TextInput::make('employee_name')
                                                    ->label('Nombre del personal')
                                                    ->placeholder('Escribir nombre...')
                                                    ->visible(fn(callable $get) => $get('is_not_registered'))
                                                    ->required(fn(callable $get) => $get('is_not_registered'))
                                                    ->maxLength(255)
                                                    ->columnSpan(1),

                                                TextInput::make('hh')
                                                    ->label('H.H')
                                                    ->numeric()
                                                    ->step(0.5)
                                                    ->minValue(0)
                                                    ->placeholder('0')
                                                    ->suffix('hrs')
                                                    ->columnSpan(1),

                                                // TextInput para cargo (visible cuando is_not_registered = false)
                                                TextInput::make('position_name')
                                                    ->label('Cargo')
                                                    ->readonly() // Solo lectura, se llena automáticamente
                                                    ->visible(fn(callable $get) => !$get('is_not_registered'))
                                                    ->columnSpan(1),

                                                // TextInput para cargo manual (visible cuando is_not_registered = true)
                                                TextInput::make('position_name')
                                                    ->label('Nombre del cargo')
                                                    ->placeholder('Escribir cargo...')
                                                    ->visible(fn(callable $get) => $get('is_not_registered'))
                                                    ->maxLength(255)
                                                    ->columnSpan(1),
                                                Toggle::make('is_not_registered')
                                                    ->label('No Registrado')
                                                    ->default(false)
                                                    ->live()
                                                    ->afterStateUpdated(function (callable $set) {
                                                        $set('employee_id', null);
                                                        $set('employee_name', null);
                                                        $set('position_id', null);
                                                        $set('position_name', null);
                                                    })
                                                    ->columnSpan(1),
                                            ]),
                                    ])
                                    ->addActionLabel('Agregar personal')
                                    ->reorderable(false)
                                    ->defaultItems(0)
                                    ->collapsible()
                                    ->afterStateHydrated(function ($component, $state) {
                                        // Hidratar employee_name y position_name desde IDs para registros existentes
                                        if (!is_array($state))
                                            return;

                                        $hydratedState = collect($state)->map(function ($item) {
                                            if (!is_array($item))
                                                return $item;

                                            // Migrar is_custom_text o is_custom_position a is_not_registered
                                            if (!isset($item['is_not_registered'])) {
                                                $item['is_not_registered'] = ($item['is_custom_text'] ?? false) || ($item['is_custom_position'] ?? false);
                                            }

                                            // Hidratar employee_name si tiene employee_id pero no employee_name
                                            if (empty($item['employee_name']) && !empty($item['employee_id'])) {
                                                $employee = Employee::with('position')->find($item['employee_id']);
                                                if ($employee) {
                                                    $item['employee_name'] = $employee->first_name . ' ' . $employee->last_name;
                                                    $item['position_id'] = $employee->position_id;
                                                    $item['position_name'] = $employee->position?->name;
                                                }
                                            }

                                            // Hidratar position_name si tiene position_id pero no position_name
                                            if (empty($item['position_name']) && !empty($item['position_id'])) {
                                                $position = Position::find($item['position_id']);
                                                if ($position) {
                                                    $item['position_name'] = $position->name;
                                                }
                                            }

                                            return $item;
                                        })->toArray();

                                        $component->state($hydratedState);
                                    })
                                    ->itemLabel(fn(array $state): ?string => $state['employee_name'] ?? 'Personal sin nombre')
                                    ->columnSpanFull()
                                    ->disabled(fn(string $operation): bool => $operation === 'view'),
                            ]),
                        // FIN DL TAB DE LISTA DE PERSONAL

                        // INICIO DE TAB DE CONCLUSIONES
                        // INICIO DE TAB DE CONCLUSIONES
                        Tabs\Tab::make('Conclusiones')
                            ->icon('heroicon-o-check-badge')
                            ->columns(2) // Esto define que habrá 2 columnas
                            ->schema([
                                RichEditor::make('conclusions')
                                    ->label('Conclusiones')
                                    // Quitamos columnSpanFull() para que use solo 1 de las 2 columnas
                                    ->maxLength(5000)
                                    ->toolbarButtons([
                                        'bold',
                                        'h2',
                                        'h3',
                                        'orderedList',
                                        'bulletList',
                                        'redo',
                                        'underline',
                                        'undo',
                                    ]),

                                RichEditor::make('suggestions')
                                    ->label('Recomendaciones')
                                    ->helperText('Proporciona sugerencias o comentarios adicionales.')
                                    // Quitamos columnSpanFull() para que ocupe la segunda columna
                                    ->maxLength(5000)
                                    ->toolbarButtons([

                                        'bold',
                                        'bulletList',
                                        'h2',
                                        'h3',
                                        'italic',
                                        'orderedList',
                                        'redo',
                                        'strike',
                                        'underline',
                                        'undo',
                                    ]),
                            ]),
                    ])
                    ->columnSpan('full'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre del Reporte')
                    ->searchable()
                    ->extraAttributes(['class' => 'font-bold'])
                    ->sortable(),

                TextColumn::make('report_date')
                    ->label('Fecha')
                    ->weight('bold')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('employee.first_name')
                    ->label('Supervisor')
                    ->formatStateUsing(fn($record) => $record->employee->first_name . ' ' . $record->employee->last_name)
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),

                TextColumn::make('photos_count')
                    ->label('Evidencias')
                    ->counts('photos')
                    ->badge()
                    ->color(fn(string $state): string => match (true) {
                        $state == 0 => 'danger',
                        $state < 5 => 'warning',
                        default => 'success',
                    }),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->label('Actualizado')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                //AssociateAction::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->icon('heroicon-o-eye')
                        ->color('info'),
                    EditAction::make()
                        ->icon('heroicon-o-pencil-square')
                        ->color('primary'),
                    DeleteAction::make()
                        ->icon('heroicon-o-trash')
                        ->color('danger'),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
