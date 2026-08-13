{{-- Quote Footer Component (Enterprise Status Bar) --}}
{{-- Shows: board info · board subtotal | global total | save button --}}

<div class="w-full shrink-0 z-20 pt-1 pb-1">

    {{-- Expandable Quote Details Popup --}}
    <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2 scale-98"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-2 scale-98" @click.away="sidebarOpen = false"
        class="mb-2.5 bg-white border border-gray-200 rounded-lg shadow-xl dark:bg-gray-950 dark:border-gray-800 overflow-visible">
        @include('filament.resources.quote-resource.components.quote-sidebar')
    </div>

    {{-- Main Footer Bar --}}
    <div
        class="flex flex-col sm:flex-row items-center justify-between gap-3 p-2.5 bg-white/95 backdrop-blur-md border border-gray-200 rounded-lg shadow-md dark:bg-gray-950/95 dark:border-gray-800">

        {{-- Left: Toggle Button + Status indicator + board info --}}
        <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-start">

            {{-- Volver a Lista Button --}}
            <a href="{{ \App\Filament\Resources\Quotes\QuoteResource::getUrl('index') }}"
                class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-medium rounded-md transition-all shrink-0 bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                title="Volver a la lista de cotizaciones">
                <span class="material-symbols-outlined text-[14px]">arrow_back</span>
                <span>Volver</span>
            </a>

            {{-- Datos Cotización Toggle Button --}}
            <button @click="sidebarOpen = !sidebarOpen" type="button"
                :class="sidebarOpen ? 'bg-gray-900 text-white dark:bg-gray-100 dark:text-gray-900 shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700'"
                class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-medium rounded-md transition-all shrink-0">
                <span class="material-symbols-outlined text-[14px]">tune</span>
                <span>Datos Cotización</span>
                <span class="material-symbols-outlined text-[13px] transition-transform duration-200"
                    :class="sidebarOpen ? 'rotate-180' : ''">expand_less</span>
            </button>

            {{-- Current board info --}}
            <div class="flex items-center gap-2 text-[11px]">
                <span class="font-semibold text-gray-900 dark:text-gray-100"
                    x-text="boards[activeBoardIndex]?.name || '—'"></span>
                <span class="text-gray-300 dark:text-gray-700">·</span>
                <span class="text-gray-500" x-text="getBoardItemCount(activeBoardIndex) + ' items'"></span>
                <span class="text-gray-300 dark:text-gray-700">·</span>
                <span class="font-semibold text-gray-700 dark:text-gray-300 tabular-nums"
                    x-text="'S/ ' + getBoardSubtotal(activeBoardIndex).toLocaleString('es-PE', {minimumFractionDigits: 2})"></span>
            </div>
        </div>

        {{-- Right: Global total + save --}}
        <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-end">
            {{-- Global totals (all boards) --}}
            <div x-show="boards.length > 1" class="text-right">
                <div class="text-[9px] uppercase tracking-wider text-gray-500 font-semibold">TOTAL</div>
                <div class="text-[11px] font-semibold text-gray-700 dark:text-gray-400"
                    x-text="getTotalItems() + ' items'">
                </div>
            </div>

            {{-- Divider --}}
            <div x-show="boards.length > 1" class="w-px h-8 bg-gray-200 dark:bg-gray-800"></div>

            {{-- Total --}}
            <div class="text-right">
                <div class="text-[9px] uppercase tracking-wider text-gray-500 font-semibold">Total</div>
                <div class="text-xs font-extrabold text-gray-900 dark:text-white tabular-nums"
                    x-text="'S/ ' + getTotal().toLocaleString('es-PE', {minimumFractionDigits: 2})"></div>
            </div>

            {{-- Save Button --}}
            <button @click="saveQuote()" :disabled="saving"
                class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap rounded-md text-xs font-semibold transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-gray-950 disabled:pointer-events-none disabled:opacity-50 bg-gray-900 text-gray-50 shadow hover:bg-gray-900/90 h-8 px-3 py-1 dark:bg-gray-50 dark:text-gray-900 dark:hover:bg-gray-50/90 dark:focus-visible:ring-gray-300">
                <span x-show="!saving" class="material-symbols-outlined text-[14px]">save</span>
                <span x-show="saving"
                    class="material-symbols-outlined text-[14px] animate-spin">progress_activity</span>
                <span x-text="saving ? 'Guardando...' : 'Guardar'"></span>
            </button>
        </div>

    </div>
</div>