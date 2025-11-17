@extends('inventario.layouts.base')

@section('title', 'Registrar Proveedor')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush

@section('content_header')
    <x-page-header
        icon="fas fa-plus"
        title="Registrar Proveedor"
        subtitle="Crear un nuevo proveedor en el inventario"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Proveedores', 'url' => route('inventario.proveedores.index')],
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
                                Información del Proveedor
                            </h5>
                        </div>

                        <div class="card-body">
                            <form action="{{ route('inventario.proveedores.store') }}" method="POST">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="proveedor">Nombre del Proveedor <span class="text-danger">*</span></label>
                                            <input 
                                                type="text" 
                                                class="form-control @error('proveedor') is-invalid @enderror" 
                                                id="proveedor" 
                                                name="proveedor" 
                                                value="{{ old('proveedor') }}" 
                                                placeholder="Ingrese el nombre del proveedor"
                                                required
                                            >
                                            @error('proveedor')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nit">NIT</label>
                                            <input 
                                                type="text" 
                                                class="form-control @error('nit') is-invalid @enderror" 
                                                id="nit" 
                                                name="nit" 
                                                value="{{ old('nit') }}" 
                                                placeholder="Ingrese el NIT del proveedor"
                                            >
                                            @error('nit')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Correo Electrónico</label>
                                            <input 
                                                type="email" 
                                                class="form-control @error('email') is-invalid @enderror" 
                                                id="email" 
                                                name="email" 
                                                value="{{ old('email') }}" 
                                                placeholder="Ingrese el correo electrónico"
                                            >
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="telefono">Teléfono</label>
                                            <input 
                                                type="text" 
                                                class="form-control @error('telefono') is-invalid @enderror" 
                                                id="telefono" 
                                                name="telefono" 
                                                value="{{ old('telefono') }}" 
                                                placeholder="Ingrese el teléfono"
                                            >
                                            @error('telefono')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="direccion">Dirección</label>
                                            <input
                                                type="text"
                                                class="form-control @error('direccion') is-invalid @enderror"
                                                id="direccion"
                                                name="direccion"
                                                value="{{ old('direccion') }}"
                                                placeholder="Ingrese la dirección"
                                            >
                                            @error('direccion')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    {{-- Componente de filtro departamento-municipio --}}
                                    @include('inventario._components.filtro-departamento', [
                                        'departamentos' => $departamentos,
                                        'municipios' => $municipios
                                    ])
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="contacto">Contacto</label>
                                            <input
                                                type="text"
                                                class="form-control @error('contacto') is-invalid @enderror"
                                                id="contacto"
                                                name="contacto"
                                                value="{{ old('contacto') }}"
                                                placeholder="Ingrese el nombre del contacto"
                                            >
                                            @error('contacto')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="estado_id">Estado</label>
                                            <select
                                                class="form-control @error('estado_id') is-invalid @enderror"
                                                id="estado_id"
                                                name="estado_id"
                                            >
                                                <option value="">Seleccione un estado</option>
                                                @foreach(
                                                    \App\Models\ParametroTema::with(['parametro','tema'])
                                                        ->whereHas('tema', fn($q) => $q->where('name', 'ESTADOS'))
                                                        ->where('status', 1)
                                                        ->get() as $estado
                                                )
                                                    <option value="{{ $estado->id }}" {{ old('estado_id') == $estado->id ? 'selected' : '' }}>
                                                        {{ $estado->parametro->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('estado_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="observaciones">Observaciones</label>
                                            <textarea 
                                                class="form-control @error('observaciones') is-invalid @enderror" 
                                                id="observaciones" 
                                                name="observaciones" 
                                                rows="3" 
                                                placeholder="Ingrese observaciones sobre el proveedor (opcional)"
                                            >{{ old('observaciones') }}</textarea>
                                            @error('observaciones')
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
                                                <a href="{{ route('inventario.proveedores.index') }}" class="btn btn-outline-secondary btn-sm">
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

@vite(['resources/js/inventario/filtro-departamento.js'])
<script>
    // Pasar datos de municipios al JavaScript
    window.municipiosData = @json($municipios->map(function($m) {
        return [
            'id' => $m->id,
            'municipio' => $m->municipio,
            'departamento' => $m->departamento->departamento ?? ''
        ];
    }));

    // Inicializar filtro con el municipio seleccionado
    document.addEventListener('DOMContentLoaded', function() {
        initFiltroMunicipios({{ json_encode(old('municipio_id')) }});
    });
</script>


@extends('inventario.layouts.base')

@section('title', 'Editar Proveedor')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush

@section('content_header')
    <x-page-header
        icon="fas fa-edit"
        title="Editar Proveedor"
        subtitle="Modificar datos del proveedor"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Proveedores', 'url' => route('inventario.proveedores.index')],
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
                                Información del Proveedor
                            </h5>
                        </div>

                        <div class="card-body">
                            <form action="{{ route('inventario.proveedores.update', $proveedor->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="proveedor">Nombre del Proveedor <span class="text-danger">*</span></label>
                                            <input 
                                                type="text" 
                                                class="form-control @error('proveedor') is-invalid @enderror" 
                                                id="proveedor" 
                                                name="proveedor" 
                                                value="{{ old('proveedor', $proveedor->proveedor) }}" 
                                                placeholder="Ingrese el nombre del proveedor"
                                                required
                                            >
                                            @error('proveedor')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nit">NIT</label>
                                            <input 
                                                type="text" 
                                                class="form-control @error('nit') is-invalid @enderror" 
                                                id="nit" 
                                                name="nit" 
                                                value="{{ old('nit', $proveedor->nit) }}" 
                                                placeholder="Ingrese el NIT del proveedor"
                                            >
                                            @error('nit')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Correo Electrónico</label>
                                            <input 
                                                type="email" 
                                                class="form-control @error('email') is-invalid @enderror" 
                                                id="email" 
                                                name="email" 
                                                value="{{ old('email', $proveedor->email) }}" 
                                                placeholder="Ingrese el correo electrónico"
                                            >
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="telefono">Teléfono</label>
                                            <input 
                                                type="text" 
                                                class="form-control @error('telefono') is-invalid @enderror" 
                                                id="telefono" 
                                                name="telefono" 
                                                value="{{ old('telefono', $proveedor->telefono) }}" 
                                                placeholder="Ingrese el teléfono"
                                            >
                                            @error('telefono')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="direccion">Dirección</label>
                                            <input
                                                type="text"
                                                class="form-control @error('direccion') is-invalid @enderror"
                                                id="direccion"
                                                name="direccion"
                                                value="{{ old('direccion', $proveedor->direccion) }}"
                                                placeholder="Ingrese la dirección"
                                            >
                                            @error('direccion')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                                                        </div>
                                    {{-- Componente de filtro departamento-municipio --}}
                                    @include('inventario._components.filtro-departamento', [
                                        'departamentos' => $departamentos,
                                        'municipios' => $municipios,
                                        'municipioSeleccionado' => $proveedor->municipio_id,
                                        'departamentoSeleccionado' => $proveedor->departamento_id
                                    ])
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="contacto">Contacto</label>
                                            <input
                                                type="text"
                                                class="form-control @error('contacto') is-invalid @enderror"
                                                id="contacto"
                                                name="contacto"
                                                value="{{ old('contacto', $proveedor->contacto) }}"
                                                placeholder="Ingrese el nombre del contacto"
                                            >
                                            @error('contacto')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="estado_id">Estado</label>
                                            <select
                                                class="form-control @error('estado_id') is-invalid @enderror"
                                                id="estado_id"
                                                name="estado_id"
                                            >
                                                <option value="">Seleccione un estado</option>
                                                @foreach(
                                                    \App\Models\ParametroTema::with(['parametro','tema'])
                                                        ->whereHas('tema', fn($q) => $q->where('name', 'ESTADOS'))
                                                        ->where('status', 1)
                                                        ->get() as $estado
                                                )
                                                    <option value="{{ $estado->id }}" {{ old('estado_id', $proveedor->estado_id) == $estado->id ? 'selected' : '' }}>
                                                        {{ $estado->parametro->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('estado_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="observaciones">Observaciones</label>
                                            <textarea 
                                                class="form-control @error('observaciones') is-invalid @enderror" 
                                                id="observaciones" 
                                                name="observaciones" 
                                                rows="3" 
                                                placeholder="Ingrese observaciones sobre el proveedor (opcional)"
                                            >{{ old('observaciones', $proveedor->observaciones) }}</textarea>
                                            @error('observaciones')
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
                                                <a href="{{ route('inventario.proveedores.index') }}" class="btn btn-outline-secondary btn-sm">
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

@push('scripts')
    @vite(['resources/js/inventario/filtro-departamento.js'])
@endpush
<script>
    // Pasar datos de municipios al JavaScript
    window.municipiosData = @json($municipios->map(function($m) {
        return [
            'id' => $m->id,
            'municipio' => $m->municipio,
            'departamento' => $m->departamento->departamento ?? ''
        ];
    }));

    // Inicializar filtro con el municipio seleccionado
    document.addEventListener('DOMContentLoaded', function() {
        initFiltroMunicipios({{ json_encode(old('municipio_id', $proveedor->municipio_id)) }});
    });
</script>


@extends('inventario.layouts.base')

@section('title', 'Gestión de Proveedores')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush

@section('content_header')
    <x-page-header
        icon="fas fa-truck"
        title="Gestión de Proveedores"
        subtitle="Administra los proveedores del inventario"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Proveedores', 'active' => true]
        ]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <x-create-card
                        url="{{ route('inventario.proveedores.create') }}"
                        title="Nuevo Proveedor"
                        icon="fa-plus-circle"
                        permission="CREAR PROVEEDOR"
                    />

                    <x-data-table
                        title="Lista de Proveedores"
                        searchable="true"
                        searchAction="{{ route('inventario.proveedores.index') }}"
                        searchPlaceholder="Buscar proveedor..."
                        searchValue="{{ request('search') }}"
                        :columns="[
                            ['label' => '#', 'width' => '3%'],
                            ['label' => 'Proveedor', 'width' => '12%'],
                            ['label' => 'NIT', 'width' => '8%'],
                            ['label' => 'Email', 'width' => '12%'],
                            ['label' => 'Teléfono', 'width' => '8%'],
                            ['label' => 'Dirección', 'width' => '12%'],
                            ['label' => 'Departamento', 'width' => '9%'],
                            ['label' => 'Municipio', 'width' => '10%'],
                            ['label' => 'Contacto', 'width' => '10%'],
                            ['label' => 'Contratos', 'width' => '6%'],
                            ['label' => 'Estado', 'width' => '8%'],
                            ['label' => 'Opciones', 'width' => '11%', 'class' => 'text-center']
                        ]"
                        :pagination="$proveedores->links()"
                    >
                        @forelse ($proveedores as $proveedor)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $proveedor->proveedor }}</td>
                                <td>{{ $proveedor->nit ?? 'N/A' }}</td>
                                <td>{{ $proveedor->email ?? 'N/A' }}</td>
                                <td>{{ $proveedor->telefono ?? 'N/A' }}</td>
                                <td>{{ $proveedor->direccion ?? 'N/A' }}</td>
                                <td>{{ $proveedor->departamento->departamento ?? 'N/A' }}</td>
                                <td>{{ $proveedor->municipio->municipio ?? 'N/A' }}</td>
                                <td>{{ $proveedor->contacto ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ $proveedor->contratos_convenios_count ?? 0 }}
                                    </span>
                                </td>
                                <td>
                                    @if($proveedor->estado)
                                        <span class="badge badge-{{ $proveedor->estado->status == 1 ? 'success' : 'danger' }}">
                                            {{ $proveedor->estado->parametro->name ?? 'N/A' }}
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">SIN ESTADO</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <x-action-buttons
                                        show="true"
                                        edit="true"
                                        delete="true"
                                        showUrl="{{ route('inventario.proveedores.show', $proveedor->id) }}"
                                        editUrl="{{ route('inventario.proveedores.edit', $proveedor->id) }}"
                                        deleteUrl="{{ route('inventario.proveedores.destroy', $proveedor->id) }}"
                                        showTitle="Ver proveedor"
                                        editTitle="Editar proveedor"
                                        deleteTitle="Eliminar proveedor"
                                    />
                                </td>
                            </tr>
                        @empty
                            <x-table-empty
                                colspan="11"
                                message="No hay proveedores registrados"
                                icon="fas fa-truck"
                            />
                        @endforelse
                    </x-data-table>
                    <div class="float-leftpt-2">
                        <small class="text-muted">
                            Mostrando {{ $proveedores->firstItem() ?? 0 }} a {{ $proveedores->lastItem() ?? 0 }}
                            de {{ $proveedores->total() }} proveedores
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

@section('title', 'Ver Proveedor')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush

@section('content_header')
    <x-page-header
        icon="fas fa-eye"
        title="Ver Proveedor"
        subtitle="Información detallada del proveedor"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Proveedores', 'url' => route('inventario.proveedores.index')],
            ['label' => $proveedor->proveedor, 'active' => true]
        ]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <!-- Alertas -->
            @include('components.session-alerts')

            <!-- Botón Volver -->
            <div class="mb-3" id="boton-volver">
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('inventario.proveedores.index') }}">
                    <i class="fas fa-arrow-left mr-1" id="icono-volver"></i> Volver
                </a>
            </div>

            <div class="row" id="estadisticas-generales">
                <!-- Estadísticas Generales -->
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="stats-number">{{ $proveedor->contratos_convenios_count ?? 0 }}</div>
                        <div class="stats-label">
                            <i class="fas fa-file-contract mr-1"></i>
                            Contratos
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="stats-number">{{ $proveedor->productos_count ?? 0 }}</div>
                        <div class="stats-label">
                            <i class="fas fa-boxes mr-1"></i>
                            Productos
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="stats-number">
                            {{ $proveedor->estado->parametro->name ?? 'SIN ESTADO' }}
                        </div>
                        <div class="stats-label">
                            <i class="fas fa-toggle-on mr-1"></i>
                            Estado
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del Proveedor -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card detail-card no-hover">
                        <div id="informacion-proveedor" class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-info-circle mr-2"></i>Información del Proveedor
                            </h5>
                        </div>

                        <div class="card-body p-0" id="detalle">
                            <div class="table-responsive">
                                <table class="table detail-table mb-0">
                                    <caption id="proveedor-description" class="sr-only">
                                        Lista de proveedores con información de nombre, NIT, correo electrónico, teléfono, dirección, departamento, municipio, contacto, estado, total de contratos/convenios y total de productos.
                                    </caption>
                                    <tbody>
                                        <tr>
                                            <th class="py-3">Nombre del Proveedor</th>
                                            <td class="py-3">
                                                <strong>{{ $proveedor->proveedor }}</strong>
                                                <br><small class="text-muted">Proveedor registrado</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">NIT</th>
                                            <td class="py-3">
                                                <i class="fas fa-id-card mr-1"></i>
                                                {{ $proveedor->nit ?? 'No especificado' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Correo Electrónico</th>
                                            <td class="py-3">
                                                @if($proveedor->email)
                                                    <a href="mailto:{{ $proveedor->email }}" class="text-primary">
                                                        <i class="fas fa-envelope mr-1"></i>
                                                        {{ $proveedor->email }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">No especificado</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Teléfono</th>
                                            <td class="py-3">
                                                @if($proveedor->telefono)
                                                    <a href="tel:{{ $proveedor->telefono }}" class="text-primary">
                                                        <i class="fas fa-phone mr-1"></i>
                                                        {{ $proveedor->telefono }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">No especificado</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Dirección</th>
                                            <td class="py-3">
                                                @if($proveedor->direccion)
                                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                                    {{ $proveedor->direccion }}
                                                @else
                                                    <span class="text-muted">No especificada</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Departamento</th>
                                            <td class="py-3">
                                                @if($proveedor->departamento)
                                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                                    {{ $proveedor->departamento->departamento }}
                                                @else
                                                    <span class="text-muted">No especificado</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Municipio</th>
                                            <td class="py-3">
                                                @if($proveedor->municipio)
                                                    <i class="fas fa-map-pin mr-1"></i>
                                                    {{ $proveedor->municipio->municipio }}
                                                    <small class="text-muted">({{ $proveedor->municipio->departamento->departamento ?? 'N/A' }})</small>
                                                @else
                                                    <span class="text-muted">No especificado</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Contacto</th>
                                            <td class="py-3">
                                                @if($proveedor->contacto)
                                                    <i class="fas fa-user mr-1"></i>
                                                    {{ $proveedor->contacto }}
                                                @else
                                                    <span class="text-muted">No especificado</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Estado</th>
                                            <td class="py-3">
                                                @if($proveedor->estado)
                                                    <span class="badge badge-{{ $proveedor->estado->status == 1 ? 'success' : 'danger' }}">
                                                        {{ $proveedor->estado->parametro->name ?? 'N/A' }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary">SIN ESTADO</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Total de Contratos/Convenios</th>
                                            <td class="py-3">
                                                <span class="status-badge status-active">
                                                    <i class="fas fa-file-contract mr-1"></i>
                                                    {{ $proveedor->contratos_convenios_count ?? 0 }} contrato(s)
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Total de Productos</th>
                                            <td class="py-3">
                                                <span class="status-badge status-active">
                                                    <i class="fas fa-boxes mr-1"></i>
                                                    {{ $proveedor->productos_count ?? 0 }} producto(s)
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha de Creación</th>
                                            <td class="py-3">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ \Carbon\Carbon::parse($proveedor->created_at)->format('d/m/Y H:i') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Última Actualización</th>
                                            <td class="py-3">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ \Carbon\Carbon::parse($proveedor->updated_at)->format('d/m/Y H:i') }}
                                            </td>
                                        </tr>
                                        @if($proveedor->observaciones)
                                        <tr>
                                            <th class="py-3">Observaciones</th>
                                            <td class="py-3">{{ $proveedor->observaciones }}</td>
                                        </tr>
                                        @endif
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
                                @can('EDITAR PROVEEDOR')
                                    <a href="{{ route('inventario.proveedores.edit', $proveedor->id) }}" class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-pencil-alt mr-1"></i> Editar
                                    </a>
                                @endcan

                                @can('ELIMINAR PROVEEDOR')
                                    <form action="{{ route('inventario.proveedores.destroy', $proveedor->id) }}" 
                                          method="POST" class="d-inline formulario-eliminar">
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


