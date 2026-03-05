<x-filament-panels::page>
    @vite(['resources/css/app.css'])
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div x-data="{
        quoteWarehouseId: {{ $quoteWarehouse->id }},
        projectId: {{ $quoteWarehouse->quote->project_id ?? 'null' }},
        status: '{{ $quoteWarehouse->status }}',
        items: [
            @foreach ($details as $i => $item)
                {
                    project_requirement_id: {{ $item['project_requirement_id'] }},
                    solicitado: {{ $item['quantity'] }},
                    entregado: {{ $item['entregado'] ?? 0 }},
                    despachar: 0,
                    comment: '{{ addslashes($item['comment'] ?? '') }}',
                    location_origin_id: {{ $item['location_origin_id'] ?? 'null' }},
                    location_destination_id: {{ $item['location_destination_id'] ?? 'null' }},
                    additional_cost: {{ $item['additional_cost'] ?? 0 }},
                    cost_description: '{{ addslashes($item['cost_description'] ?? '') }}'
                }, 
            @endforeach
        ],
        observaciones: '{{ addslashes($quoteWarehouse->observations ?? '') }}',

        // Locations Data
        locations: [
            @foreach ($locations as $loc)
                { id: {{ $loc->id }}, name: '{{ addslashes($loc->name) }}' },
            @endforeach
        ],

        // New Location Modal
        newLocationModal: {
            open: false,
            name: '',
            description: '',
            loading: false,
            target: null, // 'origin' o 'destination'
            targetIndex: null,
        },

        openNewLocationModal(target, index) {
            this.newLocationModal = {
                open: true,
                name: '',
                description: '',
                loading: false,
                target: target,
                targetIndex: index,
            };
            this.$nextTick(() => this.$refs.newLocationName?.focus());
        },

        closeNewLocationModal() {
            this.newLocationModal.open = false;
        },

        async createLocation() {
            if (!this.newLocationModal.name.trim()) {
                Swal.fire({ icon: 'warning', title: 'Nombre requerido', text: 'Ingrese un nombre para el lugar.', confirmButtonColor: '#137fec' });
                return;
            }
            this.newLocationModal.loading = true;
            try {
                const response = await fetch('{{ route("quoteswarehouse.locations.store") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
                    body: JSON.stringify({ name: this.newLocationModal.name.trim(), description: this.newLocationModal.description.trim() })
                });
                const data = await response.json();
                if (data.success) {
                    this.locations.push({ id: data.data.id, name: data.data.name });
                    const idx = this.newLocationModal.targetIndex;
                    if (this.newLocationModal.target === 'origin') {
                        this.items[idx].location_origin_id = data.data.id;
                    } else {
                        this.items[idx].location_destination_id = data.data.id;
                    }
                    this.closeNewLocationModal();
                    Swal.fire({ icon: 'success', title: 'Lugar creado', text: data.message, timer: 1500, showConfirmButton: false });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message, confirmButtonColor: '#137fec' });
                }
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo crear el lugar.', confirmButtonColor: '#137fec' });
            } finally {
                this.newLocationModal.loading = false;
            }
        },

        // Assigned Tools Data
        assignedTools: [],
        loadingTools: true,

        // Tool Search Modal Data
        toolSearchModal: {
            open: false,
            query: '',
            loading: false,
            initialLoad: false,
            results: [],
            selectedTools: [],
            categories: [],
            filters: {
                status: '',
                category_id: ''
            }
        },

        get progresoTotal() {
            let totalSolicitado = this.items.reduce((acc, item) => acc + item.solicitado, 0);
            let totalListo = this.items.reduce((acc, item) => acc + Math.min(item.entregado + item.despachar, item.solicitado), 0);
            return totalSolicitado === 0 ? 0 : Math.round((totalListo / totalSolicitado) * 100);
        },

        // Tool Search Modal Methods
        openToolSearchModal() {
            this.toolSearchModal.open = true;
            this.toolSearchModal.query = '';
            this.toolSearchModal.results = [];
            this.toolSearchModal.selectedTools = [];
            this.toolSearchModal.filters = { status: '', category_id: '' };
            this.$nextTick(() => {
                this.$refs.toolSearchInput?.focus();
                this.loadInitialTools();
            });
        },

        closeToolSearchModal() {
            this.toolSearchModal.open = false;
        },

        async loadToolCategories() {
            try {
                const response = await fetch('/tools/categories');
                const data = await response.json();
                if (data.success) {
                    this.toolSearchModal.categories = data.data;
                }
            } catch (error) {
                console.error('Error loading categories:', error);
            }
        },

        async loadInitialTools() {
            this.toolSearchModal.loading = true;
            this.toolSearchModal.initialLoad = true;
            try {
                const params = new URLSearchParams({
                    limit: 20,
                    status: this.toolSearchModal.filters.status || '',
                });
                const response = await fetch(`/tools/quick-search?${params}`);
                const data = await response.json();
                if (data.success) {
                    this.toolSearchModal.results = data.data;
                }
            } catch (error) {
                console.error('Error loading tools:', error);
            } finally {
                this.toolSearchModal.loading = false;
            }
        },

        async searchTools() {
            this.toolSearchModal.loading = true;
            try {
                const params = new URLSearchParams({
                    query: this.toolSearchModal.query,
                    status: this.toolSearchModal.filters.status || '',
                    limit: 30
                });
                const response = await fetch(`/tools/quick-search?${params}`);
                const data = await response.json();
                if (data.success) {
                    this.toolSearchModal.results = data.data;
                }
            } catch (error) {
                console.error('Error searching tools:', error);
                this.toolSearchModal.results = [];
            } finally {
                this.toolSearchModal.loading = false;
            }
        },

        clearToolFilters() {
            this.toolSearchModal.filters = { status: '', category_id: '' };
            this.searchTools();
        },

        toggleToolSelection(tool) {
            const index = this.toolSearchModal.selectedTools.findIndex(t => t.id === tool.id);
            if (index === -1) {
                if (tool.available) {
                    this.toolSearchModal.selectedTools.push(tool);
                } else {
                    Swal.fire({ icon: 'warning', title: 'No disponible', text: 'Esta herramienta no está disponible para asignar.', confirmButtonColor: '#137fec' });
                }
            } else {
                this.toolSearchModal.selectedTools.splice(index, 1);
            }
        },

        isToolSelected(id) {
            return this.toolSearchModal.selectedTools.some(t => t.id === id);
        },

        async addSelectedTools() {
            if (!this.projectId) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo identificar el proyecto.' });
                return;
            }
            const availableTools = this.toolSearchModal.selectedTools.filter(t => t.available && t.status === 'Disponible');
            const unavailableTools = this.toolSearchModal.selectedTools.filter(t => !t.available || t.status !== 'Disponible');
            if (unavailableTools.length > 0 && availableTools.length === 0) {
                Swal.fire({ icon: 'warning', title: 'No disponible', text: 'Ninguna de las herramientas seleccionadas está disponible para asignar.', confirmButtonColor: '#137fec' });
                return;
            }
            let successCount = 0;
            let errors = [];
            for (const tool of availableTools) {
                try {
                    const response = await fetch('/project-tools', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
                        body: JSON.stringify({ project_id: this.projectId, tool_id: tool.id })
                    });
                    const data = await response.json();
                    if (data.success) { successCount++; } else { errors.push(tool.name + ': ' + data.message); }
                } catch (error) { errors.push(tool.name + ': Error de conexión'); }
            }
            this.closeToolSearchModal();
            await this.loadProjectTools();
            let title = '', text = '', icon = 'success';
            if (successCount > 0) {
                title = successCount + ' herramienta(s) asignada(s)';
                if (unavailableTools.length > 0) { text = unavailableTools.length + ' herramienta(s) no estaban disponibles y fueron omitidas.'; icon = 'warning'; }
                if (errors.length > 0) { text += (text ? ' ' : '') + 'Errores: ' + errors.join(', '); icon = 'warning'; }
            } else if (errors.length > 0) { title = 'Error'; text = errors.join(', '); icon = 'error'; }
            if (title) { Swal.fire({ icon: icon, title: title, text: text || undefined, confirmButtonColor: '#137fec' }); }
        },

        async loadProjectTools() {
            if (!this.projectId) return;
            this.loadingTools = true;
            try {
                const response = await fetch(`/project-tools/project/${this.projectId}`);
                const data = await response.json();
                if (data.success) { this.assignedTools = data.data; }
            } catch (error) { console.error('Error loading project tools:', error); }
            finally { this.loadingTools = false; }
        },

        async returnTool(assignmentId) {
            const result = await Swal.fire({ title: '¿Devolver herramienta?', text: 'Esta acción marcará la herramienta como devuelta.', icon: 'question', showCancelButton: true, confirmButtonColor: '#137fec', cancelButtonColor: '#6b7280', confirmButtonText: 'Sí, devolver', cancelButtonText: 'Cancelar' });
            if (!result.isConfirmed) return;
            try {
                const response = await fetch(`/project-tools/${assignmentId}/return`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' } });
                const data = await response.json();
                if (data.success) { Swal.fire({ icon: 'success', title: 'Devuelta', text: data.message, confirmButtonColor: '#137fec' }); await this.loadProjectTools(); }
                else { Swal.fire({ icon: 'error', title: 'Error', text: data.message }); }
            } catch (error) { Swal.fire({ icon: 'error', title: 'Error', text: 'Error de conexión' }); }
        },

        async enviarFormulario() {
            const details = this.items
                .filter(i => i.despachar > 0)
                .map(i => ({
                    project_requirement_id: i.project_requirement_id,
                    a_despachar: i.despachar,
                    quantity: i.solicitado,
                    comment: i.comment,
                    location_origin_id: i.location_origin_id,
                    location_destination_id: i.location_destination_id,
                    additional_cost: i.additional_cost || 0,
                    cost_description: i.cost_description || ''
                }));
            const payload = {
                quote_warehouse_id: this.quoteWarehouseId,
                observations: this.observaciones,
                progreso_total: this.progresoTotal,
                details: details
            };
            try {
                const response = await fetch('{{ route('quoteswarehouse.store') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const data = await response.json();
                if (data.success) {
                    let message = data.message;
                    if (data.estadoMensaje) { message += `\n${data.estadoMensaje}`; }
                    Swal.fire({ icon: 'success', title: '¡Éxito!', text: message, confirmButtonColor: '#137fec' }).then(() => { location.reload(); });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message, confirmButtonColor: '#d33' });
                }
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo conectar con el servidor. Inténtalo nuevamente.', confirmButtonColor: '#d33' });
            }
        }
    }" x-init="loadToolCategories(); loadProjectTools()">

        {{-- Header Info --}}
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
            <div class="flex flex-col gap-2">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md"
                        :class="{
                            'text-yellow-800 bg-yellow-100 dark:bg-yellow-900/30 dark:text-yellow-400': status === 'Pendiente' || status === 'pending',
                            'text-green-800 bg-green-100 dark:bg-green-900/30 dark:text-green-400': status === 'Atendido',
                            'text-blue-800 bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400': status === 'Parcial'
                        }">
                        {{ $quoteWarehouse->status === 'pending' ? 'Pendiente' : $quoteWarehouse->status }}
                    </span>
                    
                </div>
                <div class="flex flex-wrap items-center text-sm gap-x-6 gap-y-2 text-slate-500 dark:text-slate-400">
                    <div class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[18px]">business</span>
                        <span>Cliente: <strong class="text-slate-700 dark:text-slate-300">{{ $client }}</strong></span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                        <span>Fecha Cotización: {{ $quote->quote_date ? $quote->quote_date->format('d M Y') : '-' }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[18px]">event</span>
                        <span>Fecha Ejecución: {{ $quote->execution_date ? $quote->execution_date->format('d M Y') : '-' }}</span>
                    </div>
                </div>
            </div>
            {{-- Actions --}}
            <div class="flex gap-3">
                <a href="{{ route('quoteswarehouse.pdf', $quoteWarehouse->id) }}" target="_blank"
                    class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold border rounded-lg shadow-sm bg-white dark:bg-gray-800 border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700">
                    <span class="material-symbols-outlined mr-2 text-[20px]">print</span>
                    Imprimir
                </a>
                <button type="button"
                    class="inline-flex items-center justify-center px-4 py-2 text-sm font-bold transition-colors rounded-lg bg-primary-600/10 text-primary-600 hover:bg-primary-600/20 dark:text-primary-400"
                    @click="items.forEach(i => { const faltante = i.solicitado - i.entregado; i.despachar = faltante > 0 ? faltante : 0; })">
                    <span class="material-symbols-outlined mr-2 text-[20px]">check_circle</span>
                    Atender Todo
                </button>
            </div>
        </div>

        {{-- Suministros Table --}}
        <div class="mt-6">

            <div class="flex flex-col overflow-hidden border shadow-sm rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-gray-900">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left border-collapse">
                        <thead class="text-xs font-medium uppercase border-b bg-slate-50 dark:bg-slate-800/50 border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400">
                            <tr>
                                <th class="w-28 px-4 py-4 text-center" scope="col">TIPO</th>
                                <th class="px-4 py-4 min-w-[300px]" scope="col">DESCRIPCIÓN ITEM</th>
                                <th class="w-20 px-4 py-4 text-center" scope="col">Unidad</th>
                                <th class="w-24 px-4 py-4 text-center" scope="col">Solicitado</th>
                                <th class="w-24 px-4 py-4 text-center" scope="col">Entregado</th>
                                <th class="w-40 px-4 py-4" scope="col">A Despachar</th>
                                <th class="min-w-[220px] px-4 py-4" scope="col">Origen</th>
                                <th class="min-w-[220px] px-4 py-4" scope="col">Destino</th>
                                <th class="min-w-[320px] px-4 py-4" scope="col">Comentarios</th>
                                <th class="w-32 px-4 py-4 text-right" scope="col">Costo Adic.</th>
                                <th class="min-w-[200px] px-4 py-4" scope="col">Desc. Costo</th>
                                <th class="w-20 px-4 py-4 text-right" scope="col">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach ($details as $i => $item)
                                @php
                                    $completado = ($item['entregado'] ?? 0) >= ($item['quantity'] ?? 0);
                                    $porcentaje = ($item['quantity'] ?? 0) > 0 ? round((($item['entregado'] ?? 0) / ($item['quantity'] ?? 0)) * 100) : 0;
                                @endphp
                                <tr class="transition-colors group hover:bg-slate-50 dark:hover:bg-slate-800/50 {{ $completado ? 'bg-slate-50/50 dark:bg-slate-800/30' : '' }}">
                                    <td class="px-4 py-4 text-center align-top">
                                        @php
                                            $typeName = $item['type_name'] ?? 'N/A';
                                            $typeColor = match($typeName) {
                                                'Suministro' => 'bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-900/20 dark:text-blue-400 dark:ring-blue-500/30',
                                                'Herramienta' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/20 dark:text-amber-400 dark:ring-amber-500/30',
                                                default => 'bg-slate-50 text-slate-700 ring-slate-600/20 dark:bg-slate-800 dark:text-slate-400 dark:ring-slate-500/30',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset {{ $typeColor }}">
                                            {{ $typeName }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 break-words whitespace-normal align-top">
                                        <span :class="items[{{ $i }}].entregado + items[{{ $i }}].despachar >= items[{{ $i }}].solicitado ? 'line-through text-slate-400 dark:text-slate-500' : 'text-slate-900 dark:text-white'"
                                            class="text-xs font-medium leading-relaxed">
                                            {{ $item['product_name'] ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-center align-top">
                                        <span class="text-xs text-slate-400">{{ $item['unit_name'] ?? '-' }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-center align-top">
                                        <span class="inline-flex items-center rounded-md bg-slate-100 dark:bg-slate-800 px-2.5 py-1 text-xs font-medium text-slate-600 dark:text-slate-300">
                                            {{ $item['quantity'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-center align-top">
                                        <div class="flex flex-col items-center gap-1">
                                            <span class="font-semibold {{ $completado ? 'text-green-600' : 'text-slate-900 dark:text-slate-200' }}">
                                                {{ $item['entregado'] ?? 0 }}
                                            </span>
                                            <div class="flex items-center justify-center w-20 h-2 mx-auto mt-1 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-700">
                                                <div class="h-full {{ $completado ? 'bg-green-500' : 'bg-blue-500' }} rounded-full" style="width: {{ $porcentaje }}%"></div>
                                            </div>
                                            @if ($completado)
                                                <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-900/20 dark:text-green-400 dark:ring-green-500/30 mt-1">
                                                    <span class="material-symbols-outlined text-[14px] mr-1">check</span>
                                                    Completado
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 align-top">
                                        @if ($completado)
                                            <span class="inline-flex items-center px-3 py-1 text-xs font-medium text-green-700 rounded-full bg-green-50 ring-1 ring-inset ring-green-600/20 dark:bg-green-900/20 dark:text-green-400 dark:ring-green-500/30">
                                                <span class="material-symbols-outlined text-[14px] mr-1">check</span>
                                                Completado
                                            </span>
                                        @else
                                            <div class="relative flex items-center">
                                                <input type="number" x-model.number="items[{{ $i }}].despachar"
                                                    :max="items[{{ $i }}].solicitado - items[{{ $i }}].entregado"
                                                    min="0"
                                                    @input="if(items[{{ $i }}].despachar > (items[{{ $i }}].solicitado - items[{{ $i }}].entregado)) items[{{ $i }}].despachar = items[{{ $i }}].solicitado - items[{{ $i }}].entregado"
                                                    class="block w-full rounded-md border-0 py-1.5 pl-2 pr-8 text-slate-900 ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 text-xs dark:bg-slate-900 dark:ring-slate-600 dark:text-white font-bold" />
                                                <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                                    <span class="text-[10px] text-slate-400 uppercase">u.</span>
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 align-top">
                                        <div class="flex items-center gap-1">
                                            <select x-model="items[{{ $i }}].location_origin_id"
                                                class="block w-full rounded-md border-0 py-1.5 text-slate-900 ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 text-xs dark:bg-slate-900 dark:ring-slate-600 dark:text-white"
                                                :disabled="{{ $completado ? 'true' : 'false' }}">
                                                <option value="">Origen...</option>
                                                <template x-for="loc in locations" :key="loc.id">
                                                    <option :value="loc.id" x-text="loc.name" :selected="loc.id == items[{{ $i }}].location_origin_id"></option>
                                                </template>
                                            </select>
                                            @if (!$completado)
                                            <button type="button" @click="openNewLocationModal('origin', {{ $i }})"
                                                class="flex-shrink-0 inline-flex items-center justify-center w-7 h-7 rounded-md text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors"
                                                title="Agregar nuevo lugar">
                                                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 align-top">
                                        <div class="flex items-center gap-1">
                                            <select x-model="items[{{ $i }}].location_destination_id"
                                                class="block w-full rounded-md border-0 py-1.5 text-slate-900 ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 text-xs dark:bg-slate-900 dark:ring-slate-600 dark:text-white"
                                                :disabled="{{ $completado ? 'true' : 'false' }}">
                                                <option value="">Destino...</option>
                                                <template x-for="loc in locations" :key="loc.id">
                                                    <option :value="loc.id" x-text="loc.name" :selected="loc.id == items[{{ $i }}].location_destination_id"></option>
                                                </template>
                                            </select>
                                            @if (!$completado)
                                            <button type="button" @click="openNewLocationModal('destination', {{ $i }})"
                                                class="flex-shrink-0 inline-flex items-center justify-center w-7 h-7 rounded-md text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors"
                                                title="Agregar nuevo lugar">
                                                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 align-top">
                                        <textarea x-model="items[{{ $i }}].comment" rows="2"
                                            class="block w-full rounded-md border-0 py-1.5 px-2 text-slate-900 ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 text-xs dark:bg-slate-900 dark:ring-slate-600 dark:text-white resize-y"
                                            placeholder="Nota opcional..."
                                            :disabled="{{ $completado ? 'true' : 'false' }}"></textarea>
                                    </td>
                                    <td class="px-4 py-4 text-right align-top">
                                        <input type="number" x-model.number="items[{{ $i }}].additional_cost"
                                            min="0" step="0.01"
                                            class="block w-full rounded-md border-0 py-1.5 px-2 text-slate-900 ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 text-xs dark:bg-slate-900 dark:ring-slate-600 dark:text-white text-right"
                                            placeholder="0.00"
                                            :disabled="{{ $completado ? 'true' : 'false' }}" />
                                    </td>
                                    <td class="px-4 py-4 align-top">
                                        <input type="text" x-model="items[{{ $i }}].cost_description"
                                            class="block w-full rounded-md border-0 py-1.5 px-2 text-slate-900 ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 text-xs dark:bg-slate-900 dark:ring-slate-600 dark:text-white"
                                            placeholder="Ej: Flete, transporte..."
                                            :disabled="{{ $completado ? 'true' : 'false' }}" />
                                    </td>
                                    <td class="px-4 py-4 text-right align-top">
                                        @if ($completado)
                                            <div class="inline-flex items-center justify-center p-2 text-green-600 dark:text-green-500">
                                                <span class="material-symbols-outlined text-[28px] fill-1">check_circle</span>
                                            </div>
                                        @else
                                            <button
                                                @click="items[{{ $i }}].despachar = items[{{ $i }}].solicitado - items[{{ $i }}].entregado"
                                                class="inline-flex items-center justify-center p-1.5 transition-all rounded-full group/btn"
                                                :class="items[{{ $i }}].despachar + items[{{ $i }}].entregado >= items[{{ $i }}].solicitado ? 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-500' : 'text-slate-300 dark:text-slate-600 hover:text-green-600 dark:hover:text-green-500'"
                                                type="button" title="Marcar como listo">
                                                <span class="material-symbols-outlined text-[22px] group-hover/btn:fill-1"
                                                    :class="items[{{ $i }}].despachar + items[{{ $i }}].entregado >= items[{{ $i }}].solicitado ? 'text-green-600 dark:text-green-500' : ''">check_circle</span>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- Table Footer --}}
                <div class="flex items-center justify-between px-6 py-3 text-sm border-t bg-slate-50 dark:bg-slate-800/50 border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400">
                    <span>Mostrando {{ count($details) }} ítems</span>
                    <div class="flex gap-4">
                        <div class="flex gap-2">
                            <span class="font-medium text-slate-700 dark:text-slate-300">Costos Adic.:</span>
                            <span class="font-bold text-amber-600" x-text="'S/' + items.reduce((sum, i) => sum + (parseFloat(i.additional_cost) || 0), 0).toFixed(2)"></span>
                        </div>
                        <div class="flex gap-2">
                            <span class="font-medium text-slate-700 dark:text-slate-300">Progreso Total:</span>
                            <span class="font-bold text-primary-600" x-text="progresoTotal + '%'"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Observaciones --}}
        <div class="mt-6">
            <label for="warehouse-observations" class="block mb-2 text-sm font-medium text-slate-700 dark:text-slate-300">
                Observaciones
            </label>
            <textarea id="warehouse-observations" rows="3" x-model="observaciones"
                class="block w-full p-3 text-sm bg-white border rounded-lg resize-y border-slate-300 dark:border-slate-600 dark:bg-gray-900 text-slate-900 dark:text-white placeholder:text-slate-400 focus:ring-2 focus:ring-primary-600 focus:border-primary-600"
                placeholder="Ingrese aquí observaciones generales para el despacho..."></textarea>
        </div>

        {{-- Bottom Actions --}}
        <div class="flex items-center justify-between pt-4 mt-4">
            <a href="{{ \App\Filament\Resources\QuoteWarehouses\QuoteWarehouseResource::getUrl() }}"
                class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold border rounded-lg shadow-sm bg-white dark:bg-gray-800 border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700">
                <span class="material-symbols-outlined mr-2 text-[20px]">arrow_back</span>
                Volver
            </a>
            <button type="button"
                class="inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-white transition-all rounded-lg shadow-lg bg-primary-600 hover:bg-primary-700 gap-2"
                @click="enviarFormulario()">
                <span class="material-symbols-outlined text-[24px]">local_shipping</span>
                <span>Confirmar Despacho</span>
            </button>
        </div>

        {{-- Modal: Nuevo Lugar --}}
        <div x-show="newLocationModal.open" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @keydown.escape.window="closeNewLocationModal()">
            <div class="w-full max-w-md bg-white rounded-xl shadow-xl dark:bg-gray-900 border border-slate-200 dark:border-slate-700"
                @click.outside="closeNewLocationModal()">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary-600 text-[22px]">add_location_alt</span>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Nuevo Lugar</h3>
                    </div>
                    <button type="button" @click="closeNewLocationModal()"
                        class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <span class="material-symbols-outlined text-slate-400 text-[20px]">close</span>
                    </button>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-slate-700 dark:text-slate-300">Nombre <span class="text-red-500">*</span></label>
                        <input type="text" x-model="newLocationModal.name" x-ref="newLocationName"
                            @keydown.enter.prevent="createLocation()"
                            class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-slate-900 ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 dark:bg-slate-800 dark:ring-slate-600 dark:text-white"
                            placeholder="Ej: Almacén Central, Planta 2...">
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-slate-700 dark:text-slate-300">Descripción <span class="text-slate-400 font-normal">(opcional)</span></label>
                        <input type="text" x-model="newLocationModal.description"
                            class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-slate-900 ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 dark:bg-slate-800 dark:ring-slate-600 dark:text-white"
                            placeholder="Descripción breve del lugar...">
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-200 dark:border-slate-700">
                    <button type="button" @click="closeNewLocationModal()"
                        class="px-4 py-2 text-sm font-medium rounded-lg border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        Cancelar
                    </button>
                    <button type="button" @click="createLocation()"
                        :disabled="newLocationModal.loading || !newLocationModal.name.trim()"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-white rounded-lg bg-primary-600 hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <span x-show="newLocationModal.loading" class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
                        <span x-show="!newLocationModal.loading" class="material-symbols-outlined text-[16px]">add</span>
                        <span x-text="newLocationModal.loading ? 'Creando...' : 'Crear Lugar'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
</x-filament-panels::page>
