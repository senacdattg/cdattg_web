@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
    >
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
    >
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-edit" 
        title="Editar Ficha de Caracterización"
        subtitle="Modificar información de la ficha"
        :breadcrumb="[['label' => 'Fichas de Caracterización', 'url' => route('fichaCaracterizacion.index') , 'icon' => 'fa-file-alt'], ['label' => 'Editar', 'icon' => 'fa-edit', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <a class="btn btn-outline-secondary btn-sm mb-3 mt-4" href="{{ route('fichaCaracterizacion.index') }}">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-edit mr-2"></i>Editar Ficha de Caracterización: {{ $ficha->ficha }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('fichaCaracterizacion.update', $ficha->id) }}" method="POST" id="formEditFicha">
                                @csrf
                                @method('PUT')
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
                                    <select
                                        class="form-control select2 @error('programa_formacion_id') is-invalid @enderror"
                                        id="programa_formacion_id"
                                        name="programa_formacion_id"
                                        data-placeholder="Seleccione un programa..."
                                        required
                                    >
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
                                    <select
                                        class="form-control select2 @error('sede_id') is-invalid @enderror"
                                        id="sede_id"
                                        name="sede_id"
                                        data-placeholder="Seleccione una sede..."
                                    >
                                        <option value="">Seleccione una sede...</option>
                                        @foreach($sedes as $sede)
                                            <option value="{{ $sede->id }}" {{ old('sede_id', $ficha->sede_id) == $sede->id ? 'selected' : '' }}>
                                                {{ $sede->sede }}
                                            </option>
                                        @endforeach
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
                                    <select
                                        class="form-control select2 @error('instructor_id') is-invalid @enderror"
                                        id="instructor_id"
                                        name="instructor_id"
                                        data-placeholder="Seleccione un instructor..."
                                    >
                                        <option value="">Seleccione un instructor...</option>
                                        @foreach($instructores as $instructor)
                                            <option value="{{ $instructor->id }}" {{ old('instructor_id', $ficha->instructor_id) == $instructor->id ? 'selected' : '' }}>
                                                {{ $instructor->persona->primer_nombre ?? '' }} {{ $instructor->persona->segundo_nombre ?? '' }} {{ $instructor->persona->primer_apellido ?? '' }} {{ $instructor->persona->segundo_apellido ?? '' }}
                                            </option>
                                        @endforeach
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
                                    <select
                                        class="form-control select2 @error('modalidad_formacion_id') is-invalid @enderror"
                                        id="modalidad_formacion_id"
                                        name="modalidad_formacion_id"
                                        data-placeholder="Seleccione una modalidad..."
                                    >
                                        <option value="">Seleccione una modalidad...</option>
                                        @foreach($modalidades as $modalidad)
                                            <option value="{{ $modalidad->id }}" {{ old('modalidad_formacion_id', $ficha->modalidad_formacion_id) == $modalidad->id ? 'selected' : '' }}>
                                                {{ $modalidad->name }}
                                            </option>
                                        @endforeach
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
                                    <select
                                        class="form-control select2 @error('jornada_id') is-invalid @enderror"
                                        id="jornada_id"
                                        name="jornada_id"
                                        data-placeholder="Seleccione una jornada..."
                                    >
                                        <option value="">Seleccione una jornada...</option>
                                        @foreach($jornadas as $jornada)
                                            <option value="{{ $jornada->id }}" {{ old('jornada_id', $ficha->jornada_id) == $jornada->id ? 'selected' : '' }}>
                                                {{ $jornada->jornada }}
                                            </option>
                                        @endforeach
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
                                    <select
                                        class="form-control select2 @error('ambiente_id') is-invalid @enderror"
                                        id="ambiente_id"
                                        name="ambiente_id"
                                        data-placeholder="Seleccione un ambiente..."
                                    >
                                        <option value="">Seleccione un ambiente...</option>
                                        @if($ficha->sede_id)
                                            @foreach($ambientes->filter(function($ambiente) use ($ficha) {
                                                return $ambiente->piso->bloque->sede_id == $ficha->sede_id;
                                            }) as $ambiente)
                                                <option value="{{ $ambiente->id }}" {{ old('ambiente_id', $ficha->ambiente_id) == $ambiente->id ? 'selected' : '' }}>
                                                    {{ $ambiente->title }} - {{ $ambiente->piso->bloque->bloque ?? '' }}
                                                </option>
                                            @endforeach
                                        @else
                                            @foreach($ambientes as $ambiente)
                                                <option value="{{ $ambiente->id }}" 
                                                        {{ old('ambiente_id', $ficha->ambiente_id) == $ambiente->id ? 'selected' : '' }}
                                                        data-sede="{{ $ambiente->piso->bloque->sede_id ?? '' }}">
                                                    {{ $ambiente->title }} - {{ $ambiente->piso->bloque->bloque ?? '' }}
                                                </option>
                                            @endforeach
                                        @endif
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
                                        <input type="hidden" name="status" value="0">
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

                                <div class="col-12">
                                    <hr class="mt-4">
                                    <div class="d-flex justify-content-end">
                                        <a href="{{ route('fichaCaracterizacion.index') }}" class="btn btn-light mr-2">
                                            Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save mr-1"></i>Guardar Cambios
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-chalkboard-teacher mr-2"></i>Gestión de Instructores
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="mb-1"><strong>Instructores asignados:</strong> {{ $ficha->instructorFicha->count() }}</p>
                                    <p class="mb-0 text-muted">Gestione los instructores que dictarán formación en esta ficha</p>
                                </div>
                                <a href="{{ route('fichaCaracterizacion.gestionarInstructores', $ficha->id) }}" 
                                   class="btn btn-primary">
                                    <i class="fas fa-users-cog mr-1"></i> Gestionar Instructores
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-users mr-2"></i>Gestión de Aprendices
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="mb-1"><strong>Aprendices asignados:</strong> {{ $ficha->aprendices->count() }}</p>
                                    <p class="mb-0 text-muted">Gestione las personas asignadas como aprendices a esta ficha</p>
                                </div>
                                <a href="{{ route('fichaCaracterizacion.gestionarAprendices', $ficha->id) }}" 
                                   class="btn btn-success">
                                    <i class="fas fa-user-graduate mr-1"></i> Gestionar Aprendices
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-calendar-week mr-2"></i>Gestión de Días de Formación
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="mb-1"><strong>Días configurados:</strong> {{ $ficha->diasFormacion->count() }}</p>
                                    <p class="mb-0 text-muted">Configure los días y horarios de formación de la ficha</p>
                                </div>
                                <a href="{{ route('fichaCaracterizacion.gestionarDiasFormacion', $ficha->id) }}" 
                                   class="btn btn-info">
                                    <i class="fas fa-calendar-alt mr-1"></i> Gestionar Días
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @vite(['resources/js/pages/fichas-form.js'])
@endsection