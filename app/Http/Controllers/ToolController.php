<?php

namespace App\Http\Controllers;

use App\Models\Tool;
use App\Models\ToolUnit;
use App\Models\ToolCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * Controlador para la gestión de herramientas (Unidades Físicas y Catálogo).
 * 
 * Se ha refactorizado para soportar la estructura de Catálogo (Tool) + Unidades (ToolUnit).
 * La mayoría de endpoints ahora retornan información de ToolUnit enriquecida con datos del Catálogo.
 */
class ToolController extends Controller
{
    /**
     * Listar todas las unidades de herramientas con filtros.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Base query on ToolUnit since status/serial/code are here
            $query = ToolUnit::with(['tool.brand', 'tool.category']);

            // Filtros opcionales
            $query->when($request->query('status'), function ($q, $status) {
                $q->where('status', $status);
            });

            // Filter by Catalog attributes (Category)
            $query->when($request->query('category_id'), function ($q, $categoryId) {
                $q->whereHas('tool', function ($sub) use ($categoryId) {
                    $sub->where('tool_category_id', $categoryId);
                });
            });

            // Filter by Catalog attributes (Brand)
            $query->when($request->query('brand_id'), function ($q, $brandId) {
                $q->whereHas('tool', function ($sub) use ($brandId) {
                    $sub->where('tool_brand_id', $brandId);
                });
            });

            // Búsqueda general
            $query->when($request->query('search'), function ($q, $search) {
                $q->where(function ($query) use ($search) {
                    $query->where('internal_code', 'like', "%{$search}%")
                        ->orWhere('serial_number', 'like', "%{$search}%")
                        ->orWhereHas('tool', function ($sub) use ($search) {
                            $sub->where('name', 'like', "%{$search}%")
                                ->orWhere('model', 'like', "%{$search}%");
                        });
                });
            });

            // Ordenamiento por defecto: nombre del catálogo
            $tools = $query->get()->sortBy(fn($unit) => $unit->tool?->name)->values();

            return response()->json([
                'success' => true,
                'data' => $tools,
                'message' => 'Herramientas obtenidas correctamente',
                'meta' => [
                    'total' => $tools->count(),
                    'timestamp' => now()->toIso8601String(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('ToolController@index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las herramientas',
            ], 500);
        }
    }

    /**
     * Mostrar una unidad específica.
     */
    public function show(int $id): JsonResponse
    {
        try {
            // Find Unit
            $toolUnit = ToolUnit::with(['tool.brand', 'tool.category', 'projectTools.project'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $toolUnit,
                'message' => 'Herramienta obtenida correctamente',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Herramienta no encontrada',
            ], 404);
        }
    }

    /**
     * Crear una nueva herramienta (Catálogo + Unidad).
     * Nota: Si se requiere agregar solo unidad a catálogo existente, lógica podría variar.
     * Aquí asumimos creación simple tipo "Registro una nueva herramienta con sus datos".
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                // Catalog fields
                'name' => 'required|string|max:255',
                'tool_brand_id' => 'nullable|exists:tool_brands,id',
                'tool_category_id' => 'nullable|exists:tool_categories,id',
                'model' => 'nullable|string|max:100',
                'description' => 'nullable|string',

                // Unit fields
                'code' => 'nullable|string|max:50', // Mapped to internal_code
                'serial_number' => 'nullable|string|max:100',
                'certification_document' => 'nullable|string|max:500',
                'certification_expiry' => 'nullable|date',
                'status' => 'nullable|in:Disponible,En Uso,En Mantenimiento,Dañado,Baja',
            ]);

            DB::beginTransaction();

            // 1. Find or Create Catalog Tool
            // Check if a tool with same name and model exists to reuse layout?
            // For simplicity in this controller, we create a new Catalog entry or use ID if provided (not in validation though)
            // Let's create a new Catalog entry for every request to match original behavior of "Created separate tool"

            $toolCatalog = Tool::create([
                'name' => $validated['name'],
                'tool_brand_id' => $validated['tool_brand_id'] ?? null,
                'tool_category_id' => $validated['tool_category_id'] ?? null,
                'model' => $validated['model'] ?? null,
                'description' => $validated['description'] ?? null,
            ]);

            // 2. Create Tool Unit
            $toolUnit = $toolCatalog->units()->create([
                'internal_code' => $validated['code'] ?? null,
                'serial_number' => $validated['serial_number'] ?? null,
                'certification_document' => $validated['certification_document'] ?? null,
                'certification_expiry' => $validated['certification_expiry'] ?? null,
                'status' => $validated['status'] ?? 'Disponible',
            ]);

            DB::commit();

            // Limpiar cache
            Cache::forget('tools_quick_search_cache');

            return response()->json([
                'success' => true,
                'data' => $toolUnit->load(['tool.brand', 'tool.category']),
                'message' => 'Herramienta creada correctamente',
            ], 201);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ToolController@store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la herramienta',
            ], 500);
        }
    }

    /**
     * Actualizar una herramienta (Unidad y su Catálogo asociado).
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $toolUnit = ToolUnit::with('tool')->findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'code' => 'nullable|string|max:50',
                'tool_brand_id' => 'nullable|exists:tool_brands,id',
                'tool_category_id' => 'nullable|exists:tool_categories,id',
                'model' => 'nullable|string|max:100',
                'serial_number' => 'nullable|string|max:100',
                'description' => 'nullable|string',
                'certification_document' => 'nullable|string|max:500',
                'certification_expiry' => 'nullable|date',
                'status' => 'nullable|in:Disponible,En Uso,En Mantenimiento,Dañado,Baja',
            ]);

            DB::beginTransaction();

            // Update Unit
            $toolUnit->update([
                'internal_code' => $validated['code'] ?? $toolUnit->internal_code,
                'serial_number' => $validated['serial_number'] ?? $toolUnit->serial_number,
                'certification_document' => $validated['certification_document'] ?? $toolUnit->certification_document,
                'certification_expiry' => $validated['certification_expiry'] ?? $toolUnit->certification_expiry,
                'status' => $validated['status'] ?? $toolUnit->status,
            ]);

            // Update Catalog (Note: This affects all units of this catalog if they share it. 
            // In this simple 1-to-1 migration flow, it's fine. For strict Catalog management, be careful.)
            if ($toolUnit->tool) {
                $toolUnit->tool->update([
                    'name' => $validated['name'] ?? $toolUnit->tool->name,
                    'tool_brand_id' => $validated['tool_brand_id'] ?? $toolUnit->tool->tool_brand_id,
                    'tool_category_id' => $validated['tool_category_id'] ?? $toolUnit->tool->tool_category_id,
                    'model' => $validated['model'] ?? $toolUnit->tool->model,
                    'description' => $validated['description'] ?? $toolUnit->tool->description,
                ]);
            }

            DB::commit();

            Cache::forget('tools_quick_search_cache');

            return response()->json([
                'success' => true,
                'data' => $toolUnit->fresh(['tool.brand', 'tool.category']),
                'message' => 'Herramienta actualizada correctamente',
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ToolController@update: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la herramienta',
            ], 500);
        }
    }

    /**
     * Eliminar una herramienta (Unidad).
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $toolUnit = ToolUnit::findOrFail($id);

            // Verificar que no esté asignada en uso
            if ($toolUnit->projectTools()->whereNull('returned_at')->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar una herramienta que está asignada a un proyecto',
                ], 422);
            }

            // Optional: Check if Catalog should be deleted if no units remain?
            // For now, just delete the unit.
            $toolUnit->delete();

            Cache::forget('tools_quick_search_cache');

            return response()->json([
                'success' => true,
                'message' => 'Herramienta eliminada correctamente',
            ]);
        } catch (\Exception $e) {
            Log::error('ToolController@destroy: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la herramienta',
            ], 500);
        }
    }

    /**
     * Lista de categorías de herramientas.
     */
    public function categories(): JsonResponse
    {
        try {
            $categories = Cache::remember('tool_categories', 600, function () {
                return ToolCategory::select(['id', 'name'])
                    ->orderBy('name')
                    ->get();
            });

            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'Categorías obtenidas correctamente',
            ]);
        } catch (\Exception $e) {
            Log::error('ToolController@categories: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las categorías',
            ], 500);
        }
    }

    /**
     * Búsqueda rápida optimizada (Sobre Unidades).
     */
    public function quickSearch(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'query' => 'nullable|string|max:100',
                'status' => 'nullable|string',
                'limit' => 'nullable|integer|min:1|max:50',
            ]);

            $searchQuery = trim($request->input('query', ''));
            $status = $request->input('status');
            $limit = $request->input('limit', 15);

            // Si no hay query, devolver las más recientes
            if (empty($searchQuery)) {
                $tools = ToolUnit::with('tool.category')
                    ->when($status, fn($q) => $q->where('status', $status))
                    ->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get();

                return $this->formatQuickSearchResponse($tools, $searchQuery);
            }

            // Cache key
            $cacheKey = 'tools_qs_' . md5($searchQuery . '_' . $status . '_' . $limit);

            $tools = Cache::remember($cacheKey, 300, function () use ($searchQuery, $status, $limit) {
                return $this->executeOptimizedSearch($searchQuery, $status, $limit);
            });

            return $this->formatQuickSearchResponse($tools, $searchQuery);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Parámetros de búsqueda inválidos',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('ToolController@quickSearch: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda',
            ], 500);
        }
    }

    /**
     * Ejecuta la búsqueda optimizada (Unidades + Catálogo).
     */
    private function executeOptimizedSearch(string $query, ?string $status, int $limit): \Illuminate\Support\Collection
    {
        $searchPattern = '%' . $query . '%';

        // Advanced Search: Union or Join strategy could work.
        // For Eloquent simplicity with relevancy:

        $results = ToolUnit::with(['tool.category'])
            ->where(function ($q) use ($query, $searchPattern) {
                // Unit match
                $q->where('internal_code', 'like', $searchPattern)
                    ->orWhere('serial_number', 'like', $searchPattern)
                    // Catalog match
                    ->orWhereHas('tool', function ($tq) use ($searchPattern) {
                        $tq->where('name', 'like', $searchPattern)
                            ->orWhere('model', 'like', $searchPattern);
                    });
            });

        if ($status) {
            $results->where('status', $status);
        }

        // Ideally apply custom sorting for relevance in memory if dataset is small, or raw SQL.
        // Given limit 15, we can take more and sort in PHP.
        $collection = $results->take(50)->get();

        return $collection->map(function ($unit) use ($query) {
            // Calculate score
            $score = 0;
            if ($unit->internal_code === $query) $score += 10;
            if (stripos($unit->internal_code, $query) !== false) $score += 5;
            if (stripos($unit->tool?->name, $query) !== false) $score += 3;
            if (stripos($unit->serial_number, $query) !== false) $score += 2;
            $unit->relevance = $score;
            return $unit;
        })
            ->sortByDesc('relevance')
            ->take($limit)
            ->values();
    }

    /**
     * Formatea la respuesta de búsqueda rápida.
     */
    private function formatQuickSearchResponse($units, string $query): JsonResponse
    {
        $data = $units->map(function ($unit) {
            return [
                'id' => $unit->id,
                'code' => $unit->internal_code,
                'name' => $unit->tool?->name,
                'display' => $unit->internal_code
                    ? "[{$unit->internal_code}] {$unit->tool?->name}"
                    : $unit->tool?->name,
                'status' => $unit->status,
                'category' => $unit->tool?->category?->name,
                'available' => $unit->status === 'Disponible',
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => 'Búsqueda completada',
            'meta' => [
                'query' => $query,
                'count' => $data->count(),
                'timestamp' => now()->toIso8601String(),
            ],
        ]);
    }

    /**
     * Unidades con certificación próxima a vencer.
     */
    public function expiringCertifications(Request $request): JsonResponse
    {
        try {
            $days = $request->input('days', 30);

            $units = ToolUnit::with(['tool.brand', 'tool.category'])
                ->whereNotNull('certification_expiry')
                ->where('certification_expiry', '<=', now()->addDays($days))
                ->where('certification_expiry', '>=', now())
                ->where('status', '!=', 'Baja')
                ->orderBy('certification_expiry')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $units,
                'message' => "Unidades con certificación por vencer en {$days} días",
                'meta' => [
                    'total' => $units->count(),
                    'days_threshold' => $days,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('ToolController@expiringCertifications: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener certificaciones por vencer',
            ], 500);
        }
    }

    /**
     * Estadísticas del catálogo de herramientas.
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = Cache::remember('tools_stats', 600, function () {
                return [
                    'total_units' => ToolUnit::count(),
                    'total_catalogs' => Tool::count(),
                    'by_status' => ToolUnit::select('status', DB::raw('count(*) as count'))
                        ->groupBy('status')
                        ->pluck('count', 'status'),
                    'by_category' => Tool::select('tool_category_id', DB::raw('count(*) as count'))
                        ->with('category:id,name')
                        ->groupBy('tool_category_id')
                        ->get()
                        ->mapWithKeys(fn($item) => [
                            $item->category?->name ?? 'Sin categoría' => $item->count
                        ]),
                    'expiring_soon' => ToolUnit::whereNotNull('certification_expiry')
                        ->where('certification_expiry', '<=', now()->addDays(30))
                        ->where('certification_expiry', '>=', now())
                        ->count(),
                    'expired' => ToolUnit::whereNotNull('certification_expiry')
                        ->where('certification_expiry', '<', now())
                        ->count(),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Estadísticas de herramientas',
            ]);
        } catch (\Exception $e) {
            Log::error('ToolController@stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas',
            ], 500);
        }
    }
}
