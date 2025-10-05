@extends('adminlte::page')

@section('title', 'Perfil del Instructor')

@section('css')
    <style>
        .profile-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            color: white;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 5px solid rgba(255,255,255,0.3);
            object-fit: cover;
            margin: 0 auto 20px;
            display: block;
        }
        .profile-name {
            font-size: 2rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .profile-title {
            text-align: center;
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 20px;
        }
        .info-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #007bff;
        }
        .info-card h5 {
            color: #007bff;
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        .info-card h5 i {
            margin-right: 10px;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #333;
            flex: 1;
        }
        .info-value {
            color: #666;
            flex: 2;
            text-align: right;
        }
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        .stats-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }
        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .stats-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn-action {
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .table-custom {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .table-custom thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .table-custom thead th {
            border: none;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.85rem;
        }
        .table-custom tbody tr {
            transition: all 0.3s ease;
        }
        .table-custom tbody tr:hover {
            background: #f8f9fa;
            transform: scale(1.01);
        }
        .breadcrumb-custom {
            background: transparent;
            padding: 0;
        }
        .breadcrumb-custom .breadcrumb-item a {
            color: #007bff;
            text-decoration: none;
        }
        .breadcrumb-custom .breadcrumb-item.active {
            color: #6c757d;
        }
        .alert-custom {
            border-radius: 10px;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        @media (max-width: 768px) {
            .profile-name {
                font-size: 1.5rem;
            }
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }
            .btn-action {
                width: 100%;
                max-width: 300px;
            }
        }
    </style>
@endsection

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="text-primary">
                        <i class="fas fa-user mr-2"></i>
                        Perfil del Instructor
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb breadcrumb-custom float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home.index') }}">
                                <i class="fas fa-home mr-1"></i>Inicio
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('instructor.index') }}">
                                <i class="fas fa-chalkboard-teacher mr-1"></i>Instructores
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            <i class="fas fa-user mr-1"></i>{{ $instructor->persona->primer_nombre }} {{ $instructor->persona->primer_apellido }}
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Alertas -->
            @if (session('success'))
                <div class="alert alert-success alert-custom alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-custom alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="row">
                <!-- Perfil Principal -->
                <div class="col-12">
                    <div class="profile-card">
                        <img src="{{ asset('dist/img/LogoSena.jpeg') }}" alt="Foto del Instructor" class="profile-avatar">
                        <h1 class="profile-name">
                            {{ $instructor->persona->primer_nombre }} 
                            {{ $instructor->persona->segundo_nombre }} 
                            {{ $instructor->persona->primer_apellido }} 
                            {{ $instructor->persona->segundo_apellido }}
                        </h1>
                        <p class="profile-title">
                            <i class="fas fa-chalkboard-teacher mr-2"></i>
                            Instructor SENA
                        </p>
                        <div class="text-center">
                            <span class="status-badge {{ $instructor->persona->user->status === 1 ? 'status-active' : 'status-inactive' }}">
                                <i class="fas fa-circle mr-1"></i>
                                {{ $instructor->persona->user->status === 1 ? 'ACTIVO' : 'INACTIVO' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas -->
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="stats-number">{{ $instructor->numero_fichas_asignadas }}</div>
                        <div class="stats-label">
                            <i class="fas fa-clipboard-list mr-1"></i>
                            Fichas Asignadas
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="stats-number">{{ $instructor->total_horas_asignadas }}</div>
                        <div class="stats-label">
                            <i class="fas fa-clock mr-1"></i>
                            Horas Totales
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="stats-number">{{ $instructor->edad }}</div>
                        <div class="stats-label">
                            <i class="fas fa-birthday-cake mr-1"></i>
                            Años de Edad
                        </div>
                    </div>
                </div>

                <!-- Información Personal -->
                <div class="col-md-6">
                    <div class="info-card">
                        <h5>
                            <i class="fas fa-id-card"></i>
                            Información Personal
                        </h5>
                        <div class="info-item">
                            <span class="info-label">Tipo de Documento:</span>
                            <span class="info-value">{{ $instructor->persona->tipoDocumento->name ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Número de Documento:</span>
                            <span class="info-value">{{ $instructor->persona->numero_documento }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Fecha de Nacimiento:</span>
                            <span class="info-value">{{ $instructor->persona->fecha_de_nacimiento ? \Carbon\Carbon::parse($instructor->persona->fecha_de_nacimiento)->format('d/m/Y') : 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Género:</span>
                            <span class="info-value">{{ $instructor->persona->tipoGenero->name ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Edad:</span>
                            <span class="info-value">{{ $instructor->edad }} años</span>
                        </div>
                    </div>
                </div>

                <!-- Información de Contacto -->
                <div class="col-md-6">
                    <div class="info-card">
                        <h5>
                            <i class="fas fa-envelope"></i>
                            Información de Contacto
                        </h5>
                        <div class="info-item">
                            <span class="info-label">Correo Electrónico:</span>
                            <span class="info-value">
                                <a href="mailto:{{ $instructor->persona->email }}" class="text-primary">
                                    {{ $instructor->persona->email }}
                                </a>
                            </span>
                        </div>
                        @if($instructor->persona->telefono)
                        <div class="info-item">
                            <span class="info-label">Teléfono:</span>
                            <span class="info-value">
                                <a href="tel:{{ $instructor->persona->telefono }}" class="text-primary">
                                    {{ $instructor->persona->telefono }}
                                </a>
                            </span>
                        </div>
                        @endif
                        @if($instructor->persona->celular)
                        <div class="info-item">
                            <span class="info-label">Celular:</span>
                            <span class="info-value">
                                <a href="tel:{{ $instructor->persona->celular }}" class="text-primary">
                                    {{ $instructor->persona->celular }}
                                </a>
                            </span>
                        </div>
                        @endif
                        @if($instructor->persona->direccion)
                        <div class="info-item">
                            <span class="info-label">Dirección:</span>
                            <span class="info-value">{{ $instructor->persona->direccion }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Información Institucional -->
                <div class="col-md-6">
                    <div class="info-card">
                        <h5>
                            <i class="fas fa-building"></i>
                            Información Institucional
                        </h5>
                        <div class="info-item">
                            <span class="info-label">Regional:</span>
                            <span class="info-value">{{ $instructor->regional->regional ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Estado del Usuario:</span>
                            <span class="info-value">
                                <span class="status-badge {{ $instructor->persona->user->status === 1 ? 'status-active' : 'status-inactive' }}">
                                    {{ $instructor->persona->user->status === 1 ? 'ACTIVO' : 'INACTIVO' }}
                                </span>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Fecha de Registro:</span>
                            <span class="info-value">{{ $instructor->fecha_creacion_formateada }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Última Actualización:</span>
                            <span class="info-value">{{ $instructor->fecha_actualizacion_formateada }}</span>
                        </div>
                    </div>
                </div>

                <!-- Estado de Disponibilidad -->
                <div class="col-md-6">
                    <div class="info-card">
                        <h5>
                            <i class="fas fa-info-circle"></i>
                            Estado de Disponibilidad
                        </h5>
                        <div class="info-item">
                            <span class="info-label">Disponible para Asignaciones:</span>
                            <span class="info-value">
                                <span class="status-badge {{ $instructor->estaDisponible() ? 'status-active' : 'status-inactive' }}">
                                    {{ $instructor->estaDisponible() ? 'DISPONIBLE' : 'OCUPADO' }}
                                </span>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Tiene Fichas Activas:</span>
                            <span class="info-value">
                                <span class="status-badge {{ $instructor->tieneFichasActivas() ? 'status-active' : 'status-inactive' }}">
                                    {{ $instructor->tieneFichasActivas() ? 'SÍ' : 'NO' }}
                                </span>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Total de Fichas:</span>
                            <span class="info-value">{{ $instructor->numero_fichas_asignadas }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Total de Horas:</span>
                            <span class="info-value">{{ $instructor->total_horas_asignadas }} horas</span>
                        </div>
                    </div>
                </div>

                <!-- Fichas de Caracterización -->
                @if($instructor->fichas->count() > 0)
                <div class="col-12">
                    <div class="info-card">
                        <h5>
                            <i class="fas fa-clipboard-list"></i>
                            Fichas de Caracterización Asignadas
                        </h5>
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
                                        <td>{{ $ficha->fecha_inicio ? \Carbon\Carbon::parse($ficha->fecha_inicio)->format('d/m/Y') : 'N/A' }}</td>
                                        <td>{{ $ficha->fecha_fin ? \Carbon\Carbon::parse($ficha->fecha_fin)->format('d/m/Y') : 'N/A' }}</td>
                                        <td>{{ $ficha->total_horas ?? 'N/A' }}</td>
                                        <td>
                                            <span class="status-badge {{ $ficha->status ? 'status-active' : 'status-inactive' }}">
                                                {{ $ficha->status ? 'ACTIVA' : 'INACTIVA' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @else
                <div class="col-12">
                    <div class="info-card text-center">
                        <h5>
                            <i class="fas fa-clipboard-list"></i>
                            Fichas de Caracterización
                        </h5>
                        <p class="text-muted">
                            <i class="fas fa-info-circle mr-2"></i>
                            Este instructor no tiene fichas de caracterización asignadas.
                        </p>
                    </div>
                </div>
                @endif

                <!-- Botones de Acción -->
                <div class="col-12">
                    <div class="info-card">
                        <h5>
                            <i class="fas fa-cogs"></i>
                            Acciones Disponibles
                        </h5>
                        <div class="action-buttons">
                            @can('EDITAR INSTRUCTOR')
                                <a href="{{ route('instructor.edit', $instructor->id) }}" class="btn btn-warning btn-action">
                                    <i class="fas fa-edit mr-2"></i>
                                    Editar Instructor
                                </a>
                            @endcan

                            @can('EDITAR INSTRUCTOR')
                                <form action="{{ route('persona.cambiarEstadoUser', $instructor->persona->user->id) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-success btn-action" 
                                            onclick="return confirm('¿Está seguro de cambiar el estado del usuario?')">
                                        <i class="fas fa-sync mr-2"></i>
                                        {{ $instructor->persona->user->status === 1 ? 'Desactivar' : 'Activar' }} Usuario
                                    </button>
                                </form>
                            @endcan

                            @can('ELIMINAR INSTRUCTOR')
                                <form action="{{ route('instructor.destroy', $instructor->id) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-action" 
                                            onclick="return confirm('¿Está seguro de eliminar este instructor? Esta acción no se puede deshacer.')">
                                        <i class="fas fa-trash mr-2"></i>
                                        Eliminar Instructor
                                    </button>
                                </form>
                            @endcan

                            <a href="{{ route('instructor.index') }}" class="btn btn-secondary btn-action">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Volver a la Lista
                            </a>
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
            // Animación de entrada para las tarjetas
            $('.info-card, .stats-card').each(function(index) {
                $(this).css('opacity', '0').delay(index * 100).animate({
                    opacity: 1
                }, 500);
            });

            // Confirmación para acciones críticas
            $('form').on('submit', function(e) {
                const action = $(this).find('button[type="submit"]').text().trim();
                if (action.includes('Eliminar') || action.includes('Desactivar') || action.includes('Activar')) {
                    e.preventDefault();
                    const form = this;
                    
                    Swal.fire({
                        title: '¿Confirmar Acción?',
                        text: 'Esta acción puede tener consecuencias importantes. ¿Está seguro?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, continuar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                }
            });

            // Tooltips para elementos interactivos
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection