@extends('adminlte::page')

@section('title', 'Detalles del Instructor')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
    <style>
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
            background: #007bff;
            color: white;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .stats-card:nth-child(2) {
            background: #28a745;
        }
        .stats-card:nth-child(3) {
            background: #ffc107;
            color: #212529;
        }
        .stats-card:nth-child(4) {
            background: #dc3545;
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
    <x-page-header 
        icon="fa-chalkboard-teacher" 
        title="Detalles del Instructor"
        subtitle="Información completa del instructor"
        :breadcrumb="[['label' => 'Instructores', 'url' => route('instructor.index') , 'icon' => 'fa-chalkboard-teacher'], ['label' => $instructor->persona->primer_nombre . ' ' . $instructor->persona->primer_apellido, 'icon' => 'fa-user', 'active' => true]]"
    />
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
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table detail-table mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="py-3">Nombre Completo</th>
                                            <td class="py-3">
                                                <strong>{{ $instructor->persona->primer_nombre }} 
                                                {{ $instructor->persona->segundo_nombre }} 
                                                {{ $instructor->persona->primer_apellido }} 
                                                {{ $instructor->persona->segundo_apellido }}</strong>
                                                <br><small class="text-muted">Instructor SENA</small>
                                            </td>
                                        </tr>
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
                                                @if($instructor->persona->fecha_nacimiento)
                                                    <i class="far fa-calendar-alt mr-1"></i>
                                                    @php
                                                        $fechaNacimiento = $instructor->persona->fecha_nacimiento;
                                                        try {
                                                            if ($fechaNacimiento instanceof \Carbon\Carbon) {
                                                                $fechaFormateada = $fechaNacimiento->format('d/m/Y');
                                                            } elseif (is_string($fechaNacimiento)) {
                                                                // Intentar formato d/m/Y primero
                                                                if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $fechaNacimiento)) {
                                                                    $fechaFormateada = $fechaNacimiento;
                                                                } else {
                                                                    // Intentar parsear formato estándar
                                                                    $fechaFormateada = \Carbon\Carbon::parse($fechaNacimiento)->format('d/m/Y');
                                                                }
                                                            } else {
                                                                $fechaFormateada = \Carbon\Carbon::parse($fechaNacimiento)->format('d/m/Y');
                                                            }
                                                        } catch (\Exception $e) {
                                                            $fechaFormateada = $fechaNacimiento;
                                                        }
                                                    @endphp
                                                    {{ $fechaFormateada }}
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
                                            <th class="py-3">Centro de Formación</th>
                                            <td class="py-3">
                                                @if($instructor->centroFormacion)
                                                    <i class="fas fa-building mr-1"></i>
                                                    {{ $instructor->centroFormacion->nombre }}
                                                @else
                                                    <span class="text-muted">No registrado</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Tipo de Vinculación</th>
                                            <td class="py-3">
                                                @if($instructor->tipoVinculacion)
                                                    <i class="fas fa-briefcase mr-1"></i>
                                                    {{ $instructor->tipoVinculacion->parametro->name ?? 'No registrado' }}
                                                @else
                                                    <span class="text-muted">No registrado</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Jornada(s) de Trabajo</th>
                                            <td class="py-3">
                                                @if($instructor->jornadas->count() > 0)
                                                    @foreach($instructor->jornadas as $jornada)
                                                        <span class="badge badge-info mr-1">
                                                            <i class="fas fa-clock mr-1"></i>
                                                            {{ $jornada->jornada }}
                                                        </span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">No registradas</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha de Ingreso al SENA</th>
                                            <td class="py-3">
                                                @if($instructor->fecha_ingreso_sena)
                                                    <i class="far fa-calendar-alt mr-1"></i>
                                                    {{ \Carbon\Carbon::parse($instructor->fecha_ingreso_sena)->format('d/m/Y') }}
                                                @else
                                                    <span class="text-muted">No registrada</span>
                                                @endif
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
                                            <th class="py-3">Experiencia como Instructor (Meses)</th>
                                            <td class="py-3">
                                                @if($instructor->experiencia_instructor_meses)
                                                    {{ $instructor->experiencia_instructor_meses }} meses
                                                @else
                                                    <span class="text-muted">No registrada</span>
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
                                            <th class="py-3">Estado</th>
                                            <td class="py-3">
                                                <span class="status-badge {{ $instructor->status ? 'status-active' : 'status-inactive' }}">
                                                    <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                                    {{ $instructor->status ? 'Activo' : 'Inactivo' }}
                                                </span>
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

            <!-- Formación Académica -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card detail-card no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-graduation-cap mr-2"></i>Formación Académica
                            </h5>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table detail-table mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="py-3">Nivel Académico</th>
                                            <td class="py-3">
                                                @if($instructor->nivelAcademico)
                                                    <i class="fas fa-certificate mr-1"></i>
                                                    {{ $instructor->nivelAcademico->parametro->name ?? 'No registrado' }}
                                                @else
                                                    <span class="text-muted">No registrado</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Formación en Pedagogía</th>
                                            <td class="py-3">
                                                @if($instructor->formacion_pedagogia)
                                                    {{ $instructor->formacion_pedagogia }}
                                                @else
                                                    <span class="text-muted">No registrada</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Título(s) Obtenido(s)</th>
                                            <td class="py-3">
                                                @php
                                                    $titulos = $instructor->titulos_obtenidos ?? [];
                                                @endphp
                                                @if(is_array($titulos) && count($titulos) > 0)
                                                    <ul class="mb-0 pl-3">
                                                        @foreach($titulos as $titulo)
                                                            <li>{{ $titulo }}</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-muted">No registrados</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Institución(es) Educativa(s)</th>
                                            <td class="py-3">
                                                @php
                                                    $instituciones = $instructor->instituciones_educativas ?? [];
                                                @endphp
                                                @if(is_array($instituciones) && count($instituciones) > 0)
                                                    <ul class="mb-0 pl-3">
                                                        @foreach($instituciones as $institucion)
                                                            <li>{{ $institucion }}</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-muted">No registradas</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Certificaciones Técnicas</th>
                                            <td class="py-3">
                                                @php
                                                    $certificaciones = $instructor->certificaciones_tecnicas ?? [];
                                                @endphp
                                                @if(is_array($certificaciones) && count($certificaciones) > 0)
                                                    <ul class="mb-0 pl-3">
                                                        @foreach($certificaciones as $certificacion)
                                                            <li>{{ $certificacion }}</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-muted">No registradas</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Cursos Complementarios</th>
                                            <td class="py-3">
                                                @php
                                                    $cursos = $instructor->cursos_complementarios ?? [];
                                                @endphp
                                                @if(is_array($cursos) && count($cursos) > 0)
                                                    <ul class="mb-0 pl-3">
                                                        @foreach($cursos as $curso)
                                                            <li>{{ $curso }}</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-muted">No registrados</span>
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

            <!-- Competencias y Habilidades -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card detail-card no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-tasks mr-2"></i>Competencias y Habilidades
                            </h5>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table detail-table mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="py-3">Áreas de Experticia</th>
                                            <td class="py-3">
                                                @php
                                                    $areas = $instructor->areas_experticia ?? [];
                                                @endphp
                                                @if(is_array($areas) && count($areas) > 0)
                                                    <ul class="mb-0 pl-3">
                                                        @foreach($areas as $area)
                                                            <li>{{ $area }}</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-muted">No registradas</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Competencias TIC</th>
                                            <td class="py-3">
                                                @php
                                                    $competencias = $instructor->competencias_tic ?? [];
                                                @endphp
                                                @if(is_array($competencias) && count($competencias) > 0)
                                                    <ul class="mb-0 pl-3">
                                                        @foreach($competencias as $competencia)
                                                            <li>{{ $competencia }}</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-muted">No registradas</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Idiomas</th>
                                            <td class="py-3">
                                                @php
                                                    $idiomas = $instructor->idiomas ?? [];
                                                @endphp
                                                @if(is_array($idiomas) && count($idiomas) > 0)
                                                    <ul class="mb-0 pl-3">
                                                        @foreach($idiomas as $idioma)
                                                            @if(is_array($idioma) && isset($idioma['idioma']) && !empty($idioma['idioma']))
                                                                <li>
                                                                    <strong>{{ $idioma['idioma'] }}</strong>
                                                                    @if(isset($idioma['nivel']) && !empty($idioma['nivel']))
                                                                        - <span class="text-muted">{{ ucfirst($idioma['nivel']) }}</span>
                                                                    @endif
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-muted">No registrados</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Habilidades Pedagógicas</th>
                                            <td class="py-3">
                                                @php
                                                    $habilidades = $instructor->habilidades_pedagogicas ?? [];
                                                @endphp
                                                @if(is_array($habilidades) && count($habilidades) > 0)
                                                    @foreach($habilidades as $habilidad)
                                                        <span class="badge badge-primary mr-1">
                                                            <i class="fas fa-chalkboard-teacher mr-1"></i>
                                                            {{ ucfirst($habilidad) }}
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

            <!-- Información Administrativa -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card detail-card no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-file-contract mr-2"></i>Información Administrativa
                            </h5>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table detail-table mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="py-3">Número de Contrato</th>
                                            <td class="py-3">
                                                @if($instructor->numero_contrato)
                                                    {{ $instructor->numero_contrato }}
                                                @else
                                                    <span class="text-muted">No registrado</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha de Inicio de Contrato</th>
                                            <td class="py-3">
                                                @if($instructor->fecha_inicio_contrato)
                                                    <i class="far fa-calendar-alt mr-1"></i>
                                                    {{ \Carbon\Carbon::parse($instructor->fecha_inicio_contrato)->format('d/m/Y') }}
                                                @else
                                                    <span class="text-muted">No registrada</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha de Fin de Contrato</th>
                                            <td class="py-3">
                                                @if($instructor->fecha_fin_contrato)
                                                    <i class="far fa-calendar-alt mr-1"></i>
                                                    {{ \Carbon\Carbon::parse($instructor->fecha_fin_contrato)->format('d/m/Y') }}
                                                @else
                                                    <span class="text-muted">No registrada</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Supervisor de Contrato</th>
                                            <td class="py-3">
                                                @if($instructor->supervisor_contrato)
                                                    {{ $instructor->supervisor_contrato }}
                                                @else
                                                    <span class="text-muted">No registrado</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">EPS</th>
                                            <td class="py-3">
                                                @if($instructor->eps)
                                                    {{ $instructor->eps }}
                                                @else
                                                    <span class="text-muted">No registrada</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">ARL</th>
                                            <td class="py-3">
                                                @if($instructor->arl)
                                                    {{ $instructor->arl }}
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
                                        <button type="submit" class="btn btn-outline-warning btn-sm cambiar-estado-usuario">
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
    @vite(['resources/js/pages/resources-views\Instructores\show.js'])
@endsection

@section('footer')
    @include('layouts.footer')
@endsection