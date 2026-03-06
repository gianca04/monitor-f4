import { DEFAULT_COLUMN_WIDTHS } from '../constants.js';

/**
 * useColumnResize
 * Handles column resizing via mouse drag on table headers.
 */
export function useColumnResize() {
    return {
        columnWidths: { ...DEFAULT_COLUMN_WIDTHS },
        resizing: null,
        startX: 0,
        startWidth: 0,

        startResize(column, event) {
            this.resizing = column;
            this.startX = event.pageX;
            this.startWidth = this.columnWidths[column];
            document.body.style.cursor = 'col-resize';
            document.body.style.userSelect = 'none';

            const moveHandler = (e) => {
                if (this.resizing !== column) return;
                const diff = e.pageX - this.startX;
                this.columnWidths[column] = Math.max(40, this.startWidth + diff);
            };

            const upHandler = () => {
                this.resizing = null;
                document.body.style.cursor = '';
                document.body.style.userSelect = '';
                document.removeEventListener('mousemove', moveHandler);
                document.removeEventListener('mouseup', upHandler);
            };

            document.addEventListener('mousemove', moveHandler);
            document.addEventListener('mouseup', upHandler);
        },
    };
}
