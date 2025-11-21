<div>
    {{-- Mensajes de sesión --}}
    <x-session-alerts />
    
    {{-- Mensajes de error generales --}}
    @error('general')
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ $message }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @enderror
    
    <div class="card shadow-sm no-hover">
        <div class="card-header bg-white py-3">
            <h5 class="card-title m-0 font-weight-bold text-primary">
                <i class="fas fa-plus-circle mr-2"></i>Crear Instructor
            </h5>
        </div>
        <div class="card-body">
            <form wire:submit="store" class="row compact-form">
                <!-- Selección de Persona -->
                <div class="col-md-12">
                    <div class="form-section">
                        <h6><i class="fas fa-user"></i> Seleccionar Persona</h6>
                        @if ($personasDisponibles->isEmpty())
                            <p class="text-muted mb-0">
                                No hay personas disponibles sin rol de instructor. Debe registrar nuevas personas o liberar el rol de un instructor existente.
                            </p>
                        @else
                            <div class="form-group">
                                <label for="persona_id" class="form-label required-field">Persona</label>
                                <select wire:model="persona_id" id="persona_id" class="form-control @error('persona_id') is-invalid @enderror" required>
                                    <option value="">-- Selecciona una persona --</option>
                                    @foreach ($personasDisponibles as $persona)
                                        <option value="{{ $persona->id }}">
                                            {{ $persona->nombre_completo }} — {{ $persona->numero_documento }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('persona_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Información laboral como instructor -->
                <div class="col-md-12">
                    <div class="form-section">
                        <h6><i class="fas fa-briefcase"></i> Información Laboral como Instructor</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="regional_id" class="form-label required-field">Regional</label>
                                    <select wire:model.live="regional_id" id="regional_id" class="form-control @error('regional_id') is-invalid @enderror" required>
                                        <option value="">Seleccione una regional</option>
                                        @foreach($regionales as $regional)
                                            <option value="{{ $regional->id }}">{{ $regional->nombre }}</option>
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
                                    <select wire:model="centro_formacion_id" id="centro_formacion_id" class="form-control @error('centro_formacion_id') is-invalid @enderror" {{ !$regional_id ? 'disabled' : '' }}>
                                        <option value="">{{ !$regional_id ? 'Primero seleccione una regional' : 'Seleccione un centro' }}</option>
                                        @foreach($centrosFormacion as $centro)
                                            <option value="{{ $centro['id'] }}">{{ $centro['nombre'] }}</option>
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
                                    @php
                                        $tiposVinculacionCount = isset($tiposVinculacion) ? $tiposVinculacion->count() : 0;
                                    @endphp
                                    @if($tiposVinculacionCount > 0)
                                        <select wire:model="tipo_vinculacion_id" id="tipo_vinculacion_id" class="form-control @error('tipo_vinculacion_id') is-invalid @enderror">
                                            <option value="">Seleccione un tipo</option>
                                            @foreach($tiposVinculacion as $parametroTema)
                                                <option value="{{ $parametroTema->id }}">{{ $parametroTema->parametro->name ?? '' }}</option>
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Jornada(s) de Trabajo</label>
                                    @php
                                        $jornadasCount = isset($jornadasTrabajo) ? $jornadasTrabajo->count() : 0;
                                    @endphp
                                    @if($jornadasCount > 0)
                                        <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                            @foreach($jornadasTrabajo as $jornada)
                                                <div class="form-check">
                                                    <input 
                                                        type="checkbox" 
                                                        wire:model="jornadas" 
                                                        value="{{ $jornada->id }}" 
                                                        id="jornada_{{ $jornada->id }}" 
                                                        class="form-check-input @error('jornadas') is-invalid @enderror">
                                                    <label class="form-check-label" for="jornada_{{ $jornada->id }}">
                                                        {{ $jornada->parametro->name ?? '' }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle mr-2"></i>
                                            <strong>No hay jornadas disponibles</strong><br>
                                            <small>Por favor, registre jornadas de trabajo en el sistema primero.</small>
                                        </div>
                                    @endif
                                    @error('jornadas')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Seleccione una o más jornadas de trabajo
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha_ingreso_sena" class="form-label">Fecha de Ingreso al SENA</label>
                                    <input type="date" wire:model="fecha_ingreso_sena" id="fecha_ingreso_sena" 
                                           class="form-control @error('fecha_ingreso_sena') is-invalid @enderror" 
                                           max="{{ date('Y-m-d') }}">
                                    @error('fecha_ingreso_sena')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="anos_experiencia" class="form-label">Años de Experiencia</label>
                                    <input type="number" wire:model="anos_experiencia" id="anos_experiencia" 
                                           class="form-control @error('anos_experiencia') is-invalid @enderror" 
                                           min="0" max="50">
                                    @error('anos_experiencia')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="experiencia_instructor_meses" class="form-label">Experiencia como Instructor (Meses)</label>
                                    <input type="number" wire:model="experiencia_instructor_meses" id="experiencia_instructor_meses" 
                                           class="form-control @error('experiencia_instructor_meses') is-invalid @enderror" 
                                           min="0">
                                    @error('experiencia_instructor_meses')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="experiencia_laboral" class="form-label">Descripción de Experiencia Laboral</label>
                                    <textarea wire:model="experiencia_laboral" id="experiencia_laboral" rows="2" 
                                              class="form-control @error('experiencia_laboral') is-invalid @enderror" 
                                              placeholder="Describa la experiencia laboral del instructor..."></textarea>
                                    @error('experiencia_laboral')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formación académica -->
                <div class="col-md-12">
                    <div class="form-section">
                        <h6><i class="fas fa-graduation-cap"></i> Formación Académica</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nivel_academico_id" class="form-label">Nivel Académico Más Alto Alcanzado</label>
                                    @php
                                        $nivelesAcademicosCount = isset($nivelesAcademicos) ? $nivelesAcademicos->count() : 0;
                                    @endphp
                                    @if($nivelesAcademicosCount > 0)
                                        <select wire:model="nivel_academico_id" id="nivel_academico_id" class="form-control @error('nivel_academico_id') is-invalid @enderror">
                                            <option value="">Seleccione un nivel</option>
                                            @foreach($nivelesAcademicos as $parametroTema)
                                                <option value="{{ $parametroTema->id }}">{{ $parametroTema->parametro->name ?? '' }}</option>
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
                                    <textarea wire:model="formacion_pedagogia" id="formacion_pedagogia" rows="2" 
                                              class="form-control @error('formacion_pedagogia') is-invalid @enderror" 
                                              placeholder="Ej: Diplomado en pedagogía SENA, Diplomado en docencia universitaria..."></textarea>
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
                                    @foreach($titulos_obtenidos as $index => $titulo)
                                        <div class="input-group mb-2">
                                            <input type="text" wire:model="titulos_obtenidos.{{ $index }}" class="form-control" placeholder="Ej: Ingeniero de Sistemas">
                                            <div class="input-group-append">
                                                @if(count($titulos_obtenidos) > 1)
                                                    <button type="button" class="btn btn-danger" wire:click="eliminarTitulo({{ $index }})">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                    <button type="button" class="btn btn-sm btn-info mt-2" wire:click="agregarTitulo">
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
                                    @foreach($instituciones_educativas as $index => $institucion)
                                        <div class="input-group mb-2">
                                            <input type="text" wire:model="instituciones_educativas.{{ $index }}" class="form-control" placeholder="Ej: Universidad Nacional">
                                            <div class="input-group-append">
                                                @if(count($instituciones_educativas) > 1)
                                                    <button type="button" class="btn btn-danger" wire:click="eliminarInstitucion({{ $index }})">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                    <button type="button" class="btn btn-sm btn-info mt-2" wire:click="agregarInstitucion">
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
                                    @foreach($certificaciones_tecnicas as $index => $certificacion)
                                        <div class="input-group mb-2">
                                            <input type="text" wire:model="certificaciones_tecnicas.{{ $index }}" class="form-control" placeholder="Ej: Certificación PMP">
                                            <div class="input-group-append">
                                                @if(count($certificaciones_tecnicas) > 1)
                                                    <button type="button" class="btn btn-danger" wire:click="eliminarCertificacion({{ $index }})">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                    <button type="button" class="btn btn-sm btn-info mt-2" wire:click="agregarCertificacion">
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
                                    @foreach($cursos_complementarios as $index => $curso)
                                        <div class="input-group mb-2">
                                            <input type="text" wire:model="cursos_complementarios.{{ $index }}" class="form-control" placeholder="Ej: Curso de Excel Avanzado">
                                            <div class="input-group-append">
                                                @if(count($cursos_complementarios) > 1)
                                                    <button type="button" class="btn btn-danger" wire:click="eliminarCurso({{ $index }})">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                    <button type="button" class="btn btn-sm btn-info mt-2" wire:click="agregarCurso">
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

                <!-- Competencias y habilidades -->
                <div class="col-md-12">
                    <div class="form-section">
                        <h6><i class="fas fa-tasks"></i> Competencias y Habilidades</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Áreas de Experticia</label>
                                    @foreach($areas_experticia as $index => $area)
                                        <div class="input-group mb-2">
                                            <input type="text" wire:model="areas_experticia.{{ $index }}" class="form-control" placeholder="Ej: Electricidad, Programación, Contabilidad">
                                            <div class="input-group-append">
                                                @if(count($areas_experticia) > 1)
                                                    <button type="button" class="btn btn-danger" wire:click="eliminarAreaExperticia({{ $index }})">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                    <button type="button" class="btn btn-sm btn-info mt-2" wire:click="agregarAreaExperticia">
                                        <i class="fas fa-plus mr-1"></i> Agregar Área
                                    </button>
                                    @error('areas_experticia')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Competencias TIC</label>
                                    @foreach($competencias_tic as $index => $competencia)
                                        <div class="input-group mb-2">
                                            <input type="text" wire:model="competencias_tic.{{ $index }}" class="form-control" placeholder="Ej: Manejo de Office, LMS, SofíaPlus">
                                            <div class="input-group-append">
                                                @if(count($competencias_tic) > 1)
                                                    <button type="button" class="btn btn-danger" wire:click="eliminarCompetenciaTic({{ $index }})">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                    <button type="button" class="btn btn-sm btn-info mt-2" wire:click="agregarCompetenciaTic">
                                        <i class="fas fa-plus mr-1"></i> Agregar Competencia
                                    </button>
                                    @error('competencias_tic')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Idiomas y Nivel</label>
                                    @foreach($idiomas as $index => $idioma)
                                        <div class="row mb-2">
                                            <div class="col-6">
                                                <input type="text" wire:model="idiomas.{{ $index }}.idioma" class="form-control form-control-sm" placeholder="Idioma (ej: Inglés)">
                                            </div>
                                            <div class="col-5">
                                                <select wire:model="idiomas.{{ $index }}.nivel" class="form-control form-control-sm">
                                                    <option value="">Nivel</option>
                                                    <option value="básico">Básico</option>
                                                    <option value="intermedio">Intermedio</option>
                                                    <option value="avanzado">Avanzado</option>
                                                    <option value="nativo">Nativo</option>
                                                </select>
                                            </div>
                                            <div class="col-1">
                                                @if(count($idiomas) > 1)
                                                    <button type="button" class="btn btn-sm btn-danger" wire:click="eliminarIdioma({{ $index }})">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                    <button type="button" class="btn btn-sm btn-secondary mt-2" wire:click="agregarIdioma">
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
                                    <div class="form-check">
                                        <input type="checkbox" wire:model="habilidades_pedagogicas" value="virtual" id="hab_virtual" class="form-check-input">
                                        <label class="form-check-label" for="hab_virtual">Virtual</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" wire:model="habilidades_pedagogicas" value="presencial" id="hab_presencial" class="form-check-input">
                                        <label class="form-check-label" for="hab_presencial">Presencial</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" wire:model="habilidades_pedagogicas" value="dual" id="hab_dual" class="form-check-input">
                                        <label class="form-check-label" for="hab_dual">Dual</label>
                                    </div>
                                    @error('habilidades_pedagogicas')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="especialidades" class="form-label">Especialidades (Redes de Conocimiento)</label>
                                    <select wire:model="especialidades" id="especialidades" class="form-control" multiple>
                                        @foreach($especialidadesList as $especialidad)
                                            <option value="{{ $especialidad->id }}">{{ $especialidad->nombre }}</option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">
                                        Mantenga presionado Ctrl (Cmd en Mac) para seleccionar múltiples especialidades
                                    </small>
                                    @error('especialidades')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información administrativa (opcional) -->
                <div class="col-md-12">
                    <div class="form-section">
                        <h6><i class="fas fa-file-contract"></i> Información Administrativa <small class="text-muted">(Opcional)</small></h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="numero_contrato" class="form-label">Número de Contrato</label>
                                    <input type="text" wire:model="numero_contrato" id="numero_contrato" 
                                           class="form-control @error('numero_contrato') is-invalid @enderror" 
                                           placeholder="Ej: CON-2024-001">
                                    @error('numero_contrato')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fecha_inicio_contrato" class="form-label">Fecha de Inicio de Contrato</label>
                                    <input type="date" wire:model="fecha_inicio_contrato" id="fecha_inicio_contrato" 
                                           class="form-control @error('fecha_inicio_contrato') is-invalid @enderror">
                                    @error('fecha_inicio_contrato')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fecha_fin_contrato" class="form-label">Fecha de Fin de Contrato</label>
                                    <input type="date" wire:model="fecha_fin_contrato" id="fecha_fin_contrato" 
                                           class="form-control @error('fecha_fin_contrato') is-invalid @enderror">
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
                                    <input type="text" wire:model="supervisor_contrato" id="supervisor_contrato" 
                                           class="form-control @error('supervisor_contrato') is-invalid @enderror" 
                                           placeholder="Nombre del supervisor">
                                    @error('supervisor_contrato')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="eps" class="form-label">EPS</label>
                                    <input type="text" wire:model="eps" id="eps" 
                                           class="form-control @error('eps') is-invalid @enderror" 
                                           placeholder="Ej: Sura, Coomeva...">
                                    @error('eps')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="arl" class="form-label">ARL</label>
                                    <input type="text" wire:model="arl" id="arl" 
                                           class="form-control @error('arl') is-invalid @enderror" 
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
                        <button type="submit" class="btn btn-primary" {{ $personasDisponibles->isEmpty() ? 'disabled' : '' }}>
                            <i class="fas fa-save mr-1"></i>Crear Instructor
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

