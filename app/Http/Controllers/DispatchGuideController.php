<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DispatchGuide;

class DispatchGuideController extends Controller
{
    /**
     * Devuelve la vista Blade con las transacciones asociadas a una guía específica
     * Usado a través de AJAX para Maestro-Detalle.
     */
    public function index($id)
    {
        $guide = DispatchGuide::with([
            'dispatchTransactions.projectRequirement.project.subClient.client',
            'dispatchTransactions.projectRequirement.unit',
            'dispatchTransactions.employee.employee',
            'dispatchTransactions.dispatchGuide' // Auto-ref (para que nullsafe no falle)
        ])->findOrFail($id);

        // Renderizamos y retornamos directamente el componente blade
        return view('components.quote-warehouse.transaction-table', [
            'transactions' => $guide->dispatchTransactions
        ]);
    }
}
