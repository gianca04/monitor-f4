@props(['details', 'locations'])



{{-- DENSE FUNCTIONAL TABLE (EXCEL-LIKE) - FILAMENT V3 STYLE --}}
<div class="bk-card flex flex-col text-xs">

    <div class="overflow-x-auto max-h-[70vh] overflow-y-auto custom-scrollbar">
        <table class="bk-table relative table-fixed" style="min-width: 1600px;">
            <thead
                class="sticky top-0 z-30 bg-gray-50/95 backdrop-blur-sm dark:bg-gray-900/95 ring-1 ring-gray-200 dark:ring-white/10 shadow-sm">
                <tr class="divide-x divide-gray-200 dark:divide-white/10">
                    {{-- Información General --}}
                    <th class="fi-ta-header-cell px-3 py-2.5 text-center select-none relative group/th"
                        style="width: 100px;">
                        <span
                            class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Tipo</span>
                        <div
                            class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-primary-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer">
                        </div>
                    </th>
                    <th class="fi-ta-header-cell px-3 py-2.5 text-left select-none relative group/th"
                        style="width: 350px;">
                        <span
                            class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Item
                            / Descripción</span>
                        <div
                            class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-primary-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer">
                        </div>
                    </th>
                    <th class="fi-ta-header-cell px-2 py-2.5 text-center select-none relative group/th"
                        style="width: 55px;">
                        <span
                            class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Und</span>
                        <div
                            class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-primary-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer">
                        </div>
                    </th>
                    <th class="fi-ta-header-cell px-2 py-2.5 text-center select-none relative group/th"
                        style="width: 60px;">
                        <span
                            class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Cant.</span>
                        <div
                            class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-primary-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer">
                        </div>
                    </th>
                    <th class="fi-ta-header-cell px-2 py-2.5 text-center select-none relative group/th"
                        style="width: 80px;">
                        <span
                            class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Precio
                            U.</span>
                        <div
                            class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-primary-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer">
                        </div>
                    </th>
                    <th class="fi-ta-header-cell px-2 py-2.5 text-center select-none relative group/th"
                        style="width: 70px;">
                        <span
                            class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Previo</span>
                        <div
                            class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-primary-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer">
                        </div>
                    </th>

                    {{-- Acción Principal --}}
                    <th class="fi-ta-header-cell px-2 py-2.5 text-center bg-primary-50/50 dark:bg-primary-500/10 border-t-2 border-t-primary-500 select-none relative group/th"
                        style="width: 90px;">
                        <span
                            class="text-[11px] font-bold uppercase tracking-wider text-primary-600 dark:text-primary-400">Despachar</span>
                        <div
                            class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-primary-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer">
                        </div>
                    </th>

                    {{-- Datos de Compra (Agrupados visualmente por el borde superior) --}}
                    <th class="fi-ta-header-cell px-2 py-2.5 text-center select-none relative border-t-2 border-t-warning-400 bg-gray-50/30 group/th"
                        style="width: 60px;">
                        <span
                            class="text-[10px] font-semibold uppercase tracking-wider text-warning-600 dark:text-warning-400"
                            title="¿Compra Externa?">EXTERIOR</span>
                        <div
                            class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-warning-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer">
                        </div>
                    </th>
                    <th class="fi-ta-header-cell px-2 py-2.5 text-center select-none relative border-t-2 border-t-warning-400 bg-gray-50/30 group/th"
                        style="width: 75px;">
                        <span
                            class="text-[10px] font-semibold uppercase tracking-wider text-warning-600 dark:text-warning-400">S/
                            Costo</span>
                        <div
                            class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-warning-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer">
                        </div>
                    </th>
                    <th class="fi-ta-header-cell px-2 py-2.5 text-left select-none relative border-t-2 border-t-warning-400 bg-gray-50/30 group/th"
                        style="width: 90px;">
                        <span
                            class="text-[10px] font-semibold uppercase tracking-wider text-warning-600 dark:text-warning-400">Comprob.</span>
                        <div
                            class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-warning-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer">
                        </div>
                    </th>
                    <th class="fi-ta-header-cell px-2 py-2.5 text-left select-none relative border-t-2 border-t-warning-400 bg-gray-50/30 group/th"
                        style="width: 160px;">
                        <span
                            class="text-[10px] font-semibold uppercase tracking-wider text-warning-600 dark:text-warning-400">Proveedor</span>
                        <div
                            class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-warning-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer">
                        </div>
                    </th>

                    {{-- Notas y Series --}}
                    <th class="fi-ta-header-cell px-3 py-2.5 text-left select-none relative group/th"
                        style="width: 180px;">
                        <span
                            class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Nota</span>
                        <div
                            class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-primary-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer">
                        </div>
                    </th>
                    <th class="fi-ta-header-cell px-3 py-2.5 text-left select-none relative group/th"
                        style="width: 130px;">
                        <span
                            class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Serie</span>
                        <div
                            class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-primary-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer">
                        </div>
                    </th>

                    {{-- Acciones Sticky --}}
                    <th class="fi-ta-header-cell p-0 bg-gray-50 dark:bg-gray-900 sticky right-0 z-[40] shadow-[-4px_0_10px_rgba(0,0,0,0.05)] dark:shadow-[-4px_0_10px_rgba(0,0,0,0.3)] border-l border-gray-200 dark:border-white/10"
                        style="width: 70px; min-width: 70px;"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-white/5 bg-white dark:bg-gray-900">
                @foreach ($details as $i => $item)
                    @php
                        $completado = ($item['entregado'] ?? 0) >= ($item['quantity'] ?? 0);
                        $typeName = $item['type_name'] ?? 'N/A';
                    @endphp
                    {{-- Zebra striping suave y estado completado --}}
                    <tr
                        class="divide-x divide-gray-200 dark:divide-white/5 transition duration-150 even:bg-gray-50/40 dark:even:bg-white/[0.02] hover:bg-primary-50/30 dark:hover:bg-primary-500/5 {{ $completado ? 'opacity-60 bg-gray-50 dark:bg-gray-800' : '' }}">

                        <td class="p-2 text-center align-middle">
                            <span class="bk-badge bk-badge--neutral text-[10px]">
                                {{ $typeName }}
                            </span>
                        </td>
                        <td class="p-2 align-middle truncate text-sm {{ $completado ? 'line-through text-gray-500' : 'text-gray-900 dark:text-gray-100 font-medium' }}"
                            title="{{ $item['product_name'] ?? '-' }}">
                            {{ $item['product_name'] ?? '-' }}
                        </td>
                        <td class="p-2 text-center align-middle text-gray-500 dark:text-gray-400">
                            {{ $item['unit_name'] ?? '-' }}
                        </td>
                        <td class="p-2 text-center align-middle font-semibold text-gray-900 dark:text-white">
                            {{ floatval($item['quantity'] ?? 0) }}
                        </td>
                        <td class="p-2 text-center align-middle text-gray-600 dark:text-gray-300">
                            {{ number_format($item['unit_price'] ?? 0, 2) }}
                        </td>
                        <td
                            class="p-2 text-center align-middle font-medium {{ $item['entregado'] > 0 ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400' }}">
                            {{ $item['entregado'] ?? 0 }}
                        </td>

                        {{-- Input Principal (Despachar) --}}
                        <td class="p-1 align-middle bg-primary-50/10 dark:bg-primary-500/5 h-full relative group">
                            @if ($completado)
                                <div
                                    class="w-full h-full flex items-center justify-center text-success-600 dark:text-success-400 min-h-[32px]">
                                    <span class="material-symbols-outlined text-[20px]">check_circle</span>
                                </div>
                            @else
                                <input type="number"
                                    class="qty-input w-full h-[32px] rounded text-center text-sm font-bold border border-transparent hover:border-gray-200 dark:hover:border-gray-700 focus:bg-white dark:focus:bg-gray-800 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 text-primary-600 dark:text-primary-400 bg-transparent px-1 transition-all"
                                    data-index="{{ $i }}" max="{{ $item['quantity'] - ($item['entregado'] ?? 0) }}" min="0"
                                    value="0" />
                            @endif
                        </td>

                        {{-- Datos de compra inputs --}}
                        <td class="p-1 align-middle text-center">
                            <input type="checkbox" data-index="{{ $i }}" {{ $completado ? 'disabled' : '' }}
                                class="is-external-checkbox w-4 h-4 text-primary-600 rounded border-gray-300 bg-gray-50 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 transition-all cursor-pointer disabled:opacity-50" />
                        </td>
                        <td class="p-1 align-middle">
                            <input type="number" step="0.01" data-index="{{ $i }}" {{ $completado ? 'disabled' : '' }}
                                class="price-unit-input w-full h-[32px] rounded text-[12px] border border-transparent hover:border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500 bg-transparent px-2 placeholder:text-gray-400 text-center dark:text-white dark:hover:border-gray-700 dark:focus:bg-gray-800 transition-all disabled:opacity-50"
                                placeholder="0.00" />
                        </td>
                        <td class="p-1 align-middle">
                            <input type="text" data-index="{{ $i }}" {{ $completado ? 'disabled' : '' }}
                                class="receipt-input w-full h-[32px] rounded text-[12px] border border-transparent hover:border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500 bg-transparent px-2 placeholder:text-gray-400 dark:text-white dark:hover:border-gray-700 dark:focus:bg-gray-800 transition-all disabled:opacity-50"
                                placeholder="F001-..." />
                        </td>
                        <td class="p-1 align-middle">
                            <input type="text" data-index="{{ $i }}" {{ $completado ? 'disabled' : '' }}
                                class="supplier-input w-full h-[32px] rounded text-[12px] border border-transparent hover:border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500 bg-transparent px-2 placeholder:text-gray-400 dark:text-white dark:hover:border-gray-700 dark:focus:bg-gray-800 transition-all disabled:opacity-50"
                                placeholder="RUC / Razón Social" />
                        </td>

                        {{-- Nota --}}
                        <td class="p-1 align-middle">
                            <textarea data-index="{{ $i }}" {{ $completado ? 'disabled' : '' }}
                                class="comment-input w-full rounded text-[12px] border border-transparent hover:border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500 bg-transparent px-2 py-1.5 placeholder:text-gray-400 dark:text-white dark:hover:border-gray-700 dark:focus:bg-gray-800 transition-all resize-none disabled:opacity-50 custom-scrollbar"
                                placeholder="Comentarios..." rows="1">{{ $item['comment'] ?? '' }}</textarea>
                        </td>

                        {{-- Serie --}}
                        <td class="p-1 align-middle">
                            <div class="flex flex-col gap-1">
                                @if ($item['is_tool'] ?? false)
                                    <select data-index="{{ $i }}" {{ $completado ? 'disabled' : '' }}
                                        class="tool-unit-select w-full rounded h-[24px] text-[11px] border border-gray-200 dark:border-gray-700 text-gray-700 bg-gray-50 dark:bg-gray-800 dark:text-gray-300 focus:ring-1 focus:ring-primary-500 py-0 pl-2 pr-6 cursor-pointer transition-all">
                                        <option value="">Sel. Unidad...</option>
                                        @foreach ($item['available_units'] ?? [] as $unit)
                                            <option value="{{ $unit['id'] }}" {{ ($item['tool_unit_id'] ?? null) == $unit['id'] ? 'selected' : '' }}>{{ $unit['internal_code'] ?? 'S/C' }}</option>
                                        @endforeach
                                    </select>
                                @endif
                                <input type="text" data-index="{{ $i }}" {{ $completado ? 'disabled' : '' }}
                                    class="w-full h-[28px] rounded text-[12px] border border-transparent hover:border-gray-200 focus:bg-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500 bg-transparent px-2 placeholder:text-gray-400 dark:text-white dark:hover:border-gray-700 dark:focus:bg-gray-800 transition-all disabled:opacity-50"
                                    placeholder="N° Serie..." value="{{ $item['serie'] ?? '' }}" />
                            </div>
                        </td>

                        {{-- ACCIONES STICKY --}}
                        <td
                            class="p-0 align-middle bg-white dark:bg-gray-900 sticky right-0 z-10 shadow-[-4px_0_10px_rgba(0,0,0,0.05)] dark:shadow-[-4px_0_10px_rgba(0,0,0,0.3)] border-l border-gray-200 dark:border-white/10 group-hover:bg-primary-50/30 dark:group-hover:bg-gray-800 transition-colors">
                            <div
                                class="flex items-center justify-center h-full divide-x divide-gray-100 dark:divide-gray-800">
                                <button type="button"
                                    class="btn-add-tx w-full h-full text-gray-400 transition hover:text-success-600 hover:bg-success-50 dark:hover:bg-success-500/10 flex justify-center items-center py-2"
                                    data-index="{{ $i }}" data-name="{{ htmlspecialchars($item['product_name'] ?? $item['name']) }}"
                                    title="Añadir / Ver más">
                                    <span class="material-symbols-outlined text-[18px]">add</span>
                                </button>
                                <button type="button"
                                    class="btn-history w-full h-full text-gray-400 transition hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-500/10 flex justify-center items-center py-2"
                                    data-req-id="{{ $item['project_requirement_id'] }}"
                                    data-name="{{ htmlspecialchars($item['product_name'] ?? '') }}" title="Ver Historial">
                                    <span class="material-symbols-outlined text-[18px]">history</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Footer Summary (Sin cambios drásticos, solo refinamiento de bordes) --}}
    <div
        class="bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-white/10 px-4 py-3 flex items-center justify-between text-xs z-20">
        <span class="font-medium text-gray-500 dark:text-gray-400">
            Total Registros: <span class="text-gray-900 dark:text-white font-bold">{{ count($details) }}</span>
        </span>
        <div class="flex items-center gap-3">
            <span class="font-medium text-gray-500 dark:text-gray-400">Progreso Operativo:</span>
            <div class="bk-progress w-40 shadow-inner">
                <div class="progress-bar bk-progress-bar" style="width: 0%"></div>
            </div>
            <span class="progress-text font-bold text-primary-600 dark:text-primary-400 w-8 text-right">0%</span>
        </div>
    </div>
</div>