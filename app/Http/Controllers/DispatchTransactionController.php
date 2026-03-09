<?php

namespace App\Http\Controllers;

use App\Models\DispatchTransaction;

/**
 * Controlador para consultar las transacciones de despacho de almacén.
 */
class DispatchTransactionController extends Controller
{
    /**
     * Obtener el historial de despachos (transacciones) para un requerimiento específico.
     *
     * @param int $requirementId  ID del requerimiento del proyecto.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(int $requirementId)
    {
        try {
            $transactions = DispatchTransaction::with(['employee.employee', 'originLocation', 'destinationLocation'])
                ->where('project_requirement_id', $requirementId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $transactions->map(fn($t) => [
                    'id'          => $t->id,
                    'date'        => $t->created_at->format('d/m/Y h:i A'),
                    'employee'    => $t->employee->employee->short_name ?? ($t->employee->name ?? 'Usuario'),
                    'quantity'    => (float) $t->quantity,
                    'origin'      => $t->originLocation->name ?? '-',
                    'destination' => $t->destinationLocation->name ?? '-',
                    'cost'        => (float) $t->additional_cost,
                    'comment'     => $t->comment ?: '-',
                ]),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar el historial: ' . $e->getMessage(),
            ], 500);
        }
    }
}
