/**
 * Módulo para manejar formularios y validaciones
 */
export class FormHandler {
    constructor(formSelector, options = {}) {
        this.form = $(formSelector);
        this.options = {
            validateOnSubmit: true,
            showLoadingOnSubmit: true,
            preventDoubleSubmit: true,
            ...options
        };
        
        this.init();
    }
    
    init() {
        this.initFormSubmission();
        this.initValidation();
        this.initPreventDoubleSubmit();
    }
    
    /**
     * Inicializa el manejo de envío de formularios
     */
    initFormSubmission() {
        this.form.on('submit', (e) => {
            if (this.options.preventDoubleSubmit) {
                this.preventDoubleSubmit(e);
            }
            
            if (this.options.showLoadingOnSubmit) {
                this.showLoadingState();
            }
        });
    }
    
    /**
     * Previene el doble envío de formularios
     */
    preventDoubleSubmit(e) {
        const submitButton = this.form.find('button[type="submit"], input[type="submit"]');
        
        if (submitButton.prop('disabled')) {
            e.preventDefault();
            return false;
        }
        
        submitButton.prop('disabled', true);
        
        // Re-habilitar después de 3 segundos como fallback
        setTimeout(() => {
            submitButton.prop('disabled', false);
        }, 3000);
    }
    
    /**
     * Muestra el estado de carga en el formulario
     */
    showLoadingState() {
        const submitButton = this.form.find('button[type="submit"], input[type="submit"]');
        const originalText = submitButton.html();
        
        submitButton.html('<i class="fas fa-spinner fa-spin"></i> Procesando...');
        
        // Restaurar texto original después de 5 segundos como fallback
        setTimeout(() => {
            submitButton.html(originalText);
        }, 5000);
    }
    
    /**
     * Inicializa la validación del formulario
     */
    initValidation() {
        if (this.options.validateOnSubmit) {
            this.form.on('submit', (e) => {
                if (!this.validateForm()) {
                    e.preventDefault();
                    return false;
                }
            });
        }
    }
    
    /**
     * Valida el formulario
     */
    validateForm() {
        let isValid = true;
        const errors = [];
        
        // Validar campos requeridos
        this.form.find('[required]').each((index, field) => {
            const $field = $(field);
            const value = $field.val().trim();
            
            if (!value) {
                isValid = false;
                errors.push(`El campo ${$field.attr('name') || $field.attr('id')} es requerido`);
                $field.addClass('is-invalid');
            } else {
                $field.removeClass('is-invalid');
            }
        });
        
        // Validar emails
        this.form.find('input[type="email"]').each((index, field) => {
            const $field = $(field);
            const value = $field.val().trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (value && !emailRegex.test(value)) {
                isValid = false;
                errors.push('El formato del email no es válido');
                $field.addClass('is-invalid');
            } else {
                $field.removeClass('is-invalid');
            }
        });
        
        // Mostrar errores si los hay
        if (!isValid) {
            this.showValidationErrors(errors);
        }
        
        return isValid;
    }
    
    /**
     * Muestra errores de validación
     */
    showValidationErrors(errors) {
        const errorMessage = errors.join('<br>');
        
        Swal.fire({
            title: 'Errores de Validación',
            html: errorMessage,
            icon: 'error',
            confirmButtonText: 'Entendido'
        });
    }
    
    /**
     * Resetea el formulario
     */
    resetForm() {
        this.form[0].reset();
        this.form.find('.is-invalid').removeClass('is-invalid');
    }
    
    /**
     * Envía el formulario programáticamente
     */
    submitForm() {
        this.form.submit();
    }
}
