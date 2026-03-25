@props([
    'transactions' => collect([])
])

<div class="space-y-4">
    <div class="grid grid-cols-1 min-w-0">
        <div class="relative overflow-auto max-h-[450px] bg-white dark:bg-gray-800 shadow-xl border border-gray-200 dark:border-gray-700 rounded-xl scrollbar-thin scrollbar-track-transparent scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-600 hover:scrollbar-thumb-gray-400 dark:hover:scrollbar-thumb-gray-500">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400 table-auto border-separate border-spacing-0">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50/90 dark:bg-gray-700/90 dark:text-gray-400 sticky top-0 z-20 backdrop-blur-sm">
                <tr>
                    @php
                        $headers = [
                            ['label' => '#', 'align' => 'text-center'],
                            ['label' => 'Correlativo', 'align' => 'text-left'],
                            ['label' => 'Descripción', 'align' => 'text-left', 'width' => '400px'],
                            ['label' => 'N° Guía', 'align' => 'text-center'],
                            ['label' => 'F. Salida', 'align' => 'text-center'],
                            ['label' => 'F. Ingreso', 'align' => 'text-center'],
                            ['label' => 'Tipo', 'align' => 'text-center'],
                            ['label' => 'Und', 'align' => 'text-center'],
                            ['label' => 'Cant.', 'align' => 'text-center'],
                            ['label' => 'Cliente', 'align' => 'text-left'],
                            ['label' => 'Tienda', 'align' => 'text-left'],
                            ['label' => 'Ubicación', 'align' => 'text-left'],
                            ['label' => 'P.U. Prev.', 'align' => 'text-right'],
                            ['label' => 'PT. Prev.', 'align' => 'text-right'],
                            ['label' => 'P.U. Real', 'align' => 'text-right'],
                            ['label' => 'PT. Real', 'align' => 'text-right'],
                            ['label' => 'C. Adic.', 'align' => 'text-right'],
                            ['label' => 'Desc. Costo', 'align' => 'text-left'],
                            ['label' => 'TOTAL', 'align' => 'text-right'],
                            ['label' => 'EXT.', 'align' => 'text-center'],
                            ['label' => 'Proveedor', 'align' => 'text-left'],
                            ['label' => 'Factura', 'align' => 'text-left'],
                            ['label' => 'Estado', 'align' => 'text-center'],
                        ];
                    @endphp

                    @foreach($headers as $header)
                        <th class="px-4 py-3 border-r border-b border-gray-200 dark:border-gray-600 relative group/th {{ $header['align'] }}"
                            style="{{ isset($header['width']) ? 'min-width: ' . $header['width'] . ';' : '' }}">
                            <span class="font-bold whitespace-nowrap">{{ $header['label'] }}</span>
                            <div
                                class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize opacity-0 group-hover/th:opacity-100 col-resizer bg-gray-300">
                            </div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($transactions as $index => $transaction)
                    <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-4 py-2 border-r border-b border-gray-200 dark:border-gray-700 text-center">{{ $index + 1 }}</td>
                        <td class="px-4 py-2 border-r border-b border-gray-200 dark:border-gray-700 whitespace-nowrap">{{ $transaction->projectRequirement?->project?->request_number ?? '-' }}</td>
                        <td class="px-4 py-2 border-r border-b border-gray-200 dark:border-gray-700 font-medium text-gray-900 dark:text-white" title="{{ $transaction->projectRequirement?->name ?? '' }}">{{ $transaction->projectRequirement?->name ?? '-' }}</td>
                        <td class="px-4 py-2 border-r border-b border-gray-200 dark:border-gray-700 text-center">{{ $transaction->dispatchGuide?->guide_number ?? '-' }}</td>
                        <td class="px-4 py-2 border-r border-b border-gray-200 dark:border-gray-700 text-center">{{ $transaction->dispatchGuide?->transfer_date ? \Carbon\Carbon::parse($transaction->dispatchGuide->transfer_date)->format('d/m/y') : '-' }}</td>
                        <td class="px-4 py-2 border-r border-b border-gray-200 dark:border-gray-700 text-center">{{ $transaction->dispatchGuide?->store_entry_date ? \Carbon\Carbon::parse($transaction->dispatchGuide->store_entry_date)->format('d/m/y') : '-' }}</td>
                        <td class="px-4 py-2 border-r border-b border-gray-200 dark:border-gray-700 text-center">
                            <span>{{ $transaction->projectRequirement?->type?->value ?? ($transaction->projectRequirement?->type ?? '-') }}</span>
                        </td>
                        <td class="px-4 py-2 border-r border-b border-gray-200 dark:border-gray-700 text-center uppercase">{{ $transaction->projectRequirement?->unit?->name ?? '-' }}</td>
                        <td class="px-4 py-2 border-r border-b border-gray-200 dark:border-gray-700 text-center font-bold text-gray-900 dark:text-white">{{ number_format($transaction->quantity, 2) }}</td>
                        <td class="px-4 py-2 border-r border-b border-gray-200 dark:border-gray-700 truncate max-w-[150px]">{{ $transaction->projectRequirement?->project?->subClient?->client?->business_name ?? '-' }}</td>
                        <td class="px-4 py-2 border-r border-b border-gray-200 dark:border-gray-700 truncate max-w-[150px]">{{ $transaction->projectRequirement?->project?->subClient?->name ?? '-' }}</td>
                        <td class="px-4 py-2 border-r border-b border-gray-200 dark:border-gray-700 truncate max-w-[150px]">{{ $transaction->projectRequirement?->project?->subClient?->location ?? '-' }}</td>
                        <td class="px-4 py-2 border-r border-b border-gray-200 dark:border-gray-700 text-right">{{ number_format($transaction->projectRequirement?->price_unit ?? 0, 2) }}</td>
                        <td class="px-4 py-2 border-r border-b border-gray-200 dark:border-gray-700 text-right">{{ number_format($transaction->projectRequirement?->subtotal ?? 0, 2) }}</td>
                        <td class="px-4 py-2 border-r border-b border-gray-200 dark:border-gray-700 text-right">{{ number_format($transaction->price_unit ?? 0, 2) }}</td>
                        <td class="px-4 py-2 border-r border-b border-gray-200 dark:border-gray-700 text-right">{{ number_format($transaction->item_total ?? 0, 2) }}</td>
                        <td class="px-4 py-2 border-r border-b border-gray-200 dark:border-gray-700 text-right">{{ number_format($transaction->additional_cost ?? 0, 2) }}</td>
                        <td class="px-4 py-2 border-r border-b border-gray-200 dark:border-gray-700 truncate max-w-[150px]">{{ $transaction->cost_description ?? '-' }}</td>
                        <td class="px-4 py-2 border-r border-b border-gray-200 dark:border-gray-700 text-right font-bold text-gray-900 dark:text-white">{{ number_format($transaction->subtotal ?? 0, 2) }}</td>
                        <td class="px-4 py-2 border-r border-b border-gray-200 dark:border-gray-700 text-center">
                            <div class="relative inline-flex items-center">
                                <div class="w-9 h-5 rounded-full {{ $transaction->is_external_purchase ? 'bg-green-600' : 'bg-gray-200 dark:bg-gray-700' }} transition-colors"></div>
                                <div class="absolute top-[2px] start-[2px] w-4 h-4 bg-white border border-gray-300 dark:border-gray-600 rounded-full transition-shadow {{ $transaction->is_external_purchase ? 'translate-x-4' : '' }}"></div>
                            </div>
                        </td>
                        <td class="px-4 py-2 border-r border-b border-gray-200 dark:border-gray-700 truncate max-w-[150px]">{{ $transaction->supplier_name ?? '-' }}</td>
                        <td class="px-4 py-2 border-r border-b border-gray-200 dark:border-gray-700">{{ $transaction->receipt_number ?? '-' }}</td>
                        <td class="px-4 py-2 border-r border-b border-gray-200 dark:border-gray-700 text-center">
                            <span class="bg-primary-100 text-primary-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-primary-900 dark:text-primary-300 uppercase">PENDIENTE</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="23" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center">
                                <span class="material-symbols-outlined text-4xl mb-2 opacity-20">inventory_2</span>
                                <p>No se encontraron transacciones registradas.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="p-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center text-sm text-gray-500 dark:text-gray-400 rounded-b-xl sticky bottom-0 z-10 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
        <span>Total transacciones: <span class="font-bold text-gray-900 dark:text-white">{{ count($transactions) }}</span></span>
    </div>
</div>
</div>