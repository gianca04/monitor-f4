{{-- filepath: c:\xampp\htdocs\monitor-f4\resources\views\filament\widgets\admin-dashboard-widget.blade.php --}}
<x-filament-widgets::widget>
    <div class="admin-dashboard-widget" x-data="{ activeTab: @entangle('activeTab') }">
        @php
            $quickStats = $this->getQuickStats();
            $urgentItems = $this->getUrgentItems();
            $globalStats = $this->getGlobalStats();
            $advancedData = $this->getAdvancedChartData();
            $completedCount = $this->getCompletedProjectsCount();
        @endphp

        {{-- Header compacto --}}
        <div class="relative mb-6 overflow-hidden bg-white shadow-lg rounded-2xl ring-1 ring-gray-200">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-slate-400 via-indigo-500 to-slate-400">
            </div>
            <div class="relative z-10 p-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center gap-4">
                        <div
                            class="flex items-center justify-center w-14 h-14 shadow-lg rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 shadow-indigo-500/20">
                            <svg class="text-white w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">Panel Administrativo</h1>
                            <p class="flex items-center gap-2 mt-1 text-sm text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ now()->format('l, d M Y - H:i') }}
                            </p>
                        </div>
                    </div>
                    {{-- Filtros --}}
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="month" wire:model.live="monthFilter"
                                class="pl-10 pr-4 text-sm text-gray-700 transition border border-gray-200 h-10 rounded-xl bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                            </div>
                            <select wire:model.live="statusFilter"
                                class="pl-10 pr-10 text-sm text-gray-700 transition border border-gray-200 appearance-none h-10 rounded-xl bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                <option value="all">Todos los estados</option>
                                <option value="Pendiente">Pendiente</option>
                                <option value="Enviada">Enviada</option>
                                <option value="Aprobado">Aprobado</option>
                                <option value="En Ejecución">En Ejecución</option>
                                <option value="Completado">Completado</option>
                                <option value="Facturado">Facturado</option>
                            </select>
                        </div>
                        <button wire:click="$refresh"
                            class="flex items-center justify-center transition bg-gray-100 hover:bg-gray-200 h-10 w-10 rounded-xl"
                            wire:loading.class="animate-spin">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="flex items-center gap-2 mt-3 text-sm">
                    <span class="text-gray-400">Mostrando:</span>
                    <span class="px-2 py-0.5 font-medium text-indigo-700 bg-indigo-50 rounded-md text-xs">
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $monthFilter)->format('F Y') }}
                    </span>
                    @if ($statusFilter !== 'all')
                        <span class="px-2 py-0.5 font-medium text-gray-600 bg-gray-100 rounded-md text-xs">
                            {{ $statusFilter }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════ --}}
        {{-- NIVEL 1: LO URGENTE — Atención Inmediata --}}
        {{-- ═══════════════════════════════════════════════════════════════ --}}
        @if (count($urgentItems) > 0)
            <div class="mb-6 space-y-2">
                @foreach ($urgentItems as $item)
                    <a href="{{ $item['url'] }}"
                        class="flex items-center gap-4 px-5 py-3 transition-all bg-white border rounded-xl hover:shadow-md group
                                {{ $item['type'] === 'danger' ? 'border-red-200 hover:border-red-300' : 'border-amber-200 hover:border-amber-300' }}">
                        <div class="flex items-center justify-center flex-shrink-0 w-10 h-10 rounded-lg
                                    {{ $item['type'] === 'danger' ? 'bg-red-100' : 'bg-amber-100' }}">
                            @if ($item['icon'] === 'exclamation-triangle')
                                <svg class="w-5 h-5 {{ $item['type'] === 'danger' ? 'text-red-600' : 'text-amber-600' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            @elseif ($item['icon'] === 'clock')
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @elseif ($item['icon'] === 'truck')
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                                </svg>
                            @elseif ($item['icon'] === 'user-minus')
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6" />
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p
                                class="text-sm font-semibold {{ $item['type'] === 'danger' ? 'text-red-800' : 'text-amber-800' }}">
                                {{ $item['title'] }}</p>
                            <p class="text-xs {{ $item['type'] === 'danger' ? 'text-red-500' : 'text-amber-500' }}">
                                {{ $item['description'] }}</p>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span
                                class="inline-flex items-center justify-center w-8 h-8 text-sm font-bold rounded-full
                                        {{ $item['type'] === 'danger' ? 'bg-red-600 text-white' : 'bg-amber-500 text-white' }}">
                                {{ $item['count'] }}
                            </span>
                            <svg class="w-4 h-4 text-gray-300 transition group-hover:text-gray-500" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════════════ --}}
        {{-- NIVEL 2: SALUD DEL NEGOCIO — KPIs Reales --}}
        {{-- ═══════════════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-2 gap-4 mb-6 lg:grid-cols-4">
            {{-- KPI: Tasa de Conversión --}}
            <div class="p-5 bg-white border border-gray-200 rounded-xl">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-semibold tracking-wider text-gray-400 uppercase">Tasa de Conversión</p>
                    <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-emerald-50">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $advancedData['conversion_rate'] }}<span
                        class="text-lg text-gray-400">%</span></p>
                <div class="h-1.5 mt-3 overflow-hidden bg-gray-100 rounded-full">
                    <div class="h-full rounded-full bg-emerald-500 transition-all duration-500"
                        style="width: {{ $advancedData['conversion_rate'] }}%"></div>
                </div>
                <p class="mt-2 text-xs text-gray-400">Aprobadas vs emitidas (mes)</p>
            </div>

            {{-- KPI: Tiempo Promedio Aprobación --}}
            <div class="p-5 bg-white border border-gray-200 rounded-xl">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-semibold tracking-wider text-gray-400 uppercase">Tiempo Aprobación</p>
                    <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-blue-50">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $advancedData['avg_approval_days'] }} <span
                        class="text-lg font-normal text-gray-400">días</span></p>
                <p class="mt-2 text-xs text-gray-400">Promedio creación → aprobación</p>
            </div>

            {{-- KPI: Total Aprobado (S/.) --}}
            <div class="p-5 bg-white border border-gray-200 rounded-xl">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-semibold tracking-wider text-gray-400 uppercase">Total Aprobado</p>
                    <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-gray-50">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-900">S/.
                    {{ number_format($quickStats['approved_amount_this_month'], 2) }}</p>
                @if ($quickStats['amount_trend'] != 0)
                    <div class="flex items-center gap-1 mt-2">
                        @if ($quickStats['amount_trend'] > 0)
                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="text-xs font-medium text-emerald-600">+{{ $quickStats['amount_trend'] }}% vs mes
                                anterior</span>
                        @else
                            <svg class="w-3.5 h-3.5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="text-xs font-medium text-red-600">{{ $quickStats['amount_trend'] }}% vs mes
                                anterior</span>
                        @endif
                    </div>
                @else
                    <p class="mt-2 text-xs text-gray-400">Este mes</p>
                @endif
            </div>

            {{-- KPI: Flujos Completos --}}
            <div class="p-5 bg-white border border-gray-200 rounded-xl">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-semibold tracking-wider text-gray-400 uppercase">Flujos Completos</p>
                    <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-indigo-50">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $completedCount }}</p>
                <p class="mt-2 text-xs text-gray-400">Cotización → Acta → Reportes → Despacho</p>
            </div>
        </div>

        {{-- Navegación por Tabs --}}
        <div class="mb-6">
            <div class="flex flex-wrap gap-2 p-2 bg-gray-100 rounded-xl">
                @php
                    $tabs = [
                        'overview' => [
                            'icon' =>
                                'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z',
                            'label' => 'Resumen',
                        ],
                        'projects' => [
                            'icon' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z',
                            'label' => 'Proyectos',
                        ],
                        'charts' => [
                            'icon' =>
                                'M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z',
                            'label' => 'Gráficos',
                        ],
                        'activity' => [
                            'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                            'label' => 'Actividad',
                        ],
                    ];
                @endphp

                @foreach ($tabs as $key => $tab)
                    <button wire:click="setTab('{{ $key }}')" @class([
                        'flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium transition-all duration-200',
                        'bg-white text-indigo-600 shadow-sm' => $activeTab === $key,
                        'text-gray-600 hover:bg-white/50 hover:text-gray-900' =>
                            $activeTab !== $key,
                    ])>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}" />
                        </svg>
                        <span class="hidden sm:inline">{{ $tab['label'] }}</span>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Contenido de Tabs (mantener el resto igual pero con wire:loading) --}}
        <div class="p-6 bg-white shadow-sm rounded-2xl ring-1 ring-gray-200" wire:loading.class="opacity-50">
            {{-- Indicador de carga --}}
            <div wire:loading class="absolute inset-0 z-10 flex items-center justify-center bg-white/80">
                <div class="flex items-center gap-2 text-indigo-600">
                    <svg class="w-6 h-6 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span class="font-medium">Cargando...</span>
                </div>
            </div>

            {{-- TAB: RESUMEN --}}
            @if ($activeTab === 'overview')
                @php $stats = $this->getOverviewStats(); @endphp

                {{-- Resumen del Mes — Compact stat row --}}
                        <h3 class="flex items-center gap-2 mb-4 text-sm font-semibold text-gray-600">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            Resumen del Mes
                        </h3>
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
                            {{-- Proyectos --}}
                            <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-100">
                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                        </svg>
                                    </div>
                                    <span class="text-xs font-medium tracking-wider text-gray-400 uppercase">Proyectos</span>
                                </div>
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['projects_total'] }}</p>
                                <div class="flex gap-2 mt-2">
                                    <span class="text-xs text-emerald-600">✓ {{ $stats['projects_approved'] }}</span>
                                    <span class="text-xs text-blue-600">⚡ {{ $stats['projects_in_execution'] }}</span>
                                </div>
                            </div>

                            {{-- Cotizaciones --}}
                            <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-100">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <span class="text-xs font-medium tracking-wider text-gray-400 uppercase">Cotizaciones</span>
                                </div>
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['quotes_approved'] }}</p>
                                <div class="mt-2">
                                    <span class="text-xs text-amber-600">{{ $stats['quotes_pending'] }} pendientes</span>
                                </div>
                            </div>

                            {{-- Actas --}}
                            <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gray-200">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                        </svg>
                                    </div>
                                    <span class="text-xs font-medium tracking-wider text-gray-400 uppercase">Actas</span>
                                </div>
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['compliance_count'] }}</p>
                            </div>

                            {{-- Reportes --}}
                            <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-amber-100">
                                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <span class="text-xs font-medium tracking-wider text-gray-400 uppercase">Reportes</span>
                                </div>
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['work_reports_count'] }}</p>
                            </div>

                            {{-- Despachos --}}
                            <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gray-200">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                                        </svg>
                                    </div>
                                    <span class="text-xs font-medium tracking-wider text-gray-400 uppercase">Despachos</span>
                                </div>
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['warehouse_attended'] }}</p>
                                <div class="mt-2">
                                    <span class="text-xs text-amber-600">{{ $stats['warehouse_pending'] }} pendientes</span>
                                </div>
                            </div>
                        </div>
            @endif

        {{-- TAB: PROYECTOS --}}
        @if ($activeTab === 'projects')
            <div class="overflow-hidden rounded-xl ring-1 ring-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Código</th>
                                <th
                                    class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Proyecto</th>
                                <th
                                    class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Cliente</th>
                                <th
                                    class="px-4 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Estado</th>
                                <th
                                    class="px-4 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Cotización</th>
                                <th
                                    class="px-4 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Acta</th>
                                <th
                                    class="px-4 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Reportes</th>
                                <th
                                    class="px-4 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Almacén</th>
                                <th
                                    class="px-4 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Estado Final</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($this->getProjectsWithFullFlow() as $project)
                                <tr class="transition-colors hover:bg-gray-50">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span
                                            class="font-mono text-xs font-medium text-gray-600">{{ $project['service_code'] }}</span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="max-w-[200px]">
                                            <p class="font-medium text-gray-900 truncate" title="{{ $project['name'] }}">
                                                {{ Str::limit($project['name'], 25) }}
                                            </p>
                                            <p class="text-xs text-gray-500">{{ $project['created_at'] }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <p class="text-sm text-gray-900">
                                            {{ Str::limit($project['sub_client'], 20) }}
                                        </p>
                                        <p class="text-xs text-gray-500">{{ Str::limit($project['client'], 15) }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-4 text-center whitespace-nowrap">
                                        @php
                                            $statusStyles = [
                                                'Pendiente' => 'bg-yellow-100 text-yellow-700 ring-yellow-600/20',
                                                'Enviado' => 'bg-blue-100 text-blue-700 ring-blue-600/20',
                                                'Aprobado' => 'bg-green-100 text-green-700 ring-green-600/20',
                                                'En Ejecución' => 'bg-indigo-100 text-indigo-700 ring-indigo-600/20',
                                                'Completado' => 'bg-emerald-100 text-emerald-700 ring-emerald-600/20',
                                                'Facturado' => 'bg-purple-100 text-purple-700 ring-purple-600/20',
                                                'Anulado' => 'bg-gray-100 text-gray-700 ring-gray-600/20',
                                            ];
                                            $displayStatus =
                                                $project['status'] === 'Pending' ? 'Pendiente' : $project['status'];
                                        @endphp
                                        <span
                                            class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset {{ $statusStyles[$project['status']] ?? 'bg-gray-100 text-gray-700 ring-gray-600/20' }}">
                                            {{ $displayStatus }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-center whitespace-nowrap">
                                        <div class="flex flex-col items-center gap-1">
                                            @if ($project['quote_status'] === 'Aprobado')
                                                <span
                                                    class="flex items-center justify-center w-6 h-6 text-green-600 bg-green-100 rounded-full">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                            @elseif($project['quote_status'] === 'Pendiente')
                                                <span
                                                    class="flex items-center justify-center w-6 h-6 text-yellow-600 bg-yellow-100 rounded-full">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                            @else
                                                <span
                                                    class="flex items-center justify-center w-6 h-6 text-gray-400 bg-gray-100 rounded-full">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                            @endif
                                            <span class="text-xs text-gray-500">{{ $project['quote_total'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center whitespace-nowrap">
                                        @if ($project['has_compliance'])
                                            <span
                                                class="flex items-center justify-center w-6 h-6 mx-auto text-green-600 bg-green-100 rounded-full">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        @else
                                            <span
                                                class="flex items-center justify-center w-6 h-6 mx-auto text-red-500 bg-red-100 rounded-full">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-center whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 px-2.5 py-1 text-xs font-medium text-blue-700">
                                            <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $project['work_reports_count'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-center whitespace-nowrap">
                                        <div class="flex flex-col items-center gap-1">
                                            @if ($project['warehouse_status'] === 'Atendido')
                                                <span
                                                    class="flex items-center justify-center w-6 h-6 text-green-600 bg-green-100 rounded-full">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                            @elseif($project['warehouse_status'] === 'Parcial')
                                                <span
                                                    class="flex items-center justify-center w-6 h-6 text-orange-600 bg-orange-100 rounded-full">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                            @else
                                                <span
                                                    class="flex items-center justify-center w-6 h-6 text-gray-400 bg-gray-100 rounded-full">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                            @endif
                                            @if ($project['warehouse_progress'] > 0)
                                                <div class="h-1.5 w-12 overflow-hidden rounded-full bg-gray-200">
                                                    <div class="h-full transition-all bg-green-500 rounded-full"
                                                        style="width: {{ $project['warehouse_progress'] }}%"></div>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center whitespace-nowrap">
                                        @if ($project['is_complete'])
                                            <span
                                                class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700 ring-1 ring-inset ring-green-600/20">
                                                <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                Completo
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">
                                                <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                                </svg>
                                                En proceso
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                            <p class="mt-4 text-sm font-medium text-gray-500">No se encontraron
                                                proyectos</p>
                                            <p class="mt-1 text-xs text-gray-400">Intenta ajustar los filtros</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- TAB: GRÁFICOS --}}
        @if ($activeTab === 'charts')
            @php
                $chartData = $this->getChartData();
                $advancedData = $this->getAdvancedChartData();
                $approvedTimeline = $this->getApprovedQuotesTimeline();
                $approvedByMonth = $this->getApprovedQuotesByMonth();
                $pieChartData = $this->getProjectsByStatusPieChart();
                $monthlyExpenses = $this->getMonthlyExpenses();
                $mostExpensive = $this->getMostExpensiveProject();
                $topProjects = $this->getTopExpensiveProjects();
            @endphp
            <div class="grid gap-6 lg:grid-cols-2">

                {{-- NUEVO: Gráfico Circular - Proyectos por Estado --}}
                <div class="p-6 rounded-xl bg-gray-50 ring-1 ring-gray-200">
                    <h4 class="flex items-center gap-2 mb-6 text-lg font-semibold text-gray-800">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                        </svg>
                        Proyectos por Estado (Gráfico Circular)
                    </h4>

                    @if (count($pieChartData['data']) > 0)
                        <div class="flex items-center gap-6">
                            {{-- Gráfico Circular SVG --}}
                            <div class="relative flex-shrink-0">
                                <svg viewBox="0 0 100 100" class="w-40 h-40">
                                    @php
                                        $offset = 0;
                                        $total = $pieChartData['total'];
                                    @endphp
                                    @foreach ($pieChartData['data'] as $item)
                                        @php
                                            $percentage = $item['percentage'];
                                            $dashArray = ($percentage / 100) * 314.159;
                                            $dashOffset = -$offset * 3.14159;
                                            $offset += $percentage;
                                        @endphp
                                        <circle cx="50" cy="50" r="40" fill="transparent" stroke="{{ $item['color'] }}"
                                            stroke-width="20" stroke-dasharray="{{ $dashArray }} 314.159"
                                            stroke-dashoffset="{{ $dashOffset }}" transform="rotate(-90 50 50)"
                                            class="transition-all duration-500 cursor-pointer hover:opacity-80">
                                            <title>{{ $item['status'] }}: {{ $item['count'] }}
                                                ({{ $item['percentage'] }}%)
                                            </title>
                                        </circle>
                                    @endforeach
                                    {{-- Centro blanco --}}
                                    <circle cx="50" cy="50" r="25" fill="white" />
                                    {{-- Texto central --}}
                                    <text x="50" y="47" text-anchor="middle" class="text-lg font-bold fill-gray-800"
                                        style="font-size: 12px; font-weight: bold;">{{ $pieChartData['total'] }}</text>
                                    <text x="50" y="58" text-anchor="middle" class="text-xs fill-gray-500"
                                        style="font-size: 6px;">proyectos</text>
                                </svg>
                            </div>

                            {{-- Leyenda --}}
                            <div class="flex-1 space-y-2">
                                @foreach ($pieChartData['data'] as $item)
                                    <div
                                        class="flex items-center justify-between p-2 transition-colors bg-white rounded-lg hover:bg-gray-100">
                                        <div class="flex items-center gap-2">
                                            <span class="w-3 h-3 rounded-full"
                                                style="background-color: {{ $item['color'] }}"></span>
                                            <span class="text-sm font-medium text-gray-700">{{ $item['status'] }}</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-sm font-bold text-gray-900">{{ $item['count'] }}</span>
                                            <span class="ml-1 text-xs text-gray-500">({{ $item['percentage'] }}%)</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center h-40 text-gray-400">
                            <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                            </svg>
                            <p class="text-sm">No hay proyectos en este período</p>
                        </div>
                    @endif
                </div>

                {{-- NUEVO: Proyecto Más Costoso + Top 5 --}}
                <div class="p-6 rounded-xl bg-gray-50 ring-1 ring-gray-200">
                    <h4 class="flex items-center gap-2 mb-6 text-lg font-semibold text-gray-800">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Proyecto Más Costoso del Mes
                    </h4>

                    @if ($mostExpensive && $mostExpensive['total_amount'] > 0)
                        {{-- Tarjeta del proyecto más costoso --}}
                        <div class="p-4 mb-4 overflow-hidden bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <p class="text-xs font-medium text-white/80">🏆 Más costoso</p>
                                    <h5 class="mt-1 text-lg font-bold text-white truncate" title="{{ $mostExpensive['name'] }}">
                                        {{ Str::limit($mostExpensive['name'], 30) }}
                                    </h5>
                                    <p class="mt-1 text-sm text-white/90">{{ $mostExpensive['service_code'] }}</p>
                                    <p class="text-xs text-white/70">{{ $mostExpensive['sub_client'] }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-bold text-white">S/.
                                        {{ number_format($mostExpensive['total_amount'], 2) }}
                                    </p>
                                    <span
                                        class="inline-block px-2 py-0.5 mt-1 text-xs font-medium rounded-full bg-white/20 text-white">
                                        {{ $mostExpensive['status'] }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Top 5 proyectos --}}
                        <h5 class="mb-3 text-sm font-semibold text-gray-700">Top 5 Proyectos por Costo</h5>
                        <div class="space-y-2">
                            @foreach ($topProjects as $index => $project)
                                <div class="flex items-center gap-3 p-2 transition-colors bg-white rounded-lg hover:bg-gray-100">
                                    <span
                                        class="flex items-center justify-center w-6 h-6 text-xs font-bold rounded-full
                                                                {{ $index === 0 ? 'bg-amber-100 text-amber-700' : ($index === 1 ? 'bg-gray-200 text-gray-600' : 'bg-orange-100 text-orange-600') }}">
                                        {{ $index + 1 }}
                                    </span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-800 truncate" title="{{ $project['name'] }}">
                                            {{ Str::limit($project['name'], 25) }}
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $project['service_code'] }}</p>
                                    </div>
                                    <span class="flex-shrink-0 text-sm font-bold text-green-600">
                                        S/. {{ number_format($project['total_amount'], 2) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center h-40 text-gray-400">
                            <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm">No hay proyectos con costos en este período</p>
                        </div>
                    @endif
                </div>

                {{-- NUEVO: Gastos Mensuales (12 meses) --}}
                <div class="p-6 lg:col-span-2 rounded-xl bg-gray-50 ring-1 ring-gray-200">
                    <h4 class="flex items-center gap-2 mb-6 text-lg font-semibold text-gray-800">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Gastos por Cotizaciones Aprobadas (Últimos 12 meses)
                    </h4>

                    @if (count($monthlyExpenses) > 0)
                        @php
                            $expensesCollection = collect($monthlyExpenses);

                            // Total acumulado
                            $totalExpenses = $expensesCollection->sum('total');

                            // Meses con datos (que tienen gastos > 0)
                            $monthsWithData = $expensesCollection->filter(fn($m) => $m['total'] > 0);
                            $monthsWithDataCount = $monthsWithData->count();

                            // Promedio mensual (solo de meses CON datos)
                            $averageMonthly = $monthsWithDataCount > 0 ? $totalExpenses / $monthsWithDataCount : 0;

                            // Mes más alto
                            $maxExpense = $expensesCollection->max('total') ?: 0;

                            // Nombre del mes con mayor gasto
                            $maxMonth = $expensesCollection->sortByDesc('total')->first();
                            $maxMonthName = $maxMonth && $maxMonth['total'] > 0 ? $maxMonth['month_year'] : '-';
                        @endphp

                        {{-- Resumen rápido --}}
                        <div class="flex flex-wrap items-center gap-4 mb-6">
                            <div class="px-4 py-2 bg-white rounded-lg ring-1 ring-gray-200">
                                <span class="text-xs text-gray-500">Total acumulado (12 meses)</span>
                                <p class="text-lg font-bold text-green-600">S/. {{ number_format($totalExpenses, 2) }}
                                </p>
                            </div>
                            <div class="px-4 py-2 bg-white rounded-lg ring-1 ring-gray-200">
                                <span class="text-xs text-gray-500">Promedio mensual
                                    <span class="text-gray-400">({{ $monthsWithDataCount }} meses con datos)</span>
                                </span>
                                <p class="text-lg font-bold text-blue-600">S/. {{ number_format($averageMonthly, 2) }}
                                </p>
                            </div>
                            <div class="px-4 py-2 bg-white rounded-lg ring-1 ring-gray-200">
                                <span class="text-xs text-gray-500">Mes más alto
                                    <span class="text-gray-400">({{ $maxMonthName }})</span>
                                </span>
                                <p class="text-lg font-bold text-amber-600">S/. {{ number_format($maxExpense, 2) }}
                                </p>
                            </div>
                        </div>

                        {{-- Gráfico de barras --}}
                        <div class="relative">
                            {{-- Líneas de guía horizontales --}}
                            <div class="absolute inset-0 flex flex-col justify-between pointer-events-none"
                                style="height: 200px;">
                                @for ($i = 0; $i <= 4; $i++)
                                    <div class="flex items-center w-full">
                                        <span class="w-16 mr-2 text-[10px] text-gray-400 text-right">
                                            S/. {{ number_format($maxExpense - ($maxExpense / 4) * $i, 0) }}
                                        </span>
                                        <div class="flex-1 border-t border-gray-200 border-dashed"></div>
                                    </div>
                                @endfor
                            </div>

                            {{-- Barras del gráfico --}}
                            <div class="relative flex items-end gap-1 pl-20" style="height: 200px;">
                                @foreach ($monthlyExpenses as $expense)
                                    @php
                                        $heightPct = $maxExpense > 0 ? ($expense['total'] / $maxExpense) * 100 : 0;
                                    @endphp
                                    <div class="relative flex flex-col items-center flex-1 group">
                                        {{-- Tooltip --}}
                                        <div
                                            class="absolute z-20 hidden px-3 py-2 mb-1 text-xs text-white transform -translate-x-1/2 bg-gray-800 rounded-lg shadow-lg bottom-full left-1/2 group-hover:block whitespace-nowrap">
                                            <p class="font-semibold">{{ $expense['month_year'] }}</p>
                                            <p class="text-green-300">S/. {{ number_format($expense['total'], 2) }}
                                            </p>
                                            <p class="text-gray-300">{{ $expense['projects_count'] }} proyectos</p>
                                        </div>

                                        {{-- Valor encima de la barra --}}
                                        @if ($expense['total'] > 0)
                                            <span class="mb-1 text-[10px] font-bold text-gray-600 hidden group-hover:block">
                                                {{ number_format($expense['total'] / 1000, 1) }}k
                                            </span>
                                        @endif

                                        {{-- Barra --}}
                                        <div class="w-full rounded-t-md transition-all duration-300 cursor-pointer
                                                                    {{ $expense['is_current'] ? 'bg-gradient-to-t from-green-600 to-green-400' : 'bg-gradient-to-t from-green-400 to-green-300 hover:from-green-500 hover:to-green-400' }}"
                                            style="height: {{ max($heightPct, 2) }}%; min-height: {{ $expense['total'] > 0 ? '8px' : '2px' }};">
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Etiquetas de meses --}}
                            <div class="flex gap-1 pl-20 mt-2">
                                @foreach ($monthlyExpenses as $expense)
                                    <div class="flex-1 text-center">
                                        <span
                                            class="text-[10px] font-medium {{ $expense['is_current'] ? 'text-green-600 font-bold' : 'text-gray-500' }}">
                                            {{ $expense['month'] }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Leyenda --}}
                        <div class="flex items-center justify-end gap-4 mt-4 text-xs text-gray-500">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded bg-gradient-to-t from-green-600 to-green-400"></span>
                                <span>Mes seleccionado</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded bg-gradient-to-t from-green-400 to-green-300"></span>
                                <span>Meses anteriores</span>
                            </div>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center h-40 text-gray-400">
                            <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <p class="text-sm">Sin datos de gastos disponibles</p>
                        </div>
                    @endif
                </div>

                {{-- Proyectos por Estado (barras) - EXISTENTE --}}
                <div class="p-6 rounded-xl bg-gray-50 ring-1 ring-gray-200">
                    <h4 class="flex items-center gap-2 mb-6 text-lg font-semibold text-gray-800">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                        </svg>
                        Proyectos por Estado (Barras)
                    </h4>
                    <div class="space-y-4">
                        @php
                            $statusConfig = [
                                'Pendiente' => ['color' => 'bg-yellow-500', 'emoji' => '⏳'],
                                'Enviado' => ['color' => 'bg-blue-500', 'emoji' => '📤'],
                                'Aprobado' => ['color' => 'bg-green-500', 'emoji' => '✅'],
                                'En Ejecución' => ['color' => 'bg-indigo-500', 'emoji' => '⚡'],
                                'Completado' => ['color' => 'bg-emerald-500', 'emoji' => '🎉'],
                                'Facturado' => ['color' => 'bg-purple-500', 'emoji' => '💰'],
                                'Anulado' => ['color' => 'bg-gray-400', 'emoji' => '❌'],
                            ];
                            $totalProjects = array_sum($chartData['projects_by_status']) ?: 1;
                        @endphp
                        @foreach ($chartData['projects_by_status'] as $status => $count)
                            @php
                                $cfg = $statusConfig[$status] ?? ['color' => 'bg-gray-400', 'emoji' => '📁'];
                                $pct = round(($count / $totalProjects) * 100);
                            @endphp
                            <div class="flex items-center gap-3">
                                <span class="w-6 text-lg text-center">{{ $cfg['emoji'] }}</span>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-1 text-sm">
                                        <span class="font-medium text-gray-700">{{ $status }}</span>
                                        <span class="font-bold text-gray-900">{{ $count }} <span
                                                class="font-normal text-gray-500">({{ $pct }}%)</span></span>
                                    </div>
                                    <div class="h-2.5 w-full overflow-hidden rounded-full bg-gray-200">
                                        <div class="{{ $cfg['color'] }} h-full rounded-full transition-all duration-500"
                                            style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- ...existing code for remaining charts... --}}
            </div>
        @endif

        {{-- TAB: ACTIVIDAD --}}
        @if ($activeTab === 'activity')
            <div>
                <h4 class="flex items-center gap-2 mb-6 text-lg font-semibold text-gray-800">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    Actividad Reciente
                </h4>

                <div class="relative">
                    <div class="absolute left-5 top-0 h-full w-0.5 bg-gray-200"></div>
                    <div class="space-y-4">
                        @forelse($this->getRecentActivity() as $activity)
                            @php
                                $colorMap = [
                                    'green' => 'bg-green-500',
                                    'blue' => 'bg-blue-500',
                                    'purple' => 'bg-purple-500',
                                    'yellow' => 'bg-amber-500',
                                ];
                                $bgColor = $colorMap[$activity['color']] ?? 'bg-gray-500';
                            @endphp
                            <div class="relative flex gap-4 pl-10">
                                <div class="absolute left-3 top-1 h-4 w-4 rounded-full {{ $bgColor }} ring-4 ring-white">
                                </div>
                                <div class="flex-1 p-4 rounded-lg bg-gray-50">
                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <p class="font-medium text-gray-800">{{ $activity['message'] }}</p>
                                            <p class="flex items-center gap-1 mt-1 text-xs text-gray-500">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                {{ $activity['employee'] ?? 'Sistema' }}
                                            </p>
                                        </div>
                                        <span
                                            class="flex-shrink-0 text-xs text-gray-500">{{ $activity['date']?->diffForHumans() ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-12 text-center">
                                <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="mt-4 text-sm text-gray-500">No hay actividad reciente</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif
    </div>
    </div>
</x-filament-widgets::widget>