<?php

namespace App\Http\Controllers;

use App\Services\VisitReportEvidencePdfService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * Controlador para generar Informes de Evidencias de Visita (PDF con fotos)
 */
class VisitReportEvidenceController extends Controller
{
    public function __construct(
        private VisitReportEvidencePdfService $pdfService
    ) {}

    /**
     * Genera un informe de evidencias de visita en formato PDF
     *
     * @param int $visitReportId
     * @param Request $request
     * @return Response|JsonResponse
     */
    public function generateReport(int $visitReportId, Request $request)
    {
        try {
            $request->validate([
                'inline' => 'boolean',
                'async' => 'boolean',
                'email' => 'email|nullable',
            ]);

            if ($request->boolean('async')) {
                return $this->generateAsync($visitReportId, $request);
            }

            return $this->generateSync($visitReportId, $request);
        } catch (\Exception $e) {
            Log::error('Error generando Informe de Evidencias de Visita', [
                'visit_report_id' => $visitReportId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Error al generar el Informe de Evidencias de Visita',
                'message' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor',
            ], 500);
        }
    }

    /**
     * Genera PDF de forma síncrona
     */
    private function generateSync(int $visitReportId, Request $request): Response
    {
        $pdf = $this->pdfService->generateSync($visitReportId);
        $visitReport = $this->pdfService->getVisitReportWithRelations($visitReportId);
        $filename = $this->pdfService->generateFilename($visitReport);

        $disposition = $request->boolean('inline') ? 'inline' : 'attachment';

        Log::info('Informe de Evidencias de Visita generado sincrónicamente', [
            'visit_report_id' => $visitReportId,
            'filename' => $filename,
            'disposition' => $disposition,
        ]);

        if ($disposition === 'inline') {
            return $pdf->stream($filename);
        } else {
            return $pdf->download($filename);
        }
    }

    /**
     * Inicia generación asíncrona
     */
    private function generateAsync(int $visitReportId, Request $request): JsonResponse
    {
        $userEmail = $request->input('email');
        $shouldEmail = !empty($userEmail);

        $this->pdfService->generateAsync($visitReportId, $userEmail, $shouldEmail);

        Log::info('Generación asíncrona de Informe de Evidencias de Visita iniciada', [
            'visit_report_id' => $visitReportId,
            'should_email' => $shouldEmail,
            'email' => $userEmail,
        ]);

        return response()->json([
            'message' => 'Generación de Informe de Evidencias de Visita iniciada',
            'visit_report_id' => $visitReportId,
            'async' => true,
            'email_notification' => $shouldEmail,
        ], 202);
    }
}
