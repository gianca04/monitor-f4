@props(['quoteWarehouse', 'client', 'quote'])

<div class="bk-card mb-4">
    <div class="bk-card-body flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">

                <span class="bk-badge {{ $quoteWarehouse->estatus === 'pending' || $quoteWarehouse->estatus === 'Pendiente' ? 'bk-badge--warning' :
    ($quoteWarehouse->estatus === 'Atendido' ? 'bk-badge--success' : 'bk-badge--info') }}">
                    {{ $quoteWarehouse->estatus === 'pending' ? 'Pendiente' : $quoteWarehouse->estatus }}
                </span>
            </div>
            <div
                class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs text-gray-500 dark:text-gray-400 font-medium">
                <div class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[14px]">business</span>
                    <span class="text-gray-700 dark:text-gray-300">{{ $client }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick action bar --}}
    <div class="border-t border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50 px-5 py-2.5 flex items-center justify-between">
        
        <button type="button"
            class="bk-btn bk-btn--ghost bk-btn--sm btn-fill-all text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition flex items-center gap-1 font-bold">
            <span class="material-symbols-outlined text-[14px]">done_all</span>
            <span>Llenar restante</span>
        </button>
    </div>
</div>