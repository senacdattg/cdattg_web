@extends('adminlte::page')

@section('title', 'Detalle de Devolución')

@section('content_header')
    <x-page-header
        icon="fas fa-info-circle"
        title="Detalle de Devolución #{{ $devolucion->id }}"
        subtitle="Información completa de la devolución"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Devoluciones', 'url' => route('inventario.devoluciones.historial')],
            ['label' => 'Detalle', 'active' => true]
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
                <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Devolución #{{ $devolucion->id }}</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Producto Devuelto</h6>
                            <p class="mb-0"><strong>{{ $devolucion->detalleOrden->producto->producto }}</strong></p>
                            <small class="text-muted">{{ $devolucion->detalleOrden->producto->descripcion }}</small>
                        </div>
                        <div class="col-md-6">
                            <h6>Información de la Orden</h6>
                            <p class="mb-0">Orden #{{ $devolucion->detalleOrden->orden->id }}</p>
                            <p class="mb-0">Fecha préstamo: {{ $devolucion->detalleOrden->orden->fecha_prestamo->format('d/m/Y') }}</p>
                            @if($devolucion->detalleOrden->orden->fecha_devolucion)
                                <p class="mb-0">Fecha devolución esperada: {{ $devolucion->detalleOrden->orden->fecha_devolucion->format('d/m/Y') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h6>Cantidad Devuelta</h6>
                                    <h3>{{ $devolucion->cantidad_devuelta }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h6>Fecha Devolución</h6>
                                    <h5>{{ $devolucion->fecha_devolucion->format('d/m/Y H:i') }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card @if($devolucion->detalleOrden->orden->fecha_devolucion && $devolucion->fecha_devolucion->lte($devolucion->detalleOrden->orden->fecha_devolucion)) bg-success @else bg-warning @endif text-white">
                                <div class="card-body text-center">
                                    <h6>Estado</h6>
                                    @if($devolucion->detalleOrden->orden->fecha_devolucion)
                                        @if($devolucion->fecha_devolucion->lte($devolucion->detalleOrden->orden->fecha_devolucion))
                                            <h5>A Tiempo</h5>
                                        @else
                                            <h5>Con Retraso</h5>
                                            <small>{{ $devolucion->fecha_devolucion->diffInDays($devolucion->detalleOrden->orden->fecha_devolucion) }} días</small>
                                        @endif
                                    @else
                                        <h5>Sin fecha límite</h5>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($devolucion->observaciones)
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Observaciones</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-0">{{ $devolucion->observaciones }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Registrado por</h6>
                            <p class="mb-0">{{ $devolucion->userCreate->name ?? 'Usuario no encontrado' }}</p>
                            <small class="text-muted">Fecha registro: {{ $devolucion->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                        @if($devolucion->userUpdate)
                            <div class="col-md-6">
                                <h6>Última modificación</h6>
                                <p class="mb-0">{{ $devolucion->userUpdate->name }}</p>
                                <small class="text-muted">Fecha: {{ $devolucion->updated_at->format('d/m/Y H:i') }}</small>
                            </div>
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <a href="{{ route('inventario.devoluciones.historial') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver al Historial
                            </a>
                            <a href="{{ route('inventario.ordenes.show', $devolucion->detalleOrden->orden->id) }}" class="btn btn-info">
                                <i class="fas fa-eye"></i> Ver Orden Completa
                            </a>
                        </div>
                    </div>
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
