@extends('inventario.layouts.base')

@section('title', 'Órdenes Aprobadas')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush

@section('content_header')
    <x-page-header
        icon="fas fa-check-circle"
        title="Órdenes Aprobadas"
        subtitle="Órdenes completadas y aprobadas"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Órdenes', 'url' => route('inventario.ordenes.index')],
            ['label' => 'Aprobadas', 'active' => true]
        ]"
    />
@endsection

@section('content')
    @include('inventario._components.filtros', ['estado' => 'APROBADA'])
@endsection

@section('footer')
    @include('layouts.footer')
@endsection


@extends('inventario.layouts.base')

@section('title', 'Gestión de Órdenes')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush

@section('content_header')
    <x-page-header
        icon="fas fa-list"
        title="Gestión de Órdenes"
        subtitle="Administra las órdenes del inventario"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Órdenes', 'active' => true]
        ]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                   {{-- Script para limpiar carrito si viene de una orden exitosa --}}
                    @if(session('clear_cart'))
                        <script>
                            // Limpiar carrito del localStorage después de crear orden exitosamente
                            localStorage.removeItem('inventario_carrito');
                            localStorage.removeItem('inventario_draft');
                            sessionStorage.removeItem('carrito_data');
                        </script>
                    @endif
                    
                    <x-data-table
                        title="Lista de Órdenes"
                        searchable="true"
                        searchAction="{{ route('inventario.ordenes.index') }}"
                        searchPlaceholder="Buscar orden..."
                        searchValue="{{ request('search') }}"
                        :columns="[
                            ['label' => '#', 'width' => '5%'],
                            ['label' => 'ID', 'width' => '5%'],
                            ['label' => 'Usuario', 'width' => '15%'],
                            ['label' => 'Tipo', 'width' => '10%'],
                            ['label' => 'Productos', 'width' => '10%'],
                            ['label' => 'Estado', 'width' => '10%'],
                            ['label' => 'F. Devolución', 'width' => '12%'],
                            ['label' => 'F. Creación', 'width' => '12%'],
                            ['label' => 'Opciones', 'width' => '11%', 'class' => 'text-center']
                        ]"
                        :pagination="$ordenes->links()"
                    >
                        @forelse ($ordenes as $orden)
                            @php
                                // Obtener tipo de orden
                                $tipoNombre = $orden->tipoOrden->parametro->name ?? 'N/A';
                                $tipoClass = $tipoNombre === 'PRÉSTAMO' ? 'info' : 'warning';
                                
                                // Obtener estado del primer detalle (todas las órdenes comparten estado)
                                $estadoNombre = $orden->detalles->first()->estadoOrden->parametro->name ?? 'N/A';
                                $estadoClass = match($estadoNombre) {
                                    'EN ESPERA' => 'warning',
                                    'APROBADA' => 'success',
                                    'RECHAZADA' => 'danger',
                                    default => 'secondary'
                                };
                                
                                // Contar productos
                                $totalProductos = $orden->detalles->count();
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><span class="badge badge-secondary">{{ $orden->id }}</span></td>
                                <td>
                                    <i class="fas fa-user-circle text-primary"></i>
                                    {{ $orden->userCreate->name ?? 'N/A' }}
                                </td>
                                <td>
                                    <span class="badge badge-{{ $tipoClass }}">
                                        <i class="fas fa-{{ $tipoNombre === 'PRÉSTAMO' ? 'handshake' : 'sign-out-alt' }}"></i>
                                        {{ $tipoNombre }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-primary">
                                        <i class="fas fa-boxes"></i> {{ $totalProductos }}
                                        {{ $totalProductos === 1 ? 'producto' : 'productos' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $estadoClass }}">
                                        <i class="fas fa-{{ $estadoNombre === 'EN ESPERA' ? 'clock' : ($estadoNombre === 'APROBADA' ? 'check-circle' : 'times-circle') }}"></i>
                                        {{ $estadoNombre }}
                                    </span>
                                </td>
                                <td>
                                    @if($orden->fecha_devolucion)
                                        <span class="badge badge-light">
                                            <i class="fas fa-calendar-alt"></i>
                                            {{ $orden->fecha_devolucion->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-light">
                                        <i class="fas fa-calendar-plus"></i>
                                        {{ $orden->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('inventario.ordenes.show', $orden) }}"
                                       class="btn btn-sm btn-info"
                                       title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($estadoNombre === 'EN ESPERA')
                                        <a href="{{ route('inventario.aprobaciones.pendientes') }}"
                                           class="btn btn-sm btn-warning"
                                           title="Gestionar">
                                            <i class="fas fa-tasks"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <x-table-empty
                                colspan="9"
                                message="No hay órdenes registradas"
                                icon="fas fa-list"
                            />
                        @endforelse
                    </x-data-table>
                    <div class="float-left pt-2">
                        <small class="text-muted">
                            Mostrando {{ $ordenes->firstItem() ?? 0 }} a {{ $ordenes->lastItem() ?? 0 }}
                            de {{ $ordenes->total() }} órdenes
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Alertas --}}
    {{-- Notificaciones manejadas globalmente por sweetalert2-notifications --}}
@endsection

@section('footer')
    {{-- Footer SENA --}}
    @include('layouts.footer')
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush


@extends('inventario.layouts.base')

@section('title', 'Órdenes Pendientes')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush

@section('content_header')
    <x-page-header
        icon="fas fa-hourglass-half"
        title="Órdenes Pendientes"
        subtitle="Órdenes en espera de aprobación"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Órdenes', 'url' => route('inventario.ordenes.index')],
            ['label' => 'Pendientes', 'active' => true]
        ]"
    />
@endsection

@section('content')
    @include('inventario._components.filtros', ['estado' => 'EN ESPERA'])
@endsection

@section('footer')
    @include('layouts.footer')
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush

@extends('inventario.layouts.base')

@section('title', 'Préstamo o Salida')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush

@section('content_header')
    <x-page-header
        icon="fas fa-exchange-alt"
        title="Préstamo o Salida"
        subtitle="Gestión de préstamos y salidas del inventario"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Préstamos/Salidas', 'active' => true]
        ]"
    />
@endsection

@section('content')
    <div class="producto-form-container fade-in">
        {{-- Alertas --}}
        @include('components.session-alerts')

        <div class="row">
            <div class="col-12">
                <div class="producto-form-card slide-in">
                    <div class="form-header-gradient">
                        <h3>
                            <span class="header-icon">
                                <i class="fas fa-exchange-alt"></i>
                            </span>
                            Préstamo o Salida
                        </h3>
                        <p class="header-subtitle mt-2 mb-0">
                            <small class="text-muted">Completa los datos para registrar la solicitud</small>
                        </p>
                    </div>

                    @php($tipoInicial = old('tipo', request('tipo')))

                    {{-- ⚙️ Importante: acción apunta al route POST --}}
                    <form action="{{ route('inventario.prestamos-salidas.store') }}" method="POST" id="form-solicitud">
                        @csrf

                        <div class="form-content-container">
                            {{-- 📦 Resumen del Carrito --}}
                            <div class="stats-grid" id="carrito-resumen-stats">
                                <div class="stat-card stat-info">
                                    <div class="stat-card-header">
                                        <div class="stat-card-icon">
                                            <i class="fas fa-boxes"></i>
                                        </div>
                                        <div>
                                            <div class="stat-card-label">Productos en la solicitud</div>
                                            <div class="stat-card-value" id="carrito-total-productos">0</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="stat-card stat-success">
                                    <div class="stat-card-header">
                                        <div class="stat-card-icon">
                                            <i class="fas fa-layer-group"></i>
                                        </div>
                                        <div>
                                            <div class="stat-card-label">Total de ítems</div>
                                            <div class="stat-card-value" id="carrito-total-items">0</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- 🛒 Detalle de productos en el carrito --}}
                            <div class="card mt-3 d-none" id="carrito-items-card">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-shopping-cart"></i> Productos del Carrito
                                    </h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0">
                                            <caption id="carrito-description" class="sr-only">
                                                Lista de productos del carrito con información de producto y cantidad.
                                            </caption>
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Producto</th>
                                                    <th class="text-center" style="width: 100px;">Cantidad</th>
                                                </tr>
                                            </thead>
                                            <tbody id="carrito-items-tbody"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            {{-- 👤 Datos del Solicitante --}}
                            <div class="form-section">
                                <h4 class="form-section-title">
                                    <i class="fas fa-user"></i> Datos del Solicitante
                                </h4>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="solicitante-nombre">
                                                <i class="fas fa-user"></i> Solicitante
                                            </label>
                                            <input
                                                type="text"
                                                id="solicitante-nombre"
                                                class="form-control-modern"
                                                value="{{ auth()->user()->name ?? 'Usuario' }}"
                                                readonly
                                            >
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="solicitante-correo">
                                                <i class="fas fa-envelope"></i> Correo
                                            </label>
                                            <input
                                                type="text"
                                                id="solicitante-correo"
                                                class="form-control-modern"
                                                value="{{ auth()->user()->email ?? '' }}"
                                                readonly
                                            >
                                        </div>
                                    </div>
                                    <input type="hidden" name="solicitante_email" value="{{ auth()->user()->email ?? '' }}">
                                    <input type="hidden" name="solicitante_id" value="{{ auth()->id() }}">
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="solicitante-rol">
                                                <i class="fas fa-user-tag"></i> Rol
                                            </label>
                                            <input
                                                type="text"
                                                id="solicitante-rol"
                                                class="form-control-modern"
                                                value="{{ (auth()->check() && method_exists(auth()->user(), 'getRoleNames') && auth()->user()->getRoleNames()->first()) ? auth()->user()->getRoleNames()->first() : (auth()->user()->role->name ?? 'N/A') }}"
                                                readonly
                                            >
                                            <input
                                                type="hidden"
                                                name="rol"
                                                value="{{ (auth()->check() && method_exists(auth()->user(), 'getRoleNames') && auth()->user()->getRoleNames()->first()) ? auth()->user()->getRoleNames()->first() : (auth()->user()->role->name ?? '') }}"
                                            >
                                        </div>
                                    </div>

                                    <div class="col-md-6" id="grupo-programa-formacion">
                                        <div class="form-group-modern">
                                            <label for="programa_formacion">
                                                <i class="fas fa-graduation-cap"></i>
                                                Programa de Formación
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select
                                                class="form-control-modern @error('programa_formacion') is-invalid @enderror"
                                                id="programa_formacon"
                                                name="programa_formacion"
                                                required
                                            >
                                                <option value="">Seleccionar programa...</option>
                                                @foreach($programas as $programa)
                                                    <option value="{{ $programa->nombre }}" {{ old('programa_formacion') == $programa->nombre ? 'selected' : '' }}>
                                                        {{ $programa->codigo }} - {{ $programa->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('programa_formacion')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <h4 class="form-section-title">
                                    <i class="fas fa-clipboard-list"></i> Detalles y Fechas
                                </h4>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="tipo">
                                                <i class="fas fa-tags"></i> Tipo
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select
                                                class="form-control-modern @error('tipo') is-invalid @enderror"
                                                id="tipo"
                                                name="tipo"
                                                required
                                            >
                                                <option value="">Seleccionar tipo...</option>
                                                <option value="prestamo" {{ old('tipo', $tipoInicial ?? '') === 'prestamo' ? 'selected' : '' }}>Préstamo</option>
                                                <option value="salida" {{ old('tipo', $tipoInicial ?? '') === 'salida' ? 'selected' : '' }}>Salida</option>
                                            </select>
                                            @error('tipo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6 d-none" id="grupo-fecha-devolucion">
                                        <div class="form-group-modern">
                                            <label for="fecha_devolucion">
                                                <i class="fas fa-calendar-check"></i> Fecha de Devolución
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="date"
                                                class="form-control-modern @error('fecha_devolucion') is-invalid @enderror"
                                                id="fecha_devolucion"
                                                name="fecha_devolucion"
                                                value="{{ old('fecha_devolucion') }}"
                                            >
                                            <small class="form-text text-muted d-block mt-1">
                                                <i class="fas fa-info-circle"></i>
                                                Fecha en la que se espera devolver los materiales
                                            </small>
                                            @error('fecha_devolucion')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <h4 class="form-section-title">
                                    <i class="fas fa-comment-alt"></i> Motivo
                                </h4>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group-modern">
                                            <label for="descripcion">
                                                <i class="fas fa-comment-alt"></i> Motivo de la solicitud
                                                <span class="text-danger">*</span>
                                            </label>
                                            <textarea
                                                class="form-control-modern @error('descripcion') is-invalid @enderror"
                                                id="descripcion"
                                                name="descripcion"
                                                rows="4"
                                                placeholder="Describe el motivo de la solicitud, para qué se necesita, etc."
                                                required
                                            >{{ old('descripcion') }}</textarea>
                                            @error('descripcion')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="carrito" id="carrito">

                        <div class="form-actions-container">
                            <a href="{{ route('inventario.productos.catalogo') }}" class="btn-modern btn-modern-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn-modern btn-modern-success">
                                <i class="fas fa-save"></i> Crear Préstamo/Salida
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- 🧩 Scripts --}}
    @vite(['resources/js/inventario/solicitud.js'])
    <script>
        // Cargar carrito al enviar formulario
        document.getElementById('form-solicitud').addEventListener('submit', function() {
            const carrito = JSON.parse(sessionStorage.getItem('carrito')) || [];
            document.getElementById('carrito').value = JSON.stringify(carrito);
        });

        // Mostrar/ocultar fecha de devolución
        const tipoSelect = document.getElementById('tipo');
        const grupoFecha = document.getElementById('grupo-fecha-devolucion');

        tipoSelect.addEventListener('change', function() {
            if (this.value === 'prestamo') {
                grupoFecha.classList.remove('d-none');
            } else {
                grupoFecha.classList.add('d-none');
            }
        });
    </script>

    {{-- Alertas --}}
    {{-- Notificaciones manejadas globalmente por sweetalert2-notifications --}}

    {{-- Footer --}}
    @include('layouts.footer')
@endsection

@push('css')
    @vite([
        'resources/css/inventario/shared/base.css',
        'resources/css/inventario/inventario.css'
    ])
@endpush


@extends('inventario.layouts.base')

@section('title', 'Órdenes Rechazadas')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush

@section('content_header')
    <x-page-header
        icon="fas fa-times-circle"
        title="Órdenes Rechazadas"
        subtitle="Órdenes rechazadas o canceladas"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Órdenes', 'url' => route('inventario.ordenes.index')],
            ['label' => 'Rechazadas', 'active' => true]
        ]"
    />
@endsection

@section('content')
    @include('inventario._components.filtros', ['estado' => 'RECHAZADA'])
@endsection

@section('footer')
    @include('layouts.footer')
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush


@extends('inventario.layouts.base')

@section('title', 'Detalles de Orden')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush

@section('content_header')
    <x-page-header
        icon="fas fa-info-circle"
        title="Detalles de Orden"
        subtitle="Ver detalles de la orden y sus productos"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Órdenes', 'url' => route('inventario.ordenes.index')],
            ['label' => 'Detalles', 'active' => true]
        ]"
    />
@endsection

@section('content')
    <div class="container-fluid orden-show-card">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-file-invoice"></i>
                            Información de la Orden #{{ $orden->id }}
                        </h3>
                    </div>

                    <div class="card-body">
                        {{-- Información general --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-group">
                                    <strong><i class="fas fa-hashtag"></i> ID de Orden:</strong>
                                    <span class="badge badge-secondary">#{{ $orden->id }}</span>
                                </div>
                                <div class="info-group">
                                    <strong><i class="fas fa-exchange-alt"></i> Tipo de Orden:</strong>
                                    @php
                                        $tipoNombre = $orden->tipoOrden->parametro->name ?? 'N/A';
                                        $tipoClass = $tipoNombre === 'PRÉSTAMO' ? 'warning' : 'info';
                                    @endphp
                                    <span class="badge badge-{{ $tipoClass }}">
                                        <i class="fas fa-{{ $tipoNombre === 'PRÉSTAMO' ? 'handshake' : 'sign-out-alt' }}"></i>
                                        {{ $tipoNombre }}
                                    </span>
                                </div>
                                <div class="info-group">
                                    <strong><i class="fas fa-calendar-plus"></i> Fecha Creación:</strong>
                                    <span>{{ $orden->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                @if($orden->fecha_devolucion)
                                    <div class="info-group">
                                        <strong><i class="fas fa-calendar-check"></i> Fecha Devolución:</strong>
                                        <span class="badge badge-warning">
                                            <i class="fas fa-clock"></i>
                                            {{ $orden->fecha_devolucion->format('d/m/Y') }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <div class="info-group">
                                    <strong><i class="fas fa-user-circle"></i> Solicitante:</strong>
                                    <span>{{ $orden->userCreate->name }}</span>
                                </div>
                                <div class="info-group">
                                    <strong><i class="fas fa-envelope"></i> Email:</strong>
                                    <span>{{ $orden->userCreate->email }}</span>
                                </div>
                                @php
                                    // Extraer información de la descripción
                                    $descripcion = $orden->descripcion_orden ?? '';
                                    preg_match('/Programa de Formación:\s*(.+?)[\n\r]/i', $descripcion, $matchPrograma);
                                    preg_match('/Rol:\s*(.+?)[\n\r]/i', $descripcion, $matchRol);
                                    $programa = $matchPrograma[1] ?? 'N/A';
                                    $rol = $matchRol[1] ?? 'N/A';
                                @endphp
                                <div class="info-group">
                                    <strong><i class="fas fa-graduation-cap"></i> Programa:</strong>
                                    <span>{{ $programa }}</span>
                                </div>
                                <div class="info-group">
                                    <strong><i class="fas fa-id-badge"></i> Rol:</strong>
                                    <span>{{ $rol }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="description-box">
                            <strong><i class="fas fa-comment-dots"></i> Motivo de la Solicitud:</strong>
                            @php
                                preg_match('/MOTIVO:\s*(.+?)$/s', $descripcion, $matchMotivo);
                                $motivo = isset($matchMotivo[1]) ? trim($matchMotivo[1]) : $orden->descripcion_orden;
                            @endphp
                            <p id="razon">{{ $motivo }}</p>
                        </div>

                        {{-- Lista de productos --}}
                        <div class="products-section">
                            <h4><i class="fas fa-boxes"></i> Productos Solicitados</h4>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <caption id="orden-description" class="sr-only">
                                        Lista de productos solicitados con información de número, producto, cantidad, estado y acciones disponibles.
                                    </caption>
                                    <thead>
                                        <tr>
                                            <th style="width: 8%;">#</th>
                                            <th style="width: 35%;">Producto</th>
                                            <th style="width: 12%;" class="text-center">Cantidad</th>
                                            <th style="width: 15%;">Estado</th>
                                            <th style="width: 30%;" class="text-center">Información</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($orden->detalles as $detalle)
                                            <tr>
                                                <td><strong>{{ $loop->iteration }}</strong></td>
                                                <td>
                                                    <i class="fas fa-box text-primary"></i>
                                                    {{ $detalle->producto->producto ?? 'N/A' }}
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-info">
                                                        <i class="fas fa-hashtag"></i>
                                                        {{ $detalle->cantidad }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @php
                                                        $estadoNombre = $detalle->estadoOrden->parametro->name ?? 'N/A';
                                                        $estadoClass = match($estadoNombre) {
                                                            'EN ESPERA' => 'warning',
                                                            'APROBADA' => 'success',
                                                            'RECHAZADA' => 'danger',
                                                            default => 'secondary'
                                                        };
                                                        $estadoIcon = match($estadoNombre) {
                                                            'EN ESPERA' => 'clock',
                                                            'APROBADA' => 'check-circle',
                                                            'RECHAZADA' => 'times-circle',
                                                            default => 'question-circle'
                                                        };
                                                    @endphp
                                                    <span class="badge badge-{{ $estadoClass }}">
                                                        <i class="fas fa-{{ $estadoIcon }}"></i>
                                                        {{ $estadoNombre }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    @if($estadoNombre === 'EN ESPERA')
                                                        <a href="{{ route('inventario.aprobaciones.pendientes') }}"
                                                           class="btn btn-sm btn-info"
                                                           data-toggle="tooltip"
                                                           title="Ir a gestionar la aprobación">
                                                            <i class="fas fa-tasks"></i> Gestionar Aprobación
                                                        </a>
                                                    @elseif($estadoNombre === 'APROBADA')
                                                        <div class="estado-info">
                                                            <span class="badge badge-success">
                                                                <i class="fas fa-user-check"></i>
                                                                Aprobada
                                                            </span>
                                                            @if($detalle->aprobacion)
                                                                <small>
                                                                    Por: {{ $detalle->aprobacion->aprobador->name ?? 'Admin' }}
                                                                    <br>
                                                                    {{ $detalle->aprobacion->created_at->format('d/m/Y H:i') }}
                                                                </small>
                                                            @endif
                                                        </div>
                                                    @elseif($estadoNombre === 'RECHAZADA')
                                                        <div class="estado-info">
                                                            <span class="badge badge-danger">
                                                                <i class="fas fa-user-times"></i>
                                                                Rechazada
                                                            </span>
                                                            @if($detalle->aprobacion)
                                                                <small>
                                                                    Por: {{ $detalle->aprobacion->aprobador->name ?? 'Admin' }}
                                                                    <br>
                                                                    {{ $detalle->aprobacion->created_at->format('d/m/Y H:i') }}
                                                                </small>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">
                                                    <i class="fas fa-inbox"></i>
                                                    <br>
                                                    No hay productos en esta orden
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-right">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de Observaciones para Rechazo --}}
    <div class="modal fade" id="modalRechazo" tabindex="-1" aria-labelledby="modalRechazoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalRechazoLabel">
                        <i class="fas fa-comment-alt"></i>
                        Observaciones del Rechazo
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formRechazo">
                        <div class="form-group">
                            <label for="observaciones">Motivo del rechazo:</label>
                            <textarea
                                class="form-control"
                                id="observaciones"
                                name="observaciones"
                                rows="3"
                                required
                            ></textarea>
                        </div>
                        <input type="hidden" id="detalleIdRechazo">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmarRechazo">
                        <i class="fas fa-check"></i> Confirmar Rechazo
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Alertas --}}
    {{-- Notificaciones manejadas globalmente por sweetalert2-notifications --}}
    
    {{-- Footer SENA --}}
    @include('layouts.footer')
@endsection

@push('css')
    @vite([
        'resources/css/inventario/shared/base.css',
        'resources/css/inventario/orden.css'
    ])
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Inicializar tooltips
            $('[data-toggle="tooltip"]').tooltip();

            // Manejar aprobación
            $('.aprobar-detalle').click(function() {
                const detalleId = $(this).data('detalle-id');
                
                Swal.fire({
                    title: '¿Aprobar este producto?',
                    text: "Esta acción descontará el stock del producto",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, aprobar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        aprobarDetalle(detalleId);
                    }
                });
            });

            // Manejar rechazo (mostrar modal)
            $('.rechazar-detalle').click(function() {
                const detalleId = $(this).data('detalle-id');
                $('#detalleIdRechazo').val(detalleId);
                $('#modalRechazo').modal('show');
            });

            // Confirmar rechazo
            $('#confirmarRechazo').click(function() {
                const detalleId = $('#detalleIdRechazo').val();
                const observaciones = $('#observaciones').val();
                
                if (!observaciones.trim()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Debe ingresar las observaciones del rechazo'
                    });
                    return;
                }

                rechazarDetalle(detalleId, observaciones);
                $('#modalRechazo').modal('hide');
            });

            // Función para aprobar detalle
            function aprobarDetalle(detalleId) {
                $.ajax({
                    url: `/inventario/ordenes/detalles/${detalleId}/aprobar`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Aprobado!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Ocurrió un error al aprobar el producto'
                        });
                    }
                });
            }

            // Función para rechazar detalle
            function rechazarDetalle(detalleId, observaciones) {
                $.ajax({
                    url: `/inventario/ordenes/detalles/${detalleId}/rechazar`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        observaciones: observaciones
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Rechazado!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Ocurrió un error al rechazar el producto'
                        });
                    }
                });
            }
        });
    </script>
@endpush

