<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Models\Employee;
use App\Models\Project;
use App\Models\Quote;
use App\Models\Compliance;
use App\Models\Pricelist;
use App\Models\ProjectConsumption;
use App\Models\QuoteWarehouse;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UnifiedStatsWidget extends Widget
{
    protected static ?int $sort = 1;

    // Ocupar toda la fila (12 columnas en el grid de Filament)
    protected int | string | array $columnSpan = 12;

    protected string $view = 'filament.widgets.unified-stats-widget';

    public function getData(): array
    {
        // Calcular total S/. de cotizaciones aprobadas
        $totalApprovedQuotesAmount = Quote::where('status', 'Aprobado')
            ->join('quote_details', 'quotes.id', '=', 'quote_details.quote_id')
            ->sum(DB::raw('quote_details.quantity * quote_details.unit_price'));
        // Calcular proyectos con consumo hoy
        $projectsWithConsumptionToday = ProjectConsumption::whereDate('consumed_at', today())
            ->distinct('project_id')
            ->count('project_id');

        return [
            'stats' => [
                [
                    'label' => 'Clientes',
                    'value' => Client::count(),
                    'description' => 'Cantidad de clientes',
                    'icon' => 'users',
                    'color' => 'bg-blue-500',
                ],
                [
                    'label' => 'Empleados Activos',
                    'value' => Employee::where('active', true)->count(),
                    'description' => 'Cantidad de empleados activos',
                    'icon' => 'user-group',
                    'color' => 'bg-green-500',
                ],
                [
                    'label' => 'Total Proyectos',
                    'value' => Project::count(),
                    'description' => 'Proyectos registrados en el sistema',
                    'icon' => 'briefcase',
                    'color' => 'bg-indigo-500',
                ],
                [
                    'label' => 'Cotizaciones Emitidas',
                    'value' => Quote::count(),
                    'description' => 'Total de cotizaciones emitidas',
                    'icon' => 'document-text',
                    'color' => 'bg-yellow-500',
                ],
                [
                    'label' => 'Actas Generadas',
                    'value' => Compliance::count(),
                    'description' => 'Cantidad de actas generadas',
                    'icon' => 'clipboard-document-check',
                    'color' => 'bg-gray-500',
                ],
                [
                    'label' => 'Cotizaciones Aprobadas',
                    'value' => Quote::where('status', 'Aprobado')->count(),
                    'description' => 'Cotizaciones con status Aprobado',
                    'icon' => 'check-badge',
                    'color' => 'bg-emerald-500',
                ],
                [
                    'label' => 'Total S/. Cotizaciones Aprobadas',
                    'value' => 'S/. ' . number_format($totalApprovedQuotesAmount, 2),
                    'description' => '', // Cambiado: no mostrar descripción
                    'icon' => 'currency-dollar',
                    'color' => 'bg-red-500',
                ],
                [
                    'label' => 'Elementos en Preciario',
                    'value' => Pricelist::count(),
                    'description' => 'Cantidad de cosas en el Preciario',
                    'icon' => 'calculator',
                    'color' => 'bg-purple-500',
                ],
                [
                    'label' => 'Despachos Atendidos',
                    'value' => QuoteWarehouse::where('status', 'Atendido')->count(),
                    'description' => 'QuoteWarehouse con status Atendido',
                    'icon' => 'truck',
                    'color' => 'bg-cyan-500',
                ],
                [
                    'label' => 'Despachos Pendientes/Parciales',
                    'value' => QuoteWarehouse::whereIn('status', ['Parcial', 'Pendiente'])->count(),
                    'description' => 'Despachos en proceso',
                    'icon' => 'clock',
                    'color' => 'bg-orange-500',
                ],
                [
                    'label' => 'Projectos atendidos por consumo hoy',
                    'value' => $projectsWithConsumptionToday,
                    'description' => 'Proyectos únicos con actividad de consumo en el día',
                    'icon' => 'cube',
                    'color' => 'bg-orange-500',
                ],
            ],
        ];
    }
    public static function canView(): bool
    {
        $user = Auth::user();

        return $user->hasAnyRole([
            'Administrador',
            'Gerencial',
        ]);
    }
}
