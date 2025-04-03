@extends('adminlte::page')

@section('js')
<script>
$(document).ready(function() {
    $('.logout-form').on('click', function(e) {
        e.preventDefault();
        var form = $('<form action="{{ route("logout") }}" method="POST">@csrf</form>');
        $('body').append(form);
        form.submit();
    });
});
</script>
@stop

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
                <a class="btn btn-outline-secondary btn-lg" href="{{ route('fichaCaracterizacion.index') }}" role="button">
                    Comencemos
                </a>
            @else
                <h1 class="display-4">
                    Hola, <strong>{{ $nombreCompleto }}</strong>!
                </h1>
                <p class="lead">Consulta las notificaciones y actualizaciones en el sistema.</p>
            @endrole
        @else
            <h1 class="display-4">Bienvenido</h1>
            <p class="lead">
                Por favor, <a href="{{ route('iniciarSesion') }}">inicia sesión</a> para acceder al sistema.
            </p>
        @endauth
    </div>
@include('layout.footer')
@endsection
