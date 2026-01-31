<?php

namespace App\Filament\Resources\WorkReports\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class WorkReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('employee_id')
                    ->required()
                    ->numeric(),
                TextInput::make('project_id')
                    ->required()
                    ->numeric(),
                TextInput::make('compliance_id')
                    ->numeric()
                    ->default(null),
                TextInput::make('name')
                    ->required(),
                Textarea::make('work_to_do')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('supervisor_signature')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('manager_signature')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('suggestions')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('tools')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('conclusions')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('personnel')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('materials')
                    ->default(null)
                    ->columnSpanFull(),
                TimePicker::make('start_time'),
                TimePicker::make('end_time'),
                DatePicker::make('report_date'),
            ]);
    }
}
