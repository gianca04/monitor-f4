{{-- Quote Section Card Component (Accordion + Compact Spreadsheet) --}}
{{-- Used inside x-for section loop, board context from activeBoardIndex --}}

<div class="mb-3 bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm dark:bg-gray-950 dark:border-gray-800"
    :class="{ 'border-gray-200/60 dark:border-gray-800/60': isSectionCollapsed(activeBoardIndex, section.key) }">

    {{-- Section Header (Clickable Accordion) --}}
    <div class="flex items-center justify-between px-3.5 py-2 cursor-pointer select-none bg-gray-50/50 hover:bg-gray-100/50 transition-colors dark:bg-gray-900/20 dark:hover:bg-gray-900/50"
        @click="toggleSection(activeBoardIndex, section.key)">
        <div class="flex items-center gap-2">
            {{-- Collapse chevron --}}
            <span class="material-symbols-outlined text-[14px] text-gray-400 transition-transform duration-200"
                :class="{ '-rotate-90': isSectionCollapsed(activeBoardIndex, section.key) }">
                expand_more
            </span>
            {{-- Section icon --}}
            <span class="material-symbols-outlined text-[14px] text-gray-500 dark:text-gray-400" x-text="section.icon"></span>
            {{-- Section title --}}
            <h3 class="text-xs font-semibold text-gray-900 dark:text-gray-100 tracking-tight" x-text="section.title">
            </h3>
            {{-- Item count --}}
            <span
                class="text-[10px] text-gray-500 bg-gray-200/50 dark:bg-gray-800 px-1.5 py-0.5 rounded-full font-medium"
                x-text="boards[activeBoardIndex].items[section.key].length + ' items'"></span>
        </div>
        {{-- Section subtotal --}}
        <div class="flex items-center gap-2">
            <span class="text-xs font-semibold text-gray-900 dark:text-gray-100 tabular-nums"
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
                        <th class="quote-table__th text-center" style="width: 48px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, index) in boards[activeBoardIndex].items[section.key]" :key="item._uid">
                        <tr class="quote-table__row"
                            @dragover.prevent="dragOver($event)" @drop="dragDrop(activeBoardIndex, section.key, index)"
                            :class="{ 'quote-table__row--dragging': draggingItem === item && draggingSection === section.key && draggingBoard === activeBoardIndex }">

                            {{-- # / Drag Handle --}}
                            <td class="quote-table__td quote-table__td--handle cursor-grab active:cursor-grabbing"
                                draggable="true"
                                @dragstart="dragStart(activeBoardIndex, section.key, index)">
                                <div class="flex items-center justify-center gap-0.5">
                                    <span class="material-symbols-outlined text-[13px] text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">drag_indicator</span>
                                    <span class="text-[10px] text-gray-400 font-medium" x-text="index + 1"></span>
                                </div>
                            </td>

                            {{-- Línea --}}
                            <td class="quote-table__td">
                                <span
                                    class="text-[11px] px-1 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 break-all"
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
                            <td class="quote-table__td text-right text-gray-700 dark:text-gray-300 text-[11px]"
                                x-text="'S/ ' + parseFloat(item.unit_price).toLocaleString('es-PE', {minimumFractionDigits: 2})">
                            </td>

                            {{-- Subtotal --}}
                            <td class="quote-table__td text-right font-bold text-gray-900 dark:text-white text-[11px]"
                                x-text="'S/ ' + (item.quantity * item.unit_price).toLocaleString('es-PE', {minimumFractionDigits: 2})">
                            </td>

                            {{-- Actions --}}
                            <td class="quote-table__td text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                    <button @click="openSearchModal(section.key, activeBoardIndex, index)"
                                        class="p-0.5 text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded transition-colors"
                                        title="Reemplazar ítem (conserva posición)">
                                        <span class="material-symbols-outlined text-[13px]">swap_horiz</span>
                                    </button>
                                    <button @click="removeItem(activeBoardIndex, section.key, index)"
                                        class="p-0.5 text-gray-400 hover:text-red-600 rounded transition-colors"
                                        title="Eliminar ítem">
                                        <span class="material-symbols-outlined text-[12px]">close</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            {{-- Empty State --}}
            <div x-show="boards[activeBoardIndex].items[section.key].length === 0"
                class="py-5 flex flex-col items-center justify-center text-center border-t border-gray-100 dark:border-gray-800">
                <span
                    class="material-symbols-outlined text-xl text-gray-300 dark:text-gray-700 block mb-1">inventory_2</span>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">No hay items</p>
                <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">Añade elementos a esta sección para comenzar.</p>
            </div>
        </div>

        {{-- Add Button --}}
        <div class="px-3.5 py-1.5 border-t border-gray-100 bg-gray-50/50 dark:border-gray-800 dark:bg-gray-900/30">
            <button @click="openSearchModal(section.key, activeBoardIndex)" class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-medium text-gray-500 bg-transparent border border-dashed border-gray-300 rounded-md hover:text-gray-900 hover:border-gray-400 hover:bg-gray-100 transition-colors dark:border-gray-700 dark:text-gray-400 dark:hover:text-gray-100 dark:hover:border-gray-600 dark:hover:bg-gray-800">
                <span class="material-symbols-outlined text-[13px]">add</span>
                <span>Agregar item</span>
            </button>
        </div>
    </div>
</div>