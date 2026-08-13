{{-- Quote Sidebar Component (Compact Enterprise Panel) --}}
{{-- Usage: @include('filament.resources.quote-resource.components.quote-sidebar') --}}

<div class="bg-white dark:bg-gray-950">
    {{-- Header Bar --}}
    <div class="flex items-center justify-between px-3.5 py-2.5 border-b border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/50">
        <div class="flex items-center gap-2.5">
            {{-- Avatar --}}
            <div
                class="flex items-center justify-center w-6 h-6 rounded-full bg-gray-900 text-white font-semibold text-[10px] dark:bg-gray-100 dark:text-gray-900 shrink-0">
                {{ substr(auth()->user()->employee->full_name, 0, 1) . (strpos(auth()->user()->employee->full_name, ' ') !== false ? substr(auth()->user()->employee->full_name, strpos(auth()->user()->employee->full_name, ' ') + 1, 1) : '') }}
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold text-gray-900 dark:text-gray-100">Datos de la Cotización</span>
                <span class="text-[10px] text-gray-400">·</span>
                <span class="text-[11px] text-gray-500 font-medium">{{ auth()->user()->employee->full_name }}</span>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if(isset($projectUrl) && $projectUrl)
                <a href="{{ $projectUrl }}" target="_blank"
                    class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-medium text-gray-900 bg-white border border-gray-200 rounded-md hover:bg-gray-100 transition-colors dark:bg-gray-900 dark:border-gray-700 dark:text-gray-100">
                    <span class="material-symbols-outlined text-[12px]">arrow_forward</span>
                    Proyecto
                </a>
            @endif
            <button @click="sidebarOpen = false" type="button"
                class="p-1 text-gray-400 hover:text-gray-700 hover:bg-gray-200/60 dark:hover:bg-gray-800 rounded-md transition-all">
                <span class="material-symbols-outlined text-[16px]">close</span>
            </button>
        </div>
    </div>

    {{-- Content --}}
    <div class="p-3 bg-white dark:bg-gray-950">

        <input type="hidden" name="employee_id" value="{{ auth()->user()->employee->id }}">

        {{-- Row 1: Request Number + Service Name --}}
        <div class="grid grid-cols-1 sm:grid-cols-[180px_1fr] gap-3 mb-3">
            <div class="flex flex-col gap-1">
                <label class="text-[11px] font-medium text-gray-700 dark:text-gray-300">N° Solicitud</label>
                <div class="relative">
                    <input
                        class="flex h-8 w-full rounded-md border border-gray-200 bg-gray-50 px-2.5 py-1 text-xs shadow-sm opacity-70 cursor-not-allowed dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                        type="text" x-model="quote.request_number"
                        :value="quote.request_number || '{{ $suggestedRequestNumber ?? '' }}'" readonly />
                    <div class="absolute inset-y-0 right-2.5 flex items-center pointer-events-none">
                        <span class="material-symbols-outlined text-gray-400 text-[12px]">lock</span>
                    </div>
                </div>
                <input type="hidden" name="request_number"
                    :value="quote.request_number || '{{ $suggestedRequestNumber ?? '' }}'">
                <input type="hidden" name="project_id" :value="quote.project_id || '{{ $suggestedProjectId ?? '' }}'">
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-[11px] font-medium text-gray-700 dark:text-gray-300">Nombre del Servicio</label>
                <input x-model="quote.project_name" :readonly="!!projectFromPHP?.name"
                    :class="!!projectFromPHP?.name ? 'bg-gray-50 opacity-70 cursor-not-allowed dark:bg-gray-900' : 'bg-transparent'"
                    class="flex h-8 w-full rounded-md border border-gray-200 px-2.5 py-1 text-xs shadow-sm transition-colors placeholder:text-gray-400 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-gray-900 dark:border-gray-800 dark:focus-visible:ring-gray-300"
                    type="text" placeholder="Ej: Mantenimiento preventivo de equipos..." />
                <input type="hidden" name="project_name" x-model="quote.project_name">
            </div>
        </div>

        {{-- Row 2: Compact field grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-3">
            {{-- Tipo Cotización --}}
            <div class="flex flex-col gap-1">
                <label class="text-[11px] font-medium text-gray-700 dark:text-gray-300">Tipo</label>
                <select x-model="quoteType"
                    class="flex h-8 w-full items-center justify-between rounded-md border border-gray-200 bg-transparent px-2.5 py-1 text-xs shadow-sm ring-offset-background placeholder:text-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-900 dark:border-gray-800 dark:focus:ring-gray-300">
                    <option value="Correctivo">Correctivo</option>
                    <option value="Preventivo">Preventivo</option>
                </select>
            </div>

            {{-- Categoría --}}
            <div class="flex flex-col gap-1">
                <label class="text-[11px] font-medium text-gray-700 dark:text-gray-300">Categoría</label>
                <select x-model.number="quote.quote_category_id"
                    class="flex h-8 w-full items-center justify-between rounded-md border border-gray-200 bg-transparent px-2.5 py-1 text-xs shadow-sm ring-offset-background placeholder:text-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-900 dark:border-gray-800 dark:focus:ring-gray-300">
                    <option value="">Seleccionar...</option>
                    <template x-for="category in quoteCategories" :key="category.id">
                        <option :value="category.id" x-text="category.name"
                            :selected="category.id == quote.quote_category_id"></option>
                    </template>
                </select>
                <input type="hidden" name="quote_category_id" x-model="quote.quote_category_id">
            </div>

            {{-- Cliente --}}
            <div class="flex flex-col gap-1">
                <label class="text-[11px] font-medium text-gray-700 dark:text-gray-300">Compañia</label>
                <div class="relative" @click.away="clientDropdownOpen = false">
                    <input type="text" x-model="clientSearch" @focus="clientDropdownOpen = true"
                        @click="clientDropdownOpen = true" @input="filterClients()"
                        :disabled="!!projectFromPHP?.sub_client_id" placeholder="Buscar..."
                        class="flex h-8 w-full rounded-md border border-gray-200 bg-transparent px-2.5 py-1 pr-7 text-xs shadow-sm transition-colors placeholder:text-gray-400 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-gray-900 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-800 dark:focus-visible:ring-gray-300" />
                    <div class="absolute inset-y-0 right-0 flex items-center pr-2"
                        :class="{ 'cursor-pointer': quote.client_id }">
                        <template x-if="quote.client_id && !projectFromPHP?.sub_client_id">
                            <button @click="clearClient()" type="button" class="p-0.5 hover:bg-gray-100 rounded-full">
                                <svg fill="none" class="w-3 h-3" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </template>
                        <template x-if="!quote.client_id">
                            <svg fill="none" class="w-3 h-3" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </template>
                    </div>
                    <div x-show="clientDropdownOpen && filteredClients.length > 0" x-transition
                        class="absolute bottom-full mb-1 z-[60] w-full bg-white border border-gray-200 rounded-md shadow-xl max-h-56 overflow-auto dark:bg-gray-950 dark:border-gray-800">
                        <template x-for="client in filteredClients" :key="client.id">
                            <div @click="selectClientFromDropdown(client)"
                                :class="{ 'bg-gray-100 dark:bg-gray-800': quote.client_id == client.id }"
                                class="px-2.5 py-1.5 text-xs cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-900">
                                <div class="font-medium text-gray-900 dark:text-gray-100" x-text="client.business_name">
                                </div>
                                <div class="text-[10px] text-gray-500" x-text="client.document_number || 'Sin RUC'">
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                <input type="hidden" name="client_id" x-model="quote.client_id">
            </div>

            {{-- SubCliente --}}
            <div class="flex flex-col gap-1">
                <label class="text-[11px] font-medium text-gray-700 dark:text-gray-300">Cliente</label>
                <div class="relative" @click.away="subClientDropdownOpen = false">
                    <input type="text" x-model="subClientSearch" @focus="subClientDropdownOpen = true"
                        @click="subClientDropdownOpen = true" @input="filterSubClients()"
                        :disabled="!!projectFromPHP?.sub_client_id || !quote.client_id || loadingSubClients"
                        :placeholder="loadingSubClients ? 'Cargando...' : (!quote.client_id ? 'Primero cliente...' : 'Buscar...')"
                        class="flex h-8 w-full rounded-md border border-gray-200 bg-transparent px-2.5 py-1 pr-7 text-xs shadow-sm transition-colors placeholder:text-gray-400 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-gray-900 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-800 dark:focus-visible:ring-gray-300" />
                    <div class="absolute inset-y-0 right-0 flex items-center pr-2"
                        :class="{ 'cursor-pointer': quote.sub_client_id }">
                        <template x-if="quote.sub_client_id && !projectFromPHP?.sub_client_id">
                            <button @click="clearSubClient()" type="button"
                                class="p-0.5 hover:bg-gray-100 rounded-full">
                                <svg fill="none" class="w-3 h-3" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </template>
                        <template x-if="!quote.sub_client_id && !loadingSubClients">
                            <svg fill="none" class="w-3 h-3" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </template>
                    </div>
                    <div x-show="subClientDropdownOpen && filteredSubClients.length > 0" x-transition
                        class="absolute bottom-full mb-1 z-[60] w-full bg-white border border-gray-200 rounded-md shadow-xl max-h-56 overflow-auto dark:bg-gray-950 dark:border-gray-800">
                        <template x-for="subClient in filteredSubClients" :key="subClient.id">
                            <div @click="selectSubClientFromDropdown(subClient)"
                                :class="{ 'bg-gray-100 dark:bg-gray-800': quote.sub_client_id == subClient.id }"
                                class="px-2.5 py-1.5 text-xs cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-900">
                                <div class="font-medium text-gray-900 dark:text-gray-100" x-text="subClient.name"></div>
                                <div class="text-[10px] text-gray-500" x-text="subClient.ceco || 'Sin CECO'">
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Gerente --}}
            <div class="flex flex-col gap-1">
                <label class="text-[11px] font-medium text-gray-700 dark:text-gray-300">Gerente Energy SCI</label>
                <input x-model="quote.energy_sci_manager"
                    class="flex h-8 w-full rounded-md border border-gray-200 bg-transparent px-2.5 py-1 text-xs shadow-sm transition-colors placeholder:text-gray-400 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-gray-900 dark:border-gray-800 dark:focus-visible:ring-gray-300"
                    type="text" placeholder="Nombre..." />
            </div>
        </div>

        {{-- Row 3: Dates + Status + CECO --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="flex flex-col gap-1">
                <label class="text-[11px] font-medium text-gray-700 dark:text-gray-300">Fecha Cotización</label>
                <input x-model="quote.quote_date"
                    class="flex h-8 w-full rounded-md border border-gray-200 bg-transparent px-2.5 py-1 text-xs shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-gray-900 dark:border-gray-800 dark:focus-visible:ring-gray-300"
                    type="date" />
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-[11px] font-medium text-gray-700 dark:text-gray-300">Fecha Ejecución</label>
                <input x-model="quote.execution_date"
                    class="flex h-8 w-full rounded-md border border-gray-200 bg-transparent px-2.5 py-1 text-xs shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-gray-900 dark:border-gray-800 dark:focus-visible:ring-gray-300"
                    type="date" />
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-[11px] font-medium text-gray-700 dark:text-gray-300">Estado</label>
                <select x-model="quote.status"
                    class="flex h-8 w-full items-center justify-between rounded-md border border-gray-200 bg-transparent px-2.5 py-1 text-xs shadow-sm ring-offset-background placeholder:text-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-900 dark:border-gray-800 dark:focus:ring-gray-300 font-medium"
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
            <div class="flex flex-col gap-1">
                <label class="text-[11px] font-medium text-gray-700 dark:text-gray-300">CECO</label>
                <input x-model="quote.ceco"
                    class="flex h-8 w-full rounded-md border border-gray-200 bg-gray-50 px-2.5 py-1 text-xs shadow-sm opacity-70 cursor-not-allowed dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400"
                    type="text" readonly placeholder="Automático" />
            </div>
        </div>
    </div>
</div>