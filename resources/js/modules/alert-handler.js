/**
 * Módulo para manejar alertas y notificaciones
 */
export class AlertHandler {
    constructor(options = {}) {
        this.options = {
            autoHide: true,
            hideDelay: 5000,
            alertSelector: '.alert',
            ...options
        };
        
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
        Swal.fire({
            title: title,
            text: message,
            icon: 'success',
            confirmButtonText: 'Entendido'
        });
    }
    
    /**
     * Muestra una alerta de error
     */
    showError(message, title = 'Error') {
        Swal.fire({
            title: title,
            text: message,
            icon: 'error',
            confirmButtonText: 'Entendido'
        });
    }
    
    /**
     * Muestra una alerta de advertencia
     */
    showWarning(message, title = 'Advertencia') {
        Swal.fire({
            title: title,
            text: message,
            icon: 'warning',
            confirmButtonText: 'Entendido'
        });
    }
    
    /**
     * Muestra una alerta de información
     */
    showInfo(message, title = 'Información') {
        Swal.fire({
            title: title,
            text: message,
            icon: 'info',
            confirmButtonText: 'Entendido'
        });
    }
    
    /**
     * Muestra una confirmación
     */
    showConfirmation(message, title = 'Confirmar') {
        return Swal.fire({
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
    
    /**
     * Muestra un toast de notificación
     */
    showToast(message, type = 'success') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
        
        Toast.fire({
            icon: type,
            title: message
        });
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
        const defaultOptions = {
            title: 'Alerta',
            text: '',
            icon: 'info',
            confirmButtonText: 'Entendido'
        };
        
        const finalOptions = { ...defaultOptions, ...options };
        
        return Swal.fire(finalOptions);
    }
}
