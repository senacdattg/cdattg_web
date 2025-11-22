@extends('inventario.layouts.base')

@section('title', 'Registrar Contrato/Convenio')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush

@section('content_header')
    <x-page-header
        icon="fas fa-plus"
        title="Registrar Contrato/Convenio"
        subtitle="Crear un nuevo contrato o convenio en el inventario"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Contratos y Convenios', 'url' => route('inventario.contratos-convenios.index')],
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
                                Información del Contrato/Convenio
                            </h5>
                        </div>

                        <div class="card-body">
                            <form action="{{ route('inventario.contratos-convenios.store') }}" method="POST">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Nombre del Contrato/Convenio <span class="text-danger">*</span></label>
                                            <input
                                                type="text"
                                                class="form-control @error('name') is-invalid @enderror"
                                                id="name"
                                                name="name"
                                                value="{{ old('name') }}"
                                                placeholder="Ingrese el nombre del contrato o convenio"
                                                required
                                            >
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="codigo">Código</label>
                                            <input
                                                type="text"
                                                class="form-control @error('codigo') is-invalid @enderror"
                                                id="codigo"
                                                name="codigo"
                                                value="{{ old('codigo') }}"
                                                placeholder="Ingrese el código del contrato/convenio"
                                            >
                                            @error('codigo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="proveedor_id">Proveedor</label>
                                            <select
                                                class="form-control @error('proveedor_id') is-invalid @enderror"
                                                id="proveedor_id"
                                                name="proveedor_id"
                                            >
                                                <option value="">Seleccione un proveedor</option>
                                                @foreach($proveedores as $proveedor)
                                                    <option value="{{ $proveedor->id }}" {{ old('proveedor_id') == $proveedor->id ? 'selected' : '' }}>
                                                        {{ $proveedor->proveedor }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('proveedor_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="estado_id">Estado <span class="text-danger">*</span></label>
                                            <select
                                                class="form-control @error('estado_id') is-invalid @enderror"
                                                id="estado_id"
                                                name="estado_id"
                                                required
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
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fecha_inicio">Fecha de Inicio</label>
                                            <input
                                                type="date"
                                                class="form-control @error('fecha_inicio') is-invalid @enderror"
                                                id="fecha_inicio"
                                                name="fecha_inicio"
                                                value="{{ old('fecha_inicio') }}"
                                            >
                                            @error('fecha_inicio')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fecha_fin">Fecha de Fin</label>
                                            <input
                                                type="date"
                                                class="form-control @error('fecha_fin') is-invalid @enderror"
                                                id="fecha_fin"
                                                name="fecha_fin"
                                                value="{{ old('fecha_fin') }}"
                                            >
                                            @error('fecha_fin')
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
                                                <a href="{{ route('inventario.contratos-convenios.index') }}" class="btn btn-outline-secondary btn-sm">
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

@section('title', 'Editar Contrato/Convenio')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush

@section('content_header')
    <x-page-header
        icon="fas fa-edit"
        title="Editar Contrato/Convenio"
        subtitle="Modificar datos del contrato o convenio"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Contratos y Convenios', 'url' => route('inventario.contratos-convenios.index')],
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
                                Información del Contrato/Convenio
                            </h5>
                        </div>

                        <div class="card-body">
                            <form action="{{ route('inventario.contratos-convenios.update', $contratoConvenio->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Nombre del Contrato/Convenio <span class="text-danger">*</span></label>
                                            <input
                                                type="text"
                                                class="form-control @error('name') is-invalid @enderror"
                                                id="name"
                                                name="name"
                                                value="{{ old('name', $contratoConvenio->name) }}"
                                                placeholder="Ingrese el nombre del contrato o convenio"
                                                required
                                            >
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="codigo">Código</label>
                                            <input
                                                type="text"
                                                class="form-control @error('codigo') is-invalid @enderror"
                                                id="codigo"
                                                name="codigo"
                                                value="{{ old('codigo', $contratoConvenio->codigo) }}"
                                                placeholder="Ingrese el código del contrato/convenio"
                                            >
                                            @error('codigo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="proveedor_id">Proveedor</label>
                                            <select
                                                class="form-control @error('proveedor_id') is-invalid @enderror"
                                                id="proveedor_id"
                                                name="proveedor_id"
                                            >
                                                <option value="">Seleccione un proveedor</option>
                                                @foreach($proveedores as $proveedor)
                                                    <option value="{{ $proveedor->id }}" {{ old('proveedor_id', $contratoConvenio->proveedor_id) == $proveedor->id ? 'selected' : '' }}>
                                                        {{ $proveedor->proveedor }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('proveedor_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="estado_id">Estado <span class="text-danger">*</span></label>
                                            <select
                                                class="form-control @error('estado_id') is-invalid @enderror"
                                                id="estado_id"
                                                name="estado_id"
                                                required
                                            >
                                                <option value="">Seleccione un estado</option>
                                                @foreach(
                                                    \App\Models\ParametroTema::with(['parametro','tema'])
                                                        ->whereHas('tema', fn($q) => $q->where('name', 'ESTADOS'))
                                                        ->where('status', 1)
                                                        ->get() as $estado
                                                )
                                                    <option value="{{ $estado->id }}" {{ old('estado_id', $contratoConvenio->estado_id) == $estado->id ? 'selected' : '' }}>
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
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fecha_inicio">Fecha de Inicio</label>
                                            <input
                                                type="date"
                                                class="form-control @error('fecha_inicio') is-invalid @enderror"
                                                id="fecha_inicio"
                                                name="fecha_inicio"
                                                value="{{ old('fecha_inicio', $contratoConvenio->fecha_inicio instanceof \Carbon\Carbon ? $contratoConvenio->fecha_inicio->format('Y-m-d') : $contratoConvenio->fecha_inicio) }}"
                                            >
                                            @error('fecha_inicio')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fecha_fin">Fecha de Fin</label>
                                            <input
                                                type="date"
                                                class="form-control @error('fecha_fin') is-invalid @enderror"
                                                id="fecha_fin"
                                                name="fecha_fin"
                                                value="{{ old('fecha_fin', $contratoConvenio->fecha_fin instanceof \Carbon\Carbon ? $contratoConvenio->fecha_fin->format('Y-m-d') : $contratoConvenio->fecha_fin) }}"
                                            >
                                            @error('fecha_fin')
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
                                                <a href="{{ route('inventario.contratos-convenios.index') }}" class="btn btn-outline-secondary btn-sm">
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

@section('title', 'Gestión de Contratos & Convenios')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush

@section('content_header')
    <x-page-header
        icon="fas fa-file-contract"
        title="Gestión de Contratos & Convenios"
        subtitle="Administra los contratos y convenios del inventario"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Contratos y Convenios', 'active' => true]
        ]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <x-create-card
                        url="{{ route('inventario.contratos-convenios.create') }}"
                        title="Nuevo Contrato/Convenio"
                        icon="fa-plus-circle"
                        permission="CREAR CONTRATO"
                    />

                    <x-data-table
                        title="Lista de Contratos y Convenios"
                        searchable="true"
                        searchAction="{{ route('inventario.contratos-convenios.index') }}"
                        searchPlaceholder="Buscar contrato..."
                        searchValue="{{ request('search') }}"
                        :columns="[
                            ['label' => '#', 'width' => '5%'],
                            ['label' => 'Nombre', 'width' => '25%'],
                            ['label' => 'Código', 'width' => '15%'],
                            ['label' => 'Fecha Inicio', 'width' => '12%'],
                            ['label' => 'Fecha Fin', 'width' => '12%'],
                            ['label' => 'Vigencia', 'width' => '10%'],
                            ['label' => 'Proveedor', 'width' => '10%'],
                            ['label' => 'Estado', 'width' => '6%'],
                            ['label' => 'Opciones', 'width' => '5%', 'class' => 'text-center']
                        ]"
                        :pagination="$contratosConvenios->links()"
                    >
                        @forelse ($contratosConvenios as $contrato)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $contrato->name ?? 'N/A' }}</td>
                                <td>{{ $contrato->codigo ?? 'N/A' }}</td>
                                <td>
                                    @if($contrato->fecha_inicio)
                                        @if(is_string($contrato->fecha_inicio))
                                            {{ \Carbon\Carbon::parse($contrato->fecha_inicio)->format('d/m/Y') }}
                                        @else
                                            {{ $contrato->fecha_inicio->format('d/m/Y') }}
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($contrato->fecha_fin)
                                        @if(is_string($contrato->fecha_fin))
                                            {{ \Carbon\Carbon::parse($contrato->fecha_fin)->format('d/m/Y') }}
                                        @else
                                            {{ $contrato->fecha_fin->format('d/m/Y') }}
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $fechaFin = null;
                                        if($contrato->fecha_fin) {
                                            $fechaFin = is_string($contrato->fecha_fin)
                                                ? \Carbon\Carbon::parse($contrato->fecha_fin)
                                                : $contrato->fecha_fin;
                                        }
                                    @endphp
                                    <span class="badge badge-{{ $fechaFin && $fechaFin->isPast() ? 'danger' : 'success' }}">
                                        {{ $fechaFin && $fechaFin->isPast() ? 'Vencido' : 'Vigente' }}
                                    </span>
                                </td>
                                <td>
                                    @if($contrato->proveedor)
                                        {{ is_object($contrato->proveedor) ? ($contrato->proveedor->proveedor ?? 'N/A') : $contrato->proveedor }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($contrato->estado)
                                        <span class="badge badge-{{ $contrato->estado->status == 1 ? 'success' : 'danger' }}">
                                            {{ $contrato->estado->parametro->name ?? 'N/A' }}
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
                                        showUrl="{{ route('inventario.contratos-convenios.show', $contrato->id) }}"
                                        editUrl="{{ route('inventario.contratos-convenios.edit', $contrato->id) }}"
                                        deleteUrl="{{ route('inventario.contratos-convenios.destroy', $contrato->id) }}"
                                        showTitle="Ver contrato"
                                        editTitle="Editar contrato"
                                        deleteTitle="Eliminar contrato"
                                    />
                                </td>
                            </tr>
                        @empty
                            <x-table-empty
                                colspan="9"
                                message="No hay contratos/convenios registrados"
                                icon="fas fa-file-contract"
                            />
                        @endforelse
                    </x-data-table>
                    <div class="float-left pt-2">
                        <small class="text-muted">
                            Mostrando {{ $contratosConvenios->firstItem() ?? 0 }} a {{ $contratosConvenios->lastItem() ?? 0 }}
                            de {{ $contratosConvenios->total() }} contratos/convenios
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Paginación --}}
    <div id="pagination-container" class="mt-3"></div>
    
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

@section('title', 'Ver Contrato/Convenio')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush

@section('content_header')
    <x-page-header
        icon="fas fa-eye"
        title="Ver Contrato/Convenio"
        subtitle="Información detallada del contrato o convenio"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Contratos y Convenios', 'url' => route('inventario.contratos-convenios.index')],
            ['label' => $contratoConvenio->name ?? 'N/A', 'active' => true]
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
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('inventario.contratos-convenios.index') }}">
                    <i class="fas fa-arrow-left mr-1" id="icono-volver"></i> Volver
                </a>
            </div>

            <div class="row" id="estadisticas-generales">
                <!-- Estadísticas Generales -->
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="stats-number">{{ $contratoConvenio->productos_count ?? 0 }}</div>
                        <div class="stats-label">
                            <i class="fas fa-boxes mr-1"></i>
                            Productos Asociados
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="stats-number">
                            @php
                                $fechaFin = $contratoConvenio->fecha_fin 
                                    ? (is_string($contratoConvenio->fecha_fin) 
                                        ? \Carbon\Carbon::parse($contratoConvenio->fecha_fin) 
                                        : $contratoConvenio->fecha_fin)
                                    : null;
                            @endphp
                            {{ $fechaFin && $fechaFin->isPast() ? 'Vencido' : 'Vigente' }}
                        </div>
                        <div class="stats-label">
                            <i class="fas fa-calendar-check mr-1"></i>
                            Vigencia
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="stats-number">
                            {{ $contratoConvenio->estado->parametro->name ?? 'N/A' }}
                        </div>
                        <div class="stats-label">
                            <i class="fas fa-toggle-on mr-1"></i>
                            Estado
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del Contrato/Convenio -->
            <div class="row mb-4" id="detalle">
                <div class="col-12">
                    <div class="card detail-card no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-info-circle mr-2"></i>Información del Contrato/Convenio
                            </h5>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table detail-table mb-0">
                                    <caption id="contrato-description" class="sr-only">
                                        Lista de contratos/convenios con información de nombre, código, proveedor, fechas, estado y total de productos.
                                    </caption>
                                    <tbody>
                                        <tr>
                                            <th class="py-3">Nombre</th>
                                            <td class="py-3">
                                                <strong>{{ $contratoConvenio->name ?? 'N/A' }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Código</th>
                                            <td class="py-3">{{ $contratoConvenio->codigo ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Proveedor</th>
                                            <td class="py-3">
                                                <i class="fas fa-truck mr-1"></i>
                                                {{ $contratoConvenio->proveedor->proveedor ?? 'N/A' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fechas</th>
                                            <td class="py-3">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <strong>Inicio:</strong><br>
                                                        @if($contratoConvenio->fecha_inicio)
                                                            <i class="far fa-calendar-alt mr-1"></i>
                                                            @if(is_string($contratoConvenio->fecha_inicio))
                                                                {{ \Carbon\Carbon::parse($contratoConvenio->fecha_inicio)->format('d/m/Y') }}
                                                            @else
                                                                {{ $contratoConvenio->fecha_inicio->format('d/m/Y') }}
                                                            @endif
                                                        @else
                                                            <span class="text-muted">No especificada</span>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Fin:</strong><br>
                                                        @if($contratoConvenio->fecha_fin)
                                                            <i class="far fa-calendar-alt mr-1"></i>
                                                            @php
                                                                $fechaFin = is_string($contratoConvenio->fecha_fin)
                                                                    ? \Carbon\Carbon::parse($contratoConvenio->fecha_fin)
                                                                    : $contratoConvenio->fecha_fin;
                                                            @endphp
                                                            <span class="badge badge-{{ $fechaFin->isPast() ? 'danger' : 'success' }}">
                                                                {{ $fechaFin->format('d/m/Y') }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">Sin vigencia</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Estado</th>
                                            <td class="py-3">
                                                @if($contratoConvenio->estado)
                                                    <span class="badge badge-{{ $contratoConvenio->estado->status == 1 ? 'success' : 'danger' }}">
                                                        {{ $contratoConvenio->estado->parametro->name ?? 'N/A' }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary">SIN ESTADO</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Total de Productos</th>
                                            <td class="py-3">
                                                <span class="status-badge status-active">
                                                    <i class="fas fa-boxes mr-1"></i>
                                                    {{ $contratoConvenio->productos_count ?? 0 }} producto(s)
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha de Creación</th>
                                            <td class="py-3">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ \Carbon\Carbon::parse($contratoConvenio->created_at)->format('d/m/Y H:i') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Última Actualización</th>
                                            <td class="py-3">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ \Carbon\Carbon::parse($contratoConvenio->updated_at)->format('d/m/Y H:i') }}
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
                                @can('EDITAR CONTRATO')
                                    <a href="{{ route('inventario.contratos-convenios.edit', $contratoConvenio->id) }}" class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-pencil-alt mr-1"></i> Editar
                                    </a>
                                @endcan

                                @can('ELIMINAR CONTRATO')
                                    <form action="{{ route('inventario.contratos-convenios.destroy', $contratoConvenio->id) }}" 
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


