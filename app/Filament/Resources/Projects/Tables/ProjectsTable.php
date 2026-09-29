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
use Filament\Tables\Columns\IconColumn;
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
                    ->badge()
                    ->searchable()
                    ->extraAttributes(['class' => 'font-bold'])
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('requested_at')
                    ->label('Fecha de solicitud')
                    ->placeholder('No definido')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('name')
                    ->label('Descripción')
                    ->searchable()
                    ->alignJustify()
                    ->wrap()
                    ->extraAttributes(['class' => 'font-bold'])
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('subClient.name')
                    ->label('Centro de costos')
                    ->placeholder('No definido')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('amount')
                    ->label('Monto')
                    ->placeholder('No definido')
                    ->prefix('S/ ')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->select('projects.*')
                            ->leftJoin('quotes', function ($join) {
                                $join->on('quotes.project_id', '=', 'projects.id')
                                    ->whereRaw('quotes.id = (select max(q2.id) from quotes q2 where q2.project_id = projects.id)');
                            })
                            ->orderBy('quotes.amount', $direction);
                    })
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('supervisor.short_name')
                    ->label('Sup. Seguimiento')
                    ->placeholder('No definido')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('supervisor', function (Builder $q) use ($search) {
                            $q->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('document_number', 'like', "%{$search}%");
                        });
                    })
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('task_type')
                    ->label('Tarea')
                    ->placeholder('No definido')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'OPEX' => 'info',
                        'CAPEX' => 'warning',
                        default => 'gray',
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                IconColumn::make('has_quote')
                    ->label('Cot')
                    ->tooltip('Tiene cotización')
                    ->boolean()
                    ->getStateUsing(fn($record): bool => $record->has_quote === 'SI')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: false),

                IconColumn::make('has_report')
                    ->label('Inf')
                    ->tooltip('Tiene informe')
                    ->boolean()
                    ->getStateUsing(fn($record): bool => $record->has_report === 'SI')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: false),

                IconColumn::make('has_compliance')
                    ->label('Ac')
                    ->tooltip('Tiene acta de conformidad')
                    ->boolean()
                    ->getStateUsing(fn($record): bool => $record->hasCompliance())
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('status')
                    ->label('Estado de invitación')
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
                        'Rechazado', 'Anulado' => 'danger',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
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

                SelectFilter::make('service_type')
                    ->label('Tipo de Servicio')
                    ->native(false)
                    ->options([
                        'Correctivo' => 'Correctivo',
                        'Emergencia' => 'Emergencia',
                        'ITSE' => 'ITSE',
                        'Preventivo' => 'Preventivo',
                    ]),

                SelectFilter::make('fracttal_status')
                    ->label('Estado Fracttal')
                    ->native(false)
                    ->options([
                        'Sin OT' => 'Sin OT',
                        'En Proceso' => 'En Proceso',
                        'En Revisión' => 'En Revisión',
                        'Finalizado' => 'Finalizado',
                        'Cancelada' => 'Cancelada',
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
