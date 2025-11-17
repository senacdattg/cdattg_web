@extends('inventario.layouts.base')

@section('title', 'Catálogo de Productos')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 text-dark">
                <i class="fas fa-store"></i> Catálogo de Productos
            </h1>
            <small class="text-muted">Vista moderna de productos disponibles</small>
        </div>
        <div>
            <a href="{{ route('inventario.carrito.ecommerce') }}" class="btn btn-primary">
                <i class="fas fa-shopping-cart"></i> Ver Carrito 
                <span class="badge badge-light" id="cart-count">0</span>
            </a>
        </div>
    </div>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            {{-- Filtros y búsqueda --}}
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="search-product">
                                            <i class="fas fa-search"></i> Buscar Producto
                                        </label>
                                        <input 
                                            type="text" 
                                            id="search-product" 
                                            class="form-control" 
                                            placeholder="Buscar por nombre..."
                                            value="{{ request('search') }}"
                                        >
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="filter-type">
                                            <i class="fas fa-box-open"></i> Tipo de producto
                                        </label>
                                        <select
                                            id="filter-type"
                                            name="filter-type"
                                            class="form-control select2"
                                            data-placeholder="Todos los tipos"
                                        >
                                            <option value="">Todos los tipos</option>
                                            @foreach($tiposProductos as $tipoProducto)
                                                <option value="{{ $tipoProducto->id }}" 
                                                    {{ request('tipo_producto_id') == $tipoProducto->id ? 'selected' : '' }}>
                                                    {{ $tipoProducto->parametro->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="sort-by">
                                            <i class="fas fa-sort"></i> Ordenar por
                                        </label>
                                        <select 
                                            id="sort-by" 
                                            class="form-control"
                                        >
                                            <option value="name" {{ request('sort_by', 'name') == 'name' ? 'selected' : '' }}>Nombre</option>
                                            <option value="stock-asc" {{ request('sort_by') == 'stock-asc' ? 'selected' : '' }}>Stock Menor</option>
                                            <option value="stock-desc" {{ request('sort_by') == 'stock-desc' ? 'selected' : '' }}>Stock Mayor</option>
                                            <option value="newest" {{ request('sort_by') == 'newest' ? 'selected' : '' }}>Más Recientes</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Grid de productos --}}
            <div class="row" id="products-grid">
                @forelse($productos as $producto)
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 product-card" 
                         data-id="{{ $producto->id }}"
                         data-type="{{ $producto->tipo_producto_id }}"
                         data-name="{{ strtolower($producto->producto) }}"
                         data-code="{{ strtolower($producto->codigo_barras) }}">
                        <div class="card h-100 shadow-sm hover-shadow">
                            {{-- Imagen del producto --}}
                            <div class="product-image-container">
                                @if($producto->imagen)
                                    <img src="{{ asset($producto->imagen) }}" 
                                         class="card-img-top product-image" 
                                         alt="{{ $producto->producto }}"
                                @else
                                    <div class="no-image-placeholder">
                                        <i class="fas fa-box fa-4x text-muted"></i>
                                        <p class="text-muted mt-2">Sin imagen</p>
                                    </div>
                                @endif
                                
                                {{-- Badge de stock --}}
                                @php
                                    $stockClass = 'success';
                                    if ($producto->cantidad <= 0) {
                                        $stockClass = 'danger';
                                    } elseif ($producto->cantidad <= 5) {
                                        $stockClass = 'warning';
                                    }
                                @endphp
                                <span class="badge stock-badge stock-badge-{{ $stockClass }}">
                                </span>
                            </div>
                            <div class="card-body d-flex flex-column">
                                {{-- Categoría y marca --}}
                                <div class="mb-2">
                                    @if($producto->tipoProducto && $producto->tipoProducto->parametro)
                                        <small class="text-muted d-block">
                                            <i class="fas fa-box-open"></i> {{ $producto->tipoProducto->parametro->name }}
                                        </small>
                                    @endif
                                    <small class="text-muted">
                                        <i class="fas fa-tag"></i> {{ $producto->categoria->name ?? 'Sin categoría' }}
                                    </small>
                                    @if($producto->marca)
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-copyright"></i> {{ $producto->marca->name }}
                                        </small>
                                    @endif
                                </div>

                                {{-- Nombre del producto --}}
                                <h5 class="card-title font-weight-bold mb-2">
                                    {{ Str::limit($producto->producto, 50) }}
                                </h5>

                                {{-- Descripción --}}
                                <p class="card-text text-muted small flex-grow-1">
                                    {{ Str::limit($producto->descripcion, 80) ?? 'Sin descripción disponible' }}
                                </p>

                                {{-- Código de barras --}}
                                <div class="mb-2">
                                    <small class="text-muted">
                                        <i class="fas fa-barcode"></i> 
                                        <span class="badge badge-secondary">{{ $producto->codigo_barras }}</span>
                                    </small>
                                </div>

                                {{-- Stock disponible --}}
                                <div class="mb-3">
                                    <strong>Stock: </strong>
                                    <span class="badge badge-{{ $stockClass }}">
                                        {{ $producto->cantidad }} unidades
                                    </span>
                                </div>

                                {{-- Acciones --}}
                                <div class="btn-group d-flex" role="group">
                                    <button type="button" 
                                            class="btn btn-sm btn-info btn-view-details w-50"
                                            data-id="{{ $producto->id }}"
                                            title="Ver detalles">
                                        <i class="fas fa-eye"></i> Detalles
                                    </button>
                                    @if($producto->cantidad > 0)
                                        <button type="button" 
                                                class="btn btn-sm btn-success btn-add-to-cart w-50"
                                                data-id="{{ $producto->id }}"
                                                data-name="{{ $producto->producto }}"
                                                data-stock="{{ $producto->cantidad }}"
                                                title="Agregar al carrito">
                                            <i class="fas fa-cart-plus"></i> Agregar
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-sm btn-secondary w-50" disabled>
                                            <i class="fas fa-ban"></i> Agotado
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle fa-3x mb-3"></i>
                            <h5>No hay productos disponibles</h5>
                            <p>Actualmente no hay productos en el catálogo.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Paginación --}}
            <div class="row">
                <div class="col-12 d-flex justify-content-center" id="catalog-pagination">
                    {{ $productos->links() }}
                </div>
            </div>

            {{-- Mensaje cuando no hay resultados de búsqueda --}}
            <div class="row d-none" id="no-results">
                <div class="col-12">
                    <div class="alert alert-warning text-center">
                        <i class="fas fa-search fa-3x mb-3"></i>
                        <h5>No se encontraron resultados</h5>
                        <p>Intenta con otros términos de búsqueda o filtros.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Modal simple de detalles del producto --}}
    <div 
        id="productDetailModal" 
        style="
            display:none;
            position:fixed;
            top:0;
            left:0;
            width:100%;
            height:100%;
            background:rgba(0,0,0,0.5);
            z-index:9999;
            align-items:center;
            justify-content:center;
        "
    >
        <div 
            style="
                background:white;
                border-radius:8px;
                width:90%;
                max-width:600px;
                max-height:90vh;
                overflow-y:auto;
                box-shadow:0 4px 20px rgba(0,0,0,0.3);
            "
        >
            <!-- Header -->
            <div 
                style="
                    padding:20px;
                    background:#17a2b8;
                    color:white;
                    display:flex;
                    justify-content:space-between;
                    align-items:center;
                    border-radius:8px 8px 0 0;
                "
            >
                <h5 style="margin:0; font-size:18px;">
                    <i class="fas fa-box"></i> Detalles del Producto
                </h5>
                <button 
                    onclick="closeProductModal()" 
                    aria-label="Cerrar" 
                    style="
                        background:none;
                        border:none;
                        color:white;
                        font-size:24px;
                        cursor:pointer;
                    "
                >
                    &times;
                </button>
            </div>
            
            <!-- Body -->
            <div id="product-detail-content" style="padding:20px;">
                <div style="text-align:center;">
                    <i class="fas fa-spinner fa-spin fa-3x"></i>
                    <p>Cargando detalles...</p>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Alertas --}}
    {{-- Notificaciones manejadas globalmente por sweetalert2-notifications --}}
@endsection

@section('footer')
    {{-- Footer SENA --}}
    @include('layouts.footer')
@endsection

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.6.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
    @vite([
    'resources/css/inventario/shared/base.css', 
    'resources/css/inventario/card.css', 
    'resources/css/inventario/modal-producto.css', 
    'resources/css/inventario/imagen.css'
    ])
@endpush

@push('js')
    @vite('resources/js/inventario/card.js')
@endpush

@extends('inventario.layouts.base')

@section('title', 'Registrar Producto')

@section('content_header')
    <x-page-header
        icon="fas fa-plus-circle"
        title="Registrar Producto"
        subtitle="Crear un nuevo producto en el inventario"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Productos', 'url' => route('inventario.productos.index')],
            ['label' => 'Registrar', 'active' => true]
        ]"
    />
@endsection

@section('content')
    <div class="producto-form-container fade-in">
        {{-- Alertas --}}
        @include('components.session-alerts')

        <div class="row">
            {{-- Columna de Imagen --}}
            <div class="col-lg-4 col-md-5">
                <div class="image-preview-container slide-in">
                    <div class="image-preview-box">
                        <img
                            id="preview"
                            src="{{ asset('img/no-image.png') }}"
                            alt="Vista previa"
                        >
                    </div>
                    <div class="image-upload-area">
                        <label class="image-upload-btn">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Seleccionar Imagen</span>
                            <input
                                type="file"
                                name="imagen"
                                id="imagen"
                                accept="image/*"
                            >
                        </label>
                        <p class="text-muted mt-2 mb-0" style="font-size: 0.85rem;">
                            <i class="fas fa-info-circle"></i> JPG, PNG (máx. 2MB)
                        </p>
                    </div>
                </div>
            </div>

            {{-- Columna de Formulario --}}
            <div class="col-lg-8 col-md-7">
                <div class="producto-form-card slide-in">
                    <div class="form-header-gradient">
                        <h3>
                            <span class="header-icon">
                                <i class="fas fa-box-open"></i>
                            </span>
                            Información del Producto
                        </h3>
                    </div>

                    <form action="{{ route('inventario.productos.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="form-content-container" id="form">
                            {{-- Sección: Información Básica --}}
                            <div class="form-section">
                                <h4 class="form-section-title">
                                    <i class="fas fa-info-circle"></i>
                                    Información Básica
                                </h4>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="producto">
                                                <i class="fas fa-tag"></i>
                                                Nombre del Producto
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text"
                                                   class="form-control-modern @error('producto') is-invalid @enderror"
                                                   id="producto"
                                                   name="producto"
                                                   value="{{ old('producto') }}"
                                                   placeholder="Ej: Laptop Dell XPS 15"
                                                   required>
                                            @error('producto')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="codigo_barras">
                                                <i class="fas fa-barcode"></i>
                                                Código de Barras
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="text"
                                                       class="form-control-modern @error('codigo_barras') is-invalid @enderror"
                                                       id="codigo_barras"
                                                       name="codigo_barras"
                                                       value="{{ old('codigo_barras') }}"
                                                       placeholder="Escanear o ingresar (opcional)">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-info" id="scan-btn">
                                                        <i class="fas fa-qrcode"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            @error('codigo_barras')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    

                                    <div class="col-md-12">
                                        <div class="form-group-modern">
                                            <label for="descripcion">
                                                <i class="fas fa-align-left"></i>
                                                Descripción
                                            </label>
                                            <textarea class="form-control-modern @error('descripcion') is-invalid @enderror"
                                                      id="descripcion"
                                                      name="descripcion"
                                                      rows="3"
                                                      placeholder="Ingrese una descripción detallada">{{ old('descripcion') }}</textarea>
                                            @error('descripcion')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Sección: Clasificación --}}
                            <div class="form-section">
                                <h4 class="form-section-title">
                                    <i class="fas fa-tags"></i>
                                    Clasificación y Tipo
                                </h4>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="tipo_producto_id">
                                                <i class="fas fa-cubes"></i>
                                                Tipo de Producto
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select
                                                class="form-control-modern @error('tipo_producto_id') is-invalid @enderror"
                                                id="tipo_producto_id"
                                                name="tipo_producto_id"
                                                required
                                            >
                                                <option value="">Seleccionar tipo</option>
                                                @foreach($tiposProductos as $tipo)
                                                    <option value="{{ $tipo->id }}" {{ old('tipo_producto_id') == $tipo->id ? 'selected' : '' }}>
                                                        {{ $tipo->parametro->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('tipo_producto_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="categoria_id">
                                                <i class="fas fa-folder"></i>
                                                Categoría
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select
                                                class="form-control-modern @error('categoria_id') is-invalid @enderror"
                                                id="categoria_id"
                                                name="categoria_id"
                                                required
                                            >
                                                <option value="">Seleccionar categoría</option>
                                                @foreach($categorias as $categoria)
                                                    <option value="{{ $categoria->parametro->id }}" {{ old('categoria_id') == $categoria->parametro->id ? 'selected' : '' }}>
                                                        {{ $categoria->parametro->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('categoria_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="marca_id">
                                                <i class="fas fa-copyright"></i>
                                                Marca
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select
                                                class="form-control-modern @error('marca_id') is-invalid @enderror"
                                                id="marca_id"
                                                name="marca_id"
                                                required
                                            >
                                                <option value="">Seleccionar marca</option>
                                                @foreach($marcas as $marca)
                                                    <option value="{{ $marca->parametro->id }}" {{ old('marca_id') == $marca->parametro->id ? 'selected' : '' }}>
                                                        {{ $marca->parametro->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('marca_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="estado_producto_id">
                                                <i class="fas fa-info-circle"></i>
                                                Estado del Producto
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select
                                                class="form-control-modern @error('estado_producto_id') is-invalid @enderror"
                                                id="estado_producto_id"
                                                name="estado_producto_id"
                                                required
                                            >
                                                <option value="">Seleccionar estado</option>
                                                @foreach($estados as $estado)
                                                    <option value="{{ $estado->id }}" {{ old('estado_producto_id') == $estado->id ? 'selected' : '' }}>
                                                        {{ $estado->parametro->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('estado_producto_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Sección: Cantidad y Medidas --}}
                            <div class="form-section">
                                <h4 class="form-section-title">
                                    <i class="fas fa-ruler-combined"></i>
                                    Cantidad y Medidas
                                </h4>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group-modern">
                                            <label for="cantidad">
                                                <i class="fas fa-sort-numeric-up"></i>
                                                Cantidad
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="number"
                                                class="form-control-modern @error('cantidad') is-invalid @enderror"
                                                id="cantidad"
                                                name="cantidad"
                                                value="{{ old('cantidad', 1) }}"
                                                min="0"
                                                placeholder="Ej: 10"
                                                required
                                            >
                                            @error('cantidad')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group-modern">
                                            <label for="peso">
                                                <i class="fas fa-weight"></i>
                                                Peso/Magnitud
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="number"
                                                class="form-control-modern @error('peso') is-invalid @enderror"
                                                id="peso"
                                                name="peso"
                                                value="{{ old('peso') }}"
                                                step="0.01"
                                                min="0"
                                                placeholder="Ej: 2.5"
                                                required
                                            >
                                            @error('peso')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group-modern">
                                            <label for="unidad_medida_id">
                                                <i class="fas fa-balance-scale"></i>
                                                Unidad de Medida
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select
                                                class="form-control-modern @error('unidad_medida_id') is-invalid @enderror"
                                                id="unidad_medida_id"
                                                name="unidad_medida_id"
                                                required
                                            >
                                                <option value="">Seleccionar unidad</option>
                                                @foreach($unidadesMedida as $unidad)
                                                    <option value="{{ $unidad->id }}" {{ old('unidad_medida_id') == $unidad->id ? 'selected' : '' }}>
                                                        {{ $unidad->parametro->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('unidad_medida_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Sección: Ubicación y Proveedor --}}
                            <div class="form-section">
                                <h4 class="form-section-title">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Ubicación y Proveedor
                                </h4>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="ambiente_id">
                                                <i class="fas fa-building"></i>
                                                Ambiente
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select
                                                class="form-control-modern @error('ambiente_id') is-invalid @enderror"
                                                id="ambiente_id"
                                                name="ambiente_id"
                                                required
                                            >
                                                <option value="">Seleccionar ambiente</option>
                                                @foreach($ambientes as $ambiente)
                                                    <option value="{{ $ambiente->id }}" {{ old('ambiente_id') == $ambiente->id ? 'selected' : '' }}>
                                                        {{ $ambiente->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('ambiente_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="proveedor_id">
                                                <i class="fas fa-truck"></i>
                                                Proveedor
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select
                                                class="form-control-modern @error('proveedor_id') is-invalid @enderror"
                                                id="proveedor_id"
                                                name="proveedor_id"
                                                required
                                            >
                                                <option value="">Seleccionar proveedor</option>
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
                                        <div class="form-group-modern">
                                            <label for="contrato_convenio_id">
                                                <i class="fas fa-file-contract"></i>
                                                Contrato/Convenio
                                            </label>
                                            <select
                                                class="form-control-modern @error('contrato_convenio_id') is-invalid @enderror"
                                                id="contrato_convenio_id"
                                                name="contrato_convenio_id"
                                            >
                                                <option value="">Seleccionar contrato/convenio</option>
                                                @foreach($contratosConvenios as $contrato)
                                                    <option value="{{ $contrato->id }}" {{ old('contrato_convenio_id') == $contrato->id ? 'selected' : '' }}>
                                                        {{ $contrato->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('contrato_convenio_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="fecha_vencimiento">
                                                <i class="fas fa-calendar-times"></i>
                                                Fecha de Vencimiento
                                            </label>
                                            <input type="date"
                                                   class="form-control-modern @error('fecha_vencimiento') is-invalid @enderror"
                                                   id="fecha_vencimiento"
                                                   name="fecha_vencimiento"
                                                   value="{{ old('fecha_vencimiento') }}">
                                            @error('fecha_vencimiento')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Botones de Acción --}}
                        <div class="form-actions-container">
                            <a href="{{ route('inventario.productos.index') }}" class="btn-modern btn-modern-secondary">
                                <i class="fas fa-times"></i>
                                Cancelar
                            </a>
                            <button type="submit" class="btn-modern btn-modern-success">
                                <i class="fas fa-save"></i>
                                Guardar Producto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal para scanner --}}
    @include('inventario._components.image-modal')
@endsection

@section('footer')
    @include('layouts.footer')
@endsection

@push('css')
    @vite([
        'resources/css/inventario/shared/base.css',
        'resources/css/inventario/inventario.css',
        'resources/css/inventario/imagen.css',
    ])
@endpush

@push('js')
    <script src="https://unpkg.com/html5-qrcode"></script>
    @vite('resources/js/inventario/imagen.js')
    <script>
        // Preview de imagen
        document.getElementById('imagen').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
@endpush

@extends('inventario.layouts.base')

@section('title', 'Editar Producto')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@push('css')
    @vite([
        'resources/css/inventario/shared/base.css',
        'resources/css/inventario/inventario.css',
        'resources/css/inventario/imagen.css',
    ])
@endpush

@section('content_header')
    <x-page-header
        icon="fas fa-edit"
        title="Editar Producto"
        subtitle="Modificar información del producto"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Productos', 'url' => route('inventario.productos.index')],
            ['label' => 'Editar', 'active' => true]
        ]"
    />
@endsection

@section('content')
    <div class="producto-form-container fade-in">
        {{-- Alertas --}}
        @include('components.session-alerts')

        <div class="row">
            {{-- Columna de Imagen --}}
            <div class="col-lg-4 col-md-5">
                <div class="image-preview-container slide-in">
                    <div class="image-preview-box">
                        <img
                            id="preview"
                            src="{{ $producto->imagen ? asset($producto->imagen) : asset('img/no-image.png') }}"
                            alt="Vista previa"
                            style="cursor: pointer;"
                            data-toggle="modal"
                            data-target="#imageModal"
                        >
                    </div>
                    <div class="image-upload-area">
                        <label class="image-upload-btn">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Cambiar Imagen</span>
                            <input
                                type="file"
                                name="imagen"
                                id="imagen"
                                accept="image/*"
                            >
                        </label>
                        <p class="text-muted mt-2 mb-0" style="font-size: 0.85rem;">
                            <i class="fas fa-info-circle"></i> JPG, PNG (máx. 2MB)
                        </p>
                    </div>
                </div>
            </div>

            {{-- Columna de Formulario --}}
            <div class="col-lg-8 col-md-7">
                <div class="producto-form-card slide-in">
                    <div class="form-header-gradient">
                        <h3>
                            <span class="header-icon">
                                <i class="fas fa-box-open"></i>
                            </span>
                            Editar Información del Producto
                        </h3>
                    </div>

                    <form action="{{ route('inventario.productos.update', $producto->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-content-container">
                            {{-- Sección: Información Básica --}}
                            <div class="form-section">
                                <h4 class="form-section-title">
                                    <i class="fas fa-info-circle"></i>
                                    Información Básica
                                </h4>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="producto">
                                                <i class="fas fa-tag"></i>
                                                Nombre del Producto
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text"
                                                   class="form-control-modern @error('producto') is-invalid @enderror"
                                                   id="producto"
                                                   name="producto"
                                                   value="{{ old('producto', $producto->producto) }}"
                                                   placeholder="Ej: Laptop Dell XPS 15"
                                                   required>
                                            @error('producto')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="codigo_barras">
                                                <i class="fas fa-barcode"></i>
                                                Código de Barras
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="text"
                                                       class="form-control-modern @error('codigo_barras') is-invalid @enderror"
                                                       id="codigo_barras"
                                                       name="codigo_barras"
                                                       value="{{ old('codigo_barras', $producto->codigo_barras) }}"
                                                       placeholder="Escanear o ingresar">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-info" id="scan-btn">
                                                        <i class="fas fa-qrcode"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            @error('codigo_barras')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group-modern">
                                            <label for="descripcion">
                                                <i class="fas fa-align-left"></i>
                                                Descripción
                                            </label>
                                            <textarea class="form-control-modern @error('descripcion') is-invalid @enderror"
                                                      id="descripcion"
                                                      name="descripcion"
                                                      rows="3"
                                                      placeholder="Ingrese una descripción detallada">{{ old('descripcion', $producto->descripcion) }}</textarea>
                                            @error('descripcion')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Sección: Clasificación --}}
                            <div class="form-section">
                                <h4 class="form-section-title">
                                    <i class="fas fa-tags"></i>
                                    Clasificación y Tipo
                                </h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="tipo_producto_id">
                                                <i class="fas fa-cubes"></i>
                                                Tipo de Producto
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select
                                                class="form-control-modern @error('tipo_producto_id') is-invalid @enderror"
                                                id="tipo_producto_id"
                                                name="tipo_producto_id"
                                                required
                                            >
                                                <option value="">Seleccionar tipo</option>
                                                @foreach($tiposProductos as $tipo)
                                                    <option value="{{ $tipo->id }}" {{ old('tipo_producto_id', $producto->tipo_producto_id) == $tipo->id ? 'selected' : '' }}>
                                                        {{ $tipo->parametro->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('tipo_producto_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="categoria_id">
                                                <i class="fas fa-list"></i>
                                                Categoría
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select
                                                class="form-control-modern @error('categoria_id') is-invalid @enderror"
                                                id="categoria_id"
                                                name="categoria_id"
                                                required
                                            >
                                                <option value="">Seleccionar categoría</option>
                                                @foreach($categorias as $categoria)
                                                    <option value="{{ $categoria->parametro->id }}" {{ old('categoria_id', $producto->categoria_id) == $categoria->parametro->id ? 'selected' : '' }}>
                                                        {{ $categoria->parametro->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('categoria_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="marca_id">
                                                <i class="fas fa-copyright"></i>
                                                Marca
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select
                                                class="form-control-modern @error('marca_id') is-invalid @enderror"
                                                id="marca_id"
                                                name="marca_id"
                                                required
                                            >
                                                <option value="">Seleccionar marca</option>
                                                @foreach($marcas as $marca)
                                                    <option value="{{ $marca->parametro->id }}" {{ old('marca_id', $producto->marca_id) == $marca->parametro->id ? 'selected' : '' }}>
                                                        {{ $marca->parametro->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('marca_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="estado_producto_id">
                                                <i class="fas fa-check-circle"></i>
                                                Estado
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select
                                                class="form-control-modern @error('estado_producto_id') is-invalid @enderror"
                                                id="estado_producto_id"
                                                name="estado_producto_id"
                                                required
                                            >
                                                <option value="">Seleccionar estado</option>
                                                @foreach($estados as $estado)
                                                    <option value="{{ $estado->id }}" {{ old('estado_producto_id', $producto->estado_producto_id) == $estado->id ? 'selected' : '' }}>
                                                        {{ $estado->parametro->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('estado_producto_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Sección: Cantidad y Medidas --}}
                            <div class="form-section">
                                <h4 class="form-section-title">
                                    <i class="fas fa-ruler-combined"></i>
                                    Cantidad y Medidas
                                </h4>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group-modern">
                                            <label for="cantidad">
                                                <i class="fas fa-boxes"></i>
                                                Cantidad
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="number"
                                                class="form-control-modern @error('cantidad') is-invalid @enderror"
                                                id="cantidad"
                                                name="cantidad"
                                                value="{{ old('cantidad', $producto->cantidad) }}"
                                                min="0"
                                                required
                                            >
                                            @error('cantidad')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group-modern">
                                            <label for="peso">
                                                <i class="fas fa-weight"></i>
                                                Peso/Magnitud
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="number"
                                                class="form-control-modern @error('peso') is-invalid @enderror"
                                                id="peso"
                                                name="peso"
                                                value="{{ old('peso', $producto->peso) }}"
                                                step="0.01"
                                                min="0"
                                                required
                                            >
                                            @error('peso')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group-modern">
                                            <label for="unidad_medida_id">
                                                <i class="fas fa-balance-scale"></i>
                                                Unidad de Medida
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select
                                                class="form-control-modern @error('unidad_medida_id') is-invalid @enderror"
                                                id="unidad_medida_id"
                                                name="unidad_medida_id"
                                                required
                                            >
                                                <option value="">Seleccionar unidad</option>
                                                @foreach($unidadesMedida as $unidad)
                                                    <option value="{{ $unidad->id }}" {{ old('unidad_medida_id', $producto->unidad_medida_id) == $unidad->id ? 'selected' : '' }}>
                                                        {{ $unidad->parametro->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('unidad_medida_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Sección: Ubicación y Proveedor --}}
                            <div class="form-section">
                                <h4 class="form-section-title">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Ubicación y Proveedor
                                </h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="ambiente_id">
                                                <i class="fas fa-building"></i>
                                                Ambiente
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select
                                                class="form-control-modern @error('ambiente_id') is-invalid @enderror"
                                                id="ambiente_id"
                                                name="ambiente_id"
                                                required
                                            >
                                                <option value="">Seleccionar ambiente</option>
                                                @foreach($ambientes as $ambiente)
                                                    <option value="{{ $ambiente->id }}" {{ old('ambiente_id', $producto->ambiente_id) == $ambiente->id ? 'selected' : '' }}>
                                                        {{ $ambiente->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('ambiente_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="proveedor_id">
                                                <i class="fas fa-truck"></i>
                                                Proveedor
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select
                                                class="form-control-modern @error('proveedor_id') is-invalid @enderror"
                                                id="proveedor_id"
                                                name="proveedor_id"
                                                required
                                            >
                                                <option value="">Seleccionar proveedor</option>
                                                @foreach($proveedores as $proveedor)
                                                    <option value="{{ $proveedor->id }}" {{ old('proveedor_id', $producto->proveedor_id) == $proveedor->id ? 'selected' : '' }}>
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
                                        <div class="form-group-modern">
                                            <label for="contrato_convenio_id">
                                                <i class="fas fa-file-contract"></i>
                                                Contrato/Convenio
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select
                                                class="form-control-modern @error('contrato_convenio_id') is-invalid @enderror"
                                                id="contrato_convenio_id"
                                                name="contrato_convenio_id"
                                                required
                                            >
                                                <option value="">Seleccionar contrato</option>
                                                @foreach($contratosConvenios as $contrato)
                                                    <option value="{{ $contrato->id }}" {{ old('contrato_convenio_id', $producto->contrato_convenio_id) == $contrato->id ? 'selected' : '' }}>
                                                        {{ $contrato->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('contrato_convenio_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="fecha_vencimiento">
                                                <i class="fas fa-calendar-times"></i>
                                                Fecha de Vencimiento
                                            </label>
                                            <input
                                                type="date"
                                                class="form-control-modern @error('fecha_vencimiento') is-invalid @enderror"
                                                id="fecha_vencimiento"
                                                name="fecha_vencimiento"
                                                value="{{ old('fecha_vencimiento', optional($producto->fecha_vencimiento)->format('Y-m-d')) }}"
                                            >
                                            @error('fecha_vencimiento')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Botones de Acción --}}
                        <div class="form-actions-container">
                            <a href="{{ route('inventario.productos.index') }}" class="btn-modern btn-modern-secondary">
                                <i class="fas fa-times"></i>
                                Cancelar
                            </a>
                            <button type="submit" class="btn-modern btn-modern-primary">
                                <i class="fas fa-save"></i>
                                Actualizar Producto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal para imagen --}}
    @include('inventario._components.image-modal')
@endsection

@section('footer')
    @include('layouts.footer')
@endsection

@push('js')
    <script src="https://unpkg.com/html5-qrcode"></script>
    @vite('resources/js/inventario/imagen.js')
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
    <script>
        // Preview de imagen
        document.getElementById('imagen').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
@endpush

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etiqueta - {{ $producto->producto }}</title>
    <style>
        @page { size: auto; margin: 10mm; }
        body { font-family: Arial, sans-serif; }
        .label { width: 80mm; }
        .title { font-size: 12px; margin-bottom: 6px; }
        .barcode { width: 100%; }
        .code { font-size: 11px; text-align: center; margin-top: 4px; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
</head>
<body onload="renderAndPrint()">
    <div class="label">
        <div class="title">{{ $producto->producto }}</div>
        <svg id="barcode" class="barcode"></svg>
        <div class="code">{{ $producto->codigo_barras ?? 'SIN CODIGO' }}</div>
    </div>

    <script>
        function renderAndPrint() {
            var value = "{{ $producto->codigo_barras ?? '' }}";
            if (!value) {
                window.print();
                return;
            }
            JsBarcode("#barcode", value, {
                format: "code128",
                width: 2,
                height: 60,
                displayValue: false,
                margin: 0
            });
            setTimeout(function(){ window.print(); }, 250);
        }
    </script>
</body>
</html>




@extends('inventario.layouts.base')

@section('title', 'Gestión de Productos')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush

@section('content_header')
    <x-page-header
        icon="fas fa-boxes"
        title="Gestión de Productos"
        subtitle="Administra los productos del inventario"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Productos', 'active' => true]
        ]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <x-create-card
                        url="{{ route('inventario.productos.create') }}"
                        title="Crear Producto"
                        icon="fa-plus-circle"
                        permission="CREAR PRODUCTO"
                    />
                    <!-- Botón para escanear código de barras -->
                    <div class="mt-3 text-right">
                        <button class="btn btn-secondary btn-lg" data-toggle="modal" data-target="#modalEscanear">
                            <i class="fas fa-barcode"></i> Escanear Código de Barras
                        </button>
                    </div>

                    <x-data-table
                        title="Lista de Productos"
                        searchable="true"
                        searchAction="{{ route('inventario.productos.index') }}"
                        searchPlaceholder="Buscar producto..."
                        searchValue="{{ request('search') }}"
                        :columns="[
                            ['label' => '#', 'width' => '3%'],
                            ['label' => 'Producto', 'width' => '20%'],
                            ['label' => 'Código', 'width' => '14%'],
                            ['label' => 'Categoría', 'width' => '10%'],
                            ['label' => 'Marca', 'width' => '10%'],
                            ['label' => 'Cantidad', 'width' => '8%'],
                            ['label' => 'Peso', 'width' => '8%'],
                            ['label' => 'Estado', 'width' => '8%'],
                            ['label' => 'Contrato', 'width' => '6%'],
                            ['label' => 'Proveedor', 'width' => '7%'],
                            ['label' => 'Opciones', 'width' => '6%', 'class' => 'text-center']
                        ]"
                        :pagination="$productos->links()"
                    >
                        @forelse ($productos as $producto)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $producto->producto }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        {{ Str::limit($producto->descripcion, 30) ?? 'Sin descripción' }}
                                    </small>
                                </td>
                                <td>
                                    @if($producto->codigo_barras)
                                        <div><span class="badge badge-secondary">{{ $producto->codigo_barras }}</span></div>
                                        <div class="mt-1">
                                            <a class="btn btn-xs btn-outline-primary" target="_blank" href="{{ route('inventario.productos.etiqueta', $producto->id) }}">
                                                <i class="fas fa-print"></i> Imprimir
                                            </a>
                                        </div>
                                    @else
                                        <small class="text-muted">N/A</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ $producto->categoria->name ?? 'Sin categoría' }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge badge-dark">
                                        {{ $producto->marca->name ?? 'Sin marca' }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $stockClass = 'success';
                                        if ($producto->cantidad <= 5) $stockClass = 'danger';
                                        elseif ($producto->cantidad <= 10) $stockClass = 'warning';
                                        elseif ($producto->cantidad <= 20) $stockClass = 'info';
                                    @endphp
                                    <span class="badge badge-{{ $stockClass }}">
                                        {{ $producto->cantidad }}
                                    </span>
                                </td>
                                <td>
                                    @if($producto->peso && $producto->unidadMedida)
                                        <small>{{ $producto->peso }} {{ $producto->unidadMedida->parametro->name ?? '' }}</small>
                                    @else
                                        <small class="text-muted">N/A</small>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $estadoClass = 'success';
                                        $estadoText = 'DISPONIBLE';
                                        $estadoProducto = $producto->estado?->parametro?->name;
                                        if ($estadoProducto === 'AGOTADO') {
                                            $estadoClass = 'danger';
                                            $estadoText = 'AGOTADO';
                                        } elseif ($producto->cantidad <= 0) {
                                            $estadoClass = 'danger';
                                            $estadoText = 'AGOTADO';
                                        } elseif ($producto->cantidad <= 5) {
                                            $estadoClass = 'warning';
                                            $estadoText = 'BAJO STOCK';
                                        }
                                    @endphp
                                    <span class="badge badge-{{ $estadoClass }}">
                                        {{ $estadoText }}
                                    </span>
                                </td>
                                <td>
                                    <small>
                                        {{ $producto->contratoConvenio->name ?? 'N/A' }}
                                    </small>
                                </td>
                                <td>
                                    <small>
                                        {{ $producto->proveedor->proveedor ?? 'N/A' }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <x-action-buttons
                                        show="true"
                                        edit="true"
                                        delete="true"
                                        showUrl="{{ route('inventario.productos.show', $producto->id) }}"
                                        editUrl="{{ route('inventario.productos.edit', $producto->id) }}"
                                        deleteUrl="{{ route('inventario.productos.destroy', $producto->id) }}"
                                        showTitle="Ver producto"
                                        editTitle="Editar producto"
                                        deleteTitle="Eliminar producto"
                                    />
                                </td>
                            </tr>
                        @empty
                            <x-table-empty
                                colspan="10"
                                message="No hay productos registrados"
                                icon="fas fa-box"
                            />
                        @endforelse
                    </x-data-table>
                    <div class="float-left pt-2">
                        <small class="text-muted">
                            Mostrando {{ $productos->firstItem() ?? 0 }} a {{ $productos->lastItem() ?? 0 }}
                            de {{ $productos->total() }} productos
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal para escanear código de barras -->
    <div class="modal fade" id="modalEscanear" tabindex="-1" aria-labelledby="modalEscanearLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalEscanearLabel">
                        <i class="fas fa-barcode"></i> Escanear Código de Barras
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body text-center">
                    <p>Escanea el código de barras del producto usando el lector.</p>
                    <input type="text" id="inputCodigoBarras" class="form-control form-control-lg text-center"
                        placeholder="Esperando código..." autocomplete="off" autofocus>
                    <div id="resultadoBusqueda" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de confirmación de eliminación --}}
    <x-confirm-delete-modal />

    {{-- Alertas --}}
    {{-- Notificaciones manejadas globalmente por sweetalert2-notifications --}}
@endsection


@push('css')
    @vite([
        'resources/css/inventario/shared/base.css',
        'resources/js/inventario/escaner.js'
    ])
@endpush

@push('scripts')
    @vite(['resources/js/pages/formularios-generico.js'])
@endpush



@extends('inventario.layouts.base')

@section('title', 'Ver Producto')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush

@section('content_header')
    <x-page-header icon="fas fa-eye" title="Ver Producto" subtitle="Detalles del producto" :breadcrumb="[
        ['label' => 'Inicio', 'url' => '#'],
        ['label' => 'Inventario', 'active' => true],
        ['label' => 'Productos', 'url' => route('inventario.productos.index')],
        ['label' => $producto->producto, 'active' => true],
    ]" />
@endsection

@section('content')
    <div class="product-details-container fade-in">
        {{-- Alertas --}}
        @include('components.session-alerts')

        {{-- Botón Volver --}}
        <div class="mb-3">
            <a href="{{ route('inventario.productos.index') }}" class="btn-action btn-action-secondary">
                <i class="fas fa-arrow-left"></i>
                Volver al Listado
            </a>
        </div>

        <div class="row">
            {{-- Columna de Imagen --}}
            <div class="col-lg-4 col-md-5">
                <div class="product-image-card slide-in">
                    <h5 class="font-weight-bold mb-3 text-gradient">
                        <i class="fas fa-image"></i> Imagen del Producto
                    </h5>
                    <div class="product-image-wrapper">
                        <img src="{{ $producto->imagen ? asset($producto->imagen) : asset('public/img/inventario/producto-default.png') }}"
                            alt="{{ $producto->producto }}" class="img-fluid" style="cursor: pointer;"
                            onclick="$('#imageModal').modal('show'); $('#expandedImage').attr('src', this.src);">
                    </div>

                    {{-- Estadísticas Rápidas --}}
                    <div class="stats-grid mt-4">
                        <div
                            class="stat-card stat-{{ $producto->cantidad <= 5 ? 'danger' : ($producto->cantidad <= 10 ? 'warning' : 'success') }}">
                            <div class="stat-card-header">
                                <div class="stat-card-icon">
                                    <i class="fas fa-boxes"></i>
                                </div>
                                <div>
                                    <div class="stat-card-label">Stock</div>
                                    <div class="stat-card-value">{{ $producto->cantidad }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Widget del Carrito --}}
                @can('AGREGAR AL CARRITO')
                    <div class="card-gradient mt-3 slide-in">
                        <div class="card-gradient-header" style="background: var(--success-gradient)">
                            <i class="fas fa-shopping-cart"></i>
                            Agregar al Carrito
                        </div>
                        <div class="card-gradient-body">
                            @include('inventario._components.cart-widget', ['producto' => $producto])
                        </div>
                    </div>
                @endcan
            </div>

            {{-- Columna de Información --}}
            <div class="col-lg-8 col-md-7">
                <div class="product-info-card slide-in">
                    <div class="card-gradient-header">
                        <i class="fas fa-info-circle"></i>
                        Información del Producto
                    </div>
                    <div class="card-gradient-body p-0">
                        <ul class="product-info-list">
                            <li>
                                <i class="fas fa-tag"></i>
                                <strong>Producto:</strong>
                                <span>{{ $producto->producto }}</span>
                            </li>

                            <li>
                                <i class="fas fa-cubes"></i>
                                <strong>Tipo:</strong>
                                <span>{{ $producto->tipoProducto->parametro->name ?? 'N/A' }}</span>
                            </li>

                            <li>
                                <i class="fas fa-align-left"></i>
                                <strong>Descripción:</strong>
                                <span>{{ $producto->descripcion ?? 'Sin descripción' }}</span>
                            </li>

                            <li>
                                <i class="fas fa-weight"></i>
                                <strong>Magnitud:</strong>
                                <span>{{ $producto->peso }} {{ $producto->unidadMedida->parametro->name ?? '' }}</span>
                            </li>

                            <li>
                                <i class="fas fa-barcode"></i>
                                <strong>Código de Barras:</strong>
                                <span class="badge-modern badge-secondary">{{ $producto->codigo_barras }}</span>
                                @if ($producto->codigo_barras)
                                    <div class="mt-2">
                                        <svg id="barcode-show" style="width:100%"></svg>
                                        <div class="mt-1">
                                            <a target="_blank" class="btn btn-xs btn-outline-primary"
                                                href="{{ route('inventario.productos.etiqueta', $producto->id) }}">
                                                <i class="fas fa-print"></i> Imprimir etiqueta
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </li>

                            <li>
                                <i class="fas fa-check-circle"></i>
                                <strong>Estado:</strong>
                                <span
                                    class="badge-modern {{ $producto->estado->parametro->name === 'DISPONIBLE' ? 'badge-success' : 'badge-warning' }}">
                                    {{ $producto->estado->parametro->name ?? 'N/A' }}
                                </span>
                            </li>

                            <li>
                                <i class="fas fa-folder"></i>
                                <strong>Categoría:</strong>
                                <span class="badge-modern badge-info">
                                    <i class="fas fa-tag"></i>
                                    {{ $producto->categoria->name ?? 'N/A' }}
                                </span>
                            </li>

                            <li>
                                <i class="fas fa-copyright"></i>
                                <strong>Marca:</strong>
                                <span class="badge-modern badge-dark">
                                    {{ $producto->marca->name ?? 'N/A' }}
                                </span>
                            </li>

                            <li>
                                <i class="fas fa-building"></i>
                                <strong>Ambiente:</strong>
                                <span>{{ $producto->ambiente->title ?? 'N/A' }}</span>
                            </li>

                            <li>
                                <i class="fas fa-file-contract"></i>
                                <strong>Contrato/Convenio:</strong>
                                <span>{{ $producto->contratoConvenio->name ?? 'N/A' }}</span>
                            </li>

                            <li>
                                <i class="fas fa-truck"></i>
                                <strong>Proveedor:</strong>
                                <span>{{ $producto->proveedor->proveedor ?? 'N/A' }}</span>
                            </li>


                            <li>
                                <i class="fas fa-calendar-times"></i>
                                <strong>Fecha de Vencimiento:</strong>
                                <span>{{ $producto->fecha_vencimiento ? $producto->fecha_vencimiento->format('d/m/Y') : 'Sin fecha' }}</span>
                            </li>

                            <li>
                                <i class="fas fa-calendar-plus"></i>
                                <strong>Fecha de Registro:</strong>
                                <span>{{ $producto->created_at->format('d/m/Y H:i') }}</span>
                            </li>

                            @if ($producto->updated_at != $producto->created_at)
                                <li>
                                    <i class="fas fa-calendar-edit"></i>
                                    <strong>Última Actualización:</strong>
                                    <span>{{ $producto->updated_at->format('d/m/Y H:i') }}</span>
                                </li>
                            @endif

                            @if ($producto->userCreate)
                                <li>
                                    <i class="fas fa-user"></i>
                                    <strong>Creado por:</strong>
                                    <span>{{ $producto->userCreate->name }}</span>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                {{-- Estadísticas Adicionales --}}
                <div class="stats-grid mt-4">
                    <div
                        class="stat-card stat-{{ $producto->cantidad <= 5 ? 'danger' : ($producto->cantidad <= 10 ? 'warning' : 'success') }}">
                        <div class="stat-card-header">
                            <div class="stat-card-icon">
                                <i class="fas fa-warehouse"></i>
                            </div>
                            <div>
                                <div class="stat-card-label">Inventario</div>
                                <div class="stat-card-value">{{ $producto->cantidad }} unidades</div>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card stat-info">
                        <div class="stat-card-header">
                            <div class="stat-card-icon">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                            <div>
                                <div class="stat-card-label">Registro</div>
                                <div class="stat-card-value" style="font-size: 1.25rem;">
                                    {{ $producto->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="stat-card stat-{{ $producto->estado->parametro->name === 'DISPONIBLE' ? 'success' : 'warning' }}">
                        <div class="stat-card-header">
                            <div class="stat-card-icon">
                                <i class="fas fa-toggle-on"></i>
                            </div>
                            <div>
                                <div class="stat-card-label">Estado</div>
                                <div class="stat-card-value" style="font-size: 1rem;">
                                    {{ $producto->estado->parametro->name ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Botones de Acción --}}
                <div class="action-buttons-container">
                    @can('EDITAR PRODUCTO')
                        <a href="{{ route('inventario.productos.edit', $producto->id) }}"
                            class="btn-action btn-action-primary">
                            <i class="fas fa-edit"></i>
                            Editar Producto
                        </a>
                    @endcan

                    @can('ELIMINAR PRODUCTO')
                        <form action="{{ route('inventario.productos.destroy', $producto->id) }}" method="POST"
                            class="d-inline formulario-eliminar">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-action btn-action-danger">
                                <i class="fas fa-trash"></i>
                                Eliminar Producto
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    {{-- Modal para imagen expandida --}}
    @include('inventario._components.image-modal')
@endsection

@section('footer')
    @include('layouts.footer')
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css', 'resources/css/inventario/inventario.css', 'resources/css/inventario/imagen.css'])
@endpush

@push('js')
    @vite('resources/js/inventario/imagen.js')
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
    <script>
        // Confirmación de eliminación
        document.querySelectorAll('.formulario-eliminar').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Esta acción no se puede deshacer",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#eb3349',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    customClass: {
                        popup: 'modal-imagen-custom'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });

        // Render del código de barras
        @if ($producto->codigo_barras)
            JsBarcode("#barcode-show", "{{ $producto->codigo_barras }}", {
                format: "code128",
                width: 2,
                height: 60,
                displayValue: false
            });
        @endif
    </script>
@endpush

<div>
    <!-- Imagen clickeable para expandir -->
    @php
        $imagenProducto = $producto->imagen ? asset($producto->imagen) : asset('img/inventario/producto-default.png');
    @endphp
    <div class="modal-img-container" style="cursor: pointer; position: relative; border-radius: 8px; overflow: hidden;" 
         onclick="$('#imageModal').modal('show'); $('#expandedImage').attr('src', '{{ $imagenProducto }}');">
        <img src="{{ $imagenProducto }}" 
             alt="{{ $producto->producto }}" 
             class="modal-img"
             style="width: 100%; height: 100%; object-fit: contain; border-radius: 8px;"
             title="Haz clic para ampliar">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; font-size: 2rem; opacity: 0; transition: opacity 0.3s; text-shadow: 0 2px 4px rgba(0,0,0,0.5);" 
             class="expand-icon" 
             onmouseover="this.style.opacity='1'" 
             onmouseout="this.style.opacity='0'">
            <i class="fas fa-expand"></i>
        </div>
    </div>
    
    <!-- Modal de Imagen Expandible -->
    @include('inventario._components.image-modal')

    <!-- Nombre del producto -->
    <h5 class="modal-title">{{ $producto->producto }}</h5>

    <!-- Tabla de información -->
    <table class="modal-table">
        <caption id="producto-description" class="sr-only">
            Lista de productos con información de código, marca, categoría, tipo, stock, peso, estado, ambiente, proveedor y contrato/convenio.
        </caption>
        <thead>
            <tr>
                <th>Detalle</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Código:</td>
                <td>{{ $producto->codigo_barras ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Marca:</td>
                <td>{{ $producto->marca?->name ?? 'Sin marca' }}</td>
            </tr>
            <tr>
                <td>Categoría:</td>
                <td>{{ $producto->categoria?->name ?? 'Sin categoría' }}</td>
            </tr>
            <tr>
                <td>Tipo:</td>
                <td>{{ $producto->tipoProducto?->parametro?->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Stock:</td>
                <td>
                    <span class="modal-badge {{ $producto->cantidad > 0 ? 'badge-success' : 'badge-danger' }}">
                        {{ $producto->cantidad }} unidades
                    </span>
                </td>
            </tr>
            <tr>
                <td>Peso:</td>
                <td>
                    @if($producto->peso)
                        {{ $producto->peso }} kg
                    @else
                        N/A
                    @endif
                </td>
            </tr>
            <tr>
                <td>Estado:</td>
                <td>
                    <span class="modal-badge {{ $producto->estado?->parametro?->name === 'DISPONIBLE' ? 'badge-success' : 'badge-danger' }}">
                        {{ $producto->estado?->parametro?->name ?? 'N/A' }}
                    </span>
                </td>
            </tr>
            <tr>
                <td>Ambiente:</td>
                <td>{{ $producto->ambiente?->title ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Proveedor:</td>
                <td>{{ $producto->proveedor?->proveedor ?? 'N/A' }}</td>
            </tr>
            @if($producto->contratoConvenio)
            <tr>
                <td>Contrato/Convenio:</td>
                <td>{{ $producto->contratoConvenio->name }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <!-- Descripción -->
    @if($producto->descripcion)
    <div class="modal-description">
        <strong>Descripción:</strong>
        <p id="descripcion">{{ $producto->descripcion }}</p>
    </div>
    @endif

    <!-- Botones -->
    <div class="modal-buttons">
        @if($producto->cantidad > 0)
            <button type="button" 
                    class="modal-btn modal-btn-success"
                    onclick="agregarAlCarritoDesdeModal({{ $producto->id }}, '{{ addslashes($producto->producto) }}', {{ $producto->cantidad }})">
                <i class="fas fa-cart-plus"></i> Agregar al Carrito
            </button>
        @else
            <button type="button" class="modal-btn modal-btn-disabled" disabled>
                Stock Agotado
            </button>
        @endif
        <button type="button" 
                class="modal-btn modal-btn-secondary"
                onclick="closeProductModal()">
            Cerrar
        </button>
    </div>
</div>


