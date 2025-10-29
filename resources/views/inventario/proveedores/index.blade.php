@extends('adminlte::page')

@section('title', 'Gestión de Proveedores')

@section('content_header')
    <x-page-header
        icon="fas fa-truck"
        title="Gestión de Proveedores"
        subtitle="Administra los proveedores del inventario"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true]
        ]"
    />
@endsection

@section('content')
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
        'actions' => ['delete'],
        'emptyMessage' => 'Sin proveedores registrados.',
        'emptyIcon' => 'fas fa-truck',
        'tableClass' => 'proveedores-table',
        'entityType' => 'proveedores'
    ])
    @endcomponent

    {{-- Paginación --}}
    <div id="pagination-container" class="mt-3"></div>
    
    {{-- Modales --}}
    @include('inventario.proveedores._modals')
    
    {{-- Alertas --}}
    @include('layout.alertas')
    
    {{-- Footer SENA --}}
    @include('inventario._components.sena-footer')
@endsection

@push('css')
    @vite(['resources/css/style.css'])
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('additional-scripts')
    @vite([
        'resources/js/inventario/proveedores.js'
    ])
@endsection
