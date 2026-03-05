<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\QuoteWarehouseDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreQuoteWarehouseDetailRequest;
use App\Models\QuoteWarehouse;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf; // Asegúrate de tener instalado barryvdh/laravel-dompdf
use Illuminate\Support\Facades\Auth;
use App\Models\Location;

/**
 * Controlador para manejar las operaciones CRUD de cotizaciones (Quotes).
 */
class QuoteWarehouseController extends Controller
{

    /**
     * Muestra la vista previa de una cotización/ITEMS para almacén.
     *
     * @param \App\Models\QuoteWarehouse $quoteWarehouse
     * @return \Illuminate\View\View
     */
    public function preview(QuoteWarehouse $quoteWarehouse, Request $request)
    {
        $quote = $quoteWarehouse->quote;
        $quote->load([
            'subClient',
            'project.projectRequirements.requirement.unit', // Cargar relaciones necesarias
            'project.projectRequirements.requirement.requirementType',
            'project.projectRequirements.quoteDetail.pricelist'
        ]);

        $quoteWarehouse->load('employee.employee');

        // Obtener los detalles atendidos por almacén (quote_warehouse_details)
        $warehouseDetailsCollection = QuoteWarehouseDetail::where('quote_warehouse_id', $quoteWarehouse->id)->get();

        // Map by project_requirement_id
        $detailsByReqId = $warehouseDetailsCollection->whereNotNull('project_requirement_id')->keyBy('project_requirement_id');

        $projectRequirements = $quote->project->projectRequirements ?? collect();

        $details = [];

        foreach ($projectRequirements as $req) {
            $attended = 0;

            // Try to find attendance
            if (isset($detailsByReqId[$req->id])) {
                $attended = $detailsByReqId[$req->id]->attended_quantity;
            }

            $satLine = '-';
            if ($req->requirementable instanceof \App\Models\QuoteDetail) {
                $satLine = $req->requirementable->pricelist->sat_line ?? '-';
            } elseif ($req->requirementable instanceof \App\Models\ToolUnit) {
                $satLine = 'HERRAMIENTAS';
            }

            // Usamos los accessors del modelo ProjectRequirement
            $details[] = [
                'project_requirement_id' => $req->id, // Principal ID
                'sat_line'         => $satLine,
                'product_name'     => $req->product_name,
                'quantity'         => $req->quantity,
                'unit_price'       => $req->price_unit,
                'subtotal'         => $req->subtotal,
                'unit_name'        => $req->unit_name,
                'entregado'        => $attended,
                'type_name'        => $req->consumable_type_name,
                'comment'          => $detailsByReqId[$req->id]->comment ?? '',
                'location_origin_id' => $detailsByReqId[$req->id]->location_origin_id ?? null,
                'location_destination_id' => $detailsByReqId[$req->id]->location_destination_id ?? null,
                'additional_cost'  => $detailsByReqId[$req->id]->additional_cost ?? 0,
                'cost_description' => $detailsByReqId[$req->id]->cost_description ?? '',
            ];
        }

        return view('filament.resources.quote-warehouse-resource.pages.list', [
            'quote'        => $quote,
            'client'       => $quote->subClient->name ?? '',
            'details'      => $details,
            'quoteWarehouse' => $quoteWarehouse,
            'locations'    => Location::where('is_active', true)->get(),
        ]);
    }

    /**
     * Guarda el detalle atendido de almacén.
     */
    public function store(StoreQuoteWarehouseDetailRequest $request)
    {
        try {
            $quoteWarehouse = QuoteWarehouse::findOrFail($request->input('quote_warehouse_id'));
            $quoteWarehouse->observations = $request->input('observations');

            // Cambiar: Guardar user_id en employee_id (para respetar la FK)
            $quoteWarehouse->employee_id = Auth::user()->id;

            // Obtener el progreso total enviado desde la vista
            $progresoTotal = $request->input('progreso_total', 0);
            Log::info('Progreso total recibido:', ['progreso_total' => $progresoTotal]);

            // Variable para almacenar el mensaje de cambio de estado
            $estadoMensaje = null;

            // Validar que el progreso total se reciba correctamente
            if ($progresoTotal > 0 && $progresoTotal < 100) {
                if ($quoteWarehouse->status !== 'Parcial') {
                    $estadoMensaje = 'El estado ha cambiado a Parcial.';
                }
                $quoteWarehouse->status = 'Parcial';
            } elseif ($progresoTotal === 100) {
                if ($quoteWarehouse->status !== 'Atendido') {
                    $estadoMensaje = 'El estado ha cambiado a Atendido.';
                    $quoteWarehouse->attended_at = now(); // Guardar la fecha y hora actual solo si es la primera vez
                }
                $quoteWarehouse->status = 'Atendido';
            }

            $quoteWarehouse->save();

            $details = $request->input('details', []);
            $errores = [];
            $guardados = 0;

            // Si no hay detalles, solo guardar las observaciones y salir
            if (empty($details)) {
                return response()->json([
                    'success' => true,
                    'message' => '¡Observaciones guardadas correctamente!',
                    'estadoMensaje' => $estadoMensaje,
                ]);
            }

            foreach ($details as $i => $detail) {
                // Solo guardar si a_despachar > 0 y existe project_requirement_id
                if (
                    !isset($detail['project_requirement_id']) ||
                    !isset($detail['a_despachar']) ||
                    $detail['a_despachar'] <= 0
                ) {
                    continue;
                }

                Log::info('Detalle recibido', [
                    'quote_warehouse_id'     => $quoteWarehouse->id,
                    'project_requirement_id' => $detail['project_requirement_id'],
                    'attended_quantity'      => $detail['a_despachar'],
                ]);

                try {
                    // Verificar si ya existe un registro para este project_requirement_id
                    $detalleExistente = QuoteWarehouseDetail::where('quote_warehouse_id', $quoteWarehouse->id)
                        ->where('project_requirement_id', $detail['project_requirement_id'])
                        ->first();

                    if ($detalleExistente) {
                        // Si ya existe, sumamos el nuevo valor al attended_quantity existente
                        $detalleExistente->update([
                            'attended_quantity' => $detalleExistente->attended_quantity + $detail['a_despachar'],
                            'comment'           => $detail['comment'] ?? $detalleExistente->comment,
                            'location_origin_id' => $detail['location_origin_id'] ?? $detalleExistente->location_origin_id,
                            'location_destination_id' => $detail['location_destination_id'] ?? $detalleExistente->location_destination_id,
                            'additional_cost'    => $detail['additional_cost'] ?? $detalleExistente->additional_cost,
                            'cost_description'   => $detail['cost_description'] ?? $detalleExistente->cost_description,
                        ]);
                    } else {
                        // Si no existe, creamos un nuevo registro
                        QuoteWarehouseDetail::create([
                            'quote_warehouse_id'     => $quoteWarehouse->id,
                            'project_requirement_id' => $detail['project_requirement_id'],
                            'attended_quantity'      => $detail['a_despachar'],
                            'comment'                => $detail['comment'] ?? null,
                            'location_origin_id'     => $detail['location_origin_id'] ?? null,
                            'location_destination_id' => $detail['location_destination_id'] ?? null,
                            'additional_cost'        => $detail['additional_cost'] ?? 0,
                            'cost_description'       => $detail['cost_description'] ?? null,
                        ]);
                    }

                    // Actualizar el estado de la herramienta a "En Uso" si es una unidad de herramienta despachada
                    $projectReq = \App\Models\ProjectRequirement::find($detail['project_requirement_id']);
                    if ($projectReq && $projectReq->requirementable_type === \App\Models\ToolUnit::class) {
                        $toolUnit = $projectReq->requirementable;
                        if ($toolUnit && $toolUnit->status !== 'En Uso') {
                            $toolUnit->update(['status' => 'En Uso']);
                        }
                    }

                    $guardados++;
                } catch (\Exception $e) {
                    $errores[] = "Error en el detalle #" . ($i + 1) . ": " . $e->getMessage();
                }
            }

            // Mensaje si no se guardó ningún detalle
            if ($guardados === 0 && count($errores) === 0) {
                return response()->json([
                    'success' => true,
                    'message' => '¡Observaciones guardadas correctamente! No se guardó ningún detalle.',
                    'estadoMensaje' => $estadoMensaje,
                ]);
            }

            if (count($errores)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Algunos detalles no se guardaron correctamente.',
                    'errors'  => $errores,
                    'estadoMensaje' => $estadoMensaje,
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => "¡Despacho guardado correctamente! Se actualizaron $guardados detalles.",
                'estadoMensaje' => $estadoMensaje,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Genera un PDF de la vista de atención de suministros.
     *
     * @param \App\Models\QuoteWarehouse $quoteWarehouse
     * @return \Illuminate\Http\Response
     */
    public function generatePdf(QuoteWarehouse $quoteWarehouse)
    {
        $quote = $quoteWarehouse->quote;
        $quote->load(['subClient.client', 'project', 'quoteDetails.pricelist.unit']);
        $quoteWarehouse->load(['employee.employee', 'details']);

        // Obtener los detalles atendidos por almacén agrupados por project_requirement_id
        $warehouseDetails = QuoteWarehouseDetail::where('quote_warehouse_id', $quoteWarehouse->id)
            ->get()
            ->keyBy('project_requirement_id');

        // Pre-cargar todas las ubicaciones usadas
        $locationIds = $warehouseDetails->pluck('location_origin_id')
            ->merge($warehouseDetails->pluck('location_destination_id'))
            ->filter()
            ->unique();
        $locationsMap = Location::whereIn('id', $locationIds)->pluck('name', 'id');

        $groupedDetails = $quote->project->projectRequirements->groupBy('consumable_type_name');

        $details = [];
        $hasAdditionalCosts = false;
        $totalAdditionalCost = 0;

        foreach (['Suministro', 'Herramienta'] as $type) {
            if ($groupedDetails->has($type)) {
                foreach ($groupedDetails[$type] as $req) {
                    $warehouseDetail = $warehouseDetails[$req->id] ?? null;
                    $attended = $warehouseDetail->attended_quantity ?? 0;

                    if ($attended <= 0) {
                        continue;
                    }

                    $satDescription = '';
                    $unitName = '';

                    if ($req->requirementable instanceof \App\Models\Requirement) {
                        $satDescription = $req->requirementable->product_description;
                        $unitName = $req->requirementable->unit->name ?? 'UND';
                    } elseif ($req->requirementable instanceof \App\Models\QuoteDetail) {
                        $satDescription = $req->requirementable->pricelist->sat_description ?? '';
                        $unitName = $req->requirementable->pricelist->unit->name ?? 'UND';
                    } elseif ($req->requirementable instanceof \App\Models\ToolUnit) {
                        $satDescription = $req->requirementable->tool->name ?? 'Herramienta';
                        $unitName = 'UND';
                    }

                    $additionalCost = (float) ($warehouseDetail->additional_cost ?? 0);
                    if ($additionalCost > 0) {
                        $hasAdditionalCosts = true;
                    }
                    $totalAdditionalCost += $additionalCost;

                    $details[] = [
                        'item_type'        => $type,
                        'product_name'     => $req->product_name ?? $satDescription,
                        'sat_description'  => $satDescription,
                        'quantity'         => $req->quantity,
                        'unit_name'        => $unitName,
                        'entregado'        => $attended,
                        'origin_name'      => $locationsMap[$warehouseDetail->location_origin_id ?? 0] ?? '',
                        'destination_name' => $locationsMap[$warehouseDetail->location_destination_id ?? 0] ?? '',
                        'additional_cost'  => $additionalCost,
                        'cost_description' => $warehouseDetail->cost_description ?? '',
                    ];
                }
            }
        }

        // Determinar ubicaciones predominantes para encabezado (la más frecuente)
        $originCounts = $warehouseDetails->pluck('location_origin_id')->filter()->countBy();
        $destCounts = $warehouseDetails->pluck('location_destination_id')->filter()->countBy();
        $mainOriginId = $originCounts->sortDesc()->keys()->first();
        $mainDestId = $destCounts->sortDesc()->keys()->first();

        // Generar el PDF con orientación portrait para acomodar en hoja A4 vertical
        $pdf = Pdf::loadView('filament.resources.quote-warehouse-resource.pages.pdf', [
            'quoteWarehouse'      => $quoteWarehouse,
            'quote'               => $quote,
            'clientName'          => $quote->subClient->client->business_name ?? ($quote->subClient->name ?? 'Sin Cliente'),
            'clientRuc'           => $quote->subClient->client->document_number ?? '—',
            'projectName'         => $quote->project->name ?? '—',
            'serviceCode'         => $quote->project->service_code ?? '—',
            'attendedBy'          => $quoteWarehouse->employee->employee->short_name ?? ($quoteWarehouse->employee->name ?? 'N/A'),
            'transferDate'        => $quoteWarehouse->attended_at ? $quoteWarehouse->attended_at->format('d/m/Y') : ($quote->execution_date ? $quote->execution_date->format('d/m/Y') : now()->timezone('America/Lima')->format('d/m/Y')),
            'originLocation'      => $locationsMap[$mainOriginId] ?? '',
            'destinationLocation' => $locationsMap[$mainDestId] ?? '',
            'details'             => $details,
            'hasAdditionalCosts'  => $hasAdditionalCosts,
            'totalAdditionalCost' => $totalAdditionalCost,
            'observations'        => $quoteWarehouse->observations,
            'downloadDate'        => now()->timezone('America/Lima')->format('d/m/Y H:i:s'),
        ])->setPaper('a4', 'portrait');

        $filename = 'Guia_Remision_' . str_pad($quoteWarehouse->id, 6, '0', STR_PAD_LEFT) . '.pdf';
        return $pdf->stream($filename);
    }

    /**
     * Crea un nuevo lugar/destino desde la vista de despacho.
     */
    public function storeLocation(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:locations,name',
        ], [
            'name.required' => 'El nombre del lugar es obligatorio.',
            'name.unique' => 'Ya existe un lugar con ese nombre.',
        ]);

        try {
            $location = Location::create([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'is_active' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lugar creado correctamente.',
                'data' => [
                    'id' => $location->id,
                    'name' => $location->name,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el lugar: ' . $e->getMessage(),
            ], 500);
        }
    }
}
