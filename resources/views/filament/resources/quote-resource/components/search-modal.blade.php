{{-- Search Side Panel (Split View Preciario) --}}
{{-- Usage: @include('filament.resources.quote-resource.components.search-modal') --}}

<div x-show="searchModal.open" x-cloak
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-x-6"
    x-transition:enter-end="opacity-100 translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-x-0"
    x-transition:leave-end="opacity-0 translate-x-6"
    class="w-80 lg:w-[24rem] shrink-0 sticky top-0 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-950 dark:border-gray-800 flex flex-col h-[calc(100vh-7rem)] overflow-hidden z-20">

    {{-- Panel Header --}}
    <div class="px-3.5 py-3 border-b border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/50">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-xs text-gray-900 dark:text-white uppercase tracking-tight"
                    x-text="searchModal.replaceIndex !== null ? 'Reemplazar Ítem' : 'Preciario'"></h3>
                <p class="text-[10px] uppercase font-medium text-gray-500 tracking-wider"
                    x-text="searchModal.replaceIndex !== null ? 'Reemplazando en: ' + getCurrentSectionTitle() : 'Agregando a: ' + getCurrentSectionTitle()">
                </p>
            </div>
            <button @click="closeSearchModal()"
                class="p-1 text-gray-400 hover:text-gray-700 hover:bg-gray-200/60 dark:hover:bg-gray-800 rounded-md transition-all"
                title="Cerrar panel de preciario">
                <span class="material-symbols-outlined text-[16px]">close</span>
            </button>
        </div>

        {{-- Search Input --}}
        <div class="relative mt-2.5">
            <span
                class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-[14px] pointer-events-none">search</span>
            <input x-ref="searchInput" x-model="searchModal.query" @input.debounce.300ms="searchPricelist()"
                class="w-full pl-8 pr-7 py-1.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-md text-xs text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-900 transition-all shadow-sm"
                type="text" placeholder="Buscar por código o descripción..." autofocus />
            <div x-show="searchModal.loading" class="absolute right-2.5 top-1/2 -translate-y-1/2 flex items-center">
                <span
                    class="material-symbols-outlined animate-spin text-gray-900 dark:text-gray-100 text-[14px]">progress_activity</span>
            </div>
        </div>
    </div>

    {{-- Price Type Tabs (solo visibles cuando NO hay búsqueda activa) --}}
    <div x-show="searchModal.query.length < 2"
        class="flex gap-1 px-3 py-2 overflow-x-auto border-b border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/30 shrink-0">
        <template x-for="(group, index) in searchModal.priceTypeGroups" :key="group.price_type.id">
            <button @click="selectPriceTypeTab(index)"
                :class="searchModal.activeTabIndex === index ? 'bg-gray-900 text-white dark:bg-gray-100 dark:text-gray-900 shadow-sm' : 'bg-gray-200/60 text-gray-600 dark:bg-gray-800 dark:text-gray-300 hover:bg-gray-300/60 dark:hover:bg-gray-700'"
                class="px-2 py-0.5 rounded-md text-[11px] font-medium whitespace-nowrap transition-all">
                <span x-text="group.price_type.name"></span>
                <span class="ml-1 opacity-75 text-[10px]" x-text="'(' + group.items.length + ')'"></span>
            </button>
        </template>
    </div>

    {{-- Selected Items List (Persistent) --}}
    <div x-show="searchModal.selectedItems.length > 0"
        class="px-3 py-2 bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-800 max-h-36 overflow-y-auto shrink-0">
        <div class="flex items-center justify-between mb-1">
            <span class="text-[10px] font-semibold text-gray-900 dark:text-gray-100 uppercase tracking-wider">
                Seleccionados (<span x-text="searchModal.selectedItems.length"></span>)
            </span>
            <button @click="searchModal.selectedItems = []"
                class="text-[10px] font-medium text-red-600 hover:text-red-800 dark:text-red-400">
                Limpiar
            </button>
        </div>
        <div class="flex flex-col gap-1">
            <template x-for="selItem in searchModal.selectedItems" :key="selItem.id">
                <div
                    class="flex items-center justify-between bg-white dark:bg-gray-800 px-2 py-0.5 rounded border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="flex items-center gap-1.5 min-w-0 pr-1">
                        <span
                            class="text-[9px] px-1 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold shrink-0"
                            x-text="selItem.code"></span>
                        <span class="text-[10px] text-gray-700 dark:text-gray-200 truncate"
                            x-text="selItem.description"></span>
                    </div>
                    <button @click.stop="toggleItemSelection(selItem)"
                        class="text-gray-400 hover:text-red-500 rounded p-0.5 transition-colors shrink-0"
                        title="Quitar selección">
                        <span class="material-symbols-outlined text-[13px]">close</span>
                    </button>
                </div>
            </template>
        </div>
    </div>

    {{-- Results / Tab Items List --}}
    <div class="flex-1 overflow-y-auto" x-ref="resultsContainer" @scroll="handleScroll($event)">

        {{-- Modo Búsqueda: Mostrar resultados de búsqueda --}}
        <template x-if="searchModal.query.length >= 2">
            <div>
                <template x-for="result in searchModal.results" :key="result.id">
                    <div @click="toggleItemSelection(result)"
                        :class="isItemSelected(result.id) ? 'bg-gray-100/80 dark:bg-gray-800/80 border-l-2 border-gray-900 dark:border-gray-100' : 'border-l-2 border-transparent hover:bg-gray-50 dark:hover:bg-gray-900/50'"
                        class="px-3 py-2 border-b border-gray-100 dark:border-gray-800/60 cursor-pointer transition-colors">
                        <div class="flex items-start gap-2.5">
                            {{-- Checkbox --}}
                            <div class="flex-shrink-0 pt-0.5">
                                <div :class="isItemSelected(result.id) ? 'bg-gray-900 border-gray-900 dark:bg-gray-100 dark:border-gray-100' : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600'"
                                    class="w-3.5 h-3.5 rounded border flex items-center justify-center transition-all duration-150">
                                    <span x-show="isItemSelected(result.id)"
                                        class="material-symbols-outlined text-white dark:text-gray-900 text-[10px] font-bold">check</span>
                                </div>
                            </div>
                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-1.5 mb-0.5">
                                    <span
                                        class="text-[10px] px-1 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 font-semibold"
                                        x-text="result.code"></span>
                                    <span class="text-[10px] text-gray-400 uppercase"
                                        x-text="result.unit"></span>
                                </div>
                                <p class="text-[11px] text-gray-700 dark:text-gray-200 line-clamp-2 leading-tight"
                                    x-text="result.description"></p>
                            </div>
                            {{-- Price --}}
                            <div class="flex-shrink-0 text-right">
                                <div class="text-[11px] font-semibold text-gray-900 dark:text-gray-100 tabular-nums"
                                    x-text="'S/ ' + result.unit_price.toFixed(2)"></div>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Empty State para búsqueda --}}
                <div x-show="searchModal.results.length === 0 && !searchModal.loading"
                    class="py-8 text-center text-gray-400">
                    <span class="material-symbols-outlined text-2xl mb-1 block opacity-50">search_off</span>
                    <p class="text-xs font-medium">Sin resultados</p>
                </div>
            </div>
        </template>

        {{-- Modo Tabs: Mostrar items por PriceType --}}
        <template x-if="searchModal.query.length < 2">
            <div>
                {{-- Título del Tab Activo --}}
                <div x-show="searchModal.priceTypeGroups.length > 0"
                    class="px-3 py-1 bg-gray-100/70 dark:bg-gray-800/60 border-b border-gray-200 dark:border-gray-800">
                    <p class="text-[9px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                        x-text="searchModal.priceTypeGroups[searchModal.activeTabIndex]?.price_type?.name || 'Cargando...'">
                    </p>
                </div>

                {{-- Items del Tab Activo --}}
                <template x-for="item in getCurrentTabItems()" :key="item.id">
                    <div @click="toggleItemSelection(item)"
                        :class="isItemSelected(item.id) ? 'bg-gray-100/80 dark:bg-gray-800/80 border-l-2 border-gray-900 dark:border-gray-100' : 'border-l-2 border-transparent hover:bg-gray-50 dark:hover:bg-gray-900/50'"
                        class="px-3 py-2 border-b border-gray-100 dark:border-gray-800/60 cursor-pointer transition-colors">
                        <div class="flex items-start gap-2.5">
                            {{-- Checkbox --}}
                            <div class="flex-shrink-0 pt-0.5">
                                <div :class="isItemSelected(item.id) ? 'bg-gray-900 border-gray-900 dark:bg-gray-100 dark:border-gray-100' : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600'"
                                    class="w-3.5 h-3.5 rounded border flex items-center justify-center transition-all duration-150">
                                    <span x-show="isItemSelected(item.id)"
                                        class="material-symbols-outlined text-white dark:text-gray-900 text-[10px] font-bold">check</span>
                                </div>
                            </div>
                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-1.5 mb-0.5">
                                    <span
                                        class="text-[10px] px-1 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 font-semibold"
                                        x-text="item.code"></span>
                                    <span class="text-[10px] text-gray-400 uppercase" x-text="item.unit"></span>
                                </div>
                                <p class="text-[11px] text-gray-700 dark:text-gray-200 line-clamp-2 leading-tight"
                                    x-text="item.description"></p>
                            </div>
                            {{-- Price --}}
                            <div class="flex-shrink-0 text-right">
                                <div class="text-[11px] font-semibold text-gray-900 dark:text-gray-100 tabular-nums"
                                    x-text="'S/ ' + item.unit_price.toFixed(2)"></div>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Loading más items --}}
                <div x-show="searchModal.loadingMore" class="py-2.5 text-center">
                    <span class="material-symbols-outlined animate-spin text-gray-500 text-lg">progress_activity</span>
                    <p class="text-[10px] text-gray-400 mt-0.5">Cargando...</p>
                </div>

                {{-- Fin de la lista --}}
                <div x-show="!searchModal.loadingMore && !getCurrentTabHasMore() && getCurrentTabItems().length > 0"
                    class="py-2 text-center border-t border-gray-100 dark:border-gray-800">
                    <p class="text-[10px] text-gray-400">
                        <span class="material-symbols-outlined text-[12px] align-middle mr-0.5">check_circle</span>
                        Fin de la lista
                    </p>
                </div>

                {{-- Empty State para tabs --}}
                <div x-show="searchModal.priceTypeGroups.length === 0 && !searchModal.loadingInitial"
                    class="py-8 text-center text-gray-400">
                    <span class="material-symbols-outlined text-2xl mb-1 block opacity-50">inventory_2</span>
                    <p class="text-xs font-medium">Sin items disponibles</p>
                </div>

                {{-- Loading inicial --}}
                <div x-show="searchModal.loadingInitial" class="py-8 text-center">
                    <span class="material-symbols-outlined animate-spin text-gray-500 text-2xl">progress_activity</span>
                    <p class="text-xs text-gray-400 mt-1">Cargando preciario...</p>
                </div>
            </div>
        </template>
    </div>

    {{-- Panel Footer --}}
    <div class="px-3 py-2.5 border-t border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/50 shrink-0">
        <div class="flex items-center justify-between gap-2">
            <span class="text-[9px] uppercase font-semibold text-gray-400 tracking-wider">
                <template x-if="searchModal.query.length >= 2">
                    <span x-text="searchModal.results.length + ' resultados'"></span>
                </template>
                <template x-if="searchModal.query.length < 2">
                    <span x-text="getCurrentTabItems().length + ' items'"></span>
                </template>
            </span>

            <div class="flex gap-1.5">
                <button @click="closeSearchModal()"
                    class="px-2.5 py-1 text-[11px] font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-200/60 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-800 rounded-md transition-all">
                    Cerrar
                </button>
                <button @click="addSelectedItems()" :disabled="searchModal.selectedItems.length === 0"
                    :class="searchModal.selectedItems.length === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-900/90 shadow-sm active:scale-95'"
                    class="px-3 py-1 text-[11px] font-medium text-white bg-gray-900 dark:bg-gray-50 dark:text-gray-900 rounded-md transition-all flex items-center gap-1">
                    <span class="material-symbols-outlined text-[13px]" x-text="searchModal.replaceIndex !== null ? 'swap_horiz' : 'add_circle'"></span>
                    <span x-text="searchModal.replaceIndex !== null ? 'Reemplazar' : 'Agregar (' + searchModal.selectedItems.length + ')'"></span>
                </button>
            </div>
        </div>
    </div>
</div>