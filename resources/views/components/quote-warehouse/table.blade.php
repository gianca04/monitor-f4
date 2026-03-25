@props(['details', 'locations'])

{{-- DENSE FUNCTIONAL TABLE (FLOWBITE INSPIRED) --}}
<div
    class="relative overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg border border-gray-200 dark:border-gray-700">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    @php
                        $headers = [
                            ['label' => 'Tipo', 'align' => 'text-center'],
                            ['label' => 'Item / Descripción', 'align' => 'text-left'],
                            ['label' => 'Und', 'align' => 'text-center'],
                            ['label' => 'Cant.', 'align' => 'text-center'],
                            ['label' => 'Precio U.', 'align' => 'text-center'],
                            ['label' => 'Previo', 'align' => 'text-center'],
                            ['label' => 'Despachar', 'align' => 'text-center'],
                            ['label' => 'EXTERIOR', 'align' => 'text-center'],
                            ['label' => 'S/ Costo', 'align' => 'text-center'],
                            ['label' => 'Comprob.', 'align' => 'text-left'],
                            ['label' => 'Proveedor', 'align' => 'text-left'],
                            ['label' => 'Nota', 'align' => 'text-left'],
                            ['label' => 'Serie', 'align' => 'text-left'],
                        ];
                    @endphp

                    @foreach($headers as $header)
                        <th
                            class="px-4 py-3 border-x border-gray-200 dark:border-gray-600 relative group/th {{ $header['align'] }}">
                            <span class="font-bold whitespace-nowrap">{{ $header['label'] }}</span>
                            <div
                                class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize opacity-0 group-hover/th:opacity-100 col-resizer bg-gray-300">
                            </div>
                        </th>
                    @endforeach

                    <th scope="col"
                        class="px-2 py-3 text-center sticky right-0 z-30 bg-gray-50 dark:bg-gray-700 font-bold uppercase tracking-wider border-l border-b border-gray-300 dark:border-gray-600 backdrop-blur-sm">
                        ACCIONES
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($details as $i => $item)
                    @php
                        $completado = ($item['entregado'] ?? 0) >= ($item['quantity'] ?? 0);
                        $typeName = $item['type_name'] ?? 'N/A';
                    @endphp
                    <tr
                        class="border-b dark:border-gray-700 {{ $completado ? 'opacity-60 bg-gray-50 dark:bg-gray-900' : 'hover:bg-gray-50 dark:hover:bg-gray-700' }}">

                        <td class="px-4 py-2 border-x border-gray-200 dark:border-gray-700 text-center">
                            <span>{{ $typeName }}</span>
                        </td>
                        <td class="px-4 py-2 border-x border-gray-200 dark:border-gray-700 font-medium text-gray-900 dark:text-white whitespace-nowrap {{ $completado ? 'line-through text-gray-500' : '' }}"
                            title="{{ $item['product_name'] ?? '-' }}">
                            {{ $item['product_name'] ?? '-' }}
                        </td>
                        <td class="px-4 py-2 border-x border-gray-200 dark:border-gray-700 text-center uppercase">
                            {{ $item['unit_name'] ?? '-' }}
                        </td>
                        <td
                            class="px-4 py-2 border-x border-gray-200 dark:border-gray-700 text-center font-bold text-gray-900 dark:text-white">
                            {{ floatval($item['quantity'] ?? 0) }}
                        </td>
                        <td class="px-4 py-2 border-x border-gray-200 dark:border-gray-700 text-center font-medium">
                            {{ number_format($item['unit_price'] ?? 0, 2) }}
                        </td>
                        <td class="px-4 py-2 border-x border-gray-200 dark:border-gray-700 text-center">
                            {{ $item['entregado'] ?? 0 }}
                        </td>

                        {{-- Input Principal (Despachar) --}}
                        <td class="px-4 py-2 border-x border-gray-200 dark:border-gray-700 align-middle relative">
                            @if ($completado)
                                <div class="w-full flex items-center justify-center text-green-500">
                                    <span class="material-symbols-outlined text-[20px]">check_circle</span>
                                </div>
                            @else
                                <input type="number"
                                    class="qty-input w-24 mx-auto h-8 flex items-center justify-center rounded text-center text-sm font-bold border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                                    data-index="{{ $i }}" max="{{ $item['quantity'] - ($item['entregado'] ?? 0) }}" min="0"
                                    value="0" />
                            @endif
                        </td>

                        {{-- Datos de compra inputs --}}
                        <td class="px-4 py-2 border-x border-gray-200 dark:border-gray-700 text-center">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" data-index="{{ $i }}" {{ $completado ? 'disabled' : '' }}
                                    class="sr-only peer is-external-checkbox" />
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600 peer-disabled:opacity-50">
                                </div>
                            </label>
                        </td>
                        <td class="px-4 py-2 border-x border-gray-200 dark:border-gray-700">
                            <input type="number" step="0.01" data-index="{{ $i }}" {{ $completado ? 'disabled' : '' }}
                                class="price-unit-input w-24 h-8 rounded text-[12px] border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500 px-2 text-center disabled:opacity-50"
                                placeholder="0.00" />
                        </td>
                        <td class="px-4 py-2 border-x border-gray-200 dark:border-gray-700">
                            <input type="text" data-index="{{ $i }}" {{ $completado ? 'disabled' : '' }}
                                class="receipt-input w-32 h-8 rounded text-[12px] border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500 px-2 disabled:opacity-50"
                                placeholder="F001-..." />
                        </td>
                        <td class="px-4 py-2 border-x border-gray-200 dark:border-gray-700">
                            <input type="text" data-index="{{ $i }}" {{ $completado ? 'disabled' : '' }}
                                class="supplier-input w-40 h-8 rounded text-[12px] border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500 px-2 disabled:opacity-50"
                                placeholder="RUC / Razón Social" />
                        </td>

                        {{-- Nota --}}
                        <td class="px-4 py-2 border-x border-gray-200 dark:border-gray-700">
                            <textarea data-index="{{ $i }}" {{ $completado ? 'disabled' : '' }}
                                class="comment-input w-48 rounded text-[12px] border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500 px-2 py-1.5 resize-none disabled:opacity-50"
                                placeholder="Comentarios..." rows="1">{{ $item['comment'] ?? '' }}</textarea>
                        </td>

                        {{-- Serie --}}
                        <td class="px-4 py-2 border-x border-gray-200 dark:border-gray-700">
                            <div class="flex flex-col gap-1">
                                @if ($item['is_tool'] ?? false)
                                    <select data-index="{{ $i }}" {{ $completado ? 'disabled' : '' }}
                                        class="tool-unit-select w-full rounded h-6 text-[11px] border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500 py-0 pl-2 pr-6 disabled:opacity-50">
                                        <option value="">Sel. Unidad...</option>
                                        @foreach ($item['available_units'] ?? [] as $unit)
                                                            <option value="{{ $unit['id'] }}" {{ ($item['tool_unit_id'] ?? null) == $unit['id']
                                            ? 'selected' : '' }}>{{ $unit['internal_code'] ?? 'S/C' }}</option>
                                        @endforeach
                                    </select>
                                @endif
                                <input type="text" data-index="{{ $i }}" {{ $completado ? 'disabled' : '' }}
                                    class="w-full h-7 rounded text-[12px] border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500 px-2 disabled:opacity-50"
                                    placeholder="N° Serie..." value="{{ $item['serie'] ?? '' }}" />
                            </div>
                        </td>

                        {{-- ACCIONES STICKY --}}
                        <td
                            class="px-2 py-2 align-middle bg-gray-50 dark:bg-gray-800 sticky right-0 z-10 border-l border-gray-300 dark:border-gray-600 {{ $completado ? '' : 'hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                            <div class="flex items-center justify-center gap-1.5">
                                <button type="button"
                                    class="btn-add-tx inline-flex items-center justify-center p-2 text-blue-600 rounded-lg hover:bg-blue-50 hover:text-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-blue-400 dark:focus:ring-blue-800 transition-all"
                                    data-index="{{ $i }}"
                                    data-name="{{ htmlspecialchars($item['product_name'] ?? $item['name']) }}"
                                    title="Añadir">
                                    <span class="material-symbols-outlined text-[20px]">add_circle</span>
                                </button>
                                <button type="button"
                                    class="btn-history inline-flex items-center justify-center p-2 text-gray-500 rounded-lg hover:bg-gray-100 hover:text-gray-900 focus:ring-4 focus:outline-none focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700 transition-all"
                                    data-req-id="{{ $item['project_requirement_id'] }}"
                                    data-name="{{ htmlspecialchars($item['product_name'] ?? '') }}" title="Ver Historial">
                                    <span class="material-symbols-outlined text-[20px]">history</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div
        class="p-4 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center text-sm text-gray-500 dark:text-gray-400">
        <span>Total registros: <span class="font-bold text-gray-900 dark:text-white">{{ count($details) }}</span></span>

        <div class="flex items-center gap-3">
            <span class="font-medium">Progreso general:</span>
            <div class="w-48 bg-gray-200 dark:bg-gray-700 h-2 rounded-full overflow-hidden">
                <div class="progress-bar bg-green-600 h-full rounded-full transition-all duration-500"
                    style="width: 0%"></div>
            </div>
            <span class="progress-text font-bold text-gray-900 dark:text-white w-10 text-right">0%</span>
        </div>
    </div>
</div>