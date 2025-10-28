/**
 * Módulo para manejar modales de confirmación de eliminación
 * Maneja modales Bootstrap y SweetAlert2 para confirmaciones
 */
import { AlertHandler } from './alert-handler.js';

export class ConfirmDeleteModal {
    constructor(options = {}) {
        this.options = {
            modalSelector: '#confirmDeleteModal',
            formSelector: '#deleteForm',
            itemNameSelector: '#deleteItemName',
            useSweetAlert: true,
            ...options
        };
        this.alertHandler = new AlertHandler();
        this.init();
    }

    init() {
        // Hacer las funciones disponibles globalmente
        window.showDeleteModal = (itemName, deleteUrl) => this.showModal(itemName, deleteUrl);
        window.confirmDelete = (itemName, deleteUrl, formElement = null) => this.confirmDelete(itemName, deleteUrl, formElement);
    }

    showModal(itemName, deleteUrl) {
        const modal = document.querySelector(this.options.modalSelector);
        const form = document.querySelector(this.options.formSelector);
        const itemNameElement = document.querySelector(this.options.itemNameSelector);
        
        if (itemNameElement) {
            itemNameElement.textContent = itemName;
        }
        
        if (form) {
            form.action = deleteUrl;
        }
        
        if (modal && typeof $(modal).modal === 'function') {
            $(modal).modal('show');
        }
    }

    confirmDelete(itemName, deleteUrl, formElement = null) {
        if (this.options.useSweetAlert) {
            return this.showSweetAlertConfirmation(itemName, deleteUrl, formElement);
        } else {
            return this.showModal(itemName, deleteUrl);
        }
    }

    showSweetAlertConfirmation(itemName, deleteUrl, formElement = null) {
        return this.alertHandler.showCustomAlert({
            title: '¿Eliminar elemento?',
            html: `
                <div class="text-center">
                    <i class="fas fa-trash-alt text-danger mb-3" style="font-size: 3rem;"></i>
                    <p class="mb-2">¿Está seguro de eliminar <strong class="text-danger">${itemName}</strong>?</p>
                    <small class="text-muted">
                        <i class="fas fa-exclamation-triangle"></i>
                        Esta acción no se puede deshacer.
                    </small>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash"></i> Sí, eliminar',
            cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
            focusConfirm: false,
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                this.executeDelete(deleteUrl, formElement);
            }
        });
    }

    executeDelete(deleteUrl, formElement = null) {
        if (formElement) {
            // Si se proporciona un elemento de formulario, enviarlo directamente
            formElement.submit();
        } else {
            // Crear un formulario dinámico para la eliminación
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = deleteUrl;
            
            // Agregar token CSRF
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = this.getCsrfToken();
            form.appendChild(csrfToken);
            
            // Agregar método DELETE
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(methodField);
            
            // Agregar formulario al DOM y enviarlo
            document.body.appendChild(form);
            form.submit();
        }
    }

    getCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        return token ? token.getAttribute('content') : '';
    }

    // Método para confirmar eliminación con opciones personalizadas
    confirmDeleteWithOptions(itemName, deleteUrl, options = {}) {
        const defaultOptions = {
            title: '¿Eliminar elemento?',
            message: `¿Está seguro de eliminar <strong>${itemName}</strong>?`,
            confirmText: 'Sí, eliminar',
            cancelText: 'Cancelar',
            icon: 'warning',
            confirmColor: '#dc3545',
            cancelColor: '#6c757d'
        };

        const finalOptions = { ...defaultOptions, ...options };

        return this.alertHandler.showCustomAlert({
            title: finalOptions.title,
            html: `
                <div class="text-center">
                    <i class="fas fa-trash-alt text-danger mb-3" style="font-size: 3rem;"></i>
                    <p class="mb-2">${finalOptions.message}</p>
                    <small class="text-muted">
                        <i class="fas fa-exclamation-triangle"></i>
                        Esta acción no se puede deshacer.
                    </small>
                </div>
            `,
            icon: finalOptions.icon,
            showCancelButton: true,
            confirmButtonColor: finalOptions.confirmColor,
            cancelButtonColor: finalOptions.cancelColor,
            confirmButtonText: `<i class="fas fa-trash"></i> ${finalOptions.confirmText}`,
            cancelButtonText: `<i class="fas fa-times"></i> ${finalOptions.cancelText}`,
            focusConfirm: false,
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                this.executeDelete(deleteUrl);
            }
        });
    }

    // Método para confirmar eliminación múltiple
    confirmBulkDelete(itemNames, deleteUrl) {
        const count = itemNames.length;
        const message = count === 1 
            ? `¿Está seguro de eliminar <strong>${itemNames[0]}</strong>?`
            : `¿Está seguro de eliminar <strong>${count} elementos</strong> seleccionados?`;

        return this.alertHandler.showCustomAlert({
            title: '¿Eliminar elementos?',
            html: `
                <div class="text-center">
                    <i class="fas fa-trash-alt text-danger mb-3" style="font-size: 3rem;"></i>
                    <p class="mb-2">${message}</p>
                    <small class="text-muted">
                        <i class="fas fa-exclamation-triangle"></i>
                        Esta acción no se puede deshacer.
                    </small>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: `<i class="fas fa-trash"></i> Sí, eliminar`,
            cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
            focusConfirm: false,
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                this.executeDelete(deleteUrl);
            }
        });
    }
}

// Inicialización automática si el DOM está listo
document.addEventListener('DOMContentLoaded', () => {
    // Solo inicializar si existe el modal de confirmación
    if (document.querySelector('#confirmDeleteModal')) {
        new ConfirmDeleteModal();
    }
});
