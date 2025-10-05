@extends('adminlte::page')

@section('title', 'Detalles del Instructor')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
    <style>
        .instructor-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e3e6f0;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }
        .specialty-badge {
            display: inline-block;
            padding: 4px 8px;
            background-color: #e3f2fd;
            color: #1976d2;
            border-radius: 12px;
            font-size: 0.75rem;
            margin: 2px;
        }
        .stats-card {
            background: #f8f9fa;
            color: #495057;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }
        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .stats-label {
            font-size: 0.85rem;
            opacity: 0.9;
        }
        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .table-custom {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .table-custom thead {
            background: #007bff;
            color: white;
        }
        .table-custom thead th {
            border: none;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.8rem;
            padding: 12px 8px;
        }
        .table-custom tbody tr {
            transition: all 0.3s ease;
        }
        .table-custom tbody tr:hover {
            background: #f8f9fa;
        }
        .table-custom tbody td {
            padding: 12px 8px;
            vertical-align: middle;
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
                        <h1 class="h3 mb-0 text-gray-800">Detalles del Instructor</h1>
                        <p class="text-muted mb-0 font-weight-light">Información completa del instructor</p>
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
                                <a href="{{ route('instructor.index') }}" class="link_right_header">
                                    <i class="fas fa-chalkboard-teacher"></i> Instructores
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <i class="fas fa-user"></i> {{ $instructor->persona->primer_nombre }} {{ $instructor->persona->primer_apellido }}
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
            <!-- Alertas -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- Botón Volver -->
            <div class="mb-3">
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('instructor.index') }}">
                    <i class="fas fa-arrow-left mr-1"></i> Volver
                </a>
            </div>

            <div class="row">
                <!-- Estadísticas Generales -->
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number">{{ $instructor->numero_fichas_asignadas }}</div>
                        <div class="stats-label">
                            <i class="fas fa-clipboard-list mr-1"></i>
                            Fichas Asignadas
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number">{{ $instructor->total_horas_asignadas }}</div>
                        <div class="stats-label">
                            <i class="fas fa-clock mr-1"></i>
                            Horas Totales
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number">{{ $instructor->edad }}</div>
                        <div class="stats-label">
                            <i class="fas fa-birthday-cake mr-1"></i>
                            Años de Edad
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number">{{ $instructor->anos_experiencia ?? 0 }}</div>
                        <div class="stats-label">
                            <i class="fas fa-briefcase mr-1"></i>
                            Años Experiencia
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información Personal -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card detail-card no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-user mr-2"></i>Información Personal
                            </h5>
                            <div class="d-flex align-items-center mt-3">
                                <img src="{{ asset('dist/img/user2-160x160.jpg') }}" alt="Avatar" class="instructor-avatar mr-3" 
                                     onerror="this.src='{{ asset('dist/img/user3-128x128.jpg') }}'; this.onerror=null;">
                                <div>
                                    <h6 class="mb-0 font-weight-bold text-dark">
                                        {{ $instructor->persona->primer_nombre }} 
                                        {{ $instructor->persona->segundo_nombre }} 
                                        {{ $instructor->persona->primer_apellido }} 
                                        {{ $instructor->persona->segundo_apellido }}
                                    </h6>
                                    <small class="text-muted">Instructor SENA</small>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table detail-table mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="py-3">Tipo de Documento</th>
                                            <td class="py-3">{{ $instructor->persona->tipoDocumento->name ?? 'No registrado' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Número de Documento</th>
                                            <td class="py-3">{{ $instructor->persona->numero_documento }}</td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha de Nacimiento</th>
                                            <td class="py-3">
                                                @if($instructor->persona->fecha_de_nacimiento)
                                                    <i class="far fa-calendar-alt mr-1"></i>
                                                    {{ \Carbon\Carbon::parse($instructor->persona->fecha_de_nacimiento)->format('d/m/Y') }}
                                                @else
                                                    <span class="text-muted">No registrado</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Género</th>
                                            <td class="py-3">{{ $instructor->persona->tipoGenero->name ?? 'No registrado' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Estado del Usuario</th>
                                            <td class="py-3">
                                                <span class="status-badge {{ $instructor->persona->user->status === 1 ? 'status-active' : 'status-inactive' }}">
                                                    <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                                    {{ $instructor->persona->user->status === 1 ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información de Contacto -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card detail-card no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-envelope mr-2"></i>Información de Contacto
                            </h5>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table detail-table mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="py-3">Correo Electrónico</th>
                                            <td class="py-3">
                                                <a href="mailto:{{ $instructor->persona->email }}" class="text-primary">
                                                    <i class="fas fa-envelope mr-1"></i>
                                                    {{ $instructor->persona->email }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Teléfono</th>
                                            <td class="py-3">
                                                @if($instructor->persona->telefono)
                                                    <a href="tel:{{ $instructor->persona->telefono }}" class="text-primary">
                                                        <i class="fas fa-phone mr-1"></i>
                                                        {{ $instructor->persona->telefono }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">No registrado</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Celular</th>
                                            <td class="py-3">
                                                @if($instructor->persona->celular)
                                                    <a href="tel:{{ $instructor->persona->celular }}" class="text-primary">
                                                        <i class="fas fa-mobile-alt mr-1"></i>
                                                        {{ $instructor->persona->celular }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">No registrado</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Dirección</th>
                                            <td class="py-3">
                                                @if($instructor->persona->direccion)
                                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                                    {{ $instructor->persona->direccion }}
                                                @else
                                                    <span class="text-muted">No registrada</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información Institucional -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card detail-card no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-building mr-2"></i>Información Institucional
                            </h5>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table detail-table mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="py-3">Regional</th>
                                            <td class="py-3">
                                                <i class="fas fa-map-marker-alt mr-1"></i>
                                                {{ $instructor->regional->nombre ?? 'No registrada' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Años de Experiencia</th>
                                            <td class="py-3">
                                                @if($instructor->anos_experiencia)
                                                    {{ $instructor->anos_experiencia }} años
                                                @else
                                                    <span class="text-muted">No registrado</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Experiencia Laboral</th>
                                            <td class="py-3">
                                                @if($instructor->experiencia_laboral)
                                                    {{ $instructor->experiencia_laboral }}
                                                @else
                                                    <span class="text-muted">No registrada</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Estado de Disponibilidad</th>
                                            <td class="py-3">
                                                <span class="status-badge {{ $instructor->estaDisponible() ? 'status-active' : 'status-inactive' }}">
                                                    <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                                    {{ $instructor->estaDisponible() ? 'Disponible' : 'Ocupado' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fichas Activas</th>
                                            <td class="py-3">
                                                <span class="status-badge {{ $instructor->tieneFichasActivas() ? 'status-active' : 'status-inactive' }}">
                                                    <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                                    {{ $instructor->tieneFichasActivas() ? 'Sí tiene' : 'No tiene' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha de Registro</th>
                                            <td class="py-3">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ $instructor->fecha_creacion_formateada }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Última Actualización</th>
                                            <td class="py-3">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ $instructor->fecha_actualizacion_formateada }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Especialidades -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card detail-card no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-graduation-cap mr-2"></i>Especialidades
                            </h5>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table detail-table mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="py-3">Especialidad Principal</th>
                                            <td class="py-3">
                                                @php
                                                    $especialidades = $instructor->especialidades ?? [];
                                                    $especialidadPrincipal = $especialidades['principal'] ?? null;
                                                    $especialidadesSecundarias = $especialidades['secundarias'] ?? [];
                                                @endphp
                                                @if($especialidadPrincipal)
                                                    <span class="specialty-badge bg-primary text-white">
                                                        <i class="fas fa-star mr-1"></i>
                                                        {{ $especialidadPrincipal }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">No registrada</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Especialidades Secundarias</th>
                                            <td class="py-3">
                                                @if(count($especialidadesSecundarias) > 0)
                                                    @foreach($especialidadesSecundarias as $especialidad)
                                                        <span class="specialty-badge">
                                                            {{ $especialidad }}
                                                        </span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">No registradas</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fichas de Caracterización -->
            @if($instructor->fichas->count() > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card detail-card no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-clipboard-list mr-2"></i>Fichas de Caracterización Asignadas
                            </h5>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-custom">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Ficha</th>
                                            <th>Programa de Formación</th>
                                            <th>Fecha Inicio</th>
                                            <th>Fecha Fin</th>
                                            <th>Total Horas</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($instructor->fichas as $index => $ficha)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $ficha->ficha }}</strong>
                                            </td>
                                            <td>{{ $ficha->programaFormacion->nombre ?? 'N/A' }}</td>
                                            <td>
                                                @if($ficha->fecha_inicio)
                                                    {{ \Carbon\Carbon::parse($ficha->fecha_inicio)->format('d/m/Y') }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                @if($ficha->fecha_fin)
                                                    {{ \Carbon\Carbon::parse($ficha->fecha_fin)->format('d/m/Y') }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ $ficha->total_horas ?? 'N/A' }}</td>
                                            <td>
                                                <span class="status-badge {{ $ficha->status ? 'status-active' : 'status-inactive' }}">
                                                    <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                                    {{ $ficha->status ? 'Activa' : 'Inactiva' }}
                                                </span>
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
            @else
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card detail-card no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-clipboard-list mr-2"></i>Fichas de Caracterización
                            </h5>
                        </div>

                        <div class="card-body text-center py-4">
                            <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
                            <p class="text-muted mb-0">
                                Este instructor no tiene fichas de caracterización asignadas.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Botones de Acción -->
            <div class="row">
                <div class="col-12">
                    <div class="card detail-card no-hover">
                        <div class="card-footer bg-white py-3">
                            <div class="action-buttons">
                                @can('EDITAR INSTRUCTOR')
                                    <a href="{{ route('instructor.edit', $instructor->id) }}" class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-pencil-alt mr-1"></i> Editar
                                    </a>
                                @endcan

                                @can('GESTIONAR ESPECIALIDADES INSTRUCTOR')
                                    <a href="{{ route('instructor.gestionarEspecialidades', $instructor->id) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-graduation-cap mr-1"></i> Gestionar Especialidades
                                    </a>
                                @endcan

                                @can('VER FICHAS ASIGNADAS')
                                    <a href="{{ route('instructor.fichasAsignadas', $instructor->id) }}" class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-clipboard-list mr-1"></i> Ver Fichas
                                    </a>
                                @endcan

                                @can('EDITAR INSTRUCTOR')
                                    <form action="{{ route('persona.cambiarEstadoUser', $instructor->persona->user->id) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-outline-warning btn-sm" 
                                                onclick="return confirm('¿Está seguro de cambiar el estado del usuario?')">
                                            <i class="fas fa-sync mr-1"></i>
                                            {{ $instructor->persona->user->status === 1 ? 'Desactivar' : 'Activar' }} Usuario
                                        </button>
                                    </form>
                                @endcan

                                @can('ELIMINAR INSTRUCTOR')
                                    <form action="{{ route('instructor.destroy', $instructor->id) }}" 
                                          method="POST" class="d-inline formulario-eliminar">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-trash mr-1"></i> Eliminar
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Confirmación para formularios de eliminación
            $('.formulario-eliminar').on('submit', function(e) {
                e.preventDefault();
                const form = this;
                
                Swal.fire({
                    title: '¿Eliminar Instructor?',
                    text: 'Esta acción eliminará el instructor pero mantendrá la persona intacta. ¿Está seguro?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // Tooltips para elementos interactivos
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection

@section('footer')
    @include('layout.footer')
@endsection