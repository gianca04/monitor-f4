<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Forms\Components\ClientMainInfo;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Project;
use App\Models\SubClient;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Mpdf\Tag\A;
use Filament\Schemas\Components\Utilities\Set;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Solicitud')
                    ->tabs([
                        Tab::make('Datos Generales')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Descripción de la solicitud')
                                            ->required()
                                            ->columnSpan(2),

                                        TextInput::make('service_code')
                                            ->label('Codigo de Servicio')
                                            ->default('COT-' . (Project::max('id') + 1))
                                            // ->helperText('Correlativo generado automáticamente y no editable')
                                            ->disabled()
                                            ->dehydrated()
                                            ->columnSpan(1),

                                        Hidden::make('employee_id')
                                            ->default(fn() => Auth::user()?->employee_id),

                                        TextInput::make('request_number')
                                            ->label('N° de Solicitud (ST)')
                                            ->columnSpan(1)
                                            ->maxLength(255),

                                        Grid::make(2)->schema([
                                            Select::make('client_id')
                                                ->required()
                                                ->columnSpan(1)
                                                ->prefixIcon('heroicon-m-briefcase')
                                                ->label('Compañia') // Título para el campo 'Cliente'
                                                ->preload()
                                                ->searchable() // Activa la búsqueda asincrónica
                                                ->options(
                                                    Client::whereIn('id', [127, 164])
                                                        ->get()
                                                        ->mapWithKeys(fn($client) => [
                                                            $client->id => "{$client->business_name} - {$client->document_number}"
                                                        ])
                                                )
                                                ->getOptionLabelUsing(fn($value): ?string => Client::find($value)?->business_name)
                                                ->reactive() // Hace el campo reactivo
                                                ->afterStateUpdated(fn($state, callable $set) => $set('sub_client_id', null))
                                                ->helperText('Selecciona el cliente para esta cotización.')

                                                // Botón para ver información del cliente
                                                ->suffixAction(
                                                    Action::make('view_client')
                                                        ->icon('heroicon-o-eye')
                                                        ->tooltip('Ver información del cliente')
                                                        ->color('info')
                                                        ->action(function (callable $get) {
                                                            $clientId = $get('client_id');
                                                            if (!$clientId) {
                                                                Notification::make()
                                                                    ->title('Selecciona un cliente primero')
                                                                    ->warning()
                                                                    ->send();
                                                                return;
                                                            }
                                                        })
                                                        ->modalContent(function (callable $get) {
                                                            $clientId = $get('client_id');
                                                            if (!$clientId)
                                                                return null;

                                                            $client = Client::with('subClients')->find($clientId);
                                                            if (!$client)
                                                                return null;

                                                            return view('filament.components.client-info-modal', compact('client'));
                                                        })
                                                        ->modalHeading('Información del Cliente')
                                                        ->modalSubmitAction(false)
                                                        ->modalCancelActionLabel('Cerrar')
                                                        ->modalWidth('2xl')
                                                        ->visible(fn(callable $get) => !empty($get('client_id')))
                                                )

                                                ->createOptionForm([
                                                    ClientMainInfo::make()
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
                                                })

                                                ->afterStateUpdated(function (callable $get, callable $set) {
                                                    $clientId = $get('client_id');
                                                    if ($clientId) {
                                                        // Cargar toda la información del cliente en una sola consulta
                                                        $client = Client::find($clientId);
                                                        if ($client) {
                                                            // Actualizar los campos de 'business_name' y 'document_number' solo si hay un cliente
                                                            $set('business_name', $client->business_name);
                                                            $set('document_type_client', $client->document_type);
                                                            $set('document_number_client', $client->document_number);
                                                            $set('contact_phone', $client->contact_phone);
                                                            $set('contact_email', $client->contact_email);
                                                        }
                                                    } else {
                                                        // Limpiar los campos si no hay cliente seleccionado
                                                        $set('business_name', null);
                                                        $set('document_number', null);
                                                    }
                                                }),

                                            Select::make('sub_client_id')
                                                ->prefixIcon('heroicon-m-home-modern')
                                                ->label('Centro de costos') // Título para el campo 'Tienda'
                                                ->required()
                                                ->columnSpan(1)
                                                ->options(
                                                    function (callable $get) {
                                                        $clientId = $get('client_id');
                                                        return SubClient::where('client_id', $clientId)
                                                            ->get()
                                                            ->mapWithKeys(function ($subClient) {
                                                                return [$subClient->id => $subClient->name];
                                                            })
                                                            ->toArray();
                                                    }
                                                )
                                                ->reactive()
                                                ->searchable()
                                                ->disabled(fn($get) => !$get('client_id')) // Deshabilita si no hay cliente seleccionado
                                                ->helperText('Selecciona el Sede para esta cotización.') // Ayuda para el campo 'Tienda'

                                                // Cuando se carga un registro existente, seleccionar automáticamente el cliente
                                                ->afterStateHydrated(function ($state, callable $set) {
                                                    if ($state) {
                                                        $subClient = SubClient::find($state);
                                                        if ($subClient) {
                                                            $set('client_id', $subClient->client_id);
                                                        }
                                                    }
                                                })

                                                // Botón para ver información de la tienda
                                                ->suffixAction(
                                                    Action::make('view_sub_client')
                                                        ->icon('heroicon-o-eye')
                                                        ->tooltip('Ver información de la tienda')
                                                        ->color('info')
                                                        ->action(function (callable $get) {
                                                            $subClientId = $get('sub_client_id');
                                                            if (!$subClientId) {
                                                                Notification::make()
                                                                    ->title('Selecciona una tienda primero')
                                                                    ->warning()
                                                                    ->send();
                                                                return;
                                                            }
                                                        })
                                                        ->modalContent(function (callable $get) {
                                                            $subClientId = $get('sub_client_id');
                                                            if (!$subClientId)
                                                                return null;

                                                            $subClient = SubClient::with('client')->find($subClientId);
                                                            if (!$subClient)
                                                                return null;

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
                                                        ->label('Nombre del subcliente')
                                                        ->required()
                                                        ->maxLength(255)
                                                        ->prefixIcon('heroicon-o-user'),

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
                                                ])
                                                ->createOptionUsing(function (array $data, callable $get): int {
                                                    $data['client_id'] = $get('client_id');
                                                    $subClient = SubClient::create($data);
                                                    return $subClient->id;
                                                })
                                                ->createOptionAction(function (Action $action) {
                                                    return $action
                                                        ->modalHeading('Crear nueva tienda')
                                                        ->modalButton('Crear tienda')
                                                        ->modalWidth('2xl');
                                                })
                                                ->afterStateUpdated(function (callable $get, callable $set) {
                                                    $subClientId = $get('sub_client_id');
                                                    if ($subClientId) {
                                                        // Cargar toda la información del Sede en una sola consulta
                                                        $subClient = SubClient::find($subClientId);
                                                        if ($subClient) {
                                                        }
                                                    } else {
                                                        // Limpiar los campos si no hay Sede seleccionado
                                                        $set('name', null);
                                                        $set('location', null);
                                                    }
                                                }),
                                        ])->columnSpan(4),

                                    ])
                                    ->columnSpanFull(),

                                Grid::make(3)
                                    ->schema([
                                        DateTimePicker::make('requested_at')
                                            ->label('Fecha y Hora de Invitación')
                                            ->columnSpan(1)
                                            ->default(now()),

                                        DateTimePicker::make('invitation_responded_at')
                                            ->label('Fecha y Hora de Respuesta de Invitación')
                                            ->columnSpan(1),

                                        Select::make('service_type')
                                            ->label('Tipo de atención')
                                            ->columnSpan(1)
                                            ->options([
                                                'Correctivo' => 'Correctivo',
                                                'Emergencia' => 'Emergencia',
                                                'ITSE' => 'ITSE',
                                                'Preventivo' => 'Preventivo',
                                            ])
                                            ->placeholder('Seleccionar tipo de atención')
                                            ->searchable()
                                            ->preload()
                                            ->prefixIcon('heroicon-m-wrench-screwdriver'),

                                    ])
                                    ->columnSpanFull(),

                                Grid::make(2)
                                    ->schema([
                                        Select::make('status')
                                            ->label('Estado de invitación')
                                            ->options([
                                                'Pendiente' => 'Pendiente',
                                                'Enviado' => 'Enviado',
                                                'Aprobado' => 'Aprobado',
                                                'En Ejecución' => 'En Ejecución',
                                                'Completado' => 'Completado',
                                                'Facturado' => 'Facturado',
                                                'Anulado' => 'Anulado',
                                            ])
                                            ->default('Pendiente'),

                                        Select::make('fracttal_status')
                                            ->label('Estado en Fracttal')
                                            ->native(false)
                                            ->options([
                                                'Sin OT' => 'Sin OT',
                                                'En Proceso' => 'En Proceso',
                                                'En Revisión' => 'En Revisión',
                                                'Finalizado' => 'Finalizado',
                                                'Cancelada' => 'Cancelada',
                                                'Resuelta | Sin OT' => 'Resuelta | Sin OT',
                                            ])
                                            ->default('Sin OT'),

                                        Textarea::make('comment')
                                            ->label('Comentario')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),


                            ]),

                        Tabs\Tab::make('Datos de la Visita')
                            ->schema([
                                Grid::make(2)->schema([
                                    // ACA COLOCAREMOS SOLAMENTE  A Supervisor de seguimiento.
                                    Select::make('supervisor_id')
                                        ->placeholder('Seleccionar un supervisor') // Placeholder
                                        ->label('Supervisor de seguimiento')
                                        ->prefixIcon('heroicon-m-user')
                                        ->options(
                                            function (callable $get) {
                                                return Employee::query()
                                                    ->select('id', 'first_name', 'last_name', 'document_number')
                                                    // Filtrar solo empleados con rol 'supervisor'
                                                    ->whereHas('user.roles', function ($query) {
                                                        $query->where('name', 'Supervisor');
                                                    })
                                                    ->when($get('search'), function ($query, $search) {
                                                        $query->where('first_name', 'like', "%{$search}%")
                                                            ->orWhere('last_name', 'like', "%{$search}%")
                                                            ->orWhere('document_number', 'like', "%{$search}%");
                                                    })
                                                    ->get()
                                                    ->mapWithKeys(function ($employee) {
                                                        return [$employee->id => $employee->full_name];
                                                    })
                                                    ->toArray();
                                            }
                                        )
                                        ->searchable()
                                        ->helperText('Solo el personal con el rol de "Supervisor" podra ser seleccionado.'),
                                    Select::make('inspectors')
                                        ->label('Inspectores asignados')
                                        ->multiple()
                                        ->relationship(
                                            name: 'inspectors',
                                            titleAttribute: 'first_name',
                                            modifyQueryUsing: fn(Builder $query) => $query->whereHas('user.roles', fn($q) => $q->where('name', 'Inspector'))
                                        )
                                        ->getOptionLabelFromRecordUsing(fn(Employee $record) => $record->full_name)
                                        ->preload()
                                        ->searchable()
                                        ->prefixIcon('heroicon-m-user')
                                        ->placeholder('Seleccionar inspectores')
                                        ->helperText('Solo el personal con el rol de "Inspector" podrá ser seleccionado.'),
                                ]),

                                Group::make()
                                    ->relationship('visit')
                                    ->schema([


                                        Grid::make(3)
                                            ->columnSpanFull()
                                            ->schema([
                                                DatePicker::make('visit_date')
                                                    ->label('Fecha de la visita'),

                                                TimePicker::make('entry_time')
                                                    ->label('Hora de ingreso')
                                                    ->seconds(false)
                                                    ->displayFormat('H:i'),

                                                TimePicker::make('exit_time')
                                                    ->label('Hora de salida')
                                                    ->seconds(false)
                                                    ->displayFormat('H:i'),

                                            ]),

                                        Textarea::make('description')
                                            ->label('Comentarios de la visita')
                                            ->rows(2),
                                    ]),
                            ]),

                        Tabs\Tab::make('Datos de la cotización')

                            ->schema([
                                // INICIO DE SELECT DE EMPLEADO

                                // TODO_ AQUI SOLO QUEDARA: COTIZADOR, MONTO Y FECHA DE ENVIO.

                                Select::make('quoted_by_id')
                                    //->default(fn() => Auth::user()?->employee_id)->required()
                                    ->reactive()
                                    ->prefixIcon('heroicon-m-user')
                                    ->label('Cotizador') // Título para el campo 'Empleado'
                                    ->options(
                                        function (callable $get) {
                                            return Employee::query()
                                                ->select('id', 'first_name', 'last_name', 'document_number')
                                                // Filtrar solo empleados con rol 'cotizador'
                                                ->whereHas('user.roles', function ($query) {
                                                    $query->where('name', 'cotizador');
                                                })
                                                ->when($get('search'), function ($query, $search) {
                                                    $query->where('first_name', 'like', "%{$search}%")
                                                        ->orWhere('last_name', 'like', "%{$search}%")
                                                        ->orWhere('document_number', 'like', "%{$search}%");
                                                })
                                                ->get()
                                                ->mapWithKeys(function ($employee) {
                                                    return [$employee->id => $employee->full_name];
                                                })
                                                ->toArray();
                                        }
                                    )
                                    ->getOptionLabelUsing(fn($value): ?string => Employee::find($value)?->full_name)
                                    ->searchable() // Activa la búsqueda asincrónica
                                    ->placeholder('Seleccionar un empleado') // Placeholder
                                    ->helperText('Solo el personal con el rol de "Cotizador" podra ser seleccionado.') // Ayuda para el campo de empleado

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

                                    // Botón para ver información del empleado
                                    ->suffixAction(
                                        Action::make('view_employee')
                                            ->icon('heroicon-o-eye')
                                            ->tooltip('Ver información del supervisor')
                                            ->color('info')
                                            ->action(function (callable $get) {
                                                $employeeId = $get('employee_id');
                                                if (!$employeeId) {
                                                    Notification::make()
                                                        ->title('Selecciona un supervisor primero')
                                                        ->warning()
                                                        ->send();
                                                    return;
                                                }
                                            })
                                            ->modalContent(function (callable $get) {
                                                $employeeId = $get('employee_id');
                                                if (!$employeeId)
                                                    return null;

                                                $employee = Employee::with('user')->find($employeeId);
                                                if (!$employee)
                                                    return null;

                                                return view('filament.components.employee-info-modal', compact('employee'));
                                            })
                                            ->modalHeading('Información del Supervisor')
                                            ->modalSubmitAction(false)
                                            ->modalCancelActionLabel('Cerrar')
                                            ->modalWidth('2xl')
                                            ->visible(fn(callable $get) => !empty($get('employee_id')))
                                    )
                                    ->afterStateHydrated(function (callable $get, callable $set) {
                                        $employeeId = $get('employee_id');
                                        if ($employeeId) {
                                            $employee = Employee::with('user')->find($employeeId);
                                            if ($employee) {
                                                $set('document_type', $employee->document_type);
                                                $set('document_number', $employee->document_number);
                                                $set('address', $employee->address);
                                                $set('date_contract', $employee->date_contract);
                                                $set('user_email', $employee->user?->email);
                                                $set('user_is_active', $employee->user?->is_active ? 'Activo' : 'Inactivo');
                                            } else {
                                                $set('user_email', null);
                                                $set('user_is_active', null);
                                            }
                                        }
                                    }),


                                Grid::make(2)->schema([

                                    TextInput::make('amount')
                                        ->numeric()
                                        ->prefix('S/ ')
                                        ->label('Monto del Proyecto')
                                        ->readOnly()
                                        ->visibleOn('edit')
                                        ->formatStateUsing(function ($state, $livewire) {
                                            if ($state)
                                                return $state;

                                            $project = null;
                                            // Filament v3 access to record might vary, but usually getRecord works on pages.
                                            // Safer to check if method exists.
                                            if (method_exists($livewire, 'getRecord')) {
                                                $project = $livewire->getRecord();
                                            }

                                            if (!$project instanceof Project)
                                                return null;

                                            // Use the latestQuote relationship and the scopeWithTotal
                                            $quote = $project->latestQuote()->withTotal()->first();

                                            return $quote ? $quote->total_cost : null;
                                        }),


                                    // 1. FECHA INICIO
                                    DateTimePicker::make('quote_sent_at')
                                        ->label('Fecha Cotización Enviada'),

                                ]),

                                Grid::make(2)
                                    ->columnSpanFull()
                                    ->schema([


                                    ]),

                            ]),

                        Tabs\Tab::make('Seguimiento')
                            ->schema([
                                Grid::make(4)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('work_order_number')
                                            ->label('N° de Orden de Trabajo (OT)')
                                            ->maxLength(255),

                                        Select::make('task_type')
                                            ->label('Tipo de tarea')
                                            ->options([
                                                'OPEX' => 'OPEX',
                                                'CAPEX' => 'CAPEX',
                                            ]),

                                        TextInput::make('purchase_order')
                                            ->label('Orden de Compra (OC)')
                                            ->maxLength(255),

                                        TextInput::make('migo_code')
                                            ->label('MIGO')
                                            ->maxLength(255),
                                    ]),
                                Grid::make(3)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('has_quote')
                                            ->label('¿Tiene cotización?')
                                            ->disabled()
                                            ->dehydrated(false) // No guardar en BD
                                            ->formatStateUsing(function ($record) {
                                                return $record?->has_quote ?? 'NO';
                                            }),

                                        Select::make('has_report')
                                            ->label('¿Tiene informe?')
                                            ->default('NO')
                                            ->native(false)
                                            ->options([
                                                'SI' => 'SI',
                                                'NO' => 'NO',
                                            ]),

                                        Select::make('compliance_relation_view') // Nombre virtual único
                                            ->label('Acta de Conformidad Relacionada')
                                            ->placeholder('No se ha generado Acta para este proyecto')

                                            // 1. Cargar la opción si existe la relación
                                            ->options(function (?Project $record) {
                                                if (!$record || !$record->compliance) {
                                                    return [];
                                                }
                                                // Mostramos el ID y el Estado del acta encontrada
                                                return [
                                                    $record->compliance->id => "Acta #{$record->compliance->id} - Estado: {$record->compliance->state}"
                                                ];
                                            })

                                            // 2. Pre-seleccionar el valor (Hidratar)
                                            ->afterStateHydrated(function ($component, ?Project $record) {
                                                // Le asignamos al select el ID del acta relacionada
                                                $component->state($record?->compliance?->id);
                                            })

                                            // 3. Configuraciones visuales y de seguridad
                                            ->disabled()        // Bloqueado porque no puedes cambiar el acta desde aquí (es 1:1)
                                            ->dehydrated(false) // IMPORTANTE: Esto evita que Filament intente guardar este campo en la tabla 'projects'
                                            ->prefixIcon('heroicon-m-document-check')

                                            // 4. Botón de Acción para ir al Acta o Descargarla (Opcional pero muy útil)
                                            ->suffixAction(
                                                Action::make('view_compliance_pdf')
                                                    ->icon('heroicon-o-eye')
                                                    ->tooltip('Ver/Descargar PDF')
                                                    ->color('success')
                                                    ->url(
                                                        fn(?Project $record) => $record?->compliance
                                                            ? url("/actas/{$record->compliance->id}/preview")
                                                            : null
                                                    )
                                                    ->openUrlInNewTab()
                                                    ->visible(fn(?Project $record) => $record?->compliance !== null)
                                            ),
                                    ]),
                                Grid::make(3)
                                    ->schema([
                                        DateTimePicker::make('service_start_date')
                                            ->label('Fecha de inicio del servicio')
                                            ->live()
                                            ->afterStateUpdated(fn(Get $get, Set $set) => self::calculateDays($get, $set)),
                                        // 2. FECHA FIN
                                        DateTimePicker::make('service_end_date')
                                            ->label('Fecha de fin del servicio')
                                            ->live()
                                            ->afterStateUpdated(fn(Get $get, Set $set) => self::calculateDays($get, $set)),
                                        // 3. DÍAS (AUTOMÁTICO)
                                        TextInput::make('service_days')
                                            ->label('Días de servicio')
                                            ->numeric()
                                            ->readOnly() // Bloqueado para que el usuario no lo rompa
                                            ->dehydrated() // Asegura que se envíe a la BD aunque sea ReadOnly
                                            ->suffix('días'),
                                    ]),
                                Grid::make(3)
                                    ->schema([
                                        DatePicker::make('quote_approved_at')
                                            ->label('Fecha Cotización Aprobada'),
                                        DatePicker::make('wo_review_at')
                                            ->label('Fecha OT en Revisión')
                                            ->live(),
                                        DatePicker::make('wo_completed_at')
                                            ->label('Fecha OT Finalizado')
                                            ->live() // Importante para que el cambio sea instantáneo
                                            ->afterStateUpdated(fn(Get $get, Set $set) => self::calculateDays($get, $set)),
                                        /*TextInput::make('days_to_completion')
                                            ->label('Días desde OT Finalizado')
                                            ->readOnly()
                                            ->numeric()
                                            ->dehydrated(),
                                            */
                                    ]),
                                Textarea::make('final_comments')
                                    ->label('Comentarios Finales')
                                    ->maxLength(255)
                                    ->columnSpanFull()
                                    ->rows(2),
                            ]),

                        Tabs\Tab::make('KPI')
                            ->schema([

                                Grid::make(4)
                                    ->columnSpanFull()
                                    ->schema([



                                        TextInput::make('emergency_response_time_hrs')
                                            ->label('Rsta. Emergencia Real (Hrs)')
                                            ->numeric()
                                            ->minValue(0)
                                            ->step(0.01)
                                            ->suffix('Hrs')
                                            ->placeholder(''),

                                        TextInput::make('emergency_attendance_time_hrs')
                                            ->label('Ate. Emergencia Real (Hrs)')
                                            ->numeric()
                                            ->minValue(0)
                                            ->step(0.01)
                                            ->suffix('Hrs')
                                            ->placeholder(''),

                                        TextInput::make('corrective_quote_upload_time_hrs')
                                            ->label('Carga Cotización Real (Hrs)')
                                            ->numeric()
                                            ->minValue(0)
                                            ->step(0.01)
                                            ->suffix('Hrs')
                                            ->placeholder(''),

                                        TextInput::make('corrective_execution_time_hrs')
                                            ->label('Ejecución Correctivo Real (Hrs)')
                                            ->numeric()
                                            ->minValue(0)
                                            ->step(0.01)
                                            ->suffix('Hrs')
                                            ->placeholder(''),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
    public static function calculateDays(Get $get, Set $set)
    {
        $start = $get('service_start_date');
        $end = $get('service_end_date');
        $completedAt = $get('wo_completed_at');

        // 1. Cálculo de días de servicio (Lógica que ya tenías)
        if ($start && $end) {
            $startDate = Carbon::parse($start);
            $endDate = Carbon::parse($end);

            if ($endDate->lt($startDate)) {
                Notification::make()
                    ->title('Error en fechas')
                    ->body('La fecha fin no puede ser anterior al inicio.')
                    ->warning()
                    ->send();
                $set('service_end_date', null);
                $set('service_days', 0);
            } else {
                $set('service_days', $startDate->diffInDays($endDate) + 1);
            }
        }

        // 2. Cálculo de días hasta finalización de OT (Lo nuevo)
        if ($end && $completedAt) {
            $endDate = Carbon::parse($end);
            $completedDate = Carbon::parse($completedAt);

            // diffInDays devuelve el valor absoluto, si quieres permitir negativos quita el 'true'
            $diff = $endDate->diffInDays($completedDate, false);

            $set('days_to_completion', (int) $diff);
        } else {
            $set('days_to_completion', null);
        }
    }
}
