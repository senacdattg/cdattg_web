@extends('inventario.layouts.page', [
    'title' => 'Gestión de Categorías',
    'subtitle' => 'Administra las categorías del inventario',
    'icon' => 'fas fa-tags',
    'showSearch' => true,
    'searchPlaceholder' => 'Buscar categorías...',
    'createRoute' => route('inventario.categorias.create'),
    'createText' => 'Nueva Categoría'
])

@section('page-content')
    {{-- Tabla usando componente genérico --}}
    @component('inventario._components.data-table', [
        'headers' => [
            '#' => '#',
            'nombre' => 'Categoria',
            'productos_count' => 'Productos',
            'estado' => 'Estado'
        ],
        'data' => $categorias,
        'actions' => ['view', 'edit', 'delete'],
        'emptyMessage' => 'Sin categorías registradas.',
        'emptyIcon' => 'fas fa-inbox',
        'tableClass' => 'categorias-table'
    ])
    @endcomponent

    {{-- Paginación --}}
    <div id="pagination-container" class="mt-3"></div>

    {{-- Modales --}}
    @include('inventario.categorias._modals')
@endsection

@section('additional-scripts')
    @vite([
        'resources/js/inventario/categorias.js'
    ])
@endsection
