class VanillaTabsManager {
    constructor() {
        this.containers = document.querySelectorAll('.vanilla-tabs-container');
        this.init();
    }

    init() {
        this.containers.forEach(container => {
            const nav = container.querySelector('.vanilla-tabs-nav');
            const panes = container.querySelectorAll('.vanilla-tab-pane');

            nav.innerHTML = ''; // Limpiar botones previos en caso de rehidratación

            panes.forEach(pane => {
                const id = pane.dataset.tabId;
                const title = pane.dataset.tabTitle;
                const icon = pane.dataset.tabIcon;
                const isActive = pane.dataset.tabActive === 'true' || pane.classList.contains('active');

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = `flex items-center gap-2 px-4 py-3 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap ${isActive
                    ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'
                    }`;
                btn.dataset.targetId = id;

                let innerHTML = '';
                if (icon) {
                    innerHTML += `<span class="material-symbols-outlined text-[18px]">${icon}</span> `;
                }
                innerHTML += title;
                btn.innerHTML = innerHTML;

                btn.addEventListener('click', () => this.switchTab(container, id));
                nav.appendChild(btn);
            });
        });
    }

    switchTab(container, targetId) {
        const nav = container.querySelector('.vanilla-tabs-nav');
        const panes = container.querySelectorAll('.vanilla-tab-pane');

        panes.forEach(pane => {
            if (pane.dataset.tabId === targetId) {
                pane.classList.remove('hidden');
                pane.classList.add('active', 'block');
            } else {
                pane.classList.add('hidden');
                pane.classList.remove('active', 'block');
            }
        });

        nav.querySelectorAll('button').forEach(btn => {
            if (btn.dataset.targetId === targetId) {
                btn.className = 'flex items-center gap-2 px-4 py-3 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap border-blue-500 text-blue-600 dark:text-blue-400';
            } else {
                btn.className = 'flex items-center gap-2 px-4 py-3 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300';
            }
        });
    }
}

class QuoteWarehouseEditor {
    constructor(config) {
        this.config = config;
        this.items = config.items;
        this.locations = config.locations;

        this.cacheElements();
        this.bindEvents();
        this.bindColumnResizers();
        this.updateProgress();
    }

    cacheElements() {
        this.inputsQty = document.querySelectorAll('.qty-input');
        this.inputsIsExternal = document.querySelectorAll('.is-external-checkbox');
        this.selectsTool = document.querySelectorAll('.tool-unit-select');
        this.inputsComment = document.querySelectorAll('.comment-input');
        this.inputsPriceUnit = document.querySelectorAll('.price-unit-input');
        this.inputsSupplier = document.querySelectorAll('.supplier-input');
        this.inputsReceipt = document.querySelectorAll('.receipt-input');
        this.btnFillAll = document.querySelector('.btn-fill-all');
        this.inputObs = document.querySelector('.obs-input');
        this.btnSubmit = document.querySelector('.btn-submit');
        this.progressText = document.querySelector('.progress-text');
        this.progressBar = document.querySelector('.progress-bar');

        // Modals
        this.modalLoc = document.getElementById('modal-new-location');
        this.inputLocName = document.getElementById('new-loc-name');
        this.inputLocDesc = document.getElementById('new-loc-desc');
        this.btnSaveLoc = document.getElementById('btn-save-loc');
        this.btnCloseLoc = document.querySelectorAll('.btn-close-loc');

        this.modalHist = document.getElementById('modal-history');
        this.histTitle = document.getElementById('hist-title');
        this.histContent = document.getElementById('hist-content');
        this.histLoader = document.getElementById('hist-loader');
        this.btnCloseHist = document.querySelectorAll('.btn-close-hist');

        // Modal de Guía de Despacho
        this.modalGuide = document.getElementById('modal-dispatch-guide');
        this.inputGuideNumber = document.getElementById('guide-number');
        this.inputGuideOrigin = document.getElementById('guide-origin');
        this.inputGuideDest = document.getElementById('guide-destination');
        this.inputGuideDate = document.getElementById('guide-transfer-date');
        this.btnConfirmDispatch = document.getElementById('btn-confirm-dispatch');
        this.btnCloseGuide = document.querySelectorAll('.btn-close-guide');

        // Modal de Transacción Detallada
        this.modalTx = document.getElementById('modal-transaction');
        this.formTx = document.getElementById('form-transaction');
        this.txIndex = document.getElementById('tx-index');
        this.txProductName = document.getElementById('tx-product-name');
        this.txDescription = document.getElementById('tx-description');
        this.txQty = document.getElementById('tx-quantity');
        this.txUnitPriceReference = document.getElementById('tx-unit-price-reference');
        this.txIsExternal = document.getElementById('tx-is-external');
        this.txPriceUnit = document.getElementById('tx-price-unit');
        this.txReceipt = document.getElementById('tx-receipt-number');
        this.txSupplier = document.getElementById('tx-supplier-name');
        this.txAddCost = document.getElementById('tx-additional-cost');
        this.txCostDesc = document.getElementById('tx-cost-description');
        this.txComment = document.getElementById('tx-comment');
        this.txToolUnitContainer = document.getElementById('tx-tool-unit-container');
        this.txToolUnitSelect = document.getElementById('tx-tool-unit-id');
        this.btnSaveTx = document.getElementById('btn-save-tx');
        this.btnCloseTx = document.querySelectorAll('.btn-close-tx');
    }

    bindEvents() {
        this.inputsQty.forEach(input => {
            input.addEventListener('input', (e) => {
                let idx = e.target.dataset.index;
                let val = parseInt(e.target.value) || 0;
                let max = parseInt(e.target.max) || 0;
                if (val > max) {
                    val = max;
                    e.target.value = val;
                }
                if (val < 0) {
                    val = 0;
                    e.target.value = val;
                }
                this.items[idx].despachar = val;
                this.updateProgress();
            });
        });

        this.inputsIsExternal.forEach(input => {
            input.addEventListener('change', (e) => {
                let idx = e.target.dataset.index;
                this.items[idx].is_external_purchase = e.target.checked;
            });
        });

        this.selectsTool.forEach(select => {
            select.addEventListener('change', (e) => {
                let idx = e.target.dataset.index;
                this.items[idx].tool_unit_id = e.target.value;
            });
        });

        this.inputsComment.forEach(input => {
            input.addEventListener('input', (e) => {
                let idx = e.target.dataset.index;
                this.items[idx].comment = e.target.value;
            });
        });

        this.inputsPriceUnit.forEach(input => {
            input.addEventListener('input', (e) => {
                let idx = e.target.dataset.index;
                this.items[idx].price_unit = parseFloat(e.target.value) || null;
            });
        });

        this.inputsSupplier.forEach(input => {
            input.addEventListener('input', (e) => {
                let idx = e.target.dataset.index;
                this.items[idx].supplier_name = e.target.value;
            });
        });

        this.inputsReceipt.forEach(input => {
            input.addEventListener('input', (e) => {
                let idx = e.target.dataset.index;
                this.items[idx].receipt_number = e.target.value;
            });
        });

        if (this.btnFillAll) {
            this.btnFillAll.addEventListener('click', () => this.fillAll());
        }

        if (this.btnSubmit) {
            this.btnSubmit.addEventListener('click', () => this.openGuideModal());
        }

        if (this.btnConfirmDispatch) {
            this.btnConfirmDispatch.addEventListener('click', () => this.submitForm());
        }

        // Ya no existen location_origin_id o location_destination_id a nivel de item, 
        // pero sí a nivel global en la guía, y podemos querer crear un nuevo lugar allí.
        // Asignamos la funcionalidad si el usuario creó un botón de nuevo lugar junto al modal de guía.
        document.querySelectorAll('.btn-new-loc-origin').forEach(btn => {
            btn.addEventListener('click', () => this.openLocModal('origin'));
        });

        document.querySelectorAll('.btn-new-loc-dest').forEach(btn => {
            btn.addEventListener('click', () => this.openLocModal('destination'));
        });

        document.querySelectorAll('.btn-history').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.openHistoryModal(e.currentTarget.dataset.reqId, e.currentTarget.dataset.name);
            });
        });

        this.btnCloseLoc.forEach(btn => btn.addEventListener('click', () => this.closeLocModal()));
        this.btnCloseHist.forEach(btn => btn.addEventListener('click', () => this.closeHistoryModal()));
        this.btnCloseGuide.forEach(btn => btn.addEventListener('click', () => this.closeGuideModal()));

        if (this.btnSaveLoc) {
            this.btnSaveLoc.addEventListener('click', () => this.createLocation());
        }

        document.querySelectorAll('.btn-add-tx').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.openTransactionModal(e.currentTarget.dataset.index, e.currentTarget.dataset.name, e.currentTarget.dataset.name);
            });
        });

        this.btnCloseTx.forEach(btn => btn.addEventListener('click', () => this.closeTransactionModal()));

        if (this.btnSaveTx) {
            this.btnSaveTx.addEventListener('click', () => this.saveTransaction());
        }

        // Validación de cantidad en tiempo real - Modal
        if (this.txQty) {
            this.txQty.addEventListener('input', (e) => {
                const idx = this.txIndex.value;
                const item = this.items[idx];
                if (!item) return;

                let val = parseFloat(e.target.value) || 0;
                const max = item.solicitado - item.entregado;

                if (val > max) {
                    val = max;
                    e.target.value = val;
                }
                if (val < 0) {
                    val = 0;
                    e.target.value = val;
                }
            });
        }

        // Lógica Maestro-Detalle para Guías (AJAX Fetch)
        document.querySelectorAll('.btn-toggle-tx').forEach(btn => {
            btn.addEventListener('click', (e) => this.toggleGuideTransactions(e.currentTarget));
        });
    }

    async toggleGuideTransactions(button) {
        const guideId = button.dataset.guideId;
        const tr = button.closest('tr');
        const icon = button.querySelector('.material-symbols-outlined');

        // Si ya está abierto, lo cerramos
        let nextRow = tr.nextElementSibling;
        if (nextRow && nextRow.classList.contains('tx-details-row')) {
            nextRow.remove();
            icon.innerText = 'expand_more';
            icon.classList.remove('text-primary-600');
            tr.classList.remove('bg-gray-50', 'dark:bg-gray-800');
            return;
        }

        // Cambiar icono a loading
        icon.innerText = 'sync';
        icon.classList.add('animate-spin', 'text-primary-600');

        try {
            // El endpoint espera autenticación web clásica de sesión
            const response = await fetch(`/quoteswarehouse/dispatch-guide/${guideId}/transactions`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            });

            if (!response.ok) throw new Error('Network response was not ok');

            const html = await response.text();

            // Restaurar icono a expandido
            icon.classList.remove('animate-spin');
            icon.innerText = 'expand_less';
            tr.classList.add('bg-gray-50', 'dark:bg-gray-800');

            // Insertar nueva fila (Maestro - Detalle)
            const detailRow = document.createElement('tr');
            detailRow.className = 'tx-details-row bg-gray-50/20 dark:bg-gray-800/20';
            // Colspan 7 (Ancho total de la tabla de guías)
            detailRow.innerHTML = `<td colspan="7" class="p-0 border-t border-gray-100 dark:border-gray-800">${html}</td>`;
            tr.insertAdjacentElement('afterend', detailRow);

            // Importante: Volver a enlazar el comportamiento de redimensionado 
            // a la nueva sub-tabla inyectada que contiene las clases `.col-resizer`
            this.bindColumnResizers();

        } catch (e) {
            console.error('Error fetching transactions:', e);
            icon.classList.remove('animate-spin', 'text-primary-600');
            icon.innerText = 'error';
            icon.classList.add('text-red-500');
        }
    }

    bindColumnResizers() {
        const resizers = document.querySelectorAll('.col-resizer');
        let currentResizer = null;
        let startX = 0;
        let startWidth = 0;
        let col = null;

        const onMouseMove = (e) => {
            if (!currentResizer) return;
            const diff = e.pageX - startX;
            col.style.width = Math.max(30, startWidth + diff) + 'px';
            col.style.minWidth = col.style.width;
            col.style.maxWidth = col.style.width;
        };

        const onMouseUp = () => {
            if (currentResizer) {
                currentResizer.classList.remove('bg-blue-600');
                currentResizer.classList.add('hover:bg-blue-500');
                document.body.style.cursor = '';
                document.body.style.userSelect = '';
                currentResizer = null;
                document.removeEventListener('mousemove', onMouseMove);
                document.removeEventListener('mouseup', onMouseUp);
            }
        };

        resizers.forEach(resizer => {
            resizer.addEventListener('mousedown', (e) => {
                currentResizer = e.target;
                currentResizer.classList.remove('hover:bg-blue-500');
                currentResizer.classList.add('bg-blue-600');
                col = currentResizer.closest('th');
                startX = e.pageX;
                startWidth = col.offsetWidth;
                document.body.style.cursor = 'col-resize';
                document.body.style.userSelect = 'none';

                document.addEventListener('mousemove', onMouseMove);
                document.addEventListener('mouseup', onMouseUp);
            });
        });
    }

    fillAll() {
        this.items.forEach((item, idx) => {
            const f = item.solicitado - item.entregado;
            item.despachar = f > 0 ? f : 0;
            const input = document.querySelector(`.qty-input[data-index="${idx}"]`);
            if (input) input.value = item.despachar;
        });
        this.updateProgress();
    }

    updateProgress() {
        let totalSolicitado = this.items.reduce((acc, item) => acc + item.solicitado, 0);
        let totalListo = this.items.reduce((acc, item) => acc + Math.min(item.entregado + (parseInt(item.despachar) || 0), item.solicitado), 0);
        let progress = totalSolicitado === 0 ? 0 : Math.round((totalListo / totalSolicitado) * 100);

        if (this.progressText) this.progressText.innerText = progress + '%';
        if (this.progressBar) this.progressBar.style.width = progress + '%';
    }

    openLocModal(target) {
        this.newLocTarget = target;
        this.inputLocName.value = '';
        this.inputLocDesc.value = '';
        this.modalLoc.classList.remove('hidden');
        setTimeout(() => this.inputLocName.focus(), 100);

        const closeOnEscape = (e) => {
            if (e.key === 'Escape') {
                this.closeLocModal();
                document.removeEventListener('keydown', closeOnEscape);
            }
        };
        document.addEventListener('keydown', closeOnEscape);
    }

    closeLocModal() {
        this.modalLoc.classList.add('hidden');
    }

    async createLocation() {
        const name = this.inputLocName.value.trim();
        const desc = this.inputLocDesc.value.trim();
        if (!name) {
            Swal.fire({ icon: 'warning', title: 'Requerido', text: 'Ingrese un nombre', confirmButtonColor: '#059669' });
            return;
        }

        this.btnSaveLoc.disabled = true;
        this.btnSaveLoc.innerHTML = `<span class="material-symbols-outlined text-[16px] animate-spin">sync</span> <span>Guardando...</span>`;

        try {
            const response = await fetch(this.config.routes.storeLocation, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ name: name, description: desc })
            });
            const data = await response.json();
            if (data.success) {
                this.locations.push({ id: data.data.id, name: data.data.name });

                // Actualizar los selects del Modal de Guía
                document.querySelectorAll('#guide-origin, #guide-destination').forEach(sel => {
                    const opt = document.createElement('option');
                    opt.value = data.data.id;
                    opt.text = data.data.name;
                    sel.add(opt);
                });

                if (this.newLocTarget === 'origin' && this.inputGuideOrigin) {
                    this.inputGuideOrigin.value = data.data.id;
                } else if (this.newLocTarget === 'destination' && this.inputGuideDest) {
                    this.inputGuideDest.value = data.data.id;
                }

                this.closeLocModal();
                Swal.fire({ icon: 'success', title: 'Creado', text: data.message, timer: 1500, showConfirmButton: false });
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message });
            }
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Error de red' });
        } finally {
            this.btnSaveLoc.disabled = false;
            this.btnSaveLoc.innerHTML = `<span class="material-symbols-outlined text-[16px]">save</span> <span>Guardar</span>`;
        }
    }

    async openHistoryModal(reqId, name) {
        this.histTitle.innerText = name;
        this.modalHist.classList.remove('hidden');
        this.histLoader.classList.remove('hidden');
        this.histContent.innerHTML = '';

        try {
            const response = await fetch(`${this.config.routes.history}/${reqId}`);
            const data = await response.json();
            if (data.success) {
                this.renderHistory(data.data);
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                this.closeHistoryModal();
            }
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Error de red al cargar el historial' });
            this.closeHistoryModal();
        } finally {
            this.histLoader.classList.add('hidden');
        }

        const closeOnEscape = (e) => {
            if (e.key === 'Escape') {
                this.closeHistoryModal();
                document.removeEventListener('keydown', closeOnEscape);
            }
        };
        document.addEventListener('keydown', closeOnEscape);
    }

    closeHistoryModal() {
        this.modalHist.classList.add('hidden');
    }

    renderHistory(transactions) {
        if (transactions.length === 0) {
            this.histContent.innerHTML = `
                <div class="text-center py-8">
                    <span class="material-symbols-outlined text-4xl text-gray-300 dark:text-gray-600 mb-2">inbox</span>
                    <p class="text-sm text-gray-500 font-medium">No hay despachos registrados para este ítem.</p>
                </div>`;
            return;
        }

        let html = `<div class="relative pl-4 space-y-6 before:absolute before:inset-0 before:ml-[23px] before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-gray-200 dark:before:via-gray-700 before:to-transparent">`;

        transactions.forEach(tx => {
            html += `
            <div class="relative flex items-start gap-4">
                <div class="absolute left-[-1.3rem] mt-1.5 w-3 h-3 rounded-full bg-blue-500 ring-4 ring-white dark:ring-gray-900 shrink-0 shadow-sm z-10 text-[0px]">.</div>
                <div class="flex-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-3 shadow-sm hover:border-blue-300 dark:hover:border-blue-700 transition-colors">
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[14px] text-gray-400">person</span>
                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300">${tx.employee}</span>
                        </div>
                        <div class="text-[10px] font-medium text-gray-400 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[12px]">schedule</span>
                            <span>${tx.date}</span>
                        </div>
                    </div>
                    <div class="my-2 p-2 bg-blue-50 dark:bg-blue-900/20 rounded border border-blue-100 dark:border-blue-900/50 flex align-center gap-2">
                        <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-[18px]">outbound</span>
                        <span class="text-xs text-gray-700 dark:text-gray-300">
                            Despachó <strong class="text-blue-700 dark:text-blue-400 text-sm">${tx.quantity}</strong> unidades
                        </span>
                    </div>
                    ${tx.comment && tx.comment !== '-' ? `
                    <div class="mt-2 pt-2 border-t border-gray-100 dark:border-gray-700 text-[10px] flex gap-1.5 items-start">
                        <span class="material-symbols-outlined text-[12px] text-amber-500 shrink-0">edit_note</span>
                        <span class="text-gray-600 dark:text-gray-400 italic">${tx.comment}</span>
                    </div>` : ''}
                    ${tx.tool_unit ? `
                    <div class="mt-2 pt-2 border-t border-gray-100 dark:border-gray-700 text-[10px] flex gap-1.5 items-center">
                        <span class="material-symbols-outlined text-[14px] text-emerald-500 shrink-0">build</span>
                        <span class="font-bold text-gray-700 dark:text-gray-300">Unidad despachada: </span>
                        <span class="text-emerald-700 dark:text-emerald-400 font-bold bg-emerald-50 dark:bg-emerald-900/30 px-2 py-0.5 rounded border border-emerald-100 dark:border-emerald-800">${tx.tool_unit}</span>
                    </div>` : ''}
                </div>
            </div>`;
        });
        html += `</div>`;
        this.histContent.innerHTML = html;
    }

    openTransactionModal(idx, name, description) {
        const item = this.items[idx];
        if (!item) return;
        this.txIndex.value = idx;
        this.txProductName.innerText = name;
        // Actualizar el div con la descripción (ProjectRequirement.name)
        const descDiv = this.txDescription.querySelector('p') || this.txDescription;
        if (this.txDescription.querySelector('p')) {
            this.txDescription.querySelector('p').textContent = description || '-';
        } else {
            this.txDescription.textContent = description || '-';
        }
        this.txQty.value = item.despachar > 0 ? item.despachar : '';
        this.txQty.max = item.solicitado - item.entregado;
        if (this.txUnitPriceReference) this.txUnitPriceReference.value = item.unit_price || '';
        this.txIsExternal.checked = item.is_external_purchase || false;
        this.txPriceUnit.value = item.price_unit || '';
        this.txReceipt.value = item.receipt_number || '';
        this.txSupplier.value = item.supplier_name || '';
        this.txAddCost.value = item.additional_cost || '';
        this.txCostDesc.value = item.cost_description || '';
        this.txComment.value = item.comment || '';

        // Manejar unidades de herramienta
        if (item.is_tool && item.available_units && item.available_units.length > 0) {
            this.txToolUnitContainer.classList.remove('hidden');
            this.txToolUnitSelect.innerHTML = '<option value="">Seleccione unidad...</option>';
            item.available_units.forEach(unit => {
                const opt = new Option(unit.internal_code || 'S/C', unit.id);
                if (item.tool_unit_id == unit.id) opt.selected = true;
                this.txToolUnitSelect.add(opt);
            });
        } else {
            this.txToolUnitContainer.classList.add('hidden');
        }

        this.modalTx.classList.remove('hidden');
        setTimeout(() => this.txQty.focus(), 100);

        const closeOnEscape = (e) => {
            if (e.key === 'Escape') {
                this.closeTransactionModal();
                document.removeEventListener('keydown', closeOnEscape);
            }
        };
        document.addEventListener('keydown', closeOnEscape);
    }

    closeTransactionModal() {
        this.modalTx.classList.add('hidden');
    }

    saveTransaction() {
        const idx = this.txIndex.value;
        const item = this.items[idx];
        if (!item) return;

        const qty = parseFloat(this.txQty.value) || 0;
        const max = item.solicitado - item.entregado;

        if (qty < 0) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'La cantidad no puede ser negativa' });
            return;
        }
        if (qty > max) {
            Swal.fire({ icon: 'warning', title: 'Exceso', text: `La cantidad máxima permitida es ${max}` });
            this.txQty.value = max;
            return;
        }

        // Guardar datos en el objeto item
        item.despachar = qty;
        // NOTA: El employee_id SIEMPRE se asigna desde el servidor (Auth::id())
        // NO se envía desde el cliente por razones de seguridad
        item.is_external_purchase = this.txIsExternal.checked;
        item.price_unit = parseFloat(this.txPriceUnit.value) || null;
        item.receipt_number = this.txReceipt.value.trim();
        item.supplier_name = this.txSupplier.value.trim();
        item.additional_cost = parseFloat(this.txAddCost.value) || 0;
        item.cost_description = this.txCostDesc.value.trim();
        item.comment = this.txComment.value.trim();
        item.tool_unit_id = this.txToolUnitSelect.value || null;

        // Sincronizar con la tabla (UI)
        const rowInputs = {
            qty: document.querySelector(`.qty-input[data-index="${idx}"]`),
            external: document.querySelector(`.is-external-checkbox[data-index="${idx}"]`),
            price: document.querySelector(`.price-unit-input[data-index="${idx}"]`),
            receipt: document.querySelector(`.receipt-input[data-index="${idx}"]`),
            supplier: document.querySelector(`.supplier-input[data-index="${idx}"]`),
            comment: document.querySelector(`.comment-input[data-index="${idx}"]`),
            tool: document.querySelector(`.tool-unit-select[data-index="${idx}"]`)
        };

        if (rowInputs.qty) rowInputs.qty.value = qty;
        if (rowInputs.external) rowInputs.external.checked = item.is_external_purchase;
        if (rowInputs.price) rowInputs.price.value = item.price_unit || '';
        if (rowInputs.receipt) rowInputs.receipt.value = item.receipt_number;
        if (rowInputs.supplier) rowInputs.supplier.value = item.supplier_name;
        if (rowInputs.comment) rowInputs.comment.value = item.comment;
        if (rowInputs.tool && item.tool_unit_id) rowInputs.tool.value = item.tool_unit_id;

        this.updateProgress();
        this.closeTransactionModal();

        // Mostrar un pequeño toast o feedback visual
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });
        Toast.fire({ icon: 'success', title: 'Datos aplicados a la fila' });
    }

    openGuideModal() {
        const hasDispatches = this.items.some(i => (parseInt(i.despachar) || 0) > 0);

        if (!hasDispatches) {
            this.submitForm();
            return;
        }

        if (this.inputGuideOrigin && this.inputGuideOrigin.options.length <= 1) {
            this.locations.forEach(loc => {
                const optO = new Option(loc.name, loc.id);
                this.inputGuideOrigin.add(optO);
                const optD = new Option(loc.name, loc.id);
                this.inputGuideDest.add(optD);
            });
        }

        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        if (this.inputGuideDate) this.inputGuideDate.value = now.toISOString().slice(0, 16);

        if (this.inputGuideNumber) this.inputGuideNumber.value = '';

        this.modalGuide.classList.remove('hidden');
        if (this.inputGuideNumber) setTimeout(() => this.inputGuideNumber.focus(), 100);

        const closeOnEscape = (e) => {
            if (e.key === 'Escape') {
                this.closeGuideModal();
                document.removeEventListener('keydown', closeOnEscape);
            }
        };
        document.addEventListener('keydown', closeOnEscape);
    }

    closeGuideModal() {
        this.modalGuide.classList.add('hidden');
    }

    async submitForm() {
        const details = this.items.filter(i => (parseInt(i.despachar) || 0) > 0).map(i => ({
            project_requirement_id: i.project_requirement_id,
            a_despachar: parseInt(i.despachar) || 0,
            quantity: parseInt(i.solicitado) || 0,
            is_external_purchase: i.is_external_purchase || false,
            price_unit: i.price_unit || null,
            supplier_name: i.supplier_name || null,
            receipt_number: i.receipt_number || null,
            comment: i.comment || '',
            additional_cost: i.additional_cost || 0,
            cost_description: i.cost_description || '',
            tool_unit_id: i.tool_unit_id || null,
            employee_id: i.employee_id || null
        }));

        let totalSolicitado = this.items.reduce((acc, item) => acc + item.solicitado, 0);
        let totalListo = this.items.reduce((acc, item) => acc + Math.min(item.entregado + (parseInt(item.despachar) || 0), item.solicitado), 0);
        let progreso_total = totalSolicitado === 0 ? 0 : Math.round((totalListo / totalSolicitado) * 100);

        const payload = {
            quote_warehouse_id: this.config.quoteWarehouseId,
            observations: this.inputObs ? this.inputObs.value : '',
            progreso_total: progreso_total,
            guide_number: this.inputGuideNumber ? this.inputGuideNumber.value.trim() : '',
            global_origin_id: this.inputGuideOrigin ? this.inputGuideOrigin.value : null,
            global_destination_id: this.inputGuideDest ? this.inputGuideDest.value : null,
            transfer_date: this.inputGuideDate ? this.inputGuideDate.value : null,
            details
        };

        try {
            if (this.btnConfirmDispatch) {
                this.btnConfirmDispatch.disabled = true;
                this.btnConfirmDispatch.innerHTML = `<span class="material-symbols-outlined text-[16px] animate-spin">sync</span> <span>Enviando...</span>`;
            } else if (this.btnSubmit) {
                this.btnSubmit.disabled = true;
                this.btnSubmit.innerHTML = `<span class="material-symbols-outlined text-[18px] animate-spin">sync</span> Registrando...`;
            }

            const r = await fetch(this.config.routes.store, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });
            const d = await r.json();
            if (d.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: d.message + (d.estadoMensaje ? `\n${d.estadoMensaje}` : ''),
                    confirmButtonColor: '#059669'
                }).then(() => location.reload());
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: d.message });
                this.resetSubmitButtons();
            }
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Error de red' });
            this.resetSubmitButtons();
        }
    }

    resetSubmitButtons() {
        if (this.btnConfirmDispatch) {
            this.btnConfirmDispatch.disabled = false;
            this.btnConfirmDispatch.innerHTML = `<span class="material-symbols-outlined text-[16px]">send</span> <span>Confirmar Despacho</span>`;
        }
        if (this.btnSubmit) {
            this.btnSubmit.disabled = false;
            this.btnSubmit.innerHTML = `<span class="material-symbols-outlined text-[18px]">send</span> Registrar Despacho`;
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Inicializar Tabs
    window.vanillaTabsManager = new VanillaTabsManager();

    // Inicializar Controlador del Warehouse
    if (window.quoteWarehouseConfig) {
        window.quoteWarehouseEditor = new QuoteWarehouseEditor(window.quoteWarehouseConfig);
    }
});
