/**
 * Módulo para manejar acciones en lote (bulk actions)
 * Maneja selección múltiple, contadores y acciones en lote
 */
export class BulkActionsHandler {
    constructor(options = {}) {
        this.options = {
            selectAllSelector: '#selectAll',
            rowCheckboxSelector: 'input[type="checkbox"][name="selected_items[]"]',
            bulkActionsContainerSelector: '.bulk-actions-container',
            selectedCountSelector: '.selected-count',
            bulkActionBtnSelector: '.bulk-action-btn',
            ...options
        };
        this.init();
    }

    init() {
        this.selectAllCheckbox = document.getElementById('selectAll');
        this.rowCheckboxes = document.querySelectorAll(this.options.rowCheckboxSelector);
        this.bulkActionsContainer = document.querySelector(this.options.bulkActionsContainerSelector);
        this.selectedCountSpan = document.querySelector(this.options.selectedCountSelector);
        this.bulkActionBtns = document.querySelectorAll(this.options.bulkActionBtnSelector);
        
        this.bindEvents();
        this.updateBulkActions();
    }

    bindEvents() {
        // Evento para el checkbox "Seleccionar todo"
        if (this.selectAllCheckbox) {
            this.selectAllCheckbox.addEventListener('change', () => {
                this.toggleAllRows(this.selectAllCheckbox.checked);
            });
        }

        // Eventos para los checkboxes de filas
        this.rowCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                this.updateBulkActions();
            });
        });

        // Eventos para botones de acción en lote
        this.bulkActionBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.handleBulkAction(e);
            });
        });
    }

    updateBulkActions() {
        const selectedCount = document.querySelectorAll(`${this.options.rowCheckboxSelector}:checked`).length;
        
        if (selectedCount > 0) {
            this.bulkActionsContainer?.classList.remove('d-none');
            if (this.selectedCountSpan) {
                this.selectedCountSpan.textContent = selectedCount;
            }
        } else {
            this.bulkActionsContainer?.classList.add('d-none');
        }
        
        // Actualizar estado del checkbox "Seleccionar todo"
        if (this.selectAllCheckbox) {
            if (selectedCount === 0) {
                this.selectAllCheckbox.indeterminate = false;
                this.selectAllCheckbox.checked = false;
            } else if (selectedCount === this.rowCheckboxes.length) {
                this.selectAllCheckbox.indeterminate = false;
                this.selectAllCheckbox.checked = true;
            } else {
                this.selectAllCheckbox.indeterminate = true;
            }
        }
    }

    toggleAllRows(checked) {
        this.rowCheckboxes.forEach(checkbox => {
            checkbox.checked = checked;
        });
        this.updateBulkActions();
    }

    handleBulkAction(event) {
        const selectedItems = document.querySelectorAll(`${this.options.rowCheckboxSelector}:checked`);
        
        if (selectedItems.length === 0) {
            event.preventDefault();
            alert('Por favor seleccione al menos un elemento para realizar la acción.');
            return;
        }

        const action = event.target.dataset.action;
        const actionName = event.target.textContent.trim();
        
        if (action === 'delete') {
            this.confirmBulkDelete(selectedItems, actionName);
        } else {
            this.executeBulkAction(selectedItems, action, actionName);
        }
    }

    confirmBulkDelete(selectedItems, actionName) {
        const count = selectedItems.length;
        const message = `¿Está seguro de que desea ${actionName.toLowerCase()} ${count} elemento(s) seleccionado(s)? Esta acción no se puede deshacer.`;
        
        if (confirm(message)) {
            this.executeBulkAction(selectedItems, 'delete', actionName);
        }
    }

    executeBulkAction(selectedItems, action, actionName) {
        // Crear formulario para envío
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = this.getBulkActionUrl(action);
        
        // Agregar token CSRF
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        form.appendChild(csrfToken);
        
        // Agregar método HTTP
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = this.getHttpMethod(action);
        form.appendChild(methodField);
        
        // Agregar elementos seleccionados
        selectedItems.forEach(checkbox => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_items[]';
            input.value = checkbox.value;
            form.appendChild(input);
        });
        
        // Agregar formulario al DOM y enviarlo
        document.body.appendChild(form);
        form.submit();
    }

    getBulkActionUrl(action) {
        // Obtener la URL base de la página actual
        const currentPath = window.location.pathname;
        const baseUrl = currentPath.replace(/\/[^\/]*$/, '');
        
        // Mapear acciones a URLs
        const actionUrls = {
            'delete': `${baseUrl}/bulk-delete`,
            'activate': `${baseUrl}/bulk-activate`,
            'deactivate': `${baseUrl}/bulk-deactivate`,
            'export': `${baseUrl}/bulk-export`
        };
        
        return actionUrls[action] || `${baseUrl}/bulk-action`;
    }

    getHttpMethod(action) {
        const methods = {
            'delete': 'DELETE',
            'activate': 'PATCH',
            'deactivate': 'PATCH',
            'export': 'GET'
        };
        
        return methods[action] || 'POST';
    }

    // Método para obtener elementos seleccionados
    getSelectedItems() {
        return Array.from(document.querySelectorAll(`${this.options.rowCheckboxSelector}:checked`))
            .map(checkbox => checkbox.value);
    }

    // Método para limpiar selección
    clearSelection() {
        this.rowCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        this.updateBulkActions();
    }

    // Método para seleccionar todos los elementos visibles
    selectAllVisible() {
        this.rowCheckboxes.forEach(checkbox => {
            if (checkbox.closest('tr').style.display !== 'none') {
                checkbox.checked = true;
            }
        });
        this.updateBulkActions();
    }
}

// Inicialización automática si el DOM está listo
document.addEventListener('DOMContentLoaded', () => {
    // Solo inicializar si existe el contenedor de bulk actions
    if (document.querySelector('.bulk-actions-container')) {
        new BulkActionsHandler();
    }
});
