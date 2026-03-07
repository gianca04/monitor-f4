import { createEmptyBoard, ITEM_TYPE_MAP, GLOBAL_SECTIONS, REGULAR_SECTIONS, createEmptyItems } from '../constants.js';

/**
 * useBoardManager
 * Manages the boards array, active tab, section collapse, quote type switching, and board initialization.
 */
export function useBoardManager() {
    return {
        /** @type {'Correctivo'|'Preventivo'} */
        quoteType: 'Correctivo',

        /** @type {Array<{id: string, name: string, items: object}>} */
        boards: [],

        /** Index of the currently visible board (tab) */
        activeBoardIndex: 0,

        /** Track which sections are collapsed per board: { 'boardId:sectionKey': true } */
        collapsedSections: {},

        /** Tab being renamed (index), null if none */
        renamingTabIndex: null,

        // ─── Active Board Helpers ─────────────────────────

        /** Get the currently active board object. */
        get activeBoard() {
            return this.boards[this.activeBoardIndex] || null;
        },

        setActiveBoard(index) {
            if (index >= 0 && index < this.boards.length) {
                this.activeBoardIndex = index;
            }
        },

        // ─── Section Collapse (Accordion) ──────────────────

        toggleSection(bIndex, sectionKey) {
            const key = `${this.boards[bIndex]?.id}:${sectionKey}`;
            this.collapsedSections[key] = !this.collapsedSections[key];
        },

        isSectionCollapsed(bIndex, sectionKey) {
            const key = `${this.boards[bIndex]?.id}:${sectionKey}`;
            return !!this.collapsedSections[key];
        },

        // ─── Tab Rename ──────────────────────────────────

        startRenameTab(index) {
            if (this.quoteType === 'Preventivo') {
                this.renamingTabIndex = index;
                this.$nextTick(() => {
                    const input = this.$refs[`tabInput${index}`];
                    if (input) {
                        input.focus();
                        input.select();
                    }
                });
            }
        },

        finishRenameTab() {
            this.renamingTabIndex = null;
        },

        // ─── Board CRUD ───────────────────────────────────

        addBoard() {
            const idx = this.boards.length + 1;
            this.boards.push(createEmptyBoard(`Tablero ${idx}`));
            // Auto-switch to the new tab
            this.activeBoardIndex = this.boards.length - 1;
        },

        removeBoard(index) {
            // Prevent removing the GLOBAL tab (index 0) in Preventivo mode
            if (this.quoteType === 'Preventivo' && index === 0) return;

            if (this.boards.length > 1) {
                this.boards.splice(index, 1);
                if (this.activeBoardIndex >= this.boards.length) {
                    this.activeBoardIndex = this.boards.length - 1;
                } else if (this.activeBoardIndex > index) {
                    this.activeBoardIndex--;
                }
            }
        },

        // ─── Initialization helpers ───────────────────────

        /**
         * Initializes boards from an existing quote (edit mode).
         * Determines quoteType from the saved groups.
         */
        initBoardsFromGroups(quoteGroups) {
            const isCorrectivo =
                quoteGroups.length === 1 &&
                quoteGroups[0].name.toUpperCase() === 'CORRECTIVO';
            this.quoteType = isCorrectivo ? 'Correctivo' : 'Preventivo';

            quoteGroups.forEach((group) => {
                const board = createEmptyBoard(group.name);
                (group.quote_details || []).forEach((detail) => {
                    const sectionKey = ITEM_TYPE_MAP[detail.item_type] || 'suministros';
                    board.items[sectionKey].push({
                        _uid: crypto.randomUUID(),
                        code: detail.pricelist?.sat_line || '',
                        description: detail.pricelist?.sat_description || detail.description || '',
                        comment: detail.comment || '',
                        unit: detail.pricelist?.unit?.name || 'UND',
                        quantity: parseFloat(detail.quantity),
                        unit_price: parseFloat(detail.unit_price),
                        pricelist_id: detail.pricelist_id,
                    });
                });
                this.boards.push(board);
            });

            // Ensure GLOBAL tab is at index 0 and named GLOBAL if Preventivo
            if (!isCorrectivo) {
                if (this.boards.length === 0 || this.boards[0].name.toUpperCase() !== 'GLOBAL') {
                    this.boards.unshift(createEmptyBoard('GLOBAL'));
                } else {
                    this.boards[0].name = 'GLOBAL';
                }
            }

            this.activeBoardIndex = 0;
        },

        /**
         * Fallback: load flat quote_details into a single board.
         */
        initBoardsFromDetails(quoteDetails) {
            const board = createEmptyBoard(
                this.quoteType === 'Preventivo' ? 'Tablero 1' : 'CORRECTIVO'
            );
            quoteDetails.forEach((detail) => {
                const sectionKey = ITEM_TYPE_MAP[detail.item_type] || 'suministros';
                board.items[sectionKey].push({
                    _uid: crypto.randomUUID(),
                    code: detail.pricelist?.sat_line || '',
                    description: detail.pricelist?.sat_description || detail.description || '',
                    comment: detail.comment || '',
                    unit: detail.pricelist?.unit?.name || 'UND',
                    quantity: parseFloat(detail.quantity),
                    unit_price: parseFloat(detail.unit_price),
                    pricelist_id: detail.pricelist_id,
                });
            });
            this.boards.push(board);

            // If Preventivo, ensure GLOBAL tab is at index 0 and data is split
            if (this.quoteType === 'Preventivo') {
                this._splitToPreventivo();
                this.activeBoardIndex = 1; // Focus the first equipment tab
            } else {
                this.activeBoardIndex = 0;
            }
        },

        /** Creates a default empty board structure based on quoteType. */
        initDefaultBoard() {
            if (this.quoteType === 'Preventivo') {
                this.boards.push(createEmptyBoard('GLOBAL'));
                this.boards.push(createEmptyBoard('Tablero 1'));
                this.activeBoardIndex = 1;
            } else {
                this.boards.push(createEmptyBoard('CORRECTIVO'));
                this.activeBoardIndex = 0;
            }
        },

        /**
         * Sets up the Alpine watcher for quoteType changes.
         * Handles merging/splitting logic when transitioning.
         */
        setupQuoteTypeWatcher() {
            this.$watch('quoteType', (value, oldValue) => {
                if (value === oldValue) return;

                if (value === 'Correctivo') {
                    this._mergeToCorrectivo();
                } else if (value === 'Preventivo') {
                    this._splitToPreventivo();
                }
            });
        },

        // ─── Data Splitting & Merging ─────────────────────

        /** Merges all boards into a single CORRECTIVO board */
        _mergeToCorrectivo() {
            const mergedBoard = createEmptyBoard('CORRECTIVO');

            this.boards.forEach((board) => {
                Object.keys(mergedBoard.items).forEach(sectionKey => {
                    if (board.items[sectionKey] && board.items[sectionKey].length > 0) {
                        mergedBoard.items[sectionKey].push(...board.items[sectionKey]);
                    }
                });
            });

            this.boards = [mergedBoard];
            this.activeBoardIndex = 0;
        },

        /** Splits a CORRECTIVO board into GLOBAL and equipment tabs */
        _splitToPreventivo() {
            const globalBoard = createEmptyBoard('GLOBAL');
            const equipmentBoards = [];

            this.boards.forEach((board) => {
                // If it's already named GLOBAL, just use it (shouldn't happen on transition though)
                if (board.name.toUpperCase() === 'GLOBAL') {
                    Object.keys(globalBoard.items).forEach(sk => globalBoard.items[sk].push(...(board.items[sk] || [])));
                    return;
                }

                const newEquipmentBoard = createEmptyBoard(board.name === 'CORRECTIVO' ? 'Tablero 1' : board.name);

                Object.keys(board.items).forEach(sectionKey => {
                    const items = board.items[sectionKey] || [];
                    if (items.length === 0) return;

                    if (GLOBAL_SECTIONS.includes(sectionKey)) {
                        globalBoard.items[sectionKey].push(...items);
                    } else {
                        newEquipmentBoard.items[sectionKey].push(...items);
                    }
                });

                equipmentBoards.push(newEquipmentBoard);
            });

            if (equipmentBoards.length === 0) {
                equipmentBoards.push(createEmptyBoard('Tablero 1'));
            }

            this.boards = [globalBoard, ...equipmentBoards];
            this.activeBoardIndex = 1; // Focus the first equipment tab instead of global
        },
    };
}
