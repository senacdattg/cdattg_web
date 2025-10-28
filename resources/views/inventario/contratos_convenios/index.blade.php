@extends('inventario.layouts.page', [
    'title' => 'Gestión de Contratos & Convenios',
    'subtitle' => 'Administra los contratos y convenios del inventario',
    'icon' => 'fas fa-file-contract',
    'showSearch' => true,
    'searchPlaceholder' => 'Buscar contratos/convenios...'
])

@section('page-content')
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
        'actions' => ['view', 'edit', 'delete'],
        'emptyMessage' => 'Sin contratos/convenios registrados.',
        'emptyIcon' => 'fas fa-file-contract',
        'tableClass' => 'contratos-table'
    ])
    @endcomponent

    {{-- Paginación --}}
    <div id="pagination-container" class="mt-3"></div>
    
    {{-- Modales --}}
    @include('inventario.contratos_convenios._modals')
@endsection

@section('additional-scripts')
    @vite([
        'resources/js/inventario/contratos_convenios.js'
    ])
@endsection
