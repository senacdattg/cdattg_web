@extends('inventario.layouts.page', [
    'title' => 'Gestión de Marcas',
    'subtitle' => 'Administra las marcas del inventario',
    'icon' => 'fas fa-trademark',
    'showSearch' => true,
    'searchPlaceholder' => 'Buscar marcas...'
])

@section('page-content')
    <div class="d-flex justify-content-end mb-3">
        <button type="button" 
                class="btn btn-primary btn-lg" 
                data-toggle="modal" 
                data-target="#createMarcaModal">
            <i class="fas fa-plus mr-2"></i> Nueva Marca
        </button>
    </div>
    {{-- Tabla usando componente genérico --}}
    @component('inventario._components.data-table', [
        'headers' => [
            '#' => '#',
            'nombre' => 'Marca',
            'productos_count' => 'Productos',
            'status' => 'Estado'
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
