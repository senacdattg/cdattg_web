@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <x-page-header icon="fa-cogs" title="Personas" subtitle="Gestión de personas del sistema" :breadcrumb="[
        ['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'],
        ['label' => 'Personas', 'url' => route('personas.index'), 'icon' => 'fa-cog'],
        ['label' => $persona->nombre_completo, 'icon' => 'fa-user', 'active' => true],
    ]" />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <a class="btn btn-outline-secondary" href="{{ route('personas.index') }}">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>
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
                                $rolesPersona = $persona->user?->getRoleNames();
                            @endphp
                            <p class="text-muted mb-3">
                                {{ $rolesPersona && $rolesPersona->isNotEmpty() ? $rolesPersona->implode(', ') : 'Sin roles asignados' }}
                            </p>

                            <span class="badge badge-{{ $persona->status === 1 ? 'success' : 'danger' }} px-3 py-2 mb-3">
                                <i class="fas fa-circle mr-1"></i>{{ $persona->status === 1 ? 'Activo' : 'Inactivo' }}
                            </span>

                            <div class="d-flex flex-wrap justify-content-center w-100">
                                @if ($persona->email)
                                    <a class="btn btn-sm btn-light mx-1 mb-2" href="mailto:{{ $persona->email }}">
                                        <i class="fas fa-envelope mr-1 text-primary"></i> Contactar
                                    </a>
                                @endif
                                @if ($persona->celular)
                                    <a class="btn btn-sm btn-light mx-1 mb-2" href="https://wa.me/{{ $persona->celular }}"
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
                                            {{ $persona->edad }} años
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
                                <div class="col-md-6 mb-0">
                                    <span class="text-muted text-uppercase small d-block">Caracterización</span>
                                    <p class="h6 mb-0">
                                        @if ($persona->caracterizacion)
                                            {{ $persona->caracterizacion->nombre }}
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
                                    <span class="text-muted text-uppercase small d-block">Usuario que modifica</span>
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
                                        <span
                                            class="badge {{ $estadoSofiaBadgeClass }} text-white">{{ $estadoSofiaLabel }}</span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-0">
                                    <span class="text-muted text-uppercase small d-block">Caracterización</span>
                                    <p class="h6 mb-0">
                                        @if ($persona->caracterizacion)
                                            {{ $persona->caracterizacion->nombre }}
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
    @include('layout.footer')
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/js/parametros.js'])
@endsection
