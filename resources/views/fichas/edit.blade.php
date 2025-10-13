@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-edit" 
        title="Editar Ficha de Caracterización"
        subtitle="Modificar información de la ficha"
        :breadcrumb="[['label' => 'Fichas de Caracterización', 'url' => '{{ route('fichaCaracterizacion.index') }}', 'icon' => 'fa-file-alt'], ['label' => 'Editar', 'icon' => 'fa-edit', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3 d-flex align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary d-flex flex-grow-1">
                                <i class="fas fa-file-alt mr-2"></i> Información de la Ficha: {{ $ficha->ficha }}
                            </h6>
                        </div>

                <form action="{{ route('fichaCaracterizacion.update', $ficha->id) }}" method="POST" id="formEditFicha">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <!-- Número de Ficha -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ficha">
                                        <i class="fas fa-hashtag"></i> Número de Ficha <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('ficha') is-invalid @enderror" 
                                           id="ficha" name="ficha" value="{{ old('ficha', $ficha->ficha) }}" 
                                           placeholder="Ej: 123456" maxlength="50" required>
                                    @error('ficha')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Programa de Formación -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="programa_formacion_id">
                                        <i class="fas fa-graduation-cap"></i> Programa de Formación <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control @error('programa_formacion_id') is-invalid @enderror" 
                                            id="programa_formacion_id" name="programa_formacion_id" required>
                                        <option value="">Seleccione un programa...</option>
                                        @foreach($programas as $programa)
                                            <option value="{{ $programa->id }}" 
                                                    {{ old('programa_formacion_id', $ficha->programa_formacion_id) == $programa->id ? 'selected' : '' }}
                                                    data-sede="{{ $programa->sede_id }}">
                                                {{ $programa->nombre }} ({{ $programa->codigo }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('programa_formacion_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Fecha de Inicio -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha_inicio">
                                        <i class="fas fa-calendar-alt"></i> Fecha de Inicio <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control @error('fecha_inicio') is-invalid @enderror" 
                                           id="fecha_inicio" name="fecha_inicio" 
                                           value="{{ old('fecha_inicio', $ficha->fecha_inicio?->format('Y-m-d')) }}" required>
                                    @error('fecha_inicio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Fecha de Fin -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha_fin">
                                        <i class="fas fa-calendar-alt"></i> Fecha de Fin <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control @error('fecha_fin') is-invalid @enderror" 
                                           id="fecha_fin" name="fecha_fin" 
                                           value="{{ old('fecha_fin', $ficha->fecha_fin?->format('Y-m-d')) }}" required>
                                    @error('fecha_fin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Sede -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sede_id">
                                        <i class="fas fa-building"></i> Sede
                                    </label>
                                    <select class="form-control @error('sede_id') is-invalid @enderror" 
                                            id="sede_id" name="sede_id">
                                        <option value="">Seleccione una sede...</option>
                                        <!-- Se llenará dinámicamente -->
                                    </select>
                                    @error('sede_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Instructor -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="instructor_id">
                                        <i class="fas fa-chalkboard-teacher"></i> Instructor Principal
                                    </label>
                                    <select class="form-control @error('instructor_id') is-invalid @enderror" 
                                            id="instructor_id" name="instructor_id">
                                        <option value="">Seleccione un instructor...</option>
                                        <!-- Se llenará dinámicamente -->
                                    </select>
                                    @error('instructor_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Modalidad de Formación -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="modalidad_formacion_id">
                                        <i class="fas fa-laptop"></i> Modalidad de Formación
                                    </label>
                                    <select class="form-control @error('modalidad_formacion_id') is-invalid @enderror" 
                                            id="modalidad_formacion_id" name="modalidad_formacion_id">
                                        <option value="">Seleccione una modalidad...</option>
                                        <!-- Se llenará dinámicamente -->
                                    </select>
                                    @error('modalidad_formacion_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Jornada -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="jornada_id">
                                        <i class="fas fa-clock"></i> Jornada de Formación
                                    </label>
                                    <select class="form-control @error('jornada_id') is-invalid @enderror" 
                                            id="jornada_id" name="jornada_id">
                                        <option value="">Seleccione una jornada...</option>
                                        <!-- Se llenará dinámicamente -->
                                    </select>
                                    @error('jornada_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Ambiente -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ambiente_id">
                                        <i class="fas fa-door-open"></i> Ambiente
                                    </label>
                                    <select class="form-control @error('ambiente_id') is-invalid @enderror" 
                                            id="ambiente_id" name="ambiente_id">
                                        <option value="">Seleccione un ambiente...</option>
                                        <!-- Se llenará dinámicamente -->
                                    </select>
                                    @error('ambiente_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Total de Horas -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="total_horas">
                                        <i class="fas fa-hourglass-half"></i> Total de Horas
                                    </label>
                                    <input type="number" class="form-control @error('total_horas') is-invalid @enderror" 
                                           id="total_horas" name="total_horas" 
                                           value="{{ old('total_horas', $ficha->total_horas) }}" 
                                           min="1" max="9999" placeholder="Ej: 120">
                                    @error('total_horas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Estado -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" 
                                               id="status" name="status" value="1" 
                                               {{ old('status', $ficha->status) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="status">
                                            <i class="fas fa-toggle-on"></i> Ficha Activa
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información adicional -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <h5><i class="icon fas fa-info"></i> Información de la Ficha</h5>
                                    <ul class="mb-0">
                                        <li><strong>Creada:</strong> {{ $ficha->created_at->format('d/m/Y H:i') }}</li>
                                        @if($ficha->updated_at != $ficha->created_at)
                                            <li><strong>Última modificación:</strong> {{ $ficha->updated_at->format('d/m/Y H:i') }}</li>
                                        @endif
                                        @if($ficha->tieneAprendices())
                                            <li><strong>Aprendices asignados:</strong> {{ $ficha->contarAprendices() }}</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('fichaCaracterizacion.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Cancelar
                                </a>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Actualizar Ficha
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // Cargar datos iniciales
        loadModalidades();
        loadJornadas();
        loadInstructores();
        loadSedes();

        // Establecer valores actuales
        @if($ficha->sede_id)
            $('#sede_id').val({{ $ficha->sede_id }});
        @endif
        
        @if($ficha->instructor_id)
            setTimeout(() => {
                $('#instructor_id').val({{ $ficha->instructor_id }});
            }, 1000);
        @endif
        
        @if($ficha->modalidad_formacion_id)
            setTimeout(() => {
                $('#modalidad_formacion_id').val({{ $ficha->modalidad_formacion_id }});
            }, 1000);
        @endif
        
        @if($ficha->jornada_id)
            setTimeout(() => {
                $('#jornada_id').val({{ $ficha->jornada_id }});
            }, 1000);
        @endif
        
        @if($ficha->ambiente_id)
            setTimeout(() => {
                $('#ambiente_id').val({{ $ficha->ambiente_id }});
            }, 1000);
        @endif

        // Cargar ambientes si hay sede seleccionada
        @if($ficha->sede_id)
            loadAmbientes({{ $ficha->sede_id }});
        @endif

        // Cuando cambie el programa de formación
        $('#programa_formacion_id').change(function() {
            const programaId = $(this).val();
            if (programaId) {
                const sedeId = $(this).find('option:selected').data('sede');
                loadAmbientes(sedeId);
                $('#sede_id').val(sedeId);
            }
        });

        // Cuando cambie la sede
        $('#sede_id').change(function() {
            const sedeId = $(this).val();
            if (sedeId) {
                loadAmbientes(sedeId);
            }
        });

        // Validación de fechas
        $('#fecha_inicio, #fecha_fin').change(function() {
            validateDates();
        });
    });

    function loadModalidades() {
        $.get('/api/modalidades', function(data) {
            const select = $('#modalidad_formacion_id');
            select.find('option:not(:first)').remove();
            data.forEach(function(modalidad) {
                select.append(new Option(modalidad.name, modalidad.id));
            });
        });
    }

    function loadJornadas() {
        $.get('/api/jornadas', function(data) {
            const select = $('#jornada_id');
            select.find('option:not(:first)').remove();
            data.forEach(function(jornada) {
                select.append(new Option(jornada.name, jornada.id));
            });
        });
    }

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

    function loadSedes() {
        $.get('/api/sedes', function(data) {
            const select = $('#sede_id');
            select.find('option:not(:first)').remove();
            data.forEach(function(sede) {
                select.append(new Option(sede.nombre, sede.id));
            });
        });
    }

    function loadAmbientes(sedeId) {
        if (!sedeId) return;
        
        $.get('/api/ambientes/' + sedeId, function(data) {
            const select = $('#ambiente_id');
            select.find('option:not(:first)').remove();
            data.forEach(function(ambiente) {
                select.append(new Option(
                    ambiente.nombre + ' - ' + ambiente.piso.bloque.nombre + ' - ' + ambiente.piso.nombre,
                    ambiente.id
                ));
            });
        });
    }

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

    // Validación del formulario
    $('#formEditFicha').submit(function(e) {
        e.preventDefault();
        
        // Validar fechas
        validateDates();
        
        if (this.checkValidity()) {
            this.submit();
        } else {
            $(this).addClass('was-validated');
        }
    });
</script>
@endsection