@extends('adminlte::page')

@section('title', 'Detalles de Orden')

@section('content_header')
    <x-page-header
        icon="fas fa-info-circle"
        title="Detalles de Orden"
        subtitle="Ver detalles de la orden y sus productos"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Órdenes', 'url' => route('inventario.ordenes.index')],
            ['label' => 'Detalles', 'active' => true]
        ]"
    />
@endsection

@section('content')
    <div class="container-fluid orden-show-card">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-file-invoice"></i>
                            Información de la Orden #{{ $orden->id }}
                        </h3>
                    </div>

                    <div class="card-body">
                        {{-- Información general --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label><i class="fas fa-hashtag"></i> ID de Orden:</label>
                                    <span class="badge badge-secondary">#{{ $orden->id }}</span>
                                </div>
                                <div class="info-group">
                                    <label><i class="fas fa-exchange-alt"></i> Tipo de Orden:</label>
                                    @php
                                        $tipoNombre = $orden->tipoOrden->parametro->name ?? 'N/A';
                                        $tipoClass = $tipoNombre === 'PRÉSTAMO' ? 'warning' : 'info';
                                    @endphp
                                    <span class="badge badge-{{ $tipoClass }}">
                                        <i class="fas fa-{{ $tipoNombre === 'PRÉSTAMO' ? 'handshake' : 'sign-out-alt' }}"></i>
                                        {{ $tipoNombre }}
                                    </span>
                                </div>
                                <div class="info-group">
                                    <label><i class="fas fa-calendar-plus"></i> Fecha Creación:</label>
                                    <span>{{ $orden->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                @if($orden->fecha_devolucion)
                                    <div class="info-group">
                                        <label><i class="fas fa-calendar-check"></i> Fecha Devolución:</label>
                                        <span class="badge badge-warning">
                                            <i class="fas fa-clock"></i>
                                            {{ $orden->fecha_devolucion->format('d/m/Y') }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label><i class="fas fa-user-circle"></i> Solicitante:</label>
                                    <span>{{ $orden->userCreate->name }}</span>
                                </div>
                                <div class="info-group">
                                    <label><i class="fas fa-envelope"></i> Email:</label>
                                    <span>{{ $orden->userCreate->email }}</span>
                                </div>
                                @php
                                    // Extraer información de la descripción
                                    $descripcion = $orden->descripcion_orden ?? '';
                                    preg_match('/Programa de Formación:\s*(.+?)[\n\r]/i', $descripcion, $matchPrograma);
                                    preg_match('/Rol:\s*(.+?)[\n\r]/i', $descripcion, $matchRol);
                                    $programa = $matchPrograma[1] ?? 'N/A';
                                    $rol = $matchRol[1] ?? 'N/A';
                                @endphp
                                <div class="info-group">
                                    <label><i class="fas fa-graduation-cap"></i> Programa:</label>
                                    <span>{{ $programa }}</span>
                                </div>
                                <div class="info-group">
                                    <label><i class="fas fa-id-badge"></i> Rol:</label>
                                    <span>{{ $rol }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="description-box">
                            <label><i class="fas fa-comment-dots"></i> Motivo de la Solicitud:</label>
                            @php
                                preg_match('/MOTIVO:\s*(.+?)$/s', $descripcion, $matchMotivo);
                                $motivo = isset($matchMotivo[1]) ? trim($matchMotivo[1]) : $orden->descripcion_orden;
                            @endphp
                            <p id="razon">{{ $motivo }}</p>
                        </div>

                        {{-- Lista de productos --}}
                        <div class="products-section">
                            <h4><i class="fas fa-boxes"></i> Productos Solicitados</h4>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <caption id="orden-description" class="sr-only">
                                        Lista de productos solicitados con información de número, producto, cantidad, estado y acciones disponibles.
                                    </caption>
                                    <thead>
                                        <tr>
                                            <th width="8%">#</th>
                                            <th width="35%">Producto</th>
                                            <th width="12%" class="text-center">Cantidad</th>
                                            <th width="15%">Estado</th>
                                            <th width="30%" class="text-center">Información</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($orden->detalles as $detalle)
                                            <tr>
                                                <td><strong>{{ $loop->iteration }}</strong></td>
                                                <td>
                                                    <i class="fas fa-box text-primary"></i>
                                                    {{ $detalle->producto->producto ?? 'N/A' }}
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-info">
                                                        <i class="fas fa-hashtag"></i>
                                                        {{ $detalle->cantidad }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @php
                                                        $estadoNombre = $detalle->estadoOrden->parametro->name ?? 'N/A';
                                                        $estadoClass = match($estadoNombre) {
                                                            'EN ESPERA' => 'warning',
                                                            'APROBADA' => 'success',
                                                            'RECHAZADA' => 'danger',
                                                            default => 'secondary'
                                                        };
                                                        $estadoIcon = match($estadoNombre) {
                                                            'EN ESPERA' => 'clock',
                                                            'APROBADA' => 'check-circle',
                                                            'RECHAZADA' => 'times-circle',
                                                            default => 'question-circle'
                                                        };
                                                    @endphp
                                                    <span class="badge badge-{{ $estadoClass }}">
                                                        <i class="fas fa-{{ $estadoIcon }}"></i>
                                                        {{ $estadoNombre }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    @if($estadoNombre === 'EN ESPERA')
                                                        <a href="{{ route('inventario.aprobaciones.pendientes') }}" 
                                                           class="btn btn-sm btn-info"
                                                           data-toggle="tooltip" 
                                                           title="Ir a gestionar la aprobación">
                                                            <i class="fas fa-tasks"></i> Gestionar Aprobación
                                                        </a>
                                                    @elseif($estadoNombre === 'APROBADA')
                                                        <div class="estado-info">
                                                            <span class="badge badge-success">
                                                                <i class="fas fa-user-check"></i>
                                                                Aprobada
                                                            </span>
                                                            @if($detalle->aprobacion)
                                                                <small>
                                                                    Por: {{ $detalle->aprobacion->aprobador->name ?? 'Admin' }}
                                                                    <br>
                                                                    {{ $detalle->aprobacion->created_at->format('d/m/Y H:i') }}
                                                                </small>
                                                            @endif
                                                        </div>
                                                    @elseif($estadoNombre === 'RECHAZADA')
                                                        <div class="estado-info">
                                                            <span class="badge badge-danger">
                                                                <i class="fas fa-user-times"></i>
                                                                Rechazada
                                                            </span>
                                                            @if($detalle->aprobacion)
                                                                <small>
                                                                    Por: {{ $detalle->aprobacion->aprobador->name ?? 'Admin' }}
                                                                    <br>
                                                                    {{ $detalle->aprobacion->created_at->format('d/m/Y H:i') }}
                                                                </small>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">
                                                    <i class="fas fa-inbox"></i>
                                                    <br>
                                                    No hay productos en esta orden
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-right">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de Observaciones para Rechazo --}}
    <div class="modal fade" id="modalRechazo" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-comment-alt"></i>
                        Observaciones del Rechazo
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formRechazo">
                        <div class="form-group">
                            <label for="observaciones">Motivo del rechazo:</label>
                            <textarea 
                                class="form-control" 
                                id="observaciones" 
                                name="observaciones" 
                                rows="3" 
                                required
                            ></textarea>
                        </div>
                        <input type="hidden" id="detalleIdRechazo">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmarRechazo">
                        <i class="fas fa-check"></i> Confirmar Rechazo
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Alertas --}}
    @include('layout.alertas')
    
    {{-- Footer SENA --}}
    @include('layout.footer')
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
    <link rel="stylesheet" href="{{ asset('css/inventario/orden.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Inicializar tooltips
            $('[data-toggle="tooltip"]').tooltip();

            // Manejar aprobación
            $('.aprobar-detalle').click(function() {
                const detalleId = $(this).data('detalle-id');
                
                Swal.fire({
                    title: '¿Aprobar este producto?',
                    text: "Esta acción descontará el stock del producto",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, aprobar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        aprobarDetalle(detalleId);
                    }
                });
            });

            // Manejar rechazo (mostrar modal)
            $('.rechazar-detalle').click(function() {
                const detalleId = $(this).data('detalle-id');
                $('#detalleIdRechazo').val(detalleId);
                $('#modalRechazo').modal('show');
            });

            // Confirmar rechazo
            $('#confirmarRechazo').click(function() {
                const detalleId = $('#detalleIdRechazo').val();
                const observaciones = $('#observaciones').val();
                
                if (!observaciones.trim()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Debe ingresar las observaciones del rechazo'
                    });
                    return;
                }

                rechazarDetalle(detalleId, observaciones);
                $('#modalRechazo').modal('hide');
            });

            // Función para aprobar detalle
            function aprobarDetalle(detalleId) {
                $.ajax({
                    url: `/inventario/ordenes/detalles/${detalleId}/aprobar`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Aprobado!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Ocurrió un error al aprobar el producto'
                        });
                    }
                });
            }

            // Función para rechazar detalle
            function rechazarDetalle(detalleId, observaciones) {
                $.ajax({
                    url: `/inventario/ordenes/detalles/${detalleId}/rechazar`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        observaciones: observaciones
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Rechazado!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Ocurrió un error al rechazar el producto'
                        });
                    }
                });
            }
        });
    </script>
@endpush
