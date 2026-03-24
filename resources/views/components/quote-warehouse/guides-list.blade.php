@props(['guides'])

<div class="bk-card flex flex-col text-xs">
    @if($guides->isEmpty())
        <div class="p-10 text-center flex flex-col items-center justify-center bg-gray-50/50 dark:bg-gray-900/50 rounded-lg">
            <span class="material-symbols-outlined text-gray-300 dark:text-gray-600 text-5xl mb-3">inbox</span>
            <p class="text-gray-500 dark:text-gray-400 font-medium">Aún no hay guías de despacho registradas en este proyecto.</p>
        </div>
    @else
        <div class="overflow-x-auto max-h-[70vh] overflow-y-auto custom-scrollbar">
            <table class="bk-table relative table-fixed w-full" style="min-width: 900px;">
                <thead class="sticky top-0 z-30 bg-gray-50/95 backdrop-blur-sm dark:bg-gray-900/95 ring-1 ring-gray-200 dark:ring-white/10 shadow-sm border-b-2 border-primary-500">
                    <tr class="divide-x divide-gray-200 dark:divide-white/10">
                        <th class="fi-ta-header-cell px-3 py-2.5 text-center select-none relative group/th bg-primary-50/30" style="width: 120px;">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-primary-700 dark:text-primary-400">N° de Guía</span>
                            <div class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-primary-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer"></div>
                        </th>
                        <th class="fi-ta-header-cell px-3 py-2.5 text-left select-none relative group/th" style="width: 250px;">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Punto de Partida</span>
                            <div class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-primary-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer"></div>
                        </th>
                        <th class="fi-ta-header-cell px-3 py-2.5 text-left select-none relative group/th" style="width: 250px;">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Punto de Llegada</span>
                            <div class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-primary-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer"></div>
                        </th>
                        <th class="fi-ta-header-cell px-3 py-2.5 text-center select-none relative group/th" style="width: 100px;">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Fech. Traslado</span>
                            <div class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-primary-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer"></div>
                        </th>
                        <th class="fi-ta-header-cell px-3 py-2.5 text-left select-none relative group/th" style="width: 150px;">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Generada por</span>
                            <div class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-primary-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer"></div>
                        </th>
                        <th class="fi-ta-header-cell px-3 py-2.5 text-center select-none relative group/th" style="width: 100px;">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Ítems</span>
                            <div class="absolute right-0 top-0 bottom-0 w-1 cursor-col-resize hover:bg-primary-500 opacity-0 group-hover/th:opacity-100 transition-opacity z-10 col-resizer"></div>
                        </th>
                        <th class="fi-ta-header-cell p-0 bg-gray-50 dark:bg-gray-900 sticky right-0 z-[40] shadow-[-4px_0_10px_rgba(0,0,0,0.05)] border-l border-gray-200" style="width: 90px; min-width: 90px;">
                            <div class="w-full h-full text-center flex items-center justify-center text-[10px] font-bold uppercase text-gray-500">
                                Acciones
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-white/5 bg-white dark:bg-gray-900">
                    @foreach($guides as $guide)
                        <tr class="divide-x divide-gray-200 dark:divide-white/5 transition duration-150 even:bg-gray-50/40 dark:even:bg-white/[0.02] hover:bg-primary-50/30 dark:hover:bg-primary-500/5 hover:shadow-[inset_0_0_0_1px_rgba(59,130,246,0.3)] group cursor-default">
                            <td class="p-2 text-center align-middle font-mono font-bold text-primary-700 dark:text-primary-400 bg-primary-50/10">
                                {{ $guide->guide_number }}
                            </td>
                            <td class="p-2 align-middle truncate text-gray-800 dark:text-gray-200" title="{{ $guide->originLocation->name ?? '' }}">
                                <div class="flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[14px] text-gray-400 shrink-0">location_on</span>
                                    <span>{{ $guide->originLocation->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="p-2 align-middle truncate text-gray-800 dark:text-gray-200" title="{{ $guide->destinationLocation->name ?? '' }}">
                                <div class="flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[14px] text-gray-400 shrink-0">flag</span>
                                    <span>{{ $guide->destinationLocation->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="p-2 align-middle text-center font-mono text-[11px] tracking-tight">
                                {{ $guide->transfer_date ? $guide->transfer_date->format('d/m/Y') : '-' }}
                            </td>
                            <td class="p-2 align-middle truncate font-medium text-gray-600 dark:text-gray-300">
                                <div class="flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[14px] text-gray-400 shrink-0">person</span>
                                    <span>{{ $guide->dispatchTransactions->first()->employee->employee->short_name ?? ($guide->dispatchTransactions->first()->employee->name ?? 'Usuario') }}</span>
                                </div>
                            </td>
                            <td class="p-2 align-middle text-center">
                                <span class="bk-badge bk-badge--info font-bold text-[10px]">
                                    {{ $guide->dispatchTransactions->count() }}
                                </span>
                            </td>
                            <td class="p-0 align-middle bg-white dark:bg-gray-900 sticky right-0 z-10 shadow-[-4px_0_10px_rgba(0,0,0,0.05)] border-l border-gray-200 group-hover:bg-primary-50/30 transition-colors">
                                <div class="flex items-center justify-center h-full">
                                    <a href="{{ route('quoteswarehouse.pdf', $guide->id) }}" target="_blank"
                                       class="w-full h-full text-gray-400 transition hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-500/10 flex justify-center items-center py-2" title="Imprimir Guía">
                                        <span class="material-symbols-outlined text-[20px]">print</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="bg-gray-50/80 dark:bg-gray-900/50 border-t border-gray-200 dark:border-white/10 px-4 py-2 flex items-center justify-between text-[11px] z-20">
            <span class="font-semibold text-gray-500 dark:text-gray-400">
                Total Registros: <span class="text-gray-900 dark:text-white font-bold ml-1">{{ count($guides) }}</span>
            </span>
        </div>
    @endif
</div>
