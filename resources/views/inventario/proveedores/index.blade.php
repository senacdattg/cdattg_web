@extends('inventario.layouts.page', [
        'title' => 'Gestión de Proveedores',
        'subtitle' => 'Administra los proveedores del inventario',
        'icon' => 'fas fa-truck',
    'showSearch' => true,
    'searchPlaceholder' => 'Buscar proveedores...'
])

@section('page-content')
    <div class="d-flex justify-content-end mb-3">
        <button type="button" 
                class="btn btn-primary btn-lg" 
                data-toggle="modal" 
                data-target="#createProveedorModal">
            <i class="fas fa-plus mr-2"></i> Nuevo Proveedor
                                </button>
    </div>
    {{-- Tabla usando componente genérico --}}
    @component('inventario._components.data-table', [
        'headers' => [
            '#' => '#',
            'proveedor' => 'Proveedor',
            'nit' => 'NIT',
            'contacto' => 'Contacto',
            'contratos_convenios_count' => 'Contratos',
            'status' => 'Estado'
        ],
        'data' => $proveedores,
        'actions' => ['view', 'edit', 'delete'],
        'emptyMessage' => 'Sin proveedores registrados.',
        'emptyIcon' => 'fas fa-truck',
        'tableClass' => 'proveedores-table'
    ])
    @endcomponent

    {{-- Paginación --}}
    <div id="pagination-container" class="mt-3"></div>

    {{-- Modales --}}
    @include('inventario.proveedores._modals')
@endsection

@section('additional-scripts')
    @vite([
        'resources/js/inventario/proveedores.js'
    ])
@endsection
