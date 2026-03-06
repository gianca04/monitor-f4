import { pricelistService } from '../services/PricelistService.js';

/**
 * useSearchModal
 * Manages the search drawer state, tabs, multi-selection, and infinite scroll.
 */
export function useSearchModal() {
    return {
        searchModal: {
            open: false,
            section: null,
            boardIndex: null,
            query: '',
            results: [],
            loading: false,
            filter: null,
            selectedItems: [],
            priceTypeGroups: [],
            activeTabIndex: 0,
            loadingInitial: false,
            loadingMore: false,
        },

        // ─── Open / Close ──────────────────────────────────

        async openSearchModal(sectionKey, bIndex) {
            this.searchModal.open = true;
            this.searchModal.section = sectionKey;
            this.searchModal.boardIndex = bIndex;
            this.searchModal.query = '';
            this.searchModal.results = [];
            this.searchModal.selectedItems = [];
            this.searchModal.activeTabIndex = 0;

            const section = this.sections.find((s) => s.key === sectionKey);
            this.searchModal.filter = section?.priceTypeId || null;

            if (this.searchModal.priceTypeGroups.length === 0) {
                await this.loadInitialItems();
            }

            // Auto-select the matching tab
            if (section?.priceTypeId) {
                const tabIndex = this.searchModal.priceTypeGroups.findIndex(
                    (g) => g.price_type.id === section.priceTypeId
                );
                if (tabIndex >= 0) {
                    this.searchModal.activeTabIndex = tabIndex;
                }
            }

            this.$nextTick(() => this.$refs.searchInput?.focus());
        },

        closeSearchModal() {
            this.searchModal.open = false;
            this.searchModal.boardIndex = null;
            this.searchModal.query = '';
            this.searchModal.results = [];
            this.searchModal.filter = null;
            this.searchModal.selectedItems = [];
            this.searchModal.activeTabIndex = 0;
        },

        getCurrentSectionTitle() {
            const section = this.sections.find((s) => s.key === this.searchModal.section);
            return section ? section.title : '';
        },

        // ─── Tabs ──────────────────────────────────────────

        selectPriceTypeTab(index) {
            this.searchModal.activeTabIndex = index;
            this.$refs.resultsContainer?.scrollTo({ top: 0, behavior: 'smooth' });
        },

        getCurrentTabItems() {
            return this.searchModal.priceTypeGroups[this.searchModal.activeTabIndex]?.items || [];
        },

        getCurrentTabHasMore() {
            return this.searchModal.priceTypeGroups[this.searchModal.activeTabIndex]?.has_more || false;
        },

        // ─── Data Loading ──────────────────────────────────

        async loadInitialItems() {
            this.searchModal.loadingInitial = true;
            try {
                const data = await pricelistService.loadInitialItems();
                this.searchModal.priceTypeGroups = data.map((group) => ({
                    ...group,
                    page: 1,
                }));
            } catch (err) {
                console.error('loadInitialItems:', err);
                this.searchModal.priceTypeGroups = [];
            } finally {
                this.searchModal.loadingInitial = false;
            }
        },

        handleScroll(event) {
            if (this.searchModal.query.length >= 2) return;
            if (this.searchModal.loadingMore) return;
            if (!this.getCurrentTabHasMore()) return;

            const container = event.target;
            const scrollBottom = container.scrollHeight - container.scrollTop - container.clientHeight;
            if (scrollBottom < 100) {
                this.loadMoreItems();
            }
        },

        async loadMoreItems() {
            const groupIndex = this.searchModal.activeTabIndex;
            const group = this.searchModal.priceTypeGroups[groupIndex];
            if (!group || !group.has_more || this.searchModal.loadingMore) return;

            this.searchModal.loadingMore = true;
            try {
                const nextPage = group.page + 1;
                const data = await pricelistService.loadByPriceType(group.price_type.id, nextPage);

                this.searchModal.priceTypeGroups[groupIndex].items = [
                    ...group.items,
                    ...data.data,
                ];
                this.searchModal.priceTypeGroups[groupIndex].page = nextPage;
                this.searchModal.priceTypeGroups[groupIndex].has_more = data.meta.has_more;
            } catch (err) {
                console.error('loadMoreItems:', err);
            } finally {
                this.searchModal.loadingMore = false;
            }
        },

        // ─── Search ────────────────────────────────────────

        async searchPricelist() {
            if (this.searchModal.query.length < 2) {
                this.searchModal.results = [];
                return;
            }
            this.searchModal.loading = true;
            try {
                this.searchModal.results = await pricelistService.search(this.searchModal.query);
            } catch (err) {
                console.error('searchPricelist:', err);
                this.searchModal.results = [];
            } finally {
                this.searchModal.loading = false;
            }
        },

        // ─── Selection ─────────────────────────────────────

        toggleItemSelection(result) {
            const idx = this.searchModal.selectedItems.findIndex((i) => i.id === result.id);
            if (idx === -1) {
                this.searchModal.selectedItems.push(result);
            } else {
                this.searchModal.selectedItems.splice(idx, 1);
            }
        },

        isItemSelected(resultId) {
            return this.searchModal.selectedItems.some((i) => i.id === resultId);
        },

        addSelectedItems() {
            const target = this.boards[this.searchModal.boardIndex].items[this.searchModal.section];
            this.searchModal.selectedItems.forEach((result) => {
                target.push({
                    _uid: crypto.randomUUID(),
                    code: result.code,
                    description: result.description,
                    comment: '',
                    unit: result.unit,
                    quantity: 1,
                    unit_price: result.unit_price,
                    pricelist_id: result.id,
                });
            });
            this.searchModal.selectedItems = [];
        },

        selectItem(result) {
            this.boards[this.searchModal.boardIndex].items[this.searchModal.section].push({
                _uid: crypto.randomUUID(),
                code: result.code,
                description: result.description,
                comment: '',
                unit: result.unit,
                quantity: 1,
                unit_price: result.unit_price,
                pricelist_id: result.id,
            });
            this.closeSearchModal();
        },
    };
}
