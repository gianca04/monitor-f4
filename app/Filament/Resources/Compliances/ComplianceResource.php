<?php

namespace App\Filament\Resources\Compliances;

use App\Filament\Resources\Compliances\Pages\CreateCompliance;
use App\Filament\Resources\Compliances\Pages\EditCompliance;
use App\Filament\Resources\Compliances\Pages\ListCompliances;
use App\Filament\Resources\Compliances\Schemas\ComplianceForm;
use App\Filament\Resources\Compliances\Tables\CompliancesTable;
use App\Models\Compliance;
use App\Models\Project;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Ramsey\Collection\Set;

class ComplianceResource extends Resource
{
    protected static ?string $model = Compliance::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCheck;
    protected static ?string $navigationLabel = 'Orden de trabajo';
    protected static ?string $pluralModelLabel = 'Órdenes de trabajo';
    protected static ?string $modelLabel = 'Orden de trabajo';
    public static function getEloquentQuery(): Builder
    {
        // 1. Obtenemos la consulta base de Compliance
        $query = parent::getEloquentQuery();

        // 2. Filtramos usando una subconsulta en la relación 'project'
        // Esto significa: "Traeme las actas DONDE el proyecto asociado
        // cumpla con las reglas de 'AllowedForUser'".
        return $query->whereHas('project', function (Builder $projectQuery) {
            $projectQuery->allowedForUser(Auth::user());
        });
    }
    public static function form(Schema $schema): Schema
    {
        return ComplianceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompliancesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCompliances::route('/'),
            'create' => CreateCompliance::route('/create'),
            'edit' => EditCompliance::route('/{record}/edit'),
        ];
    }
}
