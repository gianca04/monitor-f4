{{-- Quote Sidebar Component (Compact Enterprise Panel) --}}
{{-- Usage: @include('filament.resources.quote-resource.components.quote-sidebar') --}}

<div class="quote-sidebar">
    {{-- Header Bar (Always visible) --}}
    <div @click="sidebarOpen = !sidebarOpen" class="quote-sidebar__header">
        <div class="flex items-center gap-3">
            {{-- Avatar --}}
            <div class="quote-sidebar__avatar">
                {{ substr(auth()->user()->employee->full_name, 0, 1) . (strpos(auth()->user()->employee->full_name, ' ') !== false ? substr(auth()->user()->employee->full_name, strpos(auth()->user()->employee->full_name, ' ') + 1, 1) : '') }}
            </div>
            {{-- Info --}}
            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-3">
                <div class="flex items-center gap-1.5">
                    <span class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">Cotizador:</span>
                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-200">
                        {{ auth()->user()->employee->full_name }}
                    </span>
                </div>
                {{-- Collapsed summary --}}
                <template x-if="!sidebarOpen && (quote.client_id || quote.quote_category_id)">
                    <div class="flex items-center gap-2 text-[10px] text-gray-500 dark:text-gray-400">
                        <span class="hidden sm:block w-px h-3 bg-gray-300 dark:bg-gray-600"></span>
                        <span x-show="quote.quote_category_id"
                            class="px-1.5 py-0.5 bg-emerald-50 dark:bg-emerald-900/30 rounded font-bold text-emerald-600 dark:text-emerald-400"
                            x-text="quoteCategories.find(c => c.id == quote.quote_category_id)?.name || ''"></span>
                        <span x-show="clientSearch" class="truncate max-w-[120px]" x-text="clientSearch"></span>
                    </div>
                </template>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if(isset($projectUrl) && $projectUrl)
                <a href="{{ $projectUrl }}" @click.stop
                    class="hidden sm:inline-flex items-center gap-1 px-2 py-1 text-[10px] font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded transition-colors border border-blue-200">
                    <span class="material-symbols-outlined text-xs">arrow_forward</span>
                    Proyecto
                </a>
            @endif
            <span class="hidden sm:inline text-[10px] font-medium text-gray-400"
                x-text="sidebarOpen ? 'Ocultar' : 'Datos'"></span>
            <span
                class="material-symbols-outlined text-gray-400 text-sm p-0.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition-all duration-200"
                :class="sidebarOpen ? 'rotate-180' : ''">expand_more</span>
        </div>
    </div>

    {{-- Collapsible Content --}}
    <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2" class="quote-sidebar__body">

        <input type="hidden" name="employee_id" value="{{ auth()->user()->employee->id }}">

        {{-- Row 1: Request Number + Service Name --}}
        <div class="grid grid-cols-1 sm:grid-cols-[200px_1fr] gap-3 mb-3">
            <div>
                <label class="quote-sidebar__label">N° Solicitud</label>
                <div class="relative">
                    <input class="quote-sidebar__input quote-sidebar__input--readonly" type="text"
                        x-model="quote.request_number"
                        :value="quote.request_number || '{{ $suggestedRequestNumber ?? '' }}'" readonly />
                    <div class="absolute inset-y-0 right-2 flex items-center pointer-events-none">
                        <span class="material-symbols-outlined text-gray-300 text-sm">lock</span>
                    </div>
                </div>
                <input type="hidden" name="request_number"
                    :value="quote.request_number || '{{ $suggestedRequestNumber ?? '' }}'">
                <input type="hidden" name="project_id" :value="quote.project_id || '{{ $suggestedProjectId ?? '' }}'">
            </div>
            <div>
                <label class="quote-sidebar__label">Nombre del Servicio</label>
                <input x-model="quote.project_name" :readonly="!!projectFromPHP?.name"
                    :class="{'quote-sidebar__input--readonly': !!projectFromPHP?.name}" class="quote-sidebar__input"
                    type="text" placeholder="Ej: Mantenimiento preventivo de equipos..." />
                <input type="hidden" name="project_name" x-model="quote.project_name">
            </div>
        </div>

        {{-- Row 2: Compact field grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-3">
            {{-- Tipo Cotización --}}
            <div>
                <label class="quote-sidebar__label">Tipo</label>
                <select x-model="quoteType" class="quote-sidebar__input quote-sidebar__input--select"
                    :class="quoteType === 'Preventivo' ? 'text-purple-700 bg-purple-50 dark:text-purple-400 dark:bg-purple-900/20' : 'text-emerald-700 bg-emerald-50 dark:text-emerald-400 dark:bg-emerald-900/20'">
                    <option value="Correctivo">Correctivo</option>
                    <option value="Preventivo">Preventivo</option>
                </select>
            </div>

            {{-- Categoría --}}
            <div>
                <label class="quote-sidebar__label">Categoría</label>
                <select x-model.number="quote.quote_category_id"
                    class="quote-sidebar__input quote-sidebar__input--select">
                    <option value="">Seleccionar...</option>
                    <template x-for="category in quoteCategories" :key="category.id">
                        <option :value="category.id" x-text="category.name"
                            :selected="category.id == quote.quote_category_id"></option>
                    </template>
                </select>
                <input type="hidden" name="quote_category_id" x-model="quote.quote_category_id">
            </div>

            {{-- Cliente --}}
            <div>
                <label class="quote-sidebar__label">Cliente</label>
                <div class="searchable-select" @click.away="clientDropdownOpen = false">
                    <input type="text" x-model="clientSearch" @focus="clientDropdownOpen = true"
                        @click="clientDropdownOpen = true" @input="filterClients()"
                        :disabled="!!projectFromPHP?.sub_client_id" placeholder="Buscar..."
                        class="quote-sidebar__input pr-7" />
                    <div class="searchable-select-icon" :class="{ 'clickable': quote.client_id }">
                        <template x-if="quote.client_id && !projectFromPHP?.sub_client_id">
                            <button @click="clearClient()" type="button" class="p-0.5 hover:bg-gray-100 rounded-full">
                                <svg fill="none" class="w-3.5 h-3.5" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </template>
                        <template x-if="!quote.client_id">
                            <svg fill="none" class="w-3.5 h-3.5" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </template>
                    </div>
                    <div x-show="clientDropdownOpen && filteredClients.length > 0" x-transition
                        class="searchable-select-dropdown">
                        <template x-for="client in filteredClients" :key="client.id">
                            <div @click="selectClientFromDropdown(client)"
                                :class="{ 'selected': quote.client_id == client.id }" class="searchable-select-item">
                                <div class="text-xs searchable-select-item-title" x-text="client.business_name"></div>
                                <div class="searchable-select-item-subtitle"
                                    x-text="client.document_number || 'Sin RUC'"></div>
                            </div>
                        </template>
                    </div>
                </div>
                <input type="hidden" name="client_id" x-model="quote.client_id">
            </div>

            {{-- SubCliente --}}
            <div>
                <label class="quote-sidebar__label">Tienda / SubCliente</label>
                <div class="searchable-select" @click.away="subClientDropdownOpen = false">
                    <input type="text" x-model="subClientSearch" @focus="subClientDropdownOpen = true"
                        @click="subClientDropdownOpen = true" @input="filterSubClients()"
                        :disabled="!!projectFromPHP?.sub_client_id || !quote.client_id || loadingSubClients"
                        :placeholder="loadingSubClients ? 'Cargando...' : (!quote.client_id ? 'Primero cliente...' : 'Buscar...')"
                        class="quote-sidebar__input pr-7" />
                    <div class="searchable-select-icon" :class="{ 'clickable': quote.sub_client_id }">
                        <template x-if="quote.sub_client_id && !projectFromPHP?.sub_client_id">
                            <button @click="clearSubClient()" type="button"
                                class="p-0.5 hover:bg-gray-100 rounded-full">
                                <svg fill="none" class="w-3.5 h-3.5" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </template>
                        <template x-if="!quote.sub_client_id && !loadingSubClients">
                            <svg fill="none" class="w-3.5 h-3.5" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </template>
                    </div>
                    <div x-show="subClientDropdownOpen && filteredSubClients.length > 0" x-transition
                        class="searchable-select-dropdown">
                        <template x-for="subClient in filteredSubClients" :key="subClient.id">
                            <div @click="selectSubClientFromDropdown(subClient)"
                                :class="{ 'selected': quote.sub_client_id == subClient.id }"
                                class="searchable-select-item">
                                <div class="text-xs searchable-select-item-title" x-text="subClient.name"></div>
                                <div class="searchable-select-item-subtitle" x-text="subClient.ceco || 'Sin CECO'">
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Gerente --}}
            <div>
                <label class="quote-sidebar__label">Gerente Energy SCI</label>
                <input x-model="quote.energy_sci_manager" class="quote-sidebar__input" type="text"
                    placeholder="Nombre..." />
            </div>
        </div>

        {{-- Row 3: Dates + Status + CECO --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div>
                <label class="quote-sidebar__label">Fecha Cotización</label>
                <input x-model="quote.quote_date" class="quote-sidebar__input" type="date" />
            </div>
            <div>
                <label class="quote-sidebar__label">Fecha Ejecución</label>
                <input x-model="quote.execution_date" class="quote-sidebar__input" type="date" />
            </div>
            <div>
                <label class="quote-sidebar__label">Estado</label>
                <select x-model="quote.status" class="quote-sidebar__input quote-sidebar__input--select font-bold"
                    :class="{
                    'text-amber-600': quote.status == 'Pendiente',
                    'text-blue-600': quote.status == 'Enviado',
                    'text-emerald-600': quote.status == 'Aprobado',
                    'text-red-600': quote.status == 'Anulado'
                }">
                    <option value="Pendiente">Pendiente</option>
                    <option value="Enviado">Enviado</option>
                    <option value="Aprobado">Aprobado</option>
                    <option value="Anulado">Anulado</option>
                </select>
            </div>
            <div>
                <label class="quote-sidebar__label">CECO</label>
                <input x-model="quote.ceco"
                    class="quote-sidebar__input quote-sidebar__input--readonly font-mono text-[10px] text-gray-400"
                    type="text" readonly placeholder="Automático" />
            </div>
        </div>
    </div>
</div>