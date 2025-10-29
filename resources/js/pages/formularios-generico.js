/**
 * Script genérico para formularios con Select2 y validaciones
 */
import { AlertHandler } from '../modules/alert-handler.js';

document.addEventListener('DOMContentLoaded', () => {
    // Inicializar manejador de alertas
    const alertHandler = new AlertHandler({
        autoHide: true,
        hideDelay: 5000,
        alertSelector: '.alert'
    });
    
    // Inicializar Select2 para todos los select
    if (typeof $ !== 'undefined' && $.fn.select2) {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Seleccione una opción',
            allowClear: true,
            language: {
                noResults: function() {
                    return "No se encontraron resultados";
                },
                searching: function() {
                    return "Buscando...";
                }
            }
        });
    }

    // Validación de formularios
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                
                // Mostrar alerta de error
                alertHandler.showError('Por favor, complete todos los campos requeridos correctamente.');
            }
            form.classList.add('was-validated');
        });
    });

    // Auto-focus en el primer campo de entrada
    const firstInput = document.querySelector('input[type="text"], input[type="email"], input[type="number"], select, textarea');
    if (firstInput && !firstInput.value) {
        firstInput.focus();
    }

    // Confirmación antes de enviar formularios de eliminación
    const deleteForms = document.querySelectorAll('form[action*="destroy"], form[action*="delete"]');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            const entityName = formData.get('entity_name') || 'este elemento';
            
            alertHandler.showCustomAlert({
                title: '¿Estás seguro?',
                text: `¿Deseas eliminar ${entityName}? Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Limpiar formulario después de envío exitoso
    if (window.location.search.includes('success')) {
        const form = document.querySelector('form[action*="store"], form[action*="update"]');
        if (form) {
            form.reset();
            form.classList.remove('was-validated');
            
            // Limpiar Select2 si existe
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('.select2').val(null).trigger('change');
            }
        }
    }

    // Manejo de archivos de carga
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                // Validar tamaño de archivo (5MB máximo)
                const maxSize = 5 * 1024 * 1024; // 5MB
                if (file.size > maxSize) {
                    alertHandler.showError('El archivo es demasiado grande. El tamaño máximo permitido es 5MB.');
                    this.value = '';
                    return;
                }
                
                // Validar tipo de archivo si es necesario
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
                if (allowedTypes.length > 0 && !allowedTypes.includes(file.type)) {
                    alertHandler.showError('Tipo de archivo no permitido. Solo se permiten: ' + allowedTypes.join(', '));
                    this.value = '';
                    return;
                }
            }
        });
    });

    console.log('Formulario genérico inicializado correctamente');
});
