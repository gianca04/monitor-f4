@props(['users'])

<!-- Slide-over Backdrop (Container) -->
<div id="modal-transaction" class="fixed inset-0 z-[100] hidden bg-gray-900/50 dark:bg-gray-900/80"
    x-data="{ isExternal: false }">

    <!-- Panel Container -->
    <div class="fixed inset-y-0 right-0 flex max-w-full">
        <!-- Width defined by responsive classes, default to 100% on mobile and sm:max-w-md onwards -->
        <div class="w-screen max-w-md bg-white dark:bg-gray-800 flex flex-col shadow-xl border-l dark:border-gray-700">

            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b dark:border-gray-700">
                <div class="flex flex-col">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Añadir Despacho
                    </h3>
                    <input type="hidden" id="tx-product-name" class="text-sm text-gray-500 dark:text-gray-400">
                </div>
                <button type="button"
                    class="btn-close-tx text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Cerrar</span>
                </button>
            </div>

            <!-- Body (Scrollable) -->
            <div class="flex-1 overflow-y-auto p-4 space-y-4">
                <form id="form-transaction">
                    <input type="hidden" id="tx-index">
                    <input type="hidden" id="tx-req-id">

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Descripción</label>
                        <div id="tx-description"
                            class="p-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <p>-</p>
                        </div>
                    </div>

                    <div>
                        <label for="tx-quantity"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cantidad</label>
                        <input type="number" id="tx-quantity" step="0.01" min="0" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <div class="flex items-center py-2">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="tx-is-external" class="sr-only peer"
                                @change="isExternal = $el.checked">
                            <div
                                class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                            </div>
                            <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Registro de Compra
                                Externa</span>
                        </label>
                    </div>

                    <div x-show="isExternal" x-cloak
                        class="grid gap-4 grid-cols-2 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <div class="col-span-1">
                            <label for="tx-unit-price-reference"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Precio Ref.
                                (S/)</label>
                            <input type="number" id="tx-unit-price-reference" readonly
                                class="bg-gray-100 border border-gray-300 text-gray-500 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500">
                        </div>
                        <div class="col-span-1">
                            <label for="tx-price-unit"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Precio Real
                                (S/)</label>
                            <input type="number" id="tx-price-unit" step="0.01"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600">
                        </div>
                        <div class="col-span-2">
                            <label for="tx-receipt-number"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">N°
                                Comprobante</label>
                            <input type="text" id="tx-receipt-number"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600">
                        </div>
                        <div class="col-span-2">
                            <label for="tx-supplier-name"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Proveedor</label>
                            <input type="text" id="tx-supplier-name"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600">
                        </div>
                    </div>

                    <div class="grid gap-4 grid-cols-2">
                        <div class="col-span-1">
                            <label for="tx-additional-cost"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Costo Adic.
                                (S/)</label>
                            <input type="number" id="tx-additional-cost" step="0.01"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600">
                        </div>
                        <div class="col-span-1">
                            <label for="tx-cost-description"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Detalle
                                Costo</label>
                            <input type="text" id="tx-cost-description"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600">
                        </div>
                    </div>

                    <div>
                        <label for="tx-comment"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Observaciones</label>
                        <textarea id="tx-comment" rows="3"
                            class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            placeholder="Notas del despacho..."></textarea>
                    </div>

                    <div id="tx-tool-unit-container" class="hidden">
                        <label for="tx-tool-unit-id"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Unidad de
                            Herramienta</label>
                        <select id="tx-tool-unit-id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Seleccione...</option>
                        </select>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="flex items-center p-4 border-t border-gray-200 dark:border-gray-700 space-x-2">
                <button id="btn-save-tx" type="button"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 flex-1">
                    Guardar
                </button>
                <button type="button"
                    class="btn-close-tx py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 flex-1">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>