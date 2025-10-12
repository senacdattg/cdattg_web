@extends('adminlte::page')

@section('title', 'Gestión de Programas de Formación')

@section('content_header')
    <div class="content-header d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-graduation-cap me-3"></i>Gestión de Programas de Formación</h1>
            <p>Administre los programas de formación complementaria disponibles</p>
        </div>
        <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#newProgramModal">
            <i class="fas fa-plus-circle"></i> Nuevo Programa
        </a>
    </div>
@stop

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Search and Filters -->
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" placeholder="Buscar programa por nombre o código"
                            aria-label="Search">
                    </div>
                </div>
                <div class="col-md-4">
                    <select class="custom-select mb-3" aria-label="Estado">
                        <option selected value="">Todos los estados</option>
                        <option value="activo">Con Oferta</option>
                        <option value="inactivo">Sin Oferta</option>
                        <option value="completo">Cupos llenos</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary">Todos</button>
                        <button type="button" class="btn btn-outline-success">Con cupos disponibles</button>
                        <button type="button" class="btn btn-outline-warning">Próximos a iniciar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Programs Cards View -->
    <div class="row row-cols-1 row-cols-md-3 g-4 mb-4">
        @forelse($programas as $programa)
        <div class="col mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="h1 text-primary mb-3">
                        <i class="{{ $programa->icono ?? 'fas fa-graduation-cap' }}"></i>
                    </div>
                    <div class="d-flex justify-content-center">
                        <h5 class="card-title font-weight-bold">{{ $programa->nombre }}</h5>
                    </div>
                    <span class="badge {{ $programa->badge_class }} mb-2 w-20 text-center">{{ $programa->estado_label }}</span>
                    <p class="card-text">{{ $programa->descripcion }}</p>
                    <div class="d-flex justify-content-center mt-3 pt-3 border-top">
                        <div>
                            <small class="text-muted">Duración</small>
                            <p class="mb-0"><strong>{{ $programa->duracion }} horas</strong></p>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <button class="btn btn-sm btn-outline-primary me-md-2 mr-2" onclick="viewPrograma({{ $programa->id }})">
                            <i class="fas fa-eye"></i> Ver
                        </button>
                        <button class="btn btn-sm btn-outline-warning me-md-2 mr-2" onclick="editPrograma({{ $programa->id }})">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deletePrograma({{ $programa->id }})">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <p class="text-center">No hay programas disponibles.</p>
        </div>
        @endforelse
    </div>

    <!-- New Program Modal -->
    <div class="modal fade" id="newProgramModal" tabindex="-1" aria-labelledby="newProgramModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newProgramModalLabel">
                        <i class="fas fa-plus-circle me-3"></i>Nuevo Programa de Formación
                    </h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Complete el formulario para registrar un nuevo programa de formación</p>
                    <form id="newProgramForm">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre del Programa</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="codigo" class="form-label">Código del Programa</label>
                            <input type="text" class="form-control" id="codigo" name="codigo" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="duracion" class="form-label">Duración (horas)</label>
                                    <input type="number" class="form-control" id="duracion"
                                        name="duracion" required min="1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cupos" class="form-label">Cupos</label>
                                    <input type="number" class="form-control" id="cupos"
                                        name="cupos" required min="1">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="modalidad_id" class="form-label">Modalidad</label>
                            <select class="form-select" id="modalidad_id" name="modalidad_id" required>
                                @foreach($modalidades as $mod)
                                <option value="{{ $mod->id }}">{{ $mod->parametro->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jornada_id" class="form-label">Jornada</label>
                            <select class="form-select" id="jornada_id" name="jornada_id" required>
                                @foreach($jornadas as $jor)
                                <option value="{{ $jor->id }}">{{ $jor->jornada }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-select" id="estado" name="estado" required>
                                <option value="0">Sin Oferta</option>
                                <option value="1">Con Oferta</option>
                                <option value="2">Cupos Llenos</option>
                            </select>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Programa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Program Modal -->
    <div class="modal fade" id="editProgramModal" tabindex="-1" aria-labelledby="editProgramModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProgramModalLabel">
                        <i class="fas fa-edit me-3"></i>Editar Programa de Formación
                    </h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Modifique los datos del programa de formación</p>
                    <form id="editProgramForm">
                        <input type="hidden" id="edit_programa_id" name="programa_id">
                        <div class="mb-3">
                            <label for="edit_nombre" class="form-label">Nombre del Programa</label>
                            <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_codigo" class="form-label">Código del Programa</label>
                            <input type="text" class="form-control" id="edit_codigo" name="codigo" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="edit_descripcion" name="descripcion" rows="3" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_duracion" class="form-label">Duración (horas)</label>
                                    <input type="number" class="form-control" id="edit_duracion"
                                        name="duracion" required min="1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_cupos" class="form-label">Cupos</label>
                                    <input type="number" class="form-control" id="edit_cupos"
                                        name="cupos" required min="1">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_modalidad_id" class="form-label">Modalidad</label>
                            <select class="form-select" id="edit_modalidad_id" name="modalidad_id" required>
                                @foreach($modalidades as $mod)
                                <option value="{{ $mod->id }}">{{ $mod->parametro->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_jornada_id" class="form-label">Jornada</label>
                            <select class="form-select" id="edit_jornada_id" name="jornada_id" required>
                                @foreach($jornadas as $jor)
                                <option value="{{ $jor->id }}">{{ $jor->jornada }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_estado" class="form-label">Estado</label>
                            <select class="form-select" id="edit_estado" name="estado" required>
                                <option value="0">Sin Oferta</option>
                                <option value="1">Con Oferta</option>
                                <option value="2">Cupos Llenos</option>
                            </select>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar Programa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- View Program Modal -->
    <div class="modal fade" id="viewProgramModal" tabindex="-1" aria-labelledby="viewProgramModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewProgramModalLabel">
                        <i class="fas fa-eye me-3"></i>Detalles del Programa de Formación
                    </h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nombre del Programa</label>
                                <p id="view_nombre" class="form-control-plaintext"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Código del Programa</label>
                                <p id="view_codigo" class="form-control-plaintext"></p>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción</label>
                        <p id="view_descripcion" class="form-control-plaintext"></p>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Duración (horas)</label>
                                <p id="view_duracion" class="form-control-plaintext"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Cupos</label>
                                <p id="view_cupos" class="form-control-plaintext"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Modalidad</label>
                                <p id="view_modalidad" class="form-control-plaintext"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Jornada</label>
                                <p id="view_jornada" class="form-control-plaintext"></p>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Estado</label>
                        <p id="view_estado" class="form-control-plaintext"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Días de Formación</label>
                        <p id="view_dias" class="form-control-plaintext"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script>
        console.log("AdminLTE programs: bootstrapping");
        document.addEventListener('DOMContentLoaded', function() {
<<<<<<< HEAD
            console.log("DOM loaded for programs view");
            
            // Ensure bootstrap JS is loaded (Bootstrap 5)
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                console.log("Bootstrap modal available");
            } else {@section('js')
    <script>
        console.log("AdminLTE programs: bootstrapping");
        document.addEventListener('DOMContentLoaded', function() {
<<<<<<< HEAD
            console.log("DOM loaded for programs view");
            
            // Ensure jQuery and Bootstrap modal are loaded
            if (typeof $ !== 'undefined' && $.fn.modal) {
                console.log("Bootstrap modal available");
            } else {
                console.warn("Bootstrap modal not available.");
            }

            // Store reference to the button that opens the modal
            let newProgramButton = null;
            let previousActiveElement = null;

            // Log button click for debugging and store reference
            const newProgramButtons = document.querySelectorAll('[data-target="#newProgramModal"]');
            if (newProgramButtons.length > 0) {
                newProgramButton = newProgramButtons[0];
                newProgramButton.addEventListener('click', function() {
                    console.log('Nuevo Programa button clicked');
                });
            }

            // Handle modal focus management
            $('#newProgramModal').on('show.bs.modal', function() {
                previousActiveElement = document.activeElement;
            }).on('hidden.bs.modal', function() {
                // Restore focus to the element that opened the modal
                if (newProgramButton) {
                    $(newProgramButton).focus();
                } else if (previousActiveElement) {
                    $(previousActiveElement).focus();
                }

                // Ensure no element inside the hidden modal retains focus
                const focusedElement = document.activeElement;
                if (focusedElement && $(this).find(focusedElement).length) {
                    $(focusedElement).blur();
                }
            }).on('keydown', function(e) {
                if (e.key === 'Tab' && !$(this).hasClass('show')) {
                    e.preventDefault();
                }
            });

            // Handle form submission
            const newProgramForm = document.getElementById('newProgramForm');
            if (newProgramForm) {
                newProgramForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(newProgramForm);
                    fetch('{{ route("complementarios-ofertados.store") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
<<<<<<< HEAD
                    }
                    newProgramForm.reset();
=======
            const newProgramForm = document.getElementById('newProgramForm');

            if (newProgramForm) {
                newProgramForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Aquí iría la lógica para guardar el programa
                    // Por ahora, mostramos un mensaje de éxito y cerramos la modal
                    alert('Programa guardado exitosamente');

                    // Cerrar la modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('newProgramModal'));
                    modal.hide();

                    // Limpiar el formulario
                    newProgramForm.reset();

                    // Aquí podrías recargar la página o actualizar la lista de programas
                    // window.location.reload();
                });
            }
                console.warn("Bootstrap modal not available. Bootstrap 5 JS may not be loaded.");
            }

            // Store reference to the button that opens the modal
            let newProgramButton = null;

            // Log button click for debugging and store reference
            const newProgramButtons = document.querySelectorAll('[data-bs-target="#newProgramModal"]');
            if (newProgramButtons.length > 0) {
                newProgramButton = newProgramButtons[0];
                newProgramButton.addEventListener('click', function() {
                    console.log('Nuevo Programa button clicked');
                });
            }

            // Handle modal focus management
            const newProgramModalEl = document.getElementById('newProgramModal');
            if (newProgramModalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                // Store the element that had focus before opening the modal
                let previousActiveElement = null;
                
                newProgramModalEl.addEventListener('show.bs.modal', function() {
                    previousActiveElement = document.activeElement;
                });

                newProgramModalEl.addEventListener('hidden.bs.modal', function() {
                    // Restore focus to the element that opened the modal
                    if (newProgramButton && typeof newProgramButton.focus === 'function') {
                        newProgramButton.focus();
                    } else if (previousActiveElement && typeof previousActiveElement.focus === 'function') {
                        previousActiveElement.focus();
                    }
                    
                    // Ensure no element inside the hidden modal retains focus
                    const focusedElement = document.activeElement;
                    if (focusedElement && newProgramModalEl.contains(focusedElement)) {
                        focusedElement.blur();
                    }
                });

                // Prevent focus from being trapped in hidden modal
                newProgramModalEl.addEventListener('keydown', function(e) {
                    if (e.key === 'Tab' && !newProgramModalEl.classList.contains('show')) {
                        e.preventDefault();
                    }
                });
            }

            // Handle form submission
            const newProgramForm = document.getElementById('newProgramForm');
            if (newProgramForm) {
                newProgramForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    alert('Programa guardado exitosamente');
                    
                    // Close modal and handle focus properly
                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('newProgramModal'));
                        if (modal) {
                            modal.hide();
                            // Focus will be handled by the hidden.bs.modal event
                        }
                    }
                    newProgramForm.reset();

=======
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload(); // Reload to show new program
                        } else {
                            alert('Error: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while saving the program.');
                    });
>>>>>>> ac5de14 (Configurar vista de complementarios ofertados y conectarla con database)
                });
            }

            // Handle edit form submission
            const editProgramForm = document.getElementById('editProgramForm');
            if (editProgramForm) {
                editProgramForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const programaId = document.getElementById('edit_programa_id').value;
                    const formData = new FormData(editProgramForm);
                    fetch('{{ route("complementarios-ofertados.update", ":id") }}'.replace(':id', programaId), {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-HTTP-Method-Override': 'PUT'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload(); // Reload to show updated program
                        } else {
                            alert('Error: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while updating the program.');
                    });
                });
            }

            // Function to view program
            window.viewPrograma = function(id) {
                fetch('{{ route("complementarios-ofertados.edit", ":id") }}'.replace(':id', id), {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Populate the view modal with data
                    document.getElementById('view_nombre').textContent = data.nombre;
                    document.getElementById('view_codigo').textContent = data.codigo;
                    document.getElementById('view_descripcion').textContent = data.descripcion;
                    document.getElementById('view_duracion').textContent = data.duracion;
                    document.getElementById('view_cupos').textContent = data.cupos;

                    // Get modality name
                    const modalidades = @json($modalidades);
                    const modalidad = modalidades.find(m => m.id == data.modalidad_id);
                    document.getElementById('view_modalidad').textContent = modalidad ? modalidad.parametro.name : 'N/A';

                    // Get jornada name
                    const jornadas = @json($jornadas);
                    const jornada = jornadas.find(j => j.id == data.jornada_id);
                    document.getElementById('view_jornada').textContent = jornada ? jornada.jornada : 'N/A';

                    // Estado
                    const estados = {0: 'Sin Oferta', 1: 'Con Oferta', 2: 'Cupos Llenos'};
                    document.getElementById('view_estado').textContent = estados[data.estado] || 'N/A';

                    // Dias
                    if (data.dias && data.dias.length > 0) {
                        const diasText = data.dias.map(dia => {
                            const diaName = modalidades.find(m => m.id == dia.dia_id)?.parametro.name || 'N/A';
                            return `${diaName} (${dia.hora_inicio} - ${dia.hora_fin})`;
                        }).join(', ');
                        document.getElementById('view_dias').textContent = diasText;
                    } else {
                        document.getElementById('view_dias').textContent = 'No especificado';
                    }

                    // Show the view modal
                    $('#viewProgramModal').modal('show');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while loading the program data.');
                });
            };

            // Function to edit program
            window.editPrograma = function(id) {
                fetch('{{ route("complementarios-ofertados.edit", ":id") }}'.replace(':id', id), {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Populate the edit modal with data
                    document.getElementById('edit_programa_id').value = data.id;
                    document.getElementById('edit_nombre').value = data.nombre;
                    document.getElementById('edit_codigo').value = data.codigo;
                    document.getElementById('edit_descripcion').value = data.descripcion;
                    document.getElementById('edit_duracion').value = data.duracion;
                    document.getElementById('edit_cupos').value = data.cupos;
                    document.getElementById('edit_modalidad_id').value = data.modalidad_id;
                    document.getElementById('edit_jornada_id').value = data.jornada_id;
                    document.getElementById('edit_estado').value = data.estado;

                    // Show the edit modal
                    $('#editProgramModal').modal('show');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while loading the program data.');
                });
            };

            // Function to delete program
            window.deletePrograma = function(id) {
                if (confirm('¿Estás seguro de que quieres eliminar este programa?')) {
                    fetch('{{ route("complementarios-ofertados.destroy", ":id") }}'.replace(':id', id), {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
                            alert('Error: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while deleting the program.');
                    });
                }
            };
        });
    </script>
@stop
