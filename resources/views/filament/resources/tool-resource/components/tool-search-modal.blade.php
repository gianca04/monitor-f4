{{-- Tool Search Drawer Component (Panel Lateral para buscar herramientas) --}}
{{-- Usage: @include('filament.resources.tool-resource.components.tool-search-modal') --}}

{{-- Overlay container --}}
<div x-show="toolSearchModal.open" x-cloak @keydown.escape.window="closeToolSearchModal()"
    class="fixed inset-0 z-50 overflow-hidden">

    {{-- Backdrop --}}
    <div x-show="toolSearchModal.open" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 backdrop-blur-none" x-transition:enter-end="opacity-100 backdrop-blur-sm"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 backdrop-blur-sm"
        x-transition:leave-end="opacity-0 backdrop-blur-none" class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm"
        @click="closeToolSearchModal()"></div>

    {{-- Drawer Panel --}}
    <div class="fixed inset-y-0 right-0 flex max-w-full" x-show="toolSearchModal.open"
        x-transition:enter="transform transition ease-out duration-300" x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in duration-200"
        x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">

        <div class="w-screen max-w-lg">
            <div class="flex flex-col h-full bg-white dark:bg-gray-900 shadow-xl">

                {{-- Drawer Header --}}
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-xl bg-blue-100 dark:bg-blue-900/50">
                                <span
                                    class="material-symbols-outlined text-blue-600 dark:text-blue-400">construction</span>
                            </div>
                            <div>
                                <h3 class="font-black text-sm text-gray-800 dark:text-white uppercase tracking-tight">
                                    Catálogo de Herramientas</h3>
                                <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">
                                    Seleccionar herramientas para asignar
                                </p>
                            </div>
                        </div>
                        <button @click="closeToolSearchModal()"
                            class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-all active:scale-95">
                            <span class="material-symbols-outlined text-xl">close</span>
                        </button>
                    </div>

                    {{-- Search Input --}}
                    <div class="relative mt-5">
                        <span
                            class="material-symbols-outlined absolute left-4 top-3 text-gray-400 text-lg">search</span>
                        <input x-ref="toolSearchInput" x-model="toolSearchModal.query"
                            @input.debounce.300ms="searchTools()"
                            class="w-full pl-11 pr-4 py-3 bg-gray-50 dark:bg-gray-900 border-gray-100 dark:border-gray-800 rounded-2xl text-sm text-gray-800 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all shadow-inner"
                            type="text" placeholder="Buscar por código, nombre o número de serie..." autofocus />
                        <div x-show="toolSearchModal.loading" class="absolute right-4 top-3">
                            <span
                                class="material-symbols-outlined animate-spin text-blue-500 text-lg">progress_activity</span>
                        </div>
                    </div>
                </div>

                {{-- Filters Row --}}
                <div
                    class="flex gap-2 px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 overflow-x-auto">
                    {{-- Status Filter --}}
                    <select x-model="toolSearchModal.filters.status" @change="searchTools()"
                        class="px-3 py-1.5 rounded-lg text-xs font-medium bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-500">
                        <option value="">Todos los estados</option>
                        <option value="Disponible">🟢 Disponible</option>
                        <option value="En Uso">🔵 En Uso</option>
                        <option value="En Mantenimiento">🟡 En Mantenimiento</option>
                        <option value="Dañado">🔴 Dañado</option>
                    </select>

                    {{-- Category Filter --}}
                    <select x-model="toolSearchModal.filters.category_id" @change="searchTools()"
                        class="px-3 py-1.5 rounded-lg text-xs font-medium bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-500">
                        <option value="">Todas las categorías</option>
                        <template x-for="cat in toolSearchModal.categories" :key="cat.id">
                            <option :value="cat.id" x-text="cat.name"></option>
                        </template>
                    </select>

                    {{-- Clear Filters --}}
                    <button x-show="toolSearchModal.filters.status || toolSearchModal.filters.category_id"
                        @click="clearToolFilters()"
                        class="px-3 py-1.5 rounded-lg text-xs font-medium text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors whitespace-nowrap">
                        <span class="material-symbols-outlined text-sm align-middle">filter_alt_off</span>
                        Limpiar
                    </button>
                </div>

                {{-- Selected Items Badge --}}
                <div x-show="toolSearchModal.selectedTools.length > 0"
                    class="px-4 py-2 bg-blue-50 dark:bg-blue-900/30 border-b border-blue-200 dark:border-blue-800">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-blue-700 dark:text-blue-400">
                            <span x-text="toolSearchModal.selectedTools.length"></span> herramienta(s) seleccionada(s)
                        </span>
                        <button @click="toolSearchModal.selectedTools = []"
                            class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400">
                            Limpiar selección
                        </button>
                    </div>
                </div>

                {{-- Results List --}}
                <div class="flex-1 overflow-y-auto" x-ref="toolResultsContainer">
                    <template x-for="tool in toolSearchModal.results" :key="tool.id">
                        <div @click="toggleToolSelection(tool)"
                            :class="isToolSelected(tool.id) ? 'bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500' : 'border-l-4 border-transparent hover:bg-gray-50 dark:hover:bg-gray-800'"
                            class="px-4 py-3 border-b border-gray-100 dark:border-gray-800 cursor-pointer transition-colors">
                            <div class="flex items-start gap-3">
                                {{-- Checkbox --}}
                                <div class="flex-shrink-0 pt-0.5">
                                    <div :class="isToolSelected(tool.id) ? 'bg-blue-500 border-blue-500 shadow-lg shadow-blue-500/30' : 'bg-white dark:bg-gray-700 border-gray-200 dark:border-gray-600'"
                                        class="w-6 h-6 rounded-lg border-2 flex items-center justify-center transition-all duration-200">
                                        <span x-show="isToolSelected(tool.id)"
                                            class="material-symbols-outlined text-white text-base font-bold">check</span>
                                    </div>
                                </div>

                                {{-- Content --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        {{-- Code Badge --}}
                                        <span x-show="tool.code"
                                            class="font-mono text-xs px-1.5 py-0.5 rounded bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400 font-semibold"
                                            x-text="tool.code"></span>

                                        {{-- Status Badge --}}
                                        <span :class="{
                                            'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400': tool.status === 'Disponible',
                                            'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-400': tool.status === 'En Uso',
                                            'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50 dark:text-yellow-400': tool.status === 'En Mantenimiento',
                                            'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400': tool.status === 'Dañado',
                                            'bg-gray-100 text-gray-700 dark:bg-gray-900/50 dark:text-gray-400': tool.status === 'Baja'
                                        }" class="text-[10px] px-1.5 py-0.5 rounded font-semibold uppercase"
                                            x-text="tool.status"></span>
                                    </div>

                                    {{-- Name --}}
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200 line-clamp-1"
                                        x-text="tool.name"></p>

                                    {{-- Category & Brand --}}
                                    <div class="flex items-center gap-2 mt-1">
                                        <span x-show="tool.category" class="text-xs text-gray-500 dark:text-gray-400">
                                            <span
                                                class="material-symbols-outlined text-xs align-middle mr-0.5">category</span>
                                            <span x-text="tool.category"></span>
                                        </span>
                                        <span x-show="tool.brand" class="text-xs text-gray-500 dark:text-gray-400">
                                            <span
                                                class="material-symbols-outlined text-xs align-middle mr-0.5">business</span>
                                            <span x-text="tool.brand"></span>
                                        </span>
                                    </div>
                                </div>

                                {{-- Availability Icon --}}
                                <div class="flex-shrink-0">
                                    <template x-if="tool.available">
                                        <span
                                            class="material-symbols-outlined text-green-500 text-xl">check_circle</span>
                                    </template>
                                    <template x-if="!tool.available">
                                        <span class="material-symbols-outlined text-gray-400 text-xl">block</span>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Loading State --}}
                    <div x-show="toolSearchModal.loading" class="py-12 text-center">
                        <span
                            class="material-symbols-outlined animate-spin text-blue-500 text-4xl">progress_activity</span>
                        <p class="text-sm text-gray-400 mt-2">Buscando herramientas...</p>
                    </div>

                    {{-- Empty State --}}
                    <div x-show="toolSearchModal.results.length === 0 && !toolSearchModal.loading"
                        class="py-12 text-center text-gray-400">
                        <span class="material-symbols-outlined text-4xl mb-2 block opacity-50">construction</span>
                        <p class="text-sm font-medium">No se encontraron herramientas</p>
                        <p class="text-xs mt-1">Intenta con otros términos de búsqueda</p>
                    </div>

                    {{-- Initial State --}}
                    <div x-show="toolSearchModal.query.length === 0 && toolSearchModal.results.length === 0 && !toolSearchModal.loading && !toolSearchModal.initialLoad"
                        class="py-12 text-center text-gray-400">
                        <span class="material-symbols-outlined text-4xl mb-2 block opacity-50">search</span>
                        <p class="text-sm font-medium">Buscar herramientas</p>
                        <p class="text-xs mt-1">Escribe para buscar o usa los filtros</p>
                    </div>
                </div>

                {{-- Drawer Footer --}}
                <div
                    class="px-6 py-4 border-t border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-[0_-10px_20px_-5px_rgba(0,0,0,0.05)]">
                    <div class="flex items-center justify-between gap-4">
                        <span
                            class="text-[10px] uppercase font-bold text-gray-400 tracking-widest px-2 py-1 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <span x-text="toolSearchModal.results.length + ' resultados'"></span>
                        </span>

                        <div class="flex gap-3">
                            <button @click="closeToolSearchModal()"
                                class="px-4 py-2 text-xs font-bold text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-800 rounded-xl transition-all">
                                Cancelar
                            </button>
                            <button @click="addSelectedTools()" :disabled="toolSearchModal.selectedTools.length === 0"
                                :class="toolSearchModal.selectedTools.length === 0 ? 'opacity-50 grayscale cursor-not-allowed' : 'hover:bg-blue-700 hover:scale-105 shadow-blue-500/20 shadow-lg active:scale-95'"
                                class="px-6 py-2.5 text-xs font-black text-white bg-blue-600 rounded-xl transition-all flex items-center gap-2">
                                <span class="material-symbols-outlined text-base">add_circle</span>
                                ASIGNAR (<span x-text="toolSearchModal.selectedTools.length"></span>)
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Nota: Los métodos y datos de Alpine.js deben estar definidos en el componente padre que incluye este modal --}}
{{-- Ver list.blade.php para la implementación completa de toolSearchModal --}}