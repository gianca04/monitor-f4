@props([
    'transactions' => collect([])
])

<div class="bk-card flex flex-col text-xs">
    <div class="overflow-x-auto max-h-[70vh] overflow-y-auto custom-scrollbar">
        <table class="bk-table relative table-fixed" style="min-width: 2800px;">
            <thead class="sticky top-0 z-30 bg-gray-50/95 backdrop-blur-sm dark:bg-gray-900/95 ring-1 ring-gray-200 dark:ring-white/10 shadow-sm">
                <tr class="divide-x divide-gray-200 dark:divide-white/10 border-b-2 border-primary-500">
                    <th class="fi-ta-header-cell px-2 py-2.5 text-center select-none relative group/th" style="width: 50px;">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-primary-600 dark:text-primary-400">#</span>
                        <div class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-primary-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer"></div>
                    </th>
                    <th class="fi-ta-header-cell px-3 py-2.5 text-left select-none relative group/th" style="width: 120px;">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-primary-600 dark:text-primary-400">Correlativo</span>
                        <div class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-primary-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer"></div>
                    </th>
                    <th class="fi-ta-header-cell px-3 py-2.5 text-left select-none relative group/th" style="width: 300px;">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-primary-600 dark:text-primary-400">Descripción</span>
                        <div class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-primary-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer"></div>
                    </th>
                    <th class="fi-ta-header-cell px-3 py-2.5 text-center select-none relative group/th" style="width: 120px;">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-primary-600 dark:text-primary-400">N° Guía</span>
                        <div class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-primary-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer"></div>
                    </th>
                    <th class="fi-ta-header-cell px-3 py-2.5 text-center select-none relative group/th" style="width: 110px;">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-primary-600 dark:text-primary-400">F. Salida</span>
                        <div class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-primary-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer"></div>
                    </th>
                    <th class="fi-ta-header-cell px-3 py-2.5 text-center select-none relative group/th" style="width: 110px;">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-primary-600 dark:text-primary-400">F. Ingreso</span>
                        <div class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-primary-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer"></div>
                    </th>
                    <th class="fi-ta-header-cell px-3 py-2.5 text-center select-none relative group/th" style="width: 100px;">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-primary-600 dark:text-primary-400">Tipo</span>
                        <div class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-primary-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer"></div>
                    </th>
                    <th class="fi-ta-header-cell px-2 py-2.5 text-center select-none relative group/th" style="width: 60px;">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-primary-600 dark:text-primary-400">Und</span>
                        <div class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-primary-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer"></div>
                    </th>
                    <th class="fi-ta-header-cell px-3 py-2.5 text-center select-none relative group/th bg-primary-50/50 dark:bg-primary-900/10" style="width: 80px;">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-primary-600 dark:text-primary-400">Cant.</span>
                        <div class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-primary-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer"></div>
                    </th>
                    
                    {{-- Datos del Cliente (Agrupados Visualmente) --}}
                    <th class="fi-ta-header-cell px-3 py-2.5 text-left select-none relative group/th bg-gray-50/30" style="width: 180px;">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Cliente</span>
                        <div class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-primary-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer"></div>
                    </th>
                    <th class="fi-ta-header-cell px-3 py-2.5 text-left select-none relative group/th bg-gray-50/30" style="width: 170px;">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Tienda</span>
                        <div class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-primary-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer"></div>
                    </th>
                    <th class="fi-ta-header-cell px-3 py-2.5 text-left select-none relative group/th bg-gray-50/30" style="width: 170px;">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Ubicación</span>
                        <div class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-primary-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer"></div>
                    </th>

                    {{-- Costos Previstos vs Reales --}}
                    <th class="fi-ta-header-cell px-3 py-2.5 text-right select-none relative border-t-2 border-t-gray-400 bg-gray-100/30 group/th" style="width: 110px;">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500">P.U. Prev.</span>
                        <div class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-gray-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer"></div>
                    </th>
                    <th class="fi-ta-header-cell px-3 py-2.5 text-right select-none relative border-t-2 border-t-gray-400 bg-gray-100/30 group/th" style="width: 110px;">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500">PT. Prev.</span>
                        <div class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-gray-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer"></div>
                    </th>
                    <th class="fi-ta-header-cell px-3 py-2.5 text-right select-none relative border-t-2 border-t-primary-400 bg-primary-50/20 group/th" style="width: 110px;">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-primary-600">P.U. Real</span>
                        <div class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-primary-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer"></div>
                    </th>
                    <th class="fi-ta-header-cell px-3 py-2.5 text-right select-none relative border-t-2 border-t-primary-400 bg-primary-50/20 group/th" style="width: 110px;">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-primary-600">PT. Real</span>
                        <div class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-primary-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer"></div>
                    </th>
                    <th class="fi-ta-header-cell px-3 py-2.5 text-right select-none relative border-t-2 border-t-warning-400 bg-warning-50/20 group/th" style="width: 110px;">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-warning-600">C. Adic.</span>
                        <div class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-warning-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer"></div>
                    </th>
                    <th class="fi-ta-header-cell px-3 py-2.5 text-left select-none relative border-t-2 border-t-warning-400 bg-warning-50/20 group/th" style="width: 160px;">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-warning-600">Desc. Costo</span>
                        <div class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-warning-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer"></div>
                    </th>
                    <th class="fi-ta-header-cell px-3 py-2.5 text-right select-none relative border-t-2 border-t-success-500 bg-success-50/30 dark:bg-success-900/10 group/th" style="width: 120px;">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-success-600">TOTAL</span>
                        <div class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-success-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer"></div>
                    </th>

                    {{-- Compras Externas (Agrupados) --}}
                    <th class="fi-ta-header-cell px-3 py-2.5 text-center select-none relative border-t-2 border-t-info-400 bg-info-50/20 group/th" style="width: 80px;">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-info-600">EXT.</span>
                        <div class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-info-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer"></div>
                    </th>
                    <th class="fi-ta-header-cell px-3 py-2.5 text-left select-none relative border-t-2 border-t-info-400 bg-info-50/20 group/th" style="width: 160px;">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-info-600">Proveedor</span>
                        <div class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-info-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer"></div>
                    </th>
                    <th class="fi-ta-header-cell px-3 py-2.5 text-left select-none relative border-t-2 border-t-info-400 bg-info-50/20 group/th" style="width: 120px;">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-info-600">Factura</span>
                        <div class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-info-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer"></div>
                    </th>

                    <th class="fi-ta-header-cell px-3 py-2.5 text-center select-none relative group/th bg-warning-50/20" style="width: 120px;">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-warning-700">Estado</span>
                        <div class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-warning-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer"></div>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-white/5 bg-white dark:bg-gray-900">
                @forelse($transactions as $index => $transaction)
                    <tr class="divide-x divide-gray-200 dark:divide-white/5 transition duration-150 even:bg-gray-50/40 dark:even:bg-white/[0.02] hover:bg-primary-50/30 dark:hover:bg-primary-500/5 hover:shadow-[inset_0_0_0_1px_rgba(59,130,246,0.3)] group cursor-default">
                        <td class="p-1.5 text-center align-middle text-gray-400 font-mono text-[10px]">
                            {{ $index + 1 }}
                        </td>
                        <td class="p-1.5 align-middle truncate font-semibold text-primary-600 dark:text-primary-400">
                            {{ $transaction->projectRequirement?->project?->service_code ?? '-' }}
                        </td>
                        <td class="p-1.5 align-middle truncate text-gray-900 dark:text-white" title="{{ $transaction->projectRequirement?->name ?? '' }}">
                            {{ $transaction->projectRequirement?->name ?? '-' }}
                        </td>
                        <td class="p-1.5 align-middle text-center font-mono font-medium text-gray-700 dark:text-gray-300">
                            {{ $transaction->dispatchGuide?->guide_number ?? '-' }}
                        </td>
                        <td class="p-1.5 align-middle text-center font-mono text-[10px] tracking-tight">
                            {{ $transaction->dispatchGuide?->transfer_date ? \Carbon\Carbon::parse($transaction->dispatchGuide->transfer_date)->format('d/m/y') : '-' }}
                        </td>
                        <td class="p-1.5 align-middle text-center font-mono text-[10px] tracking-tight">
                            {{ $transaction->dispatchGuide?->store_entry_date ? \Carbon\Carbon::parse($transaction->dispatchGuide->store_entry_date)->format('d/m/y') : '-' }}
                        </td>
                        <td class="p-1.5 align-middle text-center">
                            <span class="bk-badge bk-badge--neutral text-[9px] uppercase">
                                {{ $transaction->projectRequirement?->type?->value ?? ($transaction->projectRequirement?->type ?? '-') }}
                            </span>
                        </td>
                        <td class="p-1.5 align-middle text-center text-gray-500 dark:text-gray-400 uppercase text-[10px] font-bold">
                            {{ $transaction->projectRequirement?->unit?->name ?? '-' }}
                        </td>
                        <td class="p-1.5 align-middle text-center font-bold text-primary-700 dark:text-primary-400 bg-primary-50/30 dark:bg-primary-900/10">
                            {{ number_format($transaction->quantity, 2) }}
                        </td>

                        {{-- Cliente / Tienda --}}
                        <td class="p-1.5 align-middle truncate text-[11px]" title="{{ $transaction->projectRequirement?->project?->subClient?->client?->business_name ?? '' }}">
                            {{ $transaction->projectRequirement?->project?->subClient?->client?->business_name ?? '-' }}
                        </td>
                        <td class="p-1.5 align-middle truncate text-[11px]" title="{{ $transaction->projectRequirement?->project?->subClient?->name ?? '' }}">
                            {{ $transaction->projectRequirement?->project?->subClient?->name ?? '-' }}
                        </td>
                        <td class="p-1.5 align-middle truncate text-[11px] text-gray-500" title="{{ $transaction->projectRequirement?->project?->subClient?->location ?? '' }}">
                            {{ $transaction->projectRequirement?->project?->subClient?->location ?? '-' }}
                        </td>

                        {{-- Financieros Previstos --}}
                        <td class="p-1.5 align-middle text-right font-mono text-gray-400">
                            {{ number_format($transaction->projectRequirement?->price_unit ?? 0, 2) }}
                        </td>
                        <td class="p-1.5 align-middle text-right font-mono text-gray-500 bg-gray-50/50">
                            {{ number_format($transaction->projectRequirement?->subtotal ?? 0, 2) }}
                        </td>

                        {{-- Financieros Reales --}}
                        <td class="p-1.5 align-middle text-right font-mono font-medium text-primary-700">
                            {{ number_format($transaction->price_unit ?? 0, 2) }}
                        </td>
                        <td class="p-1.5 align-middle text-right font-mono font-medium text-primary-800 bg-primary-50/10">
                            {{ number_format($transaction->item_total ?? 0, 2) }}
                        </td>
                        <td class="p-1.5 align-middle text-right font-mono text-warning-600 bg-warning-50/10">
                            {{ number_format($transaction->additional_cost ?? 0, 2) }}
                        </td>
                        <td class="p-1.5 align-middle truncate text-[10px] text-gray-500" title="{{ $transaction->cost_description ?? '' }}">
                            {{ $transaction->cost_description ?? '-' }}
                        </td>
                        <td class="p-1.5 align-middle text-right font-mono font-bold text-success-700 bg-success-50/50">
                            {{ number_format($transaction->subtotal ?? 0, 2) }}
                        </td>

                        {{-- Compras Externas --}}
                        <td class="p-1.5 align-middle text-center bg-info-50/5">
                            @if($transaction->is_external_purchase)
                                <span class="material-symbols-outlined text-[16px] text-info-600">check_box</span>
                            @else
                                <span class="material-symbols-outlined text-[16px] text-gray-300">check_box_outline_blank</span>
                            @endif
                        </td>
                        <td class="p-1.5 align-middle truncate text-[11px]">{{ $transaction->supplier_name ?? '-' }}</td>
                        <td class="p-1.5 align-middle text-left font-mono text-[10px]">{{ $transaction->receipt_number ?? '-' }}</td>
                        
                        {{-- Estado Devolución --}}
                        <td class="p-1.5 align-middle text-center">
                            <span class="bk-badge text-[9px] bg-warning-100 text-warning-700 border-warning-200">
                                PENDIENTE
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="23" class="p-8 text-center text-gray-500 dark:text-gray-400 bg-gray-50/50">
                            <div class="flex flex-col items-center justify-center space-y-2">
                                <span class="material-symbols-outlined text-gray-300 text-4xl">inventory_2</span>
                                <p class="font-medium">No se encontraron transacciones registradas.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="bg-gray-50/80 dark:bg-gray-900/50 border-t border-gray-200 dark:border-white/10 px-4 py-2 flex items-center justify-between text-[11px] z-20">
        <span class="font-semibold text-gray-500 dark:text-gray-400">
            Total Transacciones: <span class="text-gray-900 dark:text-white font-bold ml-1">{{ count($transactions) }}</span>
        </span>
    </div>
</div>
