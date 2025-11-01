@extends('adminlte::page')

@section('title', 'Gestión de Proveedores')

@section('content_header')
    <x-page-header
        icon="fas fa-truck"
        title="Gestión de Proveedores"
        subtitle="Administra los proveedores del inventario"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Proveedores', 'active' => true]
        ]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <x-create-card
                        url="{{ route('inventario.proveedores.create') }}"
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
                            ['label' => '#', 'width' => '3%'],
                            ['label' => 'Proveedor', 'width' => '12%'],
                            ['label' => 'NIT', 'width' => '8%'],
                            ['label' => 'Email', 'width' => '12%'],
                            ['label' => 'Teléfono', 'width' => '8%'],
                            ['label' => 'Dirección', 'width' => '12%'],
                            ['label' => 'Departamento', 'width' => '9%'],
                            ['label' => 'Municipio', 'width' => '10%'],
                            ['label' => 'Contacto', 'width' => '10%'],
                            ['label' => 'Contratos', 'width' => '6%'],
                            ['label' => 'Estado', 'width' => '8%'],
                            ['label' => 'Opciones', 'width' => '11%', 'class' => 'text-center']
                        ]"
                        :pagination="$proveedores->links()"
                    >
                        @forelse ($proveedores as $proveedor)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $proveedor->proveedor }}</td>
                                <td>{{ $proveedor->nit ?? 'N/A' }}</td>
                                <td>{{ $proveedor->email ?? 'N/A' }}</td>
                                <td>{{ $proveedor->telefono ?? 'N/A' }}</td>
                                <td>{{ $proveedor->direccion ?? 'N/A' }}</td>
                                <td>{{ $proveedor->departamento->departamento ?? 'N/A' }}</td>
                                <td>{{ $proveedor->municipio->municipio ?? 'N/A' }}</td>
                                <td>{{ $proveedor->contacto ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ $proveedor->contratos_convenios_count ?? 0 }}
                                    </span>
                                </td>
                                <td>
                                    @if($proveedor->estado)
                                        <span class="badge badge-{{ $proveedor->estado->status == 1 ? 'success' : 'danger' }}">
                                            {{ $proveedor->estado->parametro->name ?? 'N/A' }}
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">SIN ESTADO</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <x-action-buttons
                                        show="true"
                                        edit="true"
                                        delete="true"
                                        showUrl="{{ route('inventario.proveedores.show', $proveedor->id) }}"
                                        editUrl="{{ route('inventario.proveedores.edit', $proveedor->id) }}"
                                        deleteUrl="{{ route('inventario.proveedores.destroy', $proveedor->id) }}"
                                        showTitle="Ver proveedor"
                                        editTitle="Editar proveedor"
                                        deleteTitle="Eliminar proveedor"
                                    />
                                </td>
                            </tr>
                        @empty
                            <x-table-empty
                                colspan="11"
                                message="No hay proveedores registrados"
                                icon="fas fa-truck"
                            />
                        @endforelse
                    </x-data-table>
                </div>
            </div>
        </div>
    </section>

    {{-- Modal de confirmación de eliminación --}}
    <x-confirm-delete-modal />
    
    {{-- Alertas --}}
    @include('layout.alertas')
    
    {{-- Footer SENA --}}
    @include('layout.footer')
@endsection

@push('css')
    @vite([
        'public/css/inventario/shared/base.css',
    ])
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/pages/formularios-generico.js'])
@endpush
