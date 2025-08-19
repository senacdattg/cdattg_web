@extends('adminlte::page')

@section('classes_body', 'productos-page')

@vite([
    'resources/css/inventario/productos.css',
    'resources/js/inventario/productos.js',
    'resources/css/inventario/shared/modal-imagen.css',
    'resources/js/inventario/shared/modal-imagen.js'
])


@section('content_header')
    <div class="header-container">
        <h1>Catálogo de Productos</h1>
        <a href="{{ route('productos.create') }}" class="btn btn-primary btn-lg">
            <i class="fas fa-plus"></i> Nuevo Producto
        </a>
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
                             class="product-image">
                    @else
                        <div class="placeholder-image">
                            <img src="https://placehold.co/300x300?text={{ urlencode($producto->producto) }}" 
                                 alt="Vista previa de {{ $producto->producto }}" 
                                 class="product-image">
                        </div>
                    @endif
                </div>
                <div class="product-info">
                    <h3 class="product-title">{{ $producto->producto }}</h3>
                    <div class="product-actions">
                        <a href="{{ route('productos.show', $producto->id) }}" 
                           class="btn btn-sm btn-info action-btn" 
                           title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('productos.edit', $producto->id) }}" 
                           class="btn btn-sm btn-warning action-btn" 
                           title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('productos.destroy', $producto->id) }}" 
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
@stop


