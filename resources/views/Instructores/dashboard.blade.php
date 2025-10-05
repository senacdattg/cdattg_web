@extends('adminlte::page')

@section('title', 'Dashboard - ' . $instructor->nombre_completo)

@section('css')
    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
    
    <style>
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .stats-card {
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: none;
            overflow: hidden;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .stats-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .stats-success { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; }
        .stats-warning { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; }
        .stats-info { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #333; }
        .stats-danger { background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%); color: #333; }
        
        .ficha-card {
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border-left: 4px solid #007bff;
            margin-bottom: 1rem;
        }
        
        .ficha-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.12);
        }
        
        .ficha-card.activa { border-left-color: #28a745; }
        .ficha-card.proxima { border-left-color: #ffc107; }
        
        .notification-item {
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 0.5rem;
            border-left: 4px solid;
            transition: all 0.3s ease;
        }
        
        .notification-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .notification-success { border-left-color: #28a745; background: #f8fff9; }
        .notification-info { border-left-color: #17a2b8; background: #f8fcfe; }
        .notification-warning { border-left-color: #ffc107; background: #fffef8; }
        
        .activity-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 0.5rem;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        
        .activity-item:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .calendar-container {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .quick-action-btn {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            text-decoration: none;
            color: #495057;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }
        
        .quick-action-btn:hover {
            border-color: #007bff;
            color: #007bff;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,123,255,0.1);
        }
        
        .progress-mini {
            height: 6px;
            border-radius: 3px;
            background: #e9ecef;
            overflow: hidden;
        }
        
        .progress-mini .progress-bar {
            border-radius: 3px;
            transition: width 0.6s ease;
        }
        
        .badge-status {
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .badge-activa { background: #d4edda; color: #155724; }
        .badge-proxima { background: #fff3cd; color: #856404; }
        .badge-finalizada { background: #e2e3e5; color: #383d41; }
        
        @media (max-width: 768px) {
            .dashboard-header {
                padding: 1rem;
                margin-bottom: 1rem;
            }
            
            .stats-card {
                margin-bottom: 1rem;
            }
            
            .quick-actions {
                grid-template-columns: 1fr;
            }
        }
    </style>
@stop

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>
                <i class="fas fa-tachometer-alt mr-2"></i>
                Dashboard Instructor
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-custom float-sm-right">
                <li class="breadcrumb-item">
                    <a href="{{ route('home.index') }}">
                        <i class="fas fa-home mr-1"></i>Inicio
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    <i class="fas fa-tachometer-alt mr-1"></i>Dashboard
                </li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <!-- Header del Dashboard -->
        <div class="dashboard-header">
            <div class="row align-items-center">
                <div class="col-md-2 text-center">
                    <i class="fas fa-user-circle" style="font-size: 4rem; opacity: 0.9;"></i>
                </div>
                <div class="col-md-8">
                    <h3 class="mb-2">{{ $instructor->nombre_completo }}</h3>
                    <p class="mb-1"><strong>Especialidades:</strong> 
                        @if($instructor->especialidades['principal'] ?? null)
                            <span class="badge badge-primary">{{ $instructor->especialidades['principal'] }}</span>
                        @endif
                        @foreach($instructor->especialidades['secundarias'] ?? [] as $especialidad)
                            <span class="badge badge-secondary">{{ $especialidad }}</span>
                        @endforeach
                    </p>
                    <p class="mb-0"><strong>Experiencia:</strong> {{ $estadisticas['anos_experiencia'] }} años</p>
                </div>
                <div class="col-md-2 text-right">
                    <div class="text-center">
                        <h4 class="mb-0">{{ $estadisticas['fichas_activas'] }}</h4>
                        <small>Fichas Activas</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas Principales -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stats-card stats-primary">
                    <div class="card-body text-center">
                        <i class="fas fa-play-circle" style="font-size: 2.5rem; margin-bottom: 1rem;"></i>
                        <h3 class="mb-1">{{ $estadisticas['fichas_activas'] }}</h3>
                        <p class="mb-0">Fichas Activas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card stats-success">
                    <div class="card-body text-center">
                        <i class="fas fa-clock" style="font-size: 2.5rem; margin-bottom: 1rem;"></i>
                        <h3 class="mb-1">{{ number_format($estadisticas['total_horas']) }}</h3>
                        <p class="mb-0">Horas Totales</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card stats-warning">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-check" style="font-size: 2.5rem; margin-bottom: 1rem;"></i>
                        <h3 class="mb-1">{{ $estadisticas['fichas_finalizadas'] }}</h3>
                        <p class="mb-0">Fichas Finalizadas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card stats-info">
                    <div class="card-body text-center">
                        <i class="fas fa-graduation-cap" style="font-size: 2.5rem; margin-bottom: 1rem;"></i>
                        <h3 class="mb-1">{{ $estadisticas['especialidades'] }}</h3>
                        <p class="mb-0">Especialidades</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Fichas Activas y Próximas -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-clipboard-list mr-2"></i>
                            Fichas Activas y Próximas
                        </h5>
                    </div>
                    <div class="card-body">
                        @forelse($fichasActivas->take(5) as $instructorFicha)
                            @php
                                $ficha = $instructorFicha->ficha;
                                $esProxima = $ficha->fecha_inicio > now()->toDateString();
                            @endphp
                            
                            <div class="ficha-card {{ $esProxima ? 'proxima' : 'activa' }}">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $ficha->programaFormacion->nombre ?? 'Sin programa' }}</h6>
                                            <p class="mb-1 text-muted small">Ficha: {{ $ficha->ficha }}</p>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-calendar-alt text-muted mr-1"></i>
                                                <small class="text-muted">
                                                    {{ Carbon\Carbon::parse($ficha->fecha_inicio)->format('d/m/Y') }} - 
                                                    {{ Carbon\Carbon::parse($ficha->fecha_fin)->format('d/m/Y') }}
                                                </small>
                                            </div>
                                            <div class="d-flex align-items-center mt-1">
                                                <i class="fas fa-clock text-muted mr-1"></i>
                                                <small class="text-muted">{{ number_format($instructorFicha->total_horas_instructor) }}h</small>
                                                <span class="badge-status {{ $esProxima ? 'badge-proxima' : 'badge-activa' }} ml-2">
                                                    {{ $esProxima ? 'Próxima' : 'Activa' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="badge badge-info">{{ $ficha->ambiente->sede->nombre ?? 'Sin sede' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="fas fa-clipboard-list text-muted" style="font-size: 3rem;"></i>
                                <h5 class="mt-3 text-muted">No hay fichas activas</h5>
                                <p class="text-muted">No tienes fichas asignadas actualmente</p>
                            </div>
                        @endforelse
                        
                        @if($fichasActivas->count() > 5)
                            <div class="text-center mt-3">
                                <a href="{{ route('instructor.fichasAsignadas') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-eye mr-1"></i>Ver todas las fichas
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Notificaciones y Actividades -->
            <div class="col-md-6">
                <div class="row">
                    <!-- Notificaciones -->
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-bell mr-2"></i>
                                    Notificaciones Recientes
                                </h5>
                            </div>
                            <div class="card-body p-2">
                                @forelse($notificaciones as $notificacion)
                                    <div class="notification-item notification-{{ $notificacion['tipo'] }}">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $notificacion['titulo'] }}</h6>
                                                <p class="mb-1 small">{{ $notificacion['mensaje'] }}</p>
                                                <small class="text-muted">
                                                    {{ $notificacion['fecha']->diffForHumans() }}
                                                </small>
                                            </div>
                                            @if(!$notificacion['leida'])
                                                <span class="badge badge-danger badge-sm">Nuevo</span>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-3">
                                        <i class="fas fa-bell-slash text-muted"></i>
                                        <p class="text-muted mb-0">No hay notificaciones</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Actividades Recientes -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-history mr-2"></i>
                                    Actividades Recientes
                                </h5>
                            </div>
                            <div class="card-body p-2">
                                @forelse($actividadesRecientes as $actividad)
                                    <div class="activity-item">
                                        <div class="mr-3">
                                            <i class="{{ $actividad['icono'] }} text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $actividad['titulo'] }}</h6>
                                            <p class="mb-1 small text-muted">{{ $actividad['descripcion'] }}</p>
                                            <small class="text-muted">
                                                {{ $actividad['fecha']->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-3">
                                        <i class="fas fa-history text-muted"></i>
                                        <p class="text-muted mb-0">No hay actividades recientes</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendario de Clases -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="calendar-container">
                    <h5 class="mb-3">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        Calendario de Clases
                    </h5>
                    <div id="calendar"></div>
                </div>
            </div>
        </div>

        <!-- Acciones Rápidas -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-bolt mr-2"></i>
                            Acciones Rápidas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="quick-actions">
                            <a href="{{ route('instructor.fichasAsignadas') }}" class="quick-action-btn">
                                <i class="fas fa-clipboard-list fa-2x"></i>
                                <span>Ver Fichas</span>
                            </a>
                            <a href="{{ route('instructor.gestionarEspecialidades', $instructor->id) }}" class="quick-action-btn">
                                <i class="fas fa-graduation-cap fa-2x"></i>
                                <span>Especialidades</span>
                            </a>
                            <a href="{{ route('instructor.show', $instructor->id) }}" class="quick-action-btn">
                                <i class="fas fa-user fa-2x"></i>
                                <span>Mi Perfil</span>
                            </a>
                            <a href="{{ route('instructor.edit', $instructor->id) }}" class="quick-action-btn">
                                <i class="fas fa-edit fa-2x"></i>
                                <span>Editar Perfil</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    
    <script>
        $(document).ready(function() {
            // Inicializar calendario
            var calendarEl = document.getElementById('calendar');
            var eventos = <?php echo json_encode($eventosCalendario); ?>;
            
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: eventos,
                eventClick: function(info) {
                    var event = info.event;
                    var props = event.extendedProps;
                    
                    Swal.fire({
                        title: event.title,
                        html: '<div class="text-left">' +
                              '<p><strong>Fecha:</strong> ' + event.start.toLocaleDateString('es-ES') + '</p>' +
                              '<p><strong>Hora:</strong> ' + event.start.toLocaleTimeString('es-ES', {hour: '2-digit', minute: '2-digit'}) + ' - ' + event.end.toLocaleTimeString('es-ES', {hour: '2-digit', minute: '2-digit'}) + '</p>' +
                              '<p><strong>Ambiente:</strong> ' + props.ambiente + '</p>' +
                              '<p><strong>Sede:</strong> ' + props.sede + '</p>' +
                              '<p><strong>Modalidad:</strong> ' + props.modalidad + '</p>' +
                              '</div>',
                        icon: 'info',
                        confirmButtonText: 'Cerrar'
                    });
                },
                eventMouseEnter: function(info) {
                    $(info.el).css('cursor', 'pointer');
                }
            });
            
            calendar.render();

            // Auto-hide alerts
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);

            // Animación de entrada para las tarjetas
            $('.stats-card, .ficha-card, .notification-item, .activity-item').each(function(index) {
                $(this).css('opacity', '0').delay(index * 100).animate({
                    opacity: 1
                }, 500);
            });

            // Tooltip initialization
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@stop
