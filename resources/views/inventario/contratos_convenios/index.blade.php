@extends('inventario.layouts.page', [
    'title' => 'Gestión de Contratos & Convenios',
    'subtitle' => 'Administra los contratos y convenios del inventario',
    'icon' => 'fas fa-file-contract',
    'showSearch' => true,
    'searchPlaceholder' => 'Buscar contratos/convenios...',
    'createRoute' => route('inventario.contratos-convenios.create'),
    'createText' => 'Nuevo Contrato/Convenio'
])

@section('page-content')
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
            'estado' => 'Estado'
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
