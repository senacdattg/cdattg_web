@extends('inventario.layouts.page', [
    'title' => 'Gestión de Marcas',
    'subtitle' => 'Administra las marcas del inventario',
    'icon' => 'fas fa-trademark',
    'showSearch' => true,
    'searchPlaceholder' => 'Buscar marcas...',
    'createRoute' => route('inventario.marcas.create'),
    'createText' => 'Nueva Marca'
])

@section('page-content')
    {{-- Tabla usando componente genérico --}}
    @component('inventario._components.data-table', [
        'headers' => [
            '#' => '#',
            'nombre' => 'Marca',
            'productos_count' => 'Productos',
            'estado' => 'Estado'
        ],
        'data' => $marcas,
        'actions' => ['view', 'edit', 'delete'],
        'emptyMessage' => 'Sin marcas registradas.',
        'emptyIcon' => 'fas fa-tags',
        'tableClass' => 'marcas-table'
    ])
    @endcomponent

    {{-- Paginación --}}
    <div id="pagination-container" class="mt-3"></div>

    {{-- Modales --}}
    @include('inventario.marcas._modals')
@endsection

@section('additional-scripts')
    @vite([
        'resources/js/inventario/marcas.js'
    ])
@endsection
