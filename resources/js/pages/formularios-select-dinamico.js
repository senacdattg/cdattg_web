/**
 * Script genérico para formularios con select dinámico
 * Reutilizable en múltiples vistas de formularios
 */
import { SelectDinamicoHandler } from '../modules/select-dinamico.js';
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

    // Inicializar select dinámico
    const selectDinamico = new SelectDinamicoHandler({
        paisSelector: '#pais_id',
        departamentoSelector: '#departamento_id',
        municipioSelector: '#municipio_id',
        sedeSelector: '#sede_id',
        ambienteSelector: '#ambiente_id',
        programaSelector: '#programa_formacion_id'
    });

    // Validación de formulario
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(event) {
            // Validaciones específicas según el tipo de formulario
            const formId = form.id || form.className;
            
            if (formId.includes('persona') || formId.includes('sede') || formId.includes('piso') || 
                formId.includes('bloque') || formId.includes('ambiente')) {
                
                // Validar ubicación geográfica
                const paisId = $('#pais_id').val();
                const departamentoId = $('#departamento_id').val();
                const municipioId = $('#municipio_id').val();

                if (!paisId) {
                    event.preventDefault();
                    alertHandler.showError('Debe seleccionar un país.');
                    return;
                }

                if (!departamentoId) {
                    event.preventDefault();
                    alertHandler.showError('Debe seleccionar un departamento.');
                    return;
                }

                if (!municipioId) {
                    event.preventDefault();
                    alertHandler.showError('Debe seleccionar un municipio.');
                    return;
                }
            }

            if (formId.includes('ficha')) {
                // Validar ficha
                const programaId = $('#programa_formacion_id').val();
                const sedeId = $('#sede_id').val();
                const numeroFicha = $('#numero_ficha').val();

                if (!programaId) {
                    event.preventDefault();
                    alertHandler.showError('Debe seleccionar un programa de formación.');
                    return;
                }

                if (!sedeId) {
                    event.preventDefault();
                    alertHandler.showError('Debe seleccionar una sede.');
                    return;
                }

                if (!numeroFicha || numeroFicha.trim() === '') {
                    event.preventDefault();
                    alertHandler.showError('El número de ficha es requerido.');
                    return;
                }
            }

            // Validar fechas si existen
            const fechaInicio = $('#fecha_inicio').val();
            const fechaFin = $('#fecha_fin').val();
            
            if (fechaInicio && fechaFin) {
                if (new Date(fechaInicio) >= new Date(fechaFin)) {
                    event.preventDefault();
                    alertHandler.showError('La fecha de inicio debe ser anterior a la fecha de fin.');
                    return;
                }
            }

            // Validar campos requeridos básicos
            const requiredFields = form.querySelectorAll('[required]');
            let hasErrors = false;
            
            requiredFields.forEach(field => {
                if (!field.value || field.value.trim() === '') {
                    hasErrors = true;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (hasErrors) {
                event.preventDefault();
                alertHandler.showError('Por favor, complete todos los campos requeridos.');
                return;
            }
        });
    }

    // Auto-focus en el primer campo
    const firstInput = document.querySelector('input[type="text"], input[type="email"], input[type="number"], select');
    if (firstInput && !firstInput.value) {
        firstInput.focus();
    }

    // Limpiar formulario después de envío exitoso
    if (window.location.search.includes('success')) {
        if (form) {
            form.reset();
            selectDinamico.reset();
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('.select2').val(null).trigger('change');
            }
        }
    }

    // Establecer fecha mínima como hoy para fechas de inicio
    const fechaInicio = document.querySelector('#fecha_inicio');
    if (fechaInicio) {
        fechaInicio.setAttribute('min', new Date().toISOString().split('T')[0]);
    }

    // Validación de fechas en tiempo real
    $('#fecha_inicio, #fecha_fin').on('change', function() {
        const fechaInicio = $('#fecha_inicio').val();
        const fechaFin = $('#fecha_fin').val();
        
        if (fechaInicio && fechaFin) {
            if (new Date(fechaInicio) >= new Date(fechaFin)) {
                $('#fecha_fin')[0].setCustomValidity('La fecha de fin debe ser posterior a la fecha de inicio');
            } else {
                $('#fecha_fin')[0].setCustomValidity('');
            }
        }
    });

    // Confirmación de eliminación si es necesario
    window.confirmarEliminacion = function(nombre, url, tipo = 'elemento') {
        alertHandler.showCustomAlert({
            title: '¿Estás seguro?',
            text: `¿Deseas eliminar ${tipo} "${nombre}"? Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Crear formulario para enviar DELETE
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        });
    };
});
