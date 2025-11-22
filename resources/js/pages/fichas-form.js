/**
 * Script específico para formularios de fichas (create/edit)
 */
import { AlertHandler } from '../modules/alert-handler.js';

document.addEventListener('DOMContentLoaded', () => {
    // Inicializar manejador de alertas
    const alertHandler = new AlertHandler({
        autoHide: true,
        hideDelay: 5000,
        alertSelector: '.alert'
    });
    
    // Inicializar Select2
    const tieneSelect2 = typeof $ !== 'undefined' && $.fn.select2;

    // Función para inicializar Select2 en todos los campos con clase .select2
    function inicializarSelect2() {
        if (tieneSelect2) {
            // Destruir instancias existentes para evitar duplicados
            $('.select2').each(function() {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2('destroy');
                }
            });
            
            // Inicializar Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: function() {
                    return $(this).data('placeholder') || 'Seleccione una opción';
                },
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
    }

    // Inicializar Select2 inmediatamente
    inicializarSelect2();

    // Reinicializar Select2 cuando el collapse se expanda (para formularios dentro de acordeones)
    $('#collapseCrearFicha').on('shown.bs.collapse', function() {
        inicializarSelect2();
    });

    // Función para cargar modalidades
    function loadModalidades() {
        // Implementar carga de modalidades si es necesario
        console.log('Cargando modalidades...');
    }

    // Función para cargar jornadas
    function loadJornadas() {
        // Implementar carga de jornadas si es necesario
        console.log('Cargando jornadas...');
    }

    // Función para cargar instructores
    function loadInstructores() {
        // Implementar carga de instructores si es necesario
        console.log('Cargando instructores...');
    }

    // Función para cargar sedes
    function loadSedes() {
        // Implementar carga de sedes si es necesario
        console.log('Cargando sedes...');
    }

    // Función para cargar ambientes por sede
    function loadAmbientesPorSede(sedeId) {
        console.log('Cargando ambientes para sede:', sedeId);
        if (!sedeId) return;
        
        const ambienteSelect = $('#ambiente_id');
        ambienteSelect.prop('disabled', true);
        ambienteSelect.html('<option value="">Cargando ambientes...</option>');
        if (tieneSelect2) {
            ambienteSelect.trigger('change');
        }
        
        $.ajax({
            url: '/ficha/ambientes-por-sede/' + sedeId,
            method: 'GET',
            success: function(response) {
                console.log('Respuesta de ambientes:', response);
                if (response.success) {
                    ambienteSelect.html('<option value="">Seleccione un ambiente...</option>');
                    
                    response.data.forEach(function(ambiente) {
                        ambienteSelect.append(new Option(
                            ambiente.title + ' - ' + ambiente.descripcion,
                            ambiente.id
                        ));
                    });
                    
                    ambienteSelect.prop('disabled', false);
                    if (tieneSelect2) {
                        // Reinicializar Select2 para el campo de ambiente
                        if (ambienteSelect.hasClass('select2-hidden-accessible')) {
                            ambienteSelect.select2('destroy');
                        }
                        ambienteSelect.select2({
                            theme: 'bootstrap-5',
                            width: '100%',
                            placeholder: ambienteSelect.data('placeholder') || 'Seleccione un ambiente...',
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
                        ambienteSelect.trigger('change');
                    }
                    console.log('Ambientes cargados:', response.data.length);
                } else {
                    ambienteSelect.html('<option value="">Error al cargar ambientes</option>');
                    if (tieneSelect2) {
                        ambienteSelect.trigger('change');
                    }
                    console.error('Error al cargar ambientes:', response.message);
                }
            },
            error: function(xhr, status, error) {
                ambienteSelect.html('<option value="">Error al cargar ambientes</option>');
                if (tieneSelect2) {
                    ambienteSelect.trigger('change');
                }
                console.error('Error AJAX al cargar ambientes:', error);
            }
        });
    }

    // Función para validar fechas
    function validateDates() {
        const fechaInicio = $('#fecha_inicio').val();
        const fechaFin = $('#fecha_fin').val();
        
        if (fechaInicio && fechaFin) {
            if (new Date(fechaInicio) >= new Date(fechaFin)) {
                $('#fecha_fin')[0].setCustomValidity('La fecha de fin debe ser posterior a la fecha de inicio');
            } else {
                $('#fecha_fin')[0].setCustomValidity('');
            }
        }
    }

    // Cuando cambie el programa de formación
    $('#programa_formacion_id').change(function() {
        const programaId = $(this).val();
        console.log('Programa seleccionado:', programaId);
        
        if (programaId) {
            const sedeId = $(this).find('option:selected').data('sede');
            console.log('Sede del programa:', sedeId);
            
            if (sedeId) {
                $('#sede_id').val(sedeId).trigger('change');
            }
        }
    });

    // Cuando cambie la sede
    $('#sede_id').change(function() {
        const sedeId = $(this).val();
        const ambienteSelect = $('#ambiente_id');
        
        // Limpiar y deshabilitar ambiente
        ambienteSelect.prop('disabled', true);
        ambienteSelect.find('option:not(:first)').remove();
        if (tieneSelect2) {
            ambienteSelect.trigger('change');
        }
        
        if (sedeId) {
            loadAmbientesPorSede(sedeId);
        } else {
            ambienteSelect.html('<option value="">Primero seleccione una sede...</option>');
            if (tieneSelect2) {
                ambienteSelect.trigger('change');
            }
        }
    });

    // Validación de fechas
    $('#fecha_inicio, #fecha_fin').change(function() {
        validateDates();
        validarDiasFormacionSegunFechas();
    });
    
    // Función para validar días de formación según el rango de fechas
    function validarDiasFormacionSegunFechas() {
        // Intentar obtener fechas de los inputs primero (para create)
        let fechaInicio = $('#fecha_inicio').val();
        let fechaFin = $('#fecha_fin').val();
        
        // Si no hay fechas en los inputs, usar las fechas de la ficha (para edición)
        if (!fechaInicio && typeof window.fichaFechaInicio !== 'undefined' && window.fichaFechaInicio) {
            fechaInicio = window.fichaFechaInicio;
        }
        if (!fechaFin && typeof window.fichaFechaFin !== 'undefined' && window.fichaFechaFin) {
            fechaFin = window.fichaFechaFin;
        }
        
        if (!fechaInicio || !fechaFin) {
            // Si no hay ambas fechas, habilitar todos los días
            $('.dia-formacion-checkbox').prop('disabled', false);
            return;
        }
        
        // Mapa de días de la semana: 0 = Domingo, 1 = Lunes, ..., 6 = Sábado
        // IDs en la base de datos: 12 = LUNES, 13 = MARTES, 14 = MIÉRCOLES, 15 = JUEVES, 16 = VIERNES, 17 = SÁBADO, 18 = DOMINGO
        const mapeoDias = {
            12: 1, // LUNES -> 1
            13: 2, // MARTES -> 2
            14: 3, // MIÉRCOLES -> 3
            15: 4, // JUEVES -> 4
            16: 5, // VIERNES -> 5
            17: 6, // SÁBADO -> 6
            18: 0  // DOMINGO -> 0
        };
        
        // Calcular qué días de la semana están en el rango
        // Parsear fechas correctamente para evitar problemas de zona horaria
        const parsearFecha = (fechaStr) => {
            const partes = fechaStr.split('-');
            // new Date(year, monthIndex, day) usa zona horaria local
            return new Date(parseInt(partes[0]), parseInt(partes[1]) - 1, parseInt(partes[2]));
        };
        
        const fechaInicioObj = parsearFecha(fechaInicio);
        const fechaFinObj = parsearFecha(fechaFin);
        const diasEnRango = new Set();
        
        // Iterar por todas las fechas en el rango
        const fechaActual = new Date(fechaInicioObj);
        while (fechaActual <= fechaFinObj) {
            const diaSemana = fechaActual.getDay(); // 0 = Domingo, 1 = Lunes, ..., 6 = Sábado
            diasEnRango.add(diaSemana);
            
            // Avanzar al siguiente día
            fechaActual.setDate(fechaActual.getDate() + 1);
        }
        
        console.log('Validación de días:', {
            fechaInicio,
            fechaFin,
            fechaInicioObj: fechaInicioObj.toISOString().split('T')[0],
            fechaFinObj: fechaFinObj.toISOString().split('T')[0],
            diasEnRango: Array.from(diasEnRango)
        });
        
        // Habilitar/deshabilitar checkboxes según si el día está en el rango
        let diasDeshabilitados = [];
        let diasHabilitados = [];
        
        $('.dia-formacion-checkbox').each(function() {
            const checkbox = $(this);
            const diaId = Number.parseInt(checkbox.val(), 10);
            const diaSemana = mapeoDias[diaId];
            
            if (diaSemana !== undefined) {
                if (diasEnRango.has(diaSemana)) {
                    // El día está en el rango, habilitarlo
                    checkbox.prop('disabled', false);
                    checkbox.closest('.custom-control').removeClass('text-muted');
                    const nombreDia = checkbox.next('label').text().trim();
                    diasHabilitados.push(nombreDia);
                    console.log(`Día habilitado: ${nombreDia} (ID: ${diaId}, Día semana: ${diaSemana})`);
                } else {
                    // El día no está en el rango, deshabilitarlo y desmarcarlo
                    checkbox.prop('disabled', true);
                    checkbox.prop('checked', false);
                    checkbox.closest('.custom-control').addClass('text-muted');
                    const nombreDia = checkbox.next('label').text().trim();
                    diasDeshabilitados.push(nombreDia);
                    console.log(`Día deshabilitado: ${nombreDia} (ID: ${diaId}, Día semana: ${diaSemana})`);
                }
            }
        });
        
        // Mostrar mensaje informativo si hay días deshabilitados
        let mensajeInfo = $('#mensaje-dias-formacion');
        if (diasDeshabilitados.length > 0) {
            if (mensajeInfo.length === 0) {
                // Crear el mensaje si no existe
                $('#dias-formacion-container').parent().after(`
                    <div class="alert alert-info mt-2" id="mensaje-dias-formacion">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Nota:</strong> Los días <strong>${diasDeshabilitados.join(', ')}</strong> han sido deshabilitados porque no están dentro del rango de fechas seleccionado (${fechaInicio} a ${fechaFin}).
                    </div>
                `);
            } else {
                // Actualizar el mensaje existente
                mensajeInfo.html(`
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Nota:</strong> Los días <strong>${diasDeshabilitados.join(', ')}</strong> han sido deshabilitados porque no están dentro del rango de fechas seleccionado (${fechaInicio} a ${fechaFin}).
                `);
            }
        } else {
            // Eliminar el mensaje si todos los días están habilitados
            mensajeInfo.remove();
        }
        
        // Actualizar horarios si hay días seleccionados
        const diasSeleccionados = $('.dia-formacion-checkbox:checked:not(:disabled)');
        if (diasSeleccionados.length > 0) {
            // Trigger change para actualizar horarios
            $('.dia-formacion-checkbox:checked:not(:disabled)').first().trigger('change');
        } else {
            // Ocultar contenedor de horarios si no hay días seleccionados
            $('#horarios-container').hide();
        }
    }
    
    // Ejecutar validación al cargar la página si ya hay fechas
    $(document).ready(function() {
        if ($('#fecha_inicio').val() && $('#fecha_fin').val()) {
            validarDiasFormacionSegunFechas();
        }
    });

    // Establecer fecha mínima (2 años antes de hoy)
    const fechaMinima = new Date();
    fechaMinima.setFullYear(fechaMinima.getFullYear() - 2);
    const fechaMinimaFormato = fechaMinima.toISOString().split('T')[0];
    $('#fecha_inicio').attr('min', fechaMinimaFormato);

    // Configuración de horarios por jornada
    const horariosJornadas = {
        'MAÑANA': { min: '06:00', max: '13:10', defaultInicio: '08:00', defaultFin: '12:00', step: 30 },
        'TARDE': { min: '13:00', max: '18:10', defaultInicio: '14:00', defaultFin: '18:00', step: 30 },
        'NOCHE': { min: '17:50', max: '23:10', defaultInicio: '18:00', defaultFin: '22:00', step: 30 },
        'FIN DE SEMANA': { min: '08:00', max: '17:00', defaultInicio: '08:00', defaultFin: '17:00', step: 30 },
        'FINES DE SEMANA': { min: '08:00', max: '17:00', defaultInicio: '08:00', defaultFin: '17:00', step: 30 },
        'MIXTA': { min: '06:00', max: '23:10', defaultInicio: '08:00', defaultFin: '18:00', step: 30 }
    };

    // Función para generar array de horas permitidas según la jornada
    function generarHorasPermitidas(configHorarios) {
        if (!configHorarios) return [];
        
        const horas = [];
        const [minHora, minMinuto] = configHorarios.min.split(':').map(Number);
        const [maxHora, maxMinuto] = configHorarios.max.split(':').map(Number);
        const step = configHorarios.step || 30; // minutos
        
        const minTotal = minHora * 60 + minMinuto;
        const maxTotal = maxHora * 60 + maxMinuto;
        
        for (let total = minTotal; total <= maxTotal; total += step) {
            const hora = Math.floor(total / 60);
            const minuto = total % 60;
            const horaFormato = `${String(hora).padStart(2, '0')}:${String(minuto).padStart(2, '0')}`;
            horas.push(horaFormato);
        }
        
        return horas;
    }

    // Función para obtener la configuración de horarios según la jornada seleccionada
    function obtenerHorariosJornada() {
        // Primero intentar usar la jornada de la ficha si está disponible (para edición)
        let jornadaNombre = null;
        
        if (typeof window.fichaJornadaNombre !== 'undefined' && window.fichaJornadaNombre) {
            jornadaNombre = window.fichaJornadaNombre.toUpperCase();
        } else {
            // Si no hay jornada de ficha, usar el select (para creación)
            const jornadaSelect = $('#jornada_id');
            const jornadaId = jornadaSelect.val();
            
            if (!jornadaId) {
                return null;
            }
            
            // Obtener el texto de la opción seleccionada (nombre de la jornada)
            jornadaNombre = jornadaSelect.find('option:selected').text().trim().toUpperCase();
        }
        
        if (!jornadaNombre) {
            return null;
        }
        
        // Buscar la configuración de horarios para esta jornada
        for (const [key, config] of Object.entries(horariosJornadas)) {
            if (jornadaNombre.includes(key) || key.includes(jornadaNombre)) {
                return config;
            }
        }
        
        // Si no se encuentra, usar configuración por defecto
        return { min: '06:00', max: '23:10', defaultInicio: '08:00', defaultFin: '16:00' };
    }

    // Manejo de días de formación
    function manejarDiasFormacion() {
        const diasCheckboxes = $('.dia-formacion-checkbox');
        const horariosContainer = $('#horarios-container');
        const horariosDias = $('#horarios-dias');
        
        // Objeto para almacenar los valores de horarios (global para acceso desde otras funciones)
        if (typeof window.valoresHorarios === 'undefined') {
            window.valoresHorarios = {};
        }
        const valoresHorarios = window.valoresHorarios;
        
        // Función para guardar valores actuales antes de regenerar
        function guardarValoresActuales() {
            horariosDias.find('.card').each(function() {
                const cardId = $(this).parent().attr('id');
                if (cardId) {
                    const diaId = cardId.replace('horario-dia-', '');
                    // Buscar tanto selects como inputs (por compatibilidad)
                    const horaInicioSelect = $(this).find('select[name*="hora_inicio"], input[name*="hora_inicio"]');
                    const horaFinSelect = $(this).find('select[name*="hora_fin"], input[name*="hora_fin"]');
                    const horaInicio = horaInicioSelect.val();
                    const horaFin = horaFinSelect.val();
                    
                    // Guardar los valores incluso si están vacíos, para mantener el estado
                    if (horaInicio || horaFin) {
                        valoresHorarios[diaId] = {
                            hora_inicio: horaInicio || null,
                            hora_fin: horaFin || null
                        };
                    }
                }
            });
        }
        
        // Función para generar horarios para un día específico
        function generarHorariosDia(diaId, diaNombre) {
            // Obtener configuración de horarios según la jornada
            let configHorarios = obtenerHorariosJornada();
            
            // Si no hay configuración, verificar si hay jornada de ficha disponible
            if (!configHorarios) {
                const tieneJornadaFicha = typeof window.fichaJornadaNombre !== 'undefined' && window.fichaJornadaNombre;
                
                if (!tieneJornadaFicha) {
                    // Si no hay jornada seleccionada ni de ficha, mostrar mensaje con SweetAlert2
                    if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
                        Swal.fire({
                            icon: 'info',
                            title: 'Jornada Requerida',
                            text: 'Por favor, seleccione primero una jornada de formación para configurar los horarios.',
                            confirmButtonText: 'Entendido',
                            confirmButtonColor: '#17a2b8',
                            allowOutsideClick: true,
                            allowEscapeKey: true
                        });
                    }
                    return `
                        <div class="col-md-12 mb-3">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                Por favor, seleccione primero una jornada de formación para configurar los horarios.
                            </div>
                        </div>
                    `;
                }
                // Si hay jornada de ficha pero no se encontró configuración, usar valores por defecto
                configHorarios = { min: '06:00', max: '23:10', defaultInicio: '08:00', defaultFin: '16:00' };
            }
            
            // Usar valores guardados o valores por defecto según la jornada
            const horaInicio = valoresHorarios[diaId]?.hora_inicio || configHorarios.defaultInicio;
            const horaFin = valoresHorarios[diaId]?.hora_fin || configHorarios.defaultFin;
            
            // Generar horas permitidas para esta jornada
            const horasPermitidas = generarHorasPermitidas(configHorarios);
            
            // Generar opciones para select de hora inicio
            let opcionesInicio = '<option value="">Seleccione...</option>';
            horasPermitidas.forEach(hora => {
                const selected = hora === horaInicio ? 'selected' : '';
                opcionesInicio += `<option value="${hora}" ${selected}>${hora}</option>`;
            });
            
            // Generar opciones para select de hora fin
            let opcionesFin = '<option value="">Seleccione...</option>';
            horasPermitidas.forEach(hora => {
                const selected = hora === horaFin ? 'selected' : '';
                opcionesFin += `<option value="${hora}" ${selected}>${hora}</option>`;
            });
            
            return `
                <div class="col-md-6 col-lg-4 mb-3" id="horario-dia-${diaId}">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white py-2">
                            <h6 class="mb-0"><i class="fas fa-calendar-day"></i> ${diaNombre}</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-2">
                                <div class="col-12 col-sm-6">
                                    <label class="form-label small mb-1 d-block">Hora Inicio</label>
                                    <select class="form-control hora-inicio-input" 
                                            name="horarios[${diaId}][hora_inicio]" 
                                            style="width: 100%; font-size: 0.9rem;"
                                            required>
                                        ${opcionesInicio}
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <label class="form-label small mb-1 d-block">Hora Fin</label>
                                    <select class="form-control hora-fin-input" 
                                            name="horarios[${diaId}][hora_fin]" 
                                            style="width: 100%; font-size: 0.9rem;"
                                            required>
                                        ${opcionesFin}
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
        
        // Función para actualizar horarios
        function actualizarHorarios() {
            // Guardar valores actuales antes de regenerar
            guardarValoresActuales();
            
            const diasSeleccionados = diasCheckboxes.filter(':checked');
            const btnGuardar = $('#btn-guardar-dias');
            
            if (diasSeleccionados.length > 0) {
                horariosContainer.show();
                horariosDias.empty();
                
                diasSeleccionados.each(function() {
                    const diaId = $(this).val();
                    const diaNombre = $(this).next('label').text().trim();
                    const horarioHTML = generarHorariosDia(diaId, diaNombre);
                    horariosDias.append(horarioHTML);
                });
                
                // Mostrar botón de guardar
                if (btnGuardar.length) {
                    btnGuardar.show();
                }
            } else {
                horariosContainer.hide();
                horariosDias.empty();
                
                // Ocultar botón de guardar
                if (btnGuardar.length) {
                    btnGuardar.hide();
                }
            }
        }
        
        // Event listener para cambios en checkboxes
        diasCheckboxes.on('change', function() {
            // Verificar si hay jornada disponible (del select o de la ficha)
            const jornadaId = $('#jornada_id').val();
            const tieneJornadaFicha = typeof window.fichaJornadaNombre !== 'undefined' && window.fichaJornadaNombre;
            
            if (!jornadaId && !tieneJornadaFicha) {
                // Usar SweetAlert2 si está disponible, sino usar alert nativo
                if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Jornada Requerida',
                        text: 'Por favor, seleccione primero una jornada de formación antes de seleccionar los días.',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#ffc107',
                        allowOutsideClick: true,
                        allowEscapeKey: true
                    });
                } else {
                    alert('Por favor, seleccione primero una jornada de formación antes de seleccionar los días.');
                }
                $(this).prop('checked', false);
                return;
            }
            actualizarHorarios();
        });

        // Event listener para cambios en la jornada - actualizar horarios si hay días seleccionados
        $('#jornada_id').on('change', function() {
            const diasSeleccionados = diasCheckboxes.filter(':checked');
            if (diasSeleccionados.length > 0) {
                actualizarHorarios();
            }
        });
        
        // Guardar valores cuando el usuario los cambia
        $(document).on('change', '.hora-inicio-input, .hora-fin-input', function() {
            // Guardar el valor inmediatamente cuando cambia
            const cardBody = $(this).closest('.card-body');
            const cardId = $(this).closest('[id^="horario-dia-"]').attr('id');
            if (cardId) {
                const diaId = cardId.replace('horario-dia-', '');
                const horaInicioSelect = cardBody.find('.hora-inicio-input');
                const horaFinSelect = cardBody.find('.hora-fin-input');
                
                // Actualizar el objeto de valores guardados
                if (!valoresHorarios[diaId]) {
                    valoresHorarios[diaId] = {};
                }
                if ($(this).hasClass('hora-inicio-input')) {
                    valoresHorarios[diaId].hora_inicio = $(this).val();
                }
                if ($(this).hasClass('hora-fin-input')) {
                    valoresHorarios[diaId].hora_fin = $(this).val();
                }
            }
        });
        
        // Validación de horarios (ahora con selects)
        $(document).on('change', '.hora-inicio-input, .hora-fin-input', function() {
            const cardBody = $(this).closest('.card-body');
            const horaInicioSelect = cardBody.find('.hora-inicio-input');
            const horaFinSelect = cardBody.find('.hora-fin-input');
            const horaInicio = horaInicioSelect.val();
            const horaFin = horaFinSelect.val();
            
            // Limpiar estados de error previos
            horaInicioSelect.removeClass('is-invalid');
            horaFinSelect.removeClass('is-invalid');
            
            // Validar que la hora de fin sea posterior a la hora de inicio
            if (horaInicio && horaFin) {
                if (horaInicio >= horaFin) {
                    const mensaje = 'La hora de fin debe ser posterior a la hora de inicio';
                    horaFinSelect.addClass('is-invalid');
                    
                    // Mostrar alerta SweetAlert2 si está disponible
                    if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Horario Inválido',
                            text: mensaje,
                            confirmButtonText: 'Entendido',
                            confirmButtonColor: '#d33',
                            timer: 3000,
                            timerProgressBar: true
                        });
                    }
                }
            }
        });
        
        // Inicializar horarios si hay días preseleccionados
        actualizarHorarios();
    }
    
    // Inicializar manejo de días de formación
    manejarDiasFormacion();

    // Validación de formulario
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(event) {
            console.log('=== FORMULARIO INTERCEPTADO ===');
            console.log('Formulario válido:', this.checkValidity());
            
            // Validar fechas
            validateDates();
            
            // Recopilar información de horarios para debugging
            const horariosData = {};
            document.querySelectorAll('select[name*="horarios"], input[name*="horarios"]').forEach(element => {
                console.log('Elemento horario:', element.name, '=', element.value);
                horariosData[element.name] = element.value;
            });
            console.log('Datos de horarios:', horariosData);
            
            // Recopilar días de formación
            const diasFormacion = [];
            document.querySelectorAll('input[name="dias_formacion[]"]:checked').forEach(checkbox => {
                diasFormacion.push(checkbox.value);
            });
            console.log('Días de formación seleccionados:', diasFormacion);
            
            const isValid = this.checkValidity();
            
            if (!isValid) {
                console.log('=== FORMULARIO INVÁLIDO ===');
                event.preventDefault();
                const invalidElements = this.querySelectorAll(':invalid');
                console.log('Elementos inválidos:', invalidElements.length);
                
                // Recopilar mensajes de error
                const errores = [];
                invalidElements.forEach(function(element) {
                    const mensaje = element.validationMessage || 'Este campo es requerido';
                    errores.push(mensaje);
                    console.log('Campo inválido:', element.name, 'Error:', mensaje);
                });
                
                $(this).addClass('was-validated');
                
                // Mostrar alerta SweetAlert2 con los errores
                if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
                    const mensajeError = errores.length > 0 
                        ? errores.slice(0, 3).join('<br>') + (errores.length > 3 ? '<br>... y más errores' : '')
                        : 'Por favor, complete todos los campos requeridos correctamente.';
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Formulario Inválido',
                        html: mensajeError,
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#d33'
                    });
                }
                
                return false;
            } else {
                console.log('=== FORMULARIO VÁLIDO - ENVIANDO ===');
                // Permitir envío normal
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
        if (form) {
            form.reset();
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('.select2').val(null).trigger('change');
            }
        }
    }

    // Cargar datos iniciales para edición
    if (window.location.pathname.includes('/edit')) {
        // Establecer valores actuales después de inicializar Select2
        setTimeout(() => {
            // Obtener valores directamente del select (ya vienen del HTML)
            const sedeId = $('#sede_id').val();
            const instructorId = $('#instructor_id').val();
            const modalidadId = $('#modalidad_formacion_id').val();
            const jornadaId = $('#jornada_id').val();
            const ambienteId = $('#ambiente_id').val();
            
            // Si hay valores, asegurarse de que Select2 los muestre correctamente
            if (sedeId) {
                $('#sede_id').val(sedeId).trigger('change');
                if (sedeId && !$('#ambiente_id').prop('disabled')) {
                    loadAmbientesPorSede(sedeId);
                }
            }
            
            if (instructorId) {
                $('#instructor_id').val(instructorId).trigger('change');
            }
            
            if (modalidadId) {
                $('#modalidad_formacion_id').val(modalidadId).trigger('change');
            }
            
            if (jornadaId) {
                $('#jornada_id').val(jornadaId).trigger('change');
            }
            
            if (ambienteId && sedeId) {
                setTimeout(() => {
                    $('#ambiente_id').val(ambienteId).trigger('change');
                }, 500);
            }
        }, 500);
    }

    // Función para cargar modalidades
    function loadModalidades() {
        $.get('/api/modalidades', function(data) {
            const select = $('#modalidad_formacion_id');
            select.find('option:not(:first)').remove();
            data.forEach(function(modalidad) {
                select.append(new Option(modalidad.name, modalidad.id));
            });
            if (tieneSelect2) {
                select.trigger('change');
            }
        });
    }

    // Función para cargar jornadas
    function loadJornadas() {
        $.get('/api/jornadas', function(data) {
            const select = $('#jornada_id');
            select.find('option:not(:first)').remove();
            data.forEach(function(jornada) {
                select.append(new Option(jornada.name, jornada.id));
            });
            if (tieneSelect2) {
                select.trigger('change');
            }
        });
    }

    // Función para cargar instructores
    function loadInstructores() {
        $.get('/api/instructores', function(data) {
            const select = $('#instructor_id');
            select.find('option:not(:first)').remove();
            data.forEach(function(instructor) {
                select.append(new Option(
                    instructor.persona.primer_nombre + ' ' + instructor.persona.primer_apellido,
                    instructor.id
                ));
            });
            if (tieneSelect2) {
                select.trigger('change');
            }
        });
    }

    // Función para cargar sedes
    function loadSedes() {
        $.get('/api/sedes', function(data) {
            const select = $('#sede_id');
            select.find('option:not(:first)').remove();
            data.forEach(function(sede) {
                select.append(new Option(sede.nombre, sede.id));
            });
            if (tieneSelect2) {
                select.trigger('change');
            }
        });
    }

    // Confirmación de eliminación si es necesario
    window.confirmarEliminacion = function(nombre, url) {
        alertHandler.showCustomAlert({
            title: '¿Estás seguro?',
            text: `¿Deseas eliminar la ficha "${nombre}"? Esta acción no se puede deshacer.`,
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

    console.log('Formulario de fichas inicializado correctamente');
});
