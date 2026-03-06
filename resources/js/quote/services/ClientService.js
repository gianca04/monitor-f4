/**
 * ClientService
 * Handles API calls related to clients and sub-clients.
 */
export class ClientService {
    /**
     * Load sub-clients for a given client, optionally filtered by search term.
     * @param {number} clientId
     * @param {string} search
     * @returns {Promise<Array>}
     */
    async loadSubClients(clientId, search = '') {
        try {
            let url = `/api/sub-clients?client_id=${clientId}`;
            if (search && search.length > 0) {
                url += `&q=${encodeURIComponent(search)}`;
            }
            const res = await fetch(url);
            const data = await res.json();
            return data.data || data;
        } catch (err) {
            console.error('ClientService.loadSubClients:', err);
            return [];
        }
    }

    /**
     * Get a single sub-client by ID.
     * @param {number} id
     * @returns {Promise<object|null>}
     */
    async getSubClient(id) {
        try {
            const res = await fetch(`/api/sub-clients/${id}`);
            return await res.json();
        } catch (err) {
            console.error('ClientService.getSubClient:', err);
            return null;
        }
    }
}

/** Singleton instance. */
export const clientService = new ClientService();
