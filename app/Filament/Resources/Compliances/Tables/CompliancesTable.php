<?php

namespace App\Filament\Resources\Compliances\Tables;

use App\Models\Compliance;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CompliancesTable
{

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('project.service_code')
                    ->label('Código de Servicio')
                    ->placeholder('No definido')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('project.name')
                    ->label('Proyecto')
                    ->placeholder('No definido')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-o-briefcase')
                    ->wrap()
                    ->limit(30)
                    ->tooltip(fn($record) => $record->project?->name),

                TextColumn::make('project.subClient.name')
                    ->label('Tienda')
                    ->placeholder('No definido')
                    ->searchable()
                    ->icon('heroicon-o-building-office')
                    ->toggleable()
                    ->limit(25),

                TextColumn::make('project.service_start_date')
                    ->label('Inicio')
                    ->placeholder('No definido')
                    ->date('d/m/Y')
                    ->icon('heroicon-o-calendar')
                    ->sortable()
                    ->toggleable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('project.end_date')
                    ->label('Fin')
                    ->placeholder('No definido')
                    ->date('d/m/Y')
                    ->icon('heroicon-o-calendar-days')
                    ->sortable()
                    ->toggleable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('fullname_cliente')
                    ->label('Responsable')
                    ->placeholder('No definido')
                    ->searchable()
                    ->icon('heroicon-o-user')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('state')
                    ->label('Estado')
                    ->placeholder('No definido')
                    ->sortable()
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'En Ejecución' => 'primary',
                        'Completado'   => 'gray',
                        default        => 'secondary',
                    }),

                TextColumn::make('assets')
                    ->label('Activos Seleccionados')
                    ->html()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(function ($record) {
                        // 1. Obtenemos el dato crudo (la lógica que te funcionó)
                        $rawAttribute = $record->getAttributes()['assets'] ?? null;

                        if (is_null($rawAttribute)) return '---';

                        // 2. Decodificamos el JSON
                        $assets = is_string($rawAttribute) ? json_decode($rawAttribute, true) : $rawAttribute;

                        if (!is_array($assets)) return '---';

                        // 3. Tu Diccionario de nombres
                        $labels = [
                            'tablero_autosoportado' => 'Tablero Autosoportado',
                            'tablero_adosados'      => 'Tablero Adosados',
                            'banco_condensadores'   => 'Banco de Condensadores',
                            'pozos_baja_tension'    => 'Pozos Tierra (BT)',
                            'pozos_media_tension'   => 'Pozos Tierra (MT)',
                        ];

                        // 4. Filtramos solo los TRUE y aplicamos el diccionario
                        return collect($assets)
                            ->filter(function ($item) {
                                // Filtramos por el booleano 'selected'
                                return isset($item['selected']) && ($item['selected'] === true || $item['selected'] === "true");
                            })
                            ->map(function ($item, $key) use ($labels) {
                                // Buscamos el nombre en el diccionario o limpiamos la key si no existe
                                $nombreLimpio = $labels[$key] ?? str($key)->replace('_', ' ')->title();
                                $cantidad = $item['quantity'] ?? 0;

                                // Formato final para la fila
                                return "<strong>{$cantidad}</strong> x {$nombreLimpio}";
                            })
                            ->join('<br>'); // Salto de línea para que se vea como lista
                    }),

                IconColumn::make('client_signature')
                    ->label('Firma Cliente')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->toggleable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('employee_signature')
                    ->label('Firma Empleado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->toggleable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])

            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->label('Editar Registro')
                        ->color('info'), // Azul para edición es estándar

                    // Acción Excel
                    Action::make('downloadExcel')
                        ->label('Exportar a Excel')
                        ->icon('heroicon-m-table-cells') // Icono más específico de Excel
                        ->color('success')
                        ->visible(false)
                        ->action(function (Compliance $record) {
                            Notification::make()
                                ->title('Preparando Excel')
                                ->body('La descarga comenzará en un momento.')
                                ->success()
                                ->send();
                        })
                        ->url(fn(Compliance $record) => route('actas.excel', $record->id))
                        ->openUrlInNewTab(),

                    // Acción PDF
                    Action::make('downloadPdfOrWithReports')
                        ->label(
                            fn(Compliance $record) =>
                            $record->workReports()->count() > 0
                                ? 'Acta + Reportes PDF'
                                : 'Descargar Acta PDF'
                        )
                        ->icon(
                            fn(Compliance $record) =>
                            $record->workReports()->count() > 0
                                ? 'heroicon-m-document-arrow-down'
                                : 'heroicon-m-arrow-down-tray'
                        )
                        ->color(
                            fn(Compliance $record) =>
                            $record->workReports()->count() > 0
                                ? 'primary'
                                : 'danger'
                        )
                        ->url(
                            fn(Compliance $record) =>
                            $record->workReports()->count() > 0
                                ? route('actas.pdf-with-reports', $record->id)
                                : route('actas.pdf', $record->id)
                        )
                        ->openUrlInNewTab(),
                    // Acción Vista Previa
                    Action::make('previewActaPdf')
                        ->label('Vista Rápida')
                        ->icon('heroicon-m-magnifying-glass-circle')
                        ->color('gray')
                        ->url(fn(Compliance $record) => route('actas.preview', $record->id))
                        ->openUrlInNewTab(),
                ])
                    ->icon('heroicon-m-cog-6-tooth') // Cambiado a un engranaje (ajustes/acciones)
                    ->button() // Esto lo convierte en un botón con texto en lugar de solo iconos
                    ->label('Opciones')
                    ->color('gray')
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Eliminar Actas Seleccionadas')
                        ->modalDescription('¿Está seguro de eliminar las actas seleccionadas? Esta acción no se puede deshacer.')
                        ->modalSubmitActionLabel('Sí, eliminar'),
                ]),
            ]);
    }
}
