<x-filament-panels::page>
    {{-- Assets --}}
    @vite(['resources/css/app.css', 'resources/css/quote-form.css', 'resources/js/app.js', 'resources/js/quote/index.js'])
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    {{-- Main Container with Alpine --}}
    {{-- Pasamos los datos desde PHP directamente, eliminando llamadas API innecesarias --}}
    <div x-data="quoteManager(
        @js($quoteCategories),
        @js($clients),
        @js($priceTypes),
        @js($record ?? null),
        @js($project ?? null),
        @js($quoteCount ?? 1),
        @js($subClientId ?? null),
        @js($serviceCode ?? null),
        @js($projectId ?? null),
        @js($suggestedRequestNumber ?? null),
        @js($quoteType ?? 'Correctivo')
    )" class="space-y-4">

        {{-- Collapsible Sidebar (Top Panel) --}}
        @include('filament.resources.quote-resource.components.quote-sidebar')

        {{-- Main Content (Full Width) --}}
        <main class="space-y-6">
            <template x-for="(board, bIndex) in boards" :key="board.id">
                <div
                    class="relative bg-white dark:bg-gray-800 rounded-[2rem] shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-5">

                    {{-- Board Header --}}
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3 w-full max-w-sm">
                            <span class="flex items-center justify-center w-10 h-10 rounded-full"
                                :class="quoteType === 'Preventivo' ? 'bg-purple-100 text-purple-600 dark:bg-purple-900/40 dark:text-purple-400' : 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-400'">
                                <span class="material-symbols-outlined text-xl"
                                    x-text="quoteType === 'Preventivo' ? 'calendar_month' : 'build'"></span>
                            </span>
                            <div class="flex-1">
                                <input x-show="quoteType === 'Preventivo'" type="text" x-model="board.name"
                                    class="w-full px-3 py-1.5 text-lg font-black text-gray-800 dark:text-white bg-transparent border-0 border-b-2 border-transparent hover:border-gray-300 focus:border-emerald-500 focus:ring-0 transition-all"
                                    placeholder="Nombre del grupo..." />
                                <h2 x-show="quoteType === 'Correctivo'"
                                    class="text-lg font-black text-gray-800 dark:text-white uppercase tracking-tight"
                                    x-text="board.name"></h2>
                            </div>
                        </div>

                        {{-- Eliminar Tarjeta Button --}}
                        <button x-show="quoteType === 'Preventivo' && boards.length > 1" @click="removeBoard(bIndex)"
                            class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-all">
                            <span class="material-symbols-outlined text-xl">delete</span>
                        </button>
                    </div>

                    {{-- Sections loop for this board --}}
                    <div class="space-y-5">
                        <template x-for="section in sections" :key="section.key">
                            @include('filament.resources.quote-resource.components.section-card')
                        </template>
                    </div>

                </div>
            </template>

            {{-- Añadir Board Múltiple --}}
            <div x-show="quoteType === 'Preventivo'" class="flex justify-center mt-6">
                <button @click="addBoard()"
                    class="flex items-center gap-2 px-6 py-3 font-bold text-emerald-600 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-full shadow-sm hover:shadow-md transition-all active:scale-95">
                    <span class="material-symbols-outlined text-xl">add_box</span>
                    <span>Añadir Grupo</span>
                </button>
            </div>
        </main>

        {{-- Spacer for sticky footer --}}
        <div class="h-24"></div>

        {{-- Search Modal --}}
        @include('filament.resources.quote-resource.components.search-modal')

        {{-- Sticky Footer --}}
        @include('filament.resources.quote-resource.components.quote-footer')

        {{-- Puedes mostrar el project_id en la vista principal si lo necesitas --}}

    </div>

    {{-- SweetAlert2 (CDN) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</x-filament-panels::page>