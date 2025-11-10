@extends('adminlte::page')

@section('title', 'Historial de Devoluciones')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush

@section('content_header')
    <x-page-header
        icon="fas fa-history"
        title="Historial de Devoluciones"
        subtitle="Registro completo de todas las devoluciones realizadas"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Devoluciones', 'active' => true]
        ]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

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
                                <caption id="devoluciones-description" class="sr-only">
                                    Listado de devoluciones con información de ID, producto, cantidad devuelta, fecha de devolución, estado, registrado por y acciones disponibles.
                                </caption>
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
                                                <a href="{{ route('inventario.ordenes.show', $devolucion->detalleOrden->orden->id) }}"
                                                   class="btn btn-sm btn-info"
                                                   title="Ver orden">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($devoluciones instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
                            <div class="d-flex justify-content-center mt-3">
                                {{ $devoluciones->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-history fa-4x text-muted mb-3"></i>
                            <h5>No hay devoluciones registradas</h5>
                            <p class="text-muted">Aún no se han realizado devoluciones en el sistema.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
        </div>
    </section>
    {{-- Alertas --}}
    @include('layout.alertas')
    
    {{-- Footer SENA --}}
    @include('layout.footer')
@endsection

@push('css')
    @vite([
        'resources/css/inventario/shared/base.css',
    ])
@endpush
