@extends('adminlte::page')

@section('title', 'Gestionar Instructores - Ficha ' . $ficha->ficha)

@section('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    @vite(['resources/css/parametros.css'])
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2-bootstrap4.min.css') }}">
    <style>
        /* Estilos para badges de estado */
        .badge {
            font-size: 0.75rem;
        }
        
        /* Estilos para alertas expandibles */
        .alert {
            border-radius: 0.375rem;
        }
        
        /* Estilos para alertas de error mejorados */
        .alert-danger {
            border-left: 4px solid #dc3545;
            animation: slideDown 0.4s ease-out;
        }
        
        .alert-success {
            border-left: 4px solid #28a745;
            animation: slideDown 0.4s ease-out;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .alert-heading {
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        .alert ul {
            padding-left: 1.5rem;
        }
        
        .alert ul li {
            margin-bottom: 0.5rem;
        }
        
        /* Estilos para tabla de instructores */
        .table-responsive {
            border-radius: 0.375rem;
        }
        
        .table tbody tr.table-warning {
            background-color: rgba(255, 193, 7, 0.1);
        }
        
        .table tbody tr.table-info {
            background-color: rgba(23, 162, 184, 0.1);
        }
        
        .table tbody tr.table-light {
            background-color: rgba(248, 249, 250, 0.8);
        }
        
        /* Estilos para estadísticas */
        .border-left-primary {
            border-left: 0.25rem solid #007bff !important;
        }
        
        .border-left-success {
            border-left: 0.25rem solid #28a745 !important;
        }
        
        .border-left-warning {
            border-left: 0.25rem solid #ffc107 !important;
        }
        
        .border-left-info {
            border-left: 0.25rem solid #17a2b8 !important;
        }
        
        .border-left-danger {
            border-left: 0.25rem solid #dc3545 !important;
        }
        
        /* Estilos para hover effects */
        .hover-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .hover-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
        }
    </style>
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-chalkboard-teacher" 
        title="Gestionar Instructores"
        subtitle="Ficha {{ $ficha->ficha }}"
        :breadcrumb="[['label' => 'Ficha {{ $ficha->ficha }}', 'url' => route('fichaCaracterizacion.show', $ficha->id) , 'icon' => 'fa-eye'], ['label' => 'Gestionar Instructores', 'icon' => 'fa-chalkboard-teacher', 'active' => true]]"
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
                    <div class="card detail-card no-hover mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-info-circle mr-2"></i>Información de la Ficha
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="info-item">
                                        <strong>Programa:</strong><br>
                                        <span class="text-muted">{{ $ficha->programaFormacion->nombre ?? 'No asignado' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-item">
                                        <strong>Fecha Inicio:</strong><br>
                                        <span class="text-muted">{{ $ficha->fecha_inicio ? $ficha->fecha_inicio->format('d/m/Y') : 'No definida' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-item">
                                        <strong>Fecha Fin:</strong><br>
                                        <span class="text-muted">{{ $ficha->fecha_fin ? $ficha->fecha_fin->format('d/m/Y') : 'No definida' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-item">
                                        <strong>Total Horas:</strong><br>
                                        <span class="text-muted">{{ $ficha->total_horas ?? 'No definido' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario de Asignación de Instructores -->
                    <div class="card border-0 shadow-lg mb-4">
                        <div class="card-header bg-white border-0 py-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary rounded-circle p-2 me-3">
                                    <i class="fas fa-users text-white"></i>
                                </div>
                                <div>
                                    <h4 class="mb-0 text-dark">Asignar Instructores</h4>
                                    <small class="text-muted">Agregue instructores adicionales a esta ficha</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            {{-- Mostrar errores de validación --}}
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <h5 class="alert-heading"><i class="fas fa-exclamation-triangle mr-2"></i>Error en la asignación</h5>
                                    <hr>
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            {{-- Mostrar mensajes de éxito --}}
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                                </div>
                            @endif

                            <form action="{{ route('fichaCaracterizacion.asignarInstructores', $ficha->id) }}" method="POST" id="formAsignarInstructores">
                                @csrf
                                
                                {{-- Campo oculto para instructor principal --}}
                                <input type="hidden" 
                                       name="instructor_principal_id" 
                                       id="instructor_principal_id" 
                                       value="{{ old('instructor_principal_id', $ficha->instructor_id) }}">

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
                                    <label class="form-label font-weight-bold">
                                        <i class="fas fa-users mr-1"></i>
                                        Instructores Asignados <span class="text-danger">*</span>
                                    </label>
                                    <div id="instructores-container">   
                                        <!-- Los instructores se agregarán dinámicamente aquí -->
                                    </div>
                                    <button type="button" class="btn btn-primary btn-lg mt-4" onclick="agregarInstructor()">
                                        <i class="fas fa-plus me-2"></i> Agregar Instructor
                                    </button>
                                </div>

                                <div class="border-top pt-4 mt-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="{{ route('fichaCaracterizacion.show', $ficha->id) }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-arrow-left me-2"></i> Volver
                                        </a>
                                        <button type="submit" class="btn btn-success btn-lg px-4">
                                            <i class="fas fa-check me-2"></i> Asignar Instructores
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Instructores Asignados -->
                    <div class="card border-0 shadow-lg mb-4">
                        <div class="card-header bg-white border-0 py-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-success rounded-circle p-2 me-3">
                                    <i class="fas fa-user-check text-white"></i>
                                </div>
                                <div>
                                    <h4 class="mb-0 text-dark">Instructores Asignados</h4>
                                    <small class="text-muted">Instructores ya asignados a esta ficha</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            @if($instructoresAsignados->count() > 0)
                                <div class="row g-3">
                                    @foreach($instructoresAsignados as $asignacion)
                                        <div class="col-md-6">
                                            <div class="bg-light border rounded p-3">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-1 text-dark">
                                                            {{ $asignacion->instructor->persona->primer_nombre }} 
                                                            {{ $asignacion->instructor->persona->primer_apellido }}
                                                            @if($ficha->instructor_id == $asignacion->instructor_id)
                                                                <span class="badge bg-primary ms-2">Principal</span>
                                                            @else
                                                                <span class="badge bg-secondary ms-2">Auxiliar</span>
                                                            @endif
                                                            @if($asignacion->instructorFichaDias && $asignacion->instructorFichaDias->count() > 0)
                                                                <span class="badge bg-success ms-2" title="Tiene días asignados">
                                                                    <i class="fas fa-check-circle"></i> Días configurados
                                                                </span>
                                                            @else
                                                                <span class="badge bg-warning text-dark ms-2" title="Sin días asignados">
                                                                    <i class="fas fa-exclamation-triangle"></i> Sin días
                                                                </span>
                                                            @endif
                                                        </h6>
                                                        <p class="text-muted mb-1 small">
                                                            <i class="fas fa-calendar me-1"></i>
                                                            {{ $asignacion->fecha_inicio->format('d/m/Y') }} - 
                                                            {{ $asignacion->fecha_fin->format('d/m/Y') }}
                                                        </p>
                                                        <p class="text-muted mb-0 small">
                                                            <i class="fas fa-clock me-1"></i>
                                                            {{ $asignacion->total_horas_instructor }} horas
                                                        </p>
                                                        @if($asignacion->instructorFichaDias && $asignacion->instructorFichaDias->count() > 0)
                                                            @php
                                                                $diasAsignados = $asignacion->instructorFichaDias
                                                                    ->filter(function($dia) { return $dia->dia && $dia->dia->name; })
                                                                    ->map(function($dia) { return $dia->dia->name; })
                                                                    ->implode(', ');
                                                            @endphp
                                                            @if($diasAsignados)
                                                                <p class="text-muted mb-0 small mt-1">
                                                                    <i class="fas fa-calendar-week me-1"></i>
                                                                    <strong>Días:</strong> {{ $diasAsignados }}
                                                                </p>
                                                            @endif
                                                        @endif
                                                    </div>
                                                    <div class="btn-group-vertical btn-group-sm" role="group">
                                                        <!-- Botón para gestionar días de formación -->
                                                        <button type="button"
                                                           class="btn btn-sm {{ $asignacion->instructorFichaDias && $asignacion->instructorFichaDias->count() > 0 ? 'btn-info' : 'btn-outline-info' }} mb-1 btn-gestionar-dias" 
                                                           data-instructor-ficha-id="{{ $asignacion->id }}"
                                                           data-instructor-nombre="{{ $asignacion->instructor->persona->primer_nombre }} {{ $asignacion->instructor->persona->primer_apellido }}"
                                                           data-toggle="tooltip" 
                                                           data-placement="top"
                                                           title="Gestionar días de formación"
                                                           onclick="abrirModalDias({{ $asignacion->id }}, '{{ $asignacion->instructor->persona->primer_nombre }} {{ $asignacion->instructor->persona->primer_apellido }}')">
                                                            <i class="fas fa-calendar-week"></i>
                                                        </button>
                                                        
                                                        <!-- Botón para desasignar instructor -->
                                                        <form action="{{ route('fichaCaracterizacion.desasignarInstructor', [$ficha->id, $asignacion->instructor_id]) }}" 
                                                              method="POST" style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                                    data-toggle="tooltip"
                                                                    data-placement="top"
                                                                    onclick="return confirm('¿Está seguro de desasignar este instructor?')"
                                                                    title="Desasignar instructor">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-user-slash fa-3x mb-3"></i>
                                    <p>No hay instructores adicionales asignados a esta ficha.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- Modal para Gestionar Días de Formación -->
    <div class="modal fade" id="modalDiasFormacion" tabindex="-1" role="dialog" aria-labelledby="modalDiasFormacionLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalDiasFormacionLabel">
                        <i class="fas fa-calendar-week mr-2"></i>Gestionar Días de Formación
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Información del instructor -->
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle mr-2"></i>Información</h6>
                        <p class="mb-1"><strong>Instructor:</strong> <span id="modal-instructor-nombre"></span></p>
                        <p class="mb-1"><strong>Ficha:</strong> {{ $ficha->ficha }}</p>
                        <p class="mb-0"><strong>Programa:</strong> {{ $ficha->programaFormacion->nombre ?? 'N/A' }}</p>
                    </div>

                    <!-- Formulario de días -->
                    <form id="form-asignar-dias-modal">
                        <input type="hidden" id="modal-instructor-ficha-id">
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="10%" class="text-center">
                                            <input type="checkbox" id="select-all-modal" title="Seleccionar todos">
                                        </th>
                                        <th width="25%">Día de la Semana</th>
                                        <th width="30%">Hora Inicio</th>
                                        <th width="30%">Hora Fin</th>
                                        <th width="5%" class="text-center"><i class="fas fa-info-circle"></i></th>
                                    </tr>
                                </thead>
                                <tbody id="dias-tbody">
                                    @if(isset($diasSemana) && $diasSemana->count() > 0)
                                        @foreach($diasSemana as $dia)
                                        <tr class="dia-row-modal" data-dia-id="{{ $dia->id }}">
                                            <td class="text-center align-middle">
                                                <input type="checkbox" class="dia-checkbox-modal" value="{{ $dia->id }}" name="dias_selected[]">
                                            </td>
                                            <td class="align-middle">
                                                <strong><i class="far fa-calendar mr-1"></i>{{ $dia->name }}</strong>
                                            </td>
                                            <td>
                                                <input type="time" class="form-control hora-inicio-modal" name="hora_inicio_{{ $dia->id }}" data-dia="{{ $dia->id }}" disabled>
                                            </td>
                                            <td>
                                                <input type="time" class="form-control hora-fin-modal" name="hora_fin_{{ $dia->id }}" data-dia="{{ $dia->id }}" disabled>
                                            </td>
                                            <td class="text-center align-middle">
                                                <i class="fas fa-check-circle text-success dia-status-modal" style="display:none;"></i>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No se encontraron días de la semana configurados</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <!-- Preview de fechas -->
                        <div id="preview-fechas-modal" class="card card-success mt-3" style="display: none;">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-calendar-check mr-2"></i>Fechas Efectivas de Formación
                                </h5>
                            </div>
                            <div class="card-body">
                                <div id="fechas-container-modal"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info" id="btn-preview-modal">
                        <i class="fas fa-eye mr-1"></i>Vista Previa
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-success" id="btn-guardar-dias-modal">
                        <i class="fas fa-save mr-1"></i>Guardar Días
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    @include('layout.footer')
@endsection

@section('js')
    <script>
        // Pasar datos al contexto de JavaScript
        window.fichaId = {{ $ficha->id }};
        
        window.diasSemana = @json($diasSemana->map(function($dia) {
            return ['id' => $dia->id, 'nombre' => $dia->name];
        })->values());
        
        // Crear objeto de días de la semana con horas por defecto
        window.diasSemanaData = {};
        @if(isset($diasSemana) && $diasSemana->count() > 0)
            @foreach($diasSemana as $dia)
                window.diasSemanaData[{{ $dia->id }}] = {
                    id: {{ $dia->id }},
                    name: '{{ $dia->name }}',
                    hora_inicio: '{{ $dia->hora_inicio ?? $ficha->hora_inicio ?? "06:30" }}',
                    hora_fin: '{{ $dia->hora_fin ?? $ficha->hora_fin ?? "13:00" }}'
                };
            @endforeach
        @endif
    </script>
    @vite(['resources/js/pages/gestion-especializada.js'])
    <script>
        const fichaId = {{ $ficha->id }};
        let instructorFichaIdActual = null;

        $(document).ready(function() {
            // Inicializar tooltips de Bootstrap
            $('[data-toggle="tooltip"]').tooltip();
            
            // Seleccionar/deseleccionar todos en modal
            $('#select-all-modal').change(function() {
                const isChecked = $(this).is(':checked');
                $('.dia-checkbox-modal').prop('checked', isChecked).trigger('change');
            });

            // Habilitar/deshabilitar campos de hora según checkbox en modal
            $('.dia-checkbox-modal').change(function() {
                const diaId = $(this).val();
                const isChecked = $(this).is(':checked');
                const $row = $(this).closest('tr');
                
                $(`input[name="hora_inicio_${diaId}"]`).prop('disabled', !isChecked);
                $(`input[name="hora_fin_${diaId}"]`).prop('disabled', !isChecked);
                
                $row.find('.dia-status-modal').toggle(isChecked);
                
                if (isChecked) {
                    $row.addClass('table-active');
                } else {
                    $row.removeClass('table-active');
                    $(`input[name="hora_inicio_${diaId}"]`).val('');
                    $(`input[name="hora_fin_${diaId}"]`).val('');
                }
            });

            // Preview de fechas en modal
            $('#btn-preview-modal').click(function() {
                const diasSeleccionados = obtenerDiasSeleccionadosModal();
                
                if (diasSeleccionados.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Sin días seleccionados',
                        text: 'Debe seleccionar al menos un día para ver las fechas efectivas'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Generando fechas...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: `/fichaCaracterizacion/${fichaId}/instructor/${instructorFichaIdActual}/preview-fechas`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        dias: diasSeleccionados
                    },
                    success: function(response) {
                        Swal.close();
                        if (response.success) {
                            mostrarPreviewFechasModal(response.fechas_efectivas, response.total_sesiones);
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudieron generar las fechas'
                        });
                    }
                });
            });

            // Guardar días en modal
            $('#btn-guardar-dias-modal').click(function() {
                const diasSeleccionados = obtenerDiasSeleccionadosModal();
                
                if (diasSeleccionados.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Sin días seleccionados',
                        text: 'Debe seleccionar al menos un día de formación'
                    });
                    return;
                }

                Swal.fire({
                    title: '¿Confirmar asignación?',
                    text: `Se asignarán ${diasSeleccionados.length} días de formación`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, asignar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        guardarAsignacionModal(diasSeleccionados);
                    }
                });
            });
            
            console.log('✅ Gestión de días de formación inicializada');
        });

        // Función para abrir el modal
        function abrirModalDias(instructorFichaId, instructorNombre) {
            instructorFichaIdActual = instructorFichaId;
            
            // Establecer información del instructor
            $('#modal-instructor-nombre').text(instructorNombre);
            $('#modal-instructor-ficha-id').val(instructorFichaId);
            
            // Limpiar formulario
            limpiarFormularioModal();
            
            // Cargar días asignados
            cargarDiasAsignados(instructorFichaId);
            
            // Mostrar modal
            $('#modalDiasFormacion').modal('show');
        }

        // Limpiar formulario del modal
        function limpiarFormularioModal() {
            $('.dia-checkbox-modal').prop('checked', false);
            $('.hora-inicio-modal, .hora-fin-modal').val('').prop('disabled', true);
            $('.dia-row-modal').removeClass('table-active');
            $('.dia-status-modal').hide();
            $('#preview-fechas-modal').hide();
            $('#select-all-modal').prop('checked', false);
        }

        // Cargar días ya asignados
        function cargarDiasAsignados(instructorFichaId) {
            $.ajax({
                url: `/fichaCaracterizacion/${fichaId}/instructor/${instructorFichaId}/obtener-dias`,
                method: 'GET',
                success: function(response) {
                    if (response.success && response.dias.length > 0) {
                        response.dias.forEach(function(dia) {
                            const $checkbox = $(`.dia-checkbox-modal[value="${dia.dia_id}"]`);
                            $checkbox.prop('checked', true).trigger('change');
                            
                            if (dia.hora_inicio) {
                                $(`input[name="hora_inicio_${dia.dia_id}"]`).val(dia.hora_inicio);
                            }
                            if (dia.hora_fin) {
                                $(`input[name="hora_fin_${dia.dia_id}"]`).val(dia.hora_fin);
                            }
                        });
                    }
                },
                error: function() {
                    console.log('No se pudieron cargar los días asignados');
                }
            });
        }

        // Obtener días seleccionados del modal
        function obtenerDiasSeleccionadosModal() {
            const dias = [];
            $('.dia-checkbox-modal:checked').each(function() {
                const diaId = $(this).val();
                const horaInicio = $(`input[name="hora_inicio_${diaId}"]`).val();
                const horaFin = $(`input[name="hora_fin_${diaId}"]`).val();
                
                dias.push({
                    dia_id: parseInt(diaId),
                    hora_inicio: horaInicio || null,
                    hora_fin: horaFin || null
                });
            });
            return dias;
        }

        // Mostrar preview de fechas en modal
        function mostrarPreviewFechasModal(fechas, total) {
            let html = `<div class="alert alert-info"><strong><i class="fas fa-calendar-check"></i> Se generarán ${total} sesiones de formación</strong></div>`;
            html += '<div class="table-responsive"><table class="table table-sm table-striped table-bordered">';
            html += '<thead class="thead-light"><tr><th width="5%">#</th><th width="20%">Fecha</th><th width="25%">Día</th><th width="25%">Horario</th></tr></thead><tbody>';
            
            fechas.forEach((fecha, index) => {
                const horario = fecha.hora_inicio && fecha.hora_fin 
                    ? `${fecha.hora_inicio} - ${fecha.hora_fin}` 
                    : '<span class="text-muted">Sin horario</span>';
                
                html += `<tr>
                    <td class="text-center">${index + 1}</td>
                    <td><strong>${fecha.fecha}</strong></td>
                    <td>${fecha.dia_semana}</td>
                    <td>${horario}</td>
                </tr>`;
            });
            
            html += '</tbody></table></div>';
            
            $('#fechas-container-modal').html(html);
            $('#preview-fechas-modal').slideDown();
        }

        // Guardar asignación desde el modal
        function guardarAsignacionModal(diasSeleccionados) {
            Swal.fire({
                title: 'Guardando...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: `/fichaCaracterizacion/${fichaId}/instructor/${instructorFichaIdActual}/asignar-dias`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    dias: diasSeleccionados
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            html: `
                                <p>${response.message}</p>
                                <p class="mb-0"><strong>Total de sesiones programadas:</strong> ${response.total_sesiones || 0}</p>
                            `,
                            confirmButtonText: 'Entendido'
                        }).then(() => {
                            $('#modalDiasFormacion').modal('hide');
                            location.reload(); // Recargar para mostrar los días actualizados
                        });
                    } else {
                        mostrarErrorConflictosModal(response);
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Error al guardar los días de formación';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMsg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: errorMsg
                    });
                }
            });
        }

        // Mostrar errores de conflictos
        function mostrarErrorConflictosModal(response) {
            let html = `<p>${response.message}</p>`;
            
            if (response.conflictos && response.conflictos.length > 0) {
                html += '<hr><p><strong>Conflictos detectados:</strong></p><ul class="text-left">';
                response.conflictos.forEach(conflicto => {
                    html += `<li>${conflicto.dia_nombre}: Ficha ${conflicto.ficha_conflicto} (${conflicto.horario_conflicto})</li>`;
                });
                html += '</ul>';
            }
            
            Swal.fire({
                icon: 'error',
                title: 'No se pudo asignar',
                html: html
            });
        }
    </script>
@endsection
