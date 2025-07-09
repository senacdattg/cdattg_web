@extends('adminlte::page')

@section('title', 'Gestión de Actividades')

@section('css')
    @vite(['resources/css/Asistencia/caracter_selecter.css'])
@endsection

@section('content_header')
    <section class="content-header dashboard-header py-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 d-flex align-items-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width: 48px; height: 48px;">
                        <i class="fas fa-fw fa-paint-brush text-white"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Registro de actividades</h1>
                        <p class="text-muted mb-0 font-weight-light">Gestión de actividades</p>
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
                            <li class="breadcrumb-item active" aria-current="page">
                                <i class="fas fa-fw fa-paint-brush"></i> Registro de actividades
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content')
    <!-- Filtros de Búsqueda -->
    <div class="card card-outline card-primary mb-4">
        <div class="card-body">
            <form class="form-inline d-flex justify-content-between w-100">
                <div class="d-flex align-items-center">
                    <div class="form-group mr-3 mb-2">
                        <label for="filtroEstado" class="mr-2">Estado:</label>
                        <select class="form-control form-control-sm" id="filtroEstado">
                            <option value="">Todos</option>
                            <option value="pendiente">Pendientes</option>
                            <option value="en_curso">En Curso</option>
                            <option value="completada">Completadas</option>
                        </select>
                    </div>
                    <div class="form-group mr-3 mb-2">
                        <label for="filtroFecha" class="mr-2">Fecha:</label>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" id="filtroFecha" placeholder="dd/mm/aaaa">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm mb-2">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                </div>
                <div>
                    <a href="#" class="btn btn-success btn-sm mb-2">
                        <i class="fas fa-plus"></i> Nueva Actividad
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3 gx-3 gy-4 mb-4">
        <div class="col">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="bg-soft-info rounded-circle p-2 p-sm-3 me-3">
                            <i class="fas fa-calendar-check text-info" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-uppercase text-muted small mb-1">Total de Actividades</h6>
                            <div class="d-flex align-items-end">
                                <h3 class="mb-0 fw-bold me-2">3</h3>
                                <span class="text-success small mb-1">
                                    <i class="fas fa-arrow-up"></i> 12%
                                </span>
                            </div>
                            <div class="progress mt-2" style="height: 4px;">
                                <div class="progress-bar bg-info" style="width: 85%"></div>
                            </div>
                            <p class="small text-muted mt-2 mb-0">
                                <i class="far fa-calendar me-1"></i> 3 este mes
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="bg-soft-warning rounded-circle p-2 p-sm-3 me-3">
                            <i class="fas fa-user-clock text-warning" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-uppercase text-muted small mb-1">En Curso</h6>
                            <div class="d-flex align-items-end">
                                <h3 class="mb-0 fw-bold me-2">1</h3>
                                <span class="text-warning small mb-1">
                                    <i class="fas fa-pause"></i> 33%
                                </span>
                            </div>
                            <div class="progress mt-2" style="height: 4px;">
                                <div class="progress-bar bg-warning" style="width: 33%"></div>
                            </div>
                            <p class="small text-muted mt-2 mb-0">
                                <i class="far fa-clock me-1"></i> 1 en progreso
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="bg-soft-success rounded-circle p-2 p-sm-3 me-3">
                            <i class="fas fa-check-circle text-success" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-uppercase text-muted small mb-1">Completadas</h6>
                            <div class="d-flex align-items-end">
                                <h3 class="mb-0 fw-bold me-2">1</h3>
                                <span class="text-success small mb-1">
                                    <i class="fas fa-check"></i> 100%
                                </span>
                            </div>
                            <div class="progress mt-2" style="height: 4px;">
                                <div class="progress-bar bg-success" style="width: 100%"></div>
                            </div>
                            <p class="small text-muted mt-2 mb-0">
                                <i class="far fa-calendar-check me-1"></i> 1 esta semana
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Actividades -->
    <div class="row">
        @php
            // Datos de prueba mejorados
            $actividades = [
                [
                    'id' => 1,
                    'codigo' => 'ACT-' . str_pad(1, 4, '0', STR_PAD_LEFT),
                    'titulo' => 'Sesión de Programación Web Avanzada',
                    'fecha' => '2025-07-05',
                    'hora_inicio' => '08:00',
                    'hora_fin' => '12:00',
                    'descripcion' =>
                        'Desarrollo de aplicaciones web modernas utilizando Laravel y Vue.js, implementando buenas prácticas y patrones de diseño.',
                    'instructor' => 'Juan Pérez',
                    'estado' => 'pendiente',
                    'asistentes' => 15,
                    'total_aprendices' => 20,
                    'tipo' => 'Teórico-Práctica',
                    'sala' => 'Sala 305 - Laboratorio de Computación',
                    'temas' => ['Introducción a Laravel', 'Componentes Vue.js', 'API REST', 'Autenticación JWT'],
                ],
                [
                    'id' => 2,
                    'codigo' => 'ACT-' . str_pad(2, 4, '0', STR_PAD_LEFT),
                    'titulo' => 'Taller de Base de Datos Relacionales',
                    'fecha' => '2025-07-04',
                    'hora_inicio' => '13:00',
                    'hora_fin' => '17:00',
                    'descripcion' =>
                        'Modelado avanzado de bases de datos relacionales y optimización de consultas SQL complejas.',
                    'instructor' => 'María García',
                    'estado' => 'completada',
                    'asistentes' => 18,
                    'total_aprendices' => 18,
                    'tipo' => 'Práctica',
                    'sala' => 'Sala 201 - Laboratorio de BD',
                    'temas' => ['Modelo Relacional', 'Normalización', 'Índices', 'Vistas'],
                ],
                [
                    'id' => 3,
                    'codigo' => 'ACT-' . str_pad(3, 4, '0', STR_PAD_LEFT),
                    'titulo' => 'Control de Versiones con Git',
                    'fecha' => '2025-07-03',
                    'hora_inicio' => '09:00',
                    'hora_fin' => '11:00',
                    'descripcion' =>
                        'Gestión de código fuente utilizando Git y GitHub, incluyendo flujos de trabajo colaborativos.',
                    'instructor' => 'Carlos López',
                    'estado' => 'en_curso',
                    'asistentes' => 22,
                    'total_aprendices' => 25,
                    'tipo' => 'Taller',
                    'sala' => 'Sala 102 - Aula de Capacitación',
                    'temas' => ['Ramas', 'Fusión', 'Pull Requests', 'CI/CD'],
                ],
            ];
        @endphp

        @foreach ($actividades as $actividad)
            @php
                $porcentaje = ($actividad['asistentes'] / $actividad['total_aprendices']) * 100;
                $diasRestantes = \Carbon\Carbon::parse($actividad['fecha'])->diffInDays(now(), false) * -1;
            @endphp

            <div class="col-12 mb-4">
                <div class="card activity-card h-100">
                    <div
                        class="card-header 
                        @if ($actividad['estado'] == 'pendiente') bg-gradient-lightblue
                        @elseif($actividad['estado'] == 'en_curso') bg-gradient-info
                        @else bg-gradient-success @endif">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    @if ($actividad['estado'] == 'pendiente')
                                        <i class="far fa-clock fa-2x text-white"></i>
                                    @elseif($actividad['estado'] == 'en_curso')
                                        <i class="fas fa-spinner fa-spin fa-2x text-white"></i>
                                    @else
                                        <i class="fas fa-check-circle fa-2x text-white"></i>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="mb-0 text-white">{{ $actividad['titulo'] }}</h4>
                                    <div class="text-white-50">
                                        <i class="far fa-hashtag"></i> {{ $actividad['codigo'] }}
                                    </div>
                                </div>
                            </div>
                            <div>
                                <span
                                    class="badge badge-pill 
                                    @if ($actividad['estado'] == 'pendiente') bg-dark
                                    @elseif($actividad['estado'] == 'en_curso') bg-primary
                                    @else bg-light text-dark @endif">
                                    {{ ucfirst(str_replace('_', ' ', $actividad['estado'])) }}
                                </span>
                                @if ($actividad['estado'] == 'pendiente' && $diasRestantes > 0)
                                    <span class="badge badge-pill bg-warning text-dark ml-1">
                                        <i class="far fa-calendar-alt"></i> En {{ $diasRestantes }} días
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <!-- Columna de información principal -->
                            <div class="col-lg-8">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <i class="far fa-calendar-alt text-primary"></i>
                                            <div>
                                                <small class="text-muted d-block">Fecha</small>
                                                <strong>{{ \Carbon\Carbon::parse($actividad['fecha'])->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <i class="far fa-clock text-primary"></i>
                                            <div>
                                                <small class="text-muted d-block">Horario</small>
                                                <strong>{{ $actividad['hora_inicio'] }} -
                                                    {{ $actividad['hora_fin'] }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-3">
                                        <div class="info-item">
                                            <i class="fas fa-user-tie text-primary"></i>
                                            <div>
                                                <small class="text-muted d-block">Instructor</small>
                                                <strong>{{ $actividad['instructor'] }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-3">
                                        <div class="info-item">
                                            <i class="fas fa-door-open text-primary"></i>
                                            <div>
                                                <small class="text-muted d-block">Ubicación</small>
                                                <strong>{{ $actividad['sala'] }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Descripción -->
                                <div class="mb-3">
                                    <h6 class="text-muted mb-2">
                                        <i class="fas fa-align-left"></i> Descripción
                                    </h6>
                                    <p class="mb-0">{{ $actividad['descripcion'] }}</p>
                                </div>

                                <!-- Temas -->
                                @if (!empty($actividad['temas']))
                                    <div class="mb-3">
                                        <h6 class="text-muted mb-2">
                                            <i class="fas fa-list-ul"></i> Temas a tratar
                                        </h6>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach ($actividad['temas'] as $tema)
                                                <span class="badge badge-pill badge-light border">
                                                    <i class="fas fa-circle text-primary" style="font-size: 6px;"></i>
                                                    {{ $tema }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Progreso de asistencia -->
                                <div class="mt-4">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">Asistencia registrada</span>
                                        <span class="font-weight-bold">{{ number_format($porcentaje, 0) }}%</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar 
                                            @if ($porcentaje < 60) bg-danger
                                            @elseif($porcentaje < 80) bg-warning
                                            @else bg-success @endif"
                                            role="progressbar" style="width: {{ $porcentaje }}%"
                                            aria-valuenow="{{ $porcentaje }}" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ $actividad['asistentes'] }} de
                                        {{ $actividad['total_aprendices'] }} aprendices</small>
                                </div>
                            </div>

                            <!-- Columna de acciones -->
                            <div class="col-lg-4 mt-4 mt-lg-0">
                                <div class="d-flex flex-column h-100">
                                    @if ($actividad['estado'] == 'pendiente' || $actividad['estado'] == 'en_curso')
                                        <button class="btn btn-primary btn-block mb-3" data-toggle="modal"
                                            data-target="#tomarAsistenciaModal"
                                            data-actividad-id="{{ $actividad['id'] }}">
                                            <i class="fas fa-clipboard-check"></i> Tomar Asistencia
                                        </button>

                                        <div class="dropdown mb-3">
                                            <button class="btn btn-outline-secondary btn-block dropdown-toggle"
                                                type="button" id="dropdownMenuButton" data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-h"></i> Más opciones
                                            </button>
                                            <div class="dropdown-menu w-100" aria-labelledby="dropdownMenuButton">
                                                <a class="dropdown-item" href="#">
                                                    <i class="fas fa-edit text-primary mr-2"></i> Editar Actividad
                                                </a>
                                                <a class="dropdown-item" href="#">
                                                    <i class="fas fa-file-export text-info mr-2"></i> Exportar Lista
                                                </a>
                                                <a class="dropdown-item" href="#">
                                                    <i class="fas fa-envelope text-success mr-2"></i> Enviar Recordatorio
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item text-danger" href="#">
                                                    <i class="fas fa-trash-alt mr-2"></i> Cancelar Actividad
                                                </a>
                                            </div>
                                        </div>
                                    @else
                                        <button class="btn btn-outline-secondary btn-block mb-3" disabled>
                                            <i class="fas fa-check-circle"></i> Asistencia Registrada
                                        </button>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-outline-primary btn-block">
                                                <i class="fas fa-chart-bar"></i> Estadísticas
                                            </button>
                                            <button class="btn btn-outline-info">
                                                <i class="fas fa-print"></i>
                                            </button>
                                        </div>
                                    @endif

                                    <div class="mt-auto pt-3 border-top">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="far fa-calendar-alt"></i>
                                                {{ \Carbon\Carbon::parse($actividad['fecha'])->format('d/m/Y') }}
                                            </small>
                                            <span
                                                class="badge 
                                                @if ($porcentaje < 60) badge-danger
                                                @elseif($porcentaje < 80) badge-warning
                                                @else badge-success @endif">
                                                {{ number_format($porcentaje, 0) }}% de asistencia
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Modal para Tomar Asistencia -->
    <div class="modal fade" id="tomarAsistenciaModal" tabindex="-1" role="dialog"
        aria-labelledby="tomarAsistenciaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-gradient-primary text-white">
                    <h5 class="modal-title d-flex align-items-center">
                        <i class="fas fa-clipboard-check mr-2"></i>
                        <span>Registrar Asistencia</span>
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="container-fluid p-0">
                        <!-- Encabezado de la actividad -->
                        <div class="bg-light p-3 border-bottom">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 class="mb-1" id="modalActividadTitulo">Sesión de Programación Web Avanzada</h5>
                                    <div class="d-flex flex-wrap text-muted small">
                                        <span class="mr-3">
                                            <i class="far fa-calendar-alt mr-1"></i>
                                            <span id="modalActividadFecha">05/07/2025</span>
                                        </span>
                                        <span class="mr-3">
                                            <i class="far fa-clock mr-1"></i>
                                            <span id="modalActividadHorario">08:00 - 12:00</span>
                                        </span>
                                        <span class="mr-3">
                                            <i class="fas fa-user-tie mr-1"></i>
                                            <span id="modalActividadInstructor">Juan Pérez</span>
                                        </span>
                                        <span>
                                            <i class="fas fa-users mr-1"></i>
                                            <span id="modalActividadAsistencia">15/20 asistentes</span>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0">
                                                <i class="fas fa-search text-muted"></i>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control border-left-0"
                                            placeholder="Buscar aprendiz..." id="buscarAprendiz">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Filtros
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="#">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="filtroAsistencia" checked>
                                                        <label class="custom-control-label"
                                                            for="filtroAsistencia">Asistencia Pendiente</label>
                                                    </div>
                                                </a>
                                                <a class="dropdown-item" href="#">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="filtroInasistencia" checked>
                                                        <label class="custom-control-label"
                                                            for="filtroInasistencia">Inasistencias</label>
                                                    </div>
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item" href="#">
                                                    <i class="fas fa-check-double text-success mr-2"></i> Marcar todos como
                                                    presentes
                                                </a>
                                                <a class="dropdown-item" href="#">
                                                    <i class="fas fa-times-circle text-danger mr-2"></i> Marcar todos como
                                                    ausentes
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabla de asistentes -->
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="50" class="text-center">#</th>
                                        <th width="120">Documento</th>
                                        <th>Nombre Completo</th>
                                        <th width="150" class="text-center">Estado</th>
                                        <th width="200">Observaciones</th>
                                        <th width="100" class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaAsistencia">
                                    @php
                                        $nombres = [
                                            'Ana María',
                                            'Carlos Andrés',
                                            'Diana Carolina',
                                            'Jorge Luis',
                                            'Laura Valentina',
                                            'Miguel Ángel',
                                            'Sofía Alejandra',
                                            'Juan David',
                                        ];
                                        $apellidos = [
                                            'González',
                                            'Rodríguez',
                                            'Martínez',
                                            'López',
                                            'Pérez',
                                            'García',
                                            'Sánchez',
                                            'Ramírez',
                                        ];
                                    @endphp

                                    @for ($i = 1; $i <= 8; $i++)
                                        @php
                                            $nombre = $nombres[array_rand($nombres)];
                                            $apellido = $apellidos[array_rand($apellidos)];
                                            $asistio = rand(0, 1);
                                            $llegadaTarde = $asistio ? rand(0, 1) : 0;
                                            $justificada = $llegadaTarde ? rand(0, 1) : 0;
                                        @endphp
                                        <tr class="align-middle">
                                            <td class="text-center text-muted">{{ $i }}</td>
                                            <td>100{{ str_pad($i, 4, '0', STR_PAD_LEFT) }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm mr-3">
                                                        <span
                                                            class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                            {{ substr($nombre, 0, 1) }}{{ substr($apellido, 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $nombre }} {{ $apellido }}</h6>
                                                        <small class="text-muted">Aprendiz</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                                    <label
                                                        class="btn btn-sm btn-outline-success mb-0 {{ $asistio && !$llegadaTarde ? 'active' : '' }}">
                                                        <input type="radio" name="asistencia{{ $i }}"
                                                            autocomplete="off"
                                                            {{ $asistio && !$llegadaTarde ? 'checked' : '' }}>
                                                        <i class="fas fa-check"></i> Presente
                                                    </label>
                                                    <label
                                                        class="btn btn-sm btn-outline-warning mb-0 {{ $llegadaTarde ? 'active' : '' }}">
                                                        <input type="radio" name="asistencia{{ $i }}"
                                                            autocomplete="off" {{ $llegadaTarde ? 'checked' : '' }}>
                                                        <i class="fas fa-clock"></i> Tarde
                                                    </label>
                                                    <label
                                                        class="btn btn-sm btn-outline-danger mb-0 {{ !$asistio ? 'active' : '' }}">
                                                        <input type="radio" name="asistencia{{ $i }}"
                                                            autocomplete="off" {{ !$asistio ? 'checked' : '' }}>
                                                        <i class="fas fa-times"></i> Ausente
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm observacion"
                                                    placeholder="Motivo (opcional)"
                                                    value="{{ $llegadaTarde ? 'Llegó 15 minutos tarde' : '' }}">
                                            </td>
                                            <td class="text-center">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-link text-muted" type="button"
                                                        data-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <a class="dropdown-item" href="#">
                                                            <i class="fas fa-paperclip text-muted mr-2"></i> Adjuntar
                                                            justificación
                                                        </a>
                                                        <a class="dropdown-item" href="#">
                                                            <i class="fas fa-envelope text-muted mr-2"></i> Enviar
                                                            recordatorio
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div class="text-muted small">
                            <i class="fas fa-info-circle text-primary"></i>
                            <span id="contadorAsistencia">5 de 8 aprendices marcados como presentes</span>
                        </div>
                        <div>
                            <button type="button" class="btn btn-outline-secondary mr-2" data-dismiss="modal">
                                <i class="fas fa-times mr-1"></i> Cancelar
                            </button>
                            <button type="button" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> Guardar Asistencia
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            // Actualizar datos del modal al mostrarlo
            $('#tomarAsistenciaModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var actividadId = button.data('actividad-id');
                var modal = $(this);

                // Aquí iría la lógica para cargar los datos de la actividad
                // Por ahora usamos datos de ejemplo
                var actividad = {
                    titulo: 'Sesión de Programación Web Avanzada',
                    fecha: '05/07/2025',
                    horario: '08:00 - 12:00',
                    instructor: 'Juan Pérez',
                    asistentes: 5,
                    totalAprendices: 8
                };

                modal.find('#modalActividadTitulo').text(actividad.titulo);
                modal.find('#modalActividadFecha').text(actividad.fecha);
                modal.find('#modalActividadHorario').text(actividad.horario);
                modal.find('#modalActividadInstructor').text(actividad.instructor);
                modal.find('#modalActividadAsistencia').text(actividad.asistentes + '/' + actividad.totalAprendices +
                    ' asistentes');

                // Actualizar contador de asistentes
                actualizarContadorAsistencia();

                // Manejar cambios en los radio buttons
                $('input[type="radio"]').change(function() {
                    actualizarContadorAsistencia();
                });

                // Función para actualizar el contador de asistentes
                function actualizarContadorAsistencia() {
                    var total = $('input[type="radio"]').length / 3; // 3 opciones por aprendiz
                    var presentes = $('input[type="radio"]:checked').filter(function() {
                        return $(this).val() === 'presente' || $(this).val() === 'tarde';
                    }).length;

                    $('#contadorAsistencia').text(presentes + ' de ' + total + ' aprendices marcados como presentes');
                }

                // Búsqueda de aprendices
                $('#buscarAprendiz').on('keyup', function() {
                    var value = $(this).val().toLowerCase();
                    $('#tablaAsistencia tr').filter(function() {
                        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                    });
                });
            });

            // Inicializar tooltips
            $(function() {
                $('[data-toggle="tooltip"]').tooltip();
            });
        </script>
    @endpush
@endsection

@section('footer')
    @include('layout.footer')
@endsection


@section('css')
    <style>
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .progress {
            background-color: #e9ecef;
        }

        .btn-lg {
            padding: 0.75rem 1.5rem;
            font-size: 1.1rem;
        }

        .badge {
            font-size: 0.9em;
            padding: 0.5em 0.8em;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Inicializar tooltips
            $('[data-toggle="tooltip"]').tooltip();

            // Manejar el modal de tomar asistencia
            $('#tomarAsistenciaModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var actividadId = button.data('actividad-id');
                var modal = $(this);
                modal.find('.modal-title').text('Registrar Asistencia - Actividad #' + actividadId);

                // Aquí podrías cargar los datos de los aprendices vía AJAX
                // Ejemplo:
                // $.get('/actividades/' + actividadId + '/aprendices', function(data) {
                //     // Actualizar la tabla con los aprendices
                // });
            });

            // Manejar el envío del formulario de asistencia
            $('#guardarAsistenciaBtn').click(function() {
                // Aquí iría la lógica para guardar la asistencia
                Swal.fire({
                    icon: 'success',
                    title: '¡Asistencia registrada!',
                    text: 'La asistencia se ha guardado correctamente.',
                    confirmButtonColor: '#3085d6',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#tomarAsistenciaModal').modal('hide');
                        // Recargar la página o actualizar la interfaz
                        location.reload();
                    }
                });
            });
        });
    </script>
@stop
