@extends('inventario.layouts.base')

@section('title', 'Registrar Categoría')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush

@section('content_header')
    <x-page-header
        icon="fas fa-plus"
        title="Registrar Categoría"
        subtitle="Crear una nueva categoría en el inventario"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Categorías', 'url' => route('inventario.categorias.index')],
            ['label' => 'Registrar', 'active' => true]
        ]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <!-- Alertas -->
            @include('components.session-alerts')

            <div class="row">
                <div class="col-12">
                    <div class="card detail-card no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-info-circle mr-2"></i>
                                Información de la Categoría
                            </h5>
                        </div>

                        <div class="card-body">
                            <form action="{{ route('inventario.categorias.store') }}" method="POST">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Nombre de la Categoría <span class="text-danger">*</span></label>
                                            <input
                                                type="text"
                                                class="form-control @error('name') is-invalid @enderror"
                                                id="name"
                                                name="name"
                                                value="{{ old('name') }}"
                                                placeholder="Ingrese el nombre de la categoría"
                                                required
                                            >
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="status">Estado</label>
                                            <select
                                                class="form-control @error('status') is-invalid @enderror"
                                                id="status"
                                                name="status"
                                            >
                                                <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Activa</option>
                                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactiva</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="descripcion">Descripción</label>
                                            <textarea
                                                class="form-control @error('descripcion') is-invalid @enderror"
                                                id="descripcion"
                                                name="descripcion"
                                                rows="3"
                                                placeholder="Ingrese una descripción de la categoría (opcional)"
                                            >{{ old('descripcion') }}</textarea>
                                            @error('descripcion')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="card-footer bg-white py-3">
                                            <div class="action-buttons">
                                                <button type="submit" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-save mr-1"></i> Guardar
                                                </button>
                                                <a href="{{ route('inventario.categorias.index') }}" class="btn btn-outline-secondary btn-sm">
                                                    <i class="fas fa-times mr-1"></i> Cancelar
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
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


@extends('inventario.layouts.base')

@section('title', 'Editar Categoría')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush

@section('content_header')
    <x-page-header
        icon="fas fa-edit"
        title="Editar Categoría"
        subtitle="Modificar datos de la categoría"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Categorías', 'url' => route('inventario.categorias.index')],
            ['label' => 'Editar', 'active' => true]
        ]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <!-- Alertas -->
            @include('components.session-alerts')

            <div class="row">
                <div class="col-12">
                    <div class="card detail-card no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-info-circle mr-2"></i>
                                Información de la Categoría
                            </h5>
                        </div>

                        <div class="card-body">
                            <form action="{{ route('inventario.categorias.update', $categoria->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Nombre de la Categoría <span class="text-danger">*</span></label>
                                            <input
                                                type="text"
                                                class="form-control @error('name') is-invalid @enderror"
                                                id="name"
                                                name="name"
                                                value="{{ old('name', $categoria->name) }}"
                                                placeholder="Ingrese el nombre de la categoría"
                                                required
                                            >
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="status">Estado</label>
                                            <select
                                                class="form-control @error('status') is-invalid @enderror"
                                                id="status"
                                                name="status"
                                            >
                                                <option value="1" {{ old('status', $categoria->status) == '1' ? 'selected' : '' }}>Activa</option>
                                                <option value="0" {{ old('status', $categoria->status) == '0' ? 'selected' : '' }}>Inactiva</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="descripcion">Descripción</label>
                                            <textarea
                                                class="form-control @error('descripcion') is-invalid @enderror"
                                                id="descripcion"
                                                name="descripcion"
                                                rows="3"
                                                placeholder="Ingrese una descripción de la categoría (opcional)"
                                            >{{ old('descripcion', $categoria->descripcion) }}</textarea>
                                            @error('descripcion')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="card-footer bg-white py-3">
                                            <div class="action-buttons">
                                                <button type="submit" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-save mr-1"></i> Guardar Cambios
                                                </button>
                                                <a href="{{ route('inventario.categorias.index') }}" class="btn btn-outline-secondary btn-sm">
                                                    <i class="fas fa-times mr-1"></i> Cancelar
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
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



@extends('inventario.layouts.base')

@section('title', 'Gestión de Categorías')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush

@section('content_header')
    <x-page-header
        icon="fas fa-tags"
        title="Gestión de Categorías"
        subtitle="Administra las categorías del inventario"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Categorías', 'active' => true]
        ]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <x-create-card
                        url="{{ route('inventario.categorias.create') }}"
                        title="Nueva Categoría"
                        icon="fa-plus-circle"
                        permission="CREAR CATEGORIA"
                    />

                    <x-data-table
                        title="Lista de Categorías"
                        searchable="true"
                        searchAction="{{ route('inventario.categorias.index') }}"
                        searchPlaceholder="Buscar categoría..."
                        searchValue="{{ request('search') }}"
                        :columns="[
                            ['label' => '#', 'width' => '5%'],
                            ['label' => 'Nombre', 'width' => '40%'],
                            ['label' => 'Productos', 'width' => '15%'],
                            ['label' => 'Estado', 'width' => '15%'],
                            ['label' => 'Opciones', 'width' => '25%', 'class' => 'text-center']
                        ]"
                        :pagination="$categorias->links()"
                    >
                        @forelse ($categorias as $categoria)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $categoria->name }}</td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ $categoria->productos_count ?? 0 }}
                                    </span>
                                </td>
                                <td>
                                    <x-status-badge
                                        status="{{ $categoria->status ?? true }}"
                                        activeText="ACTIVA"
                                        inactiveText="INACTIVA"
                                    />
                                </td>
                                <td class="text-center">
                                    <x-action-buttons
                                        show="true"
                                        edit="true"
                                        delete="true"
                                        showUrl="{{ route('inventario.categorias.show', $categoria->id) }}"
                                        editUrl="{{ route('inventario.categorias.edit', $categoria->id) }}"
                                        deleteUrl="{{ route('inventario.categorias.destroy', $categoria->id) }}"
                                        showTitle="Ver categoría"
                                        editTitle="Editar categoría"
                                        deleteTitle="Eliminar categoría"
                                    />
                                </td>
                            </tr>
                        @empty
                            <x-table-empty
                                colspan="5"
                                message="No hay categorías registradas"
                                icon="fas fa-tags"
                            />
                        @endforelse
                    </x-data-table>
                    <div class="float-left pt-2">
                        <small class="text-muted">
                            Mostrando {{ $categorias->firstItem() ?? 0 }} a {{ $categorias->lastItem() ?? 0 }}
                            de {{ $categorias->total() }} categorías
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Modal de confirmación de eliminación --}}
    <x-confirm-delete-modal />
    
    {{-- Alertas --}}
    {{-- Notificaciones manejadas globalmente por sweetalert2-notifications --}}
    
    {{-- Footer SENA --}}
    @include('layouts.footer')
@endsection

@push('css')
    @vite([
        'resources/css/inventario/shared/base.css',
    ])
@endpush

@push('scripts')
    @vite(['resources/js/pages/formularios-generico.js'])
@endpush


@extends('inventario.layouts.base')

@section('title', 'Ver Categoría')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush

@section('content_header')
    <x-page-header
        icon="fas fa-eye"
        title="Ver Categoría"
        subtitle="Información detallada de la categoría"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Categorías', 'url' => route('inventario.categorias.index')],
            ['label' => $categoria->name, 'active' => true]
        ]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <!-- Alertas -->
            @include('components.session-alerts')

            <!-- Botón Volver -->
            <div class="mb-3">
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('inventario.categorias.index') }}">
                    <i class="fas fa-arrow-left mr-1" id="icono-volver"></i> Volver
                </a>
            </div>

            <div class="row" id="estadisticas-generales">
                <!-- Estadísticas Generales -->
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="stats-number">{{ $categoria->productos_count ?? 0 }}</div>
                        <div class="stats-label">
                            <i class="fas fa-boxes mr-1"></i>
                            Productos
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="stats-number">{{ $categoria->status == 1 ? 'Activa' : 'Inactiva' }}</div>
                        <div class="stats-label">
                            <i class="fas fa-toggle-on mr-1"></i>
                            Estado
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="stats-number">{{ \Carbon\Carbon::parse($categoria->created_at)->diffForHumans() }}</div>
                        <div class="stats-label">
                            <i class="fas fa-clock mr-1"></i>
                            Antigüedad
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información de la Categoría -->
            <div class="row mb-4" id="detalle">
                <div class="col-12">
                    <div class="card detail-card no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-info-circle mr-2"></i>Información de la Categoría
                            </h5>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table detail-table mb-0">
                                    <caption id="categoria-description" class="sr-only">
                                        Lista de categorías con información de nombre, estado, descripción, 
                                        total de productos y fechas de creación y actualización.
                                    </caption>
                                    <tbody>
                                        <tr>
                                            <th class="py-3">Nombre</th>
                                            <td class="py-3">
                                                <strong>{{ $categoria->name }}</strong>
                                                <br><small class="text-muted">Categoría registrada</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Estado</th>
                                            <td class="py-3">
                                                <x-status-badge
                                                    status="{{ $categoria->status ?? true }}"
                                                    activeText="ACTIVA"
                                                    inactiveText="INACTIVA"
                                                />
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Descripción</th>
                                            <td class="py-3">{{ $categoria->descripcion ?? 'Sin descripción' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Total de Productos</th>
                                            <td class="py-3">
                                                <span class="status-badge status-active">
                                                    <i class="fas fa-boxes mr-1"></i>
                                                    {{ $categoria->productos_count ?? 0 }} producto(s)
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha de Creación</th>
                                            <td class="py-3">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ \Carbon\Carbon::parse($categoria->created_at)->format('d/m/Y H:i') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Última Actualización</th>
                                            <td class="py-3">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ \Carbon\Carbon::parse($categoria->updated_at)->format('d/m/Y H:i') }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="row">
                <div class="col-12">
                    <div class="card detail-card no-hover">
                        <div class="card-footer bg-white py-3">
                            <div class="action-buttons">
                                @can('EDITAR CATEGORIA')
                                    <a 
                                        href="{{ route('inventario.categorias.edit', $categoria->id) }}" 
                                        class="btn btn-outline-info btn-sm"
                                    >
                                        <i class="fas fa-pencil-alt mr-1"></i> Editar
                                    </a>
                                @endcan

                                @can('ELIMINAR CATEGORIA')
                                    <form 
                                        action="{{ route('inventario.categorias.destroy', $categoria->id) }}"
                                        method="POST" 
                                        class="d-inline formulario-eliminar"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-trash mr-1"></i> Eliminar
                                        </button>
                                    </form>
                                @endcan
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


