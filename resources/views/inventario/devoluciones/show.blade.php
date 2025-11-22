@extends('inventario.layouts.base')

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
                                           value="{{ old('cantidad_devuelta', $detalleOrden->getCantidadPendiente()) }}"
                                           min="0"
                                           max="{{ $detalleOrden->getCantidadPendiente() }}"
                                           required>
                                    @error('cantidad_devuelta')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Máximo: {{ $detalleOrden->getCantidadPendiente() }} unidades. Usa 0 si el consumible se utilizó totalmente.
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
                                    <small class="form-text text-muted">
                                        Justifica aquí cuando registres la devolución en cero por consumo total.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Nota:</strong> El stock solo se restaurará cuando registres cantidades mayores a cero. Si el consumible se usó por completo, registra cantidad 0 y explica el motivo.
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
    {{-- Notificaciones manejadas globalmente por sweetalert2-notifications --}}
    
    {{-- Footer SENA --}}
    @include('layouts.footer')
@endsection

@push('css')
    @vite([
        'resources/css/inventario/shared/base.css',
    ])
@endpush

@extends('inventario.layouts.base')

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
                                        @php
                                            $esConsumoTotal = $devolucion->cierra_sin_stock === true;
                                        @endphp
                                        <tr @if($esConsumoTotal) class="table-warning" @endif>
                                            <td>{{ $devolucion->id }}</td>
                                            <td>
                                                <strong>{{ $devolucion->detalleOrden->producto->producto }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $devolucion->detalleOrden->producto->descripcion }}</small>
                                            </td>
                                            <td>
                                                {{ $devolucion->cantidad_devuelta }}
                                                @if($esConsumoTotal)
                                                    <span class="badge badge-warning ml-1">Consumo total</span>
                                                @endif
                                            </td>
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
                                                @if($esConsumoTotal)
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-warning btn-motivo-consumo-total"
                                                        data-consumo-total
                                                        data-motivo="{{ $devolucion->observaciones ? e($devolucion->observaciones) : 'Sin observaciones registradas.' }}"
                                                        title="Ver motivo de consumo total"
                                                    >
                                                        <i class="fas fa-info-circle"></i>
                                                    </button>
                                                @endif
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
    {{-- Notificaciones manejadas globalmente por sweetalert2-notifications --}}
    
    {{-- Footer SENA --}}
    @include('layouts.footer')
@endsection

@push('css')
    @vite([
        'resources/css/inventario/shared/base.css',
    ])
@endpush

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-consumo-total]').forEach(function (button) {
                button.addEventListener('click', function () {
                    var motivo = button.getAttribute('data-motivo') || 'Sin observaciones registradas.';

                    if (window.Swal && typeof window.Swal.fire === 'function') {
                        window.Swal.fire({
                            icon: 'info',
                            title: 'Motivo de consumo total',
                            text: motivo,
                            confirmButtonText: 'Cerrar'
                        });
                        return;
                    }

                    window.alert(motivo);
                });
            });
        });
    </script>
@endpush

@extends('inventario.layouts.base')

@section('title', 'Préstamos Pendientes de Devolución')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush

@section('content_header')
    <x-page-header
        icon="fas fa-undo"
        title="Préstamos Pendientes de Devolución"
        subtitle="Lista de préstamos que requieren devolución"
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
                    <h5 class="card-title mb-0">Préstamos Pendientes</h5>
                </div>
                <div class="card-body">
                    @if($prestamos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <caption id="devoluciones-description" class="sr-only">
                                    Listado de devoluciones con información de producto, cantidad prestada,
                                    cantidad devuelta, cantidad pendiente, fecha de préstamo y fecha de devolución.
                                </caption>
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Cantidad Prestada</th>
                                        <th>Cantidad Devuelta</th>
                                        <th>Cantidad Pendiente</th>
                                        <th>Fecha Préstamo</th>
                                        <th>Fecha Devolución</th>
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
                                            <td>
                                                {{ $detalle->orden->created_at ? $detalle->orden->created_at->format('d/m/Y') : 'Sin fecha' }}
                                            </td>
                                            <td>
                                                {{ $detalle->orden->fecha_devolucion ? $detalle->orden->fecha_devolucion->format('d/m/Y') : 'Sin fecha' }}
                                            </td>
                                            
                                            <td>
                                                <a
                                                    href="{{ route('inventario.devoluciones.create', $detalle->id) }}"
                                                    class="btn btn-sm btn-primary"
                                                >
                                                    <i class="fas fa-undo"></i> Devolver
                                                </a>
                                                <a
                                                    href="{{ route('inventario.ordenes.show', $detalle->orden->id) }}"
                                                    class="btn btn-sm btn-info"
                                                >
                                                    <i class="fas fa-eye"></i> Ver Orden
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($prestamos instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
                            <div class="d-flex justify-content-center mt-3">
                                {{ $prestamos->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fa-4x text-muted mb-3"></i>
                            <h5>No hay préstamos pendientes</h5>
                            <p class="text-muted">Todos los préstamos han sido devueltos completamente.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
        </div>
    </section>

   {{-- Alertas --}}
    {{-- Notificaciones manejadas globalmente por sweetalert2-notifications --}}
    
    {{-- Footer SENA --}}
    @include('layouts.footer')
@endsection

@push('css')
    @vite([
        'resources/css/inventario/shared/base.css',
    ])
@endpush

@extends('inventario.layouts.base')

@section('title', 'Detalle de Devolución')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush

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
                            <p class="mb-0">
                                Orden #{{ $devolucion->detalleOrden->orden->id }}
                            </p>
                            <p class="mb-0">
                                Fecha préstamo:
                                {{ $devolucion->detalleOrden->orden->fecha_prestamo->format('d/m/Y') }}
                            </p>
                            @if($devolucion->detalleOrden->orden->fecha_devolucion)
                                <p class="mb-0">
                                    Fecha devolución esperada:
                                    {{ $devolucion->detalleOrden->orden->fecha_devolucion->format('d/m/Y') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h6>Cantidad Devuelta</h6>
                                    <h3>{{ $devolucion->cantidad_devuelta }}</h3>
                                    @if($devolucion->cierra_sin_stock)
                                        <span class="badge badge-warning mt-2">Consumo total sin stock</span>
                                    @endif
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
                            <div class="card
                                @if($devolucion->detalleOrden->orden->fecha_devolucion && $devolucion->fecha_devolucion->lte($devolucion->detalleOrden->orden->fecha_devolucion))
                                    bg-success
                                @else
                                    bg-warning
                                @endif text-white">
                                <div class="card-body text-center">
                                    <h6>Estado</h6>
                                    @if($devolucion->detalleOrden->orden->fecha_devolucion)
                                        @if($devolucion->fecha_devolucion->lte($devolucion->detalleOrden->orden->fecha_devolucion))
                                            <h5>A Tiempo</h5>
                                        @else
                                            <h5>Con Retraso</h5>
                                            <small>
                                                {{ $devolucion->fecha_devolucion->diffInDays($devolucion->detalleOrden->orden->fecha_devolucion) }} días
                                            </small>
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
                            <a
                                href="{{ route('inventario.devoluciones.historial') }}"
                                class="btn btn-secondary"
                            >
                                <i class="fas fa-arrow-left"></i> Volver al Historial
                            </a>
                            <a
                                href="{{ route('inventario.ordenes.show', $devolucion->detalleOrden->orden->id) }}"
                                class="btn btn-info"
                            >
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
    {{-- Notificaciones manejadas globalmente por sweetalert2-notifications --}}
    
    {{-- Footer SENA --}}
    @include('layouts.footer')
@endsection

@push('css')
    @vite([
        'resources/css/inventario/shared/base.css',
    ])
@endpush

