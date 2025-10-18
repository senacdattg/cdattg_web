/**
 * Script genérico para vistas de gestión especializada
 * Maneja funcionalidades complejas como asignaciones, contadores, etc.
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

    // Variables globales para contadores
    let elementosSeleccionadosAsignados = 0;
    let elementosSeleccionadosDisponibles = 0;

    // Función para actualizar contadores
    function actualizarContadores() {
        elementosSeleccionadosAsignados = $('.checkbox-asignado:checked').length;
        elementosSeleccionadosDisponibles = $('.checkbox-disponible:checked').length;
        
        // Actualizar botones de acción
        $('.btn-asignar').prop('disabled', elementosSeleccionadosDisponibles === 0);
        $('.btn-desasignar').prop('disabled', elementosSeleccionadosAsignados === 0);
        
        // Actualizar contadores en la UI si existen
        $('.contador-asignados').text(elementosSeleccionadosAsignados);
        $('.contador-disponibles').text(elementosSeleccionadosDisponibles);
    }

    // Eventos para checkboxes
    $(document).on('change', '.checkbox-asignado, .checkbox-disponible', function() {
        actualizarContadores();
    });

    // Función para confirmar asignación principal
    window.confirmarAsignacionPrincipal = function(especialidadNombre) {
        return alertHandler.showCustomAlert({
            title: '¿Asignar como Especialidad Principal?',
            html: `<div class="text-left">
                <p><strong>Especialidad:</strong> ${especialidadNombre}</p>
                <p><strong>Acción:</strong> Se asignará como especialidad principal</p>
                <p class="text-warning"><i class="fas fa-exclamation-triangle"></i> Esto reemplazará la especialidad principal actual (si existe)</p>
            </div>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, asignar',
            cancelButtonText: 'Cancelar'
        });
    };

    // Función para confirmar eliminación de especialidad
    window.confirmarEliminacionEspecialidad = function(especialidadNombre) {
        return alertHandler.showCustomAlert({
            title: '¿Eliminar Especialidad?',
            html: `<div class="text-left">
                <p><strong>Especialidad:</strong> ${especialidadNombre}</p>
                <p><strong>Acción:</strong> Se eliminará la especialidad del instructor</p>
                <p class="text-danger"><i class="fas fa-exclamation-triangle"></i> Esta acción no se puede deshacer</p>
            </div>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        });
    };

    // Función para mover elementos entre listas
    function moverElementos(sourceContainer, targetContainer, targetName) {
        $(sourceContainer + ' .list-item input:checked').each(function() {
            const item = $(this).closest('.list-item');
            const itemId = $(this).val();
            const itemText = item.find('.item-text').text();
            
            // Crear nuevo elemento en el contenedor destino
            const newItem = $(`
                <div class="list-item">
                    <input type="checkbox" name="${targetName}[]" value="${itemId}" class="form-check-input">
                    <span class="item-text">${itemText}</span>
                </div>
            `);
            
            $(targetContainer).append(newItem);
            item.remove();
        });
        
        actualizarContadores();
    }

    // Eventos para botones de movimiento
    $('.btn-mover-derecha').on('click', function() {
        moverElementos('.lista-disponibles', '.lista-asignados', 'elementos_asignados');
    });

    $('.btn-mover-izquierda').on('click', function() {
        moverElementos('.lista-asignados', '.lista-disponibles', 'elementos_disponibles');
    });

    // Función para recalcular horas cuando cambien los días de formación
    $('select[name*="[dias_formacion]"]').on('change', function() {
        const instructorRow = $(this).closest('.instructor-row')[0];
        if (instructorRow) {
            recalcularHoras(instructorRow);
        }
    });

    function recalcularHoras(instructorRow) {
        const diasFormacion = $(instructorRow).find('select[name*="[dias_formacion]"]').val();
        const horasPorDia = $(instructorRow).find('input[name*="[horas_dia]"]').val() || 8;
        
        if (diasFormacion && horasPorDia) {
            const totalHoras = diasFormacion * horasPorDia;
            $(instructorRow).find('.total-horas').text(totalHoras);
            $(instructorRow).find('input[name*="[total_horas]"]').val(totalHoras);
        }
    }

    // Función para validar formularios de gestión
    $('.form-gestion').on('submit', function(e) {
        const form = this;
        const formType = $(form).data('form-type');
        
        // Validaciones específicas según el tipo de formulario
        if (formType === 'asignacion-instructores') {
            const instructoresAsignados = $('.checkbox-asignado:checked').length;
            if (instructoresAsignados === 0) {
                e.preventDefault();
                alertHandler.showError('Debe seleccionar al menos un instructor para asignar.');
                return;
            }
        }
        
        if (formType === 'asignacion-aprendices') {
            const aprendicesAsignados = $('.checkbox-asignado:checked').length;
            if (aprendicesAsignados === 0) {
                e.preventDefault();
                alertHandler.showError('Debe seleccionar al menos un aprendiz para asignar.');
                return;
            }
        }
        
        // Mostrar loading en el botón de envío
        $(form).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Procesando...');
    });

    // Función para seleccionar/deseleccionar todos
    $('.select-all-asignados').on('change', function() {
        $('.checkbox-asignado').prop('checked', $(this).is(':checked'));
        actualizarContadores();
    });

    $('.select-all-disponibles').on('change', function() {
        $('.checkbox-disponible').prop('checked', $(this).is(':checked'));
        actualizarContadores();
    });

    // Función para agregar instructor dinámicamente
    window.agregarInstructor = function() {
        const container = document.getElementById('instructores-container');
        if (!container) {
            console.error('No se encontró el contenedor de instructores');
            return;
        }

        // Contar instructores existentes
        const instructoresExistentes = container.querySelectorAll('.instructor-row').length;
        const nuevoIndice = instructoresExistentes;

        // Crear HTML para el nuevo instructor
        const instructorHTML = `
            <div class="instructor-row border rounded p-3 mb-3 bg-light" data-index="${nuevoIndice}">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label font-weight-bold">Instructor</label>
                        <select name="instructores[${nuevoIndice}][instructor_id]" class="form-control instructor-select" required>
                            <option value="">Seleccionar instructor...</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label font-weight-bold">Días de Formación</label>
                        <input type="number" name="instructores[${nuevoIndice}][dias_formacion]" 
                               class="form-control dias-formacion" min="1" max="7" value="5" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label font-weight-bold">Horas por Día</label>
                        <input type="number" name="instructores[${nuevoIndice}][horas_dia]" 
                               class="form-control horas-dia" min="1" max="12" value="8" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label font-weight-bold">Total Horas</label>
                        <input type="number" name="instructores[${nuevoIndice}][total_horas]" 
                               class="form-control total-horas" readonly value="40">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm" onclick="eliminarInstructor(this)">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold">Fecha Inicio</label>
                        <input type="date" name="instructores[${nuevoIndice}][fecha_inicio]" 
                               class="form-control fecha-inicio" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold">Fecha Fin</label>
                        <input type="date" name="instructores[${nuevoIndice}][fecha_fin]" 
                               class="form-control fecha-fin" required>
                    </div>
                </div>
            </div>
        `;

        // Agregar al contenedor
        container.insertAdjacentHTML('beforeend', instructorHTML);

        // Obtener instructores disponibles via AJAX
        cargarInstructoresDisponibles(nuevoIndice);

        // Configurar eventos para el nuevo instructor
        const nuevoRow = container.querySelector(`.instructor-row[data-index="${nuevoIndice}"]`);
        configurarEventosInstructor(nuevoRow);

        console.log(`Instructor agregado con índice: ${nuevoIndice}`);
    };

    // Función para cargar instructores disponibles
    function cargarInstructoresDisponibles(indice) {
        fetch('/instructor/disponibles-para-ficha')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.instructores) {
                    const select = document.querySelector(`.instructor-row[data-index="${indice}"] .instructor-select`);
                    if (select) {
                        // Limpiar opciones existentes (excepto la primera)
                        select.innerHTML = '<option value="">Seleccionar instructor...</option>';
                        
                        // Agregar instructores
                        data.instructores.forEach(instructor => {
                            const option = document.createElement('option');
                            option.value = instructor.id;
                            option.textContent = `${instructor.persona.primer_nombre} ${instructor.persona.primer_apellido} (${instructor.persona.numero_documento})`;
                            select.appendChild(option);
                        });

                        // Inicializar Select2 si está disponible
                        if (typeof $ !== 'undefined' && $.fn.select2) {
                            $(select).select2({
                                theme: 'bootstrap-5',
                                width: '100%',
                                placeholder: 'Seleccionar instructor...',
                                allowClear: true
                            });
                        }
                    }
                } else {
                    console.error('Error al cargar instructores:', data.message);
                }
            })
            .catch(error => {
                console.error('Error en la petición:', error);
            });
    }

    // Función para eliminar instructor
    window.eliminarInstructor = function(button) {
        const instructorRow = button.closest('.instructor-row');
        if (instructorRow) {
            instructorRow.remove();
            console.log('Instructor eliminado');
        }
    };

    // Función para configurar eventos de un instructor
    function configurarEventosInstructor(instructorRow) {
        const diasFormacion = instructorRow.querySelector('.dias-formacion');
        const horasDia = instructorRow.querySelector('.horas-dia');
        const totalHoras = instructorRow.querySelector('.total-horas');
        const fechaInicio = instructorRow.querySelector('.fecha-inicio');
        const fechaFin = instructorRow.querySelector('.fecha-fin');

        // Recalcular horas cuando cambien días o horas por día
        function recalcularHoras() {
            const dias = parseInt(diasFormacion.value) || 0;
            const horas = parseInt(horasDia.value) || 0;
            const total = dias * horas;
            totalHoras.value = total;
        }

        diasFormacion.addEventListener('input', recalcularHoras);
        horasDia.addEventListener('input', recalcularHoras);

        // Configurar fechas por defecto
        const hoy = new Date();
        const fechaInicioDefault = new Date(hoy);
        fechaInicioDefault.setDate(hoy.getDate() + 1);
        
        const fechaFinDefault = new Date(fechaInicioDefault);
        fechaFinDefault.setDate(fechaInicioDefault.getDate() + 30);

        fechaInicio.value = fechaInicioDefault.toISOString().split('T')[0];
        fechaFin.value = fechaFinDefault.toISOString().split('T')[0];

        // Validar que fecha fin sea posterior a fecha inicio
        fechaInicio.addEventListener('change', function() {
            if (fechaFin.value && fechaFin.value <= fechaInicio.value) {
                const nuevaFechaFin = new Date(fechaInicio.value);
                nuevaFechaFin.setDate(nuevaFechaFin.getDate() + 30);
                fechaFin.value = nuevaFechaFin.toISOString().split('T')[0];
            }
        });
    }

    // Inicializar contadores
    actualizarContadores();

    // Auto-focus en el primer campo de búsqueda si existe
    const searchInput = document.querySelector('input[type="search"], input[placeholder*="buscar" i]');
    if (searchInput) {
        searchInput.focus();
    }

    console.log('Vista de gestión especializada inicializada correctamente');
});
