<x-filament-panels::page>
    {{-- Assets --}}
    @vite(['resources/css/app.css', 'resources/css/quote-form.css', 'resources/js/app.js', 'resources/js/quote/index.js'])
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    {{-- Main Container with Alpine --}}
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
    )" class="quote-workspace">

        {{-- ═══════════════════════════════════════════════ --}}
        {{-- SPLIT VIEW LAYOUT (Workspace + Preciario Panel) --}}
        {{-- ═══════════════════════════════════════════════ --}}
        <div class="flex gap-3 items-start w-full relative">

            {{-- Main Quoting Workspace (Left side) --}}
            <div class="flex-1 min-w-0 transition-all duration-300 relative flex flex-col h-[calc(100vh-7rem)]">
                {{-- Compact Quote Name Header --}}
                <div class="flex items-center justify-between px-1 py-1 mb-1 shrink-0 border-b border-gray-100 dark:border-gray-800/80">
                    <div class="flex items-center gap-2 min-w-0">
                        <h1 class="text-xs font-bold text-gray-900 dark:text-gray-100 truncate tracking-tight"
                            x-text="quote.service_name || quote.project_name || ('Cotización #' + (quote.quote_number || record?.id || '1'))"></h1>
                        <span class="text-[10px] text-gray-400 font-mono bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded"
                            x-text="quote.request_number ? ('Req. #' + quote.request_number) : ('#' + (quote.quote_number || record?.id || '1'))"></span>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="text-[10px] px-2 py-0.5 rounded-full font-medium"
                            :class="{
                                'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300': quote.status === 'Approved',
                                'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300': quote.status === 'Draft' || quote.status === 'Pending',
                                'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300': !quote.status
                            }"
                            x-text="quote.status || 'Borrador'"></span>
                    </div>
                </div>

                {{-- TAB BAR (Fixed top) --}}
                <div class="quote-tab-bar shrink-0">
                    <div class="quote-tab-bar__tabs">
                        <template x-for="(board, bIndex) in boards" :key="board.id">
                            <div class="quote-tab" :class="{
                                    'quote-tab--active': activeBoardIndex === bIndex,
                                    'quote-tab--preventivo': quoteType === 'Preventivo',
                                    'quote-tab--dragging-tab': draggingTabIndex === bIndex,
                                    'quote-tab--drag-over': dragOverTabIndex === bIndex && draggingTabIndex !== null && draggingTabIndex !== bIndex
                                }" @click="setActiveBoard(bIndex)"
                                @dblclick.stop="if(quoteType !== 'Preventivo' || bIndex !== 0) startRenameTab(bIndex)"
                                @contextmenu.prevent="if($event.target.closest('.quote-tab') && quoteType === 'Preventivo' && boards.length > 1 && bIndex !== 0) removeBoard(bIndex)"
                                :draggable="!(quoteType === 'Preventivo' && bIndex === 0)"
                                @dragstart="tabDragStart(bIndex, $event)" @dragover="tabDragOver(bIndex, $event)"
                                @dragleave="if (dragOverTabIndex === bIndex) dragOverTabIndex = null"
                                @drop.prevent="tabDrop(bIndex)" @dragend="tabDragEnd()">

                                {{-- Tab icon --}}
                                <span class="quote-tab__icon material-symbols-outlined"
                                    x-text="quoteType === 'Preventivo' && bIndex === 0 ? 'public' : (quoteType === 'Preventivo' ? 'dashboard' : 'build')"></span>

                                {{-- Tab label (display) --}}
                                <span x-show="renamingTabIndex !== bIndex" class="quote-tab__label" x-text="board.name"></span>

                                {{-- Tab label (inline edit) --}}
                                <input x-show="renamingTabIndex === bIndex" x-ref="tabInput" x-model="board.name"
                                    @blur="finishRenameTab()" @keydown.enter="finishRenameTab()"
                                    @keydown.escape="finishRenameTab()" @click.stop class="quote-tab__input" type="text" />

                                {{-- Item count badge --}}
                                <span class="quote-tab__badge" x-text="getBoardItemCount(bIndex)"
                                    x-show="getBoardItemCount(bIndex) > 0"></span>

                                {{-- Close button (Preventivo, >1 board, NOT Global tab) --}}
                                <button
                                    x-show="quoteType === 'Preventivo' && boards.length > 1 && activeBoardIndex === bIndex && bIndex !== 0"
                                    @click.stop="removeBoard(bIndex)" class="quote-tab__close" title="Eliminar grupo">
                                    <span class="material-symbols-outlined text-xs">close</span>
                                </button>
                            </div>
                        </template>

                        {{-- Add Tab Button (Preventivo only) --}}
                        <button x-show="quoteType === 'Preventivo'" @click="addBoard()" class="quote-tab quote-tab--add"
                            title="Añadir nuevo grupo">
                            <span class="material-symbols-outlined text-base">add</span>
                        </button>
                    </div>

                    {{-- Right side: Board info --}}
                    <div class="quote-tab-bar__info">
                        <span class="text-[10px] uppercase tracking-wider text-gray-400 font-bold" x-text="quoteType"></span>
                        <span class="text-[10px] text-gray-300">·</span>
                        <span class="text-[10px] text-gray-400"
                            x-text="boards.length + (boards.length === 1 ? ' grupo' : ' grupos')"></span>
                    </div>
                </div>

                {{-- ACTIVE BOARD CONTENT (Only this partidas area scrolls) --}}
                <main class="quote-board flex-1 overflow-y-auto pr-1 my-1.5">
                    <template x-if="boards.length > 0 && boards[activeBoardIndex]">
                        <div class="pt-1">
                            {{-- Sections loop for active board --}}
                            <template x-for="section in getVisibleSections(activeBoardIndex)" :key="section.key">
                                @include('filament.resources.quote-resource.components.section-card')
                            </template>
                        </div>
                    </template>
                </main>

                {{-- Sticky Footer (Fixed bottom of left panel) --}}
                <div class="shrink-0 mt-auto">
                    @include('filament.resources.quote-resource.components.quote-footer')
                </div>
            </div>

            {{-- Preciario Search Side Panel (Static Split View) --}}
            @include('filament.resources.quote-resource.components.search-modal')
        </div>

    </div>

    {{-- SweetAlert2 (CDN) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</x-filament-panels::page>