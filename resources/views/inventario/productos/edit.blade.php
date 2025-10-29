
@extends('inventario.layouts.form', [
    'title' => 'Editar Producto',
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

