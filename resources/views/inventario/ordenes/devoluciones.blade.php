@extends('adminlte::page')

@section('title', 'Devoluciones')

@section('content_header')
    <x-page-header
        icon="fas fa-undo"
        title="Devoluciones"
        subtitle="Productos devueltos o reintegrados"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Órdenes', 'url' => route('inventario.ordenes.index')],
            ['label' => 'Devoluciones', 'active' => true]
        ]"
    />
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header with-border">
                        <h3 class="card-title">
                            <i class="fas fa-undo"></i> Listado de Devoluciones
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($devoluciones && count($devoluciones) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Orden</th>
                                            <th>Producto</th>
                                            <th>Cantidad</th>
                                            <th>Fecha</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($devoluciones as $devolucion)
                                            <tr>
                                                <td>{{ $devolucion->id }}</td>
                                                <td>{{ $devolucion->orden_id ?? 'N/A' }}</td>
                                                <td>{{ $devolucion->producto->producto ?? 'N/A' }}</td>
                                                <td>{{ $devolucion->cantidad }}</td>
                                                <td>{{ $devolucion->created_at->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <span class="badge badge-success">Completada</span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('inventario.ordenes.show', $devolucion->orden_id) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">
                                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                                    <p>No hay devoluciones registradas</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                No hay devoluciones para mostrar
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    @include('layout.footer')
@endsection
@push('css')
    @vite(['public/css/inventario/shared/base.css'])
@endpush
