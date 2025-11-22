/**
 * Módulo para manejar alertas y notificaciones
 */
export class AlertHandler {
    constructor(options = {}) {
        this.options = {
            autoHide: true,
            hideDelay: 5000,
            alertSelector: '.alert',
            Swal: null,
            ...options
        };

        // Si no se proporciona Swal, intentar importarlo
        if (!this.options.Swal) {
            import('sweetalert2').then(Swal => {
                this.options.Swal = Swal.default || Swal;
            }).catch(() => {
                console.warn('SweetAlert2 no está disponible');
            });
        }

        this.init();
    }
    
    init() {
        this.initAutoHide();
        this.initDismissButtons();
    }
    
    /**
     * Inicializa el auto-ocultamiento de alertas
     */
    initAutoHide() {
        if (this.options.autoHide) {
            setTimeout(() => {
                $(this.options.alertSelector).fadeOut('slow');
            }, this.options.hideDelay);
        }
    }
    
    /**
     * Inicializa los botones de cerrar alertas
     */
    initDismissButtons() {
        $(document).on('click', '.alert .close', (e) => {
            e.preventDefault();
            $(e.target).closest('.alert').fadeOut('slow');
        });
    }
    
    /**
     * Muestra una alerta de éxito
     */
    showSuccess(message, title = 'Éxito') {
        if (this.options.Swal) {
            this.options.Swal.fire({
                title: title,
                text: message,
                icon: 'success',
                confirmButtonText: 'Entendido'
            });
        }
    }
    
    /**
     * Muestra una alerta de error
     */
    showError(message, title = 'Error') {
        if (this.options.Swal) {
            this.options.Swal.fire({
                title: title,
                text: message,
                icon: 'error',
                confirmButtonText: 'Entendido'
            });
        }
    }

    /**
     * Muestra una alerta de advertencia
     */
    showWarning(message, title = 'Advertencia') {
        if (this.options.Swal) {
            this.options.Swal.fire({
                title: title,
                text: message,
                icon: 'warning',
                confirmButtonText: 'Entendido'
            });
        }
    }

    /**
     * Muestra una alerta de información
     */
    showInfo(message, title = 'Información') {
        if (this.options.Swal) {
            this.options.Swal.fire({
                title: title,
                text: message,
                icon: 'info',
                confirmButtonText: 'Entendido'
            });
        }
    }

    /**
     * Muestra una confirmación
     */
    showConfirmation(message, title = 'Confirmar') {
        if (this.options.Swal) {
            return this.options.Swal.fire({
                title: title,
                text: message,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí',
                cancelButtonText: 'No'
            });
        }
        return Promise.resolve({ isConfirmed: false });
    }
    
    /**
     * Muestra un toast de notificación
     */
    showToast(message, type = 'success') {
        if (this.options.Swal) {
            const Toast = this.options.Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', this.options.Swal.stopTimer);
                    toast.addEventListener('mouseleave', this.options.Swal.resumeTimer);
                }
            });

            Toast.fire({
                icon: type,
                title: message
            });
        }
    }
    
    /**
     * Oculta todas las alertas
     */
    hideAllAlerts() {
        $(this.options.alertSelector).fadeOut('slow');
    }
    
    /**
     * Muestra una alerta personalizada
     */
    showCustomAlert(options) {
        if (this.options.Swal) {
            const defaultOptions = {
                title: 'Alerta',
                text: '',
                icon: 'info',
                confirmButtonText: 'Entendido'
            };

            const finalOptions = { ...defaultOptions, ...options };

            return this.options.Swal.fire(finalOptions);
        }
        return Promise.resolve({ isConfirmed: false });
    }

    /**
     * Muestra confirmación de eliminación con diseño personalizado
     */
    showDeleteConfirmation(itemName) {
        if (!this.options.Swal && typeof Swal !== 'undefined') {
            this.options.Swal = Swal;
        }
        if (!this.options.Swal) {
            const confirmMessage = '¿Está seguro de eliminar ' + itemName + '? ' +
                'Esta acción no se puede deshacer.';
            return Promise.resolve({
                isConfirmed: confirm(confirmMessage)
            });
        }
        return this.options.Swal.fire({
            title: '¿Eliminar importación?',
            html: `
                <div class="text-center">
                    <i class="fas fa-trash-alt text-danger mb-3" style="font-size: 3rem;"></i>
                    <p class="mb-2">
                        ¿Está seguro de eliminar <strong class="text-danger">${itemName}</strong>?
                    </p>
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
        });
    }

    /**
     * Muestra errores de validación formateados
     */
    showValidationErrors(errors) {
        if (!this.options.Swal && typeof Swal !== 'undefined') {
            this.options.Swal = Swal;
        }
        if (!this.options.Swal) {
            let errorText = 'Errores de validación:\n';
            for (let field in errors) {
                if (errors.hasOwnProperty(field)) {
                    errors[field].forEach(error => {
                        errorText += '- ' + error + '\n';
                    });
                }
            }
            alert(errorText);
            return;
        }
        let errorsHtml = '<ul class="text-left mb-0">';
        for (let field in errors) {
            if (errors.hasOwnProperty(field)) {
                errors[field].forEach(error => {
                    errorsHtml += '<li>' + error + '</li>';
                });
            }
        }
        errorsHtml += '</ul>';

        return this.options.Swal.fire({
            icon: 'error',
            title: 'Errores de validación',
            html: errorsHtml,
            confirmButtonText: 'Entendido'
        });
    }

    /**
     * Muestra info con timer (sin botón de confirmación)
     */
    showInfoWithTimer(title, message, timer = 2000) {
        if (!this.options.Swal && typeof Swal !== 'undefined') {
            this.options.Swal = Swal;
        }
        if (!this.options.Swal) return;
        return this.options.Swal.fire({
            icon: 'info',
            title: title,
            text: message,
            timer: timer,
            showConfirmButton: false
        });
    }

    /**
     * Muestra éxito con timer (sin botón de confirmación)
     */
    showSuccessWithTimer(title, message, timer = 2500) {
        if (!this.options.Swal && typeof Swal !== 'undefined') {
            this.options.Swal = Swal;
        }
        if (!this.options.Swal) return;
        return this.options.Swal.fire({
            icon: 'success',
            title: title,
            text: message,
            timer: timer,
            showConfirmButton: false
        });
    }
}

// Instancia global para uso directo sin necesidad de instanciar
let globalAlertHandler = null;

/**
 * Obtiene o crea la instancia global de AlertHandler
 */
export function getGlobalAlertHandler() {
    if (!globalAlertHandler) {
        globalAlertHandler = new AlertHandler({
            Swal: typeof Swal !== 'undefined' ? Swal : null
        });
    }
    return globalAlertHandler;
}
