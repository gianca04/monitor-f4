<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\Quotes\QuoteResource;
use App\Filament\Resources\QuoteWarehouses\QuoteWarehouseResource;
use App\Models\Client;
use App\Models\Compliance;
use App\Models\Employee;
use App\Models\Pricelist;
use App\Models\Project;
use App\Models\ProjectConsumption;
use App\Models\Quote;
use App\Models\QuoteWarehouse;
use App\Models\WorkReport;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminDashboardWidget extends Widget
{
    protected static ?int $sort = 0;
    protected int | string | array $columnSpan = 12;
    protected string $view = 'filament.widgets.admin-dashboard-widget';

    public string $activeTab = 'overview';
    public string $statusFilter = 'all';
    public string $monthFilter = '';

    // Definir las propiedades que deben ser reactivas
    protected $queryString = ['activeTab', 'statusFilter', 'monthFilter'];

    public function mount(): void
    {
        $this->monthFilter = now()->format('Y-m');
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    // Método helper para obtener año y mes del filtro
    protected function getFilterYear(): int
    {
        return (int) substr($this->monthFilter ?: now()->format('Y-m'), 0, 4);
    }

    protected function getFilterMonth(): int
    {
        return (int) substr($this->monthFilter ?: now()->format('Y-m'), 5, 2);
    }

    /**
     * Compact summary stats (only essential operational metrics)
     */
    public function getGlobalStats(): array
    {
        $totalApprovedQuotesAmount = Quote::where('status', 'Aprobado')
            ->join('quote_details', 'quotes.id', '=', 'quote_details.quote_id')
            ->sum(DB::raw('quote_details.quantity * quote_details.unit_price'));

        return [
            'active_employees' => Employee::where('active', true)->count(),
            'total_approved_amount' => $totalApprovedQuotesAmount,
            'dispatches_pending' => QuoteWarehouse::whereIn('status', ['Parcial', 'Pendiente'])->count(),
            'projects_with_consumption_today' => ProjectConsumption::whereDate('consumed_at', today())
                ->distinct('project_id')
                ->count('project_id'),
        ];
    }

    /**
     * Level 1: Urgent items needing immediate attention (with actionable URLs)
     */
    public function getUrgentItems(): array
    {
        $year = $this->getFilterYear();
        $month = $this->getFilterMonth();
        $items = [];

        // Cotizaciones pendientes por más de 7 días
        $oldPendingQuotes = Quote::where('status', 'Pendiente')
            ->where('created_at', '<', now()->subDays(7))
            ->count();
        if ($oldPendingQuotes > 0) {
            $items[] = [
                'type' => 'danger',
                'icon' => 'exclamation-triangle',
                'title' => "{$oldPendingQuotes} cotizaciones pendientes +7 días",
                'description' => 'Requieren aprobación urgente',
                'count' => $oldPendingQuotes,
                'url' => QuoteResource::getUrl('index'),
            ];
        }

        // Cotizaciones pendientes recientes (menos de 7 días)
        $recentPendingQuotes = Quote::where('status', 'Pendiente')
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
        if ($recentPendingQuotes > 0) {
            $items[] = [
                'type' => 'warning',
                'icon' => 'clock',
                'title' => "{$recentPendingQuotes} cotizaciones pendientes",
                'description' => 'Pendientes de aprobación',
                'count' => $recentPendingQuotes,
                'url' => QuoteResource::getUrl('index'),
            ];
        }

        // Despachos pendientes o parciales
        $pendingDispatches = QuoteWarehouse::whereIn('status', ['Pendiente', 'Parcial'])->count();
        if ($pendingDispatches > 0) {
            $items[] = [
                'type' => 'warning',
                'icon' => 'truck',
                'title' => "{$pendingDispatches} despachos pendientes",
                'description' => 'Pendientes de atender en almacén',
                'count' => $pendingDispatches,
                'url' => QuoteWarehouseResource::getUrl('index'),
            ];
        }

        // Proyectos sin supervisor
        $noSupervisor = Project::whereNull('supervisor_id')
            ->whereNotIn('status', ['Completado', 'Facturado', 'Anulado'])
            ->count();
        if ($noSupervisor > 0) {
            $items[] = [
                'type' => 'danger',
                'icon' => 'user-minus',
                'title' => "{$noSupervisor} proyectos sin supervisor",
                'description' => 'Requieren asignación de supervisor',
                'count' => $noSupervisor,
                'url' => ProjectResource::getUrl('index'),
            ];
        }

        return $items;
    }

    public function getOverviewStats(): array
    {
        $year = $this->getFilterYear();
        $month = $this->getFilterMonth();

        // Query base para proyectos del mes filtrado
        $projectsQuery = Project::whereYear('created_at', $year)
            ->whereMonth('created_at', $month);

        if ($this->statusFilter !== 'all') {
            $projectsQuery->where('status', $this->statusFilter);
        }

        // Query base para cotizaciones del mes filtrado
        $quotesQuery = Quote::whereYear('created_at', $year)
            ->whereMonth('created_at', $month);

        return [
            'projects_total' => (clone $projectsQuery)->count(),
            'projects_approved' => (clone $projectsQuery)->where('status', 'Aprobado')->count(),
            'projects_in_execution' => (clone $projectsQuery)->where('status', 'En Ejecución')->count(),
            'projects_completed' => (clone $projectsQuery)->where('status', 'Completado')->count(),
            'quotes_approved' => (clone $quotesQuery)->where('status', 'Aprobado')->count(),
            'quotes_pending' => (clone $quotesQuery)->whereIn('status', ['Pendiente', 'pending'])->count(),
            'compliance_count' => Compliance::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)->count(),
            'work_reports_count' => WorkReport::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)->count(),
            'warehouse_attended' => QuoteWarehouse::where('status', 'Atendido')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)->count(),
            'warehouse_pending' => QuoteWarehouse::whereIn('status', ['Pendiente', 'pending', 'Parcial'])
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)->count(),
        ];
    }

    public function getQuickStats(): array
    {
        $year = $this->getFilterYear();
        $month = $this->getFilterMonth();

        $currentDate = \Carbon\Carbon::createFromDate($year, $month, 1);
        $lastMonthDate = $currentDate->copy()->subMonth();

        $projectsQuery = Project::query();
        if ($this->statusFilter !== 'all') {
            $projectsQuery->where('status', $this->statusFilter);
        }

        $projectsThisMonth = (clone $projectsQuery)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        $projectsLastMonth = (clone $projectsQuery)
            ->whereYear('created_at', $lastMonthDate->year)
            ->whereMonth('created_at', $lastMonthDate->month)
            ->count();

        $quotesQuery = Quote::query();
        if ($this->statusFilter !== 'all') {
            $quotesQuery->where('status', $this->statusFilter);
        }

        $quotesThisMonth = (clone $quotesQuery)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        $approvedThisMonth = Quote::where('status', 'Aprobado')
            ->whereYear('updated_at', $year)
            ->whereMonth('updated_at', $month)
            ->count();

        $projectsTrend = $projectsLastMonth > 0
            ? round((($projectsThisMonth - $projectsLastMonth) / $projectsLastMonth) * 100)
            : ($projectsThisMonth > 0 ? 100 : 0);

        $pendingQuotes = Quote::where('status', 'Pendiente')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        $pendingWarehouse = QuoteWarehouse::whereIn('status', ['Pendiente', 'Parcial'])
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        // Month-over-month approved amount comparison
        $approvedAmountThisMonth = Quote::where('quotes.status', 'Aprobado')
            ->whereYear('quotes.created_at', $year)
            ->whereMonth('quotes.created_at', $month)
            ->join('quote_details', 'quotes.id', '=', 'quote_details.quote_id')
            ->sum(DB::raw('quote_details.quantity * quote_details.unit_price'));

        $approvedAmountLastMonth = Quote::where('quotes.status', 'Aprobado')
            ->whereYear('quotes.created_at', $lastMonthDate->year)
            ->whereMonth('quotes.created_at', $lastMonthDate->month)
            ->join('quote_details', 'quotes.id', '=', 'quote_details.quote_id')
            ->sum(DB::raw('quote_details.quantity * quote_details.unit_price'));

        $amountTrend = $approvedAmountLastMonth > 0
            ? round((($approvedAmountThisMonth - $approvedAmountLastMonth) / $approvedAmountLastMonth) * 100)
            : ($approvedAmountThisMonth > 0 ? 100 : 0);

        return [
            'projects_this_month' => $projectsThisMonth,
            'projects_trend' => $projectsTrend,
            'quotes_this_month' => $quotesThisMonth,
            'approved_this_month' => $approvedThisMonth,
            'pending_actions' => $pendingQuotes + $pendingWarehouse,
            'approved_amount_this_month' => $approvedAmountThisMonth,
            'approved_amount_last_month' => $approvedAmountLastMonth,
            'amount_trend' => $amountTrend,
        ];
    }

    public function getAdvancedChartData(): array
    {
        // Tendencia de proyectos últimos 12 meses desde el mes filtrado
        $baseDate = \Carbon\Carbon::createFromDate($this->getFilterYear(), $this->getFilterMonth(), 1);

        $projectsTrend = collect();
        for ($i = 11; $i >= 0; $i--) {
            $date = $baseDate->copy()->subMonths($i);

            $query = Project::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);

            if ($this->statusFilter !== 'all') {
                $query->where('status', $this->statusFilter);
            }

            $count = $query->count();
            $projectsTrend->push([
                'month' => $date->format('M'),
                'year' => $date->format('Y'),
                'count' => $count,
            ]);
        }

        // Cotizaciones por categoría del mes filtrado
        $quotesByCategory = Quote::select('quote_category_id')
            ->selectRaw('COUNT(*) as total')
            ->with('quoteCategory')
            ->whereNotNull('quote_category_id')
            ->whereYear('created_at', $this->getFilterYear())
            ->whereMonth('created_at', $this->getFilterMonth())
            ->groupBy('quote_category_id')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->quoteCategory?->name ?? 'Sin categoría',
                    'count' => $item->total,
                ];
            });

        // Eficiencia: tiempo promedio de aprobación
        $avgApprovalDays = Quote::where('status', 'Aprobado')
            ->whereNotNull('updated_at')
            ->whereYear('created_at', $this->getFilterYear())
            ->whereMonth('created_at', $this->getFilterMonth())
            ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
            ->value('avg_days') ?? 0;

        // Tasa de conversión del mes filtrado
        $totalQuotes = Quote::whereYear('created_at', $this->getFilterYear())
            ->whereMonth('created_at', $this->getFilterMonth())
            ->count();
        $approvedQuotes = Quote::where('status', 'Aprobado')
            ->whereYear('created_at', $this->getFilterYear())
            ->whereMonth('created_at', $this->getFilterMonth())
            ->count();
        $conversionRate = $totalQuotes > 0 ? round(($approvedQuotes / $totalQuotes) * 100, 1) : 0;

        return [
            'projects_trend' => $projectsTrend,
            'quotes_by_category' => $quotesByCategory,
            'avg_approval_days' => round($avgApprovalDays, 1),
            'conversion_rate' => $conversionRate,
        ];
    }

    public function getAlerts(): array
    {
        $alerts = [];
        $year = $this->getFilterYear();
        $month = $this->getFilterMonth();

        // Cotizaciones pendientes del mes filtrado por más de 7 días
        $oldpendingQuotes = Quote::where('status', 'Pendiente')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('created_at', '<', now()->subDays(7))
            ->count();
        if ($oldpendingQuotes > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'exclamation-triangle',
                'message' => "{$oldpendingQuotes} cotizaciones pendientes hace más de 7 días",
                'count' => $oldpendingQuotes,
            ];
        }

        // Despachos parciales del mes
        $partialDispatches = QuoteWarehouse::where('status', 'Parcial')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();
        if ($partialDispatches > 0) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'truck',
                'message' => "{$partialDispatches} despachos pendientes de completar",
                'count' => $partialDispatches,
            ];
        }

        // Proyectos sin supervisor del mes
        $projectsWithoutSupervisor = Project::whereNull('supervisor_id')
            ->whereNotIn('status', ['Completado', 'Facturado', 'Anulado'])
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();
        if ($projectsWithoutSupervisor > 0) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'user-minus',
                'message' => "{$projectsWithoutSupervisor} proyectos sin supervisor asignado",
                'count' => $projectsWithoutSupervisor,
            ];
        }

        return $alerts;
    }

    public function getProjectsWithFullFlow(): \Illuminate\Support\Collection
    {
        $query = Project::with([
            'quotes' => function ($q) {
                $q->with(['quoteWarehouse', 'details']);
            },
            'compliance',
            'workReports',
            'subClient.client',
            'supervisor',
        ]);

        // Aplicar filtro de estado
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Aplicar filtro de mes
        if ($this->monthFilter) {
            $query->whereYear('created_at', $this->getFilterYear())
                ->whereMonth('created_at', $this->getFilterMonth());
        }

        // Limitar a 10 proyectos más recientes
        return $query->latest()->limit(10)->get()->map(function ($project) {
            $latestQuote = $project->quotes->first();
            $warehouse = $latestQuote?->quoteWarehouse;

            // Normalizar status de proyecto
            $projectStatus = $project->status ?? 'Pendiente';
            if (strtolower($projectStatus) === 'pending') {
                $projectStatus = 'Pendiente';
            }

            // Normalizar status de cotización
            $quoteStatus = $latestQuote?->status ?? 'Sin cotización';
            if (strtolower($quoteStatus) === 'pending') {
                $quoteStatus = 'Pendiente';
            }

            // Normalizar status de almacén
            $warehouseStatus = $warehouse?->status ?? 'Sin despacho';
            if (strtolower($warehouseStatus) === 'pending') {
                $warehouseStatus = 'Pendiente';
            }

            // Determinar estado final del flujo
            $finalStatus = $this->determineFinalStatus($project, $latestQuote, $warehouse);

            return [
                'id' => $project->id,
                'name' => $project->name ?? 'Sin nombre',
                'service_code' => $project->service_code ?? '-',
                'status' => $projectStatus,
                'sub_client' => $project->subClient?->name ?? '-',
                'client' => $project->subClient?->client?->business_name ?? '-',
                'supervisor' => $project->supervisor?->short_name ?? '-',
                'quote_status' => $quoteStatus,
                'quote_total' => $latestQuote ? 'S/. ' . number_format($latestQuote->total_amount, 2) : '-',
                'has_compliance' => $project->compliance !== null,
                'compliance_state' => $project->compliance?->state ?? '-',
                'work_reports_count' => $project->workReports->count(),
                'warehouse_status' => $warehouseStatus,
                'warehouse_progress' => $warehouse?->calculateProgress() ?? 0,
                'is_complete' => $finalStatus === 'Completado',
                'final_status' => $finalStatus,
                'created_at' => $project->created_at?->format('d/m/Y') ?? '-',
            ];
        });
    }

    /**
     * Determina el estado final del proyecto basado en el flujo completo
     */
    protected function determineFinalStatus($project, $latestQuote, $warehouse): string
    {
        // Si el proyecto está marcado como Completado o Facturado, respetamos ese estado
        if (in_array($project->status, ['Completado', 'Facturado'])) {
            return $project->status;
        }

        // Si está anulado
        if ($project->status === 'Anulado') {
            return 'Anulado';
        }

        // Verificar si tiene todos los componentes completos
        $hasApprovedQuote = $latestQuote && in_array($latestQuote->status, ['Aprobado', 'aprobado']);
        $hasCompliance = $project->compliance !== null;
        $hasWorkReports = $project->workReports->count() > 0;
        $hasAttendedWarehouse = $warehouse && in_array($warehouse->status, ['Atendido', 'atendido']);

        // Si tiene todo completo
        if ($hasApprovedQuote && $hasCompliance && $hasWorkReports && $hasAttendedWarehouse) {
            return 'Completado';
        }

        // Si está en ejecución
        if ($project->status === 'En Ejecución') {
            return 'En Ejecución';
        }

        // Si tiene cotización aprobada pero no todo lo demás
        if ($hasApprovedQuote) {
            return 'En proceso';
        }

        // Si tiene cotización pendiente o enviada
        if ($latestQuote) {
            return 'Pendiente';
        }

        return 'Sin iniciar';
    }

    protected function isProjectComplete($project): bool
    {
        $latestQuote = $project->quotes->first();

        // Verificar estado del proyecto (puede ser 'Completado' o 'Facturado')
        $projectCompleted = in_array($project->status, ['Completado', 'Facturado']);

        // Verificar cotización aprobada (case insensitive)
        $quoteApproved = $latestQuote && in_array(strtolower($latestQuote->status ?? ''), ['aprobado']);

        // Verificar acta de conformidad
        $hasCompliance = $project->compliance !== null;

        // Verificar reportes de trabajo
        $hasWorkReports = $project->workReports->count() > 0;

        // Verificar almacén atendido (case insensitive)
        $warehouseAttended = $latestQuote?->quoteWarehouse
            && in_array(strtolower($latestQuote->quoteWarehouse->status ?? ''), ['atendido']);

        return $projectCompleted || ($quoteApproved && $hasCompliance && $hasWorkReports && $warehouseAttended);
    }

    public function getCompletedProjectsCount(): int
    {
        $query = Project::where('status', 'Completado')
            ->whereHas('quotes', function ($q) {
                $q->where('status', 'Aprobado')
                    ->whereHas('quoteWarehouse', function ($w) {
                        $w->where('status', 'Atendido');
                    });
            })
            ->whereHas('compliance')
            ->whereHas('workReports');

        // Aplicar filtro de mes
        if ($this->monthFilter) {
            $query->whereYear('created_at', $this->getFilterYear())
                ->whereMonth('created_at', $this->getFilterMonth());
        }

        return $query->count();
    }

    public function getChartData(): array
    {
        $year = $this->getFilterYear();
        $month = $this->getFilterMonth();

        // Proyectos por estado del mes filtrado
        $projectsQuery = Project::whereYear('created_at', $year)
            ->whereMonth('created_at', $month);

        if ($this->statusFilter !== 'all') {
            $projectsQuery->where('status', $this->statusFilter);
        }

        $projectsByStatus = $projectsQuery
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Unificar pending y Pendiente
        if (isset($projectsByStatus['pending'])) {
            $projectsByStatus['Pendiente'] = ($projectsByStatus['Pendiente'] ?? 0) + $projectsByStatus['pending'];
            unset($projectsByStatus['pending']);
        }

        // Cotizaciones por mes (últimos 6 meses desde el mes filtrado)
        $baseDate = \Carbon\Carbon::createFromDate($year, $month, 1);
        $quotesByMonth = collect();

        for ($i = 5; $i >= 0; $i--) {
            $date = $baseDate->copy()->subMonths($i);
            $monthKey = $date->format('Y-m');

            $total = Quote::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $approved = Quote::where('status', 'Aprobado')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $quotesByMonth->push((object)[
                'month' => $monthKey,
                'total' => $total,
                'approved' => $approved,
            ]);
        }

        // Estado de almacén del mes filtrado
        $warehouseStats = QuoteWarehouse::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->select(
                DB::raw("CASE WHEN status IN ('pending', 'Pendiente') THEN 'Pendiente' ELSE status END as status_normalized"),
                DB::raw('count(*) as total')
            )
            ->groupBy('status_normalized')
            ->pluck('total', 'status_normalized')
            ->toArray();

        return [
            'projects_by_status' => $projectsByStatus,
            'quotes_by_month' => $quotesByMonth,
            'warehouse_stats' => $warehouseStats,
        ];
    }

    public function getRecentActivity(): \Illuminate\Support\Collection
    {
        $year = $this->getFilterYear();
        $month = $this->getFilterMonth();
        $activities = collect();

        // Limitar a 3 por tipo para un total máximo de ~10-12
        Quote::where('status', 'Aprobado')
            ->whereNotNull('updated_at')
            ->whereYear('updated_at', $year)
            ->whereMonth('updated_at', $month)
            ->with('employee')
            ->latest('updated_at')
            ->limit(3)
            ->get()
            ->each(function ($quote) use ($activities) {
                $activities->push([
                    'type' => 'quote_approved',
                    'icon' => 'check-circle',
                    'color' => 'green',
                    'message' => "Cotización {$quote->request_number} aprobada",
                    'employee' => $quote->employee?->short_name ?? 'Sistema',
                    'date' => $quote->updated_at,
                ]);
            });

        Compliance::whereNotNull('created_at')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->with('project.supervisor')
            ->latest()
            ->limit(3)
            ->get()
            ->each(function ($compliance) use ($activities) {
                $activities->push([
                    'type' => 'compliance_created',
                    'icon' => 'clipboard-document-check',
                    'color' => 'blue',
                    'message' => "Acta generada para proyecto #{$compliance->project_id}",
                    'employee' => $compliance->project?->supervisor?->short_name ?? 'Sistema',
                    'date' => $compliance->created_at,
                ]);
            });

        QuoteWarehouse::where('status', 'Atendido')
            ->whereNotNull('attended_at')
            ->whereYear('attended_at', $year)
            ->whereMonth('attended_at', $month)
            ->with('employee')
            ->latest('attended_at')
            ->limit(2)
            ->get()
            ->each(function ($warehouse) use ($activities) {
                $activities->push([
                    'type' => 'warehouse_attended',
                    'icon' => 'truck',
                    'color' => 'purple',
                    'message' => "Despacho atendido para cotización #{$warehouse->quote_id}",
                    'employee' => $warehouse->employee?->name ?? 'Almacén',
                    'date' => $warehouse->attended_at,
                ]);
            });

        WorkReport::whereNotNull('created_at')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->with('employee')
            ->latest()
            ->limit(2)
            ->get()
            ->each(function ($report) use ($activities) {
                $activities->push([
                    'type' => 'work_report',
                    'icon' => 'document-text',
                    'color' => 'yellow',
                    'message' => "Reporte de trabajo: {$report->name}",
                    'employee' => $report->employee?->short_name ?? 'Sistema',
                    'date' => $report->created_at,
                ]);
            });

        // Ordenar por fecha y limitar a 10 registros máximo
        return $activities
            ->filter(fn($a) => $a['date'] !== null)
            ->sortByDesc('date')
            ->take(10)
            ->values();
    }

    public function getApprovedQuotesTimeline(): \Illuminate\Support\Collection
    {
        $year = $this->getFilterYear();
        $month = $this->getFilterMonth();

        return Quote::where('status', 'Aprobado')
            ->whereNotNull('quote_date')
            ->whereYear('quote_date', $year)
            ->whereMonth('quote_date', $month)
            ->with(['project', 'subClient', 'employee', 'details'])
            ->orderBy('quote_date', 'asc')
            ->limit(30)
            ->get()
            ->map(function ($quote) {
                return [
                    'id' => $quote->id,
                    'request_number' => $quote->request_number ?? 'S/N',
                    'project_name' => $quote->project?->name ?? 'Sin proyecto',
                    'sub_client' => $quote->subClient?->name ?? '-',
                    'employee' => $quote->employee?->short_name ?? '-',
                    'quote_date' => $quote->quote_date,
                    'total_amount' => $quote->total_amount ?? 0,
                    'formatted_date' => $quote->quote_date?->format('d/m/Y') ?? '-',
                    'month_year' => $quote->quote_date?->format('M Y') ?? '-',
                ];
            });
    }

    public function getApprovedQuotesByMonth(): array
    {
        $baseDate = \Carbon\Carbon::createFromDate($this->getFilterYear(), $this->getFilterMonth(), 1);
        $data = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = $baseDate->copy()->subMonths($i);

            $quotes = Quote::where('status', 'Aprobado')
                ->whereYear('quote_date', $date->year)
                ->whereMonth('quote_date', $date->month)
                ->get();

            $data[] = [
                'month' => $date->format('M'),
                'month_year' => $date->format('M Y'),
                'full_month' => $date->format('F Y'),
                'count' => $quotes->count(),
                'total_amount' => $quotes->sum('total_amount'),
            ];
        }

        return $data;
    }

    /**
     * Obtiene datos para el gráfico circular de proyectos por estado
     */
    public function getProjectsByStatusPieChart(): array
    {
        $year = $this->getFilterYear();
        $month = $this->getFilterMonth();

        $statuses = [
            'Pendiente' => ['color' => '#F59E0B', 'label' => 'Pendiente'],
            'Enviado' => ['color' => '#3B82F6', 'label' => 'Enviado'],
            'Aprobado' => ['color' => '#10B981', 'label' => 'Aprobado'],
            'En Ejecución' => ['color' => '#6366F1', 'label' => 'En Ejecución'],
            'Completado' => ['color' => '#047857', 'label' => 'Completado'],
            'Facturado' => ['color' => '#1E3A8A', 'label' => 'Facturado'],
            'Anulado' => ['color' => '#9CA3AF', 'label' => 'Anulado'],
        ];

        $data = [];
        $total = 0;

        foreach ($statuses as $status => $config) {
            $query = Project::whereYear('created_at', $year)
                ->whereMonth('created_at', $month);

            if ($status === 'Pendiente') {
                $count = (clone $query)->whereIn('status', ['Pendiente', 'pending'])->count();
            } else {
                $count = (clone $query)->where('status', $status)->count();
            }

            if ($count > 0) {
                $data[] = [
                    'status' => $config['label'],
                    'count' => $count,
                    'color' => $config['color'],
                ];
                $total += $count;
            }
        }

        // Calcular porcentajes
        foreach ($data as &$item) {
            $item['percentage'] = $total > 0 ? round(($item['count'] / $total) * 100, 1) : 0;
        }

        return [
            'data' => $data,
            'total' => $total,
        ];
    }

    /**
     * Obtiene el proyecto más costoso del mes
     */
    public function getMostExpensiveProject(): ?array
    {
        $year = $this->getFilterYear();
        $month = $this->getFilterMonth();

        $project = Project::with(['quotes.details', 'subClient.client'])
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get()
            ->map(function ($project) {
                $totalAmount = $project->quotes->sum(function ($quote) {
                    return $quote->details->sum(function ($detail) {
                        return $detail->subtotal ?? ($detail->quantity * $detail->unit_price);
                    });
                });

                return [
                    'id' => $project->id,
                    'name' => $project->name ?? 'Sin nombre',
                    'service_code' => $project->service_code ?? '-',
                    'client' => $project->subClient?->client?->business_name ?? '-',
                    'sub_client' => $project->subClient?->name ?? '-',
                    'total_amount' => $totalAmount,
                    'status' => $project->status,
                    'created_at' => $project->created_at?->format('d/m/Y'),
                ];
            })
            ->sortByDesc('total_amount')
            ->first();

        return $project;
    }

    /**
     * Obtiene gastos mensuales (últimos 12 meses)
     */
    public function getMonthlyExpenses(): array
    {
        $baseDate = \Carbon\Carbon::createFromDate($this->getFilterYear(), $this->getFilterMonth(), 1);
        $data = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = $baseDate->copy()->subMonths($i);

            // Obtener todas las cotizaciones aprobadas del mes
            $monthlyTotal = Quote::where('status', 'Aprobado')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->with('details')
                ->get()
                ->sum(function ($quote) {
                    return $quote->details->sum(function ($detail) {
                        return $detail->subtotal ?? ($detail->quantity * $detail->unit_price);
                    });
                });

            // Contar proyectos del mes - CORREGIDO: usar $date->year y $date->month
            $projectsCount = Project::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $data[] = [
                'month' => $date->format('M'),
                'month_year' => $date->format('M Y'),
                'full_date' => $date->format('Y-m'),
                'total' => $monthlyTotal,
                'projects_count' => $projectsCount,
                'is_current' => $i === 0,
            ];
        }

        return $data;
    }

    /**
     * Calcula estadísticas de gastos mensuales
     */
    public function getMonthlyExpensesStats(array $monthlyExpenses): array
    {
        $collection = collect($monthlyExpenses);

        // Total acumulado (suma de todos los meses)
        $totalExpenses = $collection->sum('total');

        // Meses con datos (meses que tienen al menos algún gasto)
        $monthsWithData = $collection->filter(fn($m) => $m['total'] > 0);
        $monthsWithDataCount = $monthsWithData->count();

        // Promedio mensual (solo de meses con datos, no de los 12)
        $averageMonthly = $monthsWithDataCount > 0
            ? $totalExpenses / $monthsWithDataCount
            : 0;

        // Mes más alto
        $maxExpense = $collection->max('total') ?: 0;

        // Mes con mayor gasto (nombre)
        $maxMonth = $collection->sortByDesc('total')->first();
        $maxMonthName = $maxMonth ? $maxMonth['month_year'] : '-';

        // Mes más bajo (excluyendo ceros)
        $minExpense = $monthsWithData->min('total') ?: 0;

        return [
            'total_accumulated' => $totalExpenses,
            'average_monthly' => $averageMonthly,
            'max_expense' => $maxExpense,
            'max_month_name' => $maxMonthName,
            'min_expense' => $minExpense,
            'months_with_data' => $monthsWithDataCount,
            'total_months' => 12,
        ];
    }

    /**
     * Obtiene el top 5 de proyectos más costosos del mes
     */
    public function getTopExpensiveProjects(): \Illuminate\Support\Collection
    {
        $year = $this->getFilterYear();
        $month = $this->getFilterMonth();

        return Project::with(['quotes.details', 'subClient.client', 'supervisor'])
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get()
            ->map(function ($project) {
                $totalAmount = $project->quotes->sum(function ($quote) {
                    return $quote->details->sum(function ($detail) {
                        return $detail->subtotal ?? ($detail->quantity * $detail->unit_price);
                    });
                });

                return [
                    'id' => $project->id,
                    'name' => $project->name ?? 'Sin nombre',
                    'service_code' => $project->service_code ?? '-',
                    'client' => $project->subClient?->client?->business_name ?? '-',
                    'sub_client' => $project->subClient?->name ?? '-',
                    'supervisor' => $project->supervisor?->short_name ?? '-',
                    'total_amount' => $totalAmount,
                    'status' => $project->status,
                ];
            })
            ->filter(fn($p) => $p['total_amount'] > 0)
            ->sortByDesc('total_amount')
            ->take(5)
            ->values();
    }

    public static function canView(): bool
    {
        $user = Auth::user();
        return $user && $user->hasAnyRole(['Administrador', 'Gerencial']);
    }
}
