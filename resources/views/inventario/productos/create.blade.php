
@extends('inventario.layouts.form', [
    'title' => 'Registrar Producto',
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

