@extends('adminlte::page')

@section('title', 'Perfil')

@section('content_header')
    <h1><i class="fas fa-user-circle mr-2"></i>Perfil</h1>
    <p class="text-muted mb-0">Información de mi inscripción a programas complementarios</p>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user mr-2"></i>Datos Personales</h5>
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
                <h5 class="mb-0"><i class="fas fa-graduation-cap mr-2"></i>Programa Inscrito</h5>
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
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white text-center">
                <h5 class="mb-0"><i class="fas fa-graduation-cap mr-2"></i>Programa Inscrito</h5>
            </div>
            <div class="card-body text-center">
                <div class="bg-light rounded p-4 mb-3">
                    <i class="fas fa-graduation-cap fa-3x text-success mb-3"></i>
                    <h5 class="text-success mb-2">{{ $aspirante->complementario->nombre }}</h5>
                    <p class="text-muted small mb-3">{{ $aspirante->complementario->descripcion }}</p>
                    <div class="row text-center">
                        <div class="col-6">
                            <small class="text-muted d-block">Duración</small>
                            <strong>{{ $aspirante->complementario->duracion }} horas</strong>
                        </div>
                        
                    </div>
                </div>
                <div class="border-top pt-3">
                    
                    <small class="text-muted">Fecha de registro: {{ $aspirante->created_at->format('d/m/Y') }}</small>
                </div>
            </div>
        </div>
@endsection

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
@endsection