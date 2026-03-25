@props(['quoteWarehouse'])

<div class="w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm mt-3">
    <div class="px-4 py-3 flex flex-col sm:flex-row items-center justify-between gap-3">

        {{-- Left: Observaciones --}}
        <div
            class="flex-1 w-full flex items-center bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus-within:ring-1 focus-within:border-blue-500 focus-within:ring-blue-500 transition-all">
            <div class="px-3 flex items-center border-r border-gray-300 dark:border-gray-600">
                <span class="material-symbols-outlined text-gray-500 dark:text-gray-400 text-[18px]">edit_note</span>
            </div>
            <input type="text"
                class="obs-input flex-1 border-none focus:ring-0 text-sm text-gray-900 dark:text-white p-2.5 bg-transparent placeholder-gray-400"
                placeholder="Obs. generales de despacho (opcional)..."
                value="{{ $quoteWarehouse->observations ?? '' }}" />
        </div>

        {{-- Right: Actions --}}
        <div class="flex items-center gap-2 shrink-0 w-full sm:w-auto">
            <a href="{{ \App\Filament\Resources\QuoteWarehouses\QuoteWarehouseResource::getUrl() }}"
                class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                Cancelar
            </a>
            <button type="button" id="btn-registrar-despacho"
                class="btn-submit flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-4 py-2 text-xs font-black text-white bg-primary-600 rounded-md hover:bg-primary-700 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 transition-all uppercase shadow-sm">
                <span class="material-symbols-outlined">send</span>
                Registrar Despacho
            </button>
        </div>
    </div>
</div>