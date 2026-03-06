/**
 * PricelistService
 * Handles all API calls related to the Pricelist catalog.
 */
export class PricelistService {
    constructor(baseUrl = '/api/pricelists') {
        this.baseUrl = baseUrl;
    }

    /**
     * Full-text search across all price types.
     * @param {string} query
     * @param {number} limit
     * @returns {Promise<Array>}
     */
    async search(query, limit = 30) {
        if (!query || query.length < 2) return [];

        const params = new URLSearchParams({ q: query, limit: limit.toString() });

        try {
            const res = await fetch(`${this.baseUrl}/search?${params}`);
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return await res.json();
        } catch (err) {
            console.error('PricelistService.search:', err);
            return [];
        }
    }

    /**
     * Load the first page of items grouped by PriceType.
     * @returns {Promise<Array>} Array of { price_type, items, has_more }
     */
    async loadInitialItems() {
        try {
            const res = await fetch(`${this.baseUrl}/initial-items`);
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return await res.json();
        } catch (err) {
            console.error('PricelistService.loadInitialItems:', err);
            return [];
        }
    }

    /**
     * Paginated load of items for a specific PriceType.
     * @param {number} priceTypeId
     * @param {number} page
     * @param {number} perPage
     * @returns {Promise<{data: Array, meta: {has_more: boolean}}>}
     */
    async loadByPriceType(priceTypeId, page = 1, perPage = 30) {
        try {
            const params = new URLSearchParams({
                price_type_id: priceTypeId.toString(),
                page: page.toString(),
                per_page: perPage.toString(),
            });
            const res = await fetch(`${this.baseUrl}/by-price-type?${params}`);
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return await res.json();
        } catch (err) {
            console.error('PricelistService.loadByPriceType:', err);
            return { data: [], meta: { has_more: false } };
        }
    }
}

/** Singleton instance. */
export const pricelistService = new PricelistService();
