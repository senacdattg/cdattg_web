
@extends('adminlte::page')

@section('title', 'Editar Producto')

@section('content_header')
    <x-page-header
        icon="fas fa-edit"
        title="Editar Producto"
        subtitle="Modificar datos del producto"
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
    'icon' => 'fas fa-edit',
    'action' => route('inventario.productos.update', $producto->id),
    'method' => 'PUT',
    'submitText' => 'Guardar cambios',
    'cancelRoute' => route('inventario.productos.index'),
    'cancelText' => 'Cancelar',
    'showReset' => true,
    'resetText' => 'Limpiar'
])

@section('form-content')
    @include('inventario._components.product-form', [
        'action' => route('inventario.productos.update', $producto->id),
        'method' => 'PUT',
        'producto' => $producto,
        'tiposProductos' => $tiposProductos,
        'unidadesMedida' => $unidadesMedida,
        'estados' => $estados,
        'contratosConvenios' => $contratosConvenios,
        'categorias' => $categorias,
        'marcas' => $marcas,
        'ambientes' => $ambientes,
        'submitText' => 'Guardar cambios',
        'title' => 'Editar Producto'
    ])
@endsection

@section('form-scripts')
    @vite([
        'resources/js/inventario/productos.js',
        'resources/js/inventario/shared/modal-imagen.js'
    ])
@endsection

