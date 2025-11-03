@extends('inventario.layouts.base')

@section('title', 'Historial de Devoluciones')

@section('content')
<div class="container-fluid">
    @include('inventario._components.page-header', [
        'title' => 'Historial de Devoluciones',
        'subtitle' => 'Registro completo de todas las devoluciones realizadas',
        'breadcrumb' => [
            ['text' => 'Inventario', 'url' => route('inventario.dashboard')],
            ['text' => 'Devoluciones', 'active' => true]
        ]
    ])

    @include('inventario._components.alerts')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Historial Completo</h5>
                </div>
                <div class="card-body">
                    @if($devoluciones->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Producto</th>
                                        <th>Cantidad Devuelta</th>
                                        <th>Fecha Devolución</th>
                                        <th>Estado</th>
                                        <th>Registrado por</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($devoluciones as $devolucion)
                                        <tr>
                                            <td>{{ $devolucion->id }}</td>
                                            <td>
                                                <strong>{{ $devolucion->detalleOrden->producto->producto }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $devolucion->detalleOrden->producto->descripcion }}</small>
                                            </td>
                                            <td>{{ $devolucion->cantidad_devuelta }}</td>
                                            <td>{{ $devolucion->fecha_devolucion->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @if($devolucion->detalleOrden->orden->fecha_devolucion)
                                                    @if($devolucion->fecha_devolucion->lte($devolucion->detalleOrden->orden->fecha_devolucion))
                                                        <span class="badge badge-success">A Tiempo</span>
                                                    @else
                                                        <span class="badge badge-warning">
                                                            Retraso ({{ $devolucion->fecha_devolucion->diffInDays($devolucion->detalleOrden->orden->fecha_devolucion) }} días)
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="badge badge-secondary">Sin fecha límite</span>
                                                @endif
                                            </td>
                                            <td>{{ $devolucion->userCreate->name ?? 'Usuario no encontrado' }}</td>
                                            <td>
                                                <a href="{{ route('inventario.devoluciones.show', $devolucion->id) }}"
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> Ver
                                                </a>
                                                <a href="{{ route('inventario.ordenes.show', $devolucion->detalleOrden->orden->id) }}"
                                                   class="btn btn-sm btn-secondary">
                                                    <i class="fas fa-external-link-alt"></i> Orden
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($devoluciones instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
                            <div class="d-flex justify-content-center">
                                {{ $devoluciones->links() }}
                            </div>
                        @endif
                    @else
                        @include('inventario._components.empty-state', [
                            'title' => 'No hay devoluciones registradas',
                            'message' => 'Aún no se han realizado devoluciones en el sistema.',
                            'icon' => 'fas fa-history'
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