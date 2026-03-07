/**
 * Quote Module Constants
 * Centralized configuration for sections, mappings, and defaults.
 */

/** Section definitions used by boards and the UI. */
export const SECTIONS = [
    {
        key: 'viaticos',
        title: 'Viáticos',
        icon: 'flight_takeoff',
        priceTypeId: 3,
        bgClass: 'bg-blue-100 dark:bg-blue-900/30',
        iconClass: 'text-blue-600 dark:text-blue-400',
    },
    {
        key: 'suministros',
        title: 'Suministros',
        icon: 'inventory_2',
        priceTypeId: 2,
        bgClass: 'bg-amber-100 dark:bg-amber-900/30',
        iconClass: 'text-amber-600 dark:text-amber-400',
    },
    {
        key: 'mano_obra',
        title: 'Mano de Obra',
        icon: 'engineering',
        priceTypeId: 2,
        bgClass: 'bg-purple-100 dark:bg-purple-900/30',
        iconClass: 'text-purple-600 dark:text-purple-400',
    },
    {
        key: 'consumibles',
        title: 'Consumibles',
        icon: 'shopping_cart',
        priceTypeId: 2,
        bgClass: 'bg-emerald-100 dark:bg-emerald-900/30',
        iconClass: 'text-emerald-600 dark:text-emerald-400',
    },
    {
        key: 'transporte',
        title: 'Transporte',
        icon: 'local_shipping',
        priceTypeId: 2,
        bgClass: 'bg-rose-100 dark:bg-rose-900/30',
        iconClass: 'text-rose-600 dark:text-rose-400',
    },
];

/** Maps database item_type values to frontend section keys. */
export const ITEM_TYPE_MAP = {
    VIATICOS: 'viaticos',
    SUMINISTRO: 'suministros',
    'MANO DE OBRA': 'mano_obra',
    CONSUMIBLE: 'consumibles',
    TRANSPORTE: 'transporte',
};

/** Reverse map: frontend section keys → database item_type values. */
export const SECTION_TO_ITEM_TYPE = {
    viaticos: 'VIATICOS',
    suministros: 'SUMINISTRO',
    mano_obra: 'MANO DE OBRA',
    consumibles: 'CONSUMIBLE',
    transporte: 'TRANSPORTE',
};

/** Sections that belong ONLY in the GLOBAL tab (Preventivo) */
export const GLOBAL_SECTIONS = ['viaticos', 'consumibles', 'transporte'];

/** Sections that belong ONLY in the REGULAR equipment tabs (Preventivo) */
export const REGULAR_SECTIONS = ['suministros', 'mano_obra'];

/** Creates a fresh empty items object matching all section keys. */
export function createEmptyItems() {
    return {
        viaticos: [],
        suministros: [],
        mano_obra: [],
        consumibles: [],
        transporte: [],
    };
}

/**
 * Factory: creates one empty board.
 * @param {string} name - Board display name.
 * @returns {object}
 */
export function createEmptyBoard(name = 'CORRECTIVO') {
    return {
        id: crypto.randomUUID(),
        name,
        items: createEmptyItems(),
    };
}

/** Default column widths for the items table. */
export const DEFAULT_COLUMN_WIDTHS = {
    code: 80,
    description: 300,
    comment: 150,
    unit: 60,
    quantity: 70,
    unit_price: 90,
    subtotal: 100,
};

/** Default quote header template. */
export function createDefaultQuote(project = null, suggestedRequestNumber = null, defaultCategoryId = null) {
    return {
        id: null,
        request_number: suggestedRequestNumber || project?.service_code || '',
        employee_id: null,
        project_name: project?.name || '',
        client_id: project?.client_id || null,
        sub_client_id: project?.sub_client_id || null,
        quote_category_id: defaultCategoryId,
        energy_sci_manager: 'Raul Quispe',
        ceco: '',
        status: 'Pendiente',
        quote_date: new Date().toISOString().split('T')[0],
        execution_date: '',
        service_code: project?.service_code || '',
        project_id: project?.id || null,
    };
}
