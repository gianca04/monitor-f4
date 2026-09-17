<?php

namespace App\Filament\Resources\Quotes\Tables;

use App\Models\Quote;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class QuotesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('request_number')
                    ->label('N° Cotización')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->extraAttributes(['class' => 'font-bold']),

                TextColumn::make('project.name')
                    ->label('Proyecto / Servicio')
                    ->placeholder('Sin proyecto')
                    ->searchable()
                    ->sortable()
                    ->limit(35)
                    ->tooltip(fn($record) => $record->project?->name),

                TextColumn::make('subClient.client.business_name')
                    ->label('Cliente')
                    ->placeholder('No definido')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('subClient.name')
                    ->label('Tienda / Subcliente')
                    ->placeholder('No definido')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('employee')
                    ->label('Cotizador')
                    ->placeholder('Sin asignar')
                    ->formatStateUsing(fn($record) => $record->employee
                        ? "{$record->employee->first_name} {$record->employee->last_name}"
                        : null)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('employee', function (Builder $q) use ($search) {
                            $q->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->leftJoin('employees', 'quotes.employee_id', '=', 'employees.id')
                            ->orderBy('employees.first_name', $direction)
                            ->select('quotes.*');
                    }),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->colors([
                        'warning' => 'Pendiente',
                        'info' => 'Enviado',
                        'success' => 'Aprobado',
                        'danger' => 'Anulado',
                    ])
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('Monto Total')
                    ->formatStateUsing(fn($state) => 'S/ ' . number_format((float) ($state ?? 0), 2))
                    ->alignment(Alignment::End)
                    ->extraAttributes(['class' => 'font-bold'])
                    ->sortable(),

                TextColumn::make('quoteCategory.name')
                    ->label('Categoría')
                    ->badge()
                    ->placeholder('General')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('quote_type')
                    ->label('Tipo')
                    ->badge()
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('ceco')
                    ->label('CECO')
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('energy_sci_manager')
                    ->label('Jefe Energía / SCI')
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('quote_date')
                    ->label('Fecha Cotización')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('execution_date')
                    ->label('Fecha Ejecución')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

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
                SelectFilter::make('status')
                    ->label('Estado')
                    ->native(false)
                    ->options([
                        'Pendiente' => 'Pendiente',
                        'Enviado' => 'Enviado',
                        'Aprobado' => 'Aprobado',
                        'Anulado' => 'Anulado',
                    ]),

                SelectFilter::make('client')
                    ->label('Cliente')
                    ->relationship('subClient.client', 'business_name')
                    ->searchable()
                    ->preload()
                    ->native(false),

                SelectFilter::make('sub_client_id')
                    ->label('Tienda / Subcliente')
                    ->relationship('subClient', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),

                SelectFilter::make('employee_id')
                    ->label('Cotizador')
                    ->relationship('employee', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->first_name} {$record->last_name}")
                    ->searchable()
                    ->preload()
                    ->native(false),

                SelectFilter::make('quote_category_id')
                    ->label('Categoría')
                    ->relationship('quoteCategory', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),

                Filter::make('quote_date')
                    ->label('Rango de Fecha')
                    ->form([
                        DatePicker::make('desde')
                            ->label('Fecha Desde'),
                        DatePicker::make('hasta')
                            ->label('Fecha Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['desde'], fn(Builder $q, $date) => $q->whereDate('quote_date', '>=', $date))
                            ->when($data['hasta'], fn(Builder $q, $date) => $q->whereDate('quote_date', '<=', $date));
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make()
                        ->label('Editar Cotización')
                        ->icon('heroicon-m-pencil-square')
                        ->color('primary'),

                    Action::make('preview')
                        ->label('Vista Previa')
                        ->icon('heroicon-m-eye')
                        ->color('info')
                        ->url(fn($record) => route('quotes.preview', $record->id))
                        ->openUrlInNewTab(),

                    Action::make('pdf')
                        ->label('Descargar PDF')
                        ->icon('heroicon-m-arrow-down-tray')
                        ->color('danger')
                        ->url(fn($record) => route('quotes.pdf', $record->id))
                        ->openUrlInNewTab(),

                    Action::make('excel')
                        ->label('Descargar Excel')
                        ->icon('heroicon-m-table-cells')
                        ->color('success')
                        ->url(fn($record) => route('quotes.excel', $record->id))
                        ->openUrlInNewTab(),

                    Action::make('cambiar_estado')
                        ->label('Cambiar Estado')
                        ->icon('heroicon-m-arrow-path')
                        ->color('warning')
                        ->form([
                            Select::make('nuevo_estado')
                                ->label('Nuevo Estado')
                                ->options([
                                    'Pendiente' => 'Pendiente',
                                    'Enviado' => 'Enviado',
                                    'Aprobado' => 'Aprobado',
                                    'Anulado' => 'Anulado',
                                ])
                                ->required()
                                ->default(fn($record) => $record->status),
                        ])
                        ->action(function (array $data, $record) {
                            $record->update(['status' => $data['nuevo_estado']]);

                            Notification::make()
                                ->title('Estado actualizado')
                                ->success()
                                ->body('El estado de la cotización ha sido actualizado a: ' . $data['nuevo_estado'])
                                ->send();
                        }),

                    DeleteAction::make()
                        ->label('Eliminar')
                        ->icon('heroicon-m-trash')
                        ->color('danger'),
                ])
                    ->icon('heroicon-m-cog-6-tooth')
                    ->button()
                    ->label('Opciones')
                    ->color('gray'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
