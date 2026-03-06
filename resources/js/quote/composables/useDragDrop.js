/**
 * useDragDrop
 * Drag and drop state and handlers for reordering items within a board section.
 */
export function useDragDrop() {
    return {
        draggingItem: null,
        draggingSection: null,
        draggingIndex: null,
        draggingBoard: null,

        dragStart(bIndex, sectionKey, index) {
            this.draggingItem = this.boards[bIndex].items[sectionKey][index];
            this.draggingSection = sectionKey;
            this.draggingIndex = index;
            this.draggingBoard = bIndex;
        },

        dragOver(event) {
            return false;
        },

        dragDrop(bIndex, sectionKey, targetIndex) {
            if (
                this.draggingSection !== sectionKey ||
                this.draggingBoard !== bIndex ||
                this.draggingIndex === null
            ) {
                return;
            }

            const items = this.boards[bIndex].items[sectionKey];
            const itemToMove = items[this.draggingIndex];

            items.splice(this.draggingIndex, 1);
            items.splice(targetIndex, 0, itemToMove);

            this.draggingItem = null;
            this.draggingSection = null;
            this.draggingIndex = null;
            this.draggingBoard = null;
        },
    };
}
