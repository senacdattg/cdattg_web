@extends('adminlte::page')

@section('title', 'Historial de Mis Préstamos')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush

@section('content')
<div class="container-fluid">
    @include('inventario._components.page-header', [
        'title' => 'Historial de Mis Préstamos',
        'subtitle' => 'Registro completo de todos tus préstamos',
        'breadcrumb' => [
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Historial Prestamos', 'active' => true]
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
                    @if($prestamos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <caption id="prestamos-description" class="sr-only">
                                    Listado de préstamos con información de producto, cantidad prestada, cantidad devuelta, estado, fecha de préstamo, fecha de devolución esperada, última devolución y acciones disponibles.
                                </caption>
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Cantidad Prestada</th>
                                        <th>Cantidad Devuelta</th>
                                        <th>Estado</th>
                                        <th>Fecha Préstamo</th>
                                        <th>Fecha Devolución Esperada</th>
                                        <th>Última Devolución</th>
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
                                                @if($detalle->estaCompletamenteDevuelto())
                                                    <span class="badge badge-success">Completado</span>
                                                @elseif($detalle->Vencido())
                                                    <span class="badge badge-danger">Vencido</span>
                                                @else
                                                    <span class="badge badge-warning">Pendiente</span>
                                                @endif
                                            </td>
                                            <td>{{ $detalle->orden->fecha_prestamo ? $detalle->orden->fecha_prestamo->format('d/m/Y') : 'N/A' }}</td>
                                            <td>
                                                @if($detalle->orden->fecha_devolucion)
                                                    {{ $detalle->orden->fecha_devolucion->format('d/m/Y') }}
                                                @else
                                                    <span class="text-muted">Sin fecha</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($detalle->devoluciones->count() > 0)
                                                    {{ $detalle->devoluciones->last()->fecha_devolucion->format('d/m/Y H:i') }}
                                                @else
                                                    <span class="text-muted">Sin devoluciones</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(!$detalle->estaCompletamenteDevuelto())
                                                    <a href="{{ route('inventario.devoluciones.create', $detalle->id) }}"
                                                       class="btn btn-sm btn-primary">
                                                        <i class="fas fa-undo"></i> Devolver
                                                    </a>
                                                @endif
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
                            'title' => 'No tienes historial de préstamos',
                            'message' => 'Aún no has realizado ningún préstamo.',
                            'icon' => 'fas fa-history'
                        ])
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

    @include('layouts.alertas')
@endsection

@section('footer')
    {{-- Footer SENA --}}
    @include('layouts.footer')
@endsection

@push('css')
    @vite([
        'resources/css/inventario/shared/base.css',
    ])
@endpush

