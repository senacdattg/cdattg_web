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
                    console.log('Ambientes cargados:', response.data.length);
                } else {
                    ambienteSelect.html('<option value="">Error al cargar ambientes</option>');
                    console.error('Error al cargar ambientes:', response.message);
                }
            },
            error: function(xhr, status, error) {
                ambienteSelect.html('<option value="">Error al cargar ambientes</option>');
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
                $('#sede_id').val(sedeId);
                loadAmbientesPorSede(sedeId);
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
        
        if (sedeId) {
            loadAmbientesPorSede(sedeId);
        } else {
            ambienteSelect.html('<option value="">Primero seleccione una sede...</option>');
        }
    });

    // Validación de fechas
    $('#fecha_inicio, #fecha_fin').change(function() {
        validateDates();
    });

    // Establecer fecha mínima como hoy
    $('#fecha_inicio').attr('min', new Date().toISOString().split('T')[0]);

    // Validación de formulario
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(event) {
            console.log('=== FORMULARIO INTERCEPTADO ===');
            console.log('Formulario válido:', this.checkValidity());
            
            // Validar fechas
            validateDates();
            
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
                $('#sede_id').val(sedeId);
                loadAmbientesPorSede(sedeId);
            }
            
            if (instructorId) {
                $('#instructor_id').val(instructorId);
            }
            
            if (modalidadId) {
                $('#modalidad_formacion_id').val(modalidadId);
            }
            
            if (jornadaId) {
                $('#jornada_id').val(jornadaId);
            }
            
            if (ambienteId) {
                setTimeout(() => {
                    $('#ambiente_id').val(ambienteId);
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
