
@extends('adminlte::page')

@section('title', 'Gestión de Categorías')

@section('content_header')
    <x-page-header
        icon="fas fa-tags"
        title="Gestión de Categorías"
        subtitle="Administra las categorías del inventario"
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
        'actions' => ['delete'],
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
        'resources/js/inventario/categorias.js'
    ])
@endsection
