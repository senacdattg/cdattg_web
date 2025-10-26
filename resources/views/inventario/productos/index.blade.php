@extends('adminlte::page')

@section('classes_body', 'productos-page')

@vite([
    'resources/css/inventario/shared/base.css',
    'resources/css/inventario/productos.css',
    'resources/js/inventario/productos.js',
    'resources/css/inventario/shared/modal-imagen.css',
    'resources/js/inventario/shared/modal-imagen.js'
])


@section('content_header')
    <div class="header-container">
        <h1>Catálogo de Productos</h1>
        
        <!-- Barra de búsqueda con carrito -->
        <div class="search-container">
            <div class="search-box">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="searchProducts" class="search-input" placeholder="Buscar productos..." autocomplete="off">
                <button type="button" class="search-clear" id="clearSearch" style="display: none;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <a href="{{ route('inventario.carrito.index') }}" class="btn-lg carrito-icon-btn" title="Ver Carrito">
                <i class="fas fa-shopping-cart"></i>
            </a>
        </div>
        
        <div class="header-buttons">
            <a href="{{ route('inventario.productos.create') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-plus"></i> Nuevo Producto
            </a>
        </div>
    </div>
    <!-- Botón para escanear código de barras -->
    <div class="mt-3 text-right">
        <button class="btn btn-secondary btn-lg" data-toggle="modal" data-target="#modalEscanear">
            <i class="fas fa-barcode"></i> Escanear Código de Barras
        </button>
    </div>

@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="product-grid">
        @forelse($productos as $producto)
            <div class="product-card" data-product-id="{{ $producto->id }}">
                <div class="product-image-container">
                    @if($producto->imagen)
                        <img src="{{ asset($producto->imagen) }}" 
                            alt="{{ $producto->producto }}" 
                            class="product-image img-expandable">
                    @else
                        <div class="placeholder-image">
                            <img src="https://placehold.co/300x300?text={{ urlencode($producto->producto) }}" 
                                alt="Vista previa de {{ $producto->producto }}" 
                                class="product-image img-expandable">
                        </div>
                    @endif
                </div>
                <div class="product-info">
                    <h3 class="product-title">{{ $producto->producto }}</h3>
                    <div class="product-actions">
                        <a href="{{ route('inventario.productos.show', $producto->id) }}" 
                            class="btn btn-sm btn-info action-btn" 
                            title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('inventario.productos.edit', $producto->id) }}" 
                            class="btn btn-sm btn-warning action-btn" 
                            title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('inventario.productos.destroy', $producto->id) }}" 
                                method="POST" 
                                class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="btn btn-sm btn-danger action-btn" 
                                    title="Eliminar"
                                    onclick="return confirm('¿Estás seguro de eliminar este producto?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <img src="https://placehold.co/400x300?text=No+hay+productos" 
                    alt="No hay productos disponibles" 
                    class="empty-image">
                <h4>No hay productos registrados</h4>
                <a href="{{ route('productos.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Agregar primer producto
                </a>
            </div>
        @endforelse
    </div>

    
<!-- Modal para imagen expandida -->
<div id="modalImagen" class="modal-imagen">
    <span class="cerrar">&times;</span>
    <img class="modal-contenido" id="imgExpandida">
</div>

<!-- Modal para escanear código de barras -->
<div class="modal fade" id="modalEscanear" tabindex="-1" role="dialog" aria-labelledby="modalEscanearLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
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

<!-- Script para manejar el escaneo y búsqueda -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputCodigo = document.getElementById('inputCodigoBarras');
    const resultadoDiv = document.getElementById('resultadoBusqueda');

    // Enfocar automáticamente al abrir el modal
    $('#modalEscanear').on('shown.bs.modal', function () {
        inputCodigo.focus();
        inputCodigo.value = '';
        resultadoDiv.innerHTML = '';
    });

    // Detectar "Enter" después de escanear
    inputCodigo.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            const codigo = inputCodigo.value.trim();

            if (codigo === '') return;

            resultadoDiv.innerHTML = '<p class="text-info">🔍 Buscando producto...</p>';

            fetch(`/inventario/productos/buscar/${codigo}`)
                .then(response => {
                    if (!response.ok) throw new Error('Producto no encontrado');
                    return response.json();
                })
                .then(producto => {
                    resultadoDiv.innerHTML = `
                        <div class="alert alert-success mt-3">
                            ✅ Producto encontrado: <strong>${producto.producto}</strong>. Redirigiendo...
                        </div>
                    `;
                    setTimeout(() => {
                        window.location.href = `/inventario/productos/${producto.id}`;
                    }, 1000);
                })
                .catch(error => {
                    resultadoDiv.innerHTML = `
                        <div class="alert alert-danger mt-3">
                                No se encontró ningún producto con el código <strong>${codigo}</strong>.
                        </div>
                    `;
                });
        }
    });
});
</script>

@stop


