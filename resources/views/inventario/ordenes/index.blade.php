@extends('adminlte::page')

@section('title', 'Gestión de Órdenes')

@section('content_header')
    <x-page-header
        icon="fas fa-list"
        title="Gestión de Órdenes"
        subtitle="Administra las órdenes del inventario"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Órdenes', 'active' => true]
        ]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <x-create-card
                        url="inventario.ordenes.prestamos_salidas"
                        title="Nueva Orden"
                        icon="fa-plus-circle"
                        permission="CREAR ORDEN"
                    />
                    
                    <x-data-table
                        title="Lista de Órdenes"
                        searchable="true"
                        searchAction="{{ route('inventario.ordenes.index') }}"
                        searchPlaceholder="Buscar orden..."
                        searchValue="{{ request('search') }}"
                        :columns="[
                            ['label' => '#', 'width' => '5%'],
                            ['label' => 'Descripción', 'width' => '25%'],
                            ['label' => 'Tipo', 'width' => '15%'],
                            ['label' => 'Fecha Devolución', 'width' => '15%'],
                            ['label' => 'Usuario', 'width' => '15%'],
                            ['label' => 'Estado', 'width' => '10%'],
                            ['label' => 'Opciones', 'width' => '15%', 'class' => 'text-center']
                        ]"
                        :pagination="$ordenes->links()"
                    >
                        @forelse ($ordenes as $orden)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ Str::limit($orden->descripcion_orden, 50) }}</td>
                                <td>
                                    <span class="badge badge-secondary">
                                        {{ $orden->tipoOrden->parametro->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>{{ $orden->fecha_devolucion ? $orden->fecha_devolucion->format('d/m/Y') : 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-primary">
                                        <i class="fas fa-user-circle"></i> {{ $orden->userCreate->name ?? 'Usuario' }}
                                    </span>
                                </td>
                                <td>
                                    <x-status-badge
                                        status="true"
                                        activeText="ACTIVA"
                                        inactiveText="INACTIVA"
                                    />
                                </td>
                                <td class="text-center">
                                    <x-action-buttons
                                        show="true"
                                        edit="true"
                                        delete="true"
                                        showUrl="{{ route('inventario.ordenes.show', $orden) }}"
                                        editUrl="#"
                                        deleteUrl="#"
                                        showTitle="Ver orden"
                                        editTitle="Editar orden"
                                        deleteTitle="Eliminar orden"
                                    />
                            </tr>
                        @empty
                            <x-table-empty
                                colspan="7"
                                message="No hay órdenes registradas"
                                icon="fas fa-list"
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
@endsection

@section('footer')
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
