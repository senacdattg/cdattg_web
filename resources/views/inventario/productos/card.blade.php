@extends('adminlte::page')

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
    @include('layouts.alertas')
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
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
    @vite('resources/js/inventario/card.js')
@endpush
