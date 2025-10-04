@extends('adminlte::page')

@section('title', 'Gestionar Instructores - Ficha ' . $ficha->ficha)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-chalkboard-teacher text-primary"></i>
            Gestionar Instructores - Ficha {{ $ficha->ficha }}
        </h1>
        <div>
            <a href="{{ route('fichaCaracterizacion.show', $ficha->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver a la Ficha
            </a>
        </div>
    </div>
@stop

@section('content')
    <!-- Información de la Ficha -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i>
                        Información de la Ficha
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Programa:</strong><br>
                            <span class="text-muted">{{ $ficha->programaFormacion->nombre ?? 'No asignado' }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Fecha Inicio:</strong><br>
                            <span class="text-muted">{{ $ficha->fecha_inicio ? $ficha->fecha_inicio->format('d/m/Y') : 'No definida' }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Fecha Fin:</strong><br>
                            <span class="text-muted">{{ $ficha->fecha_fin ? $ficha->fecha_fin->format('d/m/Y') : 'No definida' }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Total Horas:</strong><br>
                            <span class="text-muted">{{ $ficha->total_horas ?? 'No definido' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Instructores Asignados -->
        <div class="col-md-6">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users text-success"></i>
                        Instructores Asignados
                    </h3>
                </div>
                <div class="card-body">
                    @if($instructoresAsignados->count() > 0)
                        @foreach($instructoresAsignados as $asignacion)
                            <div class="card mb-3 {{ $ficha->instructor_id == $asignacion->instructor_id ? 'border-primary' : '' }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h5 class="mb-1">
                                                {{ $asignacion->instructor->persona->primer_nombre }} 
                                                {{ $asignacion->instructor->persona->primer_apellido }}
                                                @if($ficha->instructor_id == $asignacion->instructor_id)
                                                    <span class="badge badge-primary ml-2">Principal</span>
                                                @else
                                                    <span class="badge badge-secondary ml-2">Auxiliar</span>
                                                @endif
                                            </h5>
                                            <p class="text-muted mb-2">
                                                <i class="fas fa-calendar"></i>
                                                {{ $asignacion->fecha_inicio->format('d/m/Y') }} - 
                                                {{ $asignacion->fecha_fin->format('d/m/Y') }}
                                            </p>
                                            <p class="text-muted mb-0">
                                                <i class="fas fa-clock"></i>
                                                {{ $asignacion->total_horas_instructor }} horas
                                            </p>
                                        </div>
                                        <div>
                                            @if($ficha->instructor_id != $asignacion->instructor_id)
                                                <form action="{{ route('fichaCaracterizacion.desasignarInstructor', [$ficha->id, $asignacion->instructor_id]) }}" 
                                                      method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                            onclick="return confirm('¿Está seguro de desasignar este instructor?')">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-user-slash fa-3x mb-3"></i>
                            <p>No hay instructores asignados a esta ficha.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Asignar Instructores -->
        <div class="col-md-6">
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-plus text-warning"></i>
                        Asignar Instructores
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('fichaCaracterizacion.asignarInstructores', $ficha->id) }}" method="POST" id="formAsignarInstructores">
                        @csrf
                        
                        <!-- Instructor Principal -->
                        <div class="form-group">
                            <label for="instructor_principal_id">
                                <i class="fas fa-star text-warning"></i>
                                Instructor Principal <span class="text-danger">*</span>
                            </label>
                            <select name="instructor_principal_id" id="instructor_principal_id" class="form-control select2" required>
                                <option value="">Seleccione un instructor principal</option>
                                @foreach($instructoresConDisponibilidad as $instructorId => $data)
                                    <option value="{{ $instructorId }}" 
                                            {{ $ficha->instructor_id == $instructorId ? 'selected' : '' }}
                                            {{ !$data['disponible'] ? 'disabled' : '' }}>
                                        {{ $data['instructor']->persona->primer_nombre }} 
                                        {{ $data['instructor']->persona->primer_apellido }}
                                        @if(!$data['disponible'])
                                            (No disponible - {{ $data['fichas_superpuestas'] }} fichas superpuestas)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('instructor_principal_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Información de fechas permitidas -->
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Rango de fechas permitidas:</strong>
                            @if($ficha->fecha_inicio && $ficha->fecha_fin)
                                Desde {{ $ficha->fecha_inicio->format('d/m/Y') }} hasta {{ $ficha->fecha_fin->format('d/m/Y') }}
                            @else
                                <span class="text-warning">Las fechas de la ficha no están definidas</span>
                            @endif
                        </div>

                        <!-- Información de días de formación -->
                        @if($diasFormacionFicha->count() > 0)
                            <div class="alert alert-success">
                                <i class="fas fa-calendar-check"></i>
                                <strong>Días de formación disponibles:</strong>
                                {{ $diasFormacionFicha->pluck('name')->implode(', ') }}
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Advertencia:</strong> Esta ficha no tiene días de formación asignados. 
                                <a href="{{ route('fichaCaracterizacion.gestionarDiasFormacion', $ficha->id) }}" class="alert-link">
                                    Asignar días de formación
                                </a>
                            </div>
                        @endif

                        <!-- Lista de Instructores -->
                        <div class="form-group">
                            <label>
                                <i class="fas fa-users"></i>
                                Instructores Asignados <span class="text-danger">*</span>
                            </label>
                            <div id="instructores-container">
                                <!-- Los instructores se agregarán dinámicamente aquí -->
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="agregarInstructor()">
                                <i class="fas fa-plus"></i> Agregar Instructor
                            </button>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Asignaciones
                            </button>
                            <a href="{{ route('fichaCaracterizacion.show', $ficha->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Instructores Disponibles -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list text-info"></i>
                        Instructores Disponibles
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Disponibilidad</th>
                                    <th>Fichas Superpuestas</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($instructoresConDisponibilidad as $instructorId => $data)
                                    <tr class="{{ $data['disponible'] ? '' : 'table-warning' }}">
                                        <td>
                                            {{ $data['instructor']->persona->primer_nombre }} 
                                            {{ $data['instructor']->persona->primer_apellido }}
                                        </td>
                                        <td>
                                            @if($data['disponible'])
                                                <span class="badge badge-success">Disponible</span>
                                            @else
                                                <span class="badge badge-danger">No disponible</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $data['fichas_superpuestas'] }}</span>
                                        </td>
                                        <td>
                                            @if($data['disponible'])
                                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                                        onclick="agregarInstructorSeleccionado({{ $instructorId }})">
                                                    <i class="fas fa-plus"></i> Agregar
                                                </button>
                                            @else
                                                <span class="text-muted">No disponible</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@stop

@section('js')
    <script src="{{ asset('vendor/adminlte/plugins/select2/js/select2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Inicializar Select2
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // Cargar instructores existentes
            cargarInstructoresExistentes();
        });

        // Función para cargar instructores ya asignados
        function cargarInstructoresExistentes() {
            @if($instructoresAsignados->count() > 0)
                @foreach($instructoresAsignados as $asignacion)
                    @php
                        $diasFormacion = $asignacion->instructorFichaDias->map(function($dia) {
                            return [
                                'dia_id' => $dia->dia_id
                            ];
                        })->toArray();
                    @endphp
                    agregarInstructorRow(
                        {{ $asignacion->instructor_id }},
                        '{{ $asignacion->instructor->persona->primer_nombre }} {{ $asignacion->instructor->persona->primer_apellido }}',
                        '{{ $asignacion->fecha_inicio->format('Y-m-d') }}',
                        '{{ $asignacion->fecha_fin->format('Y-m-d') }}',
                        {{ $asignacion->total_horas_instructor }},
                        {{ $ficha->instructor_id == $asignacion->instructor_id ? 'true' : 'false' }},
                        @json($diasFormacion)
                    );
                @endforeach
            @endif
        }

        // Función para agregar un instructor desde el botón
        function agregarInstructor() {
            agregarInstructorRow(null, '', '', '', '', false);
        }

        // Función para agregar un instructor seleccionado de la tabla
        function agregarInstructorSeleccionado(instructorId) {
            // Buscar el instructor en los datos disponibles
            const instructores = @json($instructoresConDisponibilidad);
            const instructor = instructores[instructorId];
            
            if (instructor && instructor.disponible) {
                const nombre = instructor.instructor.persona.primer_nombre + ' ' + instructor.instructor.persona.primer_apellido;
                agregarInstructorRow(instructorId, nombre, '', '', '', false);
            }
        }

        // Función para agregar una fila de instructor
        function agregarInstructorRow(instructorId, nombre, fechaInicio, fechaFin, horas, esPrincipal, diasFormacion = []) {
            const container = document.getElementById('instructores-container');
            const index = container.children.length;
            
            const div = document.createElement('div');
            div.className = 'card mb-2 instructor-row';
            div.innerHTML = `
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5">
                            <label class="form-label">Instructor</label>
                            <select name="instructores[${index}][instructor_id]" class="form-control select2 instructor-select" required>
                                <option value="">Seleccione un instructor</option>
                                @foreach($instructoresConDisponibilidad as $id => $data)
                                    <option value="{{ $id }}" ${instructorId == {{ $id }} ? 'selected' : ''}>
                                        {{ $data['instructor']->persona->primer_nombre }} 
                                        {{ $data['instructor']->persona->primer_apellido }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Fecha Inicio</label>
                            <input type="date" name="instructores[${index}][fecha_inicio]" 
                                   class="form-control" value="${fechaInicio}" 
                                   min="{{ $ficha->fecha_inicio ? $ficha->fecha_inicio->format('Y-m-d') : '' }}"
                                   max="{{ $ficha->fecha_fin ? $ficha->fecha_fin->format('Y-m-d') : '' }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Fecha Fin</label>
                            <input type="date" name="instructores[${index}][fecha_fin]" 
                                   class="form-control" value="${fechaFin}" 
                                   min="{{ $ficha->fecha_inicio ? $ficha->fecha_inicio->format('Y-m-d') : '' }}"
                                   max="{{ $ficha->fecha_fin ? $ficha->fecha_fin->format('Y-m-d') : '' }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Horas</label>
                            <input type="number" name="instructores[${index}][total_horas_instructor]" 
                                   class="form-control" value="${horas}" min="1" required>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-sm btn-danger d-block" onclick="eliminarInstructor(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <!-- Días de Formación -->
                    <div class="row mt-2">
                        <div class="col-12">
                            <label class="form-label">
                                <i class="fas fa-calendar-week"></i>
                                Días de Formación
                            </label>
                            <div class="dias-formacion-container" data-index="${index}">
                                <!-- Los días se agregarán dinámicamente aquí -->
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-success mt-1" onclick="agregarDiaFormacion(${index})" {{ $diasFormacionFicha->count() == 0 ? 'disabled' : '' }}>
                                <i class="fas fa-plus"></i> Agregar Día
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            container.appendChild(div);
            
            // Inicializar Select2 en el nuevo elemento
            $(div).find('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
            
            // Agregar validación de fechas
            const fechaInicioInput = div.querySelector('input[name*="[fecha_inicio]"]');
            const fechaFinInput = div.querySelector('input[name*="[fecha_fin]"]');
            
            fechaInicioInput.addEventListener('change', function() {
                fechaFinInput.min = this.value;
                if (fechaFinInput.value && fechaFinInput.value < this.value) {
                    fechaFinInput.value = this.value;
                }
            });
            
            fechaFinInput.addEventListener('change', function() {
                fechaInicioInput.max = this.value;
                if (fechaInicioInput.value && fechaInicioInput.value > this.value) {
                    fechaInicioInput.value = this.value;
                }
            });
            
            // Cargar días de formación existentes si los hay
            if (diasFormacion && diasFormacion.length > 0) {
                diasFormacion.forEach(dia => {
                    agregarDiaFormacionRow(index, dia.dia_id);
                });
            }
        }

        // Función para eliminar un instructor
        function eliminarInstructor(button) {
            const row = button.closest('.instructor-row');
            row.remove();
            
            // Renumerar los índices
            renumerarIndices();
        }

        // Función para renumerar los índices de los instructores
        function renumerarIndices() {
            const container = document.getElementById('instructores-container');
            const rows = container.querySelectorAll('.instructor-row');
            
            rows.forEach((row, index) => {
                // Actualizar los nombres de los campos
                const inputs = row.querySelectorAll('input, select');
                inputs.forEach(input => {
                    const name = input.getAttribute('name');
                    if (name) {
                        const newName = name.replace(/\[\d+\]/, `[${index}]`);
                        input.setAttribute('name', newName);
                    }
                });
            });
        }

        // Función para agregar un día de formación
        function agregarDiaFormacion(instructorIndex) {
            const diasDisponibles = @json($diasFormacionFicha);
            if (diasDisponibles.length === 0) {
                alert('No hay días de formación asignados a esta ficha. Debe asignar días de formación primero.');
                return;
            }
            agregarDiaFormacionRow(instructorIndex, '');
        }

        // Función para agregar una fila de día de formación
        function agregarDiaFormacionRow(instructorIndex, diaId) {
            const container = document.querySelector(`[data-index="${instructorIndex}"]`);
            const diaIndex = container.children.length;
            
            const div = document.createElement('div');
            div.className = 'row mb-2 dia-formacion-row';
            div.innerHTML = `
                <div class="col-md-10">
                    <select name="instructores[${instructorIndex}][dias_formacion][${diaIndex}][dia_id]" class="form-control select2" required>
                        <option value="">Seleccione día</option>
                        @foreach($diasFormacionFicha as $dia)
                            <option value="{{ $dia->id }}" ${diaId == {{ $dia->id }} ? 'selected' : ''}>
                                {{ $dia->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-sm btn-danger" onclick="eliminarDiaFormacion(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            
            container.appendChild(div);
            
            // Inicializar Select2 en el nuevo elemento
            $(div).find('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
        }

        // Función para eliminar un día de formación
        function eliminarDiaFormacion(button) {
            const row = button.closest('.dia-formacion-row');
            row.remove();
        }

        // Validación del formulario
        document.getElementById('formAsignarInstructores').addEventListener('submit', function(e) {
            const instructorPrincipal = document.getElementById('instructor_principal_id').value;
            const instructoresAsignados = document.querySelectorAll('.instructor-select');
            
            if (!instructorPrincipal) {
                e.preventDefault();
                alert('Debe seleccionar un instructor principal.');
                return;
            }
            
            if (instructoresAsignados.length === 0) {
                e.preventDefault();
                alert('Debe asignar al menos un instructor.');
                return;
            }
            
            // Verificar que el instructor principal esté en la lista
            let instructorPrincipalEnLista = false;
            instructoresAsignados.forEach(select => {
                if (select.value === instructorPrincipal) {
                    instructorPrincipalEnLista = true;
                }
            });
            
            if (!instructorPrincipalEnLista) {
                e.preventDefault();
                alert('El instructor principal debe estar en la lista de instructores asignados.');
                return;
            }
            
            // Validar fechas de instructores
            const fechaInicioFicha = '{{ $ficha->fecha_inicio ? $ficha->fecha_inicio->format("Y-m-d") : "" }}';
            const fechaFinFicha = '{{ $ficha->fecha_fin ? $ficha->fecha_fin->format("Y-m-d") : "" }}';
            
            const fechaInicioInputs = document.querySelectorAll('input[name*="[fecha_inicio]"]');
            const fechaFinInputs = document.querySelectorAll('input[name*="[fecha_fin]"]');
            
            for (let i = 0; i < fechaInicioInputs.length; i++) {
                const fechaInicio = fechaInicioInputs[i].value;
                const fechaFin = fechaFinInputs[i].value;
                
                if (fechaInicioFicha && fechaInicio < fechaInicioFicha) {
                    e.preventDefault();
                    alert(`La fecha de inicio del instructor ${i + 1} debe ser posterior o igual a ${fechaInicioFicha}.`);
                    return;
                }
                
                if (fechaFinFicha && fechaFin > fechaFinFicha) {
                    e.preventDefault();
                    alert(`La fecha de fin del instructor ${i + 1} debe ser anterior o igual a ${fechaFinFicha}.`);
                    return;
                }
                
                if (fechaInicio > fechaFin) {
                    e.preventDefault();
                    alert(`La fecha de inicio del instructor ${i + 1} debe ser anterior o igual a la fecha de fin.`);
                    return;
                }
            }
        });
    </script>
@stop
