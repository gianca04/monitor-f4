@props(['guides'])

<div class="space-y-4">
    @if($guides->isEmpty())
        <div
            class="p-10 text-center flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-800 rounded-lg border border-dashed border-gray-300 dark:border-gray-600">
            <span class="material-symbols-outlined text-gray-300 dark:text-gray-600 text-5xl mb-3">inbox</span>
            <p class="text-gray-500 dark:text-gray-400 font-medium">Aún no hay guías de despacho registradas en este
                proyecto.</p>
        </div>
    @else
<div class="relative overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg border border-gray-200 dark:border-gray-700">            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            @php
                                $headers = [
                                    ['label' => 'N° de Guía', 'align' => 'text-center', 'width' => 'w-28 min-w-[124px]'],
                                    ['label' => 'Punto de Partida', 'align' => 'text-left', 'width' => ''],
                                    ['label' => 'Punto de Llegada', 'align' => 'text-left', 'width' => ''],
                                    ['label' => 'Fech. Traslado', 'align' => 'text-center', 'width' => 'w-32'],
                                    ['label' => 'Generada por', 'align' => 'text-left', 'width' => 'w-44'],
                                    ['label' => 'Ítems', 'align' => 'text-center', 'width' => 'w-20'],
                                ];
                            @endphp

                            @foreach($headers as $header)
                                <th
                                    class="px-4 py-3 border-x border-gray-200 dark:border-gray-600 relative group/th {{ $header['align'] }} {{ $header['width'] }}">
                                    <span class="font-bold whitespace-nowrap">{{ $header['label'] }}</span>
                                    <div
                                        class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize opacity-0 group-hover/th:opacity-100 col-resizer bg-gray-300">
                                    </div>
                                </th>
                            @endforeach

                            <th
                                class="p-0 bg-white dark:bg-gray-800 sticky right-0 z-40 border-l border-gray-300 dark:border-gray-600">
                                <span class="sr-only">Acciones</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($guides as $guide)
                            <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                <td
                                    class="px-4 py-2 border-x border-gray-200 dark:border-gray-700 text-center font-bold text-gray-900 dark:text-white">
                                    {{ $guide->guide_number }}
                                </td>
                                <td class="px-4 py-2 border-x border-gray-200 dark:border-gray-700 truncate max-w-[200px]"
                                    title="{{ $guide->originLocation->name ?? '' }}">
                                    <div class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[16px] text-gray-400">location_on</span>
                                        <span>{{ $guide->originLocation->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-2 border-x border-gray-200 dark:border-gray-700 truncate max-w-[200px]"
                                    title="{{ $guide->destinationLocation->name ?? '' }}">
                                    <div class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[16px] text-gray-400">flag</span>
                                        <span>{{ $guide->destinationLocation->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-2 border-x border-gray-200 dark:border-gray-700 text-center">
                                    {{ $guide->transfer_date ? $guide->transfer_date->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-4 py-2 border-x border-gray-200 dark:border-gray-700 truncate max-w-[150px]">
                                    <div class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[16px] text-gray-400">person</span>
                                        <span
                                            class="font-medium">{{ $guide->dispatchTransactions->first()->employee->employee->short_name ?? ($guide->dispatchTransactions->first()->employee->name ?? 'Usuario') }}</span>
                                    </div>
                                </td>
                                <td
                                    class="px-4 py-2 border-x border-gray-200 dark:border-gray-700 text-center font-bold text-gray-900 dark:text-white">
                                    {{ $guide->dispatchTransactions->count() }}
                                </td>
                                <td
                                    class="p-0 bg-white dark:bg-gray-800 sticky right-0 z-10 border-l border-gray-200 dark:border-gray-600">
                                    <div
                                        class="flex items-center justify-center h-full divide-x divide-gray-200 dark:divide-gray-600">
                                        <button type="button"
                                            class="btn-toggle-tx w-full h-full text-center flex justify-center items-center py-3 text-primary-600 dark:text-primary-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all font-bold"
                                            data-guide-id="{{ $guide->id }}" title="Ver Transacciones de esta Guía">
                                            <span class="material-symbols-outlined text-[20px]">expand_circle_down</span>
                                        </button>
                                        <a href="{{ route('quoteswarehouse.pdf', $guide->id) }}" target="_blank"
                                            class="w-full h-full text-center flex justify-center items-center py-3 text-gray-500 hover:text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all"
                                            title="Imprimir Guía">
                                            <span class="material-symbols-outlined text-[20px]">picture_as_pdf</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div
                class="p-4 border-t border-gray-200 dark:border-gray-700 flex items-center text-sm text-gray-500 dark:text-gray-400">
                <span>Registros encontrados: <span
                        class="font-bold text-gray-900 dark:text-white">{{ count($guides) }}</span></span>
            </div>
        </div>
    @endif
</div>