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
    <x-create-card
        url="#"
        title="Nuevo Proveedor"
        icon="fa-plus-circle"
        permission="CREAR PROVEEDOR"
    />

    <x-data-table
        title="Lista de Proveedores"
        searchable="true"
        searchAction="{{ route('inventario.proveedores.index') }}"
        searchPlaceholder="Buscar proveedor..."
        searchValue="{{ request('search') }}"
        :columns="[
            ['label' => '#', 'width' => '5%'],
            ['label' => 'Proveedor', 'width' => '25%'],
            ['label' => 'NIT', 'width' => '15%'],
            ['label' => 'Email', 'width' => '20%'],
            ['label' => 'Contratos', 'width' => '10%'],
            ['label' => 'Estado', 'width' => '10%'],
            ['label' => 'Opciones', 'width' => '15%', 'class' => 'text-center']
        ]"
    >
        @forelse ($proveedores as $proveedor)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $proveedor->proveedor }}</td>
                <td>{{ $proveedor->nit ?? 'N/A' }}</td>
                <td>{{ $proveedor->email ?? 'N/A' }}</td>
                <td>
                    <span class="badge badge-info">
                        {{ $proveedor->contratos_convenios_count ?? 0 }}
                    </span>
                </td>
                <td>
                    <x-status-badge
                        status="{{ $proveedor->status ?? true }}"
                        activeText="ACTIVO"
                        inactiveText="INACTIVO"
                    />
                </td>
                <td class="text-center">
                    <x-action-buttons
                        show="true"
                        edit="true"
                        delete="true"
                        showUrl="#"
                        editUrl="#"
                        deleteUrl="#"
                        showTitle="Ver proveedor"
                        editTitle="Editar proveedor"
                        deleteTitle="Eliminar proveedor"
                    />
                </td>
            </tr>
        @empty
            <x-table-empty
                colspan="7"
                message="No hay proveedores registrados"
                icon="fas fa-truck"
            />
        @endforelse
    </x-data-table>

    {{-- Modal de confirmación de eliminación --}}
    <x-confirm-delete-modal />
    
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
