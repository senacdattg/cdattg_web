@extends('adminlte::page')

@section('title', 'Gestión de Productos')

@section('content_header')
    <x-page-header
        icon="fas fa-boxes"
        title="Gestión de Productos"
        subtitle="Administra los productos del inventario"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true]
        ]"
    />
@endsection

@section('content')
    
    {{-- Alertas --}}
    @include('layout.alertas')

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
    
    {{-- Footer SENA --}}
    @include('inventario._components.sena-footer')
@endsection

@push('css')
    @vite(['resources/css/style.css'])
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush