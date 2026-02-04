{{-- filepath: c:\xampp\htdocs\monitor-f4\resources\views\filament\widgets\admin-dashboard-widget.blade.php --}}
<x-filament-widgets::widget>
    <div class="admin-dashboard-widget" x-data="{ activeTab: @entangle('activeTab') }">
        @php
            $quickStats = $this->getQuickStats();
            $alerts = $this->getAlerts();
            $globalStats = $this->getGlobalStats();
        @endphp

        {{-- Header Principal --}}
        <div class="relative mb-6 overflow-hidden bg-white shadow-lg rounded-2xl ring-1 ring-gray-200">
            {{-- Barra superior de color --}}
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500">
            </div>

            <div class="relative z-10 p-6">
                {{-- Fila superior: Título y Filtros --}}
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <div
                                class="flex items-center justify-center w-16 h-16 border shadow-lg rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-indigo-500/30">
                                <svg class="text-white w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800 lg:text-3xl">Panel Administrativo</h1>
                            <p class="flex items-center gap-2 mt-1 text-sm text-gray-500">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ now()->format('l, d M Y - H:i') }}
                            </p>
                        </div>
                    </div>

                    {{-- Filtros con wire:model.live para reactividad --}}
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
                                class="pl-10 pr-4 text-sm text-gray-700 placeholder-gray-400 transition border border-gray-300 h-11 rounded-xl bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
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
                                class="pl-10 pr-10 text-sm text-gray-700 transition border border-gray-300 appearance-none h-11 rounded-xl bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
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
                            class="flex items-center justify-center transition bg-gray-100 hover:bg-gray-200 h-11 w-11 rounded-xl"
                            wire:loading.class="animate-spin">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Indicador de filtro activo --}}
                <div class="flex items-center gap-2 mt-3 text-sm">
                    <span class="text-gray-500">Mostrando datos de:</span>
                    <span class="px-2 py-1 font-medium text-indigo-700 bg-indigo-100 rounded-lg">
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $monthFilter)->format('F Y') }}
                    </span>
                    @if ($statusFilter !== 'all')
                        <span class="px-2 py-1 font-medium text-purple-700 bg-purple-100 rounded-lg">
                            Estado: {{ $statusFilter }}
                        </span>
                    @endif
                </div>

                {{-- Estadísticas Globales del Sistema (como UnifiedStatsWidget) --}}
                <div class="mt-6">
                    <h3 class="flex items-center gap-2 mb-3 text-sm font-semibold text-gray-700">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Estadísticas Globales del Sistema
                    </h3>
                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                        {{-- Clientes --}}
                        <div class="p-3 border border-blue-200 bg-blue-50 rounded-xl">
                            <div class="flex items-center gap-2">
                                <div class="flex items-center justify-center w-8 h-8 bg-blue-500 rounded-lg">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-lg font-bold text-blue-700">{{ $globalStats['clients'] }}</p>
                                    <p class="text-xs text-blue-600">Clientes</p>
                                </div>
                            </div>
                        </div>

                        {{-- Empleados Activos --}}
                        <div class="p-3 border border-green-200 bg-green-50 rounded-xl">
                            <div class="flex items-center gap-2">
                                <div class="flex items-center justify-center w-8 h-8 bg-green-500 rounded-lg">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-lg font-bold text-green-700">{{ $globalStats['active_employees'] }}
                                    </p>
                                    <p class="text-xs text-green-600">Empleados</p>
                                </div>
                            </div>
                        </div>

                        {{-- Total Proyectos --}}
                        <div class="p-3 border border-indigo-200 bg-indigo-50 rounded-xl">
                            <div class="flex items-center gap-2">
                                <div class="flex items-center justify-center w-8 h-8 bg-indigo-500 rounded-lg">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-lg font-bold text-indigo-700">{{ $globalStats['total_projects'] }}
                                    </p>
                                    <p class="text-xs text-indigo-600">Proyectos</p>
                                </div>
                            </div>
                        </div>

                        {{-- Cotizaciones Emitidas --}}
                        <div class="p-3 border border-yellow-200 bg-yellow-50 rounded-xl">
                            <div class="flex items-center gap-2">
                                <div class="flex items-center justify-center w-8 h-8 bg-yellow-500 rounded-lg">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-lg font-bold text-yellow-700">{{ $globalStats['total_quotes'] }}</p>
                                    <p class="text-xs text-yellow-600">Cotizaciones</p>
                                </div>
                            </div>
                        </div>

                        {{-- Cotizaciones Aprobadas --}}
                        <div class="p-3 border bg-emerald-50 border-emerald-200 rounded-xl">
                            <div class="flex items-center gap-2">
                                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-500">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-lg font-bold text-emerald-700">{{ $globalStats['approved_quotes'] }}
                                    </p>
                                    <p class="text-xs text-emerald-600">Aprobadas</p>
                                </div>
                            </div>
                        </div>

                        {{-- Total S/. Aprobadas --}}
                        <div class="p-3 border border-red-200 bg-red-50 rounded-xl">
                            <div class="flex items-center gap-2">
                                <div class="flex items-center justify-center w-8 h-8 bg-red-500 rounded-lg">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-red-700">S/.
                                        {{ number_format($globalStats['total_approved_amount'], 2) }}</p>
                                    <p class="text-xs text-red-600">Total Aprobado</p>
                                </div>
                            </div>
                        </div>

                        {{-- Actas --}}
                        <div class="p-3 border border-gray-200 bg-gray-50 rounded-xl">
                            <div class="flex items-center gap-2">
                                <div class="flex items-center justify-center w-8 h-8 bg-gray-500 rounded-lg">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-lg font-bold text-gray-700">{{ $globalStats['compliances'] }}</p>
                                    <p class="text-xs text-gray-600">Actas</p>
                                </div>
                            </div>
                        </div>

                        {{-- Preciario --}}
                        <div class="p-3 border border-purple-200 bg-purple-50 rounded-xl">
                            <div class="flex items-center gap-2">
                                <div class="flex items-center justify-center w-8 h-8 bg-purple-500 rounded-lg">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-lg font-bold text-purple-700">{{ $globalStats['pricelist_items'] }}
                                    </p>
                                    <p class="text-xs text-purple-600">Preciario</p>
                                </div>
                            </div>
                        </div>

                        {{-- Despachos Atendidos --}}
                        <div class="p-3 border bg-cyan-50 border-cyan-200 rounded-xl">
                            <div class="flex items-center gap-2">
                                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-cyan-500">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-lg font-bold text-cyan-700">
                                        {{ $globalStats['dispatches_attended'] }}</p>
                                    <p class="text-xs text-cyan-600">Despachos</p>
                                </div>
                            </div>
                        </div>

                        {{-- Despachos Pendientes --}}
                        <div class="p-3 border border-orange-200 bg-orange-50 rounded-xl">
                            <div class="flex items-center gap-2">
                                <div class="flex items-center justify-center w-8 h-8 bg-orange-500 rounded-lg">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-lg font-bold text-orange-700">
                                        {{ $globalStats['dispatches_pending'] }}</p>
                                    <p class="text-xs text-orange-600">Pendientes</p>
                                </div>
                            </div>
                        </div>

                        {{-- Consumo Hoy --}}
                        <div class="p-3 border border-pink-200 bg-pink-50 rounded-xl">
                            <div class="flex items-center gap-2">
                                <div class="flex items-center justify-center w-8 h-8 bg-pink-500 rounded-lg">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-lg font-bold text-pink-700">
                                        {{ $globalStats['projects_with_consumption_today'] }}</p>
                                    <p class="text-xs text-pink-600">Consumo Hoy</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Estadísticas rápidas del mes filtrado --}}
                <div class="grid grid-cols-2 gap-3 mt-6 md:grid-cols-5">
                    <div class="p-4 border border-gray-200 bg-gray-50 rounded-xl">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-gray-500 uppercase">Proyectos (mes)</span>
                            @if ($quickStats['projects_trend'] > 0)
                                <span class="flex items-center gap-1 text-xs font-medium text-green-600">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ $quickStats['projects_trend'] }}%
                                </span>
                            @elseif($quickStats['projects_trend'] < 0)
                                <span class="flex items-center gap-1 text-xs font-medium text-red-600">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ abs($quickStats['projects_trend']) }}%
                                </span>
                            @endif
                        </div>
                        <p class="mt-2 text-2xl font-bold text-gray-800">{{ $quickStats['projects_this_month'] }}</p>
                    </div>
                    <div class="p-4 border border-gray-200 bg-gray-50 rounded-xl">
                        <span class="text-xs font-medium text-gray-500 uppercase">Cotizaciones</span>
                        <p class="mt-2 text-2xl font-bold text-gray-800">{{ $quickStats['quotes_this_month'] }}</p>
                    </div>
                    <div class="p-4 border border-gray-200 bg-gray-50 rounded-xl">
                        <span class="text-xs font-medium text-gray-500 uppercase">Aprobadas</span>
                        <p class="mt-2 text-2xl font-bold text-green-600">{{ $quickStats['approved_this_month'] }}</p>
                    </div>
                    <div class="p-4 border border-gray-200 bg-gray-50 rounded-xl">
                        <span class="text-xs font-medium text-gray-500 uppercase">Pendientes</span>
                        <p class="mt-2 text-2xl font-bold text-amber-600">{{ $quickStats['pending_actions'] }}</p>
                    </div>
                    <div class="hidden p-4 border border-gray-200 md:block bg-gray-50 rounded-xl">
                        <span class="text-xs font-medium text-gray-500 uppercase">Flujos completos</span>
                        <p class="mt-2 text-2xl font-bold text-indigo-600">{{ $this->getCompletedProjectsCount() }}
                        </p>
                    </div>
                </div>

                {{-- Alertas importantes --}}
                @if (count($alerts) > 0)
                    <div class="flex gap-3 mt-4 overflow-x-auto">
                        @foreach ($alerts as $alert)
                            @php
                                $alertColors = [
                                    'warning' => 'bg-amber-100 text-amber-700 border-amber-300',
                                    'danger' => 'bg-red-100 text-red-700 border-red-300',
                                    'info' => 'bg-blue-100 text-blue-700 border-blue-300',
                                ];
                            @endphp
                            <div
                                class="flex items-center flex-shrink-0 gap-2 px-4 py-2 text-xs font-medium border rounded-full {{ $alertColors[$alert['type']] ?? $alertColors['info'] }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                {{ $alert['message'] }}
                            </div>
                        @endforeach
                    </div>
                @endif
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="{{ $tab['icon'] }}" />
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
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
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

                {{-- Tarjetas de Estadísticas --}}
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
                    {{-- Proyectos --}}
                    <div
                        class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 p-5 shadow-lg transition-transform hover:scale-[1.02]">
                        <div class="absolute w-24 h-24 rounded-full -right-4 -top-4 bg-white/10"></div>
                        <div class="relative">
                            <div class="flex items-center justify-between">
                                <svg class="w-8 h-8 text-white/80" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                </svg>
                                <span class="text-3xl font-bold text-white">{{ $stats['projects_total'] }}</span>
                            </div>
                            <p class="mt-2 text-sm font-medium text-white/90">Proyectos Totales</p>
                            <div class="flex gap-2 mt-3">
                                <span
                                    class="inline-flex items-center rounded-full bg-white/20 px-2 py-0.5 text-xs font-medium text-white">
                                    ✓ {{ $stats['projects_approved'] }}
                                </span>
                                <span
                                    class="inline-flex items-center rounded-full bg-white/20 px-2 py-0.5 text-xs font-medium text-white">
                                    ⚡ {{ $stats['projects_in_execution'] }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Cotizaciones Aprobadas --}}
                    <div
                        class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 p-5 shadow-lg transition-transform hover:scale-[1.02]">
                        <div class="absolute w-24 h-24 rounded-full -right-4 -top-4 bg-white/10"></div>
                        <div class="relative">
                            <div class="flex items-center justify-between">
                                <svg class="w-8 h-8 text-white/80" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-3xl font-bold text-white">{{ $stats['quotes_approved'] }}</span>
                            </div>
                            <p class="mt-2 text-sm font-medium text-white/90">Cotizaciones Aprobadas</p>
                            <div class="mt-3">
                                <span
                                    class="inline-flex items-center rounded-full bg-white/20 px-2 py-0.5 text-xs font-medium text-white">
                                    {{ $stats['quotes_pending'] }} pendientes
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Actas --}}
                    <div
                        class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-violet-500 to-violet-600 p-5 shadow-lg transition-transform hover:scale-[1.02]">
                        <div class="absolute w-24 h-24 rounded-full -right-4 -top-4 bg-white/10"></div>
                        <div class="relative">
                            <div class="flex items-center justify-between">
                                <svg class="w-8 h-8 text-white/80" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                            </div>
                            <span class="text-3xl font-bold text-white">{{ $stats['compliance_count'] }}</span>
                        </div>
                        <p class="mt-2 text-sm font-medium text-white/90">Actas Generadas</p>
                    </div>
                </div>

                {{-- Reportes --}}
                <div
                    class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 p-5 shadow-lg transition-transform hover:scale-[1.02]">
                    <div class="absolute w-24 h-24 rounded-full -right-4 -top-4 bg-white/10"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between">
                            <svg class="w-8 h-8 text-white/80" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="text-3xl font-bold text-white">{{ $stats['work_reports_count'] }}</span>
                        </div>
                        <p class="mt-2 text-sm font-medium text-white/90">Reportes de Trabajo</p>
                    </div>
                </div>

                {{-- Almacén --}}
                <div
                    class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-cyan-500 to-teal-500 p-5 shadow-lg transition-transform hover:scale-[1.02]">
                    <div class="absolute w-24 h-24 rounded-full -right-4 -top-4 bg-white/10"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between">
                            <svg class="w-8 h-8 text-white/80" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                            </svg>
                            <span class="text-3xl font-bold text-white">{{ $stats['warehouse_attended'] }}</span>
                        </div>
                        <p class="mt-2 text-sm font-medium text-white/90">Despachos Atendidos</p>
                        <div class="mt-3">
                            <span
                                class="inline-flex items-center rounded-full bg-white/20 px-2 py-0.5 text-xs font-medium text-white">
                                {{ $stats['warehouse_pending'] }} pendientes
                            </span>
                        </div>
                    </div>
                </div>
        </div>

        {{-- Métricas adicionales --}}
        @php $advancedData = $this->getAdvancedChartData(); @endphp
        <div class="grid gap-4 mt-6 lg:grid-cols-3">
            {{-- Tasa de conversión --}}
            <div class="p-5 rounded-xl bg-gray-50 ring-1 ring-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Tasa de Conversión</p>
                        <p class="mt-1 text-3xl font-bold text-gray-800">
                            {{ $advancedData['conversion_rate'] }}%
                        </p>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
                <p class="mt-2 text-xs text-gray-500">Cotizaciones aprobadas vs emitidas</p>
                <div class="h-2 mt-3 overflow-hidden bg-gray-200 rounded-full">
                    <div class="h-full bg-green-500 rounded-full"
                        style="width: {{ $advancedData['conversion_rate'] }}%"></div>
                </div>
            </div>

            {{-- Tiempo promedio de aprobación --}}
            <div class="p-5 rounded-xl bg-gray-50 ring-1 ring-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Tiempo Promedio Aprobación</p>
                        <p class="mt-1 text-3xl font-bold text-gray-800">
                            {{ $advancedData['avg_approval_days'] }} <span
                                class="text-lg font-normal text-gray-500">días</span></p>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <p class="mt-2 text-xs text-gray-500">Desde creación hasta aprobación</p>
            </div>

            {{-- Proyectos completados --}}
            <div class="p-5 rounded-xl bg-gray-50 ring-1 ring-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Flujos Completos</p>
                        <p class="mt-1 text-3xl font-bold text-gray-800">
                            {{ $this->getCompletedProjectsCount() }}
                        </p>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-full">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                    </div>
                </div>
                <p class="mt-2 text-xs text-gray-500">Cotización → Acta → Reportes → Despacho atendido</p>
            </div>
        </div>

        {{-- Banner de Proyectos Completos --}}
        <div
            class="mt-6 overflow-hidden rounded-xl bg-gradient-to-r from-green-50 to-emerald-50 ring-1 ring-green-200">
            <div class="flex items-center gap-6 p-6">
                <div
                    class="flex items-center justify-center flex-shrink-0 w-16 h-16 bg-green-500 rounded-full shadow-lg shadow-green-500/30">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-green-800">Proyectos con Flujo Completo</h3>
                    <p class="mt-1 text-sm text-green-600">
                        Cotización aprobada → Acta generada → Reportes → Despacho atendido
                    </p>
                </div>
                <div class="text-right">
                    <span class="text-5xl font-bold text-green-600">{{ $this->getCompletedProjectsCount() }}</span>
                    <p class="text-sm text-green-600">completados</p>
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
                                            <p class="font-medium text-gray-900 truncate"
                                                title="{{ $project['name'] }}">
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
                                                'Enviada' => 'bg-blue-100 text-blue-700 ring-blue-600/20',
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
                                                        d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
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
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.5"
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
            @endphp
            <div class="grid gap-6 lg:grid-cols-2">
                {{-- Proyectos por Estado --}}
                <div class="p-6 rounded-xl bg-gray-50 ring-1 ring-gray-200">
                    <h4 class="flex items-center gap-2 mb-6 text-lg font-semibold text-gray-800">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                        </svg>
                        Proyectos por Estado
                    </h4>
                    <div class="space-y-4">
                        @php
                            $statusConfig = [
                                'Pendiente' => ['color' => 'bg-yellow-500', 'emoji' => '⏳'],
                                'Enviada' => ['color' => 'bg-blue-500', 'emoji' => '📤'],
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

                {{-- Estado de Despachos --}}
                <div class="p-6 rounded-xl bg-gray-50 ring-1 ring-gray-200">
                    <h4 class="flex items-center gap-2 mb-6 text-lg font-semibold text-gray-800">
                        <svg class="w-5 h-5 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                        </svg>
                        Estado de Despachos
                    </h4>
                    <div class="flex flex-wrap items-center justify-center gap-6">
                        @php
                            $warehouseConfig = [
                                'Atendido' => ['bg' => 'from-green-400 to-green-600', 'ring' => 'ring-green-300'],
                                'Parcial' => ['bg' => 'from-orange-400 to-orange-600', 'ring' => 'ring-orange-300'],
                                'Pendiente' => [
                                    'bg' => 'from-yellow-400 to-yellow-600',
                                    'ring' => 'ring-yellow-300',
                                ],
                            ];
                        @endphp
                        @foreach ($chartData['warehouse_stats'] as $status => $count)
                            @php $cfg = $warehouseConfig[$status] ?? ['bg' => 'from-gray-400 to-gray-600', 'ring' => 'ring-gray-300']; @endphp
                            <div class="text-center">
                                <div
                                    class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-br {{ $cfg['bg'] }} {{ $cfg['ring'] }} ring-4 shadow-lg">
                                    <span class="text-3xl font-bold text-white">{{ $count }}</span>
                                </div>
                                <p class="mt-3 text-sm font-semibold text-gray-700">{{ $status }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Tendencia de Proyectos (12 meses) --}}
                <div class="p-6 rounded-xl bg-gray-50 ring-1 ring-gray-200">
                    <h4 class="flex items-center gap-2 mb-6 text-lg font-semibold text-gray-800">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        Tendencia de Proyectos (12 meses)
                    </h4>
                    @if ($advancedData['projects_trend']->count() > 0)
                        @php
                            $maxTrend = $advancedData['projects_trend']->max('count') ?: 1;
                            $totalProyectos = $advancedData['projects_trend']->sum('count');
                        @endphp

                        {{-- Resumen rápido --}}
                        <div class="flex items-center justify-between mb-4 text-sm">
                            <span class="text-gray-500">Total: <span
                                    class="font-bold text-indigo-600">{{ $totalProyectos }}</span>
                                proyectos</span>
                            <span class="text-gray-500">Máx: <span
                                    class="font-bold text-indigo-600">{{ $maxTrend }}</span>/mes</span>
                        </div>

                        {{-- Contenedor del gráfico --}}
                        <div class="relative">
                            {{-- Líneas de guía horizontales --}}
                            <div class="absolute inset-0 flex flex-col justify-between pointer-events-none"
                                style="height: 160px;">
                                @for ($i = 0; $i <= 4; $i++)
                                    <div class="flex items-center w-full">
                                        <span
                                            class="w-6 mr-2 text-[10px] text-gray-400 text-right">{{ round($maxTrend - ($maxTrend / 4) * $i) }}</span>
                                        <div class="flex-1 border-t border-gray-200 border-dashed"></div>
                                    </div>
                                @endfor
                            </div>

                            {{-- Barras del gráfico --}}
                            <div class="relative flex items-end gap-1 pl-8" style="height: 160px;">
                                @foreach ($advancedData['projects_trend'] as $index => $data)
                                    @php
                                        $heightPct = $maxTrend > 0 ? ($data['count'] / $maxTrend) * 100 : 0;
                                        $isCurrentMonth = $index === $advancedData['projects_trend']->count() - 1;
                                    @endphp
                                    <div class="relative flex flex-col items-center flex-1 group">
                                        {{-- Tooltip --}}
                                        <div
                                            class="absolute z-20 hidden px-2 py-1 mb-1 text-xs text-white transform -translate-x-1/2 bg-gray-800 rounded shadow-lg bottom-full left-1/2 group-hover:block whitespace-nowrap">
                                            <p class="font-semibold">{{ $data['month'] }} {{ $data['year'] }}</p>
                                            <p>{{ $data['count'] }} proyectos</p>
                                        </div>

                                        {{-- Valor encima de la barra --}}
                                        <span
                                            class="mb-1 text-xs font-bold {{ $isCurrentMonth ? 'text-indigo-600' : 'text-gray-600' }} {{ $data['count'] == 0 ? 'opacity-50' : '' }}">
                                            {{ $data['count'] }}
                                        </span>

                                        {{-- Barra --}}
                                        <div class="w-full rounded-t-md transition-all duration-300 cursor-pointer {{ $isCurrentMonth ? 'bg-gradient-to-t from-indigo-600 to-indigo-400' : 'bg-gradient-to-t from-indigo-400 to-indigo-300 hover:from-indigo-500 hover:to-indigo-400' }}"
                                            style="height: {{ max($heightPct, 2) }}%; min-height: {{ $data['count'] > 0 ? '8px' : '2px' }};">
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Etiquetas de meses --}}
                            <div class="flex gap-1 pl-8 mt-2">
                                @foreach ($advancedData['projects_trend'] as $index => $data)
                                    @php $isCurrentMonth = $index === $advancedData['projects_trend']->count() - 1; @endphp
                                    <div class="flex-1 text-center">
                                        <span
                                            class="text-[10px] font-medium {{ $isCurrentMonth ? 'text-indigo-600 font-bold' : 'text-gray-500' }}">
                                            {{ $data['month'] }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Indicador del mes actual --}}
                        <div class="flex items-center justify-end gap-2 mt-4 text-xs text-gray-500">
                            <span class="w-3 h-3 rounded bg-gradient-to-t from-indigo-600 to-indigo-400"></span>
                            <span>Mes actual</span>
                            <span class="w-3 h-3 ml-2 rounded bg-gradient-to-t from-indigo-400 to-indigo-300"></span>
                            <span>Meses anteriores</span>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center h-40 text-gray-400">
                            <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <p class="text-sm">Sin datos disponibles</p>
                        </div>
                    @endif
                </div>

                {{-- Cotizaciones por Mes --}}
                <div class="p-6 rounded-xl bg-gray-50 ring-1 ring-gray-200">
                    <h4 class="flex items-center gap-2 mb-6 text-lg font-semibold text-gray-800">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Cotizaciones por Mes (Últimos 6 meses)
                    </h4>
                    @if ($chartData['quotes_by_month']->count() > 0)
                        <div class="flex items-end justify-around gap-4" style="height: 200px;">
                            @php $maxTotal = $chartData['quotes_by_month']->max('total') ?: 1; @endphp
                            @foreach ($chartData['quotes_by_month'] as $data)
                                @php
                                    $heightPct = ($data->total / $maxTotal) * 100;
                                    $approvedPct = $data->total > 0 ? ($data->approved / $data->total) * 100 : 0;
                                @endphp
                                <div class="flex flex-col items-center flex-1">
                                    <span class="mb-2 text-sm font-bold text-gray-700">{{ $data->total }}</span>
                                    <div class="relative w-full max-w-[50px] overflow-hidden rounded-t-lg bg-blue-200"
                                        style="height: {{ max($heightPct, 10) }}%;">
                                        <div class="absolute bottom-0 w-full bg-green-500"
                                            style="height: {{ $approvedPct }}%;"></div>
                                    </div>
                                    <span
                                        class="mt-2 text-xs font-medium text-gray-500">{{ \Carbon\Carbon::parse($data->month . '-01')->format('M') }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex items-center justify-center gap-6 mt-6 text-sm">
                            <span class="flex items-center gap-2">
                                <span class="w-3 h-3 bg-blue-400 rounded"></span>
                                <span class="text-gray-600">Total</span>
                            </span>
                            <span class="flex items-center gap-2">
                                <span class="w-3 h-3 bg-green-500 rounded"></span>
                                <span class="text-gray-600">Aprobadas</span>
                            </span>
                        </div>
                    @else
                        <div class="flex items-center justify-center h-40 text-gray-400">No hay datos disponibles
                        </div>
                    @endif
                </div>

                {{-- NUEVO: Gráfico de Puntos - Cotizaciones Aprobadas por Fecha --}}
                <div class="p-6 lg:col-span-2 rounded-xl bg-gray-50 ring-1 ring-gray-200">
                    <h4 class="flex items-center gap-2 mb-6 text-lg font-semibold text-gray-800">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Cotizaciones Aprobadas (Ordenadas por Fecha)
                    </h4>

                    @if ($approvedTimeline->count() > 0)
                        {{-- Gráfico de puntos --}}
                        <div class="relative mb-6" style="height: 200px;">
                            <div class="absolute inset-0 flex items-end">
                                {{-- Línea base --}}
                                <div class="absolute bottom-8 left-0 right-0 h-0.5 bg-gray-300"></div>

                                {{-- Puntos --}}
                                <div class="flex items-end justify-between w-full px-4">
                                    @php
                                        $maxAmount = $approvedTimeline->max('total_amount') ?: 1;
                                        $count = $approvedTimeline->count();
                                    @endphp
                                    @foreach ($approvedTimeline as $index => $quote)
                                        @php
                                            $heightPct =
                                                $maxAmount > 0 ? ($quote['total_amount'] / $maxAmount) * 100 : 10;
                                            $dotSize = min(max($heightPct / 10, 3), 6);
                                        @endphp
                                        <div class="flex flex-col items-center group"
                                            style="flex: 1; max-width: {{ 100 / max($count, 1) }}%;">
                                            {{-- Tooltip --}}
                                            <div
                                                class="absolute z-10 hidden p-2 mb-2 text-xs text-white transition-opacity transform -translate-x-1/2 bg-gray-800 rounded-lg shadow-lg bottom-full group-hover:block whitespace-nowrap">
                                                <p class="font-semibold">{{ $quote['request_number'] }}</p>
                                                <p>{{ $quote['formatted_date'] }}</p>
                                                <p class="text-green-300">S/.
                                                    {{ number_format($quote['total_amount'], 2) }}</p>
                                                <p class="text-gray-300">
                                                    {{ Str::limit($quote['sub_client'], 20) }}</p>
                                            </div>

                                            {{-- Línea vertical --}}
                                            <div class="w-0.5 bg-green-300 transition-all group-hover:bg-green-500"
                                                style="height: {{ max($heightPct, 10) }}px;"></div>

                                            {{-- Punto --}}
                                            <div class="w-3 h-3 transition-transform transform bg-green-500 border-2 border-white rounded-full shadow-md cursor-pointer group-hover:scale-150 group-hover:bg-green-600"
                                                title="{{ $quote['request_number'] }} - {{ $quote['formatted_date'] }}">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Eje Y labels --}}
                            <div class="absolute left-0 flex flex-col justify-between h-full text-xs text-gray-500"
                                style="top: 0; bottom: 32px;">
                                <span>S/. {{ number_format($maxAmount, 0) }}</span>
                                <span>S/. {{ number_format($maxAmount / 2, 0) }}</span>
                                <span>S/. 0</span>
                            </div>
                        </div>

                        {{-- Lista de cotizaciones aprobadas --}}
                        <div class="mt-6">
                            <h5 class="mb-3 text-sm font-semibold text-gray-700">Detalle de Cotizaciones Aprobadas
                            </h5>
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm divide-y divide-gray-200">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th
                                                class="px-3 py-2 text-xs font-semibold text-left text-gray-600 uppercase">
                                                Fecha</th>
                                            <th
                                                class="px-3 py-2 text-xs font-semibold text-left text-gray-600 uppercase">
                                                N° Cotización</th>
                                            <th
                                                class="px-3 py-2 text-xs font-semibold text-left text-gray-600 uppercase">
                                                Proyecto</th>
                                            <th
                                                class="px-3 py-2 text-xs font-semibold text-left text-gray-600 uppercase">
                                                Cliente</th>
                                            <th
                                                class="px-3 py-2 text-xs font-semibold text-left text-gray-600 uppercase">
                                                Cotizador</th>
                                            <th
                                                class="px-3 py-2 text-xs font-semibold text-right text-gray-600 uppercase">
                                                Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-100">
                                        @foreach ($approvedTimeline as $quote)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-3 py-2 text-gray-600 whitespace-nowrap">
                                                    {{ $quote['formatted_date'] }}</td>
                                                <td class="px-3 py-2 font-medium text-gray-900 whitespace-nowrap">
                                                    {{ $quote['request_number'] }}</td>
                                                <td class="px-3 py-2 text-gray-600"
                                                    title="{{ $quote['project_name'] }}">
                                                    {{ Str::limit($quote['project_name'], 30) }}</td>
                                                <td class="px-3 py-2 text-gray-600">
                                                    {{ Str::limit($quote['sub_client'], 25) }}</td>
                                                <td class="px-3 py-2 text-gray-600">{{ $quote['employee'] }}</td>
                                                <td
                                                    class="px-3 py-2 font-semibold text-right text-green-600 whitespace-nowrap">
                                                    S/. {{ number_format($quote['total_amount'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                            <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-lg font-medium">No hay cotizaciones aprobadas</p>
                            <p class="text-sm">Las cotizaciones aprobadas aparecerán aquí ordenadas por fecha</p>
                        </div>
                    @endif
                </div>
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
                                <div
                                    class="absolute left-3 top-1 h-4 w-4 rounded-full {{ $bgColor }} ring-4 ring-white">
                                </div>
                                <div class="flex-1 p-4 rounded-lg bg-gray-50">
                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <p class="font-medium text-gray-800">{{ $activity['message'] }}</p>
                                            <p class="flex items-center gap-1 mt-1 text-xs text-gray-500">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
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
