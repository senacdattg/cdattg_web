@extends('inventario.layouts.base')

@push('styles')
    @vite([
        'resources/css/inventario/productos.css',
        'resources/css/inventario/shared/modal-imagen.css',
        'resources/js/inventario/productos.js'
    ])
@endpush

@section('classes_body', 'productos-page')

@section('header')
    @include('inventario._components.page-header', [
        'title' => 'Catálogo de Productos',
        'icon' => 'fas fa-box',
        'showSearch' => true,
        'showCart' => true,
        'searchPlaceholder' => 'Buscar productos...',
        'createRoute' => route('inventario.productos.create'),
        'createText' => 'Nuevo Producto'
    ])
@endsection

@section('main-content')
    <div class="product-grid" id="productGrid">
        @forelse($productos as $producto)
            @include('inventario._components.product-card', ['producto' => $producto])
        @empty
            @include('inventario._components.empty-state', [
                'message' => 'No hay productos registrados',
                'icon' => 'fas fa-box-open',
                'actionRoute' => route('inventario.productos.create'),
                'actionText' => 'Agregar primer producto'
            ])
        @endforelse
    </div>

    {{-- Modal para imagen expandible --}}
    @include('inventario._components.image-modal')
@endsection