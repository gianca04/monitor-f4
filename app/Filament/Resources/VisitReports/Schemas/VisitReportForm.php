<?php

namespace App\Filament\Resources\VisitReports\Schemas;

use App\Models\Client;
use App\Models\Employee;
use App\Models\Project;
use App\Models\SubClient;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class VisitReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('MainTabs')
                    ->tabs([

                        // ── TAB: Información General ──
                        Tab::make('Información general')
                            ->icon('heroicon-o-information-circle')
                            ->columns(2)
                            ->schema([

                                Hidden::make('employee_id')
                                    ->default(Auth::user()->employee_id)
                                    ->required(),

                                Select::make('project_id')
                                    ->label('Solicitud de trabajo')
                                    ->prefixIcon('heroicon-m-briefcase')
                                    ->helperText('Solo se listan las solicitudes pendientes, enviados o aprobados')
                                    ->relationship(
                                        name: 'project',
                                        titleAttribute: 'service_code',
                                        modifyQueryUsing: fn(Builder $query) => $query
                                            ->allowedForUser()
                                            ->pendientesORevision()
                                    )
                                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->service_code} - {$record->name}")
                                    ->searchable(['service_code', 'name'])
                                    ->preload()
                                    ->live()
                                    ->nullable()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $project = Project::with('subClient')->find($state);
                                            if ($project?->subClient) {
                                                $set('client_id', $project->subClient->client_id);
                                                $set('sub_client_id', $project->sub_client_id);
                                            }
                                        }
                                    }),

                                Select::make('client_id')
                                    ->label('Cliente')
                                    ->required()
                                    ->prefixIcon('heroicon-m-briefcase')
                                    ->preload()
                                    ->searchable()
                                    ->options(Client::query()->pluck('business_name', 'id'))
                                    ->getOptionLabelUsing(fn($value): ?string => Client::find($value)?->business_name)
                                    ->live()
                                    ->afterStateUpdated(fn(callable $set) => $set('sub_client_id', null))
                                    ->afterStateHydrated(function ($state, callable $set, $record) {
                                        if ($record && $record->sub_client) {
                                            $set('client_id', $record->sub_client->client_id);
                                        }
                                    })
                                    ->helperText('Selecciona el cliente para esta visita.')
                                    ->suffixAction(
                                        Action::make('view_client')
                                            ->icon('heroicon-o-eye')
                                            ->tooltip('Ver información del cliente')
                                            ->color('info')
                                            ->action(function (callable $get) {
                                                if (!$get('client_id')) {
                                                    \Filament\Notifications\Notification::make()
                                                        ->title('Selecciona un cliente primero')
                                                        ->warning()
                                                        ->send();
                                                }
                                            })
                                            ->modalContent(function (callable $get) {
                                                $clientId = $get('client_id');
                                                if (!$clientId) return null;
                                                $client = Client::with('subClients')->find($clientId);
                                                if (!$client) return null;
                                                return view('filament.components.client-info-modal', compact('client'));
                                            })
                                            ->modalHeading('Información del Cliente')
                                            ->modalSubmitAction(false)
                                            ->modalCancelActionLabel('Cerrar')
                                            ->modalWidth('2xl')
                                            ->visible(fn(callable $get) => !empty($get('client_id')))
                                    )
                                    ->createOptionForm([
                                        \App\Forms\Components\ClientMainInfo::make()
                                    ])
                                    ->createOptionUsing(function (array $data): int {
                                        $client = Client::create($data);
                                        return $client->id;
                                    })
                                    ->createOptionAction(function (Action $action) {
                                        return $action
                                            ->modalHeading('Crear nuevo cliente')
                                            ->modalButton('Crear cliente')
                                            ->modalWidth('6xl');
                                    }),

                                Select::make('sub_client_id')
                                    ->label('Sede / Tienda')
                                    ->required()
                                    ->prefixIcon('heroicon-m-home-modern')
                                    ->options(function (callable $get) {
                                        $clientId = $get('client_id');
                                        if (!$clientId) return [];
                                        return SubClient::where('client_id', $clientId)
                                            ->pluck('name', 'id')
                                            ->toArray();
                                    })
                                    ->live()
                                    ->searchable()
                                    ->disabled(fn($get) => !$get('client_id'))
                                    ->helperText('Selecciona la Sede para esta visita.')
                                    ->suffixAction(
                                        Action::make('view_sub_client')
                                            ->icon('heroicon-o-eye')
                                            ->tooltip('Ver información de la tienda')
                                            ->color('info')
                                            ->action(function (callable $get) {
                                                if (!$get('sub_client_id')) {
                                                    \Filament\Notifications\Notification::make()
                                                        ->title('Selecciona una tienda primero')
                                                        ->warning()
                                                        ->send();
                                                }
                                            })
                                            ->modalContent(function (callable $get) {
                                                $subClientId = $get('sub_client_id');
                                                if (!$subClientId) return null;
                                                $subClient = SubClient::with('client')->find($subClientId);
                                                if (!$subClient) return null;
                                                return view('filament.components.sub-client-info-modal', compact('subClient'));
                                            })
                                            ->modalHeading('Información de la Sede')
                                            ->modalSubmitAction(false)
                                            ->modalCancelActionLabel('Cerrar')
                                            ->modalWidth('2xl')
                                            ->visible(fn(callable $get) => !empty($get('sub_client_id')))
                                    )
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->label('Nombre de la sede')
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
                                            ->placeholder('Dirección de la sede')
                                            ->maxLength(255)
                                            ->prefixIcon('heroicon-o-map-pin'),
                                        Textarea::make('description')
                                            ->label('Descripción')
                                            ->maxLength(500)
                                            ->autosize()
                                            ->columnSpanFull(),
                                    ])
                                    ->createOptionUsing(function (array $data, callable $get): int {
                                        $data['client_id'] = $get('client_id');
                                        $subClient = SubClient::create($data);
                                        return $subClient->id;
                                    })
                                    ->createOptionAction(function (Action $action) {
                                        return $action
                                            ->modalHeading('Crear nueva sede')
                                            ->modalButton('Crear sede')
                                            ->modalWidth('2xl');
                                    }),

                                // Logica para filtrar unicamente los projectos que no se encuentren en estado 'Finalizado',

                                TextInput::make('name')
                                    ->label('Nombre del reporte')
                                    ->required()
                                    ->maxLength(255),

                                Grid::make(3)
                                    ->columnSpanFull()
                                    ->schema([
                                        DatePicker::make('report_date')
                                            ->label('Fecha')
                                            ->native(false)
                                            ->displayFormat('d/m/Y')
                                            ->required()
                                            ->helperText('Selecciona la fecha de la visita')
                                            ->suffixAction(
                                                Action::make('set_today')
                                                    ->icon('heroicon-o-calendar')
                                                    ->tooltip('Establecer fecha de hoy')
                                                    ->color('primary')
                                                    ->action(function (callable $set) {
                                                        $set('report_date', now()->format('Y-m-d'));
                                                    })
                                            ),

                                        TimePicker::make('start_time')
                                            ->label('Hora de inicio')
                                            ->seconds(false)
                                            ->displayFormat('H:i')
                                            ->helperText('Hora de inicio de la visita'),

                                        TimePicker::make('end_time')
                                            ->label('Hora de finalización')
                                            ->seconds(false)
                                            ->displayFormat('H:i')
                                            ->helperText('Hora de finalización de la visita'),
                                    ]),
                            ]),

                        // ── TAB: Actividades ──
                        Tab::make('Actividades')
                            ->icon('heroicon-o-clipboard-document-list')
                            ->columns(1)
                            ->schema([

                                RichEditor::make('work_to_do')
                                    ->label('Trabajos a realizar')
                                    ->helperText('Describe las actividades planificadas o realizadas durante la visita.')
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

                        // ── TAB: Conclusiones y Recomendaciones ──
                        Tab::make('Conclusiones')
                            ->icon('heroicon-o-check-badge')
                            ->columns(2)
                            ->schema([

                                RichEditor::make('conclusions')
                                    ->label('Conclusiones')
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

                                RichEditor::make('suggestions')
                                    ->label('Recomendaciones')
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

                        // ── TAB: Evidencias Fotográficas ──

                    ])
                    ->columnSpan('full'),

                Repeater::make('visitPhotos')
                    ->relationship('visitPhotos')
                    ->label('Fotografías de la visita')
                    ->visible(fn(string $operation): bool => in_array($operation, ['edit', 'view']))
                    ->helperText('Agrega fotografías como evidencia de la visita realizada.')
                    ->grid(2)
                    ->schema([
                        Group::make([
                            FileUpload::make('photo_path')
                                ->label('Fotografía')
                                ->image()
                                ->downloadable()
                                ->directory('visit-reports/photos')
                                ->visibility('public')
                                ->disk('public')
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->maxSize(25600)
                                ->extraInputAttributes(['capture' => 'user'])
                                ->imageEditor()
                                ->imageEditorAspectRatios([null, '16:9', '4:3', '1:1'])
                                ->imageResizeMode('cover')
                                ->imageResizeTargetWidth(1920)
                                ->imageResizeTargetHeight(1080)
                                ->imagePreviewHeight('200')
                                ->orientImagesFromExif(true)
                                ->helperText('Formatos: JPEG, PNG, WebP. Máx: 25MB.'),

                            Textarea::make('description')
                                ->label('Descripción')
                                ->placeholder('Describe lo observado...')
                                ->rows(2)
                                ->maxLength(500),
                        ])
                            ->columns(1) // Apila foto y descripción verticalmente dentro de cada ítem
                    ])
                    ->addActionLabel('Agregar fotografía')
                    ->reorderable(false)
                    ->defaultItems(0)
                    ->collapsible()
                    ->cloneable()
                    ->itemLabel(
                        fn(array $state): ?string => ($state['description'] ?? null)
                            ? 'Foto: ' . \Illuminate\Support\Str::limit($state['description'], 30)
                            : 'Nueva fotografía'
                    )
                    ->columnSpanFull(),

            ]);
    }
}
