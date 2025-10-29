@extends('adminlte::page')

@section('title', 'Gestión de Marcas')

@section('content_header')
    <x-page-header
        icon="fas fa-trademark"
        title="Gestión de Marcas"
        subtitle="Administra las marcas del inventario"
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
        'actions' => ['delete'],
        'emptyMessage' => 'Sin marcas registradas.',
        'emptyIcon' => 'fas fa-tags',
        'tableClass' => 'marcas-table',
        'entityType' => 'marcas'
    ])
    @endcomponent

    {{-- Paginación --}}
    <div id="pagination-container" class="mt-3"></div>
    
    {{-- Modales --}}
    @include('inventario.marcas._modals')
    
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
        'resources/js/inventario/marcas.js'
    ])
@endsection
