@extends('adminlte::page')

@section('title', 'Gestionar Días de Formación - Ficha ' . $ficha->ficha)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-calendar-week text-primary"></i>
            Gestionar Días de Formación - Ficha {{ $ficha->ficha }}
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
                            <strong>Jornada:</strong><br>
                            <span class="text-muted">{{ $ficha->jornadaFormacion->jornada ?? 'No asignada' }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Fecha Inicio:</strong><br>
                            <span class="text-muted">{{ $ficha->fecha_inicio ? $ficha->fecha_inicio->format('d/m/Y') : 'No definida' }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Fecha Fin:</strong><br>
                            <span class="text-muted">{{ $ficha->fecha_fin ? $ficha->fecha_fin->format('d/m/Y') : 'No definida' }}</span>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <strong>Duración:</strong><br>
                            <span class="text-muted">{{ $ficha->duracionEnDias() }} días</span>
                        </div>
                        <div class="col-md-4">
                            <strong>Horas Totales Actuales:</strong><br>
                            <span class="text-primary font-weight-bold">{{ $horasTotalesActuales }} horas</span>
                        </div>
                        <div class="col-md-4">
                            <strong>Días Asignados:</strong><br>
                            <span class="text-muted">{{ $diasAsignados->count() }} días</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PASO 1: Configuración de Horarios -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock text-primary"></i>
                        <span class="badge badge-primary mr-2">1</span>
                        Configurar Horarios Según Jornada
                    </h3>
                </div>
                <div class="card-body">
                    @if($ficha->jornadaFormacion)
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">
                                    <i class="fas fa-play-circle"></i> Hora de Inicio
                                </label>
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
                            <div class="col-md-4">
                                <label class="form-label">
                                    <i class="fas fa-stop-circle"></i> Hora de Fin
                                </label>
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
                            <div class="col-md-4">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-primary btn-block" onclick="aplicarHorarioGlobal()">
                                    <i class="fas fa-magic"></i> Aplicar Horario a Todos los Días
                                </button>
                            </div>
                        </div>
                        <div class="alert alert-info mt-3 mb-0">
                            <i class="fas fa-info-circle"></i>
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
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Atención:</strong> No se ha asignado una jornada a esta ficha. Por favor, configure primero la jornada en la información general de la ficha.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- PASO 2: Agregar Días de Formación -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-plus text-success"></i>
                        <span class="badge badge-success mr-2">2</span>
                        Agregar Días de Formación
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Información de Jornada -->
                    @if($ficha->jornada_id && isset($configuracionJornadas[$ficha->jornada_id]))
                        <div class="alert alert-info mb-3">
                            <h5><i class="fas fa-info-circle"></i> Jornada: {{ $configuracionJornadas[$ficha->jornada_id]['nombre'] }}</h5>
                            <p class="mb-1"><strong>Días permitidos:</strong> 
                                @php
                                    $diasPermitidos = collect($configuracionJornadas[$ficha->jornada_id]['dias_permitidos'])
                                        ->map(function($diaId) use ($diasSemana) {
                                            return $diasSemana->where('id', $diaId)->first()->name ?? '';
                                        })
                                        ->filter()
                                        ->implode(', ');
                                @endphp
                                {{ $diasPermitidos }}
                            </p>
                            <p class="mb-0"><strong>Horario típico:</strong> {{ $configuracionJornadas[$ficha->jornada_id]['horario_tipico'][0] }} - {{ $configuracionJornadas[$ficha->jornada_id]['horario_tipico'][1] }}</p>
                        </div>
                    @endif

                    <!-- Formulario para agregar días -->
                    <form action="{{ route('fichaCaracterizacion.guardarDiasFormacion', $ficha->id) }}" method="POST" id="formDiasFormacion">
                        @csrf
                        
                        <div id="dias-container">
                            <!-- Los días se agregarán dinámicamente aquí -->
                        </div>
                        
                        <div class="form-group">
                            <button type="button" class="btn btn-success" onclick="agregarDia()">
                                <i class="fas fa-plus"></i> Agregar Día
                            </button>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Días de Formación
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

    <!-- PASO 3: Días Asignados -->
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-check text-info"></i>
                        <span class="badge badge-info mr-2">3</span>
                        Días de Formación Asignados
                        <span class="badge badge-primary ml-2">{{ $diasAsignados->count() }} días</span>
                    </h3>
                </div>
                <div class="card-body">
                    @if($diasAsignados->count() > 0)
                        <div class="row">
                            @foreach($diasAsignados as $diaAsignado)
                                <div class="col-md-6 mb-3">
                                    <div class="card border-success">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h5 class="mb-1">
                                                        <i class="fas fa-calendar-day text-success"></i>
                                                        {{ $diaAsignado->dia->name }}
                                                    </h5>
                                                    <p class="text-muted mb-1">
                                                        <i class="fas fa-clock"></i>
                                                        {{ $diaAsignado->hora_inicio }} - {{ $diaAsignado->hora_fin }}
                                                    </p>
                                                    <p class="text-muted mb-0">
                                                        <i class="fas fa-hourglass-half"></i>
                                                        @if($diaAsignado->hora_inicio && $diaAsignado->hora_fin)
                                                            @try
                                                                {{ \Carbon\Carbon::parse($diaAsignado->hora_inicio)->diffInHours(\Carbon\Carbon::parse($diaAsignado->hora_fin)) }} horas por día
                                                            @catch(Exception $e)
                                                                Horas no calculables
                                                            @endtry
                                                        @else
                                                            Horas no definidas
                                                        @endif
                                                    </p>
                                                </div>
                                                <div>
                                                    <button type="button" class="btn btn-sm btn-warning" 
                                                            onclick="editarDia({{ $diaAsignado->id }}, '{{ $diaAsignado->hora_inicio }}', '{{ $diaAsignado->hora_fin }}')">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <form action="{{ route('fichaCaracterizacion.eliminarDiaFormacion', [$ficha->id, $diaAsignado->id]) }}" 
                                                          method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                                onclick="return confirm('¿Está seguro de eliminar este día de formación?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-calendar-times fa-3x mb-3"></i>
                            <h5>No hay días de formación asignados</h5>
                            <p>Use el paso 2 para agregar días de formación a esta ficha.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


    <!-- Modal para Editar Día -->
    <div class="modal fade" id="modalEditarDia" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit"></i> Editar Día de Formación
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

@stop

@section('css')
    <style>
        .card.border-success {
            border-color: #28a745 !important;
        }
        .card.border-primary {
            border-color: #007bff !important;
        }
        .card.border-secondary {
            border-color: #6c757d !important;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Cargar días existentes
            cargarDiasExistentes();
        });

        // Función para cargar días ya asignados
        function cargarDiasExistentes() {
            @if($diasAsignados->count() > 0)
                @foreach($diasAsignados as $diaAsignado)
                    agregarDiaRow(
                        {{ $diaAsignado->dia_id }},
                        '{{ $diaAsignado->dia->name }}',
                        '{{ $diaAsignado->hora_inicio }}',
                        '{{ $diaAsignado->hora_fin }}'
                    );
                @endforeach
            @endif
        }

        // Función para agregar un día desde el botón
        function agregarDia() {
            agregarDiaRow(null, '', '', '');
        }

        // Función para agregar un día seleccionado de la tabla
        function agregarDiaSeleccionado(diaId, nombreDia) {
            agregarDiaRow(diaId, nombreDia, '', '');
        }

        // Función para agregar una fila de día
        function agregarDiaRow(diaId, nombreDia, horaInicio, horaFin) {
            const container = document.getElementById('dias-container');
            const index = container.children.length;
            
            const div = document.createElement('div');
            div.className = 'card mb-2 dia-row';
            div.innerHTML = `
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <label class="form-label">Día</label>
                            <select name="dias[${index}][dia_id]" class="form-control dia-select" required>
                                <option value="">Seleccione un día</option>
                                @foreach($diasSemana as $dia)
                                    <option value="{{ $dia->id }}" ${diaId == {{ $dia->id }} ? 'selected' : ''}>
                                        {{ $dia->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="dias[${index}][hora_inicio]" class="hora-inicio" value="${horaInicio}">
                        <input type="hidden" name="dias[${index}][hora_fin]" class="hora-fin" value="${horaFin}">
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-sm btn-danger d-block" onclick="eliminarDia(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            container.appendChild(div);
        }

        // Función para eliminar un día
        function eliminarDia(button) {
            const row = button.closest('.dia-row');
            row.remove();
            
            // Renumerar los índices
            renumerarIndices();
        }

        // Función para renumerar los índices de los días
        function renumerarIndices() {
            const container = document.getElementById('dias-container');
            const rows = container.querySelectorAll('.dia-row');
            
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

        // Función para editar un día existente
        function editarDia(diaId, horaInicio, horaFin) {
            document.getElementById('edit_hora_inicio').value = horaInicio;
            document.getElementById('edit_hora_fin').value = horaFin;
            document.getElementById('formEditarDia').action = '{{ route("fichaCaracterizacion.actualizarDiaFormacion", [$ficha->id, ":diaId"]) }}'.replace(':diaId', diaId);
            $('#modalEditarDia').modal('show');
        }

        // Validación del formulario
        document.getElementById('formDiasFormacion').addEventListener('submit', function(e) {
            const diasSeleccionados = document.querySelectorAll('.dia-select');
            
            if (diasSeleccionados.length === 0) {
                e.preventDefault();
                alert('Debe agregar al menos un día de formación.');
                return;
            }
            
            // Verificar que no haya días duplicados
            const diasIds = Array.from(diasSeleccionados).map(select => select.value);
            const diasUnicos = [...new Set(diasIds)];
            
            if (diasIds.length !== diasUnicos.length) {
                e.preventDefault();
                alert('No puede asignar el mismo día más de una vez.');
                return;
            }
            
            // Verificar que todos los días tengan horarios válidos
            const horasInicio = document.querySelectorAll('input[name*="[hora_inicio]"]');
            const horasFin = document.querySelectorAll('input[name*="[hora_fin]"]');
            
            for (let i = 0; i < horasInicio.length; i++) {
                if (horasInicio[i].value >= horasFin[i].value) {
                    e.preventDefault();
                    alert('La hora de fin debe ser posterior a la hora de inicio.');
                    return;
                }
            }
        });

        // Cálculo automático de horas totales
        function calcularHorasTotales() {
            const diasRows = document.querySelectorAll('.dia-row');
            let horasTotales = 0;
            
            diasRows.forEach(row => {
                const horaInicio = row.querySelector('input[name*="[hora_inicio]"]').value;
                const horaFin = row.querySelector('input[name*="[hora_fin]"]').value;
                
                if (horaInicio && horaFin) {
                    const inicio = new Date('2000-01-01 ' + horaInicio);
                    const fin = new Date('2000-01-01 ' + horaFin);
                    const horas = (fin - inicio) / (1000 * 60 * 60);
                    horasTotales += horas;
                }
            });
            
            // Mostrar horas totales estimadas
            if (horasTotales > 0) {
                const duracionDias = {{ $ficha->duracionEnDias() }};
                const horasEstimadas = horasTotales * duracionDias;
                
                // Crear o actualizar indicador de horas estimadas
                let indicador = document.getElementById('horas-estimadas');
                if (!indicador) {
                    indicador = document.createElement('div');
                    indicador.id = 'horas-estimadas';
                    indicador.className = 'alert alert-info mt-3';
                    document.getElementById('formDiasFormacion').appendChild(indicador);
                }
                
                indicador.innerHTML = `
                    <i class="fas fa-calculator"></i>
                    <strong>Horas estimadas totales:</strong> ${Math.round(horasEstimadas)} horas 
                    (${horasTotales.toFixed(1)} horas/día × ${duracionDias} días)
                `;
            }
        }

        // Función para validar horarios según jornada
        function validarHorariosSegunJornada(horaInicio, horaFin) {
            @if($ficha->jornadaFormacion)
                const jornada = '{{ $ficha->jornadaFormacion->jornada }}';
                
                // Convertir horas a objetos Date para comparación
                const inicio = new Date('2000-01-01 ' + horaInicio);
                const fin = new Date('2000-01-01 ' + horaFin);
                
                switch(jornada.toUpperCase()) {
                    case 'MAÑANA':
                        const mananaMin = new Date('2000-01-01 06:00');
                        const mananaMax = new Date('2000-01-01 13:00');
                        
                        if (inicio < mananaMin || fin > mananaMax) {
                            return {
                                valido: false,
                                mensaje: 'Para la jornada MAÑANA, los horarios deben estar entre las 06:00 y 13:00 horas.'
                            };
                        }
                        break;
                        
                    case 'TARDE':
                        const tardeMin = new Date('2000-01-01 13:00');
                        const tardeMax = new Date('2000-01-01 17:59');
                        
                        if (inicio < tardeMin || fin > tardeMax) {
                            return {
                                valido: false,
                                mensaje: 'Para la jornada TARDE, los horarios deben estar entre las 13:00 y 17:59 horas.'
                            };
                        }
                        break;
                        
                    case 'NOCHE':
                        const nocheMin = new Date('2000-01-01 18:00');
                        const nocheMax = new Date('2000-01-01 22:59');
                        
                        if (inicio < nocheMin || fin > nocheMax) {
                            return {
                                valido: false,
                                mensaje: 'Para la jornada NOCHE, los horarios deben estar entre las 18:00 y 22:59 horas.'
                            };
                        }
                        break;
                        
                    case 'FINES DE SEMANA':
                        const fsMin = new Date('2000-01-01 08:00');
                        const fsMax = new Date('2000-01-01 16:59');
                        
                        if (inicio < fsMin || fin > fsMax) {
                            return {
                                valido: false,
                                mensaje: 'Para FINES DE SEMANA, los horarios deben estar entre las 08:00 y 16:59 horas.'
                            };
                        }
                        break;
                }
            @endif
            
            return { valido: true, mensaje: '' };
        }

        // Función para aplicar horario global a todos los días
        function aplicarHorarioGlobal() {
            const horaInicio = document.getElementById('hora_inicio_global').value;
            const horaFin = document.getElementById('hora_fin_global').value;
            
            if (!horaInicio || !horaFin) {
                alert('Por favor, configure tanto la hora de inicio como la hora de fin.');
                return;
            }
            
            if (horaInicio >= horaFin) {
                alert('La hora de fin debe ser posterior a la hora de inicio.');
                return;
            }
            
            // Validar horarios según jornada
            const validacion = validarHorariosSegunJornada(horaInicio, horaFin);
            if (!validacion.valido) {
                alert(validacion.mensaje);
                return;
            }
            
            // Aplicar a todos los campos de hora existentes
            const camposHoraInicio = document.querySelectorAll('.hora-inicio');
            const camposHoraFin = document.querySelectorAll('.hora-fin');
            
            if (camposHoraInicio.length === 0) {
                alert('No hay días seleccionados para aplicar el horario. Por favor, agregue días primero.');
                return;
            }
            
            camposHoraInicio.forEach(campo => {
                campo.value = horaInicio;
            });
            
            camposHoraFin.forEach(campo => {
                campo.value = horaFin;
            });
            
            // Recalcular horas totales
            calcularHorasTotales();
            
            // Mostrar mensaje de éxito
            alert(`Horario aplicado exitosamente:\nInicio: ${horaInicio}\nFin: ${horaFin}\n\nSe aplicó a ${camposHoraInicio.length} días.`);
        }

        // Función para validar horarios en tiempo real
        function validarHorariosTiempoReal() {
            const horaInicio = document.getElementById('hora_inicio_global').value;
            const horaFin = document.getElementById('hora_fin_global').value;
            
            if (horaInicio && horaFin) {
                const validacion = validarHorariosSegunJornada(horaInicio, horaFin);
                
                // Remover alertas previas
                const alertasPrevias = document.querySelectorAll('.alert-horario-jornada');
                alertasPrevias.forEach(alerta => alerta.remove());
                
                if (!validacion.valido) {
                    // Crear alerta de error
                    const alerta = document.createElement('div');
                    alerta.className = 'alert alert-danger alert-horario-jornada mt-2';
                    alerta.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${validacion.mensaje}`;
                    
                    // Insertar después del botón
                    const boton = document.querySelector('button[onclick="aplicarHorarioGlobal()"]');
                    boton.parentNode.insertAdjacentElement('afterend', alerta);
                }
            }
        }

        // Función para actualizar restricciones dinámicamente
        function actualizarRestriccionesHorarios() {
            const horaInicio = document.getElementById('hora_inicio_global');
            const horaFin = document.getElementById('hora_fin_global');
            
            // Cuando cambie la hora de inicio, actualizar el min de hora de fin
            horaInicio.addEventListener('change', function() {
                const valorInicio = this.value;
                if (valorInicio) {
                    // Establecer el min de hora fin como la hora de inicio + 1 hora
                    const fechaInicio = new Date('2000-01-01 ' + valorInicio);
                    fechaInicio.setHours(fechaInicio.getHours() + 1);
                    const minHoraFin = fechaInicio.toTimeString().slice(0, 5);
                    
                    horaFin.min = minHoraFin;
                    
                    // Si la hora fin actual es menor que la nueva restricción, ajustarla
                    if (horaFin.value && horaFin.value <= valorInicio) {
                        horaFin.value = minHoraFin;
                    }
                }
                
                validarHorariosTiempoReal();
            });
            
            // Cuando cambie la hora de fin, validar que sea posterior a inicio
            horaFin.addEventListener('change', function() {
                const valorFin = this.value;
                const valorInicio = horaInicio.value;
                
                if (valorInicio && valorFin && valorFin <= valorInicio) {
                    alert('La hora de fin debe ser posterior a la hora de inicio.');
                    // Ajustar automáticamente a 1 hora después del inicio
                    const fechaInicio = new Date('2000-01-01 ' + valorInicio);
                    fechaInicio.setHours(fechaInicio.getHours() + 1);
                    this.value = fechaInicio.toTimeString().slice(0, 5);
                }
                
                validarHorariosTiempoReal();
            });
        }

        // Agregar event listeners para el cálculo automático y validación en tiempo real
        document.addEventListener('change', function(e) {
            if (e.target.matches('input[name*="[hora_inicio]"], input[name*="[hora_fin]"]')) {
                calcularHorasTotales();
            }
            
            // Validar horarios globales en tiempo real
            if (e.target.id === 'hora_inicio_global' || e.target.id === 'hora_fin_global') {
                validarHorariosTiempoReal();
            }
        });

        // Inicializar restricciones dinámicas cuando se carga la página
        document.addEventListener('DOMContentLoaded', function() {
            actualizarRestriccionesHorarios();
        });
    </script>
@stop
