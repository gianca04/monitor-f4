{{-- MODAL: Nuevo Lugar --}}
<div id="modal-new-location" class="hidden bk-modal-backdrop transition-opacity duration-200">
    <div class="bk-modal-overlay"></div>
    <div class="bk-modal bk-modal--sm z-10">
        <div class="bk-modal-header">
            <div class="flex items-center gap-3">
                <div
                    class="w-8 h-8 rounded bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <span class="material-symbols-outlined text-[18px]">add_location_alt</span>
                </div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Registrar Ubicación</h3>
            </div>
            <button type="button"
                class="btn-close-loc text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <div class="bk-modal-body">
            <div>
                <label class="bk-label">Nombre <span class="text-red-500">*</span></label>
                <input type="text" id="new-loc-name" class="bk-input" placeholder="Ej: Almacén Principal">
            </div>
            <div>
                <label class="bk-label">Descripción <span class="font-normal text-gray-400">(Opcional)</span></label>
                <input type="text" id="new-loc-desc" class="bk-input" placeholder="Detalles breves...">
            </div>
        </div>
        <div class="bk-modal-footer bg-gray-50/80 dark:bg-gray-800/60 rounded-b-lg">
            <button type="button" class="btn-close-loc bk-btn bk-btn--secondary">
                Cancelar
            </button>
            <button type="button" id="btn-save-loc" class="bk-btn bk-btn--primary">
                <span class="material-symbols-outlined text-[16px]">save</span>
                <span>Guardar</span>
            </button>
        </div>
    </div>
</div>

{{-- MODAL: Historial de Despachos --}}
<div id="modal-history" class="hidden bk-modal-backdrop transition-opacity duration-200">
    <div class="bk-modal-overlay"></div>
    <div class="bk-modal bk-modal--md z-10 flex flex-col max-h-[90vh]">
        <div class="bk-modal-header shrink-0">
            <div class="flex items-center gap-3">
                <div
                    class="w-8 h-8 rounded bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <span class="material-symbols-outlined text-[18px]">history</span>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Historial de Despachos</h3>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium truncate max-w-[250px]"
                        id="hist-title"></p>
                </div>
            </div>
            <button type="button"
                class="btn-close-hist text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <div class="bk-modal-body custom-scrollbar flex-1 relative">
            <div id="hist-loader"
                class="hidden absolute inset-0 bg-white/80 dark:bg-gray-900/80 flex flex-col items-center justify-center z-10 backdrop-blur-sm">
                <span class="material-symbols-outlined text-3xl animate-spin text-primary-500 mb-2">sync</span>
                <span class="text-xs font-semibold text-gray-500">Cargando transacciones...</span>
            </div>
            <div id="hist-content"></div>
        </div>
        <div class="bk-modal-footer shrink-0 bg-gray-50/80 dark:bg-gray-800/60 rounded-b-lg">
            <button type="button" class="btn-close-hist bk-btn bk-btn--secondary">
                Cerrar
            </button>
        </div>
    </div>
</div>

{{-- MODAL: Crear Guía de Despacho --}}
<div id="modal-dispatch-guide" class="hidden bk-modal-backdrop z-[60] transition-opacity duration-200">
    <div class="bk-modal-overlay"></div>
    <div class="bk-modal bk-modal--sm z-10">
        <div class="bk-modal-header">
            <div class="flex items-center gap-3">
                <div
                    class="w-8 h-8 rounded bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <span class="material-symbols-outlined text-[18px]">local_shipping</span>
                </div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Generar Guía de Despacho</h3>
            </div>
            <button type="button"
                class="btn-close-guide text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <div class="bk-modal-body">
            <p class="text-xs text-gray-500 mb-2">Se registrará el movimiento agrupando los ítems a despachar.</p>
            <div>
                <label class="bk-label">N° de Guía <span class="text-red-500">*</span></label>
                <input type="text" id="guide-number" class="bk-input" placeholder="Ej: 001-000456">
            </div>
            <div>
                <label class="bk-label">Punto de Partida (Origen)</label>
                <select id="guide-origin" class="bk-select custom-scrollbar">
                    <option value="">Seleccione origen...</option>
                </select>
            </div>
            <div>
                <label class="bk-label">Punto de Llegada (Destino)</label>
                <select id="guide-destination" class="bk-select custom-scrollbar">
                    <option value="">Seleccione destino...</option>
                </select>
            </div>
            <div>
                <label class="bk-label">Fecha de Traslado <span class="text-red-500">*</span></label>
                <input type="datetime-local" id="guide-transfer-date" class="bk-input">
            </div>
        </div>
        <div class="bk-modal-footer bg-gray-50/80 dark:bg-gray-800/60 rounded-b-lg">
            <button type="button" class="btn-close-guide bk-btn bk-btn--secondary">
                Cancelar
            </button>
            <button type="button" id="btn-confirm-dispatch" class="bk-btn bk-btn--primary">
                <span class="material-symbols-outlined text-[16px]">send</span>
                <span>Confirmar Despacho</span>
            </button>
        </div>
    </div>
</div>