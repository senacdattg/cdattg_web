@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
    <style>
        .dual-list-container {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .list-column {
            flex: 1;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
        }

        .list-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .list-title {
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .list-actions {
            display: flex;
            gap: 5px;
        }

        .list-content {
            height: 300px;
            overflow-y: auto;
            border: 1px solid #eee;
            border-radius: 4px;
        }

        .list-item {
            padding: 8px 12px;
            border-bottom: 1px solid #f5f5f5;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .list-item:hover {
            background-color: #f8f9fa;
        }

        .list-item.selected {
            background-color: #e3f2fd;
        }

        .list-item input[type="checkbox"] {
            margin: 0;
        }

        .transfer-buttons {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 10px;
        }

        .btn-transfer {
            padding: 8px 12px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-transfer:hover {
            background: #e9ecef;
        }

        .search-box {
            margin-bottom: 10px;
        }

        .search-box input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-cogs" 
        title="Permisos"
        subtitle="Gestión de permisos del sistema"
        :breadcrumb="[['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'], ['label' => 'Permisos', 'icon' => 'fa-cog', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <!-- Botón Volver -->
            <div class="mb-3">
                <a class="btn btn-sm btn-light" href="{{ route('permiso.index') }}" title="Volver">
                    <i class="fas fa-arrow-left text-secondary"></i> Volver
                </a>
            </div>

            <div class="row">
                <!-- Columna Izquierda: Perfil del Usuario -->
                <div class="col-md-3">
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <img class="profile-user-img img-fluid img-circle"
                                    src="{{ asset('dist/img/logoSena.png') }}" alt="User profile picture">
                            </div>
                            <h3 class="profile-username text-center">
                                {{ $user->persona->nombre_completo }}
                            </h3>
                            <!-- Mostrar roles -->
                            <p class="text-muted text-center">
                                {{ $user->getRoleNames()->implode(', ') }}
                            </p>
                            <p class="text-muted text-center">Información Básica</p>
                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b><i class="fas fa-id-card"></i> Tipo de documento:</b>
                                    <span class="float-right">{{ $user->persona->tipoDocumento->name }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b><i class="fas fa-file-alt"></i> Número de documento:</b>
                                    <span class="float-right">{{ $user->persona->numero_documento }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b><i class="fas fa-birthday-cake"></i> Fecha de nacimiento:</b>
                                    <span class="float-right">{{ $user->persona->fecha_nacimiento }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b><i class="fas fa-envelope"></i> Correo:</b>
                                    <span class="float-right">{{ $user->persona->email }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b><i class="fas fa-hourglass-half"></i> Edad:</b>
                                    <span class="float-right">{{ $user->persona->edad }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b><i class="fas fa-venus-mars"></i> Género:</b>
                                    <span class="float-right">{{ $user->persona->tipoGenero->name }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b><i class="fas fa-toggle-on"></i> Estado:</b>
                                    <span
                                        class="badge badge-{{ $user->persona->user->status === 1 ? 'success' : 'danger' }} float-right">
                                        {{ $user->status === 1 ? 'ACTIVO' : 'INACTIVO' }}
                                    </span>
                                </li>
                                @if ($user->persona->instructor && $user->persona->instructor->regional)
                                    <li class="list-group-item">
                                        <b><i class="fas fa-map-marker-alt"></i> Regional:</b>
                                        <span
                                            class="float-right">{{ $user->persona->instructor->regional->regional }}</span>
                                    </li>
                                @endif
                            </ul>
                            @if (auth()->user()->hasAnyRole(['ADMINISTRADOR', 'SUPER ADMINISTRADOR']))
                                <form action="{{ route('user.toggleStatus', $user->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-light"
                                        title="Cambiar estado de usuario">
                                        @if ($user->persona->user->status === 1)
                                            <i class="fas fa-user-slash text-danger"></i> Inactivar usuario
                                        @else
                                            <i class="fas fa-user-check text-success"></i> Activar usuario
                                        @endif
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha: Asignación de Permisos y Roles -->
                <div class="col-md-9">
                    <div class="card card-primary card-outline mb-4">
                        <div class="card-header">
                            <h4 class="card-title"><i class="fas fa-key"></i> Permisos de Usuario</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('permiso.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $user->id }}">

                                <div class="dual-list-container">
                                    <!-- Lista de Permisos Disponibles -->
                                    <div class="list-column">
                                        <div class="list-header">
                                            <h5 class="list-title">Permisos Disponibles</h5>
                                            <div class="list-actions">
                                                <button type="button" id="select-all-available-permissions"
                                                    class="btn btn-sm btn-light">
                                                    <i class="fas fa-check-double text-primary"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="search-box">
                                            <input type="text" class="form-control search-available"
                                                placeholder="Buscar permisos...">
                                        </div>
                                        <div class="list-content" id="available-permissions">
                                            @forelse ($permisos as $permiso)
                                                @if (!$user->hasPermissionTo($permiso))
                                                    <div class="list-item" data-value="{{ $permiso->name }}">
                                                        <input type="checkbox" name="available_permissions[]"
                                                            value="{{ $permiso->name }}">
                                                        <span>{{ $permiso->name }}</span>
                                                    </div>
                                                @endif
                                            @empty
                                                <div class="list-item">No hay permisos disponibles</div>
                                            @endforelse
                                        </div>
                                    </div>

                                    <!-- Botones de Transferencia -->
                                    <div class="transfer-buttons">
                                        <button type="button" id="move-right-permissions" class="btn-transfer move-right">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                        <button type="button" id="move-left-permissions" class="btn-transfer move-left">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                    </div>

                                    <!-- Lista de Permisos Asignados -->
                                    <div class="list-column">
                                        <div class="list-header">
                                            <h5 class="list-title">Permisos Asignados</h5>
                                            <div class="list-actions">
                                                <button type="button" id="select-all-assigned-permissions"
                                                    class="btn btn-sm btn-light">
                                                    <i class="fas fa-check-double text-primary"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="search-box">
                                            <input type="text" class="form-control search-assigned"
                                                placeholder="Buscar permisos...">
                                        </div>
                                        <div class="list-content" id="assigned-permissions">
                                            @forelse ($permisos as $permiso)
                                                @if ($user->hasPermissionTo($permiso))
                                                    <div class="list-item" data-value="{{ $permiso->name }}">
                                                        <input type="checkbox" name="permissions[]"
                                                            value="{{ $permiso->name }}" checked>
                                                        <span>{{ $permiso->name }}</span>
                                                    </div>
                                                @endif
                                            @empty
                                                <div class="list-item">No hay permisos asignados</div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center mt-3">
                                    <button type="submit" class="btn btn-sm btn-light">
                                        <i class="fas fa-save text-success"></i> Guardar Cambios
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    @if (auth()->user()->hasRole('SUPER ADMINISTRADOR'))
                        <div class="card card-info card-outline">
                            <div class="card-header">
                                <h4 class="card-title"><i class="fas fa-user-tag"></i> Asignación de Roles</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('user.assignRoles', ['user' => $user->id]) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">

                                    <div class="dual-list-container">
                                        <!-- Lista de Roles Disponibles -->
                                        <div class="list-column">
                                            <div class="list-header">
                                                <h5 class="list-title">Roles Disponibles</h5>
                                                <div class="list-actions">
                                                    <button type="button" id="select-all-available-roles"
                                                        class="btn btn-sm btn-light">
                                                        <i class="fas fa-check-double text-primary"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="search-box">
                                                <input type="text" class="form-control search-available-roles"
                                                    placeholder="Buscar roles...">
                                            </div>
                                            <div class="list-content" id="available-roles">
                                                @foreach ($roles as $role)
                                                    @if (!$user->hasRole($role->name))
                                                        <div class="list-item" data-value="{{ $role->name }}">
                                                            <input type="checkbox" name="available_roles[]"
                                                                value="{{ $role->name }}">
                                                            <span>{{ $role->name }}</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>

                                        <!-- Botones de Transferencia -->
                                        <div class="transfer-buttons">
                                            <button type="button" id="move-right-roles"
                                                class="btn-transfer move-right-roles">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                            <button type="button" id="move-left-roles"
                                                class="btn-transfer move-left-roles">
                                                <i class="fas fa-chevron-left"></i>
                                            </button>
                                        </div>

                                        <!-- Lista de Roles Asignados -->
                                        <div class="list-column">
                                            <div class="list-header">
                                                <h5 class="list-title">Roles Asignados</h5>
                                                <div class="list-actions">
                                                    <button type="button" id="select-all-assigned-roles"
                                                        class="btn btn-sm btn-light">
                                                        <i class="fas fa-check-double text-primary"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="search-box">
                                                <input type="text" class="form-control search-assigned-roles"
                                                    placeholder="Buscar roles...">
                                            </div>
                                            <div class="list-content" id="assigned-roles">
                                                @foreach ($roles as $role)
                                                    @if ($user->hasRole($role->name))
                                                        <div class="list-item" data-value="{{ $role->name }}">
                                                            <input type="checkbox" name="roles[]"
                                                                value="{{ $role->name }}" checked>
                                                            <span>{{ $role->name }}</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-center mt-3">
                                        <button type="submit" class="btn btn-sm btn-light">
                                            <i class="fas fa-save text-success"></i> Guardar Cambios
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div> <!-- Fin de row -->
        </div>
    </section>
@endsection

@section('footer')
    @include('layout.footer')
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/js/parametros.js'])
    @vite(['resources/js/pages/gestion-especializada.js'])
@endsection
