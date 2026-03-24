@props(['guides'])

<div class="bk-card">
    @if($guides->isEmpty())
        <div class="p-8 text-center flex flex-col items-center justify-center h-48">
            <span class="material-symbols-outlined text-gray-300 dark:text-gray-600 text-5xl mb-3">inbox</span>
            <p class="text-gray-500 dark:text-gray-400 font-medium">Aún no hay guías de despacho registradas.</p>
        </div>
    @else
        <div class="overflow-x-auto custom-scrollbar">
            <table class="bk-table">
                <thead>
                    <tr>
                        <th>N° de Guía</th>
                        <th>Punto de Partida</th>
                        <th>Punto de Llegada</th>
                        <th>Fecha de Traslado</th>
                        <th>Generada por</th>
                        <th>Ítems Despachados</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($guides as $guide)
                        <tr>
                            <td class="font-semibold text-gray-900 dark:text-white">
                                {{ $guide->guide_number }}
                            </td>
                            <td>
                                {{ $guide->originLocation->name ?? '-' }}
                            </td>
                            <td>
                                {{ $guide->destinationLocation->name ?? '-' }}
                            </td>
                            <td>
                                {{ $guide->transfer_date ? $guide->transfer_date->format('d/m/Y') : '-' }}
                            </td>
                            <td>
                                {{ $guide->dispatchTransactions->first()->employee->employee->short_name ?? ($guide->dispatchTransactions->first()->employee->name ?? 'Usuario') }}
                            </td>
                            <td>
                                <span class="bk-badge bk-badge--info">
                                    {{ $guide->dispatchTransactions->count() }} ítems
                                </span>
                            </td>
                            <td class="text-right">
                                <a href="{{ route('quoteswarehouse.pdf', $guide->id) }}" target="_blank"
                                    class="bk-btn bk-btn--secondary bk-btn--sm">
                                    <span class="material-symbols-outlined text-[16px]">print</span>
                                    <span>Imprimir Guía</span>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
