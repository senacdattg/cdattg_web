
@extends('adminlte::page')

@section('title', 'Registrar Producto')

@section('content_header')
    <x-page-header
        icon="fas fa-plus"
        title="Registrar Producto"
        subtitle="Crear un nuevo producto en el inventario"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true]
        ]"
    />
@endsection

@section('content')
    
    {{-- Alertas --}}
    @include('layout.alertas')
    
    {{-- Footer SENA --}}
    @include('inventario._components.sena-footer')
    
@push('css')
    @vite(['resources/css/style.css'])
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush
    'icon' => 'fas fa-plus',
    'action' => route('inventario.productos.store'),
    'method' => 'POST',
    'submitText' => 'Guardar',
    'cancelRoute' => route('inventario.productos.index'),
    'cancelText' => 'Cancelar',
    'showReset' => true,
    'resetText' => 'Limpiar'
])

@section('form-content')
    @include('inventario._components.product-form', [
        'action' => route('inventario.productos.store'),
        'method' => 'POST',
        'producto' => null,
        'tiposProductos' => $tiposProductos,
        'unidadesMedida' => $unidadesMedida,
        'estados' => $estados,
        'contratosConvenios' => $contratosConvenios,
        'categorias' => $categorias,
        'marcas' => $marcas,
        'ambientes' => $ambientes
    ])
@endsection

@section('form-scripts')
    @vite([
        'resources/js/inventario/productos.js',
        'resources/js/inventario/shared/modal-imagen.js'
    ])
@endsection

