
@extends('inventario.layouts.page', [
    'title' => 'Gestión de Categorías',
    'subtitle' => 'Administra las categorías del inventario',
    'icon' => 'fas fa-tags',
    'showSearch' => true,
    'searchPlaceholder' => 'Buscar categorías...'
])

@section('page-content')
    <div class="d-flex justify-content-end mb-3">
        <button type="button" 
                class="btn btn-primary btn-lg" 
                data-toggle="modal" 
                data-target="#createCategoriaModal">
            <i class="fas fa-plus mr-2"></i> Nueva Categoría
        </button>
    </div>
    {{-- Tabla usando componente genérico --}}
    @component('inventario._components.data-table', [
        'headers' => [
            '#' => '#',
            'nombre' => 'Categoria',
            'productos_count' => 'Productos',
            'status' => 'Estado'
        ],
        'data' => $categorias,
        'actions' => ['view', 'edit', 'delete'],
        'emptyMessage' => 'Sin categorías registradas.',
        'emptyIcon' => 'fas fa-inbox',
        'tableClass' => 'categorias-table',
        'entityType' => 'categorias'
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
