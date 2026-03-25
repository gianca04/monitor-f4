{{-- MODAL: Nuevo Lugar (SIDEOVER) --}}
<div id="modal-new-location"
    class="fixed inset-0 z-[100] hidden bg-gray-900/50 dark:bg-gray-900/80 transition-opacity duration-300">
    <div class="fixed inset-y-0 right-0 flex max-w-full pl-10">
        <div class="w-screen max-w-md bg-white dark:bg-gray-800 flex flex-col shadow-xl border-l dark:border-gray-700">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b dark:border-gray-700 font-sans">
                <div class="flex flex-col">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Registrar Ubicación
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 italic">Nueva ubicación de almacenaje</p>
                </div>
                <button type="button"
                    class="btn-close-loc text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white transition-colors">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Cerrar</span>
                </button>
            </div>

            <!-- Body -->
            <div class="flex-1 overflow-y-auto p-4 space-y-4 custom-scrollbar">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="new-loc-name"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="Ej: Almacén Principal">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Descripción <span
                            class="font-normal text-gray-400">(Opcional)</span></label>
                    <textarea id="new-loc-desc" rows="3"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white resize-none"
                        placeholder="Detalles breves..."></textarea>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex items-center p-4 border-t border-gray-200 dark:border-gray-700 space-x-2">
                <button id="btn-save-loc" type="button"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 flex-1 transition-all active:scale-95">
                    Guardar
                </button>
                <button type="button"
                    class="btn-close-loc py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 flex-1 transition-all">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: Historial de Despachos (SIDEOVER) --}}
<div id="modal-history"
    class="fixed inset-0 z-[100] hidden bg-gray-900/50 dark:bg-gray-900/80 transition-opacity duration-300">
    <div class="fixed inset-y-0 right-0 flex max-w-full pl-10">
        <div class="w-screen max-w-lg bg-white dark:bg-gray-800 flex flex-col shadow-xl border-l dark:border-gray-700">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b dark:border-gray-700">
                <div class="flex flex-col">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Historial de Despachos
                    </h3>
                    <p id="hist-title" class="text-sm text-gray-500 dark:text-gray-400"></p>
                </div>
                <button type="button"
                    class="btn-close-hist text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white transition-colors">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Cerrar</span>
                </button>
            </div>

            <!-- Body -->
            <div class="flex-1 overflow-y-auto p-4 custom-scrollbar relative min-h-[300px]">
                <div id="hist-loader"
                    class="hidden absolute inset-0 bg-white/80 dark:bg-gray-900/80 flex flex-col items-center justify-center z-10 backdrop-blur-sm">
                    <span class="material-symbols-outlined text-3xl animate-spin text-primary-500 mb-2">sync</span>
                    <span class="text-xs font-semibold text-gray-500 text-center">Cargando transacciones...</span>
                </div>
                <div id="hist-content"></div>
            </div>

            <!-- Footer -->
            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                <button type="button"
                    class="btn-close-hist py-2.5 w-full text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 transition-all">
                    Cerrar Panel
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: Crear Guía de Despacho (SIDEOVER) --}}
<div id="modal-dispatch-guide"
    class="fixed inset-0 z-[100] hidden bg-gray-900/50 dark:bg-gray-900/80 transition-opacity duration-300">
    <div class="fixed inset-y-0 right-0 flex max-w-full pl-10">
        <div class="w-screen max-w-md bg-white dark:bg-gray-800 flex flex-col shadow-xl border-l dark:border-gray-700">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b dark:border-gray-700 font-sans">
                <div class="flex flex-col">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white uppercase tracking-tight">
                        Guía de Despacho
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 italic">Generar remisión de salida</p>
                </div>
                <button type="button"
                    class="btn-close-guide text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white transition-colors">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Cerrar</span>
                </button>
            </div>

            <!-- Body -->
            <div class="flex-1 overflow-y-auto p-4 space-y-4 custom-scrollbar">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">N° de Guía <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="guide-number"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white font-mono"
                        placeholder="Ej: 001-000456">
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ubicación
                            Origen</label>
                        <select id="guide-origin"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Seleccione Origen...</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ubicación
                            Destino</label>
                        <select id="guide-destination"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Seleccione Destino...</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fecha de Traslado <span
                            class="text-red-500">*</span></label>
                    <input type="datetime-local" id="guide-transfer-date"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
            </div>

            <!-- Footer -->
            <div class="flex items-center p-4 border-t border-gray-200 dark:border-gray-700 space-x-2">
                <button id="btn-confirm-dispatch" type="button"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 flex-1 transition-all active:scale-95">
                    Confirmar
                </button>
                <button type="button"
                    class="btn-close-guide py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 flex-1 transition-all">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>