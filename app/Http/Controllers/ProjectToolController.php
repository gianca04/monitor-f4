<?php

namespace App\Http\Controllers;

use App\Models\ProjectTool;
use App\Models\Tool;
use App\Models\ToolUnit;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * Controlador para la gestión de asignación de herramientas a proyectos.
 * 
 * Maneja las operaciones CRUD para la tabla pivote project_tools,
 * así como la asignación y devolución de herramientas.
 */
class ProjectToolController extends Controller
{
    /**
     * Listar todas las asignaciones de herramientas.
     * Soporta filtros por project_id, tool_unit_id y estado.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Eager load: Project -> ProjectTool -> ToolUnit -> Tool (Catalog) -> Brand/Category
            $query = ProjectTool::with(['project', 'tool.tool.brand', 'tool.tool.category']);

            // Filtros opcionales
            $query->when($request->query('project_id'), function ($q, $projectId) {
                $q->where('project_id', $projectId);
            });

            $query->when($request->query('tool_unit_id'), function ($q, $toolId) {
                $q->where('tool_unit_id', $toolId);
            });

            // Filtrar solo asignaciones activas (sin devolver)
            $query->when($request->query('active'), function ($q) {
                $q->whereNull('returned_at');
            });

            // Filtrar solo asignaciones devueltas
            $query->when($request->query('returned'), function ($q) {
                $q->whereNotNull('returned_at');
            });

            $assignments = $query->orderBy('assigned_at', 'desc')->get();

            $data = $assignments->map(function ($assignment) {
                $unit = $assignment->tool; // Relation is 'tool' but returns ToolUnit
                $catalog = $unit?->tool;

                return [
                    'id' => $assignment->id,
                    'project_id' => $assignment->project_id,
                    'project_name' => $assignment->project?->name,
                    'tool_unit_id' => $assignment->tool_unit_id,
                    'tool_name' => $catalog?->name,
                    'tool_model' => $catalog?->model,
                    'tool_code' => $unit?->internal_code,
                    'serial_number' => $unit?->serial_number,
                    'tool_brand' => $catalog?->brand?->name,
                    'assigned_at' => $assignment->assigned_at?->format('Y-m-d'),
                    'returned_at' => $assignment->returned_at?->format('Y-m-d'),
                    'notes' => $assignment->notes,
                    'is_active' => $assignment->returned_at === null,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Asignaciones obtenidas correctamente',
                'meta' => [
                    'total' => $data->count(),
                    'timestamp' => now()->toIso8601String(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('ProjectToolController@index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las asignaciones',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mostrar una asignación específica.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $assignment = ProjectTool::with(['project', 'tool.tool.brand', 'tool.tool.category'])
                ->findOrFail($id);

            $unit = $assignment->tool;
            $catalog = $unit?->tool;

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $assignment->id,
                    'project' => $assignment->project,
                    'tool_unit' => $unit,
                    'tool_catalog' => $catalog,
                    'assigned_at' => $assignment->assigned_at?->format('Y-m-d'),
                    'returned_at' => $assignment->returned_at?->format('Y-m-d'),
                    'notes' => $assignment->notes,
                    'is_active' => $assignment->returned_at === null,
                ],
                'message' => 'Asignación obtenida correctamente',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Asignación no encontrada',
            ], 404);
        }
    }

    /**
     * Asignar una unidad de herramienta a un proyecto.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Frontend might send 'tool_id' meaning 'tool_unit_id'. Support both.
            $data = $request->all();
            if (isset($data['tool_id']) && !isset($data['tool_unit_id'])) {
                $data['tool_unit_id'] = $data['tool_id'];
            }
            $request->merge($data);

            $validated = $request->validate([
                'project_id' => 'required|exists:projects,id',
                'tool_unit_id' => 'required|exists:tool_units,id',
                'assigned_at' => 'nullable|date',
                'notes' => 'nullable|string|max:500',
            ]);

            // Verificar que la unidad esté disponible
            $toolUnit = ToolUnit::with('tool')->findOrFail($validated['tool_unit_id']);

            if ($toolUnit->status !== 'Disponible') {
                $name = $toolUnit->tool?->name ?? 'Unidad';
                return response()->json([
                    'success' => false,
                    'message' => "La unidad '{$name}' ({$toolUnit->internal_code}) no está disponible. Estado actual: {$toolUnit->status}",
                ], 422);
            }

            // Verificar que la unidad no esté ya asignada activa
            // (Strictly speaking redundant checks if status is managed correctly, but good for safety)
            $existingAssignment = ProjectTool::where('tool_unit_id', $validated['tool_unit_id'])
                ->whereNull('returned_at')
                ->first();

            if ($existingAssignment) {
                $name = $toolUnit->tool?->name ?? 'Unidad';
                return response()->json([
                    'success' => false,
                    'message' => "La unidad '{$name}' ya está asignada al proyecto ID: {$existingAssignment->project_id}",
                ], 422);
            }

            // Crear la asignación
            $assignment = ProjectTool::create([
                'project_id' => $validated['project_id'],
                'tool_unit_id' => $validated['tool_unit_id'],
                'assigned_at' => $validated['assigned_at'] ?? now(),
                'notes' => $validated['notes'] ?? null,
            ]);

            // Actualizar estado de la unidad
            $toolUnit->update(['status' => 'En Uso']);

            $assignment->load(['project', 'tool.tool']);

            $toolName = $toolUnit->tool?->name;

            return response()->json([
                'success' => true,
                'data' => $assignment,
                'message' => "Herramienta '{$toolName}' asignada correctamente",
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('ProjectToolController@store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar la herramienta',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Actualizar una asignación existente.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $assignment = ProjectTool::with('tool')->findOrFail($id);

            $validated = $request->validate([
                'assigned_at' => 'nullable|date',
                'returned_at' => 'nullable|date',
                'notes' => 'nullable|string|max:500',
            ]);

            $assignment->update($validated);

            // Si se marca como devuelto, actualizar estado de herramienta
            if (isset($validated['returned_at']) && $validated['returned_at']) {
                $assignment->tool->update(['status' => 'Disponible']);
            }

            $assignment->load(['project', 'tool.tool']);

            return response()->json([
                'success' => true,
                'data' => $assignment,
                'message' => 'Asignación actualizada correctamente',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('ProjectToolController@update: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la asignación',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Registrar la devolución de una herramienta.
     */
    public function returnTool(Request $request, int $id): JsonResponse
    {
        try {
            $assignment = ProjectTool::with('tool.tool')->findOrFail($id);

            if ($assignment->returned_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta herramienta ya fue devuelta',
                ], 422);
            }

            $validated = $request->validate([
                'returned_at' => 'nullable|date',
                'notes' => 'nullable|string|max:500',
            ]);

            $assignment->update([
                'returned_at' => $validated['returned_at'] ?? now(),
                'notes' => $validated['notes'] ?? $assignment->notes,
            ]);

            // Actualizar estado de herramienta a Disponible
            $assignment->tool->update(['status' => 'Disponible']); // assignment->tool is ToolUnit

            $toolName = $assignment->tool->tool?->name ?? 'Herramienta';

            return response()->json([
                'success' => true,
                'data' => $assignment->fresh(['project', 'tool']),
                'message' => "Herramienta '{$toolName}' devuelta correctamente",
            ]);
        } catch (\Exception $e) {
            Log::error('ProjectToolController@returnTool: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la devolución',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Eliminar una asignación.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $assignment = ProjectTool::with('tool.tool')->findOrFail($id);

            // Si la herramienta no fue devuelta, marcarla como disponible
            if (!$assignment->returned_at) {
                $assignment->tool->update(['status' => 'Disponible']);
            }

            $toolName = $assignment->tool->tool?->name ?? 'Herramienta';
            $assignment->delete();

            return response()->json([
                'success' => true,
                'message' => "Asignación de '{$toolName}' eliminada correctamente",
            ]);
        } catch (\Exception $e) {
            Log::error('ProjectToolController@destroy: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la asignación',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener unidades de herramientas disponibles para asignar.
     */
    public function availableTools(Request $request): JsonResponse
    {
        try {
            // Buscamos unidades disponibles, eager loading de su definición de catálogo
            $query = ToolUnit::with(['tool.brand', 'tool.category'])
                ->where('status', 'Disponible');

            // Búsqueda
            $query->when($request->query('search'), function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    // Buscar por código o serie de la unidad
                    $sub->where('internal_code', 'like', "%{$search}%")
                        ->orWhere('serial_number', 'like', "%{$search}%")
                        // O buscar por nombre/modelo del catálogo
                        ->orWhereHas('tool', function ($catalogQ) use ($search) {
                            $catalogQ->where('name', 'like', "%{$search}%")
                                ->orWhere('model', 'like', "%{$search}%");
                        });
                });
            });

            // Filtro por categoría (del catálogo)
            $query->when($request->query('category_id'), function ($q, $categoryId) {
                $q->whereHas('tool', function ($catalogQ) use ($categoryId) {
                    $catalogQ->where('tool_category_id', $categoryId);
                });
            });

            // Ordenar por nombre de herramienta
            $units = $query->get()->sortBy(fn($unit) => $unit->tool?->name);

            // Transformar para el frontend
            $data = $units->map(function ($unit) {
                return [
                    'id' => $unit->id, // Use Unit ID
                    'tool_id' => $unit->id, // Alias for frontend compatibility (if it expects tool_id)
                    'internal_code' => $unit->internal_code,
                    'serial_number' => $unit->serial_number,
                    'status' => $unit->status,
                    'name' => $unit->tool?->name, // Flatten catalog data
                    'model' => $unit->tool?->model,
                    'brand' => $unit->tool?->brand,
                    'category' => $unit->tool?->category,
                    'description' => $unit->tool?->description,
                ];
            })->values();

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Herramientas disponibles obtenidas correctamente',
                'meta' => [
                    'total' => $data->count(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('ProjectToolController@availableTools: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener herramientas disponibles',
                'error' => $e->getMessage(), // Dev info
            ], 500);
        }
    }

    /**
     * Obtener herramientas asignadas a un proyecto específico.
     */
    public function toolsByProject(int $projectId): JsonResponse
    {
        try {
            $assignments = ProjectTool::with(['tool.tool.brand', 'tool.tool.category'])
                ->where('project_id', $projectId)
                ->orderBy('assigned_at', 'desc')
                ->get();

            $data = $assignments->map(function ($assignment) {
                $unit = $assignment->tool;
                $catalog = $unit?->tool;
                return [
                    'assignment_id' => $assignment->id,
                    'tool' => [ // Reconstruct tool object structure if needed, or flatten
                        'id' => $unit?->id,
                        'name' => $catalog?->name,
                        'code' => $unit?->internal_code,
                        'model' => $catalog?->model,
                        'brand' => $catalog?->brand,
                        'category' => $catalog?->category,
                    ],
                    'assigned_at' => $assignment->assigned_at?->format('Y-m-d'),
                    'returned_at' => $assignment->returned_at?->format('Y-m-d'),
                    'notes' => $assignment->notes,
                    'is_active' => $assignment->returned_at === null,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Herramientas del proyecto obtenidas correctamente',
                'meta' => [
                    'project_id' => $projectId,
                    'total' => $data->count(),
                    'active' => $data->where('is_active', true)->count(),
                    'returned' => $data->where('is_active', false)->count(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('ProjectToolController@toolsByProject: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener herramientas del proyecto',
            ], 500);
        }
    }
}
