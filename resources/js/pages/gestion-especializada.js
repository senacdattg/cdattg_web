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
        
        // Actualizar botones de acción (usar IDs y clases)
        $('#btn-asignar, .btn-asignar').prop('disabled', elementosSeleccionadosDisponibles === 0);
        $('#btn-desasignar, .btn-desasignar').prop('disabled', elementosSeleccionadosAsignados === 0);
        
        // Actualizar contadores en la UI si existen (usar IDs y clases)
        $('#contador-seleccionados, .contador-asignados').text(elementosSeleccionadosAsignados);
        $('#contador-disponibles, .contador-disponibles').text(elementosSeleccionadosDisponibles);
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

    // Función para seleccionar/deseleccionar todos (usar IDs y clases)
    $('#select-all-asignados, .select-all-asignados').on('change', function() {
        $('.checkbox-asignado').prop('checked', $(this).is(':checked'));
        actualizarContadores();
    });

    $('#select-all-disponibles, .select-all-disponibles').on('change', function() {
        $('.checkbox-disponible').prop('checked', $(this).is(':checked'));
        actualizarContadores();
    });

    // Evento click para botón asignar
    $('#btn-asignar').on('click', function() {
        const seleccionados = $('.checkbox-disponible:checked').length;
        if (seleccionados > 0) {
            $('#form-asignar').submit();
        }
    });

    // Evento click para botón desasignar
    $('#btn-desasignar').on('click', function() {
        const seleccionados = $('.checkbox-asignado:checked').length;
        if (seleccionados > 0) {
            if (confirm(`¿Está seguro de desasignar ${seleccionados} aprendiz(es)?`)) {
                $('#form-desasignar').submit();
            }
        }
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

        // Obtener días de la semana del DOM (deben estar disponibles en la vista)
        const diasSemanaDisponibles = window.diasSemana || [
            {id: 12, nombre: 'LUNES'},
            {id: 13, nombre: 'MARTES'},
            {id: 14, nombre: 'MIÉRCOLES'},
            {id: 15, nombre: 'JUEVES'},
            {id: 16, nombre: 'VIERNES'},
            {id: 17, nombre: 'SÁBADO'},
            {id: 18, nombre: 'DOMINGO'}
        ];

        // Crear HTML para los días de la semana (solo checkboxes)
        let diasHTML = '';
        diasSemanaDisponibles.forEach(dia => {
            diasHTML += `
                <div class="col-md-4 col-sm-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input dia-check" type="checkbox" 
                               name="instructores[${nuevoIndice}][dias_semana][]" 
                               value="${dia.id}" 
                               id="dia_${nuevoIndice}_${dia.id}">
                        <label class="form-check-label fw-bold" for="dia_${nuevoIndice}_${dia.id}">
                            <i class="far fa-calendar-alt mr-1"></i> ${dia.nombre}
                        </label>
                    </div>
                </div>
            `;
        });

        // Crear HTML para el nuevo instructor
        const instructorHTML = `
            <div class="instructor-row border rounded p-3 mb-3 bg-light position-relative" data-index="${nuevoIndice}">                
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold">
                            <i class="fas fa-user-tie mr-1"></i> Instructor <span class="text-danger">*</span>
                        </label>
                        <select name="instructores[${nuevoIndice}][instructor_id]" class="form-control instructor-select" required>
                            <option value="">Seleccionar instructor...</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label font-weight-bold">
                            <i class="fas fa-calendar-alt mr-1"></i> Fecha Inicio <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="instructores[${nuevoIndice}][fecha_inicio]" 
                               class="form-control fecha-inicio" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label font-weight-bold">
                            <i class="fas fa-calendar-check mr-1"></i> Fecha Fin <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="instructores[${nuevoIndice}][fecha_fin]" 
                               class="form-control fecha-fin" required>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-12">
                        <label class="form-label font-weight-bold">
                            <i class="fas fa-calendar-week mr-1"></i> Días de Formación <span class="text-danger">*</span>
                            <small class="text-muted">(Seleccione los días - Los horarios se tomarán de la configuración de la ficha)</small>
                        </label>
                        <div class="border rounded p-3 bg-white">
                            <div class="row">
                                ${diasHTML}
                            </div>
                            <div class="mt-2 text-center">
                                <span class="badge badge-secondary dias-count-${nuevoIndice}">0 días seleccionados</span>
                            </div>
                        </div>
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
        // Obtener el ID de la ficha desde la URL o variable global
        let fichaId = null;
        
        // Intentar obtener de variable global primero
        if (typeof window.fichaId !== 'undefined') {
            fichaId = window.fichaId;
        } else {
            // Obtener desde la URL: /fichaCaracterizacion/{id}/gestionar-instructores
            const urlParts = window.location.pathname.split('/');
            const gestionarIndex = urlParts.indexOf('gestionar-instructores');
            if (gestionarIndex > 0) {
                fichaId = urlParts[gestionarIndex - 1];
            } else {
                // Fallback: buscar el número en la URL
                fichaId = urlParts.find(part => !isNaN(part) && part !== '');
            }
        }
        
        console.log('Cargando instructores para ficha ID:', fichaId);
        
        fetch(`/fichaCaracterizacion/${fichaId}/instructores-disponibles`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Respuesta del servidor:', data);
                
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
                        
                        console.log(`Cargados ${data.instructores.length} instructores en el select`);
                    }
                } else {
                    console.error('Error al cargar instructores:', data.message || 'No hay instructores disponibles');
                    
                    // Mostrar mensaje de error al usuario
                    const select = document.querySelector(`.instructor-row[data-index="${indice}"] .instructor-select`);
                    if (select) {
                        select.innerHTML = '<option value="">No hay instructores disponibles</option>';
                    }
                }
            })
            .catch(error => {
                console.error('Error en la petición:', error);
                
                // Mostrar mensaje de error al usuario
                const select = document.querySelector(`.instructor-row[data-index="${indice}"] .instructor-select`);
                if (select) {
                    select.innerHTML = '<option value="">Error al cargar instructores</option>';
                }
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
        const fechaInicio = instructorRow.querySelector('.fecha-inicio');
        const fechaFin = instructorRow.querySelector('.fecha-fin');
        const indice = instructorRow.dataset.index;

        // Configurar fechas por defecto (sin restricción de fecha mínima)
        // Dejar vacías para que el usuario las seleccione libremente
        // fechaInicio.value = '';
        // fechaFin.value = '';

        // Validar que fecha fin sea posterior a fecha inicio (solo validación, sin auto-ajuste)
        fechaInicio.addEventListener('change', function() {
            if (fechaFin.value && fechaFin.value < fechaInicio.value) {
                // Solo mostrar advertencia, no auto-ajustar
                console.warn('La fecha de fin debe ser posterior o igual a la fecha de inicio');
            }
        });

        // Configurar eventos para los checkboxes de días (solo contador)
        const diasCheckboxes = instructorRow.querySelectorAll('.dia-check');
        diasCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                actualizarContadorDias(indice);
            });
        });
    }

    // Función para actualizar contador de días seleccionados
    function actualizarContadorDias(indice) {
        const container = document.querySelector(`.instructor-row[data-index="${indice}"]`);
        if (!container) return;
        
        const diasChecked = container.querySelectorAll('.dia-check:checked').length;
        const contadorSpan = container.querySelector(`.dias-count-${indice}`);
        
        if (contadorSpan) {
            const texto = diasChecked > 0 ? `${diasChecked} día${diasChecked > 1 ? 's' : ''} seleccionado${diasChecked > 1 ? 's' : ''}` : '0 días seleccionados';
            contadorSpan.textContent = texto;
            contadorSpan.className = diasChecked > 0 ? 'badge badge-success' : 'badge badge-secondary';
        }
    }

    // Inicializar contadores al cargar la página
    actualizarContadores();
    
    // Log de inicialización
    console.log('Contadores inicializados:', {
        asignados: elementosSeleccionadosAsignados,
        disponibles: elementosSeleccionadosDisponibles
    });

    // Auto-focus en el primer campo de búsqueda si existe
    const searchInput = document.querySelector('input[type="search"], input[placeholder*="buscar" i]');
    if (searchInput) {
        searchInput.focus();
    }

    console.log('Vista de gestión especializada inicializada correctamente');

    // ===== FUNCIONALIDAD DEL MODAL DE DÍAS DE FORMACIÓN =====
    
    // Variables globales para el modal
    let instructorFichaIdActual = null;
    let fichaIdActual = null;
    let guardandoDias = false; // Flag para prevenir múltiples guardados

    // Abrir modal de días de formación
    $(document).on('click', '.btn-gestionar-dias', function() {
        const instructorFichaId = $(this).data('instructor-ficha-id');
        const instructorNombre = $(this).data('instructor-nombre');
        
        instructorFichaIdActual = instructorFichaId;
        fichaIdActual = window.fichaId || obtenerFichaIdDeUrl();
        
        // Llenar información del instructor
        $('#modal-instructor-nombre').text(instructorNombre);
        $('#modal-instructor-ficha-id').val(instructorFichaId);
        
        // Cargar días existentes
        cargarDiasExistentes();
        
        // Mostrar modal
        $('#modalDiasFormacion').modal('show');
    });

    // Cargar días existentes del instructor
    function cargarDiasExistentes() {
        if (!instructorFichaIdActual || !fichaIdActual) return;
        
        $.ajax({
            url: `/fichaCaracterizacion/${fichaIdActual}/instructor/${instructorFichaIdActual}/obtener-dias`,
            method: 'GET',
            success: function(response) {
                if (response.success && response.data) {
                    // Limpiar selecciones previas
                    $('.dia-checkbox-modal').prop('checked', false);
                    $('.hora-inicio-modal, .hora-fin-modal').val('').prop('disabled', true);
                    $('.dia-status-modal').hide();
                    
                    // Marcar días existentes
                    response.data.forEach(dia => {
                        const checkbox = $(`.dia-checkbox-modal[value="${dia.dia_id}"]`);
                        const horaInicio = $(`.hora-inicio-modal[data-dia="${dia.dia_id}"]`);
                        const horaFin = $(`.hora-fin-modal[data-dia="${dia.dia_id}"]`);
                        const status = $(`.dia-row-modal[data-dia-id="${dia.dia_id}"] .dia-status-modal`);
                        
                        checkbox.prop('checked', true);
                        horaInicio.val(dia.hora_inicio).prop('disabled', false);
                        horaFin.val(dia.hora_fin).prop('disabled', false);
                        status.show();
                    });
                }
            },
            error: function(xhr) {
                console.error('Error al cargar días existentes:', xhr);
            }
        });
    }

    // Manejar cambio en checkboxes del modal
    $(document).on('change', '.dia-checkbox-modal', function() {
        const diaId = $(this).val();
        const horaInicio = $(`.hora-inicio-modal[data-dia="${diaId}"]`);
        const horaFin = $(`.hora-fin-modal[data-dia="${diaId}"]`);
        const status = $(`.dia-row-modal[data-dia-id="${diaId}"] .dia-status-modal`);
        
        if ($(this).is(':checked')) {
            horaInicio.prop('disabled', false);
            horaFin.prop('disabled', false);
            // Cargar horas por defecto de la ficha si están disponibles
            cargarHorasPorDefecto(diaId);
        } else {
            horaInicio.prop('disabled', true).val('');
            horaFin.prop('disabled', true).val('');
            status.hide();
        }
    });

    // Cargar horas por defecto de la ficha
    function cargarHorasPorDefecto(diaId) {
        // Si hay datos de días de la semana disponibles, usarlos
        if (window.diasSemanaData && window.diasSemanaData[diaId]) {
            const diaData = window.diasSemanaData[diaId];
            // Remover segundos de las horas por defecto
            const horaInicio = diaData.hora_inicio ? diaData.hora_inicio.substring(0, 5) : '';
            const horaFin = diaData.hora_fin ? diaData.hora_fin.substring(0, 5) : '';
            $(`.hora-inicio-modal[data-dia="${diaId}"]`).val(horaInicio);
            $(`.hora-fin-modal[data-dia="${diaId}"]`).val(horaFin);
        }
    }

    // Seleccionar todos los días
    $(document).on('change', '#select-all-modal', function() {
        const isChecked = $(this).is(':checked');
        $('.dia-checkbox-modal').prop('checked', isChecked).trigger('change');
    });

    // Vista previa de fechas
    $(document).on('click', '#btn-preview-modal', function() {
        const diasSeleccionados = obtenerDiasSeleccionadosModal();
        
        if (diasSeleccionados.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Sin días seleccionados',
                text: 'Por favor selecciona al menos un día de la semana.'
            });
            return;
        }
        
        $.ajax({
            url: `/fichaCaracterizacion/${fichaIdActual}/instructor/${instructorFichaIdActual}/preview-fechas`,
            method: 'POST',
            data: {
                dias: diasSeleccionados,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success && response.data) {
                    mostrarPreviewFechas(response.data);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'No se pudieron generar las fechas'
                    });
                }
            },
            error: function(xhr) {
                console.error('Error en vista previa:', xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al generar la vista previa de fechas'
                });
            }
        });
    });

    // Obtener días seleccionados del modal
    function obtenerDiasSeleccionadosModal() {
        const dias = [];
        $('.dia-checkbox-modal:checked').each(function() {
            const diaId = $(this).val();
            const horaInicio = $(`.hora-inicio-modal[data-dia="${diaId}"]`).val();
            const horaFin = $(`.hora-fin-modal[data-dia="${diaId}"]`).val();
            
            dias.push({
                dia_id: parseInt(diaId),
                hora_inicio: horaInicio ? horaInicio.substring(0, 5) : null, // Remover segundos
                hora_fin: horaFin ? horaFin.substring(0, 5) : null // Remover segundos
            });
        });
        return dias;
    }

    // Mostrar preview de fechas
    function mostrarPreviewFechas(fechas) {
        let html = '<div class="row">';
        
        fechas.forEach(fecha => {
            html += `
                <div class="col-md-6 col-lg-4 mb-2">
                    <div class="badge badge-info p-2">
                        <i class="fas fa-calendar-day mr-1"></i>
                        ${fecha.fecha} - ${fecha.dia_semana}
                        ${fecha.hora_inicio ? `<br><small>${fecha.hora_inicio} - ${fecha.hora_fin}</small>` : ''}
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        
        $('#fechas-container-modal').html(html);
        $('#preview-fechas-modal').show();
    }

    // Guardar días de formación
    $(document).on('click', '#btn-guardar-dias-modal', function() {
        const btnGuardar = $(this);
        
        // Prevenir doble clic y múltiples guardados
        if (btnGuardar.prop('disabled') || guardandoDias) {
            return;
        }
        
        guardandoDias = true;
        
        const diasSeleccionados = obtenerDiasSeleccionadosModal();
        
        console.log('Días seleccionados:', diasSeleccionados);
        console.log('Ficha ID:', fichaIdActual);
        console.log('Instructor Ficha ID:', instructorFichaIdActual);
        
        if (diasSeleccionados.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Sin días seleccionados',
                text: 'Por favor selecciona al menos un día de la semana.'
            });
            return;
        }
        
        // Validar que todos los días tengan horas si es requerido
        const diasSinHoras = diasSeleccionados.filter(dia => !dia.hora_inicio || !dia.hora_fin);
        if (diasSinHoras.length > 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Horas incompletas',
                text: 'Algunos días seleccionados no tienen horas de inicio y fin configuradas.'
            });
            return;
        }
        
        // Mostrar loading
        const textoOriginal = btnGuardar.html();
        btnGuardar.html('<i class="fas fa-spinner fa-spin mr-1"></i>Guardando...').prop('disabled', true);
        
        $.ajax({
            url: `/fichaCaracterizacion/${fichaIdActual}/instructor/${instructorFichaIdActual}/asignar-dias`,
            method: 'POST',
            data: {
                dias: diasSeleccionados,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Los días de formación han sido guardados correctamente.'
                    }).then(() => {
                        $('#modalDiasFormacion').modal('hide');
                        // Recargar la página para mostrar los cambios
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Error al guardar los días de formación'
                    });
                }
            },
            error: function(xhr) {
                console.error('Error al guardar días:', xhr);
                let mensaje = 'Error al guardar los días de formación';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    mensaje = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errores = Object.values(xhr.responseJSON.errors).flat();
                    mensaje = errores.join(', ');
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: mensaje
                });
            },
            complete: function() {
                btnGuardar.html(textoOriginal).prop('disabled', false);
                guardandoDias = false; // Resetear flag
            }
        });
    });

    // Limpiar modal al cerrar
    $('#modalDiasFormacion').on('hidden.bs.modal', function() {
        $('#form-asignar-dias-modal')[0].reset();
        $('.dia-checkbox-modal').prop('checked', false);
        $('.hora-inicio-modal, .hora-fin-modal').val('').prop('disabled', true);
        $('.dia-status-modal').hide();
        $('#preview-fechas-modal').hide();
        $('#fechas-container-modal').empty();
        instructorFichaIdActual = null;
        fichaIdActual = null;
        guardandoDias = false; // Resetear flag
    });

});
