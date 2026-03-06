import { SECTIONS, SECTION_TO_ITEM_TYPE, createEmptyBoard, createDefaultQuote } from './constants.js';
import { clientService } from './services/ClientService.js';
import { useBoardManager } from './composables/useBoardManager.js';
import { useSearchModal } from './composables/useSearchModal.js';
import { useDragDrop } from './composables/useDragDrop.js';
import { useColumnResize } from './composables/useColumnResize.js';

/**
 * quoteManager — Alpine.js component factory.
 *
 * Receives PHP-injected data and returns the full reactive data object.
 * All complex logic is delegated to composables; this file acts as
 * a thin orchestrator that wires everything together.
 */
export function quoteManager(
    categoriesFromPHP = [],
    clientsFromPHP = [],
    priceTypesFromPHP = [],
    existingQuote = null,
    projectFromPHP = null,
    quoteCount = 1,
    subClientId = null,
    serviceCode = null,
    projectId = null,
    suggestedRequestNumber = null,
    quoteType = 'Correctivo'
) {
    // Resolve default category
    const defaultCategoryId =
        categoriesFromPHP.find((c) => c.name === 'II.EE. Baja Tensión')?.id || null;

    return {
        // ─── Spread composables ─────────────────────────────
        ...useBoardManager(),
        ...useSearchModal(),
        ...useDragDrop(),
        ...useColumnResize(),

        // ─── Static config ──────────────────────────────────
        sections: SECTIONS,
        quoteCategories: categoriesFromPHP,
        allClients: clientsFromPHP,
        priceTypes: priceTypesFromPHP.map((pt) => ({
            id: pt.id,
            name: pt.name,
            shortName: pt.name.split(' ')[0],
        })),

        // ─── Quote header ───────────────────────────────────
        quote: existingQuote
            ? {
                id: existingQuote.id,
                request_number: existingQuote.request_number || '',
                employee_id: existingQuote.employee_id || null,
                project_name: existingQuote.project?.name || '',
                client_id: existingQuote.sub_client?.client_id || null,
                sub_client_id: existingQuote.sub_client_id,
                quote_category_id: existingQuote.quote_category_id,
                energy_sci_manager: existingQuote.energy_sci_manager || '',
                ceco: existingQuote.ceco || existingQuote.sub_client?.ceco || '',
                status: existingQuote.status,
                quote_date: existingQuote.quote_date?.split('T')[0] || '',
                execution_date: existingQuote.execution_date?.split('T')[0] || '',
                service_code: projectFromPHP?.service_code || '',
                project_id: existingQuote.project_id || projectFromPHP?.id || projectId || null,
            }
            : createDefaultQuote(projectFromPHP, suggestedRequestNumber, defaultCategoryId),

        // Override quoteType from PHP param
        quoteType,

        // ─── UI State ───────────────────────────────────────
        sidebarOpen: true,
        saving: false,
        igvRate: 0.18,

        // Client search
        subClients: [],
        loadingSubClients: false,
        subClientSearch: '',
        subClientDropdownOpen: false,
        filteredSubClients: [],
        subClientSearchTimeout: null,
        clientSearch: '',
        clientDropdownOpen: false,
        filteredClients: [],

        // Expose for Blade
        projectFromPHP,

        // ─── Lifecycle ──────────────────────────────────────

        init() {
            this.filteredClients = [...this.allClients];

            if (existingQuote) {
                this._initFromExistingQuote(existingQuote);
            } else {
                this._initFromProject(projectFromPHP, suggestedRequestNumber);
            }

            this.setupQuoteTypeWatcher();
        },

        /** @private */
        _initFromExistingQuote(eq) {
            // Client search UI
            if (this.quote.client_id) {
                const client = this.allClients.find((c) => c.id === this.quote.client_id);
                if (client) {
                    this.clientSearch = client.business_name;
                    if (eq.sub_client) {
                        this.subClientSearch = eq.sub_client.name;
                        if (!this.quote.ceco) {
                            this.quote.ceco = eq.sub_client.ceco || '';
                        }
                    }
                    this.loadSubClients(client.id).then(() => {
                        if (this.quote.sub_client_id && !this.subClientSearch) {
                            const sc = this.subClients.find((s) => s.id === this.quote.sub_client_id);
                            if (sc) this.subClientSearch = sc.name;
                        }
                    });
                }
            }

            // Boards
            if (eq.quote_groups?.length > 0) {
                this.initBoardsFromGroups(eq.quote_groups);
            } else if (eq.quote_details?.length > 0) {
                this.initBoardsFromDetails(eq.quote_details);
            } else {
                this.initDefaultBoard();
            }
        },

        /** @private */
        _initFromProject(project, suggestedReq) {
            if (project) {
                this.quote.client_id = project.client_id || null;
                this.quote.sub_client_id = project.sub_client_id || null;
                this.quote.service_code = project.service_code || '';
                this.quote.request_number = project.service_code || '';
                this.quote.project_id = project.id || null;
            }

            this.initDefaultBoard();

            if (suggestedReq) {
                this.quote.request_number = suggestedReq;
            }

            // Pre-fill sub-client if project has one
            if (project?.sub_client_id) {
                clientService.getSubClient(project.sub_client_id).then((subClient) => {
                    if (!subClient) return;
                    this.quote.sub_client_id = subClient.id;
                    this.subClientSearch = subClient.name;
                    this.quote.ceco = subClient.ceco || '';
                    this.quote.client_id = subClient.client_id;
                    const client = this.allClients.find((c) => c.id === subClient.client_id);
                    if (client) this.clientSearch = client.business_name;
                    this.loadSubClients(subClient.client_id);
                });
            }
        },

        // ─── Client / SubClient Methods ─────────────────────

        filterClients() {
            const q = this.clientSearch.toLowerCase().trim();
            this.filteredClients = q
                ? this.allClients.filter(
                    (c) =>
                        c.business_name.toLowerCase().includes(q) ||
                        (c.document_number && c.document_number.includes(q))
                )
                : [...this.allClients];
            this.clientDropdownOpen = true;
        },

        selectClientFromDropdown(client) {
            this.quote.client_id = client.id;
            this.clientSearch = client.business_name;
            this.clientDropdownOpen = false;
            this.quote.sub_client_id = null;
            this.quote.ceco = '';
            this.subClients = [];
            this.filteredSubClients = [];
            this.subClientSearch = '';
            this.subClientDropdownOpen = false;
            this.loadSubClients(client.id);
        },

        clearClient() {
            this.quote.client_id = null;
            this.clientSearch = '';
            this.filteredClients = [...this.allClients];
            this.quote.sub_client_id = null;
            this.quote.ceco = '';
            this.subClients = [];
            this.filteredSubClients = [];
            this.subClientSearch = '';
        },

        async loadSubClients(clientId, search = '') {
            this.loadingSubClients = true;
            try {
                this.subClients = await clientService.loadSubClients(clientId, search);
                this.filteredSubClients = [...this.subClients];
            } catch {
                this.subClients = [];
                this.filteredSubClients = [];
            } finally {
                this.loadingSubClients = false;
            }
        },

        filterSubClients() {
            if (this.subClientSearchTimeout) clearTimeout(this.subClientSearchTimeout);
            const q = this.subClientSearch.toLowerCase().trim();
            if (!this.quote.client_id) return;
            this.subClientDropdownOpen = true;
            this.subClientSearchTimeout = setTimeout(() => {
                this.loadSubClients(this.quote.client_id, q);
            }, 400);
        },

        selectSubClientFromDropdown(subClient) {
            this.quote.sub_client_id = subClient.id;
            this.subClientSearch = subClient.name;
            this.quote.ceco = subClient.ceco || 'No definido';
            this.subClientDropdownOpen = false;
        },

        clearSubClient() {
            this.quote.sub_client_id = null;
            this.subClientSearch = '';
            this.quote.ceco = '';
            this.filteredSubClients = [...this.subClients];
        },

        // ─── Item Methods ───────────────────────────────────

        removeItem(bIndex, sectionKey, index) {
            this.boards[bIndex].items[sectionKey].splice(index, 1);
        },

        recalculate() { /* Alpine reactivity handles this */ },

        // ─── Calculations ───────────────────────────────────

        getSectionSubtotal(bIndex, sectionKey) {
            return this.boards[bIndex].items[sectionKey].reduce(
                (sum, item) => sum + (parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0),
                0
            );
        },

        getTotalItems() {
            let total = 0;
            this.boards.forEach((board) => {
                Object.values(board.items).forEach((arr) => (total += arr.length));
            });
            return total;
        },

        getSubtotal() {
            let total = 0;
            this.boards.forEach((_, bIndex) => {
                this.sections.forEach((s) => (total += this.getSectionSubtotal(bIndex, s.key)));
            });
            return total;
        },

        getTotal() {
            return Math.round(this.getSubtotal() * 10) / 10;
        },

        // ─── Persistence ────────────────────────────────────

        async saveQuote() {
            this.saving = true;
            try {
                const groupsData = this.boards.map((board) => {
                    const combinedItems = [];
                    for (const [sectionKey, items] of Object.entries(board.items)) {
                        const itemType = SECTION_TO_ITEM_TYPE[sectionKey];
                        items.forEach((item) => {
                            combinedItems.push({
                                ...item,
                                item_type: itemType,
                                budget_code: item.code,
                                pricelist_id: item.pricelist_id,
                            });
                        });
                    }
                    return { name: board.name, items: combinedItems };
                });

                const quoteData = {
                    request_number: this.quote.request_number,
                    project_id: this.quote.project_id,
                    employee_id: this.quote.employee_id,
                    project_name: this.quote.project_name,
                    sub_client_id: this.quote.sub_client_id,
                    quote_category_id: this.quote.quote_category_id,
                    energy_sci_manager: this.quote.energy_sci_manager,
                    ceco: this.quote.ceco,
                    status: this.quote.status,
                    quote_date: this.quote.quote_date,
                    execution_date: this.quote.execution_date,
                    groups: groupsData,
                };

                const url = this.quote.id ? `/quotes/${this.quote.id}` : '/quotes';
                const method = this.quote.id ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        Accept: 'application/json',
                    },
                    body: JSON.stringify(quoteData),
                });

                const result = await response.json();

                if (response.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: this.quote.id ? '¡Cotización actualizada!' : '¡Cotización creada!',
                        text: this.quote.id
                            ? 'La cotización se ha actualizado correctamente.'
                            : 'La cotización se ha guardado correctamente.',
                        timer: 1800,
                        showConfirmButton: false,
                    });
                    if (!this.quote.id) {
                        setTimeout(() => {
                            window.location.href = `/dashboard/quotes/${result.id}/edit`;
                        }, 1800);
                    }
                } else if (result.errors) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Errores de validación',
                        html: Object.values(result.errors).flat().join('<br>'),
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: result.message || 'Error desconocido al guardar la cotización',
                    });
                }
            } catch {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'Error de conexión al guardar la cotización',
                });
            } finally {
                this.saving = false;
            }
        },

        resetForm() {
            this.quote = createDefaultQuote(projectFromPHP, suggestedRequestNumber, defaultCategoryId);
            this.clientSearch = '';
            this.clientDropdownOpen = false;
            this.filteredClients = [...this.allClients];
            this.subClients = [];
            this.filteredSubClients = [];
            this.subClientSearch = '';
            this.subClientDropdownOpen = false;
            this.boards = [
                createEmptyBoard(this.quoteType === 'Preventivo' ? 'Tablero 1' : 'CORRECTIVO'),
            ];
        },
    };
}
