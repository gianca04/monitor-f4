<?php

namespace App\Services;

use App\Models\VisitReport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class VisitReportEvidencePdfService
{
    /**
     * Configuración por defecto para PDFs
     */
    private const PDF_OPTIONS = [
        'defaultFont' => 'DejaVu Sans',
        'isHtml5ParserEnabled' => true,
        'isPhpEnabled' => true,
        'dpi' => 150,
        'defaultPaperSize' => 'a4',
        'enable_php' => true,
        'enable_javascript' => false,
        'enable_remote' => false,
        'enable_html5_parser' => true,
    ];

    /**
     * Genera un PDF de forma síncrona
     */
    public function generateSync(int $visitReportId, array $options = []): \Barryvdh\DomPDF\PDF
    {
        $visitReport = $this->getVisitReportWithRelations($visitReportId);
        $this->validateVisitReportData($visitReport);

        $data = $this->prepareViewData($visitReport);

        return $this->createPdf($data, $options);
    }

    /**
     * Genera un PDF de forma asíncrona usando Jobs
     */
    public function generateAsync(int $visitReportId, ?string $userEmail = null, bool $shouldEmail = false): void
    {
        Log::info('Generación asíncrona de Informe de Evidencias de Visita solicitada', [
            'visit_report_id' => $visitReportId,
            'user_email' => $userEmail,
            'should_email' => $shouldEmail,
        ]);
    }

    /**
     * Obtiene el VisitReport con todas sus relaciones necesarias
     */
    public function getVisitReportWithRelations(int $visitReportId): VisitReport
    {
        return VisitReport::with([
            'employee',
            'project',
            'project.subClient',
            'project.subClient.client',
            'visitPhotos' => function ($query) {
                $query->orderBy('created_at', 'asc');
            },
        ])->findOrFail($visitReportId);
    }

    /**
     * Valida los datos del reporte de visita
     */
    public function validateVisitReportData(VisitReport $visitReport): void
    {
        if (!$visitReport->employee) {
            throw new \Exception('El reporte de visita debe tener un empleado asignado');
        }

        if (!$visitReport->project) {
            throw new \Exception('El reporte de visita debe estar asociado a un proyecto');
        }
    }

    /**
     * Prepara los datos para la vista
     */
    public function prepareViewData(VisitReport $visitReport): array
    {
        // Obtener información del cliente desde el proyecto
        $mainClient = null;
        $mainSubClient = null;

        if ($visitReport->project && $visitReport->project->subClient) {
            $mainSubClient = $visitReport->project->subClient;
            $mainClient = $visitReport->project->subClient->client;
        }

        return [
            'visitReport' => $visitReport,
            'employee' => $visitReport->employee,
            'project' => $visitReport->project,
            'visitPhotos' => $visitReport->visitPhotos,
            'mainClient' => $mainClient,
            'mainSubClient' => $mainSubClient,
            'generatedAt' => now(),
        ];
    }

    /**
     * Crea el objeto PDF
     */
    public function createPdf(array $data, array $customOptions = []): \Barryvdh\DomPDF\PDF
    {
        $options = array_merge(self::PDF_OPTIONS, [
            'chroot' => [
                public_path('storage'),
                public_path('images'),
                storage_path('app/public'),
            ],
        ], $customOptions);

        return Pdf::loadView('reports.visit-report-evidence-pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions($options);
    }

    /**
     * Genera un nombre único para el archivo
     */
    public function generateFilename(VisitReport $visitReport): string
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $reportName = Str::slug($visitReport->name ?? 'visita', '_');

        return "evidencias_visita_{$visitReport->id}_{$reportName}_{$timestamp}.pdf";
    }
}
