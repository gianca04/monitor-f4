<?php

namespace App\Filament\Resources\Quotes\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class QuoteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('project_id')
                    ->relationship('project', 'name')
                    ->default(null),
                TextInput::make('request_number')
                    ->default(null),
                Select::make('employee_id')
                    ->relationship('employee', 'id')
                    ->default(null),
                Select::make('sub_client_id')
                    ->relationship('subClient', 'name')
                    ->default(null),
                Select::make('quote_category_id')
                    ->relationship('quoteCategory', 'name')
                    ->default(null),
                TextInput::make('energy_sci_manager')
                    ->default(null),
                TextInput::make('ceco')
                    ->default(null),
                Select::make('status')
                    ->options([
            'Pendiente' => 'Pendiente',
            'Enviado' => 'Enviado',
            'Aprobado' => 'Aprobado',
            'Anulado' => 'Anulado',
        ])
                    ->required(),
                DatePicker::make('quote_date'),
                DatePicker::make('execution_date'),
            ]);
    }
}
