@extends('adminlte::page')

@section('title', 'Aprobaciones Pendientes')

@section('content_header')
    <x-page-header
        icon="fas fa-clipboard-check"
        title="Aprobaciones Pendientes"
        subtitle="Gestión de solicitudes de préstamos y salidas"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Aprobaciones', 'active' => true]
        ]"
    />
@endsection
@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
    <link rel="stylesheet" href="{{ asset('css/inventario/modal-orden.css') }}">
@endpush

@section('content')
    <div class="container-fluid">
        {{-- Alertas --}}
        @include('components.session-alerts')

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title">
                    <i class="fas fa-hourglass-half"></i>
                    Solicitudes en Espera de Aprobación
                </h3>
                <div class="card-tools">
                    <span class="badge badge-light">
                        {{ $detalles->count() }} pendiente(s)
                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                @if($detalles->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        <h4 class="mt-3">No hay solicitudes pendientes</h4>
                        <p class="text-muted">Todas las solicitudes han sido procesadas</p>
                    </div>
                @else
                    {{-- Agrupar detalles por orden --}}
                    @php
                        $detallesPorOrden = $detalles->groupBy('orden_id');
                    @endphp

                    @foreach($detallesPorOrden as $ordenId => $detallesOrden)
                        @php
                            $primeraDetalle = $detallesOrden->first();
                            $orden = $primeraDetalle->orden;
                            $descripcion = $orden->descripcion_orden ?? '';
                            preg_match('/MOTIVO:\s*(.+?)$/s', $descripcion, $matchMotivo);
                            $motivoCorto = isset($matchMotivo[1]) ? trim($matchMotivo[1]) : 'N/A';
                            $productosOrden = $detallesOrden->pluck('producto.producto')->join(', ');
                        @endphp

                        <div class="card mb-3 border-primary">
                            <div class="card-header bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">
                                            <i class="fas fa-file-invoice"></i>
                                            Orden #{{ $ordenId }}
                                            <span class="badge badge-secondary ml-2">
                                                {{ $detallesOrden->count() }} producto(s)
                                            </span>
                                        </h6>
                                        <small class="text-muted">
                                            <i class="fas fa-user"></i> 
                                            {{ $orden->userCreate->name ?? 'N/A' }} |
                                            <i class="fas fa-calendar"></i> 
                                            {{ $primeraDetalle->created_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                    <div class="text-right">
                                        @php
                                            $tipoOrden = $orden->tipoOrden->parametro->name ?? 'N/A';
                                            $tipoClass = $tipoOrden === 'PRÉSTAMO' ? 'warning' : 'info';
                                        @endphp
                                        <span class="badge badge-{{ $tipoClass }}">
                                            <i class="fas fa-{{ $tipoOrden === 'PRÉSTAMO' ? 'handshake' : 'sign-out-alt' }}"></i>
                                            {{ $tipoOrden }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-2">
                                            <strong><i class="fas fa-boxes"></i> Productos:</strong>
                                            <span class="text-muted">{{ Str::limit($productosOrden, 100) }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <strong><i class="fas fa-comment-dots"></i> Motivo:</strong>
                                            <span class="text-muted">{{ Str::limit($motivoCorto, 80) }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <button type="button"
                                                class="btn btn-info btn-sm mb-2"
                                                data-toggle="modal"
                                                data-target="#ordenModal{{ $ordenId }}"
                                                title="Ver detalles completos"
                                        >
                                            <i class="fas fa-eye"></i> Ver Detalles
                                        </button>
                                        <br>
                                        <button type="button"
                                                class="btn btn-success btn-sm mr-1"
                                                onclick="aprobarOrden({{ $ordenId }}, '{{ addslashes($productosOrden) }}')"
                                        >
                                            <i class="fas fa-check"></i> Aprobar Todo
                                        </button>
                                        <button type="button"
                                                class="btn btn-danger btn-sm"
                                                onclick="rechazarOrden({{ $ordenId }}, '{{ addslashes($productosOrden) }}')"
                                        >
                                            <i class="fas fa-times"></i> Rechazar Todo
                                        </button>
                                    </div>
                                </div>

                                {{-- Lista de productos de la orden --}}
                                <div class="mt-3">
                                    <table class="table table-sm table-borderless">
                                        <caption id="ordenes-description" class="sr-only">
                                            Listado de órdenes pendientes de aprobación con información de producto, stock, solicitado, estado y acciones disponibles.
                                        </caption>
                                        <thead>
                                            <tr class="table-light">
                                                <th style="width: 40%;">Producto</th>
                                                <th style="width: 15%;" class="text-center">Stock</th>
                                                <th style="width: 15%;" class="text-center">Solicitado</th>
                                                <th style="width: 15%;" class="text-center">Estado</th>
                                                <th style="width: 15%;" class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($detallesOrden as $detalle)
                                            @php
                                                $producto = $detalle->producto;
                                                $productoNombre = $producto->producto ?? 'N/A';
                                                $productoNombreCorto = Str::limit($productoNombre, 30);
                                                $productoImagen = $producto->imagen ?? 'img/inventario/producto-default.png';
                                                $productoImagenSrc = asset($productoImagen);
                                                $stockDisponible = $producto->cantidad ?? 0;
                                                $stockBadgeClass = $stockDisponible >= $detalle->cantidad ? 'success' : 'danger';
                                                $productoCodigo = $producto->codigo_barras ?? 'N/A';
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img 
                                                            src="{{ $productoImagenSrc }}"
                                                            alt="{{ $productoNombre }}"
                                                            class="img-thumbnail mr-2"
                                                            style="width: 30px; height: 30px; object-fit: cover;"
                                                        >
                                                        <span>{{ $productoNombreCorto }}</span>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-{{ $stockBadgeClass }}">
                                                        {{ $stockDisponible }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-info">{{ $detalle->cantidad }}</span>
                                                </td>
                                                <td class="text-center">
                                                    @if($stockBadgeClass === 'success')
                                                        <span class="badge badge-success">
                                                            <i class="fas fa-check"></i> OK
                                                        </span>
                                                    @else
                                                        <span class="badge badge-danger">
                                                            <i class="fas fa-exclamation-triangle"></i> Insuficiente
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group-xs">
                                                        <button 
                                                            type="button"
                                                            class="btn btn-success btn-xs"
                                                            onclick="aprobarProducto({{ $detalle->id }}, '{{ addslashes($productoNombre) }}')"
                                                            title="Aprobar este producto"
                                                        >
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button 
                                                            type="button"
                                                            class="btn btn-danger btn-xs"
                                                            onclick="rechazarProducto({{ $detalle->id }}, '{{ addslashes($productoNombre) }}')"
                                                            title="Rechazar este producto"
                                                        >
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Modal de detalles de la orden --}}
                        <div 
                            class="modal fade modal-orden" 
                            id="ordenModal{{ $ordenId }}" 
                            tabindex="-1"
                        >
                            <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            <i class="fas fa-clipboard-list"></i>
                                            Detalles de Orden #{{ $ordenId }}
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="info-card">
                                                    <h6><i class="fas fa-user-graduate"></i> Información del Solicitante</h6>
                                                    @php
                                                        preg_match('/Programa de Formación:\s*(.+?)[\n\r]/i', $descripcion, $matchPrograma);
                                                        preg_match('/Rol:\s*(.+?)[\n\r]/i', $descripcion, $matchRol);
                                                        preg_match('/Email:\s*(.+?)[\n\r]/i', $descripcion, $matchEmail);

                                                        $programa = isset($matchPrograma[1]) ? trim($matchPrograma[1]) : 'N/A';
                                                        $rol = isset($matchRol[1]) ? trim($matchRol[1]) : 'N/A';
                                                        $email = isset($matchEmail[1]) ? trim($matchEmail[1]) : 'N/A';
                                                    @endphp
                                                    <div class="info-item">
                                                        <strong>
                                                            <i class="fas fa-graduation-cap"></i>
                                                            Programa:
                                                        </strong><br>
                                                        <span class="text-muted">{{ $programa }}</span>
                                                    </div>
                                                    <div class="info-item">
                                                        <strong>
                                                            <i class="fas fa-id-badge"></i>
                                                            Rol:
                                                        </strong><br>
                                                        <span class="text-muted">{{ $rol }}</span>
                                                    </div>
                                                    <div class="info-item">
                                                        <strong>
                                                            <i class="fas fa-envelope"></i>
                                                            Email:
                                                        </strong><br>
                                                        <span class="text-muted">{{ $email }}</span>
                                                    </div>
                                                    <div class="info-item">
                                                        <strong>
                                                            <i class="fas fa-exchange-alt"></i>
                                                            Tipo:
                                                        </strong><br>
                                                        <span class="badge badge-{{ $tipoClass }}">
                                                            <i class="fas fa-{{ $tipoOrden === 'PRÉSTAMO' ? 'handshake' : 'sign-out-alt' }}"></i>
                                                            {{ $tipoOrden }}
                                                        </span>
                                                    </div>
                                                    @if($orden->fecha_devolucion)
                                                        <div class="info-item">
                                                            <strong>
                                                                <i class="fas fa-calendar-check"></i>
                                                                Fecha Devolución:
                                                            </strong><br>
                                                            <span class="badge badge-warning">
                                                                <i class="fas fa-clock"></i>
                                                                {{ $orden->fecha_devolucion->format('d/m/Y') }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-card">
                                                    <h6>
                                                        <i class="fas fa-boxes"></i>
                                                        Productos Solicitados
                                                    </h6>
                                                    @foreach($detallesOrden as $detalle)
                                                        <div class="product-item mb-3 p-2 border rounded">
                                                            <div class="d-flex justify-content-between">
                                                                <div>
                                                                    <strong>{{ $detalle->producto->producto }}</strong><br>
                                                                    <small class="text-muted">
                                                                        Código: {{ $productoCodigo }}
                                                                    </small>
                                                                </div>
                                                                <div class="text-right">
                                                                    <span class="badge badge-info">
                                                                        <i class="fas fa-hashtag"></i>
                                                                        {{ $detalle->cantidad }}
                                                                    </span>
                                                                    <br>
                                                                    <small class="text-muted">
                                                                        Stock: {{ $detalle->producto->cantidad }}
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="divider"></div>
                                        <div class="motivo-section">
                                            <strong><i class="fas fa-comment-dots"></i> Motivo de la Solicitud:</strong>
                                            <div class="motivo-text">
                                                @php
                                                    // Extraer el motivo completo de la descripción de la orden
                                                    $descripcionCompleta = $orden->descripcion_orden ?? '';
                                                    preg_match('/MOTIVO:\s*(.+?)$/s', $descripcionCompleta, $matchMotivoCompleto);
                                                    $motivoCompleto = isset($matchMotivoCompleto[1]) ? trim($matchMotivoCompleto[1]) : $descripcionCompleta;
                                                @endphp
                                                {{ $motivoCompleto ?: 'Sin motivo especificado' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                            <i class="fas fa-times"></i> Cerrar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    @include('layout.footer')
@endsection



@push('js')
<!-- Script de aprobaciones -->
@vite(['resources/js/inventario/aprobaciones.js'])
@endpush

