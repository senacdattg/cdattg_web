@extends('adminlte::page')

@section('title', 'Registrar Devolución')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush

@section('content_header')
    <x-page-header
        icon="fas fa-undo"
        title="Registrar Devolución"
        subtitle="Devolver productos prestados"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Devoluciones', 'url' => route('inventario.devoluciones.index')],
            ['label' => 'Registrar', 'active' => true]
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
                    <h5 class="card-title mb-0">Información del Préstamo</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Producto</h6>
                            <p class="mb-0"><strong>{{ $detalleOrden->producto->producto }}</strong></p>
                            <small class="text-muted">{{ $detalleOrden->producto->descripcion }}</small>
                        </div>
                        <div class="col-md-6">
                            <h6>Orden #{{ $detalleOrden->orden->id }}</h6>
                            <p class="mb-0">
                                Fecha préstamo: 
                                {{ $detalleOrden->orden->fecha_prestamo ? $detalleOrden->orden->fecha_prestamo->format('d/m/Y') : 'N/A' }}
                            </p>
                            @if($detalleOrden->orden->fecha_devolucion)
                                <p class="mb-0">
                                    Fecha devolución esperada: 
                                    {{ $detalleOrden->orden->fecha_devolucion->format('d/m/Y') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6>Cantidad Prestada</h6>
                                    <h3 class="text-primary">{{ $detalleOrden->cantidad }}</h3>
                                </div>
                            </div>
                        </div>
                            <div class="col-md-4">
                                <div class="card bg-warning">
                                <div class="card-body text-center">
                                    <h6>Ya Devuelto</h6>
                                    <h3 class="text-white">{{ $detalleOrden->getCantidadDevuelta() }}</h3>
                                </div>
                            </div>
                        </div>
                            <div class="col-md-4">
                                <div class="card bg-danger">
                                <div class="card-body text-center">
                                    <h6>Pendiente</h6>
                                    <h3 class="text-white">{{ $detalleOrden->getCantidadPendiente() }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('inventario.devoluciones.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="detalle_orden_id" value="{{ $detalleOrden->id }}">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cantidad_devuelta">Cantidad a Devolver *</label>
                                    <input type="number"
                                           class="form-control @error('cantidad_devuelta') is-invalid @enderror"
                                           id="cantidad_devuelta"
                                           name="cantidad_devuelta"
                                           value="{{ old('cantidad_devuelta', 1) }}"
                                           min="1"
                                           max="{{ $detalleOrden->getCantidadPendiente() }}"
                                           required>
                                    @error('cantidad_devuelta')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Máximo: {{ $detalleOrden->getCantidadPendiente() }} unidades
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="observaciones">Observaciones</label>
                                    <textarea 
                                        class="form-control @error('observaciones') is-invalid @enderror"
                                        id="observaciones"
                                        name="observaciones"
                                        rows="3"
                                        placeholder="Observaciones sobre la devolución..."
                                    >{{ old('observaciones') }}</textarea>
                                    @error('observaciones')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Nota:</strong> Al registrar esta devolución, el stock del producto será restaurado automáticamente.
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button 
                                    type="submit" 
                                    class="btn btn-primary"
                                >
                                    <i class="fas fa-save"></i> Registrar Devolución
                                </button>
                                <a 
                                    href="{{ route('inventario.devoluciones.index') }}" 
                                    class="btn btn-secondary"
                                >
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </div>
                    </form>
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
