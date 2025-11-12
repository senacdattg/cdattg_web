@extends('adminlte::page')

@section('title', 'Gestión de Órdenes')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush

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
                   {{-- Script para limpiar carrito si viene de una orden exitosa --}}
                    @if(session('clear_cart'))
                        <script>
                            // Limpiar carrito del localStorage después de crear orden exitosamente
                            localStorage.removeItem('inventario_carrito');
                            localStorage.removeItem('inventario_draft');
                            sessionStorage.removeItem('carrito_data');
                        </script>
                    @endif
                    
                    <x-data-table
                        title="Lista de Órdenes"
                        searchable="true"
                        searchAction="{{ route('inventario.ordenes.index') }}"
                        searchPlaceholder="Buscar orden..."
                        searchValue="{{ request('search') }}"
                        :columns="[
                            ['label' => '#', 'width' => '5%'],
                            ['label' => 'ID', 'width' => '5%'],
                            ['label' => 'Usuario', 'width' => '15%'],
                            ['label' => 'Tipo', 'width' => '10%'],
                            ['label' => 'Productos', 'width' => '10%'],
                            ['label' => 'Estado', 'width' => '10%'],
                            ['label' => 'F. Devolución', 'width' => '12%'],
                            ['label' => 'F. Creación', 'width' => '12%'],
                            ['label' => 'Opciones', 'width' => '11%', 'class' => 'text-center']
                        ]"
                        :pagination="$ordenes->links()"
                    >
                        @forelse ($ordenes as $orden)
                            @php
                                // Obtener tipo de orden
                                $tipoNombre = $orden->tipoOrden->parametro->name ?? 'N/A';
                                $tipoClass = $tipoNombre === 'PRÉSTAMO' ? 'info' : 'warning';
                                
                                // Obtener estado del primer detalle (todas las órdenes comparten estado)
                                $estadoNombre = $orden->detalles->first()->estadoOrden->parametro->name ?? 'N/A';
                                $estadoClass = match($estadoNombre) {
                                    'EN ESPERA' => 'warning',
                                    'APROBADA' => 'success',
                                    'RECHAZADA' => 'danger',
                                    default => 'secondary'
                                };
                                
                                // Contar productos
                                $totalProductos = $orden->detalles->count();
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><span class="badge badge-secondary">{{ $orden->id }}</span></td>
                                <td>
                                    <i class="fas fa-user-circle text-primary"></i>
                                    {{ $orden->userCreate->name ?? 'N/A' }}
                                </td>
                                <td>
                                    <span class="badge badge-{{ $tipoClass }}">
                                        <i class="fas fa-{{ $tipoNombre === 'PRÉSTAMO' ? 'handshake' : 'sign-out-alt' }}"></i>
                                        {{ $tipoNombre }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-primary">
                                        <i class="fas fa-boxes"></i> {{ $totalProductos }}
                                        {{ $totalProductos === 1 ? 'producto' : 'productos' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $estadoClass }}">
                                        <i class="fas fa-{{ $estadoNombre === 'EN ESPERA' ? 'clock' : ($estadoNombre === 'APROBADA' ? 'check-circle' : 'times-circle') }}"></i>
                                        {{ $estadoNombre }}
                                    </span>
                                </td>
                                <td>
                                    @if($orden->fecha_devolucion)
                                        <span class="badge badge-light">
                                            <i class="fas fa-calendar-alt"></i>
                                            {{ $orden->fecha_devolucion->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-light">
                                        <i class="fas fa-calendar-plus"></i>
                                        {{ $orden->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('inventario.ordenes.show', $orden) }}"
                                       class="btn btn-sm btn-info"
                                       title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($estadoNombre === 'EN ESPERA')
                                        <a href="{{ route('inventario.aprobaciones.pendientes') }}"
                                           class="btn btn-sm btn-warning"
                                           title="Gestionar">
                                            <i class="fas fa-tasks"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <x-table-empty
                                colspan="9"
                                message="No hay órdenes registradas"
                                icon="fas fa-list"
                            />
                        @endforelse
                    </x-data-table>
                    <div class="float-left pt-2">
                        <small class="text-muted">
                            Mostrando {{ $ordenes->firstItem() ?? 0 }} a {{ $ordenes->lastItem() ?? 0 }}
                            de {{ $ordenes->total() }} órdenes
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Alertas --}}
    @include('layout.alertas')
@endsection

@section('footer')
    {{-- Footer SENA --}}
    @include('layout.footer')
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush

