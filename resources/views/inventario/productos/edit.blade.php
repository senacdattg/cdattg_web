
@extends('inventario.layouts.base')

@push('styles')
    @vite([
        'resources/css/inventario/shared/base.css',
        'resources/css/inventario/productos.css',
        'resources/css/inventario/shared/modal-imagen.css'
    ])
@endpush

@section('content_header')
    <h1>Editar Producto</h1>
@endsection

@section('main-content')
    <div class="container inventario-container">
        @include('inventario._components.product-form', [
            'action' => route('productos.update', $producto->id),
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
    </div>
@endsection

@push('scripts')
    @vite([
        'resources/js/inventario/productos.js',
        'resources/js/inventario/shared/modal-imagen.js'
    ])
@endpush
