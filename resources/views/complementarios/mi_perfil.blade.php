@extends('adminlte::page')

@section('title', 'Mi Perfil - Aspirante')

@section('content_header')
    <h1><i class="fas fa-user-circle me-2"></i>Mi Perfil</h1>
    <p class="text-muted mb-0">Información de mi inscripción a programas complementarios</p>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Datos Personales</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Tipo de Documento:</strong><br>
                        @php
                            $tipoDocumento = match($aspirante->persona->tipo_documento) {
                                1 => 'Cédula de Ciudadanía',
                                2 => 'Tarjeta de Identidad',
                                3 => 'Cédula de Extranjería',
                                4 => 'Pasaporte',
                                default => 'No especificado'
                            };
                        @endphp
                        {{ $tipoDocumento }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Número de Documento:</strong><br>
                        {{ $aspirante->persona->numero_documento }}
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Nombres:</strong><br>
                        {{ $aspirante->persona->primer_nombre }}
                        {{ $aspirante->persona->segundo_nombre ?? '' }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Apellidos:</strong><br>
                        {{ $aspirante->persona->primer_apellido }}
                        {{ $aspirante->persona->segundo_apellido ?? '' }}
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Fecha de Nacimiento:</strong><br>
                        {{ \Carbon\Carbon::parse($aspirante->persona->fecha_nacimiento)->format('d/m/Y') }}
                        ({{ $aspirante->persona->edad }} años)
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Género:</strong><br>
                        @php
                            $genero = match($aspirante->persona->genero) {
                                1 => 'Masculino',
                                2 => 'Femenino',
                                3 => 'Otro',
                                4 => 'Prefiero no decir',
                                default => 'No especificado'
                            };
                        @endphp
                        {{ $genero }}
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Teléfono Fijo:</strong><br>
                        {{ $aspirante->persona->telefono ?? 'No registrado' }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Celular:</strong><br>
                        {{ $aspirante->persona->celular }}
                    </div>
                </div>

                <div class="mb-3">
                    <strong>Correo Electrónico:</strong><br>
                    {{ $aspirante->persona->email }}
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <strong>País:</strong><br>
                        {{ $aspirante->persona->pais->pais ?? 'No especificado' }}
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Departamento:</strong><br>
                        {{ $aspirante->persona->departamento->departamento ?? 'No especificado' }}
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Municipio:</strong><br>
                        {{ $aspirante->persona->municipio->municipio ?? 'No especificado' }}
                    </div>
                </div>

                <div class="mb-3">
                    <strong>Dirección:</strong><br>
                    {{ $aspirante->persona->direccion }}
                </div>

                @if($aspirante->observaciones)
                <div class="mb-3">
                    <strong>Observaciones:</strong><br>
                    {{ $aspirante->observaciones }}
                </div>
                @endif
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Programa Inscrito</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h6>{{ $aspirante->complementario->nombre }}</h6>
                        <p class="text-muted mb-2">{{ $aspirante->complementario->descripcion }}</p>
                        <div class="row">
                            <div class="col-md-6">
                                <small><strong>Duración:</strong> {{ $aspirante->complementario->duracion }} horas</small>
                            </div>
                            <div class="col-md-6">
                                <small><strong>Cupos:</strong> {{ $aspirante->complementario->cupos }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="bg-light rounded p-3">
                            <i class="fas fa-graduation-cap fa-2x text-primary mb-2"></i>
                            <p class="mb-0 small">Programa Complementario</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card sticky-top shadow-sm" style="top:20px;">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Estado de la Inscripción</h5>
            </div>
            <div class="card-body text-center">
                @php
                    $estadoClass = match($aspirante->estado) {
                        1 => 'bg-primary',
                        2 => 'bg-danger',
                        3 => 'bg-success',
                        default => 'bg-secondary'
                    };

                    $estadoIcon = match($aspirante->estado) {
                        1 => 'fas fa-clock',
                        2 => 'fas fa-times-circle',
                        3 => 'fas fa-check-circle',
                        default => 'fas fa-question-circle'
                    };
                @endphp

                <div class="{{ $estadoClass }} text-white rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="{{ $estadoIcon }} fa-2x"></i>
                </div>

                <h4 class="mb-2">{{ $aspirante->estado_label }}</h4>

                @if($aspirante->estado == 1)
                    <p class="text-muted">Su inscripción está siendo revisada. Recibirá una notificación por correo electrónico cuando haya una actualización.</p>
                @elseif($aspirante->estado == 2)
                    <p class="text-muted">Lamentablemente su solicitud no fue aprobada. Puede contactar al área administrativa para más información.</p>
                @elseif($aspirante->estado == 3)
                    <p class="text-muted">¡Felicidades! Su inscripción ha sido aprobada. Pronto recibirá información sobre el inicio del programa.</p>
                @endif

                <div class="mt-4">
                    <small class="text-muted">ID de Inscripción: {{ $aspirante->id }}</small><br>
                    <small class="text-muted">Fecha de registro: {{ $aspirante->created_at->format('d/m/Y') }}</small>
                </div>
            </div>
        </div>

        <div class="card mt-3 shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0"><i class="fas fa-file-pdf me-2"></i>Documentos</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-id-card text-primary me-3 fa-lg"></i>
                    <div>
                        <strong>Documento de Identidad</strong>
                        <p class="mb-0 small text-muted">PDF subido</p>
                    </div>
                </div>
                <div class="alert alert-info small">
                    <i class="fas fa-info-circle me-2"></i>
                    Los documentos están siendo revisados por el equipo administrativo.
                </div>
            </div>
        </div>

        <div class="card mt-3 shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0"><i class="fas fa-phone me-2"></i>Contacto</h6>
            </div>
            <div class="card-body">
                <p class="small mb-2">
                    <i class="fas fa-envelope me-2"></i>
                    <strong>Correo:</strong> administrativo@sena.edu.co
                </p>
                <p class="small mb-0">
                    <i class="fas fa-phone me-2"></i>
                    <strong>Teléfono:</strong> (601) 546 1500
                </p>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .card-header {
        font-weight: 600;
    }
    .sticky-top {
        z-index: 100;
    }
</style>
@stop

@section('js')
<script>
    console.log('Página de perfil del aspirante cargada');
</script>
@stop