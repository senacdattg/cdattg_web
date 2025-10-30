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
    <x-create-card
        url="#"
        title="Nueva Marca"
        icon="fa-plus-circle"
        permission="CREAR MARCA"
    />

    <x-data-table
        title="Lista de Marcas"
        searchable="true"
        searchAction="{{ route('inventario.marcas.index') }}"
        searchPlaceholder="Buscar marca..."
        searchValue="{{ request('search') }}"
        :columns="[
            ['label' => '#', 'width' => '5%'],
            ['label' => 'Nombre', 'width' => '40%'],
            ['label' => 'Productos', 'width' => '15%'],
            ['label' => 'Estado', 'width' => '15%'],
            ['label' => 'Opciones', 'width' => '25%', 'class' => 'text-center']
        ]"
    >
        @forelse ($marcas as $marca)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $marca->nombre }}</td>
                <td>
                    <span class="badge badge-info">
                        {{ $marca->productos_count ?? 0 }}
                    </span>
                </td>
                <td>
                    <x-status-badge
                        status="{{ $marca->status ?? true }}"
                        activeText="ACTIVA"
                        inactiveText="INACTIVA"
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
                        showTitle="Ver marca"
                        editTitle="Editar marca"
                        deleteTitle="Eliminar marca"
                    />
                </td>
            </tr>
        @empty
            <x-table-empty
                colspan="5"
                message="No hay marcas registradas"
                icon="fas fa-trademark"
            />
        @endforelse
    </x-data-table>

    {{-- Modal de confirmación de eliminación --}}
    <x-confirm-delete-modal />
    
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
