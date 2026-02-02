<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Widgets\ChartWidget;

class ProjectStatusPieChartWidget extends ChartWidget
{
    protected ?string $heading = 'Proyectos por estado';
    protected static ?int $sort = 2;
    protected ?string $maxHeight = '200px';
    protected int | string | array $columnSpan = 6;
    protected function getType(): string
    {
        return 'pie';
    }

    protected function getData(): array
    {
        $statuses = [
            'Pendiente' => 'Pendiente',
            'Enviada' => 'Enviada',
            'Aprobado' => 'Aprobado',
            'En Ejecución' => 'En Ejecución',
            'Completado' => 'Completado',
            'Facturado' => 'Facturado',
            'Anulado' => 'Anulado',
        ];

        $data = [];
        $labels = [];
        $backgroundColor = [
            '#F59E0B', // Pendiente
            '#3B82F6', // Enviada
            '#10B981', // Aprobado
            '#6366F1', // En Ejecución
            '#047857', // Completado
            '#1E3A8A', // Facturado
            '#9CA3AF', // Anulado
        ];


        foreach ($statuses as $status => $label) {
            if ($status === 'Pendiente') {
                $count = Project::whereIn('status', ['Pendiente', 'pending'])->count();
            } else {
                $count = Project::where('status', $status)->count();
            }
            $data[] = $count;
            $labels[] = $label;
        }

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $backgroundColor,
                ],
            ],
            'labels' => $labels,
        ];
    }
    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false, // Cambiado a true para evitar deformaciones
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'right', // Leyenda derecha para dar más espacio al círculo
                    'labels' => [
                        'boxWidth' => 12,
                        'usePointStyle' => true, // Círculos en lugar de cuadrados para una leyenda más limpia
                    ],
                ],
            ],
        ];
    }
}
