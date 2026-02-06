<x-filament-panels::page>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
        rel="stylesheet" />

    @vite(['resources/css/quote-cards.css', 'resources/js/app.js', 'resources/css/app.css'])

    <style>
        .font-outfit {
            font-family: 'Outfit', sans-serif;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        .dark .glass-effect {
            background: rgba(17, 24, 39, 0.95);
        }

        .card-hoverable {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hoverable:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -10px rgba(0, 0, 0, 0.1);
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Mobile touch improvements for hover group */
        @media (hover: none) {
            .group-actions {
                transform: translateY(0) !important;
                opacity: 1 !important;
            }
        }

        /* Sidebar Input Style Match */
        .sidebar-input {
            width: 100%;
            font-size: 0.875rem;
            border-radius: 0.75rem;
            border: 1px solid rgb(229 231 235);
            background-color: rgb(249 250 251);
            padding: 0.6rem 2rem 0.6rem 0.85rem;
            /* Extra padding right for arrow */
            transition: all 0.15s ease;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
        }

        .dark .sidebar-input {
            border-color: rgb(55 65 81);
            background-color: rgb(30 41 59);
            color: white;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%239ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        }

        .sidebar-input:focus {
            border-color: rgb(16 185 129);
            box-shadow: 0 0 0 2px rgb(16 185 129 / 0.2);
            outline: none;
        }
    </style>

    <div x-data="quoteIndex()" x-init="fetchQuotes(), initPagination()" class="space-y-8 font-outfit">

        {{-- Header Stats --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Cotizaciones -->
            <div
                class="relative overflow-hidden bg-white border border-gray-100 shadow-sm rounded-2xl dark:bg-gray-900 dark:border-gray-800 card-hoverable group">
                <div
                    class="absolute top-0 right-0 w-32 h-32 -mr-8 -mt-8 transition-transform bg-primary-50 rounded-full dark:bg-primary-900/10 group-hover:scale-110">
                </div>
                <div class="relative p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="p-2 rounded-xl bg-primary-50 text-primary-600 dark:bg-primary-900/30 dark:text-primary-400">
                            <span class="text-2xl material-symbols-rounded">description</span>
                        </div>
                        <span
                            class="flex items-center text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full dark:bg-green-900/20 dark:text-green-400">
                            <span class="material-symbols-rounded text-[14px] mr-1">trending_up</span>
                            Actual
                        </span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Cotizaciones</p>
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-1" x-text="stats.total_quotes">0
                        </h3>
                    </div>
                </div>
            </div>

            <!-- Monto Total -->
            <div
                class="relative overflow-hidden bg-white border border-gray-100 shadow-sm rounded-2xl dark:bg-gray-900 dark:border-gray-800 card-hoverable group">
                <div
                    class="absolute top-0 right-0 w-32 h-32 -mr-8 -mt-8 transition-transform bg-green-50 rounded-full dark:bg-green-900/10 group-hover:scale-110">
                </div>
                <div class="relative p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-2 rounded-xl bg-green-50 text-green-600 dark:bg-green-900/30 dark:text-green-400">
                            <span class="text-2xl material-symbols-rounded">payments</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Monto Total</p>
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-1"
                            x-text="'S/ ' + formatNumber(stats.total_amount)">S/ 0.00</h3>
                    </div>
                </div>
            </div>

            <!-- Aprobadas -->
            <div
                class="relative overflow-hidden bg-white border border-gray-100 shadow-sm rounded-2xl dark:bg-gray-900 dark:border-gray-800 card-hoverable group">
                <div
                    class="absolute top-0 right-0 w-32 h-32 -mr-8 -mt-8 transition-transform bg-emerald-50 rounded-full dark:bg-emerald-900/10 group-hover:scale-110">
                </div>
                <div class="relative p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="p-2 rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                            <span class="text-2xl material-symbols-rounded">check_circle</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Aprobadas</p>
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-1" x-text="stats.approved">0</h3>
                    </div>
                </div>
            </div>

            <!-- Pendientes -->
            <div
                class="relative overflow-hidden bg-white border border-gray-100 shadow-sm rounded-2xl dark:bg-gray-900 dark:border-gray-800 card-hoverable group">
                <div
                    class="absolute top-0 right-0 w-32 h-32 -mr-8 -mt-8 transition-transform bg-amber-50 rounded-full dark:bg-amber-900/10 group-hover:scale-110">
                </div>
                <div class="relative p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-2 rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                            <span class="text-2xl material-symbols-rounded">pending</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Por Hacer</p>
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-1" x-text="stats.pending">0</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters Section --}}
        <div
            class="p-2 bg-white border border-gray-200 shadow-sm rounded-2xl dark:bg-gray-900 dark:border-gray-800 sticky top-4 z-10 mx-1">
            <div class="grid grid-cols-1 gap-3 lg:grid-cols-12">
                {{-- Search --}}
                <div class="lg:col-span-4">
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                            <span
                                class="text-gray-400 material-symbols-rounded group-focus-within:text-primary-500 transition-colors">search</span>
                        </div>
                        <input type="text" x-model="search" @input.debounce.500ms="fetchQuotes()"
                            class="block w-full py-3 pl-11 pr-4 text-sm bg-gray-50/50 border-none rounded-xl focus:ring-2 focus:ring-primary-600/20 focus:bg-white transition-all dark:bg-white/5 dark:text-white dark:focus:bg-white/10 dark:placeholder-gray-500"
                            placeholder="Buscar por cliente, cotización, proyecto...">
                    </div>
                </div>

                {{-- Select Filters --}}
                <div class="flex gap-3 overflow-x-auto lg:col-span-8 hide-scrollbar pb-1 lg:pb-0">
                    <select x-model="filterStatus" @change="fetchQuotes()"
                        class="min-w-[140px] sidebar-input appearance-none font-medium text-gray-700 cursor-pointer dark:text-gray-200">
                        <option value="">Estado: Todos</option>
                        <option value="Pendiente">Por Hacer</option>
                        <option value="Enviado">Enviado</option>
                        <option value="Aprobado">Aprobado</option>
                        <option value="Anulado">Anulado</option>
                    </select>

                    <select x-model="filterEmployeeId" @change="fetchQuotes()"
                        class="min-w-[160px] sidebar-input appearance-none font-medium text-gray-700 cursor-pointer dark:text-gray-200">
                        <option value="">Cotizador: Todos</option>
                        <template x-for="emp in stats.employees" :key="emp.id">
                            <option :value="emp.id" x-text="emp.fullname"></option>
                        </template>
                    </select>

                    <select x-model="filterCategoryId" @change="fetchQuotes()"
                        class="min-w-[160px] sidebar-input appearance-none font-medium text-gray-700 cursor-pointer dark:text-gray-200">
                        <option value="">Categoría: Todas</option>
                        <template x-for="cat in stats.categories" :key="cat.id">
                            <option :value="cat.id" x-text="cat.name"></option>
                        </template>
                    </select>

                    <div class="flex items-center gap-2 min-w-[200px] ml-auto">
                        <input type="number" x-model.number="filterMinTotal" @input.debounce.500ms="fetchQuotes()"
                            class="w-full py-3 px-4 text-sm text-center bg-gray-50/50 border-none rounded-xl focus:ring-2 focus:ring-primary-600/20 focus:bg-white transition-all dark:bg-white/5 dark:text-white dark:focus:bg-white/10"
                            placeholder="Min">
                        <span class="text-gray-300 dark:text-gray-600">-</span>
                        <input type="number" x-model.number="filterMaxTotal" @input.debounce.500ms="fetchQuotes()"
                            class="w-full py-3 px-4 text-sm text-center bg-gray-50/50 border-none rounded-xl focus:ring-2 focus:ring-primary-600/20 focus:bg-white transition-all dark:bg-white/5 dark:text-white dark:focus:bg-white/10"
                            placeholder="Max">
                    </div>
                </div>
            </div>
        </div>

        {{-- Skeleton Loading --}}
        <div x-show="loading"
            class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 animate-pulse">
            <template x-for="i in 8">
                <div
                    class="bg-white border border-gray-100 rounded-2xl h-64 dark:bg-gray-900 dark:border-gray-800 p-6 flex flex-col justify-between">
                    <div class="flex justify-between items-start">
                        <div class="w-14 h-14 bg-gray-200 rounded-xl dark:bg-gray-800"></div>
                        <div class="w-20 h-6 bg-gray-200 rounded-lg dark:bg-gray-800"></div>
                    </div>
                    <div class="space-y-3">
                        <div class="h-4 bg-gray-200 rounded w-1/3 dark:bg-gray-800"></div>
                        <div class="h-6 bg-gray-200 rounded w-full dark:bg-gray-800"></div>
                        <div class="h-4 bg-gray-200 rounded w-2/3 dark:bg-gray-800"></div>
                    </div>
                    <div class="flex justify-between items-center pt-4 border-t border-gray-100 dark:border-gray-800">
                        <div class="w-24 h-8 bg-gray-200 rounded dark:bg-gray-800"></div>
                        <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-800"></div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Quotes Grid --}}
        <div x-show="!loading" class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
            <template x-for="quote in paginatedQuotes" :key="quote.id">
                <div
                    class="group relative flex flex-col h-full bg-white border border-gray-200/60 rounded-2xl shadow-[0_2px_8px_rgba(0,0,0,0.04)] dark:bg-gray-900 dark:border-gray-700/60 transition-all duration-300 hover:shadow-[0_12px_24px_rgba(0,0,0,0.08)] hover:-translate-y-1 overflow-hidden">

                    {{-- Status Banner Line --}}
                    <div class="h-1.5 w-full" :class="{
                            'bg-emerald-500': quote.status === 'Aprobado',
                            'bg-amber-500': quote.status === 'Enviado',
                            'bg-blue-500': quote.status === 'Pendiente',
                            'bg-red-500': quote.status === 'Anulado'
                        }">
                    </div>

                    <div class="p-6 flex flex-col flex-grow relative">
                        {{-- Top: Logo & Status --}}
                        <div class="flex items-start justify-between mb-5">
                            <div
                                class="relative w-14 h-14 p-2 bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 flex items-center justify-center overflow-hidden group-hover:border-primary-100 transition-colors">
                                <template x-if="getClientLogo(quote)">
                                    <img :src="'/storage/' + getClientLogo(quote)" class="object-contain w-full h-full"
                                        alt="Logo">
                                </template>
                                <template x-if="!getClientLogo(quote)">
                                    <span class="text-gray-300 material-symbols-rounded text-2xl">business</span>
                                </template>
                            </div>

                            <div class="flex flex-col items-end gap-1">
                                <span
                                    class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-lg border shadow-sm"
                                    :class="{
                                        'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800': quote.status === 'Aprobado',
                                        'bg-amber-50 text-amber-600 border-amber-100 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-800': quote.status === 'Enviado',
                                        'bg-blue-50 text-blue-600 border-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800': quote.status === 'Pendiente',
                                        'bg-red-50 text-red-600 border-red-100 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800': quote.status === 'Anulado'
                                    }" x-text="quote.status">
                                </span>
                                <span class="text-[10px] text-gray-400" x-text="formatDate(quote.created_at)"></span>
                            </div>
                        </div>

                        {{-- Middle: Content --}}
                        <div class="mb-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span
                                    class="text-[10px] font-bold text-gray-500 uppercase tracking-widest bg-gray-100 px-2 py-0.5 rounded dark:bg-gray-800 dark:text-gray-400"
                                    x-text="quote.request_number || 'S/N'">
                                </span>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-tight mb-1 line-clamp-2 group-hover:text-primary-600 transition-colors"
                                x-text="quote.service_name || quote.project?.name || 'Servicio Desconocido'"
                                :title="quote.service_name || quote.project?.name">
                            </h3>
                            <p
                                class="text-xs text-gray-500 dark:text-gray-400 font-medium truncate flex items-center gap-1">
                                <span class="material-symbols-rounded text-[14px]">apartment</span>
                                <span x-text="getClientName(quote)"></span>
                            </p>
                        </div>

                        {{-- Bottom: Price --}}
                        <div class="mt-auto pt-4 border-t border-gray-50 dark:border-gray-800">
                            <div class="flex justify-between items-end">
                                <div>
                                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-0.5">
                                        Monto Total</p>
                                    <p class="text-xl font-bold text-gray-900 dark:text-white font-mono tracking-tight"
                                        x-text="'S/ ' + formatNumber(quote.total_amount || 0)">
                                    </p>
                                </div>

                                {{-- Avatar User --}}
                                <div class="flex items-center" :title="quote.employee?.first_name || 'Sin asignar'">
                                    <div class="flex flex-col items-end mr-2">
                                        <span class="text-[10px] text-gray-400 font-medium">Cotizador</span>
                                        <span class="text-[10px] font-bold text-gray-700 dark:text-gray-300"
                                            x-text="quote.employee?.first_name || 'N/A'"></span>
                                    </div>
                                    <div
                                        class="w-9 h-9 rounded-full border-2 border-white dark:border-gray-900 bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm">
                                        <template x-if="quote.employee?.avatar_url">
                                            <img :src="quote.employee.avatar_url"
                                                class="w-full h-full rounded-full object-cover">
                                        </template>
                                        <template x-if="!quote.employee?.avatar_url">
                                            <span x-text="quote.employee ? quote.employee.first_name[0] : '?'"></span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Overlay Actions (Hover + Touch) --}}
                    <div
                        class="group-actions absolute inset-x-0 bottom-0 p-4 bg-white/95 backdrop-blur-md dark:bg-gray-900/95 border-t border-gray-100 dark:border-gray-800 transform translate-y-full group-hover:translate-y-0 transition-all duration-300 ease-out flex justify-between gap-2 z-10">
                        <button @click="window.location.href='/dashboard/quotes/' + quote.id + '/edit'"
                            class="flex-1 flex items-center justify-center gap-2 py-2 text-xs font-bold text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors shadow-lg shadow-primary-500/20 active:scale-95">
                            <span class="material-symbols-rounded text-base">edit</span>
                            Editar
                        </button>

                        <div class="flex gap-2">
                            <button @click="window.open('/quotes/' + quote.id + '/preview', '_blank')"
                                class="w-9 h-9 flex items-center justify-center text-gray-500 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-primary-600 hover:border-primary-100 transition-colors dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700 dark:hover:bg-gray-700 active:scale-95"
                                title="Vista Previa">
                                <span class="material-symbols-rounded">visibility</span>
                            </button>
                            <button @click="deleteQuote(quote.id)"
                                class="w-9 h-9 flex items-center justify-center text-gray-500 bg-white border border-gray-200 rounded-lg hover:bg-red-50 hover:text-red-600 hover:border-red-100 transition-colors dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700 dark:hover:bg-gray-700 active:scale-95"
                                title="Eliminar">
                                <span class="material-symbols-rounded">delete</span>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Empty State --}}
        <div x-show="!loading && quotes.length === 0"
            class="flex flex-col items-center justify-center py-24 text-center bg-white border border-gray-100 border-dashed rounded-3xl dark:bg-gray-900 dark:border-gray-800">
            <div class="flex items-center justify-center w-24 h-24 mb-6 rounded-3xl bg-gray-50 dark:bg-gray-800">
                <span class="text-5xl text-gray-300 material-symbols-rounded dark:text-gray-600">search_off</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">No hay cotizaciones</h3>
            <p class="mt-2 text-gray-500 dark:text-gray-400 max-w-sm mx-auto text-base">No hemos encontrado ninguna
                cotización con los filtros actuales. Intenta limpiar la búsqueda.</p>
            <button @click="resetFilters()"
                class="mt-6 px-6 py-2.5 bg-gray-900 text-white rounded-xl font-medium text-sm hover:bg-gray-800 transition-colors dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">
                Limpiar Filtros
            </button>
        </div>

        {{-- Pagination --}}
        <div x-show="!loading && quotes.length > 0"
            class="flex items-center justify-between px-6 py-4 bg-white border border-gray-100 rounded-2xl shadow-sm dark:bg-gray-900 dark:border-gray-800 sticky bottom-4 z-10 mx-1">
            <button @click="previousPage()" :disabled="currentPage === 1"
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 transition-all bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:border-gray-300 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700">
                <span class="material-symbols-rounded text-lg">arrow_back</span>
                Anterior
            </button>
            <span
                class="text-xs font-bold text-gray-500 dark:text-gray-400 font-outfit uppercase tracking-wider bg-gray-50 px-3 py-1.5 rounded-lg dark:bg-white/5"
                x-text="'Página ' + currentPage + ' de ' + totalPages"></span>
            <button @click="nextPage()" :disabled="currentPage === totalPages"
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 transition-all bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:border-gray-300 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700">
                Siguiente
                <span class="material-symbols-rounded text-lg">arrow_forward</span>
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function quoteIndex() {
            return {
                quotes: [],
                loading: true,
                search: '',
                filterStatus: '',
                filterEmployeeId: '',
                filterMinTotal: null,
                filterMaxTotal: null,
                filterCategoryId: '',
                currentPage: 1,
                totalPages: 1,
                stats: {
                    total_quotes: 0,
                    total_amount: 0,
                    approved: 0,
                    pending: 0,
                    employees: [],
                    categories: [],
                },
                perPage: 12,
                get paginatedQuotes() {
                    const start = (this.currentPage - 1) * this.perPage;
                    return this.quotes.slice(start, start + this.perPage);
                },
                getClientName(quote) {
                    if (quote.sub_client?.name) return quote.sub_client.name;
                    if (quote.project?.sub_client?.name) return quote.project.sub_client.name;
                    return 'Cliente sin asignar';
                },
                getClientLogo(quote) {
                    if (quote.sub_client?.client?.logo) return quote.sub_client.client.logo;
                    if (quote.project?.sub_client?.client?.logo) return quote.project.sub_client.client.logo;
                    return null;
                },
                formatDate(dateString) {
                    if (!dateString) return '';
                    const date = new Date(dateString);
                    return date.toLocaleDateString('es-PE', { day: '2-digit', month: 'short', year: 'numeric' });
                },
                resetFilters() {
                    this.search = '';
                    this.filterStatus = '';
                    this.filterEmployeeId = '';
                    this.filterCategoryId = '';
                    this.filterMinTotal = null;
                    this.filterMaxTotal = null;
                    this.currentPage = 1;
                    this.fetchQuotes();
                },
                async fetchCategories() {
                    try {
                        const response = await fetch('/quotes/categories');
                        const data = await response.json();
                        this.stats.categories = data;
                    } catch (e) {
                        console.error('Error fetching categories:', e);
                    }
                },
                async fetchQuotes() {
                    this.loading = true;
                    try {
                        const params = new URLSearchParams({
                            q: this.search,
                            status: this.filterStatus,
                            page: this.currentPage
                        });
                        if (this.filterEmployeeId) params.append('employee_id', this.filterEmployeeId);
                        if (this.filterCategoryId) params.append('category', this.filterCategoryId);
                        if (this.filterMinTotal) params.append('min_total', this.filterMinTotal);
                        if (this.filterMaxTotal) params.append('max_total', this.filterMaxTotal);

                        // Small delay to prevent flickering on fast loads and show skeleton
                        await new Promise(r => setTimeout(r, 300));

                        const response = await fetch(`/quotes?${params}`);
                        const data = await response.json();

                        this.quotes = data.data || [];
                        this.totalPages = data.last_page || 1;

                        this.fetchStatistics();
                    } catch (e) {
                        console.error('Error fetching quotes:', e);
                        this.quotes = [];
                    }
                    this.loading = false;
                },

                async deleteQuote(quoteId) {
                    const result = await Swal.fire({
                        title: '¿Eliminar cotización?',
                        text: "Esta acción no se puede deshacer",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'bg-red-600 hover:bg-red-700 text-white font-medium py-2.5 px-5 rounded-lg transition-colors mx-2',
                            cancelButton: 'bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 font-medium py-2.5 px-5 rounded-lg transition-colors mx-2',
                            popup: 'rounded-2xl shadow-xl dark:bg-gray-900',
                            title: 'text-xl font-bold text-gray-900 dark:text-white',
                            htmlContainer: 'text-gray-500 dark:text-gray-400'
                        }
                    });

                    if (result.isConfirmed) {
                        try {
                            const response = await fetch(`/quotes/${quoteId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json'
                                }
                            });

                            if (response.ok) {
                                Swal.fire({
                                    title: 'Eliminado',
                                    text: 'La cotización ha sido eliminada correctamente',
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false,
                                    customClass: {
                                        popup: 'rounded-2xl shadow-xl dark:bg-gray-900',
                                        title: 'text-lg font-bold text-gray-900 dark:text-white'
                                    }
                                });
                                this.fetchQuotes();
                            } else {
                                throw new Error('Error deleting');
                            }
                        } catch (error) {
                            Swal.fire({
                                title: 'Error',
                                text: 'No se pudo eliminar la cotización',
                                icon: 'error',
                                customClass: {
                                    popup: 'rounded-2xl shadow-xl dark:bg-gray-900'
                                }
                            });
                        }
                    }
                },

                initPagination() {
                    this.fetchCategories();
                    this.fetchQuotes();
                },

                nextPage() {
                    if (this.currentPage < this.totalPages) {
                        this.currentPage++;
                        this.fetchQuotes();
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                },

                previousPage() {
                    if (this.currentPage > 1) {
                        this.currentPage--;
                        this.fetchQuotes();
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                },

                formatNumber(num) {
                    return parseFloat(num || 0).toLocaleString('es-PE', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                },

                async fetchStatistics() {
                    try {
                        const response = await fetch('/quotes/stats');
                        const data = await response.json();
                        this.stats = { ...this.stats, ...data };
                    } catch (e) {
                        console.error('Error fetching statistics:', e);
                    }
                },
            }
        }
    </script>
</x-filament-panels::page>