<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Models\SubClient;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('service_code')
                    ->label('Correlativo')
                    ->alignJustify()
                    ->badge()
                    ->searchable()
                    ->extraAttributes(['class' => 'font-bold'])
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Nombre del Proyecto')
                    ->searchable()
                    ->alignJustify()
                    ->extraAttributes(['class' => 'font-bold'])
                    ->limit(30)
                    ->tooltip(fn($record) => $record->name)
                    ->sortable(),

                TextColumn::make('subClient.client.business_name')
                    ->label('Cliente')
                    ->placeholder('No definido')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('subClient.name')
                    ->label('Tienda')
                    ->placeholder('No definido')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('visit.quotedBy.first_name')
                    ->label('Cotizador')
                    ->placeholder('No definido')
                    ->formatStateUsing(fn($record) => $record->visit?->quotedBy
                        ? $record->visit->quotedBy->first_name . ' ' . $record->visit->quotedBy->last_name
                        : null)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('visit.quotedBy', function (Builder $q) use ($search) {
                            $q->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->join('visits', 'projects.id', '=', 'visits.project_id')
                            ->join('employees', 'visits.quoted_by_id', '=', 'employees.id')
                            ->orderBy('employees.first_name', $direction);
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('status')
                    ->label('Estado')
                    ->placeholder('No definido')
                    ->badge()
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'pending' => 'Pendiente',
                        null, '' => 'No definido',
                        default => ucfirst($state),
                    })
                    ->color(fn(?string $state): string => match ($state) {
                        'pending', 'Pendiente' => 'warning',
                        'Enviado' => 'info',
                        'Aprobado' => 'success',
                        'En Ejecución' => 'primary',
                        'Completado', 'Facturado' => 'success',
                        'Anulado' => 'danger',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('service_start_date')
                    ->label('Fecha Inicio')
                    ->placeholder('No definido')
                    ->date('d/m/Y')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('request_number')
                    ->label('N° de Solicitud')
                    ->placeholder('No definido')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('service_end_date')
                    ->label('Fecha Fin')
                    ->placeholder('No definido')
                    ->date('d/m/Y')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('work_order_number')
                    ->label('N° de Orden de Trabajo')
                    ->placeholder('No definido')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                TextColumn::make('fracttal_status')
                    ->label('Estado Fracttal')
                    ->placeholder('No definido')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'Pendiente' => 'danger',
                        'En Proceso' => 'warning',
                        'Completado' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('purchase_order')
                    ->label('OC')
                    ->placeholder('No definido')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('migo_code')
                    ->label('MIGO')
                    ->placeholder('No definido')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                // Columnas adicionales del modelo
                TextColumn::make('service_days')
                    ->label('Días de Servicio')
                    ->placeholder('No definido')
                    ->suffix(' días')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('task_type')
                    ->label('Tipo de Tarea')
                    ->placeholder('No definido')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'OPEX' => 'info',
                        'CAPEX' => 'warning',
                        default => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('quote_sent_at')
                    ->label('Cotización Enviada')
                    ->placeholder('No definido')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('quote_approved_at')
                    ->label('Cotización Aprobada')
                    ->placeholder('No definido')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('wo_review_at')
                    ->label('OT en Revisión')
                    ->placeholder('No definido')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('wo_completed_at')
                    ->label('OT Finalizado')
                    ->placeholder('No definido')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('days_to_completion')
                    ->label('Días hasta Finalización')
                    ->placeholder('No definido')
                    ->suffix(' días')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filtersFormColumns(3)
            ->columnToggleFormColumns(3)

            ->filters([

                // Filtro de Cliente
                SelectFilter::make('client')
                    ->label('Cliente')
                    ->relationship('subClient.client', 'business_name')
                    ->searchable()
                    ->preload()
                    ->native(false),

                // Filtro de SubCliente (Tienda)
                SelectFilter::make('sub_client_id')
                    ->label('Tienda')
                    ->options(fn() => SubClient::query()
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray())
                    ->searchable()
                    ->preload()
                    ->native(false),

                SelectFilter::make('fracttal_status')
                    ->label('Estado Fracttal')
                    ->native(false)
                    ->options([
                        'Pendiente' => 'Pendiente',
                        'Completado' => 'Completado',
                        'En Proceso' => 'En Proceso',
                    ]),

                Filter::make('date_range')
                    ->form([
                        DatePicker::make('service_start_date')
                            ->label('Desde'),
                        DatePicker::make('serviceend_date')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['service_start_date'],
                                fn(Builder $query, $date): Builder => $query->whereDate('service_start_date', '>=', $date),
                            );
                    }),
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'Enviado' => 'Enviado',
                        'Aprobado' => 'Aprobado',
                        'En Ejecución' => 'En Ejecución',
                        'Completado' => 'Completado',
                        'Facturado' => 'Facturado',
                        'Anulado' => 'Anulado',
                    ])
                    ->searchable(),
            ])
            ->actions([

                //ACA COLOCAREMOS EL REPORTE DE ACTAS DEL PROYECTO Y REPORTE DE TRABAJO DESCARGA PDF

                ActionGroup::make([
                    // 1. ACCIÓN EDITAR
                    EditAction::make()
                        ->label('Editar Registro')
                        ->color('info'),
                    Action::make('aprobar_proyecto')
                        ->label('Marcar como Aprobado')
                        ->icon('heroicon-m-check-badge')
                        ->color('success')
                        ->visible(fn($record) => !in_array(strtolower($record->status), ['Aprobado', 'Completado']))->requiresConfirmation()
                        ->modalHeading('¿Aprobar proyecto?')
                        ->modalDescription('¿Estás seguro de que deseas marcar este proyecto como Aprobado?')
                        ->action(function ($record) {
                            $record->status = 'Aprobado';
                            $record->save();

                            Notification::make()
                                ->title('Proyecto aprobado')
                                ->success()
                                ->body('El proyecto ha sido marcado como Aprobado.')
                                ->send();
                        }),
                    Action::make('cambiar_estado')
                        ->label('Cambiar Estado')
                        ->icon('heroicon-m-arrow-path')
                        ->color('warning')
                        ->form([
                            Select::make('nuevo_estado')
                                ->label('Nuevo estado')
                                ->options([
                                    'Pendiente' => 'Pendiente',
                                    'Enviado' => 'Enviado',
                                    'Aprobado' => 'Aprobado',
                                    'En Ejecución' => 'En Ejecución',
                                    'Completado' => 'Completado',
                                    'Facturado' => 'Facturado',
                                    'Anulado' => 'Anulado',
                                ])
                                ->required()
                                ->default(fn($record) => $record->status),
                        ])
                        ->action(function (array $data, $record) {
                            $record->status = $data['nuevo_estado'];
                            $record->save();

                            Notification::make()
                                ->title('Estado actualizado')
                                ->success()
                                ->body('El estado del proyecto ha sido actualizado a: ' . $data['nuevo_estado'])
                                ->send();
                        }),

                    // 2. ACCIÓN DESCARGAR DOCUMENTOS (Lógica dinámica de tu primer bloque)
                    Action::make('descargar_documentos')
                        ->label(fn($record) => match (true) {
                            $record->compliance && $record->workReports()->exists() => 'Acta + Reportes (PDF)',
                            (bool) $record->compliance => 'Acta de conformidad',
                            $record->workReports()->exists() => 'Reportes de trabajo',
                            default => 'Sin documentos',
                        })
                        ->icon('heroicon-m-arrow-down-tray')
                        ->color('danger')
                        ->url(function ($record) {
                            $compliance = $record->compliance;
                            $hasReports = $record->workReports()->exists();

                            if ($compliance && $hasReports) {
                                return route('actas.pdf-with-reports', $compliance->id);
                            } elseif ($compliance) {
                                return route('actas.pdf', $compliance->id);
                            } elseif ($hasReports) {
                                return route('work-reports.download-multiple-pdf', $record->id);
                            }

                            return null;
                        })
                        ->visible(fn($record) => $record->compliance || $record->workReports()->exists())
                        ->openUrlInNewTab(),
                    // 3. ACCIÓN INFORME CONSOLIDADO (Tu segunda acción del primer bloque)
                    Action::make('pdf_report')
                        ->label('Informe Consolidado')
                        ->icon('heroicon-m-document-text')
                        ->color('info')
                        ->visible(fn($record): bool => $record->workReports()->exists())
                        ->url(fn($record): string => route('project.consolidated-report.pdf', [
                            'project' => $record->id,
                            'inline' => '1'
                        ]))
                        ->openUrlInNewTab(),

                ])
                    ->icon('heroicon-m-cog-6-tooth')
                    ->button()
                    ->label('Opciones')
                    ->color('gray')
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
