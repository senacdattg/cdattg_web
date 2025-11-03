@extends('inventario.layouts.base')

@section('title', 'Mis Préstamos Activos')

@section('content')
<div class="container-fluid">
    @include('inventario._components.page-header', [
        'title' => 'Mis Préstamos Activos',
        'subtitle' => 'Productos que tienes prestados actualmente',
        'breadcrumb' => [
            ['text' => 'Mis Préstamos', 'active' => true]
        ]
    ])

    @include('inventario._components.alerts')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Préstamos Activos</h5>
                </div>
                <div class="card-body">
                    @if($prestamos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Cantidad Prestada</th>
                                        <th>Cantidad Devuelta</th>
                                        <th>Cantidad Pendiente</th>
                                        <th>Fecha Préstamo</th>
                                        <th>Fecha Devolución Esperada</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($prestamos as $detalle)
                                        <tr>
                                            <td>
                                                <strong>{{ $detalle->producto->producto }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $detalle->producto->descripcion }}</small>
                                            </td>
                                            <td>{{ $detalle->cantidad }}</td>
                                            <td>{{ $detalle->getCantidadDevuelta() }}</td>
                                            <td>
                                                <span class="badge badge-warning">
                                                    {{ $detalle->getCantidadPendiente() }}
                                                </span>
                                            </td>
                                            <td>{{ $detalle->orden->fecha_prestamo ? $detalle->orden->fecha_prestamo->format('d/m/Y') : 'N/A' }}</td>
                                            <td>
                                                @if($detalle->orden->fecha_devolucion)
                                                    {{ $detalle->orden->fecha_devolucion->format('d/m/Y') }}
                                                    @if($detalle->Vencido())
                                                        <br><small class="text-danger">{{ $detalle->getDiasRetraso() }} días de retraso</small>
                                                    @else
                                                        <br><small class="text-success">{{ $detalle->getDiasRestantes() }} días restantes</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">Sin fecha</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($detalle->Vencido())
                                                    <span class="badge badge-danger">Vencido</span>
                                                @else
                                                    <span class="badge badge-success">En tiempo</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('inventario.devoluciones.create', $detalle->id) }}"
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-undo"></i> Devolver
                                                </a>
                                                <a href="{{ route('inventario.ordenes.show', $detalle->orden->id) }}"
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> Ver Orden
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($prestamos instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
                            <div class="d-flex justify-content-center">
                                {{ $prestamos->links() }}
                            </div>
                        @endif
                    @else
                        @include('inventario._components.empty-state', [
                            'title' => 'No tienes préstamos activos',
                            'message' => 'Todos tus préstamos han sido devueltos completamente.',
                            'icon' => 'fas fa-check-circle'
                        ])
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

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

