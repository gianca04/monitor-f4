<?php

namespace App\Filament\Resources\QuoteWarehouses\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class QuoteWarehousesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                // ── Identificación ──────────────────────────────────
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('gray')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('quote.project.service_code')
                    ->label('Correlativo')
                    ->placeholder('—')
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->extraAttributes(['class' => 'font-bold']),

                TextColumn::make('quote.project.name')
                    ->label('Proyecto')
                    ->placeholder('—')
                    ->searchable()
                    ->limit(35)
                    ->tooltip(fn($record) => $record->quote?->project?->name)
                    ->sortable(
                        query: fn(Builder $query, string $direction) => $query
                            ->join('quotes', 'quote_warehouse.quote_id', '=', 'quotes.id')
                            ->join('projects', 'quotes.project_id', '=', 'projects.id')
                            ->orderBy('projects.name', $direction)
                            ->select('quote_warehouse.*')
                    ),

                // ── Cliente / Tienda ────────────────────────────────
                TextColumn::make('quote.subClient.client.business_name')
                    ->label('Cliente')
                    ->placeholder('—')
                    ->searchable()
                    ->limit(25)
                    ->tooltip(fn($record) => $record->quote?->subClient?->client?->business_name)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('quote.subClient.name')
                    ->label('Tienda')
                    ->placeholder('—')
                    ->searchable()
                    ->limit(25)
                    ->tooltip(fn($record) => $record->quote?->subClient?->name)
                    ->toggleable(),

                // ── Estado ──────────────────────────────────────────
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'Pendiente' => 'warning',
                        'Parcial'   => 'info',
                        'Atendido'  => 'success',
                        default     => 'gray',
                    })
                    ->icon(fn(?string $state): ?string => match ($state) {
                        'Pendiente' => 'heroicon-m-clock',
                        'Parcial'   => 'heroicon-m-arrow-path',
                        'Atendido'  => 'heroicon-m-check-circle',
                        default     => null,
                    })
                    ->searchable()
                    ->sortable(),

                // ── Progreso (calculado) ────────────────────────────
                TextColumn::make('progress')
                    ->label('Progreso')
                    ->state(fn($record): string => $record->calculateProgress() . '%')
                    ->badge()
                    ->color(fn($record): string => match (true) {
                        $record->calculateProgress() >= 100 => 'success',
                        $record->calculateProgress() >= 50  => 'info',
                        $record->calculateProgress() > 0    => 'warning',
                        default                              => 'gray',
                    })
                    ->alignCenter()
                    ->sortable(
                        query: fn(Builder $query, string $direction) => $query
                            ->orderByRaw("(
                            SELECT COALESCE(SUM(qwd.attended_quantity), 0)
                            FROM quote_warehouse_details qwd
                            WHERE qwd.quote_warehouse_id = quote_warehouse.id
                        ) {$direction}")
                    ),

                // ── Costos adicionales (suma) ───────────────────────
                TextColumn::make('total_additional_cost')
                    ->label('Costos Adic.')
                    ->state(fn($record) => $record->details->sum('additional_cost'))
                    ->formatStateUsing(fn ($state) => '$' . number_format($state, 2))
                    ->placeholder('$0.00')
                    ->alignEnd()
                    ->color(fn($record) => $record->details->sum('additional_cost') > 0 ? 'warning' : 'gray')
                    ->toggleable(),

                // ── Ítems ───────────────────────────────────────────
                TextColumn::make('details_count')
                    ->label('Ítems')
                    ->counts('details')
                    ->badge()
                    ->color('gray')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                // ── Atendido por ────────────────────────────────────
                TextColumn::make('employee.name')
                    ->label('Atendido por')
                    ->placeholder('Sin asignar')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                // ── Fechas ──────────────────────────────────────────
                TextColumn::make('attended_at')
                    ->label('Fecha Atención')
                    ->placeholder('—')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),

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

            // ── Filtros ─────────────────────────────────────────────
            ->filtersFormColumns(3)
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'Pendiente' => 'Pendiente',
                        'Parcial'   => 'Parcial',
                        'Atendido'  => 'Atendido',
                    ])
                    ->native(false)
                    ->placeholder('Todos'),

                SelectFilter::make('client')
                    ->label('Cliente')
                    ->relationship('quote.subClient.client', 'business_name')
                    ->searchable()
                    ->preload()
                    ->native(false),

                SelectFilter::make('sub_client')
                    ->label('Tienda')
                    ->relationship('quote.subClient', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),
            ])

            // ── Acciones por registro ───────────────────────────────
            ->actions([
                ActionGroup::make([
                    EditAction::make()
                        ->label('Gestionar Despacho')
                        ->icon('heroicon-m-pencil-square')
                        ->color('primary'),

                    Action::make('preview')
                        ->label('Vista Previa')
                        ->icon('heroicon-m-eye')
                        ->color('gray')
                        ->url(fn($record) => route('quoteswarehouse.preview', $record->id))
                        ->openUrlInNewTab(),

                    Action::make('pdf')
                        ->label('Descargar PDF')
                        ->icon('heroicon-m-arrow-down-tray')
                        ->color('danger')
                        ->url(fn($record) => route('quoteswarehouse.pdf', $record->id))
                        ->openUrlInNewTab(),


                ])
                    ->icon('heroicon-m-cog-6-tooth')
                    ->button()
                    ->label('Opciones')
                    ->color('gray'),
            ])

            // ── Acciones masivas ────────────────────────────────────
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50]);
    }
}
