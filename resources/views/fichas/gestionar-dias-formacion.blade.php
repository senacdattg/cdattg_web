@extends('adminlte::page')

@section('title', 'Gestionar Días de Formación - Ficha ' . $ficha->ficha)

@section('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    @vite(['dias_formacion_css'])
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-calendar-week" 
        title="Gestionar Días de Formación"
        subtitle="Ficha {{ $ficha->ficha }}"
        :breadcrumb="[['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'], ['label' => 'Fichas de Caracterización', 'url' => route('fichaCaracterizacion.index'), 'icon' => 'fa-list'], ['label' => 'Ficha ' . $ficha->ficha, 'url' => route('fichaCaracterizacion.show', $ficha->id), 'icon' => 'fa-eye'], ['label' => 'Días de Formación', 'icon' => 'fa-calendar-week', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
        <div class="col-12">
                    <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('fichaCaracterizacion.show', $ficha->id) }}">
                        <i class="fas fa-arrow-left mr-1"></i> Volver a la Ficha
                    </a>

                    <!-- Información de la Ficha -->
                    <div class="card shadow-sm mb-4 no-hover">
                        <div class="card-header bg-white py-3 d-flex align-items-center">
                            <h5 class="card-title m-0 font-weight-bold text-primary d-flex align-items-center flex-grow-1">
                                <i class="fas fa-info-circle mr-2"></i> Información de la Ficha
                            </h5>
                            <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="collapse"
                                data-target="#fichaInfo" aria-expanded="true">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                </div>

                        <div class="collapse show" id="fichaInfo">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                                        <div class="info-item">
                                            <strong class="text-muted">Programa:</strong><br>
                                            <span class="text-dark font-weight-medium">{{ $ficha->programaFormacion->nombre ?? 'No asignado' }}</span>
                                        </div>
                        </div>
                        <div class="col-md-3">
                                        <div class="info-item">
                                            <strong class="text-muted">Jornada:</strong><br>
                                            <span class="text-dark font-weight-medium">{{ $ficha->jornadaFormacion->jornada ?? 'No asignada' }}</span>
                                        </div>
                        </div>
                        <div class="col-md-3">
                                        <div class="info-item">
                                            <strong class="text-muted">Fecha Inicio:</strong><br>
                                            <span class="text-dark font-weight-medium">{{ $ficha->fecha_inicio ? $ficha->fecha_inicio->format('d/m/Y') : 'No definida' }}</span>
                                        </div>
                        </div>
                        <div class="col-md-3">
                                        <div class="info-item">
                                            <strong class="text-muted">Fecha Fin:</strong><br>
                                            <span class="text-dark font-weight-medium">{{ $ficha->fecha_fin ? $ficha->fecha_fin->format('d/m/Y') : 'No definida' }}</span>
                        </div>
                    </div>
                                </div>
                                <hr class="my-3">
                                <div class="row">
                        <div class="col-md-4">
                                        <div class="info-item">
                                            <strong class="text-muted">Duración:</strong><br>
                                            <span class="text-dark font-weight-medium">{{ $ficha->duracionEnDias() }} días</span>
                                        </div>
                        </div>
                        <div class="col-md-4">
                                        <div class="info-item">
                                            <strong class="text-muted">Horas Totales Actuales:</strong><br>
                            <span class="text-primary font-weight-bold">{{ $horasTotalesActuales }} horas</span>
                                        </div>
                        </div>
                        <div class="col-md-4">
                                        <div class="info-item">
                                            <strong class="text-muted">Días Asignados:</strong><br>
                                            <span class="text-dark font-weight-medium">{{ $diasAsignados->count() }} días</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

                    @if($ficha->jornadaFormacion)
    <!-- Configurar Horarios y Agregar Días -->
                        <div class="card shadow-sm mb-4 no-hover">
                            <div class="card-header bg-white py-3 d-flex align-items-center">
                                <h5 class="card-title m-0 font-weight-bold text-primary d-flex align-items-center flex-grow-1">
                                    <i class="fas fa-calendar-plus mr-2"></i> Configurar Horarios y Agregar Días
                                </h5>
                                <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="collapse"
                                    data-target="#configurarHorarios" aria-expanded="true">
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                </div>

                            <div class="collapse show" id="configurarHorarios">
                <div class="card-body">
                        <!-- Configuración de Horarios -->
                                    <div class="mb-4">
                                        <h6 class="text-dark mb-3">
                                            <i class="fas fa-clock mr-2"></i>Configuración de Horarios
                                        </h6>
                                        <div class="row">
                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="text-muted small">Hora de Inicio</label>
                                <input type="time" id="hora_inicio_global" class="form-control" 
                                       value="{{ $ficha->jornadaFormacion->hora_inicio }}"
                                       @if($ficha->jornadaFormacion->jornada == 'MAÑANA')
                                           min="06:00" max="13:00" step="3600"
                                       @elseif($ficha->jornadaFormacion->jornada == 'TARDE')
                                           min="13:00" max="17:59" step="3600"
                                       @elseif($ficha->jornadaFormacion->jornada == 'NOCHE')
                                           min="18:00" max="22:59" step="3600"
                                       @elseif($ficha->jornadaFormacion->jornada == 'FINES DE SEMANA')
                                           min="08:00" max="16:59" step="3600"
                                       @endif>
                                                </div>
                            </div>
                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="text-muted small">Hora de Fin</label>
                                <input type="time" id="hora_fin_global" class="form-control" 
                                       value="{{ $ficha->jornadaFormacion->hora_fin }}"
                                       @if($ficha->jornadaFormacion->jornada == 'MAÑANA')
                                           min="06:00" max="13:00" step="3600"
                                       @elseif($ficha->jornadaFormacion->jornada == 'TARDE')
                                           min="13:00" max="17:59" step="3600"
                                       @elseif($ficha->jornadaFormacion->jornada == 'NOCHE')
                                           min="18:00" max="22:59" step="3600"
                                       @elseif($ficha->jornadaFormacion->jornada == 'FINES DE SEMANA')
                                           min="08:00" max="16:59" step="3600"
                                       @endif>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="text-muted small">&nbsp;</label>
                                                    <button type="button" class="btn btn-primary btn-block" onclick="aplicarHorarioGlobal()">
                                                        <i class="fas fa-sync-alt mr-1"></i>Aplicar a Todos
                                                    </button>
                                                </div>
                                            </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info mb-4">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            <div>
                            <strong>Jornada:</strong> {{ $ficha->jornadaFormacion->jornada }}<br>
                            <strong>Restricciones de horarios:</strong><br>
                            @if($ficha->jornadaFormacion->jornada == 'MAÑANA')
                                • <strong>MAÑANA:</strong> 06:00 - 13:00 horas (solo horas completas)
                            @elseif($ficha->jornadaFormacion->jornada == 'TARDE')
                                • <strong>TARDE:</strong> 13:00 - 17:59 horas (solo horas completas)
                            @elseif($ficha->jornadaFormacion->jornada == 'NOCHE')
                                • <strong>NOCHE:</strong> 18:00 - 22:59 horas (solo horas completas)
                            @elseif($ficha->jornadaFormacion->jornada == 'FINES DE SEMANA')
                                • <strong>FINES DE SEMANA:</strong> 08:00 - 16:59 horas (solo horas completas)
                            @endif
                            <br><strong>Nota:</strong> Los horarios deben respetar las restricciones de la jornada seleccionada.
                                            </div>
                                        </div>
                        </div>

                        <!-- Días Seleccionados -->
                        <div id="dias-seleccionados" class="mb-4" style="display: none;">
                                        <h6 class="text-dark mb-3">
                                            <i class="fas fa-list mr-2"></i>Días Seleccionados
                            </h6>
                            <div id="lista-dias-seleccionados"></div>
                        </div>

                        <!-- Selección de Días con Cuadros -->
                        <div class="mb-4">
                                        <h6 class="text-dark mb-3">
                                            <i class="fas fa-calendar-week mr-2"></i>Seleccionar Días de Formación
                                        </h6>
                            <div class="row">
                                @foreach($diasSemana as $dia)
                                    @php
                                        $estaAsignado = $diasAsignados->contains('dia_id', $dia->id);
                                        $esPermitido = $ficha->jornada_id && isset($configuracionJornadas[$ficha->jornada_id]) 
                                            ? in_array($dia->id, $configuracionJornadas[$ficha->jornada_id]['dias_permitidos'])
                                            : true;
                                    @endphp
                                    <div class="col-md-2 mb-3">
                                        <div class="dia-cuadro {{ $estaAsignado ? 'asignado' : ($esPermitido ? 'disponible' : 'no-permitido') }}" 
                                             data-dia-id="{{ $dia->id }}" 
                                             data-dia-nombre="{{ $dia->name }}"
                                             onclick="{{ $esPermitido && !$estaAsignado ? 'seleccionarDia(this)' : '' }}">
                                            <div class="text-center">
                                                <i class="fas fa-calendar-day fa-lg mb-1"></i>
                                                <div class="dia-nombre">{{ $dia->name }}</div>
                                                @if($estaAsignado)
                                                    <small class="text-success">
                                                        <i class="fas fa-check"></i> Asignado
                                                    </small>
                                                @elseif($esPermitido)
                                                    <small class="text-muted">Click para seleccionar</small>
                                                @else
                                                    <small class="text-danger">
                                                        <i class="fas fa-ban"></i> No permitido
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                        </div>
                                    </div>

                                    <!-- Formulario Oculto para Envío -->
                                    <form action="{{ route('fichaCaracterizacion.guardarDiasFormacion', $ficha->id) }}" method="POST" id="formDiasFormacion">
                                        @csrf
                                        <div id="dias-container" style="display: none;">
                                            <!-- Los días se agregarán dinámicamente aquí -->
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                            <a href="{{ route('fichaCaracterizacion.show', $ficha->id) }}" class="btn btn-outline-secondary">
                                                <i class="fas fa-arrow-left mr-1"></i> Volver
                                            </a>
                                            <button type="button" class="btn btn-success" onclick="guardarDiasSeleccionados()" id="btn-guardar-dias" style="display: none;">
                                                <i class="fas fa-check mr-1"></i> Guardar Días Seleccionados
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="card shadow-sm mb-4 no-hover">
                            <div class="card-body">
                        <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Atención:</strong> No se ha asignado una jornada a esta ficha. Por favor, configure primero la jornada en la información general de la ficha.
                        </div>
                </div>
            </div>
                    @endif

    <!-- Días Asignados -->
                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3 d-flex align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary d-flex flex-grow-1">
                                <i class="fas fa-calendar-check mr-2"></i>Días de Formación Asignados
                            </h6>
                            <div class="badge badge-primary">{{ $diasAsignados->count() }} días</div>
                </div>
                        <div class="card-body p-0">
                    @if($diasAsignados->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-borderless table-striped mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th class="px-4 py-3" style="width: 20%">Día</th>
                                                <th class="px-4 py-3" style="width: 25%">Horario</th>
                                                <th class="px-4 py-3" style="width: 20%">Duración</th>
                                                <th class="px-4 py-3 text-center" style="width: 35%">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                        @foreach($diasAsignados as $diaAsignado)
                                                <tr>
                                                    <td class="px-4">
                                                        <div class="d-flex align-items-center">
                                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mr-2"
                                                                style="width: 32px; height: 32px;">
                                                                <i class="fas fa-calendar-day text-white" style="font-size: 12px;"></i>
                                                            </div>
                                                            <span class="font-weight-medium">{{ $diaAsignado->dia->name }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="px-4">
                                                        <span class="text-muted">
                                                            <i class="fas fa-clock mr-1"></i>
                                                {{ $diaAsignado->hora_inicio }} - {{ $diaAsignado->hora_fin }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4">
                                                        <span class="text-muted">
                                                            <i class="fas fa-hourglass-half mr-1"></i>
                                                {{ $diaAsignado->calcularHorasDia() }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 text-center">
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-light btn-sm" 
                                                                    onclick="editarDia({{ $diaAsignado->id }}, '{{ $diaAsignado->hora_inicio }}', '{{ $diaAsignado->hora_fin }}')"
                                                                    data-toggle="tooltip" title="Editar horarios">
                                                                <i class="fas fa-pencil-alt text-info"></i>
                                            </button>
                                            <form action="{{ route('fichaCaracterizacion.eliminarDiaFormacion', [$ficha->id, $diaAsignado->id]) }}" 
                                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-light btn-sm" 
                                                                        onclick="return confirm('¿Está seguro de eliminar este día de formación?')"
                                                                        data-toggle="tooltip" title="Eliminar día">
                                                                    <i class="fas fa-trash text-danger"></i>
                                                </button>
                                            </form>
                                                </div>
                                                    </td>
                                                </tr>
                        @endforeach
                                        </tbody>
                                    </table>
                        </div>
                    @else
                                <div class="text-center py-5">
                                    <div class="mb-3">
                                        <i class="fas fa-calendar-times text-muted" style="font-size: 3rem;"></i>
                                    </div>
                                    <h5 class="text-muted">No hay días de formación asignados</h5>
                                    <p class="text-muted">Use la sección superior para agregar días de formación a esta ficha.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
        </div>
    </section>

    <!-- Modal para Editar Día -->
    <div class="modal fade" id="modalEditarDia" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit mr-2"></i>Editar Día de Formación
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="formEditarDia" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_hora_inicio">Hora de Inicio</label>
                            <input type="time" name="hora_inicio" id="edit_hora_inicio" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_hora_fin">Hora de Fin</label>
                            <input type="time" name="hora_fin" id="edit_hora_fin" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    @vite(['resources/js/pages/gestion-especializada.js'])
@endsection

@section('footer')
    @include('layout.footer')
@endsection