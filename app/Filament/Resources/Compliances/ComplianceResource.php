<?php

namespace App\Filament\Resources\Compliances;

use App\Filament\Resources\Compliances\Pages\CreateCompliance;
use App\Filament\Resources\Compliances\Pages\EditCompliance;
use App\Filament\Resources\Compliances\Pages\ListCompliances;
use App\Filament\Resources\Compliances\RelationManagers\WorkreportsRelationManager;
use App\Filament\Resources\Compliances\Schemas\ComplianceForm;
use App\Filament\Resources\Compliances\Tables\CompliancesTable;
use App\Models\Compliance;
use App\Models\Project;
use BackedEnum;
use Filament\Navigation\NavigationItem;
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
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        // Si el usuario tiene rol Inspector, filtrar solo los compliances
        // de proyectos donde está asignado como inspector
        if ($user && $user->hasRole('Inspector')) {
            $employeeId = $user->employee_id;

            if ($employeeId) {
                return $query->forInspector($employeeId);
            }

            // Si no tiene employee_id, no mostrar nada
            return $query->whereRaw('1 = 0');
        }

        // Para otros roles, usar la lógica existente de allowedForUser
        return $query->whereHas('project', function (Builder $q) use ($user) {
            $q->allowedForUser($user);
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
            WorkreportsRelationManager::class,
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
    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make('Orden de trabajo')
                ->icon(Heroicon::OutlinedDocumentCheck)
                ->group('Operaciones')
                ->url(static::getUrl())
                ->sort(5),
        ];
    }
}
