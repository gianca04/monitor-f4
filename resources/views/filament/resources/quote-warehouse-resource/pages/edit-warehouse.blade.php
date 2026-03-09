<x-filament-panels::page>
    @vite(['resources/css/app.css', 'resources/css/quote-form.css'])
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div x-data="{
        quoteWarehouseId: {{ $quoteWarehouse->id }},
        projectId: {{ $quoteWarehouse->quote->project_id ?? 'null' }},
        status: '{{ $quoteWarehouse->estatus }}',
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

        locations: [
            @foreach ($locations as $loc)
                { id: {{ $loc->id }}, name: '{{ addslashes($loc->name) }}' },
            @endforeach
        ],

        newLocationModal: {
            open: false,
            target: null, targetIndex: null, name: '', description: '', loading: false
        },

        openNewLocationModal(target, index) {
            this.newLocationModal = { open: true, target, targetIndex: index, name: '', description: '', loading: false };
            this.$nextTick(() => this.$refs.newLocationName?.focus());
        },
        closeNewLocationModal() { this.newLocationModal.open = false; },

        historyModal: {
            open: false, productName: '', loading: false, transactions: []
        },

        async openHistoryModal(requirementId, productName) {
            this.historyModal = { open: true, productName, loading: true, transactions: [] };
            try {
                const response = await fetch(`{{ url('quoteswarehouse/transactions') }}/${requirementId}`);
                const data = await response.json();
                if (data.success) {
                    this.historyModal.transactions = data.data;
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                    this.closeHistoryModal();
                }
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Error de red al cargar el historial' });
                this.closeHistoryModal();
            } finally {
                this.historyModal.loading = false;
            }
        },
        
        closeHistoryModal() { this.historyModal.open = false; },

        async createLocation() {
            if (!this.newLocationModal.name.trim()) {
                Swal.fire({ icon: 'warning', title: 'Requerido', text: 'Ingrese un nombre', confirmButtonColor: '#059669' });
                return;
            }
            this.newLocationModal.loading = true;
            try {
                const response = await fetch('{{ route('quoteswarehouse.locations.store') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
                    body: JSON.stringify({ name: this.newLocationModal.name.trim(), description: this.newLocationModal.description.trim() })
                });
                const data = await response.json();
                if (data.success) {
                    this.locations.push({ id: data.data.id, name: data.data.name });
                    if (this.newLocationModal.target === 'origin') this.items[this.newLocationModal.targetIndex].location_origin_id = data.data.id;
                    else this.items[this.newLocationModal.targetIndex].location_destination_id = data.data.id;
                    this.closeNewLocationModal();
                    Swal.fire({ icon: 'success', title: 'Creado', text: data.message, timer: 1500, showConfirmButton: false });
                } else Swal.fire({ icon: 'error', title: 'Error', text: data.message });
            } catch (e) { Swal.fire({ icon: 'error', title: 'Error', text: 'Error de red' }); }
            finally { this.newLocationModal.loading = false; }
        },

        get progresoTotal() {
            let totalSolicitado = this.items.reduce((acc, item) => acc + item.solicitado, 0);
            let totalListo = this.items.reduce((acc, item) => acc + Math.min(item.entregado + item.despachar, item.solicitado), 0);
            return totalSolicitado === 0 ? 0 : Math.round((totalListo / totalSolicitado) * 100);
        },

        async enviarFormulario() {
            const details = this.items.filter(i => i.despachar > 0).map(i => ({ project_requirement_id: i.project_requirement_id, a_despachar: i.despachar, quantity: i.solicitado, comment: i.comment, location_origin_id: i.location_origin_id, location_destination_id: i.location_destination_id, additional_cost: i.additional_cost || 0, cost_description: i.cost_description || '' }));
            const payload = { quote_warehouse_id: this.quoteWarehouseId, observations: this.observaciones, progreso_total: this.progresoTotal, details };
            try {
                const r = await fetch('{{ route('quoteswarehouse.store') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' }, body: JSON.stringify(payload) });
                const d = await r.json();
                if (d.success) {
                    Swal.fire({ icon: 'success', title: '¡Éxito!', text: d.message + (d.estadoMensaje ? `\n${d.estadoMensaje}` : ''), confirmButtonColor: '#059669' }).then(() => location.reload());
                } else Swal.fire({ icon: 'error', title: 'Error', text: d.message });
            } catch (e) { Swal.fire({ icon: 'error', title: 'Error', text: 'Error de red' }); }
        }
    }">

        {{-- Enterprise Header Card --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm mb-4">
            <div class="px-5 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-blue-600 dark:text-blue-500">inventory_2</span>
                            Despacho de Almacén
                        </h2>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest
                            {{ $quoteWarehouse->estatus === 'pending' || $quoteWarehouse->estatus === 'Pendiente' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400' :
    ($quoteWarehouse->estatus === 'Atendido' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' :
        'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400') }}">
                            {{ $quoteWarehouse->estatus === 'pending' ? 'Pendiente' : $quoteWarehouse->estatus }}
                        </span>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs text-gray-500 dark:text-gray-400 font-medium">
                        <div class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[14px]">business</span>
                            <span class="text-gray-700 dark:text-gray-300">{{ $client }}</span>
                        </div>
                        <div class="flex items-center gap-1.5 opacity-75">
                            <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                            Sol: {{ $quote->quote_date ? $quote->quote_date->format('d/m/Y') : '-' }}
                        </div>
                        <div class="flex items-center gap-1.5 opacity-75">
                            <span class="material-symbols-outlined text-[14px]">event</span>
                            Ejec: {{ $quote->execution_date ? $quote->execution_date->format('d/m/Y') : '-' }}
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center gap-2">
                    <a href="{{ route('quoteswarehouse.pdf', $quoteWarehouse->id) }}" target="_blank"
                        class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-bold bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition shadow-sm">
                        <span class="material-symbols-outlined text-[16px]">print</span>
                        <span class="hidden sm:inline">Imprimir PDF</span>
                    </a>
                </div>
            </div>
            
            {{-- Quick action bar --}}
            <div class="border-t border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50 px-5 py-2.5 flex items-center justify-between">
                <div class="text-xs text-gray-500 dark:text-gray-400 font-medium flex items-center gap-2">
                    <span class="material-symbols-outlined text-[14px]">info</span>
                    Ingresa las cantidades a despachar en esta sesión.
                </div>
                <button type="button" @click="items.forEach(i => { const f = i.solicitado - i.entregado; i.despachar = f > 0 ? f : 0; })"
                    class="text-xs font-bold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 hover:underline transition flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">done_all</span>
                    Llenar restante
                </button>
            </div>
        </div>

        {{-- Enterprise Table Card --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-sm rounded-xl overflow-hidden mb-24">
            <div class="overflow-x-auto max-h-[600px] overflow-y-auto custom-scrollbar">
                <table class="quote-table w-full relative">
                    <thead class="sticky top-0 z-10 bg-gray-100 dark:bg-gray-800 shadow-[0_1px_2px_rgba(0,0,0,0.05)]">
                        <tr>
                            <th class="quote-table__th text-center" style="width: 70px;">Tipo</th>
                            <th class="quote-table__th" style="min-width: 250px;">Item / Descripción</th>
                            <th class="quote-table__th text-center" style="width: 50px;">Und</th>
                            <th class="quote-table__th text-center" style="width: 60px;">Total</th>
                            <th class="quote-table__th text-center" style="width: 70px;">Previo</th>
                            {{-- Highlighted Action Header --}}
                            <th class="quote-table__th bg-blue-50 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 border-b-2 border-blue-200 dark:border-blue-800 font-bold" style="width: 100px;">Despachar</th>
                            
                            <th class="quote-table__th" style="min-width: 160px;">Ubic. Origen</th>
                            <th class="quote-table__th" style="min-width: 160px;">Ubic. Destino</th>
                            <th class="quote-table__th" style="min-width: 180px;">Nota / Serie</th>
                            <th class="quote-table__th text-center" style="width: 40px;"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        @foreach ($details as $i => $item)
                            @php
                                $completado = ($item['entregado'] ?? 0) >= ($item['quantity'] ?? 0);
                                $porcentaje = ($item['quantity'] ?? 0) > 0 ? round((($item['entregado'] ?? 0) / ($item['quantity'] ?? 0)) * 100) : 0;
                                $typeName = $item['type_name'] ?? 'N/A';
                                $typeColor = match ($typeName) {
                                    'Suministro' => 'text-blue-600 bg-blue-50 border-blue-100 dark:text-blue-400 dark:bg-blue-900/20 dark:border-blue-800/50',
                                    'Herramienta' => 'text-amber-600 bg-amber-50 border-amber-100 dark:text-amber-400 dark:bg-amber-900/20 dark:border-amber-800/50',
                                    default => 'text-gray-600 bg-gray-50 border-gray-100 dark:text-gray-400 dark:bg-gray-800/50 dark:border-gray-700',
                                };
                            @endphp
                            <tr class="transition-colors hover:bg-gray-50/80 dark:hover:bg-gray-800/50 {{ $completado ? 'bg-gray-50/50 dark:bg-gray-800/20' : '' }}">
                                {{-- Tipo --}}
                                <td class="px-3 py-2 text-center align-middle border-r border-gray-100 dark:border-gray-800/50">
                                    <span class="inline-flex px-1.5 py-0.5 text-[9px] font-black uppercase tracking-wider rounded border {{ $typeColor }} opacity-80">
                                        {{ substr($typeName, 0, 3) }}.
                                    </span>
                                </td>

                                {{-- Descripción --}}
                                <td class="px-3 py-2 align-middle border-r border-gray-100 dark:border-gray-800/50">
                                    <div class="text-[11px] font-semibold text-gray-800 dark:text-gray-200 leading-snug {{ $completado ? 'line-through opacity-50' : '' }}">
                                        {{ $item['product_name'] ?? '-' }}
                                    </div>
                                </td>

                                {{-- Unidad --}}
                                <td class="px-3 py-2 text-center align-middle border-r border-gray-100 dark:border-gray-800/50 text-[10px] text-gray-400 font-medium">
                                    {{ $item['unit_name'] ?? '-' }}
                                </td>

                                {{-- Total Solicitado --}}
                                <td class="px-3 py-2 text-center align-middle border-r border-gray-100 dark:border-gray-800/50">
                                    <span class="text-[11px] font-mono font-bold text-gray-600 dark:text-gray-400">
                                        {{ $item['quantity'] }}
                                    </span>
                                </td>

                                {{-- Previo (Entregado) --}}
                                <td class="px-3 py-2 text-center align-middle border-r border-gray-100 dark:border-gray-800/50 group">
                                    <div class="flex flex-col items-center gap-1 relative">
                                        <div class="flex items-center justify-center relative w-full">
                                            <span class="text-[11px] font-mono font-bold {{ $completado ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-700 dark:text-gray-300' }}">
                                                {{ $item['entregado'] ?? 0 }}
                                            </span>

                                        </div>
                                        <div class="w-10 h-1 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                            <div class="h-full {{ $completado ? 'bg-emerald-500' : 'bg-gray-400' }} rounded-full" style="width: {{ $porcentaje }}%"></div>
                                        </div>
                                    </div>
                                </td>

                                {{-- A Despachar (HIGHLIGHTED) --}}
                                <td class="px-2 py-1.5 align-middle bg-blue-50/30 dark:bg-blue-900/10 border-r border-blue-100 dark:border-blue-900/30">
                                    @if ($completado)
                                        <div class="flex items-center justify-center gap-1 text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 py-1.5 rounded border border-emerald-100 dark:border-emerald-800/50">
                                            <span class="material-symbols-outlined text-sm">check_circle</span> Listo
                                        </div>
                                    @else
                                        <input type="number" x-model.number="items[{{ $i }}].despachar"
                                            :max="items[{{ $i }}].solicitado - items[{{ $i }}].entregado" min="0"
                                            @input="if(items[{{ $i }}].despachar > (items[{{ $i }}].solicitado - items[{{ $i }}].entregado)) items[{{ $i }}].despachar = items[{{ $i }}].solicitado - items[{{ $i }}].entregado"
                                            class="w-full text-center text-xs font-mono font-bold border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500 rounded text-blue-700 dark:text-blue-300 bg-white dark:bg-gray-800 py-1.5 shadow-sm" />
                                    @endif
                                </td>

                                {{-- Origen --}}
                                <td class="px-2 py-1.5 align-middle border-r border-gray-100 dark:border-gray-800/50">
                                    <div class="flex items-center gap-1">
                                        <select x-model="items[{{ $i }}].location_origin_id"
                                            class="w-full text-[11px] font-medium border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/80 focus:border-blue-500 focus:ring-blue-500 rounded py-1 pl-2 pr-6 dark:text-gray-300 {{ $completado ? 'opacity-50' : '' }}"
                                            :disabled="{{ $completado ? 'true' : 'false' }}">
                                            <option value="">Seleccionar...</option>
                                            <template x-for="loc in locations" :key="loc.id">
                                                <option :value="loc.id" x-text="loc.name" :selected="loc.id == items[{{ $i }}].location_origin_id"></option>
                                            </template>
                                        </select>
                                        @if (!$completado)
                                            <button type="button" @click="openNewLocationModal('origin', {{ $i }})"
                                                class="text-gray-400 hover:text-blue-600 bg-white dark:bg-gray-800 hover:bg-blue-50 dark:hover:bg-blue-900/30 border border-gray-200 dark:border-gray-700 rounded p-0.5 transition" title="Nuevo lugar">
                                                <span class="material-symbols-outlined text-[16px]">add</span>
                                            </button>
                                        @endif
                                    </div>
                                </td>

                                {{-- Destino --}}
                                <td class="px-2 py-1.5 align-middle border-r border-gray-100 dark:border-gray-800/50">
                                    <div class="flex items-center gap-1">
                                        <select x-model="items[{{ $i }}].location_destination_id"
                                            class="w-full text-[11px] font-medium border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/80 focus:border-blue-500 focus:ring-blue-500 rounded py-1 pl-2 pr-6 dark:text-gray-300 {{ $completado ? 'opacity-50' : '' }}"
                                            :disabled="{{ $completado ? 'true' : 'false' }}">
                                            <option value="">Seleccionar...</option>
                                            <template x-for="loc in locations" :key="loc.id">
                                                <option :value="loc.id" x-text="loc.name" :selected="loc.id == items[{{ $i }}].location_destination_id"></option>
                                            </template>
                                        </select>
                                        @if (!$completado)
                                            <button type="button" @click="openNewLocationModal('destination', {{ $i }})"
                                                class="text-gray-400 hover:text-blue-600 bg-white dark:bg-gray-800 hover:bg-blue-50 dark:hover:bg-blue-900/30 border border-gray-200 dark:border-gray-700 rounded p-0.5 transition" title="Nuevo lugar">
                                                <span class="material-symbols-outlined text-[16px]">add</span>
                                            </button>
                                        @endif
                                    </div>
                                </td>

                                {{-- Comentarios / Serie --}}
                                <td class="px-2 py-1.5 align-middle">
                                    <input type="text" x-model="items[{{ $i }}].comment"
                                        class="w-full text-[11px] border-transparent hover:border-gray-300 focus:border-blue-500 focus:ring-blue-500 bg-transparent hover:bg-white dark:hover:bg-gray-800 rounded py-1 px-2 dark:text-gray-300 transition-colors {{ $completado ? 'opacity-50' : '' }}"
                                        placeholder="Núm. serie, nota..."
                                        :disabled="{{ $completado ? 'true' : 'false' }}" />
                                </td>

                                {{-- Historial --}}
                                <td class="px-1 py-1.5 text-center align-middle">
                                    <button type="button" @click="openHistoryModal({{ $item['project_requirement_id'] }}, '{{ addslashes($item['product_name']) }}')"
                                        class="inline-flex items-center justify-center text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded p-1 transition" title="Ver historial de despachos">
                                        <span class="material-symbols-outlined text-[16px]">history</span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{-- Enterprise Internal Footer Summary --}}
            <div class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 px-5 py-3 flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">
                    Total: <span class="text-gray-900 dark:text-white">{{ count($details) }} items</span> request
                </span>
                
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-medium text-gray-500">Progreso Operativo:</span>
                        <div class="flex items-center gap-2">
                            <div class="w-32 h-2 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden shadow-inner">
                                <div class="h-full bg-blue-500 rounded-full transition-all duration-500" :style="`width: ${progresoTotal}%`"></div>
                            </div>
                            <span class="text-xs font-bold font-mono text-blue-600 dark:text-blue-400" x-text="progresoTotal + '%'"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Enterprise Fixed Footer (Status Bar style) --}}
        <div class="quote-footer !w-full !max-w-none !px-0 bg-white/95 dark:bg-gray-900/95 backdrop-blur-md border-t border-gray-200 dark:border-gray-800 shadow-[0_-4px_20px_rgba(0,0,0,0.05)] fixed bottom-0 left-0 right-0 z-40 transition-all duration-300"
             :class="{ 'ml-[16rem]' : $store.sidebar.isOpen, 'ml-[5.4rem]' : !$store.sidebar.isOpen }"
             style="margin-bottom: 0; border-radius: 0;">
            <div class="max-w-[100rem] mx-auto px-4 sm:px-6 lg:px-8 py-3 flex flex-col sm:flex-row items-center justify-between gap-4">
                
                {{-- Left: Observaciones --}}
                <div class="flex-1 w-full max-w-2xl flex border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-white dark:bg-gray-800 focus-within:ring-2 focus-within:ring-blue-500/20 focus-within:border-blue-500 transition-all">
                    <div class="px-3 flex items-center bg-gray-50 dark:bg-gray-800/50 border-r border-gray-200 dark:border-gray-700">
                        <span class="material-symbols-outlined text-gray-400 text-sm">edit_note</span>
                    </div>
                    <input type="text" x-model="observaciones" 
                        class="flex-1 border-none focus:ring-0 text-xs py-2 bg-transparent dark:text-white placeholder-gray-400"
                        placeholder="Nota general del despacho (opcional)..." />
                </div>

                {{-- Right: Actions --}}
                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ \App\Filament\Resources\QuoteWarehouses\QuoteWarehouseResource::getUrl() }}"
                        class="px-4 py-2 text-xs font-bold text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition shadow-sm">
                        Cancelar
                    </a>
                    <button type="button" @click="enviarFormulario()"
                        class="flex items-center gap-2 px-6 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-[0_2px_10px_rgba(37,99,235,0.2)] rounded-lg transition active:scale-[0.98]">
                        <span class="material-symbols-outlined text-[18px]">send</span>
                        Registrar Despacho
                    </button>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════ --}}
        {{-- MODAL: Nuevo Lugar --}}
        {{-- ═══════════════════════════════════════════════ --}}
        <div x-show="newLocationModal.open" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
            x-transition.opacity duration.200ms
            @keydown.escape.window="closeNewLocationModal()">
            <div class="w-full max-w-md bg-white rounded-xl shadow-2xl dark:bg-gray-900 border border-gray-200 dark:border-gray-800"
                @click.outside="closeNewLocationModal()"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                <div class="px-5 py-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-800">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                            <span class="material-symbols-outlined text-[18px]">add_location_alt</span>
                        </div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Registrar Ubicación</h3>
                    </div>
                    <button type="button" @click="closeNewLocationModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>
                <div class="p-5 space-y-4 bg-gray-50/50 dark:bg-gray-800/20">
                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">Nombre <span class="text-red-500">*</span></label>
                        <input type="text" x-model="newLocationModal.name" x-ref="newLocationName"
                            @keydown.enter.prevent="createLocation()" class="w-full text-sm border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white py-2 px-3"
                            placeholder="Ej: Almacén Principal">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">Descripción <span class="font-normal text-gray-400">(Opcional)</span></label>
                        <input type="text" x-model="newLocationModal.description" class="w-full text-sm border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white py-2 px-3"
                            placeholder="Detalles breves...">
                    </div>
                </div>
                <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 flex justify-end gap-3 rounded-b-xl">
                    <button type="button" @click="closeNewLocationModal()" class="px-4 py-2 text-xs font-bold text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition">
                        Cancelar
                    </button>
                    <button type="button" @click="createLocation()" :disabled="newLocationModal.loading || !newLocationModal.name.trim()"
                        class="flex items-center gap-2 px-5 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="newLocationModal.loading" class="material-symbols-outlined text-[16px] animate-spin">sync</span>
                        <span x-show="!newLocationModal.loading" class="material-symbols-outlined text-[16px]">save</span>
                        <span x-text="newLocationModal.loading ? 'Guardando...' : 'Guardar'"></span>
                    </button>
        {{-- ═══════════════════════════════════════════════ --}}
        {{-- MODAL: Historial de Despachos (Timeline)        --}}
        {{-- ═══════════════════════════════════════════════ --}}
        <div x-show="historyModal.open" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
            x-transition.opacity duration.200ms
            @keydown.escape.window="closeHistoryModal()">
            <div class="w-full max-w-lg bg-white rounded-xl shadow-2xl dark:bg-gray-900 border border-gray-200 dark:border-gray-800 flex flex-col max-h-[90vh]"
                @click.outside="closeHistoryModal()"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                
                {{-- Modal Header --}}
                <div class="px-5 py-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-800 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                            <span class="material-symbols-outlined text-[18px]">history</span>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Historial de Despachos</h3>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium truncate max-w-[250px]" x-text="historyModal.productName"></p>
                        </div>
                    </div>
                    <button type="button" @click="closeHistoryModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                {{-- Modal Body (Timeline) --}}
                <div class="p-5 overflow-y-auto custom-scrollbar bg-gray-50/50 dark:bg-gray-800/20 flex-1 relative">
                    {{-- Loader --}}
                    <div x-show="historyModal.loading" class="absolute inset-0 bg-gray-50/80 dark:bg-gray-900/80 flex flex-col items-center justify-center z-10 backdrop-blur-sm">
                        <span class="material-symbols-outlined text-3xl animate-spin text-blue-500 mb-2">sync</span>
                        <span class="text-xs font-semibold text-gray-500">Cargando transacciones...</span>
                    </div>

                    {{-- Empty State --}}
                    <div x-show="!historyModal.loading && historyModal.transactions.length === 0" class="text-center py-8">
                        <span class="material-symbols-outlined text-4xl text-gray-300 dark:text-gray-600 mb-2">inbox</span>
                        <p class="text-sm text-gray-500 font-medium">No hay despachos registrados para este ítem.</p>
                    </div>

                    {{-- Timeline --}}
                    <div x-show="!historyModal.loading && historyModal.transactions.length > 0" class="relative pl-4 space-y-6 before:absolute before:inset-0 before:ml-[23px] before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-gray-200 dark:before:via-gray-700 before:to-transparent">
                        <template x-for="(tx, index) in historyModal.transactions" :key="index">
                            <div class="relative flex items-start gap-4">
                                {{-- Timeline Point --}}
                                <div class="absolute left-[-1.3rem] mt-1.5 w-3 h-3 rounded-full bg-blue-500 ring-4 ring-white dark:ring-gray-900 shrink-0 shadow-sm z-10 text-[0px]">.</div>
                                
                                {{-- Content Card --}}
                                <div class="flex-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-3 shadow-sm hover:border-blue-300 dark:hover:border-blue-700 transition-colors">
                                    <div class="flex items-center justify-between mb-1">
                                        <div class="flex items-center gap-1.5">
                                            <span class="material-symbols-outlined text-[14px] text-gray-400">person</span>
                                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300" x-text="tx.employee"></span>
                                        </div>
                                        <div class="text-[10px] font-medium text-gray-400 flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[12px]">schedule</span>
                                            <span x-text="tx.date"></span>
                                        </div>
                                    </div>
                                    
                                    <div class="my-2 p-2 bg-blue-50 dark:bg-blue-900/20 rounded border border-blue-100 dark:border-blue-900/50 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-[18px]">outbound</span>
                                        <span class="text-xs text-gray-700 dark:text-gray-300">
                                            Despachó <strong class="text-blue-700 dark:text-blue-400 font-mono text-sm" x-text="tx.quantity"></strong> unidades
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2 mt-2">
                                        <div class="text-[10px] text-gray-500 flex items-start gap-1">
                                            <span class="material-symbols-outlined text-[12px] text-gray-400 shrink-0 mt-0.5">location_on</span>
                                            <div>
                                                <span class="block text-gray-400 uppercase tracking-widest text-[8px] font-bold">Desde</span>
                                                <span class="font-medium text-gray-700 dark:text-gray-300" x-text="tx.origin"></span>
                                            </div>
                                        </div>
                                        <div class="text-[10px] text-gray-500 flex items-start gap-1 border-l border-gray-100 dark:border-gray-700 pl-2">
                                            <span class="material-symbols-outlined text-[12px] text-gray-400 shrink-0 mt-0.5">flag</span>
                                            <div>
                                                <span class="block text-gray-400 uppercase tracking-widest text-[8px] font-bold">Hacia</span>
                                                <span class="font-medium text-gray-700 dark:text-gray-300" x-text="tx.destination"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div x-show="tx.comment !== '-'" class="mt-2 pt-2 border-t border-gray-100 dark:border-gray-700 text-[10px] flex gap-1.5 items-start">
                                        <span class="material-symbols-outlined text-[12px] text-amber-500 shrink-0">edit_note</span>
                                        <span class="text-gray-600 dark:text-gray-400 italic" x-text="tx.comment"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 flex justify-end shrink-0 rounded-b-xl">
                    <button type="button" @click="closeHistoryModal()" class="px-4 py-2 text-xs font-bold text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>

    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: rgba(156, 163, 175, 0.5);
            border-radius: 10px;
        }
    </style>
</x-filament-panels::page>