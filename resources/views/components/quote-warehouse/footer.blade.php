@props(['quoteWarehouse'])

<div class="quote-footer !w-full !max-w-none !px-0 bg-white/95 dark:bg-gray-900/95 backdrop-blur-md border-t border-gray-200 dark:border-gray-800 shadow-[0_-4px_20px_rgba(0,0,0,0.05)] mt-6"
    style="margin-bottom: 0; border-radius: 0;">
    <div
        class="max-w-[100rem] mx-auto px-4 sm:px-6 lg:px-8 py-3 flex flex-col sm:flex-row items-center justify-between gap-4">

        {{-- Left: Observaciones --}}
        <div
            class="flex-1 w-full max-w-2xl flex border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-white dark:bg-gray-800 focus-within:ring-2 focus-within:ring-blue-500/20 focus-within:border-blue-500 transition-all">
            <div
                class="px-3 flex items-center bg-gray-50 dark:bg-gray-800/50 border-r border-gray-200 dark:border-gray-700">
                <span class="material-symbols-outlined text-gray-400 text-sm">edit_note</span>
            </div>
            <input type="text"
                class="obs-input flex-1 border-none focus:ring-0 text-xs py-2 bg-transparent dark:text-white placeholder-gray-400"
                placeholder="Nota general del despacho (opcional)..." value="{{ $quoteWarehouse->observations ?? '' }}" />
        </div>

        {{-- Right: Actions --}}
        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ \App\Filament\Resources\QuoteWarehouses\QuoteWarehouseResource::getUrl() }}"
                class="bk-btn bk-btn--secondary">
                Cancelar
            </a>
            <button type="button"
                class="btn-submit bk-btn bk-btn--primary">
                <span class="material-symbols-outlined text-[18px]">send</span>
                Registrar Despacho
            </button>
        </div>
    </div>
</div>
