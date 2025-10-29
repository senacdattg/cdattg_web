@extends('adminlte::page')

@section('title', 'Gestión de Contratos & Convenios')

@section('content_header')
    <x-page-header
        icon="fas fa-file-contract"
        title="Gestión de Contratos & Convenios"
        subtitle="Administra los contratos y convenios del inventario"
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
                data-target="#createContratoModal">
            <i class="fas fa-plus mr-2"></i> Nuevo Contrato/Convenio
        </button>
    </div>
    {{-- Tabla usando componente genérico --}}
    @component('inventario._components.data-table', [
        'headers' => [
            '#' => '#',
            'name' => 'Nombre',
            'codigo' => 'Código',
            'fecha_inicio' => 'Fecha Inicio',
            'fecha_fin' => 'Fecha Fin',
            'vigencia' => 'Vigencia',
            'proveedor' => 'Proveedor',
            'status' => 'Estado'
        ],
        'data' => $contratosConvenios,
        'actions' => ['delete'],
        'emptyMessage' => 'Sin contratos/convenios registrados.',
        'emptyIcon' => 'fas fa-file-contract',
        'tableClass' => 'contratos-table',
        'entityType' => 'contratos-convenios'
    ])
    @endcomponent

    {{-- Paginación --}}
    <div id="pagination-container" class="mt-3"></div>
    
    {{-- Modales --}}
    @include('inventario.contratos_convenios._modals')
    
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
        'resources/js/inventario/contratos_convenios.js'
    ])
@endsection
