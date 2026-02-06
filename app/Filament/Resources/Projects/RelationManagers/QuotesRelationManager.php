<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Filament\Resources\Quotes\QuoteResource;
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
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuotesRelationManager extends RelationManager
{
    protected static string $relationship = 'quotes';
    protected static ?string $title = 'Cotizaciones del Proyecto';
    protected static ?string $pluralModelLabel = 'Cotizaciones';
    protected static ?string $modelLabel = 'Cotización';
    protected static ?string $recordTitleAttribute = 'request_number';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('request_number')
            ->columns([
                TextColumn::make('request_number')->label('ID Cotización'),
                TextColumn::make('service_name')
                    ->label('Servicio')
                    ->getStateUsing(fn($record) => $record->project->name ?? ''),
                TextColumn::make('N° Solicitud')
                    ->label('N° Solicitud')
                    ->getStateUsing(fn($record) => $record->project->request_number ?? ''),
                TextColumn::make('status')->label('Estado'),
                TextColumn::make('quote_date')->label('Fecha Cotización')->date('d/m/Y'),
                SelectColumn::make('status')
                    ->label('Estado')
                    ->options([
                        'Pendiente' => 'Pendiente',
                        'Enviado' => 'Enviado',
                        'Aprobado' => 'Aprobado',
                        'Anulado' => 'Anulado',
                    ])
                    ->updateStateUsing(function ($state, $record, $livewire) {
                        // 1. Guardamos el nuevo estado en el registro de la tabla
                        $record->update(['status' => $state]);

                        // 2. Emitimos el evento al formulario padre
                        $livewire->dispatch('update-parent-form');
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->url(fn() => QuoteResource::getUrl('create', [
                        'project_id' => $this->getOwnerRecord()->id,
                        'sub_client_id' => $this->getOwnerRecord()->sub_client_id,
                        'service_code' => $this->getOwnerRecord()->service_code,
                        'name' => $this->getOwnerRecord()->name,
                    ])),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('preview')
                        ->label('Previsualizar')
                        ->icon('heroicon-o-eye')
                        ->url(fn($record) => url("/quotes/{$record->id}/preview"))
                        ->openUrlInNewTab(),
                    Action::make('edit')
                        ->label('Editar')
                        ->icon('heroicon-o-pencil-square')
                        ->url(fn($record) => QuoteResource::getUrl('edit', ['record' => $record->id]))
                        ->openUrlInNewTab(),
                    Action::make('export_pdf')
                        ->label(label: 'Descargar PDF')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->url(fn($record) => url("/quotes/{$record->id}/pdf"))
                        ->openUrlInNewTab(),
                    Action::make('export_excel')
                        ->label('Exportar Excel')
                        ->icon('heroicon-o-document-arrow-down')
                        ->url(fn($record) => url("/quotes/{$record->id}/excel"))
                        ->openUrlInNewTab(),
                ]),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
