@extends('adminlte::page')

@section('title', 'Detalle Aprendiz')

@section('css')
    @vite(['resources/css/parametros.css'])
    <style>
        .info-box {
            min-height: 80px;
        }
        .info-box-text {
            font-size: 0.875rem;
        }
        .info-box-number {
            font-size: 1.5rem;
            font-weight: bold;
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
                    <i class="fas fa-user-graduate text-white fa-lg"></i>
                </div>
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Información del Aprendiz</h1>
                    <p class="text-muted mb-0 font-weight-light">{{ $aprendiz->persona->nombre_completo }}</p>
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
                            <a href="{{ route('aprendices.index') }}" class="link_right_header">
                                <i class="fas fa-user-graduate"></i> Aprendices
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ $aprendiz->persona->nombre_completo }}
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
        <!-- Botón Volver y Editar -->
        <div class="mb-3">
            <a class="btn btn-secondary btn-sm" href="{{ route('aprendices.index') }}" title="Volver">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            @can('EDITAR APRENDIZ')
                <a class="btn btn-warning btn-sm" href="{{ route('aprendices.edit', $aprendiz->id) }}" title="Editar">
                    <i class="fas fa-edit"></i> Editar
                </a>
            @endcan
        </div>

        <div class="row">
            <!-- Columna izquierda: Información del Aprendiz -->
            <div class="col-md-4">
                <!-- Perfil del Aprendiz -->
                <div class="card shadow-sm card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <img class="profile-user-img img-fluid img-circle" 
                                     src="https://ui-avatars.com/api/?name={{ urlencode($aprendiz->persona->nombre_completo) }}&size=128&background=007bff&color=fff"
                                     alt="Foto de perfil">
                            </div>
                            <h3 class="profile-username text-center">{{ $aprendiz->persona->nombre_completo }}</h3>
                            <p class="text-muted text-center">
                                @if($aprendiz->persona->user)
                                    {{ $aprendiz->persona->user->getRoleNames()->implode(', ') }}
                                @else
                                    Sin rol asignado
                                @endif
                            </p>

                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b><i class="fas fa-id-card"></i> Documento</b>
                                    <span class="float-right">{{ $aprendiz->persona->numero_documento }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b><i class="fas fa-envelope"></i> Email</b>
                                    <span class="float-right">{{ $aprendiz->persona->email }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b><i class="fas fa-mobile-alt"></i> Celular</b>
                                    <span class="float-right">
                                        @if ($aprendiz->persona->celular)
                                            <a href="https://wa.me/{{ $aprendiz->persona->celular }}" target="_blank">
                                                {{ $aprendiz->persona->celular }} <i class="fab fa-whatsapp text-success"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">No disponible</span>
                                        @endif
                                    </span>
                                </li>
                                <li class="list-group-item">
                                    <b><i class="fas fa-toggle-on"></i> Estado</b>
                                    <span class="float-right">
                                        <span class="badge badge-{{ $aprendiz->estado ? 'success' : 'danger' }}">
                                            {{ $aprendiz->estado ? 'ACTIVO' : 'INACTIVO' }}
                                        </span>
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>

                <!-- Estadísticas -->
                <div class="card shadow-sm card-info">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-bar"></i> Estadísticas</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="info-box bg-info">
                                        <div class="info-box-content">
                                            <span class="info-box-text">Fichas</span>
                                            <span class="info-box-number">{{ $aprendiz->fichasCaracterizacion->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="info-box bg-success">
                                        <div class="info-box-content">
                                            <span class="info-box-text">Asistencias</span>
                                            <span class="info-box-number">{{ $aprendiz->asistencias->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- Columna derecha: Detalles y fichas -->
            <div class="col-md-8">
                <!-- Ficha Principal -->
                <div class="card shadow-sm card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-graduation-cap"></i> Ficha Principal</h3>
                        </div>
                        <div class="card-body">
                            @if($aprendiz->fichaCaracterizacion)
                                <dl class="row">
                                    <dt class="col-sm-4">Número de Ficha:</dt>
                                    <dd class="col-sm-8">
                                        <span class="badge badge-primary">{{ $aprendiz->fichaCaracterizacion->ficha }}</span>
                                    </dd>

                                    <dt class="col-sm-4">Programa de Formación:</dt>
                                    <dd class="col-sm-8">{{ $aprendiz->fichaCaracterizacion->programaFormacion->nombre ?? 'N/A' }}</dd>

                                    <dt class="col-sm-4">Jornada:</dt>
                                    <dd class="col-sm-8">{{ $aprendiz->fichaCaracterizacion->jornadaFormacion->nombre ?? 'N/A' }}</dd>

                                    <dt class="col-sm-4">Fecha Inicio:</dt>
                                    <dd class="col-sm-8">{{ $aprendiz->fichaCaracterizacion->fecha_inicio ? \Carbon\Carbon::parse($aprendiz->fichaCaracterizacion->fecha_inicio)->format('d/m/Y') : 'N/A' }}</dd>

                                    <dt class="col-sm-4">Fecha Fin:</dt>
                                    <dd class="col-sm-8">{{ $aprendiz->fichaCaracterizacion->fecha_fin ? \Carbon\Carbon::parse($aprendiz->fichaCaracterizacion->fecha_fin)->format('d/m/Y') : 'N/A' }}</dd>
                                </dl>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> No tiene ficha principal asignada
                                </div>
                            @endif
                        </div>
                    </div>

                <!-- Fichas Asociadas -->
                <div class="card shadow-sm card-success">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-list"></i> Todas las Fichas Asociadas</h3>
                        </div>
                        <div class="card-body">
                            @if($aprendiz->fichasCaracterizacion->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped table-sm">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Ficha</th>
                                                <th>Programa</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($aprendiz->fichasCaracterizacion as $ficha)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <span class="badge badge-info">{{ $ficha->ficha }}</span>
                                                    </td>
                                                    <td>{{ $ficha->programaFormacion->nombre ?? 'N/A' }}</td>
                                                    <td>
                                                        <span class="badge badge-{{ $ficha->status ? 'success' : 'danger' }}">
                                                            {{ $ficha->status ? 'Activa' : 'Inactiva' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> No tiene fichas asociadas
                                </div>
                            @endif
                        </div>
                    </div>

                <!-- Últimas Asistencias -->
                <div class="card shadow-sm card-warning">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-clock"></i> Últimas Asistencias</h3>
                        </div>
                        <div class="card-body">
                            @if($aprendiz->asistencias->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped table-sm">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Hora Ingreso</th>
                                                <th>Hora Salida</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($aprendiz->asistencias->take(5) as $asistencia)
                                                <tr>
                                                    <td>{{ $asistencia->created_at->format('d/m/Y') }}</td>
                                                    <td>
                                                        @if($asistencia->hora_ingreso)
                                                            <span class="badge badge-success">
                                                                {{ \Carbon\Carbon::parse($asistencia->hora_ingreso)->format('H:i') }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($asistencia->hora_salida)
                                                            <span class="badge badge-info">
                                                                {{ \Carbon\Carbon::parse($asistencia->hora_salida)->format('H:i') }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if($aprendiz->asistencias->count() > 5)
                                    <p class="text-muted text-center mt-2">
                                        <small>Mostrando las últimas 5 asistencias de {{ $aprendiz->asistencias->count() }} totales</small>
                                    </p>
                                @endif
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> No hay registros de asistencia
                                </div>
                            @endif
                        </div>
                    </div>

                <!-- Información de Auditoría -->
                <div class="card shadow-sm card-secondary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-history"></i> Información de Registro</h3>
                        </div>
                        <div class="card-body">
                            <dl class="row">
                                <dt class="col-sm-4">Fecha de Registro:</dt>
                                <dd class="col-sm-8">{{ $aprendiz->created_at->format('d/m/Y H:i:s') }}</dd>

                                <dt class="col-sm-4">Última Actualización:</dt>
                                <dd class="col-sm-8">{{ $aprendiz->updated_at->format('d/m/Y H:i:s') }}</dd>

                                <dt class="col-sm-4">Tiempo como Aprendiz:</dt>
                                <dd class="col-sm-8">{{ $aprendiz->created_at->diffForHumans() }}</dd>
                            </dl>
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

