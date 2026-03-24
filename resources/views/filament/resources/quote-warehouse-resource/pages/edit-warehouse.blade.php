<x-filament-panels::page>
    <div>
        @vite(['resources/css/app.css','resources/css/banking.css', 'resources/css/quote-form.css'])
        <link
            href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
            rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <div>
            <x-quote-warehouse.header :quoteWarehouse="$quoteWarehouse" :client="$client" :quote="$quote" />

            <x-quote-warehouse.tabs>
                <x-quote-warehouse.tab id="despacho" title="Despacho Rapido" icon="inventory_2" active="true">
                    <x-quote-warehouse.table :details="$details" :locations="$locations" />
                    <x-quote-warehouse.footer :quoteWarehouse="$quoteWarehouse" />
                </x-quote-warehouse.tab>
                <x-quote-warehouse.tab id="guias" title="Guías de Despacho" icon="local_shipping">
                    <x-quote-warehouse.guides-list :guides="$dispatchGuides" />
                </x-quote-warehouse.tab>
            </x-quote-warehouse.tabs>

            <x-quote-warehouse.modals />
            <x-quote-warehouse.transaction-modal :users="$users" />
        </div>

        <script>
            /* eslint-disable */
            // Nota: Este bloque contiene sintaxis Blade/PHP que no es válida JavaScript pura
            window.quoteWarehouseConfig = {
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
                            is_external_purchase: false,
                            unit_price: {{ $item['unit_price'] ?? 0 }},
                            price_unit: null,
                            supplier_name: '',
                            receipt_number: '',
                            additional_cost: {{ $item['additional_cost'] ?? 0 }},
                            cost_description: '{{ addslashes($item['cost_description'] ?? '') }}',
                            tool_unit_id: {{ $item['tool_unit_id'] ?? 'null' }},
                            is_tool: {{ $item['is_tool'] ? 'true' : 'false' }},
                            available_units: @json($item['available_units'] ?? []),
                            employee_id: null
                        },
                    @endforeach
                ],
                locations: [
                    @foreach ($locations as $loc)
                        { id: {{ $loc->id }}, name: '{{ addslashes($loc->name) }}' },
                    @endforeach
                ],
                routes: {
                    storeLocation: '{{ route('quoteswarehouse.locations.store') }}',
                    history: '{{ url('quoteswarehouse/transactions') }}',
                    store: '{{ route('quoteswarehouse.store') }}'
                },
                users: @json($users->map(fn($u) => ['id' => $u->id, 'name' => $u->name]))
            };
        </script>
        @vite(['resources/js/quote-warehouse.js'])

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
    </div>
</x-filament-panels::page>