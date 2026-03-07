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

        {{-- Collapsible Sidebar (Top Panel) --}}
        @include('filament.resources.quote-resource.components.quote-sidebar')

        {{-- ═══════════════════════════════════════════════ --}}
        {{-- TAB BAR (Excel-style board navigation) --}}
        {{-- ═══════════════════════════════════════════════ --}}
        <div class="quote-tab-bar">
            <div class="quote-tab-bar__tabs">
                <template x-for="(board, bIndex) in boards" :key="board.id">
                    <div class="quote-tab" :class="{
                            'quote-tab--active': activeBoardIndex === bIndex,
                            'quote-tab--preventivo': quoteType === 'Preventivo'
                        }" @click="setActiveBoard(bIndex)"
                        @dblclick.stop="if(quoteType !== 'Preventivo' || bIndex !== 0) startRenameTab(bIndex)"
                        @contextmenu.prevent="if($event.target.closest('.quote-tab') && quoteType === 'Preventivo' && boards.length > 1 && bIndex !== 0) removeBoard(bIndex)">

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
                <span class="text-[10px] font-mono text-gray-400"
                    x-text="boards.length + (boards.length === 1 ? ' grupo' : ' grupos')"></span>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════ --}}
        {{-- ACTIVE BOARD CONTENT (single board at a time) --}}
        {{-- ═══════════════════════════════════════════════ --}}
        <main class="quote-board">
            <template x-if="boards.length > 0 && boards[activeBoardIndex]">
                <div>
                    {{-- Sections loop for active board --}}
                    <template x-for="section in getVisibleSections(activeBoardIndex)" :key="section.key">
                        @include('filament.resources.quote-resource.components.section-card')
                    </template>
                </div>
            </template>
        </main>

        {{-- Spacer for sticky footer --}}
        <div class="h-24"></div>

        {{-- Search Modal --}}
        @include('filament.resources.quote-resource.components.search-modal')

        {{-- Sticky Footer --}}
        @include('filament.resources.quote-resource.components.quote-footer')

    </div>

    {{-- SweetAlert2 (CDN) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</x-filament-panels::page>