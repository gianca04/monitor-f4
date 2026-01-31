<?php

namespace App\Filament\Widgets;

use App\Models\Quote;
use Filament\Widgets\ChartWidget;

class ApprovedQuotesBarChartWidget extends ChartWidget
{
    // ELIMINA 'static' de aquí:
    protected ?string $heading = 'Cotizaciones Aprobadas por Mes';

    // Para que aparezca debajo y pequeño:
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 6;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getFilters(): ?array
    {
        return [
            '01' => 'Enero',
            '02' => 'Febrero',
            '03' => 'Marzo',
            '04' => 'Abril',
            '05' => 'Mayo',
            '06' => 'Junio',
            '07' => 'Julio',
            '08' => 'Agosto',
            '09' => 'Septiembre',
            '10' => 'Octubre',
            '11' => 'Noviembre',
            '12' => 'Diciembre',
        ];
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter ?? now()->format('m');
        $year = now()->year;

        $quotes = Quote::where('status', 'Aprobado')
            ->whereYear('quote_date', $year)
            ->whereMonth('quote_date', $activeFilter)
            ->get();

        $labels = [];
        $data = [];

        foreach ($quotes as $quote) {
            $labels[] = $quote->request_number ?? ('Cot ' . $quote->id);
            $data[] = $quote->total_amount ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Subtotal (S/.)',
                    'data' => $data,
                    'backgroundColor' => '#10B981',
                ],
            ],
            'labels' => $labels,
        ];
    }
    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => "function(value) { return 'S/. ' + value; }", // Formato de moneda en el eje
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }
}
