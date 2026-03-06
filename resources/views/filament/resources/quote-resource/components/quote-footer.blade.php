{{-- Quote Footer Component (Enterprise Status Bar) --}}
{{-- Shows: board info · board subtotal | global total | save button --}}

<div class="quote-footer">
    <div class="quote-footer__inner">

        {{-- Left: Status indicator + board info --}}
        <div class="quote-footer__left">
            {{-- Pulse indicator --}}
            <span class="relative flex w-2 h-2">
                <span class="absolute w-full h-full rounded-full opacity-75 animate-ping bg-emerald-400"></span>
                <span class="relative w-2 h-2 rounded-full bg-emerald-500"></span>
            </span>

            {{-- Current board info --}}
            <div class="quote-footer__board-info">
                <span class="font-semibold text-gray-700 dark:text-gray-200"
                    x-text="boards[activeBoardIndex]?.name || '—'"></span>
                <span class="text-gray-300 dark:text-gray-600">·</span>
                <span class="text-gray-400" x-text="getBoardItemCount(activeBoardIndex) + ' items'"></span>
                <span class="text-gray-300 dark:text-gray-600">·</span>
                <span class="font-mono font-bold text-gray-600 dark:text-gray-300"
                    x-text="'S/ ' + getBoardSubtotal(activeBoardIndex).toLocaleString('es-PE', {minimumFractionDigits: 2})"></span>
            </div>
        </div>

        {{-- Right: Global total + save --}}
        <div class="quote-footer__right">
            {{-- Global totals (all boards) --}}
            <div x-show="boards.length > 1" class="quote-footer__global-total">
                <div class="text-[9px] uppercase tracking-wider text-gray-400 font-bold">Global</div>
                <div class="text-xs font-bold font-mono text-gray-500 dark:text-gray-400"
                    x-text="getTotalItems() + ' items · S/ ' + getSubtotal().toLocaleString('es-PE', {minimumFractionDigits: 2})">
                </div>
            </div>

            {{-- Divider --}}
            <div x-show="boards.length > 1" class="w-px h-8 bg-gray-200 dark:bg-gray-700"></div>

            {{-- Total --}}
            <div class="text-right">
                <div class="text-[9px] uppercase tracking-wider text-gray-400 font-bold">Total</div>
                <div class="text-lg font-black font-mono text-emerald-600 dark:text-emerald-400"
                    x-text="'S/ ' + getTotal().toLocaleString('es-PE', {minimumFractionDigits: 2})"></div>
            </div>

            {{-- Save Button --}}
            <button @click="saveQuote()" :disabled="saving" class="quote-footer__save-btn">
                <span x-show="!saving" class="material-symbols-outlined text-base">save</span>
                <span x-show="saving" class="material-symbols-outlined text-base animate-spin">progress_activity</span>
                <span x-text="saving ? 'Guardando...' : 'Guardar'"></span>
            </button>
        </div>

    </div>
</div>