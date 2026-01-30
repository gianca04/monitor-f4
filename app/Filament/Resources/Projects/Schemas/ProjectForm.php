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
use Illuminate\Support\Facades\Auth;
use Mpdf\Tag\A;
use Ramsey\Collection\Set;

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
                                    ]),
                                Hidden::make('employee_id')
                                    ->default(fn() => Auth::user()?->employee_id),

                                Grid::make(4)
                                    ->schema([
                                        TextInput::make('request_number')
                                            ->label('N° de Solicitud')
                                            ->columnSpan(2)
                                            ->maxLength(255),
                                        DatePicker::make('requested_at')
                                            ->label('Fecha de Solicitud')
                                            ->columnSpan(2)
                                            ->default(now()),

                                        Select::make('client_id')
                                            ->required()
                                            ->columnSpan(2)
                                            ->prefixIcon('heroicon-m-briefcase')
                                            ->label('Cliente') // Título para el campo 'Cliente'
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
                                            ->columnSpan(2)
                                            ->prefixIcon('heroicon-m-home-modern')
                                            ->label('Tienda') // Título para el campo 'Tienda'
                                            ->required()
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

                                    ]),

                                Textarea::make('comment')
                                    ->label('Comentario')
                                    ->rows(3),
                            ]),

                        Tabs\Tab::make('Datos de la Visita')
                            ->schema([
                                // ACA COLOCAREMOS SOLAMENTE  A Supervisor de seguimiento.
                                Select::make('supervisor_name')
                                    ->label('Supervisor de seguimiento')
                                    ->options(
                                        Employee::whereIn('id', [40, 50, 55])
                                            ->with('user')
                                            ->get()
                                            ->mapWithKeys(function ($employee) {
                                                return [$employee->id => $employee->fullname];
                                            })
                                            ->toArray()
                                    )
                                    ->searchable()
                                    ->afterStateUpdated(function ($state, $set) {
                                        // Busca el empleado y guarda el nombre en supervisor_name
                                        $employee = Employee::find($state);
                                        $set('supervisor_name', $employee ? $employee->fullname : null);
                                    }),
                                Repeater::make('inspectors')
                                    ->relationship()
                                    ->label('Inspectores asignados')
                                    ->minItems(1)
                                    ->schema([
                                        Select::make('employee_id')
                                            //->default(fn() => Auth::user()?->employee_id)->required()
                                            ->columns(2)
                                            ->reactive()
                                            ->prefixIcon('heroicon-m-user')
                                            ->label('Inspector de la visita') // Título para el campo 'Empleado'
                                            ->options(
                                                function (callable $get) {
                                                    return Employee::query()
                                                        ->select('id', 'first_name', 'last_name', 'document_number')
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
                                            ->searchable() // Activa la búsqueda asincrónica
                                            ->placeholder('Seleccionar un empleado') // Placeholder
                                            ->helperText('Selecciona el empleado responsable de la cotización.') // Ayuda para el campo de empleado

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
                                    ])
                                    ->createItemButtonLabel('Agregar Empleado')
                                    ->columnSpanFull(),

                                Group::make()
                                    ->relationship('visit')
                                    ->schema([
                                        // INICIO DE SELECT DE EMPLEADO
                                        Select::make('quoted_by_id')
                                            //->default(fn() => Auth::user()?->employee_id)->required()
                                            ->columns(2)
                                            ->reactive()
                                            ->prefixIcon('heroicon-m-user')
                                            ->label('Cotizador') // Título para el campo 'Empleado'
                                            ->options(
                                                function (callable $get) {
                                                    return Employee::query()
                                                        ->select('id', 'first_name', 'last_name', 'document_number')
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
                                            ->searchable() // Activa la búsqueda asincrónica
                                            ->placeholder('Seleccionar un empleado') // Placeholder
                                            ->helperText('Selecciona el empleado responsable de la visita.') // Ayuda para el campo de empleado

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

                                        // FIN DE SELECT DE EMPLEADO

                                        DatePicker::make('visit_date')
                                            ->label('Fecha de la visita'),

                                        TimePicker::make('entry_time')
                                            ->label('Hora de ingreso'),

                                        TimePicker::make('exit_time')
                                            ->label('Hora de salida'),

                                        TextInput::make('amount')
                                            ->numeric()
                                            ->prefix('S/ ')
                                            ->label('Monto del Proyecto')
                                            ->formatStateUsing(function ($state, $livewire) {
                                                if ($state) return $state;

                                                $project = null;
                                                // Filament v3 access to record might vary, but usually getRecord works on pages.
                                                // Safer to check if method exists.
                                                if (method_exists($livewire, 'getRecord')) {
                                                    $project = $livewire->getRecord();
                                                }

                                                if (!$project instanceof \App\Models\Project) return null;

                                                // Use the latestQuote relationship and the scopeWithTotal
                                                $quote = $project->latestQuote()->withTotal()->first();

                                                return $quote ? $quote->total_cost : null;
                                            }),

                                        Textarea::make('description')
                                            ->label('Comentarios de la visita')
                                            ->rows(2),
                                    ]),
                            ]),

                        Tabs\Tab::make('Datos del Servicio')
                            ->schema([
                                TextInput::make('work_order_number')
                                    ->label('N° de Orden de Trabajo')
                                    ->maxLength(255),

                                Grid::make(3)->schema([

                                    // 1. FECHA INICIO
                                    DatePicker::make('service_start_date')
                                        ->label('Fecha de inicio del servicio')
                                        ->live() // ⚡ IMPORTANTE: Escucha cambios
                                        ->afterStateUpdated(function (Get $get, Set $set) {
                                            // Recalcular cuando cambia la fecha de inicio
                                            $start = $get('service_start_date');
                                            $end = $get('service_end_date');

                                            if ($start && $end) {
                                                $startDate = Carbon::parse($start);
                                                $endDate = Carbon::parse($end);

                                                // Evitar negativos
                                                if ($endDate->lt($startDate)) {
                                                    $set('service_days', 0);
                                                    return;
                                                }

                                                // diffInDays + 1 para incluir el día de inicio como trabajado
                                                $set('service_days', $startDate->diffInDays($endDate) + 1);
                                            }
                                        }),

                                    // 2. FECHA FIN
                                    DatePicker::make('service_end_date')
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

                                Select::make('task_type')
                                    ->label('Tipo de tarea')
                                    ->options([
                                        'OPEX' => 'OPEX',
                                        'CAPEX' => 'CAPEX',
                                    ]),

                                Select::make('has_quote')
                                    ->label('¿Tiene cotización?')
                                    ->default('NO')
                                    ->native(false)
                                    ->options([
                                        'SI' => 'SI',
                                        'NO' => 'NO',
                                    ]),

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

                        Tabs\Tab::make('Datos de Facturación')
                            ->schema([
                                Select::make('fracttal_status')
                                    ->label('Estado en Fracttal')
                                    ->native(false)
                                    ->options([
                                        'Sin OT' => 'Sin OT',
                                        'En Proceso' => 'En Proceso',
                                        'En Revisión' => 'En Revisión',
                                        'Finalizado' => 'Finalizado',
                                    ])
                                    ->default('Sin OT'),

                                TextInput::make('purchase_order')
                                    ->label('Orden de Compra')
                                    ->maxLength(255),

                                TextInput::make('migo_code')
                                    ->label('MIGO')
                                    ->maxLength(255),
                            ]),

                        Tabs\Tab::make('Seguimiento')
                            ->columns(2)
                            ->schema([
                                Select::make('status')
                                    ->label('Estado del servicio')
                                    ->options([
                                        'Pendiente' => 'Pendiente',
                                        'Enviada' => 'Enviada',
                                        'Aprobado' => 'Aprobado',
                                        'En Ejecución' => 'En Ejecución',
                                        'Completado' => 'Completado',
                                        'Facturado' => 'Facturado',
                                        'Anulado' => 'Anulado',
                                    ])
                                    ->default('Pendiente')
                                    ->live(),

                                DatePicker::make('quote_sent_at')
                                    ->label('Fecha Cotización Enviada'),

                                DatePicker::make('quote_approved_at')
                                    ->label('Fecha Cotización Aprobada'),

                                DatePicker::make('wo_review_at')
                                    ->label('Fecha OT en Revisión')
                                    ->live(),

                                DatePicker::make('wo_completed_at')
                                    ->label('Fecha OT Finalizado')
                                    ->live() // Importante para que el cambio sea instantáneo
                                    ->afterStateUpdated(fn(Get $get, Set $set) => self::calculateDays($get, $set)),

                                TextInput::make('days_to_completion')
                                    ->label('Días desde OT Finalizado')
                                    ->readOnly()
                                    ->numeric()
                                    ->dehydrated(),

                                Textarea::make('final_comments')
                                    ->label('Comentarios Finales')
                                    ->rows(2),
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
            // o usa un cálculo manual según tu necesidad de negocio
            $diff = $endDate->diffInDays($completedDate, false);

            $set('days_to_completion', (int) $diff);
        } else {
            $set('days_to_completion', null);
        }
    }
}
