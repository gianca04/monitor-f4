@props([
    'transactions' => collect([])
])

<div class="mt-4 overflow-x-auto rounded-lg border border-gray-200 shadow-sm dark:border-gray-800 custom-scrollbar">
    <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
        <thead class="bg-gray-50 text-xs uppercase text-gray-700 dark:bg-gray-800 dark:text-gray-300">
            <tr>
                <th scope="col" class="px-4 py-3 font-semibold whitespace-nowrap">ITEM</th>
                <th scope="col" class="px-4 py-3 font-semibold whitespace-nowrap">CORRELATIVO</th>
                <th scope="col" class="px-4 py-3 font-semibold whitespace-nowrap">DESCRIPCIÓN</th>
                <th scope="col" class="px-4 py-3 font-semibold whitespace-nowrap">N° GUÍA</th>
                <th scope="col" class="px-4 py-3 font-semibold whitespace-nowrap">FECHA DE SALIDA</th>
                <th scope="col" class="px-4 py-3 font-semibold whitespace-nowrap">FECHA DE INGRESO</th>
                <th scope="col" class="px-4 py-3 font-semibold whitespace-nowrap">TIPO DE RECURSO</th>
                <th scope="col" class="px-4 py-3 font-semibold whitespace-nowrap">UNIDAD</th>
                <th scope="col" class="px-4 py-3 font-semibold whitespace-nowrap text-right">CANTIDAD</th>
                <th scope="col" class="px-4 py-3 font-semibold whitespace-nowrap">CLIENTE</th>
                <th scope="col" class="px-4 py-3 font-semibold whitespace-nowrap">TIENDA</th>
                <th scope="col" class="px-4 py-3 font-semibold whitespace-nowrap">UBICACIÓN</th>
                <th scope="col" class="px-4 py-3 font-semibold whitespace-nowrap text-right">Precio Unit. Previsto (PEN)</th>
                <th scope="col" class="px-4 py-3 font-semibold whitespace-nowrap text-right">Precio Previsto (PEN)</th>
                <th scope="col" class="px-4 py-3 font-semibold whitespace-nowrap text-right">Precio Unit. Real (PEN)</th>
                <th scope="col" class="px-4 py-3 font-semibold whitespace-nowrap text-right">Precio Real (PEN)</th>
                <th scope="col" class="px-4 py-3 font-semibold whitespace-nowrap text-right">Costo Adicional (PEN)</th>
                <th scope="col" class="px-4 py-3 font-semibold whitespace-nowrap">Desc. Costo Adicional</th>
                <th scope="col" class="px-4 py-3 font-semibold whitespace-nowrap text-right">TOTAL (PEN)</th>
                <th scope="col" class="px-4 py-3 font-semibold whitespace-nowrap text-center">Compra Externa</th>
                <th scope="col" class="px-4 py-3 font-semibold whitespace-nowrap">Lugar de Compra</th>
                <th scope="col" class="px-4 py-3 font-semibold whitespace-nowrap">Factura / Guía</th>
                <th scope="col" class="px-4 py-3 font-semibold whitespace-nowrap text-center">Estado Devolución</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $index => $transaction)
                <tr class="border-b bg-white hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:hover:bg-gray-800/50 transition-colors">
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $index + 1 }}</td>
                    <td class="px-4 py-3 whitespace-nowrap font-medium text-primary-600 dark:text-primary-400">
                        {{ $transaction->projectRequirement?->project?->service_code ?? '-' }}
                    </td>
                    <td class="px-4 py-3 max-w-xs truncate" title="{{ $transaction->projectRequirement?->name ?? '' }}">
                        {{ $transaction->projectRequirement?->name ?? '-' }}
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">
                        {{ $transaction->dispatchGuide?->guide_number ?? '-' }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        {{ $transaction->dispatchGuide?->transfer_date ? \Carbon\Carbon::parse($transaction->dispatchGuide->transfer_date)->format('d/m/Y') : '-' }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        {{ $transaction->dispatchGuide?->store_entry_date ? \Carbon\Carbon::parse($transaction->dispatchGuide->store_entry_date)->format('d/m/Y') : '-' }}
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700 ring-1 ring-inset ring-gray-600/20 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-500/20">
                            {{ $transaction->projectRequirement?->type?->value ?? ($transaction->projectRequirement?->type ?? '-') }}
                        </span>
                    </td>
                    <td class="px-4 py-3">{{ $transaction->projectRequirement?->unit?->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white">
                        {{ number_format($transaction->quantity, 2) }}
                    </td>
                    <td class="px-4 py-3 truncate max-w-[150px]" title="{{ $transaction->projectRequirement?->project?->subClient?->client?->business_name ?? '' }}">
                        {{ $transaction->projectRequirement?->project?->subClient?->client?->business_name ?? '-' }}
                    </td>
                    <td class="px-4 py-3 truncate max-w-[150px]" title="{{ $transaction->projectRequirement?->project?->subClient?->name ?? '' }}">
                        {{ $transaction->projectRequirement?->project?->subClient?->name ?? '-' }}
                    </td>
                    <td class="px-4 py-3 truncate max-w-[150px]" title="{{ $transaction->projectRequirement?->project?->subClient?->location ?? '' }}">
                        {{ $transaction->projectRequirement?->project?->subClient?->location ?? '-' }}
                    </td>
                    <!-- Financieros (Planificación) -->
                    <td class="px-4 py-3 text-right font-mono text-xs text-gray-600 dark:text-gray-400">
                        S/ {{ number_format($transaction->projectRequirement?->price_unit ?? 0, 2) }}
                    </td>
                    <td class="px-4 py-3 text-right font-mono text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-50/50 dark:bg-gray-800/30">
                        S/ {{ number_format($transaction->projectRequirement?->subtotal ?? 0, 2) }}
                    </td>
                    
                    <!-- Financieros (Ejecución real) -->
                    <td class="px-4 py-3 text-right font-mono text-xs text-gray-600 dark:text-gray-400">
                        S/ {{ number_format($transaction->price_unit ?? 0, 2) }}
                    </td>
                    <td class="px-4 py-3 text-right font-mono text-xs font-medium text-gray-700 dark:text-gray-300">
                        S/ {{ number_format($transaction->item_total ?? 0, 2) }}
                    </td>
                    <td class="px-4 py-3 text-right font-mono text-xs text-warning-600 dark:text-warning-400">
                        S/ {{ number_format($transaction->additional_cost ?? 0, 2) }}
                    </td>
                    <td class="px-4 py-3 truncate max-w-[150px] text-xs" title="{{ $transaction->cost_description ?? '' }}">
                        {{ $transaction->cost_description ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-right font-mono text-sm font-bold text-success-600 dark:text-success-400 bg-success-50/30 dark:bg-success-900/10">
                        S/ {{ number_format($transaction->subtotal ?? 0, 2) }}
                    </td>
                    
                    <!-- Compras -->
                    <td class="px-4 py-3 text-center">
                        @if($transaction->is_external_purchase)
                            <span class="inline-flex items-center text-success-600">
                                <span class="material-symbols-outlined text-sm">check_circle</span>
                            </span>
                        @else
                            <span class="inline-flex items-center text-gray-400">
                                <span class="material-symbols-outlined text-sm">cancel</span>
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 truncate max-w-[150px]">{{ $transaction->supplier_name ?? '-' }}</td>
                    <td class="px-4 py-3 font-mono text-xs">{{ $transaction->receipt_number ?? '-' }}</td>
                    
                    <!-- Devolución -->
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex items-center rounded-md bg-warning-50 px-2 py-1 text-xs font-medium text-warning-700 ring-1 ring-inset ring-warning-600/20 dark:bg-warning-400/10 dark:text-warning-500 dark:ring-warning-400/20">
                            Pendiente
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="23" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                        <div class="flex flex-col items-center justify-center space-y-2">
                            <span class="material-symbols-outlined text-gray-400 text-4xl">inventory_2</span>
                            <p>No se encontraron transacciones registradas.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
