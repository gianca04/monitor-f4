@props(['users'])

<div id="modal-transaction" class="hidden bk-modal-backdrop transition-opacity duration-200 z-[70]" x-data="{ isExternal: false }" style="font-family: 'Inter', sans-serif;">
    <div class="bk-modal-overlay"></div>
    <div class="bk-modal bk-modal--lg flex flex-col max-h-[90vh]">

        {{-- Header --}}
        <div class="bk-modal-header shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded bg-primary-50 dark:bg-primary-500/10 flex items-center justify-center text-primary-600 dark:text-primary-400">
                    <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white leading-tight mb-0.5">
                        Añadir Despacho Detallado
                    </h3>
                    <p id="tx-product-name" class="text-[11px] font-medium text-gray-500 dark:text-gray-400 truncate max-w-[300px]"></p>
                </div>
            </div>

            <button type="button" class="btn-close-tx text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        {{-- Body --}}
        <div class="bk-modal-body custom-scrollbar flex-1 overflow-y-auto space-y-6">
            <form id="form-transaction">
                <input type="hidden" id="tx-index">
                <input type="hidden" id="tx-req-id">

                <div class="space-y-6">

                    {{-- Sección: Información Principal --}}
                    <div class="space-y-4">
                        <div class="bk-section-title flex items-center gap-2 m-0 border-b-0 pb-1">
                            Información General
                        </div>

                        <div class="">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 ml-1">
                                    Descripción del item
                                </label>
                                <div id="tx-description" class="relative group p-3 rounded-lg bg-gradient-to-br from-primary-50 to-primary-50/50 dark:from-primary-500/10 dark:to-primary-500/5 border border-primary-200 dark:border-primary-500/20 shadow-sm">
                                    <div class="flex items-start gap-2">
                                        <span class="material-symbols-outlined text-primary-600 dark:text-primary-400 text-[18px] shrink-0 mt-0.5">description</span>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white leading-relaxed break-words whitespace-normal">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="">
                            <div class="flex flex-col gap-1.5">
                                <label for="tx-quantity" class="text-xs font-semibold text-gray-600 dark:text-gray-400 ml-1">
                                    Cantidad<span class="text-red-500 ml-0.5">*</span>
                                </label>
                                <div class="relative group">
                                    {{-- Quité el pl-9 porque este input no tiene ícono a la izquierda, así el texto se alinea bien --}}
                                    <input type="number" id="tx-quantity" step="0.01" min="0" required
                                        class="bk-input shadow-sm" placeholder="0.00">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="md:col-span-2">
                                <label
                                    :class="isExternal ? 'border-amber-500 bg-amber-50/50 dark:bg-amber-900/20' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800'"
                                    class="relative flex items-center p-4 rounded-xl border-2 cursor-pointer transition-all duration-300 group hover:shadow-md">

                                    <div class="flex-1 pr-4">
                                        <div class="flex items-center gap-2 mb-0.5">
                                            <span :class="isExternal ? 'text-amber-700 dark:text-amber-400' : 'text-gray-700 dark:text-gray-300'"
                                                class="font-bold text-sm transition-colors">
                                                Registro de Compra Externa
                                            </span>
                                            <template x-if="isExternal">
                                                <span class="px-2 py-0.5 text-[10px] bg-amber-500 text-white rounded-full uppercase tracking-wider font-bold">Activo</span>
                                            </template>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                            Habilita la carga de costos, comprobantes y datos del proveedor para este ítem.
                                        </p>
                                    </div>

                                    <div class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="tx-is-external" class="sr-only peer" @change="isExternal = $el.checked">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                                    </div>
                                </label>
                            </div>

                            <div x-show="isExternal"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 -translate-y-4"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="md:col-span-2">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div class="flex flex-col gap-1.5">
                                        <label for="tx-unit-price-reference" class="text-xs font-semibold text-gray-600 dark:text-gray-400 ml-1 flex items-center gap-1.5">
                                            Precio Unitario Previsto
                                            <span class="text-[10px] font-normal opacity-70">(Referencia)</span>
                                        </label>
                                        <div class="relative group">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none">S/</span>
                                            <input type="number" id="tx-unit-price-reference" step="0.01" readonly
                                                class="bk-input !pl-9 bg-gray-100 dark:bg-gray-800 text-gray-500 cursor-not-allowed border-gray-200 shadow-none" placeholder="N/A">
                                        </div>
                                    </div>

                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 ml-1">
                                            Precio Unitario (S/)<span class="text-red-500 ml-0.5">*</span>
                                        </label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none">S/</span>
                                            <input type="number" id="tx-price-unit" step="0.01" required
                                                class="bk-input !pl-8 focus:ring-amber-500 focus:border-amber-500 shadow-sm" placeholder="0.00">
                                        </div>
                                    </div>

                                    <div class="md:col-span-2 flex flex-col gap-1.5">
                                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 ml-1">
                                            N° Comprobante<span class="text-red-500 ml-0.5">*</span>
                                        </label>
                                        <input type="text" id="tx-receipt-number" required
                                            class="bk-input focus:ring-amber-500 focus:border-amber-500 shadow-sm" placeholder="">
                                    </div>

                                    <div class="md:col-span-2 flex flex-col gap-1.5">
                                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 ml-1">
                                            Proveedor / Razón Social<span class="text-red-500 ml-0.5">*</span>
                                        </label>
                                        <input type="text" id="tx-supplier-name" required
                                            class="bk-input focus:ring-amber-500 focus:border-amber-500 shadow-sm"
                                            placeholder="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Sección: Costos y Observaciones --}}
                    <div class="space-y-4">
                        <div class="bk-section-title flex items-center gap-2 m-0 border-b-0 pb-1 text-primary-600 dark:text-primary-500">
                            Costos Logísticos y Notas
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 ml-1">
                                    Costo Adicional (S/)
                                </label>
                                <div class="relative group">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none">S/</span>
                                    <input type="number" id="tx-additional-cost" step="0.01" class="bk-input !pl-8 focus:ring-amber-500 focus:border-amber-500 shadow-sm"
                                        placeholder="0.00">
                                </div>
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 ml-1">
                                    Detalle del Costo
                                </label>
                                <input type="text" id="tx-cost-description" class="bk-input shadow-sm"
                                    placeholder="Ej: Delivery, Movilidad extra">
                            </div>

                            <div class="md:col-span-2 flex flex-col gap-1.5">
                                <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 ml-1">
                                    Nota de Despacho
                                </label>
                                <textarea id="tx-comment" rows="2" class="bk-input custom-scrollbar shadow-sm"
                                    placeholder="Escribe aquí cualquier observación relevante..."></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Sección: Herramientas --}}
                    <div id="tx-tool-unit-container" class="space-y-4 hidden">
                        <div class="bk-section-title flex items-center gap-2 m-0 border-b-0 pb-1 text-emerald-600 dark:text-emerald-500">
                            <span class="material-symbols-outlined text-[16px]">build</span>
                            Unidad de Herramienta
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="md:col-span-2 flex flex-col gap-1.5">
                                <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 ml-1">
                                    Unidad Específica (N° Serie / Código)
                                </label>
                                <div class="relative group">
                                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-emerald-500 text-[18px] pointer-events-none">qr_code_2</span>
                                    <select id="tx-tool-unit-id"
                                        class="bk-select border-emerald-300 dark:border-emerald-700 bg-emerald-50/50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 focus:border-emerald-500 focus:ring-emerald-500 !pl-9 !pr-9 appearance-none shadow-sm">
                                        <option value="">Seleccione una unidad operativa...</option>
                                    </select>
                                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-emerald-400 pointer-events-none text-[18px]">expand_more</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Footer --}}
        <div class="bk-modal-footer shrink-0 bg-gray-50/80 dark:bg-gray-800/60 rounded-b-lg">
            <button type="button" class="btn-close-tx bk-btn bk-btn--secondary hover:bg-gray-100">
                Cancelar
            </button>
            <button type="button" id="btn-save-tx" class="bk-btn bk-btn--primary">
                <span class="material-symbols-outlined text-[18px]">save</span>
                <span>Guardar</span>
            </button>
        </div>
    </div>
</div>