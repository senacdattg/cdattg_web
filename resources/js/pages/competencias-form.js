/**
 * Script específico para formularios de competencias (create/edit)
 */
import { AlertHandler } from '../modules/alert-handler.js';

document.addEventListener('DOMContentLoaded', () => {
    // Inicializar manejador de alertas
    const alertHandler = new AlertHandler({
        autoHide: true,
        hideDelay: 5000,
        alertSelector: '.alert'
    });
    
    // Inicializar Select2 si existe
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

    // Validación de fechas
    $('#fecha_inicio, #fecha_fin').on('change', function() {
        const fechaInicio = $('#fecha_inicio').val();
        const fechaFin = $('#fecha_fin').val();

        if (fechaInicio && fechaFin) {
            const inicio = new Date(fechaInicio);
            const fin = new Date(fechaFin);

            if (inicio >= fin) {
                alertHandler.showError('La fecha de inicio debe ser anterior a la fecha de fin.');
                $('#fecha_fin').val('');
                return;
            }

            // Calcular duración en días
            const diffTime = Math.abs(fin - inicio);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            // Actualizar campo de duración si existe
            const duracionField = $('#duracion');
            if (duracionField.length) {
                duracionField.val(diffDays);
            }
        }
    });

    // Validación de duración
    $('#duracion').on('input', function() {
        const duracion = parseInt($(this).val());
        if (duracion && duracion < 1) {
            alertHandler.showError('La duración debe ser mayor a 0.');
            $(this).val('');
        }
    });

    // Validación de formulario
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(event) {
            const fechaInicio = $('#fecha_inicio').val();
            const fechaFin = $('#fecha_fin').val();
            const duracion = $('#duracion').val();

            // Validar que al menos una de las opciones esté completa
            if (!fechaInicio && !fechaFin && !duracion) {
                event.preventDefault();
                alertHandler.showError('Debe especificar al menos la duración o las fechas de inicio y fin.');
                return;
            }

            // Si se especifican fechas, validar que sean consistentes
            if (fechaInicio && fechaFin) {
                const inicio = new Date(fechaInicio);
                const fin = new Date(fechaFin);
                
                if (inicio >= fin) {
                    event.preventDefault();
                    alertHandler.showError('La fecha de inicio debe ser anterior a la fecha de fin.');
                    return;
                }
            }
        });
    }

    // Auto-focus en el primer campo
    const firstInput = document.querySelector('input[type="text"], input[type="number"], select');
    if (firstInput && !firstInput.value) {
        firstInput.focus();
    }

    // Limpiar formulario después de envío exitoso
    if (window.location.search.includes('success')) {
        form.reset();
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('.select2').val(null).trigger('change');
        }
    }

    console.log('Formulario de competencias inicializado correctamente');
});
