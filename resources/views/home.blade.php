@extends('adminlte::page')
@section('title', 'Inicio')
@section('content')
    <div class="jumbotron py-3">
        @auth
            @php
                $nombreCompleto = optional(Auth::user()->persona)->nombre_completo ?: 'Usuario';
            @endphp

            @role('SUPER ADMINISTRADOR')
                <h1 class="display-4">
                    @include('dashboards.superadmin')
                </h1>
                <p class="lead">Tienes acceso completo a todas las herramientas del sistema.</p>
                @elserole('ADMINISTRADOR')
                <h1 class="display-4">
                    Bienvenido <strong>{{ $nombreCompleto }}</strong>!
                </h1>
                <p class="lead">Tienes acceso a la administración del sistema. Revisa las configuraciones y reportes.</p>
                @elserole('INSTRUCTOR')
                <h1 class="display-4">
                    Hola Instructor <strong>{{ $nombreCompleto }}</strong>!
                </h1>
                <p class="lead">Recuerda tomar la asistencia y revisar tus asignaciones.</p>
                <hr class="my-4">
                <p>Para comenzar, haz clic en el siguiente botón:</p>
                <a class="btn btn-outline-secondary btn-lg" 
                href="{{ route('fichaCaracterizacion.index') }}" role="button">
                    Comencemos
                </a>
            @else
                <h1 class="display-4">
                    Hola, <strong>{{ $nombreCompleto }}</strong>!
                </h1>
                <p class="lead">Consulta las notificaciones y actualizaciones en el sistema.</p>
                
                <!-- Programas Complementarios en los que estás inscrito -->
                @if(isset($programasInscritos) && $programasInscritos->count() > 0)
                    <div class="mt-5">
                        <h3 class="mb-4">Mis Programas Complementarios</h3>
                        <p class="text-muted mb-4">Estos son los programas en los que estás inscrito actualmente:</p>
                        <div class="row justify-content-center g-3">
                            @foreach ($programasInscritos as $programa)
                                @include('complementarios.components.card-programas', ['programa' => $programa])
                            @endforeach
                        </div>
                        <hr class="my-5">
                    </div>
                @endif
                
                <!-- Programas Complementarios Disponibles -->
                @if(isset($programas) && $programas->count() > 0)
                    <div class="mt-5">
                        <h3 class="mb-4">Programas Complementarios Disponibles</h3>
                        <p class="text-muted mb-4">Explora otros programas complementarios que puedes tomar:</p>
                        <div class="row justify-content-center g-3">
                            @foreach ($programas as $programa)
                                @include('complementarios.components.card-programas', ['programa' => $programa])
                            @endforeach
                        </div>
                    </div>
                @endif
            @endrole
        @else
            <h1 class="display-4">Bienvenido</h1>
            <p class="lead">
                Por favor, <a href="{{ route('iniciarSesion') }}">inicia sesión</a> para acceder al sístema.
            </p>
        @endauth
    </div>
@endsection

@section('footer')
    @include('layout.footer')
@endsection
