@extends('adminlte::page')

@section('title', 'Gestionar Instructores - Ficha ' . $ficha->ficha)

@section('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    @vite(['resources/css/parametros.css'])
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css" rel="stylesheet" />
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
    <section class="content-header dashboard-header py-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 d-flex align-items-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width: 48px; height: 48px;">
                        <i class="fas fa-chalkboard-teacher text-white fa-lg"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Gestionar Instructores</h1>
                        <p class="text-muted mb-0 font-weight-light">Ficha {{ $ficha->ficha }}</p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent mb-0 justify-content-end">
                            <li class="breadcrumb-item">
                                <a href="{{ route('verificarLogin') }}" class="link_right_header">
                                    <i class="fas fa-home"></i> Inicio
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('fichaCaracterizacion.index') }}" class="link_right_header">
                                    <i class="fas fa-list"></i> Fichas de Caracterización
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('fichaCaracterizacion.show', $ficha->id) }}" class="link_right_header">
                                    <i class="fas fa-eye"></i> Ficha {{ $ficha->ficha }}
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <i class="fas fa-chalkboard-teacher"></i> Gestionar Instructores
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>
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
                                
                                <!-- Información del Instructor Principal (Líder) -->
                                @if($ficha->instructor)
                                    <div class="alert alert-success">
                                        <i class="fas fa-star text-warning mr-1"></i>
                                        <strong>Instructor Líder de la Ficha:</strong>
                                        {{ $ficha->instructor->persona->primer_nombre }} 
                                        {{ $ficha->instructor->persona->primer_apellido }}
                                        <small class="text-muted">({{ $ficha->instructor->persona->numero_documento }})</small>
                                        <br>
                                        <small class="text-light">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            El instructor líder fue asignado en la creación de la ficha y no necesita estar en la lista de instructores adicionales.
                                        </small>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        <strong>Advertencia:</strong> Esta ficha no tiene un instructor líder asignado. 
                                        Se recomienda asignar un instructor líder desde la edición de la ficha.
                                    </div>
                                @endif

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
                                                    <div>
                                                        <form action="{{ route('fichaCaracterizacion.desasignarInstructor', [$ficha->id, $asignacion->instructor_id]) }}" 
                                                              method="POST" style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                                    onclick="return confirm('¿Está seguro de desasignar este instructor?' )"
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
@endsection

@section('footer')
    @include('layout.footer')
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Inicializar Select2
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
            
            // Agregar eventos para recalcular horas cuando cambien los días de formación
            $('select[name*="[dias_formacion]"]').on('change', function() {
                const instructorRow = $(this).closest('.instructor-row')[0];
                if (instructorRow) {
                    recalcularHorasInstructor(instructorRow);
                }
            });

            // Desplazar automáticamente a los errores si existen
            @if($errors->any())
                setTimeout(function() {
                    $('html, body').animate({
                        scrollTop: $('.alert-danger').offset().top - 100
                    }, 600);
                    
                    // Mostrar notificación sonora visual
                    @if($errors->has('error'))
                        Swal.fire({
                            icon: 'error',
                            title: 'Error en la asignación',
                            text: '{{ $errors->first('error') }}',
                            confirmButtonText: 'Entendido',
                            confirmButtonColor: '#dc3545'
                        });
                    @endif
                    
                }, 300);
                
                // Auto-dismiss del mensaje de error después de 10 segundos (independiente del scroll)
                setTimeout(function() {
                    const errorAlert = $('.alert-danger');
                    console.log('Auto-dismiss ejecutándose, alertas encontradas:', errorAlert.length);
                    if (errorAlert.length > 0) {
                        console.log('Iniciando fadeOut del mensaje de error');
                        errorAlert.fadeOut(500, function() {
                            $(this).remove();
                            console.log('Mensaje de error eliminado');
                        });
                    }
                }, 10000);
            @endif

            // Mostrar mensaje de éxito si existe
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: '{{ session('success') }}',
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            @endif
            
            // Calcular horas automáticamente para instructores existentes
            setTimeout(() => {
                console.log('🔄 Iniciando cálculo automático de horas...');
                const instructorRows = document.querySelectorAll('.instructor-row');
                console.log('📋 Filas de instructores encontradas:', instructorRows.length);
                instructorRows.forEach((row, index) => {
                    console.log(`📝 Procesando instructor ${index + 1}:`, row);
                    recalcularHorasInstructor(row);
                });
            }, 500);

            // Cargar instructores existentes
            cargarInstructoresExistentes();
        });

        // Función para cargar instructores ya asignados o desde old input
        function cargarInstructoresExistentes() {
            @php
                // Verificar si hay datos old (error de validación)
                $instructoresOld = old('instructores');
            @endphp
            
            @if($instructoresOld)
                // Cargar datos desde old() después de un error de validación
                @foreach($instructoresOld as $index => $instructorData)
                    @php
                        // Buscar el nombre del instructor
                        $instructor = \App\Models\Instructor::find($instructorData['instructor_id'] ?? null);
                        $nombreInstructor = $instructor ? 
                            $instructor->persona->primer_nombre . ' ' . $instructor->persona->primer_apellido : 
                            '';
                        
                        // Obtener días de formación
                        $diasFormacion = isset($instructorData['dias_formacion']) ? 
                            array_map(function($dia) {
                                return ['dia_id' => $dia['dia_id'] ?? null];
                            }, $instructorData['dias_formacion']) : [];
                    @endphp
                    agregarInstructorRow(
                        {{ $instructorData['instructor_id'] ?? 'null' }},
                        '{{ addslashes($nombreInstructor) }}',
                        '{{ $instructorData['fecha_inicio'] ?? '' }}',
                        '{{ $instructorData['fecha_fin'] ?? '' }}',
                        {{ $instructorData['total_horas_instructor'] ?? '0' }},
                        false,
                        {!! json_encode($diasFormacion) !!}
                    );
                @endforeach
            // No cargar instructores automáticamente
            @endif
        }

        // Función para agregar un instructor desde el botón
        function agregarInstructor() {
            agregarInstructorRow(null, '', '', '', '', false);
        }

        // Función para agregar un instructor seleccionado de la tabla
        function agregarInstructorSeleccionado(instructorId) {
            // Buscar el instructor en los datos disponibles
            const instructores = {!! json_encode($instructoresConDisponibilidad) !!};
            const instructorData = instructores.find(item => item.instructor.id == instructorId);
            
            if (instructorData && instructorData.disponible) {
                const nombre = instructorData.instructor.persona.primer_nombre + ' ' + instructorData.instructor.persona.primer_apellido;
                agregarInstructorRow(instructorId, nombre, '', '', '', false);
            }
        }

        // Función para agregar una fila de instructor
        function agregarInstructorRow(instructorId, nombre, fechaInicio, fechaFin, horas, esPrincipal, diasFormacion = []) {
            const container = document.getElementById('instructores-container');
            const index = container.children.length;
            
            // Obtener datos de instructores disponibles
            const instructoresDisponibles = {!! json_encode($instructoresConDisponibilidad) !!};
            const diasFormacionDisponibles = {!! json_encode($diasFormacionFicha) !!};
            const fechaInicioMin = '{{ $ficha->fecha_inicio ? $ficha->fecha_inicio->format('Y-m-d') : '' }}';
            const fechaFinMax = '{{ $ficha->fecha_fin ? $ficha->fecha_fin->format('Y-m-d') : '' }}';
            const diasFormacionDisabled = {{ $diasFormacionFicha->count() == 0 ? 'true' : 'false' }};
            
            const div = document.createElement('div');
            div.className = 'instructor-row mb-4';
            div.innerHTML = `
                <div class="bg-white border rounded-lg p-4 shadow-sm">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="mb-0 text-dark">
                            <i class="fas fa-user text-primary me-2"></i>
                            Instructor ${index + 1}
                        </h6>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarInstructor(this)">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Instructor</label>
                            <select name="instructores[${index}][instructor_id]" class="form-control instructor-select" required>
                                <option value="">Seleccione un instructor</option>
                                ${instructoresDisponibles.filter(data => data.disponible).map(data => {
                                    const id = data.instructor.id;
                                    const selected = instructorId == id ? 'selected' : '';
                                    return `<option value="${id}" ${selected}>${data.instructor.persona.primer_nombre} ${data.instructor.persona.primer_apellido}</option>`;
                                }).join('')}
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small">Fecha Inicio</label>
                            <input type="date" name="instructores[${index}][fecha_inicio]" 
                                   class="form-control" value="${fechaInicio}" 
                                   min="${fechaInicioMin}"
                                   max="${fechaFinMax}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small">Fecha Fin</label>
                            <input type="date" name="instructores[${index}][fecha_fin]" 
                                   class="form-control" value="${fechaFin}" 
                                   min="${fechaInicioMin}"
                                   max="${fechaFinMax}" required>
                        </div>
                    </div>
                    
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Horas Totales</label>
                            <div class="bg-light border rounded p-3 text-center">
                                <div class="h5 mb-0 text-primary horas-calculadas">${horas}</div>
                                <small class="text-muted">horas</small>
                            </div>
                            <input type="hidden" name="instructores[${index}][total_horas_instructor]" value="${horas}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Días de Formación</label>
                            <div class="dias-formacion-container" data-index="${index}">
                                <!-- Los días se agregarán dinámicamente aquí -->
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="agregarDiaFormacion(${index})" ${diasFormacionDisabled ? 'disabled' : ''}>
                                <i class="fas fa-plus me-1"></i> Agregar Día
                            </button>
                        </div>
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
            const instructorSelect = div.querySelector('select[name*="[instructor_id]"]');
            
            // Función para validar disponibilidad de fechas
            function validarDisponibilidadFechas() {
                const instructorId = instructorSelect.value;
                const fechaInicio = fechaInicioInput.value;
                const fechaFin = fechaFinInput.value;
                
                if (instructorId && fechaInicio && fechaFin) {
                    // Crear elemento de mensaje de validación si no existe
                    let mensajeValidacion = div.querySelector('.mensaje-validacion-fechas');
                    if (!mensajeValidacion) {
                        mensajeValidacion = document.createElement('div');
                        mensajeValidacion.className = 'mensaje-validacion-fechas alert mt-2';
                        div.querySelector('.card-body').appendChild(mensajeValidacion);
                    }
                    
                    // Mostrar loading
                    mensajeValidacion.className = 'mensaje-validacion-fechas alert alert-info mt-2';
                    mensajeValidacion.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Verificando disponibilidad...';
                    
                    // Llamar al endpoint de validación
                    fetch('{{ route("api.fichas.verificar-disponibilidad-fechas-instructor") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            instructor_id: instructorId,
                            fecha_inicio: fechaInicio,
                            fecha_fin: fechaFin
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.disponible) {
                            mensajeValidacion.className = 'mensaje-validacion-fechas alert alert-success mt-2';
                            mensajeValidacion.innerHTML = '<i class="fas fa-check mr-1"></i> ' + data.mensaje;
                        } else {
                            mensajeValidacion.className = 'mensaje-validacion-fechas alert alert-danger mt-2';
                            let conflictosText = data.conflictos.map(c => 
                                `Ficha ${c.ficha} (${c.programa}) del ${c.fecha_inicio} al ${c.fecha_fin}`
                            ).join(', ');
                            mensajeValidacion.innerHTML = `<i class="fas fa-exclamation-triangle mr-1"></i> ${data.mensaje}: ${conflictosText}`;
                        }
                    })
                    .catch(error => {
                        console.error('Error al verificar disponibilidad:', error);
                        mensajeValidacion.className = 'mensaje-validacion-fechas alert alert-warning mt-2';
                        mensajeValidacion.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i> Error al verificar disponibilidad. Intente nuevamente.';
                    });
                }
            }
            
            fechaInicioInput.addEventListener('change', function() {
                fechaFinInput.min = this.value;
                if (fechaFinInput.value && fechaFinInput.value < this.value) {
                    fechaFinInput.value = this.value;
                }
                // Validar disponibilidad cuando cambie la fecha de inicio
                setTimeout(validarDisponibilidadFechas, 500);
                // Recalcular horas automáticamente
                recalcularHorasInstructor(div);
            });
            
            fechaFinInput.addEventListener('change', function() {
                fechaInicioInput.max = this.value;
                if (fechaInicioInput.value && fechaInicioInput.value > this.value) {
                    fechaInicioInput.value = this.value;
                }
                // Validar disponibilidad cuando cambie la fecha de fin
                setTimeout(validarDisponibilidadFechas, 500);
                // Recalcular horas automáticamente
                recalcularHorasInstructor(div);
            });
            
            instructorSelect.addEventListener('change', function() {
                // Validar disponibilidad cuando cambie el instructor
                setTimeout(validarDisponibilidadFechas, 500);
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
            const diasDisponibles = {!! json_encode($diasFormacionFicha) !!};
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
            const diasFormacionDisponibles = {!! json_encode($diasFormacionFicha) !!};
            
            const div = document.createElement('div');
            div.className = 'dia-formacion-row mb-2';
            div.innerHTML = `
                <div class="d-flex align-items-center gap-2">
                    <select name="instructores[${instructorIndex}][dias_formacion][${diaIndex}][dia_id]" class="form-control flex-grow-1 select2" required>
                        <option value="">Seleccione día</option>
                        ${diasFormacionDisponibles.map(dia => {
                            const selected = diaId == dia.id ? 'selected' : '';
                            return `<option value="${dia.id}" ${selected}>${dia.name}</option>`;
                        }).join('')}
                    </select>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarDiaFormacion(this)">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            container.appendChild(div);
            
            // Inicializar Select2 en el nuevo elemento
            $(div).find('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
            
            // Agregar evento para recalcular cuando cambie el día
            $(div).find('.select2').on('change', function() {
                const instructorRow = container.closest('.instructor-row');
                recalcularHorasInstructor(instructorRow);
            });
            
            // Recalcular horas después de agregar día
            const instructorRow = container.closest('.instructor-row');
            setTimeout(() => {
                recalcularHorasInstructor(instructorRow);
            }, 100);
        }

        // Función para eliminar un día de formación
        function eliminarDiaFormacion(button) {
            const row = button.closest('.dia-formacion-row');
            row.remove();
            // Recalcular horas después de eliminar día
            recalcularHorasInstructor(row.closest('.instructor-row'));
        }

        // Función para calcular automáticamente las horas de un instructor
        function recalcularHorasInstructor(instructorRow) {
            console.log('🔢 Recalculando horas para instructor:', instructorRow);
            
            const fechaInicioInput = instructorRow.querySelector('input[name*="[fecha_inicio]"]');
            const fechaFinInput = instructorRow.querySelector('input[name*="[fecha_fin]"]');
            const horasHiddenInput = instructorRow.querySelector('input[name*="[total_horas_instructor]"]');
            const horasCalculadasSpan = instructorRow.querySelector('.horas-calculadas');
            const diasFormacion = instructorRow.querySelectorAll('.dia-formacion-row');
            
            console.log('🔍 Elementos encontrados:', {
                fechaInicio: !!fechaInicioInput,
                fechaFin: !!fechaFinInput,
                horasHidden: !!horasHiddenInput,
                horasSpan: !!horasCalculadasSpan,
                diasCount: diasFormacion.length
            });
            
            if (!fechaInicioInput || !fechaFinInput || !horasHiddenInput || !horasCalculadasSpan) {
                console.log('❌ Faltan elementos requeridos');
                return;
            }
            
            const fechaInicio = fechaInicioInput.value;
            const fechaFin = fechaFinInput.value;
            const diasCount = diasFormacion.length;
            
            console.log('📊 Datos para cálculo:', {
                fechaInicio,
                fechaFin,
                diasCount
            });
            
            if (fechaInicio && fechaFin && diasCount > 0) {
               // Calcular semanas entre fechas
               const inicio = new Date(fechaInicio);
               const fin = new Date(fechaFin);
               const diffTime = Math.abs(fin - inicio);
               const semanas = Math.ceil(diffTime / (1000 * 60 * 60 * 24 * 7));
               
               // Horas por jornada (6.5 horas por defecto, se puede configurar)
               const horasPorJornada = 6.5;
               
               // Calcular horas totales: días × horas por jornada × semanas
               const horasTotales = diasCount * horasPorJornada * semanas;
               
               console.log('🧮 Cálculo:', {
                   semanas,
                   horasPorJornada,
                   horasTotales
               });
                
               // Actualizar el campo oculto y el span visible
               const horasCalculadas = Math.round(horasTotales);
               horasHiddenInput.value = horasCalculadas;
               horasCalculadasSpan.textContent = horasCalculadas;
               
               console.log('✅ Horas actualizadas:', {
                   horasCalculadas,
                   spanText: horasCalculadasSpan.textContent,
                   hiddenValue: horasHiddenInput.value
               });
               
               // El cálculo se mantiene interno, sin mostrar en la interfaz
            }
        }

        // Validación del formulario
        document.getElementById('formAsignarInstructores').addEventListener('submit', function(e) {
            const instructoresAsignados = document.querySelectorAll('.instructor-select');
            
            if (instructoresAsignados.length === 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Sin instructores',
                    text: 'Debe asignar al menos un instructor adicional a la ficha.',
                    confirmButtonText: 'Entendido'
                });
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

        // Los instructores se cargarán solo cuando se presione "Agregar Instructor"

        // Función para agregar instructores existentes
        function agregarInstructorExistente(data) {
            const container = document.getElementById('instructores-container');
            const index = container.children.length;
            
            // Obtener datos de instructores disponibles
            const instructoresDisponibles = {!! json_encode($instructoresConDisponibilidad) !!};
            const fechaInicioMin = '{{ $ficha->fecha_inicio ? $ficha->fecha_inicio->format('Y-m-d') : '' }}';
            const fechaFinMax = '{{ $ficha->fecha_fin ? $ficha->fecha_fin->format('Y-m-d') : '' }}';
            
            const div = document.createElement('div');
            div.className = 'card mb-3 instructor-card';
            div.innerHTML = `
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5">
                            <label class="form-label font-weight-bold">Instructor</label>
                            <select name="instructores[${index}][instructor_id]" class="form-control select2 instructor-select" required>
                                <option value="">Seleccione un instructor</option>
                                ${instructoresDisponibles.filter(instructorData => instructorData.disponible).map(instructorData => {
                                    const id = instructorData.instructor.id;
                                    const selected = data.instructor_id == id ? 'selected' : '';
                                    return `<option value="${id}" ${selected}>${instructorData.instructor.persona.primer_nombre} ${instructorData.instructor.persona.primer_apellido}</option>`;
                                }).join('')}
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label font-weight-bold">Fecha Inicio</label>
                            <input type="date" name="instructores[${index}][fecha_inicio]" 
                                   class="form-control" value="${data.fecha_inicio}" 
                                   min="${fechaInicioMin}"
                                   max="${fechaFinMax}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label font-weight-bold">Fecha Fin</label>
                            <input type="date" name="instructores[${index}][fecha_fin]" 
                                   class="form-control" value="${data.fecha_fin}" 
                                   min="${fechaInicioMin}"
                                   max="${fechaFinMax}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label font-weight-bold">Horas Totales</label>
                            <div class="form-control-plaintext border rounded p-2 bg-light text-center">
                                <span class="horas-calculadas font-weight-bold text-primary">${data.total_horas_instructor}</span>
                                <small class="text-muted d-block">horas</small>
                            </div>
                            <input type="hidden" name="instructores[${index}][total_horas_instructor]" value="${data.total_horas_instructor}">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label font-weight-bold">&nbsp;</label>
                            <button type="button" class="btn btn-sm btn-outline-danger d-block" onclick="eliminarInstructor(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <!-- Días de Formación -->
                    <div class="row mt-2">
                        <div class="col-12">
                            <label class="form-label font-weight-bold">
                                <i class="fas fa-calendar-week mr-1"></i>
                                Días de Formación
                            </label>
                            <div class="dias-formacion-container" data-index="${index}">
                                <!-- Los días se agregarán dinámicamente aquí -->
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-success mt-2" onclick="agregarDiaFormacion(${index})">
                                <i class="fas fa-plus mr-1"></i> Agregar Día
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            container.appendChild(div);
            
            // Inicializar Select2
            $(div).find('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // Cargar días de formación existentes
            if (data.dias_formacion && data.dias_formacion.length > 0) {
                data.dias_formacion.forEach(diaId => {
                    agregarDiaFormacionExistente(index, diaId);
                });
            }
        }

        // Función para agregar días de formación existentes
        function agregarDiaFormacionExistente(index, diaId) {
            const container = document.querySelector(`.dias-formacion-container[data-index="${index}"]`);
            const diaIndex = container.children.length;
            
            const diasFormacionFicha = {!! json_encode($diasFormacionFicha) !!};
            
            const div = document.createElement('div');
            div.className = 'dia-formacion-row mb-2';
            div.innerHTML = `
                <div class="d-flex align-items-center gap-2">
                    <select name="instructores[${index}][dias_formacion][${diaIndex}][dia_id]" class="form-control flex-grow-1 select2" required>
                        <option value="">Seleccione un día</option>
                        ${diasFormacionFicha.map(dia => {
                            const selected = diaId == dia.id ? 'selected' : '';
                            return `<option value="${dia.id}" ${selected}>${dia.name}</option>`;
                        }).join('')}
                    </select>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarDiaFormacion(this)">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            container.appendChild(div);
            
            // Inicializar Select2
            $(div).find('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
        }

        // Función para mostrar razones de no disponibilidad
        function mostrarRazonesNoDisponible(index) {
            const row = document.getElementById(`razones-${index}`);
            if (row) {
                if (row.style.display === 'none') {
                    row.style.display = 'table-row';
                } else {
                    row.style.display = 'none';
                }
            }
        }

        // Función mejorada para agregar instructor seleccionado con validaciones
        function agregarInstructorSeleccionado(instructorId) {
            // Obtener datos de instructores disponibles
            const instructoresDisponibles = {!! json_encode($instructoresConDisponibilidad) !!};
            
            // Verificar si ya está agregado
            const instructoresContainer = document.getElementById('instructores-container');
            const instructoresExistentes = instructoresContainer.querySelectorAll('.instructor-select');
            
            for (let select of instructoresExistentes) {
                if (select.value == instructorId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Instructor ya agregado',
                        text: 'Este instructor ya está en la lista de asignaciones.',
                        confirmButtonText: 'Entendido'
                    });
                    return;
                }
            }

            // Buscar datos del instructor en la lista de disponibles
            const instructorData = instructoresDisponibles.find(item => item.instructor.id == instructorId);
            if (!instructorData) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se encontraron datos del instructor.',
                    confirmButtonText: 'Entendido'
                });
                return;
            }

            // Verificar disponibilidad
            if (!instructorData.disponible) {
                Swal.fire({
                    icon: 'error',
                    title: 'Instructor no disponible',
                    text: 'Este instructor no está disponible para esta ficha.',
                    confirmButtonText: 'Entendido'
                });
                return;
            }

            // Obtener nombre del instructor
            const nombre = instructorData.instructor.persona.primer_nombre + ' ' + instructorData.instructor.persona.primer_apellido;
            
            // Agregar el instructor con datos básicos
            agregarInstructorRow(instructorId, nombre, '', '', '', false);
            
            // Mostrar mensaje de éxito
            Swal.fire({
                icon: 'success',
                title: 'Instructor agregado',
                text: `${nombre} ha sido agregado a la lista de asignaciones.`,
                timer: 2000,
                showConfirmButton: false
            });
        }

        // Función para mostrar estadísticas en tiempo real
        function actualizarEstadisticas() {
            const instructoresAsignados = document.querySelectorAll('.instructor-select');
            const instructorPrincipal = document.getElementById('instructor_principal_id');
            
            // Actualizar contador de instructores asignados
            const contador = document.querySelector('.badge-info');
            if (contador) {
                const disponibles = {{ count(array_filter($instructoresConDisponibilidad ?? [], fn($i) => $i['disponible'])) }};
                contador.textContent = `${disponibles} disponibles`;
            }
        }

        // Inicializar estadísticas al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            actualizarEstadisticas();
        });
    </script>
@endsection