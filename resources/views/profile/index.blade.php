@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <x-page-header icon="fa-user" title="Mi Perfil" subtitle="Información personal y configuración" :breadcrumb="[
        ['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'],
        ['label' => 'Mi Perfil', 'icon' => 'fa-user', 'active' => true],
    ]" />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center">
                    <a class="btn btn-outline-secondary" href="{{ route('verificarLogin') }}">
                        <i class="fas fa-arrow-left mr-1"></i> Volver al inicio
                    </a>
                </div>
                <div class="d-flex align-items-center">
                    <a class="btn btn-primary mr-2" href="{{ route('password.change') }}">
                        <i class="fas fa-key mr-1"></i> Cambiar Contraseña
                    </a>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-2"></i>
                    <strong>Éxito:</strong> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <strong>Error:</strong> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body box-profile d-flex flex-column align-items-center text-center">
                            @php
                                $userImage =
                                    $persona && $persona->foto
                                        ? asset('storage/' . $persona->foto)
                                        : asset('dist/img/LogoSena.png');
                                $nombreCompleto = $persona
                                    ? trim(($persona->primer_nombre ?? '') . ' ' . ($persona->primer_apellido ?? ''))
                                    : $user->email ?? 'Usuario';
                            @endphp

                            <img class="profile-user-img img-fluid img-circle mb-3" src="{{ $userImage }}"
                                alt="Foto de perfil" style="width: 160px; height: 160px; object-fit: cover;">

                            <h3 class="profile-username font-weight-bold">{{ $nombreCompleto ?: $user->email }}</h3>

                            @php
                                $rolesPersona = collect($rolesAsignados)->sort()->values();
                            @endphp
                            <div class="mb-3">
                                @if ($rolesPersona->isNotEmpty())
                                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                                        @foreach ($rolesPersona as $rol)
                                            <span class="badge badge-info text-white px-3 py-2 mr-2 mb-2">
                                                <i class="fas fa-user-tag mr-1"></i>{{ ucfirst(strtolower($rol)) }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="badge badge-warning text-dark px-3 py-2">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>Sin roles asignados
                                    </span>
                                @endif
                            </div>

                            <span class="badge badge-{{ $user->status === 1 ? 'success' : 'danger' }} px-3 py-2 mb-3">
                                <i class="fas fa-circle mr-1"></i>
                                {{ $user->status === 1 ? 'Activo' : 'Inactivo' }}
                            </span>

                            <div class="d-flex flex-wrap justify-content-center w-100">
                                @if ($user->email)
                                    <a class="btn btn-sm btn-light mx-1 mb-2" href="mailto:{{ $user->email }}">
                                        <i class="fas fa-envelope mr-1 text-primary"></i> Contactar
                                    </a>
                                @endif
                                @if ($persona && $persona->celular)
                                    <a
                                        class="btn btn-sm btn-light mx-1 mb-2"
                                        href="https://wa.me/{{ $persona->celular }}"
                                        target="_blank">
                                        <i class="fab fa-whatsapp mr-1 text-success"></i> WhatsApp
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    @if ($persona)
                        {{-- Datos Personales --}}
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white border-0">
                                <h5 class="card-title m-0 text-primary">
                                    <i class="fas fa-user mr-2"></i>Datos personales
                                </h5>
                            </div>
                            <div class="card-body pt-0">
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <span class="text-muted text-uppercase small d-block">Primer nombre</span>
                                        <p class="h6 mb-0">
                                            @if ($persona->primer_nombre)
                                                {{ $persona->primer_nombre }}
                                            @else
                                                <span class="badge badge-warning text-dark">Sin información</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <span class="text-muted text-uppercase small d-block">Segundo nombre</span>
                                        <p class="h6 mb-0">
                                            @if ($persona->segundo_nombre)
                                                {{ $persona->segundo_nombre }}
                                            @else
                                                <span class="badge badge-warning text-dark">Sin información</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <span class="text-muted text-uppercase small d-block">Primer apellido</span>
                                        <p class="h6 mb-0">
                                            @if ($persona->primer_apellido)
                                                {{ $persona->primer_apellido }}
                                            @else
                                                <span class="badge badge-warning text-dark">Sin información</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <span class="text-muted text-uppercase small d-block">Segundo apellido</span>
                                        <p class="h6 mb-0">
                                            @if ($persona->segundo_apellido)
                                                {{ $persona->segundo_apellido }}
                                            @else
                                                <span class="badge badge-warning text-dark">Sin información</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <span class="text-muted text-uppercase small d-block">Tipo de documento</span>
                                        <p class="h6 mb-0">
                                            @if ($persona->tipoDocumento)
                                                {{ $persona->tipoDocumento->name }}
                                            @else
                                                <span class="badge badge-warning text-dark">Sin información</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <span class="text-muted text-uppercase small d-block">Número de documento</span>
                                        <p class="h6 mb-0">
                                            @if ($persona->numero_documento)
                                                {{ $persona->numero_documento }}
                                            @else
                                                <span class="badge badge-warning text-dark">Sin información</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <span class="text-muted text-uppercase small d-block">Fecha de nacimiento</span>
                                        <p class="h6 mb-0">
                                            @if ($persona->fecha_nacimiento)
                                                {{ $persona->fecha_nacimiento }}
                                            @else
                                                <span class="badge badge-warning text-dark">Sin información</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <span class="text-muted text-uppercase small d-block">Edad</span>
                                        <p class="h6 mb-0">
                                            @if ($persona->edad)
                                                {{ $persona->edad }} AÑOS
                                            @else
                                                <span class="badge badge-warning text-dark">Sin información</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <span class="text-muted text-uppercase small d-block">Género</span>
                                        <p class="h6 mb-0">
                                            @if ($persona->tipoGenero)
                                                {{ $persona->tipoGenero->name }}
                                            @else
                                                <span class="badge badge-warning text-dark">Sin información</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Contacto --}}
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white border-0">
                                <h5 class="card-title m-0 text-primary">
                                    <i class="fas fa-address-book mr-2"></i>Contacto
                                </h5>
                            </div>
                            <div class="card-body pt-0">
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <span class="text-muted text-uppercase small d-block">Teléfono</span>
                                        <p class="h6 mb-0">
                                            @if ($persona->telefono)
                                                {{ $persona->telefono }}
                                            @else
                                                <span class="badge badge-warning text-dark">Sin información</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <span class="text-muted text-uppercase small d-block">Celular</span>
                                        <p class="h6 mb-0">
                                            @if ($persona->celular)
                                                <a href="https://wa.me/{{ $persona->celular }}" target="_blank"
                                                    class="text-decoration-none">
                                                    {{ $persona->celular }} <i class="fab fa-whatsapp text-success"></i>
                                                </a>
                                            @else
                                                <span class="badge badge-warning text-dark">Sin información</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-12 mb-0">
                                        <span class="text-muted text-uppercase small d-block">Correo electrónico</span>
                                        <p class="h6 mb-0">
                                            @if ($persona->email)
                                                <a href="mailto:{{ $persona->email }}" class="text-decoration-none">
                                                    {{ $persona->email }} <i class="fas fa-envelope text-primary"></i>
                                                </a>
                                            @else
                                                <span class="badge badge-warning text-dark">Sin información</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Información de Usuario --}}
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white border-0">
                            <h5 class="card-title m-0 text-primary">
                                <i class="fas fa-user-cog mr-2"></i>Información de cuenta
                            </h5>
                        </div>
                        <div class="card-body pt-0">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <span class="text-muted text-uppercase small d-block">Email</span>
                                    <p class="h6 mb-0">
                                        @if ($user->email)
                                            <a href="mailto:{{ $user->email }}" class="text-decoration-none">
                                                {{ $user->email }} <i class="fas fa-envelope text-primary"></i>
                                            </a>
                                        @else
                                            <span class="badge badge-warning text-dark">Sin información</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <span class="text-muted text-uppercase small d-block">Estado</span>
                                    <p class="h6 mb-0">
                                        <span
                                            class="badge badge-{{ $user->status === 1 ? 'success' : 'danger' }}
                                                px-3 py-2">
                                            <i class="fas fa-circle mr-1"></i>
                                            {{ $user->status === 1 ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <span class="text-muted text-uppercase small d-block">Fecha de registro</span>
                                    <p class="h6 mb-0">
                                        @if ($user->created_at)
                                            {{ $user->created_at->translatedFormat('d \d\e F \d\e Y') }}
                                        @else
                                            <span class="badge badge-warning text-dark">Sin información</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <span class="text-muted text-uppercase small d-block">Última actualización</span>
                                    <p class="h6 mb-0">
                                        @if ($user->updated_at)
                                            {{ $user->updated_at->diffForHumans() }}
                                        @else
                                            <span class="badge badge-warning text-dark">Sin información</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection
