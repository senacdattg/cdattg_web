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
                noResults: function () {
                    return "No se encontraron resultados";
                },
                searching: function () {
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

    // Exponer instancia global para integraciones dinámicas (ej. Talento Humano)
    window.selectDinamicoHandler = selectDinamico;

    // Validación de formulario
    const form = document.querySelector('form');
    if (form) {
        initFormValidation(form, alertHandler);
    }

    function initFormValidation(form, alertHandler) {
        const validators = [
            (ctx) => validateUbicacion(ctx, alertHandler),
            (ctx) => validateFicha(ctx, alertHandler),
            (ctx) => validateFechas(ctx, alertHandler),
            (ctx) => validateRequiredFields(ctx, alertHandler),
        ];

        form.addEventListener('submit', (event) => {
            const context = {
                event,
                form,
                formId: (form.id || form.className || '').toLowerCase(),
            };

            const isValid = validators.every((validator) => validator(context));

            if (!isValid) {
                event.preventDefault();
            }
        });
    }

    /**
     * Valida la selección de país/departamento/municipio según el formulario
     */
    function validateUbicacion({ formId }, alertHandler) {
        const secciones = ['persona', 'sede', 'piso', 'bloque', 'ambiente'];
        const requiereUbicacion = secciones.some((seccion) => formId.includes(seccion));

        if (!requiereUbicacion) {
            return true;
        }

        const paisId = $('#pais_id').val();
        const departamentoId = $('#departamento_id').val();
        const municipioId = $('#municipio_id').val();

        if (!paisId) {
            alertHandler.showError('Debe seleccionar un país.');
            return false;
        }

        if (!departamentoId) {
            alertHandler.showError('Debe seleccionar un departamento.');
            return false;
        }

        if (!municipioId) {
            alertHandler.showError('Debe seleccionar un municipio.');
            return false;
        }

        return true;
    }

    /**
     * Valida la información mínima de una ficha
     */
    function validateFicha({ formId }, alertHandler) {
        if (!formId.includes('ficha')) {
            return true;
        }

        const programaId = $('#programa_formacion_id').val();
        const sedeId = $('#sede_id').val();
        const numeroFicha = $('#numero_ficha').val();

        if (!programaId) {
            alertHandler.showError('Debe seleccionar un programa de formación.');
            return false;
        }

        if (!sedeId) {
            alertHandler.showError('Debe seleccionar una sede.');
            return false;
        }

        if (!numeroFicha || numeroFicha.trim() === '') {
            alertHandler.showError('El número de ficha es requerido.');
            return false;
        }

        return true;
    }

    /**
     * Valida el rango de fechas (cuando aplica)
     */
    function validateFechas(_context, alertHandler) {
        const fechaInicio = $('#fecha_inicio').val();
        const fechaFin = $('#fecha_fin').val();

        if (!fechaInicio || !fechaFin) {
            return true;
        }

        if (new Date(fechaInicio) >= new Date(fechaFin)) {
            alertHandler.showError('La fecha de inicio debe ser anterior a la fecha de fin.');
            return false;
        }

        return true;
    }

    /**
     * Verifica que los campos requeridos estén diligenciados
     */
    function validateRequiredFields({ form }, alertHandler) {
        const requiredFields = form.querySelectorAll('[required]');
        let hasErrors = false;

        requiredFields.forEach((field) => {
            const value = (field.value || '').trim();
            const isEmpty = value === '';

            field.classList.toggle('is-invalid', isEmpty);

            if (isEmpty) {
                hasErrors = true;
            }
        });

        if (hasErrors) {
            alertHandler.showError('Por favor, complete todos los campos requeridos.');
            return false;
        }

        return true;
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
    $('#fecha_inicio, #fecha_fin').on('change', function () {
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
    window.confirmarEliminacion = function (nombre, url, tipo = 'elemento') {
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
