@extends('adminlte::page')

@section('title', 'Editar Instructor')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
    <style>
        .form-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid #007bff;
        }
        .form-section h6 {
            color: #007bff;
            margin-bottom: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        .form-section h6 i {
            margin-right: 0.5rem;
        }
        
        .compact-form .form-group {
            margin-bottom: 1rem;
        }
        .compact-form .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        .compact-form .form-control {
            font-size: 0.9rem;
            padding: 0.5rem 0.75rem;
        }
        .compact-form .required-field::after {
            content: " *";
            color: #dc3545;
        }
        
        .specialty-item {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 20px;
            padding: 0.5rem 1rem;
            margin: 0.25rem;
            display: inline-block;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .specialty-item:hover {
            background: #e9ecef;
            border-color: #adb5bd;
        }
        .specialty-item.selected {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
    </style>
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-chalkboard-teacher" 
        title="Instructor"
        subtitle="Edición del instructor"
        :breadcrumb="[['label' => 'Instructores', 'url' => route('instructor.index') , 'icon' => 'fa-chalkboard-teacher'], ['label' => 'Editar instructor', 'icon' => 'fa-edit', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('instructor.index') }}">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>

                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-edit mr-2"></i>Editar Instructor
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('instructor.update', $instructor->id) }}" class="row compact-form">
                                @csrf
                                @method('PUT')

                                <!-- Información Personal -->
                                <div class="col-md-12">
                                    <div class="form-section">
                                        <h6><i class="fas fa-user"></i> Información Personal</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Nombre Completo</label>
                                                    <input type="text" class="form-control" 
                                                           value="{{ $instructor->persona->primer_nombre }} {{ $instructor->persona->segundo_nombre }} {{ $instructor->persona->primer_apellido }} {{ $instructor->persona->segundo_apellido }}" 
                                                           readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Número de Documento</label>
                                                    <input type="text" class="form-control" 
                                                           value="{{ $instructor->persona->numero_documento }}" 
                                                           readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Información Institucional -->
                                <div class="col-md-12">
                                    <div class="form-section">
                                        <h6><i class="fas fa-building"></i> Información Institucional</h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="regional_id" class="form-label required-field">Regional</label>
                                                    <select name="regional_id" id="regional_id" class="form-control @error('regional_id') is-invalid @enderror" required>
                                                        <option value="">Seleccione una regional</option>
                                                        @foreach($regionales as $regional)
                                                            <option value="{{ $regional->id }}" {{ old('regional_id', $instructor->regional_id) == $regional->id ? 'selected' : '' }}>
                                                                {{ $regional->nombre }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('regional_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="centro_formacion_id" class="form-label">Centro de Formación</label>
                                                    <select name="centro_formacion_id" id="centro_formacion_id" class="form-control @error('centro_formacion_id') is-invalid @enderror">
                                                        <option value="">Seleccione un centro</option>
                                                        @foreach($centrosFormacion as $centro)
                                                            <option value="{{ $centro->id }}" {{ old('centro_formacion_id', $instructor->centro_formacion_id) == $centro->id ? 'selected' : '' }}>
                                                                {{ $centro->nombre }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('centro_formacion_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="tipo_vinculacion_id" class="form-label">Tipo de Vinculación</label>
                                                    @if($tiposVinculacion->count() > 0)
                                                        <select name="tipo_vinculacion_id" id="tipo_vinculacion_id" class="form-control @error('tipo_vinculacion_id') is-invalid @enderror">
                                                            <option value="">Seleccione un tipo</option>
                                                            @foreach($tiposVinculacion as $parametroTema)
                                                                <option value="{{ $parametroTema->id }}" {{ old('tipo_vinculacion_id', $instructor->tipo_vinculacion_id) == $parametroTema->id ? 'selected' : '' }}>
                                                                    {{ $parametroTema->parametro->name ?? '' }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <div class="alert alert-warning mb-0">
                                                            <small><i class="fas fa-exclamation-triangle mr-1"></i>No hay tipos de vinculación disponibles</small>
                                                        </div>
                                                    @endif
                                                    @error('tipo_vinculacion_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-label">Jornada(s) de Trabajo</label>
                                                    @if($jornadasTrabajo->count() > 0)
                                                        <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                                            @foreach($jornadasTrabajo as $jornada)
                                                                <div class="form-check">
                                                                    <input 
                                                                        type="checkbox" 
                                                                        name="jornadas[]" 
                                                                        value="{{ $jornada->id }}" 
                                                                        id="jornada_{{ $jornada->id }}" 
                                                                        class="form-check-input @error('jornadas') is-invalid @enderror"
                                                                        {{ in_array($jornada->id, old('jornadas', $jornadasAsignadas)) ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="jornada_{{ $jornada->id }}">
                                                                        {{ $jornada->parametro->name ?? '' }}
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <div class="alert alert-warning">
                                                            <small><i class="fas fa-exclamation-triangle mr-1"></i>No hay jornadas disponibles</small>
                                                        </div>
                                                    @endif
                                                    @error('jornadas')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                    <small class="form-text text-muted">Seleccione una o más jornadas de trabajo</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="fecha_ingreso_sena" class="form-label">Fecha de Ingreso al SENA</label>
                                                    <input type="date" name="fecha_ingreso_sena" id="fecha_ingreso_sena" 
                                                           class="form-control @error('fecha_ingreso_sena') is-invalid @enderror" 
                                                           value="{{ old('fecha_ingreso_sena', $instructor->fecha_ingreso_sena ? $instructor->fecha_ingreso_sena->format('Y-m-d') : '') }}" 
                                                           max="{{ date('Y-m-d') }}">
                                                    @error('fecha_ingreso_sena')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="status" class="form-label required-field">Estado</label>
                                                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                                        <option value="1" {{ old('status', $instructor->status) == 1 ? 'selected' : '' }}>Activo</option>
                                                        <option value="0" {{ old('status', $instructor->status) == 0 ? 'selected' : '' }}>Inactivo</option>
                                                    </select>
                                                    @error('status')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Experiencia -->
                                <div class="col-md-12">
                                    <div class="form-section">
                                        <h6><i class="fas fa-briefcase"></i> Experiencia</h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="anos_experiencia" class="form-label">Años de Experiencia</label>
                                                    <input type="number" name="anos_experiencia" id="anos_experiencia" 
                                                           class="form-control @error('anos_experiencia') is-invalid @enderror" 
                                                           value="{{ old('anos_experiencia', $instructor->anos_experiencia) }}" min="0" max="50">
                                                    @error('anos_experiencia')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="experiencia_instructor_meses" class="form-label">Experiencia como Instructor (Meses)</label>
                                                    <input type="number" name="experiencia_instructor_meses" id="experiencia_instructor_meses" 
                                                           class="form-control @error('experiencia_instructor_meses') is-invalid @enderror" 
                                                           value="{{ old('experiencia_instructor_meses', $instructor->experiencia_instructor_meses) }}" min="0">
                                                    @error('experiencia_instructor_meses')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="experiencia_laboral" class="form-label">Descripción de Experiencia Laboral</label>
                                                    <textarea name="experiencia_laboral" id="experiencia_laboral" rows="3" 
                                                              class="form-control @error('experiencia_laboral') is-invalid @enderror" 
                                                              placeholder="Describa la experiencia laboral del instructor...">{{ old('experiencia_laboral', $instructor->experiencia_laboral) }}</textarea>
                                                    @error('experiencia_laboral')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Especialidades -->
                                <div class="col-md-12">
                                    <div class="form-section">
                                        <h6><i class="fas fa-graduation-cap"></i> Especialidades</h6>
                                        <div class="form-group">
                                            <label for="especialidades" class="form-label">Seleccionar Especialidades</label>
                                            <select name="especialidades[]" id="especialidades" class="form-control" multiple>
                                                @php
                                                    $especialidadesActuales = $instructor->especialidades ?? [];
                                                    $idsEspecialidades = [];
                                                    if (isset($especialidadesActuales['principal'])) {
                                                        // Buscar ID de la especialidad principal
                                                        $especialidadPrincipal = \App\Models\RedConocimiento::where('nombre', $especialidadesActuales['principal'])->first();
                                                        if ($especialidadPrincipal) {
                                                            $idsEspecialidades[] = $especialidadPrincipal->id;
                                                        }
                                                    }
                                                    if (isset($especialidadesActuales['secundarias'])) {
                                                        foreach ($especialidadesActuales['secundarias'] as $especialidadSecundaria) {
                                                            $especialidad = \App\Models\RedConocimiento::where('nombre', $especialidadSecundaria)->first();
                                                            if ($especialidad) {
                                                                $idsEspecialidades[] = $especialidad->id;
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                @foreach($especialidades as $especialidad)
                                                    <option value="{{ $especialidad->id }}" 
                                                        {{ in_array($especialidad->id, old('especialidades', $idsEspecialidades)) ? 'selected' : '' }}>
                                                        {{ $especialidad->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="form-text text-muted">
                                                Mantenga presionado Ctrl (Cmd en Mac) para seleccionar múltiples especialidades
                                            </small>
                                        </div>
                                        
                                        <div id="selected-specialties-display" class="mt-2">
                                            @if(isset($especialidadesActuales['principal']))
                                                <div class="d-inline-block px-2 py-1 rounded-pill bg-primary-light text-primary mr-1 mb-1 font-weight-medium">
                                                    <i class="fas fa-star mr-1"></i>{{ $especialidadesActuales['principal'] }}
                                                </div>
                                            @endif
                                            @if(isset($especialidadesActuales['secundarias']))
                                                @foreach($especialidadesActuales['secundarias'] as $especialidad)
                                                    <div class="d-inline-block px-2 py-1 rounded-pill bg-secondary-light text-secondary mr-1 mb-1 font-weight-medium">{{ $especialidad }}</div>
                                                @endforeach
                                            @endif
                                            @if(empty($especialidadesActuales))
                                                <span class="text-muted">Ninguna especialidad asignada</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Formación Académica -->
                                <div class="col-md-12">
                                    <div class="form-section">
                                        <h6><i class="fas fa-graduation-cap"></i> Formación Académica</h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="nivel_academico_id" class="form-label">Nivel Académico Más Alto Alcanzado</label>
                                                    @if($nivelesAcademicos->count() > 0)
                                                        <select name="nivel_academico_id" id="nivel_academico_id" class="form-control @error('nivel_academico_id') is-invalid @enderror">
                                                            <option value="">Seleccione un nivel</option>
                                                            @foreach($nivelesAcademicos as $parametroTema)
                                                                <option value="{{ $parametroTema->id }}" {{ old('nivel_academico_id', $instructor->nivel_academico_id) == $parametroTema->id ? 'selected' : '' }}>
                                                                    {{ $parametroTema->parametro->name ?? '' }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <div class="alert alert-warning mb-0">
                                                            <small><i class="fas fa-exclamation-triangle mr-1"></i>No hay niveles académicos disponibles</small>
                                                        </div>
                                                    @endif
                                                    @error('nivel_academico_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="formacion_pedagogia" class="form-label">Formación en Pedagogía</label>
                                                    <textarea name="formacion_pedagogia" id="formacion_pedagogia" rows="2" 
                                                              class="form-control @error('formacion_pedagogia') is-invalid @enderror" 
                                                              placeholder="Ej: Diplomado en pedagogía SENA, Diplomado en docencia universitaria...">{{ old('formacion_pedagogia', $instructor->formacion_pedagogia) }}</textarea>
                                                    @error('formacion_pedagogia')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="form-text text-muted">Diplomado en pedagogía SENA u otros equivalentes</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Título(s) Obtenido(s)</label>
                                                    @php
                                                        $titulos = old('titulos_obtenidos', $instructor->titulos_obtenidos ?? []);
                                                        if (empty($titulos)) $titulos = [''];
                                                    @endphp
                                                    @foreach($titulos as $index => $titulo)
                                                        <div class="input-group mb-2">
                                                            <input type="text" name="titulos_obtenidos[]" value="{{ $titulo }}" class="form-control" placeholder="Ej: Ingeniero de Sistemas">
                                                            <div class="input-group-append">
                                                                @if(count($titulos) > 1)
                                                                    <button type="button" class="btn btn-danger btn-remove-titulo">
                                                                        <i class="fas fa-times"></i>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    <button type="button" class="btn btn-sm btn-info mt-2 btn-add-titulo">
                                                        <i class="fas fa-plus mr-1"></i> Agregar Título
                                                    </button>
                                                    @error('titulos_obtenidos')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Institución(es) Educativa(s)</label>
                                                    @php
                                                        $instituciones = old('instituciones_educativas', $instructor->instituciones_educativas ?? []);
                                                        if (empty($instituciones)) $instituciones = [''];
                                                    @endphp
                                                    @foreach($instituciones as $index => $institucion)
                                                        <div class="input-group mb-2">
                                                            <input type="text" name="instituciones_educativas[]" value="{{ $institucion }}" class="form-control" placeholder="Ej: Universidad Nacional">
                                                            <div class="input-group-append">
                                                                @if(count($instituciones) > 1)
                                                                    <button type="button" class="btn btn-danger btn-remove-institucion">
                                                                        <i class="fas fa-times"></i>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    <button type="button" class="btn btn-sm btn-info mt-2 btn-add-institucion">
                                                        <i class="fas fa-plus mr-1"></i> Agregar Institución
                                                    </button>
                                                    @error('instituciones_educativas')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Certificaciones Técnicas o Tecnológicas</label>
                                                    @php
                                                        $certificaciones = old('certificaciones_tecnicas', $instructor->certificaciones_tecnicas ?? []);
                                                        if (empty($certificaciones)) $certificaciones = [''];
                                                    @endphp
                                                    @foreach($certificaciones as $index => $certificacion)
                                                        <div class="input-group mb-2">
                                                            <input type="text" name="certificaciones_tecnicas[]" value="{{ $certificacion }}" class="form-control" placeholder="Ej: Certificación PMP">
                                                            <div class="input-group-append">
                                                                @if(count($certificaciones) > 1)
                                                                    <button type="button" class="btn btn-danger btn-remove-certificacion">
                                                                        <i class="fas fa-times"></i>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    <button type="button" class="btn btn-sm btn-info mt-2 btn-add-certificacion">
                                                        <i class="fas fa-plus mr-1"></i> Agregar Certificación
                                                    </button>
                                                    @error('certificaciones_tecnicas')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Cursos Complementarios Relevantes</label>
                                                    @php
                                                        $cursos = old('cursos_complementarios', $instructor->cursos_complementarios ?? []);
                                                        if (empty($cursos)) $cursos = [''];
                                                    @endphp
                                                    @foreach($cursos as $index => $curso)
                                                        <div class="input-group mb-2">
                                                            <input type="text" name="cursos_complementarios[]" value="{{ $curso }}" class="form-control" placeholder="Ej: Curso de Excel Avanzado">
                                                            <div class="input-group-append">
                                                                @if(count($cursos) > 1)
                                                                    <button type="button" class="btn btn-danger btn-remove-curso">
                                                                        <i class="fas fa-times"></i>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    <button type="button" class="btn btn-sm btn-info mt-2 btn-add-curso">
                                                        <i class="fas fa-plus mr-1"></i> Agregar Curso
                                                    </button>
                                                    @error('cursos_complementarios')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Competencias y Habilidades -->
                                <div class="col-md-12">
                                    <div class="form-section">
                                        <h6><i class="fas fa-tasks"></i> Competencias y Habilidades</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="areas_experticia" class="form-label">Áreas de Experticia</label>
                                                    <textarea name="areas_experticia" id="areas_experticia" rows="3" 
                                                              class="form-control @error('areas_experticia') is-invalid @enderror" 
                                                              placeholder="Ej: Electricidad, Programación, Contabilidad... (una por línea)">@if(is_array(old('areas_experticia', $instructor->areas_experticia))){{ implode("\n", old('areas_experticia', $instructor->areas_experticia ?? [])) }}@else{{ old('areas_experticia', $instructor->areas_experticia) }}@endif</textarea>
                                                    @error('areas_experticia')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="form-text text-muted">Un área por línea</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="competencias_tic" class="form-label">Competencias TIC</label>
                                                    <textarea name="competencias_tic" id="competencias_tic" rows="3" 
                                                              class="form-control @error('competencias_tic') is-invalid @enderror" 
                                                              placeholder="Ej: Manejo de Office, LMS, SofíaPlus... (una por línea)">@if(is_array(old('competencias_tic', $instructor->competencias_tic))){{ implode("\n", old('competencias_tic', $instructor->competencias_tic ?? [])) }}@else{{ old('competencias_tic', $instructor->competencias_tic) }}@endif</textarea>
                                                    @error('competencias_tic')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="form-text text-muted">Manejo de Office, LMS, herramientas SENA como SofíaPlus</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Idiomas y Nivel</label>
                                                    @php
                                                        $idiomas = old('idiomas', $instructor->idiomas ?? []);
                                                        if (empty($idiomas)) $idiomas = [['idioma' => '', 'nivel' => '']];
                                                    @endphp
                                                    @foreach($idiomas as $index => $idioma)
                                                        <div class="row mb-2">
                                                            <div class="col-6">
                                                                <input type="text" name="idiomas[{{ $index }}][idioma]" value="{{ $idioma['idioma'] ?? '' }}" class="form-control form-control-sm" placeholder="Idioma (ej: Inglés)">
                                                            </div>
                                                            <div class="col-5">
                                                                <select name="idiomas[{{ $index }}][nivel]" class="form-control form-control-sm">
                                                                    <option value="">Nivel</option>
                                                                    <option value="básico" {{ ($idioma['nivel'] ?? '') == 'básico' ? 'selected' : '' }}>Básico</option>
                                                                    <option value="intermedio" {{ ($idioma['nivel'] ?? '') == 'intermedio' ? 'selected' : '' }}>Intermedio</option>
                                                                    <option value="avanzado" {{ ($idioma['nivel'] ?? '') == 'avanzado' ? 'selected' : '' }}>Avanzado</option>
                                                                    <option value="nativo" {{ ($idioma['nivel'] ?? '') == 'nativo' ? 'selected' : '' }}>Nativo</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-1">
                                                                @if(count($idiomas) > 1)
                                                                    <button type="button" class="btn btn-sm btn-danger btn-remove-idioma">
                                                                        <i class="fas fa-times"></i>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    <button type="button" class="btn btn-sm btn-secondary mt-2 btn-add-idioma">
                                                        <i class="fas fa-plus"></i> Agregar Idioma
                                                    </button>
                                                    @error('idiomas')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Habilidades Pedagógicas</label>
                                                    @php
                                                        $habilidades = old('habilidades_pedagogicas', $instructor->habilidades_pedagogicas ?? []);
                                                    @endphp
                                                    <div class="form-check">
                                                        <input type="checkbox" name="habilidades_pedagogicas[]" value="virtual" id="hab_virtual" class="form-check-input" {{ in_array('virtual', $habilidades) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="hab_virtual">Virtual</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input type="checkbox" name="habilidades_pedagogicas[]" value="presencial" id="hab_presencial" class="form-check-input" {{ in_array('presencial', $habilidades) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="hab_presencial">Presencial</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input type="checkbox" name="habilidades_pedagogicas[]" value="dual" id="hab_dual" class="form-check-input" {{ in_array('dual', $habilidades) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="hab_dual">Dual</label>
                                                    </div>
                                                    @error('habilidades_pedagogicas')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Información Administrativa -->
                                <div class="col-md-12">
                                    <div class="form-section">
                                        <h6><i class="fas fa-file-contract"></i> Información Administrativa <small class="text-muted">(Opcional)</small></h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="numero_contrato" class="form-label">Número de Contrato</label>
                                                    <input type="text" name="numero_contrato" id="numero_contrato" 
                                                           class="form-control @error('numero_contrato') is-invalid @enderror" 
                                                           value="{{ old('numero_contrato', $instructor->numero_contrato) }}" 
                                                           placeholder="Ej: CON-2024-001">
                                                    @error('numero_contrato')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="fecha_inicio_contrato" class="form-label">Fecha de Inicio de Contrato</label>
                                                    <input type="date" name="fecha_inicio_contrato" id="fecha_inicio_contrato" 
                                                           class="form-control @error('fecha_inicio_contrato') is-invalid @enderror"
                                                           value="{{ old('fecha_inicio_contrato', $instructor->fecha_inicio_contrato ? $instructor->fecha_inicio_contrato->format('Y-m-d') : '') }}">
                                                    @error('fecha_inicio_contrato')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="fecha_fin_contrato" class="form-label">Fecha de Fin de Contrato</label>
                                                    <input type="date" name="fecha_fin_contrato" id="fecha_fin_contrato" 
                                                           class="form-control @error('fecha_fin_contrato') is-invalid @enderror"
                                                           value="{{ old('fecha_fin_contrato', $instructor->fecha_fin_contrato ? $instructor->fecha_fin_contrato->format('Y-m-d') : '') }}">
                                                    @error('fecha_fin_contrato')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="supervisor_contrato" class="form-label">Supervisor de Contrato</label>
                                                    <input type="text" name="supervisor_contrato" id="supervisor_contrato" 
                                                           class="form-control @error('supervisor_contrato') is-invalid @enderror" 
                                                           value="{{ old('supervisor_contrato', $instructor->supervisor_contrato) }}" 
                                                           placeholder="Nombre del supervisor">
                                                    @error('supervisor_contrato')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="eps" class="form-label">EPS</label>
                                                    <input type="text" name="eps" id="eps" 
                                                           class="form-control @error('eps') is-invalid @enderror" 
                                                           value="{{ old('eps', $instructor->eps) }}" 
                                                           placeholder="Ej: Sura, Coomeva...">
                                                    @error('eps')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="arl" class="form-label">ARL</label>
                                                    <input type="text" name="arl" id="arl" 
                                                           class="form-control @error('arl') is-invalid @enderror" 
                                                           value="{{ old('arl', $instructor->arl) }}" 
                                                           placeholder="Ej: ARL Sura...">
                                                    @error('arl')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Botones -->
                                <div class="col-12">
                                    <hr class="mt-4">
                                    <div class="d-flex justify-content-end">
                                        <a href="{{ route('instructor.index') }}" class="btn btn-light mr-2">
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
        </div>
    </section>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection

@section('js')
    @vite(['resources/js/pages/table-page.js'])
    <script>
        // Manejar campos dinámicos
        document.addEventListener('DOMContentLoaded', function() {
            // Agregar título
            document.querySelector('.btn-add-titulo')?.addEventListener('click', function() {
                const container = this.previousElementSibling;
                const newInput = document.createElement('div');
                newInput.className = 'input-group mb-2';
                newInput.innerHTML = `
                    <input type="text" name="titulos_obtenidos[]" class="form-control" placeholder="Ej: Ingeniero de Sistemas">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger btn-remove-titulo">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                container.appendChild(newInput);
            });

            // Eliminar título
            document.addEventListener('click', function(e) {
                if (e.target.closest('.btn-remove-titulo')) {
                    e.target.closest('.input-group').remove();
                }
            });

            // Agregar institución
            document.querySelector('.btn-add-institucion')?.addEventListener('click', function() {
                const container = this.previousElementSibling;
                const newInput = document.createElement('div');
                newInput.className = 'input-group mb-2';
                newInput.innerHTML = `
                    <input type="text" name="instituciones_educativas[]" class="form-control" placeholder="Ej: Universidad Nacional">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger btn-remove-institucion">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                container.appendChild(newInput);
            });

            // Eliminar institución
            document.addEventListener('click', function(e) {
                if (e.target.closest('.btn-remove-institucion')) {
                    e.target.closest('.input-group').remove();
                }
            });

            // Agregar certificación
            document.querySelector('.btn-add-certificacion')?.addEventListener('click', function() {
                const container = this.previousElementSibling;
                const newInput = document.createElement('div');
                newInput.className = 'input-group mb-2';
                newInput.innerHTML = `
                    <input type="text" name="certificaciones_tecnicas[]" class="form-control" placeholder="Ej: Certificación PMP">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger btn-remove-certificacion">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                container.appendChild(newInput);
            });

            // Eliminar certificación
            document.addEventListener('click', function(e) {
                if (e.target.closest('.btn-remove-certificacion')) {
                    e.target.closest('.input-group').remove();
                }
            });

            // Agregar curso
            document.querySelector('.btn-add-curso')?.addEventListener('click', function() {
                const container = this.previousElementSibling;
                const newInput = document.createElement('div');
                newInput.className = 'input-group mb-2';
                newInput.innerHTML = `
                    <input type="text" name="cursos_complementarios[]" class="form-control" placeholder="Ej: Curso de Excel Avanzado">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger btn-remove-curso">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                container.appendChild(newInput);
            });

            // Eliminar curso
            document.addEventListener('click', function(e) {
                if (e.target.closest('.btn-remove-curso')) {
                    e.target.closest('.input-group').remove();
                }
            });

            // Agregar idioma
            document.querySelector('.btn-add-idioma')?.addEventListener('click', function() {
                const container = this.previousElementSibling;
                const index = container.querySelectorAll('.row.mb-2').length;
                const newInput = document.createElement('div');
                newInput.className = 'row mb-2';
                newInput.innerHTML = `
                    <div class="col-6">
                        <input type="text" name="idiomas[${index}][idioma]" class="form-control form-control-sm" placeholder="Idioma (ej: Inglés)">
                    </div>
                    <div class="col-5">
                        <select name="idiomas[${index}][nivel]" class="form-control form-control-sm">
                            <option value="">Nivel</option>
                            <option value="básico">Básico</option>
                            <option value="intermedio">Intermedio</option>
                            <option value="avanzado">Avanzado</option>
                            <option value="nativo">Nativo</option>
                        </select>
                    </div>
                    <div class="col-1">
                        <button type="button" class="btn btn-sm btn-danger btn-remove-idioma">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                container.appendChild(newInput);
            });

            // Eliminar idioma
            document.addEventListener('click', function(e) {
                if (e.target.closest('.btn-remove-idioma')) {
                    e.target.closest('.row.mb-2').remove();
                }
            });

            // Cargar centros de formación cuando cambie la regional
            const regionalSelect = document.getElementById('regional_id');
            const centroSelect = document.getElementById('centro_formacion_id');
            
            if (regionalSelect && centroSelect) {
                regionalSelect.addEventListener('change', function() {
                    const regionalId = this.value;
                    
                    // Limpiar opciones actuales excepto la primera
                    centroSelect.innerHTML = '<option value="">Seleccione un centro</option>';
                    
                    if (regionalId) {
                        // Hacer petición AJAX para obtener centros
                        fetch(`{{ route('instructor.centrosPorRegional') }}?regional_id=${regionalId}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.centros) {
                                data.centros.forEach(centro => {
                                    const option = document.createElement('option');
                                    option.value = centro.id;
                                    option.textContent = centro.nombre;
                                    centroSelect.appendChild(option);
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error al cargar centros:', error);
                        });
                    }
                });
            }
        });
    </script>
@endsection