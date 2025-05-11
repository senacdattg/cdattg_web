@extends('adminlte::page')
@section('content')
    <!-- Encabezado de la Página -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Datos Personales</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home.index') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('personas.index') }}">Personas</a></li>
                        <li class="breadcrumb-item active">{{ $persona->nombre_completo }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Contenido Principal -->
    <section class="content">
        <div class="container-fluid">
            <div class="mb-3">
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('personas.index') }}" title="Volver">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <!-- Imagen de perfil: placeholder -->
                        <img class="profile-user-img img-fluid img-circle" src="https://picsum.photos/128/128"
                            alt="Foto de perfil">
                    </div>
                    <h3 class="profile-username text-center">{{ $persona->nombre_completo }}</h3>
                    <p class="text-muted text-center">{{ $persona->user->getRoleNames()->implode(', ') }}</p>

                    <li class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b><i class="fas fa-id-card"></i> Tipo de documento</b>
                        <span class="float-right">{{ $persona->tipoDocumento->name }}</span>
                    </li>
                    <li class="list-group-item">
                        <b><i class="fas fa-file-alt"></i> Número de documento</b>
                        <span class="float-right">{{ $persona->numero_documento }}</span>
                    </li>
                    <li class="list-group-item">
                        <b><i class="fas fa-birthday-cake"></i> Fecha de nacimiento</b>
                        <span class="float-right">{{ $persona->fecha_nacimiento }}</span>
                    </li>
                    <li class="list-group-item">
                        <b><i class="fas fa-hourglass-half"></i> Edad</b>
                        <span class="float-right">{{ $persona->edad }} Años</span>
                    </li>
                    <li class="list-group-item">
                        <b><i class="fas fa-venus-mars"></i> Género</b>
                        <span class="float-right">{{ $persona->tipoGenero->name }}</span>
                    </li>
                    <li class="list-group-item">
                        <b><i class="fas fa-phone"></i> Teléfono</b>
                        <span class="float-right">
                            @if ($persona->telefono)
                                {{ $persona->telefono }}
                            @else
                                <span class="text-muted">No disponible</span>
                            @endif
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b><i class="fas fa-mobile"></i> Celular</b>
                        <span class="float-right">
                            @if ($persona->celular)
                                <a href="https://wa.me/{{ $persona->celular }}" target="_blank"
                                    class="text-decoration-none">
                                    {{ $persona->celular }} <i class="fab fa-whatsapp text-success"></i>
                                </a>
                            @else
                                <span class="text-muted">No disponible</span>
                            @endif
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b><i class="fas fa-envelope"></i> Correo electrónico</b>
                        <span class="float-right">{{ $persona->email }}</span>
                    </li>
                    <li class="list-group-item">
                        <b><i class="fas fa-map-marker-alt"></i> Dirección</b>
                        <span class="float-right">
                            @if ($persona->direccion)
                                {{ $persona->direccion }}
                            @else
                                <span class="text-muted">No disponible</span>
                            @endif
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b><i class="fas fa-map"></i> Ubicación</b>
                        <span class="float-right">
                            @if ($persona->municipio && $persona->departamento && $persona->pais)
                                {{ $persona->municipio->municipio }},
                                {{ $persona->departamento->departamento }},
                                {{ $persona->pais->pais }}
                            @else
                                <span class="text-muted">No disponible</span>
                            @endif
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b><i class="fas fa-toggle-on"></i> Estado</b>
                        <span class="badge badge-{{ $persona->status === 1 ? 'success' : 'danger' }} float-right">
                            {{ $persona->status === 1 ? 'ACTIVO' : 'INACTIVO' }}
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b><i class="fas fa-user"></i> Usuario que crea</b>
                        <span class="float-right">{{ $persona->userCreatedBy->persona->nombre_completo }}</span>
                    </li>
                    <li class="list-group-item">
                        <b><i class="fas fa-calendar-alt"></i> Fecha de creación</b>
                        <span class="float-right">{{ $persona->created_at->diffForHumans() }}</span>
                    </li>
                    <li class="list-group-item">
                        <b><i class="fas fa-user-edit"></i> Usuario que modifica</b>
                        <span class="float-right">{{ $persona->userUpdatedBy->persona->nombre_completo }}</span>
                    </li>
                    <li class="list-group-item">
                        <b><i class="fas fa-calendar-check"></i>
                            Última modificación</b><span
                            class="float-right">{{ $persona->updated_at->diffForHumans() }}</span>
                    </li>
                </div>
            </div>
        </div>
    </section>
    </div>
@endsection
