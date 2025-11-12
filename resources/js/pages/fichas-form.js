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

    if (tieneSelect2) {
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
    });

    // Permitir cualquier fecha de inicio (sin restricción de fecha mínima)
    // $('#fecha_inicio').attr('min', new Date().toISOString().split('T')[0]);

    // Manejo de días de formación
    function manejarDiasFormacion() {
        const diasCheckboxes = $('.dia-formacion-checkbox');
        const horariosContainer = $('#horarios-container');
        const horariosDias = $('#horarios-dias');
        
        // Objeto para almacenar los valores de horarios
        const valoresHorarios = {};
        
        // Función para guardar valores actuales antes de regenerar
        function guardarValoresActuales() {
            horariosDias.find('.card').each(function() {
                const cardId = $(this).parent().attr('id');
                if (cardId) {
                    const diaId = cardId.replace('horario-dia-', '');
                    const horaInicio = $(this).find('input[name*="hora_inicio"]').val();
                    const horaFin = $(this).find('input[name*="hora_fin"]').val();
                    
                    if (horaInicio && horaFin) {
                        valoresHorarios[diaId] = {
                            hora_inicio: horaInicio,
                            hora_fin: horaFin
                        };
                    }
                }
            });
        }
        
        // Función para generar horarios para un día específico
        function generarHorariosDia(diaId, diaNombre) {
            // Usar valores guardados o valores por defecto
            const horaInicio = valoresHorarios[diaId]?.hora_inicio || '08:00';
            const horaFin = valoresHorarios[diaId]?.hora_fin || '16:00';
            
            return `
                <div class="col-md-6 col-lg-4 mb-3" id="horario-dia-${diaId}">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white py-2">
                            <h6 class="mb-0"><i class="fas fa-calendar-day"></i> ${diaNombre}</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-6">
                                    <label class="form-label small">Hora Inicio</label>
                                    <input type="time" class="form-control form-control-sm hora-inicio-input" 
                                           name="horarios[${diaId}][hora_inicio]" 
                                           value="${horaInicio}" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small">Hora Fin</label>
                                    <input type="time" class="form-control form-control-sm hora-fin-input" 
                                           name="horarios[${diaId}][hora_fin]" 
                                           value="${horaFin}" required>
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
            
            if (diasSeleccionados.length > 0) {
                horariosContainer.show();
                horariosDias.empty();
                
                diasSeleccionados.each(function() {
                    const diaId = $(this).val();
                    const diaNombre = $(this).next('label').text().trim();
                    const horarioHTML = generarHorariosDia(diaId, diaNombre);
                    horariosDias.append(horarioHTML);
                });
            } else {
                horariosContainer.hide();
                horariosDias.empty();
            }
        }
        
        // Event listener para cambios en checkboxes
        diasCheckboxes.on('change', function() {
            actualizarHorarios();
        });
        
        // Guardar valores cuando el usuario los cambia
        $(document).on('change', '.hora-inicio-input, .hora-fin-input', function() {
            guardarValoresActuales();
        });
        
        // Validación de horarios
        $(document).on('change', 'input[name*="[hora_inicio]"], input[name*="[hora_fin]"]', function() {
            const horaInicio = $(this).closest('.card-body').find('input[name*="[hora_inicio]"]').val();
            const horaFin = $(this).closest('.card-body').find('input[name*="[hora_fin]"]').val();
            
            if (horaInicio && horaFin) {
                if (horaInicio >= horaFin) {
                    $(this)[0].setCustomValidity('La hora de fin debe ser posterior a la hora de inicio');
                    $(this).addClass('is-invalid');
                } else {
                    $(this)[0].setCustomValidity('');
                    $(this).removeClass('is-invalid');
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
            document.querySelectorAll('input[name*="horarios"]').forEach(input => {
                console.log('Input horario:', input.name, '=', input.value);
                horariosData[input.name] = input.value;
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
                invalidElements.forEach(function(element) {
                    console.log('Campo inválido:', element.name, 'Error:', element.validationMessage);
                });
                $(this).addClass('was-validated');
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
        loadModalidades();
        loadJornadas();
        loadInstructores();
        loadSedes();
        
        // Establecer valores actuales con delay para asegurar que los selects estén cargados
        setTimeout(() => {
            // Los valores se establecerán desde el HTML con data attributes
            const sedeId = $('#sede_id').data('initial-value');
            const instructorId = $('#instructor_id').data('initial-value');
            const modalidadId = $('#modalidad_formacion_id').data('initial-value');
            const jornadaId = $('#jornada_id').data('initial-value');
            const ambienteId = $('#ambiente_id').data('initial-value');
            
            if (sedeId) {
                $('#sede_id').val(sedeId).trigger('change');
                loadAmbientesPorSede(sedeId);
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
            
            if (ambienteId) {
                setTimeout(() => {
                    $('#ambiente_id').val(ambienteId).trigger('change');
                }, 500);
            }
        }, 1000);
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
