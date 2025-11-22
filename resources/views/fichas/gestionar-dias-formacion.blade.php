@extends('adminlte::page')

@section('title', 'Gestionar Días de Formación - Ficha ' . $ficha->ficha)

@section('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    @vite(['resources/css/parametros.css'])
    <style>
        /* Estilos para días fuera del rango de fechas */
        .dia-cuadro.fuera-rango {
            opacity: 0.5;
            cursor: not-allowed;
            background-color: #f8f9fa !important;
            border-color: #dee2e6 !important;
        }
        
        .dia-cuadro.fuera-rango:hover {
            transform: none;
            box-shadow: none;
        }
    </style>
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
                        <div class="alert alert-info mb-4">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            <div>
                            <strong>Jornada:</strong> {{ $ficha->jornadaFormacion->jornada }}<br>
                                    <strong>Nota:</strong> Los horarios se configurarán automáticamente según la jornada seleccionada cuando marque los días de formación.
                                            </div>
                                        </div>
                        </div>

                        <!-- Selección de Días de Formación -->
                        <div class="mb-4">
                                        <h6 class="text-dark mb-3">
                                            <i class="fas fa-calendar-week mr-2"></i>Seleccionar Días de Formación
                                        </h6>
                            <div class="row" id="dias-formacion-container">
                                @foreach($diasSemana as $dia)
                                    @php
                                        $estaAsignado = $diasAsignados->contains('dia_id', $dia->id);
                                        $diaAsignado = $diasAsignados->firstWhere('dia_id', $dia->id);
                                    @endphp
                                    <div class="col-md-3 col-sm-4 col-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" 
                                                   class="custom-control-input dia-formacion-checkbox" 
                                                   id="dia_{{ $dia->id }}" 
                                                   name="dias_formacion[]" 
                                                   value="{{ $dia->id }}"
                                                   {{ $estaAsignado ? 'checked' : '' }}
                                                   {{ $estaAsignado ? 'data-asignado="true"' : '' }}>
                                            <label class="custom-control-label" for="dia_{{ $dia->id }}">
                                                {{ $dia->name }}
                                                @if($estaAsignado)
                                                    <small class="text-success d-block">
                                                        <i class="fas fa-check"></i> Ya asignado
                                                    </small>
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                                        </div>
                            
                            <!-- Contenedor de Horarios -->
                            <div class="row mt-3" id="horarios-container" style="display: none;">
                                <div class="col-md-12">
                                    <h6 class="fw-bold text-primary mb-3">
                                        <i class="fas fa-clock mr-2"></i>Horarios por Día
                                    </h6>
                                    <div id="horarios-dias" class="row">
                                        <!-- Los horarios se generarán dinámicamente aquí -->
                                    </div>
                                </div>
                            </div>
                                    </div>

                        <!-- Formulario para Guardar -->
                                    <form action="{{ route('fichaCaracterizacion.guardarDiasFormacion', $ficha->id) }}" method="POST" id="formDiasFormacion">
                                        @csrf
                                        <div id="dias-container" style="display: none;">
                                            <!-- Los días se agregarán dinámicamente aquí -->
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                            <a href="{{ route('fichaCaracterizacion.show', $ficha->id) }}" class="btn btn-outline-secondary">
                                                <i class="fas fa-arrow-left mr-1"></i> Volver
                                            </a>
                                <button type="submit" class="btn btn-success" id="btn-guardar-dias" style="display: none;">
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
                                                                  method="POST" class="d-inline form-eliminar-dia" 
                                                                  data-dia-id="{{ $diaAsignado->id }}"
                                                                  data-dia-nombre="{{ $diaAsignado->dia->name }}">
                                                @csrf
                                                @method('DELETE')
                                                                <button type="button" class="btn btn-light btn-sm btn-eliminar-dia" 
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
    <script>
        // Pasar datos de la ficha al JavaScript
        window.fichaId = {{ $ficha->id }};
        window.fichaFechaInicio = @json($ficha->fecha_inicio ? $ficha->fecha_inicio->format('Y-m-d') : null);
        window.fichaFechaFin = @json($ficha->fecha_fin ? $ficha->fecha_fin->format('Y-m-d') : null);
        
        // Jornada de la ficha para generar horarios automáticamente
        @if($ficha->jornadaFormacion)
            window.fichaJornadaNombre = @json($ficha->jornadaFormacion->jornada);
            window.fichaJornadaId = {{ $ficha->jornada_id }};
        @else
            window.fichaJornadaNombre = null;
            window.fichaJornadaId = null;
        @endif
        
        // Mapeo de IDs de días a días de la semana (0 = Domingo, 1 = Lunes, ..., 6 = Sábado)
        window.mapeoDiasFormacion = {
            12: 1, // LUNES -> 1
            13: 2, // MARTES -> 2
            14: 3, // MIÉRCOLES -> 3
            15: 4, // JUEVES -> 4
            16: 5, // VIERNES -> 5
            17: 6, // SÁBADO -> 6
            18: 0  // DOMINGO -> 0
        };
    </script>
    @vite(['resources/js/pages/fichas-form.js'])
    <script>
        // Cargar horarios de días ya asignados al cargar la página
        // Función de validación manual para días según fechas de la ficha
        function validarDiasFormacionManual() {
            const fechaInicio = window.fichaFechaInicio;
            const fechaFin = window.fichaFechaFin;
            
            if (!fechaInicio || !fechaFin) {
                return;
            }
            
            const mapeoDias = {
                12: 1, // LUNES -> 1
                13: 2, // MARTES -> 2
                14: 3, // MIÉRCOLES -> 3
                15: 4, // JUEVES -> 4
                16: 5, // VIERNES -> 5
                17: 6, // SÁBADO -> 6
                18: 0  // DOMINGO -> 0
            };
            
            const parsearFecha = (fechaStr) => {
                const partes = fechaStr.split('-');
                return new Date(parseInt(partes[0]), parseInt(partes[1]) - 1, parseInt(partes[2]));
            };
            
            const fechaInicioObj = parsearFecha(fechaInicio);
            const fechaFinObj = parsearFecha(fechaFin);
            const diasEnRango = new Set();
            
            const fechaActual = new Date(fechaInicioObj);
            while (fechaActual <= fechaFinObj) {
                const diaSemana = fechaActual.getDay();
                diasEnRango.add(diaSemana);
                fechaActual.setDate(fechaActual.getDate() + 1);
            }
            
            console.log('Validación manual de días:', {
                fechaInicio,
                fechaFin,
                diasEnRango: Array.from(diasEnRango)
            });
            
            let diasDeshabilitados = [];
            $('.dia-formacion-checkbox').each(function() {
                const checkbox = $(this);
                const diaId = parseInt(checkbox.val());
                const diaSemana = mapeoDias[diaId];
                const estaAsignado = checkbox.attr('data-asignado') === 'true';
                
                if (diaSemana !== undefined) {
                    if (estaAsignado) {
                        // Si ya está asignado, mantenerlo habilitado pero marcado
                        checkbox.prop('disabled', false);
                    } else if (diasEnRango.has(diaSemana)) {
                        // El día está en el rango, habilitarlo
                        checkbox.prop('disabled', false);
                        checkbox.closest('.custom-control').removeClass('text-muted');
                    } else {
                        // El día no está en el rango, deshabilitarlo y desmarcarlo
                        checkbox.prop('disabled', true);
                        checkbox.prop('checked', false);
                        checkbox.closest('.custom-control').addClass('text-muted');
                        const nombreDia = checkbox.next('label').text().trim().split('\n')[0].trim();
                        diasDeshabilitados.push(nombreDia);
                        console.log(`Día deshabilitado: ${nombreDia} (ID: ${diaId}, Día semana: ${diaSemana})`);
                    }
                }
            });
            
            if (diasDeshabilitados.length > 0) {
                let mensajeInfo = $('#mensaje-dias-formacion');
                if (mensajeInfo.length === 0) {
                    $('#dias-formacion-container').parent().after(`
                        <div class="alert alert-info mt-2" id="mensaje-dias-formacion">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Nota:</strong> Los días <strong>${diasDeshabilitados.join(', ')}</strong> han sido deshabilitados porque no están dentro del rango de fechas de la ficha (${fechaInicio} a ${fechaFin}).
                        </div>
                    `);
                } else {
                    mensajeInfo.html(`
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Nota:</strong> Los días <strong>${diasDeshabilitados.join(', ')}</strong> han sido deshabilitados porque no están dentro del rango de fechas de la ficha (${fechaInicio} a ${fechaFin}).
                    `);
                }
            } else {
                $('#mensaje-dias-formacion').remove();
            }
        }
        
        $(document).ready(function() {
            // Ejecutar validación de días según fechas al cargar (siempre)
            if (window.fichaFechaInicio && window.fichaFechaFin) {
                // Ejecutar validación manual primero
                validarDiasFormacionManual();
                
                // También intentar usar la función global si está disponible
                setTimeout(function() {
                    if (typeof validarDiasFormacionSegunFechas === 'function') {
                        validarDiasFormacionSegunFechas();
                    }
                }, 100);
            }
            
            // Cargar horarios de días ya asignados
            @if($diasAsignados->count() > 0)
                @php
                    $diasAsignadosData = $diasAsignados->map(function($dia) {
                        $horaInicio = $dia->hora_inicio ? substr($dia->hora_inicio, 0, 5) : '';
                        $horaFin = $dia->hora_fin ? substr($dia->hora_fin, 0, 5) : '';
                        return [
                            'dia_id' => $dia->dia_id,
                            'hora_inicio' => $horaInicio,
                            'hora_fin' => $horaFin
                        ];
                    })->values();
                @endphp
                const diasAsignados = @json($diasAsignadosData);
                
                // Esperar a que el script de fichas-form.js esté cargado
                setTimeout(function() {
                    // Marcar checkboxes y preparar horarios
                    diasAsignados.forEach(function(dia) {
                        const checkbox = $(`#dia_${dia.dia_id}`);
                        if (checkbox.length) {
                            checkbox.prop('checked', true);
                            // Guardar horarios en el objeto valoresHorarios si existe en el scope global
                            if (typeof window.valoresHorarios === 'undefined') {
                                window.valoresHorarios = {};
                            }
                            window.valoresHorarios[dia.dia_id] = {
                                hora_inicio: dia.hora_inicio || null,
                                hora_fin: dia.hora_fin || null
                            };
                        }
                    });
                    
                    // Trigger change en los checkboxes marcados para generar horarios
                    $('.dia-formacion-checkbox:checked').each(function() {
                        $(this).trigger('change');
                    });
                    
                    // Mostrar botón de guardar si hay días seleccionados
                    if ($('.dia-formacion-checkbox:checked').length > 0) {
                        $('#btn-guardar-dias').show();
                    }
                }, 200);
            @endif
            
            // Función para editar día
            window.editarDia = function(diaId, horaInicio, horaFin) {
                $('#formEditarDia').attr('action', `/fichaCaracterizacion/${window.fichaId}/dias-formacion/${diaId}`);
                $('#edit_hora_inicio').val(horaInicio);
                $('#edit_hora_fin').val(horaFin);
                $('#modalEditarDia').modal('show');
            };
            
            // Manejar envío del formulario de edición
            $('#formEditarDia').on('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const url = $(this).attr('action');
                
                Swal.fire({
                    title: '¿Guardar cambios?',
                    text: 'Se actualizarán los horarios del día de formación',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, guardar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Guardando...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        $.ajax({
                            url: url,
                            method: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Éxito!',
                                    text: 'Los horarios se han actualizado correctamente',
                                    confirmButtonText: 'Aceptar'
                                }).then(() => {
                                    $('#modalEditarDia').modal('hide');
                                    window.location.reload();
                                });
                            },
                            error: function(xhr) {
                                let errorMsg = 'Error al actualizar los horarios';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMsg = xhr.responseJSON.message;
                                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                                    const errors = Object.values(xhr.responseJSON.errors).flat();
                                    errorMsg = errors.join('<br>');
                                }
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    html: errorMsg
                                });
                            }
                        });
                    }
                });
            });
            
            // Manejar eliminación de días
            $(document).on('click', '.btn-eliminar-dia', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');
                const diaId = form.data('dia-id');
                const diaNombre = form.data('dia-nombre');
                
                Swal.fire({
                    title: '¿Eliminar día de formación?',
                    html: `¿Está seguro de eliminar el día <strong>${diaNombre}</strong>?<br><small class="text-muted">Esta acción no se puede deshacer.</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Eliminando...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        $.ajax({
                            url: form.attr('action'),
                            method: 'POST',
                            data: {
                                _token: form.find('input[name="_token"]').val(),
                                _method: 'DELETE'
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Eliminado!',
                                    text: 'El día de formación ha sido eliminado correctamente',
                                    confirmButtonText: 'Aceptar'
                                }).then(() => {
                                    window.location.reload();
                                });
                            },
                            error: function(xhr) {
                                let errorMsg = 'Error al eliminar el día de formación';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMsg = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: errorMsg
                                });
                            }
                        });
                    }
                });
            });
            
            // Manejar envío del formulario
            $('#formDiasFormacion').on('submit', function(e) {
                e.preventDefault();
                
                const diasSeleccionados = [];
                $('.dia-formacion-checkbox:checked').each(function() {
                    const diaId = $(this).val();
                    const horaInicio = $(`select[name="horarios[${diaId}][hora_inicio]"]`).val() || 
                                     $(`input[name="horarios[${diaId}][hora_inicio]"]`).val() || '';
                    const horaFin = $(`select[name="horarios[${diaId}][hora_fin]"]`).val() || 
                                   $(`input[name="horarios[${diaId}][hora_fin]"]`).val() || '';
                    
                    diasSeleccionados.push({
                        dia_id: parseInt(diaId),
                        hora_inicio: horaInicio,
                        hora_fin: horaFin
                    });
                });
                
                if (diasSeleccionados.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Sin días seleccionados',
                        text: 'Debe seleccionar al menos un día de formación'
                    });
                    return;
                }
                
                // Construir el formulario con los datos
                const formData = new FormData(this);
                formData.delete('dias_formacion[]');
                
                diasSeleccionados.forEach(function(dia, index) {
                    formData.append(`dias[${dia.dia_id}][dia_id]`, dia.dia_id);
                    formData.append(`dias[${dia.dia_id}][hora_inicio]`, dia.hora_inicio);
                    formData.append(`dias[${dia.dia_id}][hora_fin]`, dia.hora_fin);
                });
                
                // Enviar formulario
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: 'Los días de formación se han guardado correctamente',
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        let errorMsg = 'Error al guardar los días de formación';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMsg
                        });
                    }
                });
            });
        });
    </script>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection