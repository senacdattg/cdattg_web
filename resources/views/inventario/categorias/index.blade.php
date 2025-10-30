
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
    <x-create-card
        url="#"
        title="Nueva Categoría"
        icon="fa-plus-circle"
        permission="CREAR CATEGORIA"
    />

    <x-data-table
        title="Lista de Categorías"
        searchable="true"
        searchAction="{{ route('inventario.categorias.index') }}"
        searchPlaceholder="Buscar categoría..."
        searchValue="{{ request('search') }}"
        :columns="[
            ['label' => '#', 'width' => '5%'],
            ['label' => 'Nombre', 'width' => '40%'],
            ['label' => 'Productos', 'width' => '15%'],
            ['label' => 'Estado', 'width' => '15%'],
            ['label' => 'Opciones', 'width' => '25%', 'class' => 'text-center']
        ]"
    >
        @forelse ($categorias as $categoria)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $categoria->nombre }}</td>
                <td>
                    <span class="badge badge-info">
                        {{ $categoria->productos_count ?? 0 }}
                    </span>
                </td>
                <td>
                    <x-status-badge
                        status="{{ $categoria->status ?? true }}"
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
                        showTitle="Ver categoría"
                        editTitle="Editar categoría"
                        deleteTitle="Eliminar categoría"
                    />
                </td>
            </tr>
        @empty
            <x-table-empty
                colspan="5"
                message="No hay categorías registradas"
                icon="fas fa-tags"
            />
        @endforelse
    </x-data-table>

    {{-- Modal de confirmación de eliminación --}}
    <x-confirm-delete-modal />
    
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
