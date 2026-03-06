{{-- Quote Section Card Component (Accordion + Compact Spreadsheet) --}}
{{-- Used inside x-for section loop, board context from activeBoardIndex --}}

<div class="quote-section" :class="{ 'quote-section--collapsed': isSectionCollapsed(activeBoardIndex, section.key) }">

    {{-- Section Header (Clickable Accordion) --}}
    <div class="quote-section__header" @click="toggleSection(activeBoardIndex, section.key)">
        <div class="flex items-center gap-2.5">
            {{-- Collapse chevron --}}
            <span class="material-symbols-outlined text-sm text-gray-400 transition-transform duration-200"
                :class="{ '-rotate-90': isSectionCollapsed(activeBoardIndex, section.key) }">
                expand_more
            </span>
            {{-- Section icon --}}
            <div class="flex items-center justify-center w-6 h-6 rounded-lg" :class="section.bgClass">
                <span class="material-symbols-outlined text-sm" :class="section.iconClass" x-text="section.icon"></span>
            </div>
            {{-- Section title --}}
            <h3 class="text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wide"
                x-text="section.title"></h3>
            {{-- Item count --}}
            <span class="text-[10px] font-mono text-gray-400 bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded"
                x-text="boards[activeBoardIndex].items[section.key].length + ' items'"></span>
        </div>
        {{-- Section subtotal --}}
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-gray-600 dark:text-gray-300 font-mono"
                x-text="'S/ ' + getSectionSubtotal(activeBoardIndex, section.key).toLocaleString('es-PE', {minimumFractionDigits: 2})"></span>
        </div>
    </div>

    {{-- Section Body (Collapsible) --}}
    <div x-show="!isSectionCollapsed(activeBoardIndex, section.key)"
        x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        {{-- Items Table --}}
        <div class="overflow-x-auto">
            <table class="quote-table">
                <thead>
                    <tr>
                        <th class="quote-table__th quote-table__th--handle">#</th>
                        <th class="quote-table__th quote-table__th--resizable"
                            :style="{ width: columnWidths.code + 'px' }">
                            Línea
                            <div @mousedown.prevent.stop="startResize('code', $event)"
                                class="quote-table__resize-handle"></div>
                        </th>
                        <th class="quote-table__th quote-table__th--resizable"
                            :style="{ width: columnWidths.description + 'px' }">
                            Descripción
                            <div @mousedown.prevent.stop="startResize('description', $event)"
                                class="quote-table__resize-handle"></div>
                        </th>
                        <th class="quote-table__th quote-table__th--resizable"
                            :style="{ width: columnWidths.comment + 'px' }">
                            Comentario
                            <div @mousedown.prevent.stop="startResize('comment', $event)"
                                class="quote-table__resize-handle"></div>
                        </th>
                        <th class="quote-table__th quote-table__th--resizable text-center"
                            :style="{ width: columnWidths.unit + 'px' }">
                            Unid.
                            <div @mousedown.prevent.stop="startResize('unit', $event)"
                                class="quote-table__resize-handle"></div>
                        </th>
                        <th class="quote-table__th quote-table__th--resizable text-center"
                            :style="{ width: columnWidths.quantity + 'px' }">
                            Cant.
                            <div @mousedown.prevent.stop="startResize('quantity', $event)"
                                class="quote-table__resize-handle"></div>
                        </th>
                        <th class="quote-table__th quote-table__th--resizable text-right"
                            :style="{ width: columnWidths.unit_price + 'px' }">
                            P.U.
                            <div @mousedown.prevent.stop="startResize('unit_price', $event)"
                                class="quote-table__resize-handle"></div>
                        </th>
                        <th class="quote-table__th quote-table__th--resizable text-right"
                            :style="{ width: columnWidths.subtotal + 'px' }">
                            Subtotal
                            <div @mousedown.prevent.stop="startResize('subtotal', $event)"
                                class="quote-table__resize-handle"></div>
                        </th>
                        <th class="quote-table__th" style="width: 32px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, index) in boards[activeBoardIndex].items[section.key]" :key="item._uid">
                        <tr class="quote-table__row" draggable="true"
                            @dragstart="dragStart(activeBoardIndex, section.key, index)"
                            @dragover.prevent="dragOver($event)" @drop="dragDrop(activeBoardIndex, section.key, index)"
                            :class="{ 'quote-table__row--dragging': draggingItem === item && draggingSection === section.key && draggingBoard === activeBoardIndex }">

                            {{-- # / Drag Handle --}}
                            <td class="quote-table__td quote-table__td--handle">
                                <div class="flex items-center justify-center gap-0.5">
                                    <span class="material-symbols-outlined text-sm text-gray-300">drag_indicator</span>
                                    <span class="text-[10px] text-gray-400" x-text="index + 1"></span>
                                </div>
                            </td>

                            {{-- Línea --}}
                            <td class="quote-table__td">
                                <span
                                    class="font-mono text-[11px] px-1 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 break-all"
                                    x-text="item.code"></span>
                            </td>

                            {{-- Descripción --}}
                            <td class="quote-table__td quote-table__td--editable">
                                <textarea x-model="item.description" rows="1" class="quote-table__cell-input"
                                    style="field-sizing: content; min-height: 1.4lh;"></textarea>
                            </td>

                            {{-- Comentario --}}
                            <td class="quote-table__td quote-table__td--editable">
                                <textarea x-model="item.comment" rows="1" placeholder="—"
                                    class="quote-table__cell-input text-gray-400"
                                    style="field-sizing: content; min-height: 1.4lh;"></textarea>
                            </td>

                            {{-- Unid. --}}
                            <td class="quote-table__td text-center text-gray-400 uppercase text-[10px]"
                                x-text="item.unit"></td>

                            {{-- Cant. --}}
                            <td class="quote-table__td quote-table__td--editable">
                                <input x-model.number="item.quantity" @input="recalculate()" type="number" min="0.01"
                                    step="0.01" class="quote-table__cell-number" />
                            </td>

                            {{-- P.U. --}}
                            <td class="quote-table__td text-right text-gray-700 dark:text-gray-300 font-mono text-[11px]"
                                x-text="'S/ ' + parseFloat(item.unit_price).toLocaleString('es-PE', {minimumFractionDigits: 2})">
                            </td>

                            {{-- Subtotal --}}
                            <td class="quote-table__td text-right font-bold text-gray-900 dark:text-white font-mono text-[11px]"
                                x-text="'S/ ' + (item.quantity * item.unit_price).toLocaleString('es-PE', {minimumFractionDigits: 2})">
                            </td>

                            {{-- Actions --}}
                            <td class="quote-table__td text-center">
                                <button @click="removeItem(activeBoardIndex, section.key, index)"
                                    class="quote-table__delete-btn">
                                    <span class="material-symbols-outlined text-xs">close</span>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            {{-- Empty State --}}
            <div x-show="boards[activeBoardIndex].items[section.key].length === 0"
                class="py-4 text-center border-t border-gray-100 dark:border-gray-700/50">
                <span
                    class="material-symbols-outlined text-2xl text-gray-300 dark:text-gray-600 block mb-1">inventory_2</span>
                <p class="text-[10px] text-gray-400 uppercase tracking-wider">Sin items</p>
            </div>
        </div>

        {{-- Add Button --}}
        <div class="quote-section__add-row">
            <button @click="openSearchModal(section.key, activeBoardIndex)" class="quote-section__add-btn">
                <span class="material-symbols-outlined text-sm">add</span>
                <span>Agregar item</span>
            </button>
        </div>
    </div>
</div>