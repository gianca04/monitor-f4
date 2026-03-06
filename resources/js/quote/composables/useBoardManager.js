import { createEmptyBoard, ITEM_TYPE_MAP } from '../constants.js';

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
            if (this.boards.length > 1) {
                this.boards.splice(index, 1);
                // Adjust active index
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
            this.activeBoardIndex = 0;
        },

        /** Creates a single default empty board. */
        initDefaultBoard() {
            this.boards.push(
                createEmptyBoard(
                    this.quoteType === 'Preventivo' ? 'Tablero 1' : 'CORRECTIVO'
                )
            );
            this.activeBoardIndex = 0;
        },

        /**
         * Sets up the Alpine watcher for quoteType changes.
         * Must be called inside init() with access to `this.$watch`.
         */
        setupQuoteTypeWatcher() {
            this.$watch('quoteType', (value) => {
                if (value === 'Correctivo') {
                    if (this.boards.length > 1) {
                        this.boards = this.boards.slice(0, 1);
                    }
                    if (this.boards.length > 0) {
                        this.boards[0].name = 'CORRECTIVO';
                    }
                    this.activeBoardIndex = 0;
                } else if (value === 'Preventivo') {
                    if (this.boards.length > 0 && this.boards[0].name === 'CORRECTIVO') {
                        this.boards[0].name = 'Tablero 1';
                    }
                }
            });
        },
    };
}
