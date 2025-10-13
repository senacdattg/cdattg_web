<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Coordinación Académica Guaviare</title>

    {{-- Estilos Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    {{-- Estilos y Scripts --}}
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css?v=3.2.0') }}">
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        @php
        $user = Auth::user();
        $persona = optional($user)->persona;
        $userImage = $persona && $persona->foto ? asset('storage/' . $persona->foto) : asset('dist/img/LogoSena.png');
        $rol = $user ? ucfirst(strtolower($user->getRoleNames()->first())) : '';
        @endphp

        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{ route('verificarLogin') }}" class="nav-link">Inicio</a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="#" class="nav-link">Contact</a>
                </li>
            </ul>

            {{-- Cierre de sesión y datos del usuario --}}
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown user-menu ml-auto">
                    @auth
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                        <img src="{{ $userImage }}" class="user-image img-circle elevation-2" alt="{{ $persona ? $persona->primer_nombre : 'User' }}">
                        <span class="d-none d-md-inline">
                            {{ $persona->primer_nombre }} {{ $persona->primer_apellido }}
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <li class="user-header bg-primary">
                            <img src="{{ $userImage }}" class="img-circle elevation-2" alt="{{ $persona ? $persona->primer_nombre : 'User' }}">
                            <p>
                                {{ $persona->primer_nombre }} {{ $persona->primer_apellido }}
                            </p>
                            <p>
                                {{ $rol }}
                            </p>
                        </li>
                        <li class="user-footer">
                            @if($user && $user->hasRole('ASPIRANTE'))
                                <a href="/mi-perfil" class="btn btn-default btn-flat">Mi Perfil</a>
                            @else
                                <a href="{{ route('personas.show', ['persona' => $persona->id]) }}" class="btn btn-default btn-flat">Perfil</a>
                            @endif
                            @include('logout')
                        </li>
                    </ul>
                    @else
                    <a href="{{ route('iniciarSesion') }}" class="nav-link">Iniciar Sesión</a>
                    @endauth
                </li>
            </ul>
        </nav>
        <!-- Resto del contenido -->
         