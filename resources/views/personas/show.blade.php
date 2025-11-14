@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    @if (!isset($soloPerfil) || !$soloPerfil)
        <x-page-header icon="fa-cogs" title="Personas" subtitle="Gestión de personas del sistema" :breadcrumb="[
            ['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'],
            ['label' => 'Personas', 'url' => route('personas.index'), 'icon' => 'fa-cog'],
            ['label' => $persona->nombre_completo, 'icon' => 'fa-user', 'active' => true],
        ]" />
    @else
        <x-page-header icon="fa-user" title="Mi Perfil" subtitle="Información personal" :breadcrumb="[
            ['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'],
            ['label' => 'Mi Perfil', 'icon' => 'fa-user', 'active' => true],
        ]" />
    @endif
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    @if (!isset($soloPerfil) || !$soloPerfil)
                        <a class="btn btn-outline-secondary" href="{{ route('personas.index') }}">
                            <i class="fas fa-arrow-left mr-1"></i> Volver
                        </a>
                    @else
                        <a class="btn btn-outline-secondary" href="{{ route('verificarLogin') }}">
                            <i class="fas fa-arrow-left mr-1"></i> Volver al inicio
                        </a>
                    @endif
                </div>
                <div>
                    @can('EDITAR PERSONA')
                        <a class="btn btn-primary" href="{{ route('personas.edit', $persona->id) }}">
                            <i class="fas fa-edit mr-1"></i> Editar
                        </a>
                    @endcan
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body box-profile d-flex flex-column align-items-center text-center">
                            <img class="profile-user-img img-fluid img-circle mb-3" src="https://picsum.photos/400/400"
                                alt="Foto de perfil" style="width: 160px; height: 160px; object-fit: cover;">
                            <h3 class="profile-username font-weight-bold">{{ $persona->nombre_completo }}</h3>

                            @php
                                $rolesPersona = ($rolesAsignados ?? collect())->sort()->values();
                            @endphp
                            <div class="mb-3">
                                @if ($rolesPersona->isNotEmpty())
                                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                                        @foreach ($rolesPersona as $rol)
                                            <span class="badge badge-info text-white px-3 py-2 mr-2 mb-2">
                                                <i class="fas fa-user-tag mr-1"></i>{{ $rol }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="badge badge-warning text-dark px-3 py-2">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>Sin roles asignados
                                    </span>
                                @endif
                            </div>

                            <span
                                class="badge badge-{{ $persona->status === 1 ? 'success' : 'danger' }}
                                px-3 py-2 mb-3">
                                <i class="fas fa-circle mr-1"></i>
                                {{ $persona->status === 1 ? 'Activo' : 'Inactivo' }}
                            </span>

                            <div class="d-flex flex-wrap justify-content-center w-100">
                                @if ($persona->email)
                                    <a class="btn btn-sm btn-light mx-1 mb-2" href="mailto:{{ $persona->email }}">
                                        <i class="fas fa-envelope mr-1 text-primary"></i> Contactar
                                    </a>
                                @endif
                                @if ($persona->celular)
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
                    @php
                        $estadoLabel = $persona->status === 1 ? 'Activo' : 'Inactivo';
                        $estadoBadgeClass = $persona->status === 1 ? 'badge-success' : 'badge-danger';
                        $estadoSofiaLabel = $persona->estado_sofia_label ?? 'Sin información';
                        $estadoSofiaBadgeClass = $persona->estado_sofia_badge_class ?? 'bg-secondary';
                    @endphp

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
                                <div class="col-md-12 mb-0">
                                    <span class="text-muted text-uppercase small d-block mb-2">
                                        Caracterización
                                    </span>
                                    <div class="d-flex flex-wrap gap-2">
                                        @if ($persona->caracterizacionesComplementarias->isNotEmpty())
                                            @foreach ($persona->caracterizacionesComplementarias as $categoria)
                                                <span class="badge badge-info text-white px-3 py-2 mr-1 mb-1">
                                                    <i class="fas fa-tag mr-1"></i>{{ $categoria->name }}
                                                </span>
                                            @endforeach
                                        @elseif ($persona->caracterizacion)
                                            <span class="badge badge-info text-white px-3 py-2">
                                                <i class="fas fa-tag mr-1"></i>{{ $persona->caracterizacion->nombre }}
                                            </span>
                                        @else
                                            <span class="badge badge-warning text-dark px-3 py-2">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                Sin caracterización asignada
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

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

                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white border-0">
                            <h5 class="card-title m-0 text-primary">
                                <i class="fas fa-map-marked-alt mr-2"></i>Ubicación
                            </h5>
                        </div>
                        <div class="card-body pt-0">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <span class="text-muted text-uppercase small d-block">Dirección</span>
                                    <p class="h6 mb-0">
                                        @if ($persona->direccion)
                                            {{ $persona->direccion }}
                                        @else
                                            <span class="badge badge-warning text-dark">Sin información</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <span class="text-muted text-uppercase small d-block">Municipio</span>
                                    <p class="h6 mb-0">
                                        @if ($persona->municipio)
                                            {{ $persona->municipio->municipio }}
                                        @else
                                            <span class="badge badge-warning text-dark">Sin información</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <span class="text-muted text-uppercase small d-block">Departamento</span>
                                    <p class="h6 mb-0">
                                        @if ($persona->departamento)
                                            {{ $persona->departamento->departamento }}
                                        @else
                                            <span class="badge badge-warning text-dark">Sin información</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-0">
                                    <span class="text-muted text-uppercase small d-block">País</span>
                                    <p class="h6 mb-0">
                                        @if ($persona->pais)
                                            {{ $persona->pais->pais }}
                                        @else
                                            <span class="badge badge-warning text-dark">Sin información</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if (!isset($soloPerfil) || !$soloPerfil)
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white border-0">
                                <h5 class="card-title m-0 text-primary">
                                    <i class="fas fa-clipboard-list mr-2"></i>Auditoría
                                </h5>
                            </div>
                            <div class="card-body pt-0">
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <span class="text-muted text-uppercase small d-block">Usuario que crea</span>
                                        <p class="h6 mb-0">
                                            @if ($persona->userCreatedBy && $persona->userCreatedBy->persona)
                                                {{ $persona->userCreatedBy->persona->nombre_completo }}
                                            @else
                                                <span class="badge badge-warning text-dark">Sin información</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <span class="text-muted text-uppercase small d-block">Fecha de creación</span>
                                        <p class="h6 mb-0">
                                            @if ($persona->created_at)
                                                {{ $persona->created_at->diffForHumans() }}
                                            @else
                                                <span class="badge badge-warning text-dark">Sin información</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <span class="text-muted text-uppercase small d-block">
                                            Usuario que modifica
                                        </span>
                                        <p class="h6 mb-0">
                                            @if ($persona->userUpdatedBy && $persona->userUpdatedBy->persona)
                                                {{ $persona->userUpdatedBy->persona->nombre_completo }}
                                            @else
                                                <span class="badge badge-warning text-dark">Sin información</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-0">
                                        <span class="text-muted text-uppercase small d-block">Última modificación</span>
                                        <p class="h6 mb-0">
                                            @if ($persona->updated_at)
                                                {{ $persona->updated_at->diffForHumans() }}
                                            @else
                                                <span class="badge badge-warning text-dark">Sin información</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <span class="text-muted text-uppercase small d-block">Estado del usuario</span>
                                        <p class="h6 mb-0">
                                            <span class="badge {{ $estadoBadgeClass }}">{{ $estadoLabel }}</span>
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-0">
                                        <span class="text-muted text-uppercase small d-block">Registro en SOFIA</span>
                                        <p class="h6 mb-0">
                                            <span class="badge {{ $estadoSofiaBadgeClass }} text-white">
                                                {{ $estadoSofiaLabel }}
                                            </span>
                                        </p>
                                    </div>
                                @can('ASIGNAR PERMISOS')
                                    <div class="col-12">
                                        <hr class="mt-4 mb-4">
                                        @php
                                            $rolesActuales = isset($rolesAsignados) ? $rolesAsignados : collect();
                                        @endphp
                                            @if ($persona->user)
                                            <form method="POST" action="{{ route('personas.update-role', $persona) }}">
                                                @csrf
                                                @method('PATCH')
                                                <div class="form-group mb-3">
                                                    <label class="small text-muted text-uppercase d-block mb-2">Seleccionar roles</label>
                                                    <div class="row">
                                                        @forelse ($rolesDisponibles as $rol)
                                                            @php
                                                                $inputId = 'role-' . \Illuminate\Support\Str::slug($rol->name);
                                                                $asignado = $rolesActuales->contains($rol->name);
                                                            @endphp
                                                            <div class="col-md-6 mb-2">
                                                                <div class="custom-control custom-checkbox">
                                                                    <input
                                                                        type="checkbox"
                                                                        class="custom-control-input"
                                                                        id="{{ $inputId }}"
                                                                        name="roles[]"
                                                                        value="{{ $rol->name }}"
                                                                        @if ($asignado) checked @endif
                                                                    >
                                                                    <label class="custom-control-label" for="{{ $inputId }}">
                                                                        {{ $rol->name }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <div class="col-12">
                                                                <div class="alert alert-warning mb-0">
                                                                    No hay roles disponibles en el sistema.
                                                                </div>
                                                            </div>
                                                        @endforelse
                                                    </div>
                                                    @error('roles')
                                                        <div class="invalid-feedback d-block">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                    @error('roles.*')
                                                        <div class="invalid-feedback d-block">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                                <div class="d-flex justify-content-end">
                                                    <button type="submit" class="btn btn-primary" @if ($rolesDisponibles->isEmpty()) disabled @endif>
                                                        <i class="fas fa-user-edit mr-1"></i>Actualizar roles
                                                    </button>
                                                </div>
                                            </form>
                                        @else
                                            <div class="alert alert-warning mb-0">
                                                La persona no tiene usuario asociado.
                                            </div>
                                        @endif
                                    </div>
                                @endcan
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footer')
    @include('layout.footer')
@endsection

@section('js')
    @vite(['resources/js/parametros.js'])
@endsection
