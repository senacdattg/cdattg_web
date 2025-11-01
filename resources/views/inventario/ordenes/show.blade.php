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
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-gradient">
                        <h3 class="card-title">
                            <i class="fas fa-file-alt"></i>
                            Información de la Orden
                        </h3>
                    </div>

                    <div class="card-body">
                        {{-- Información general --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label><i class="fas fa-hashtag"></i> ID:</label>
                                    <span>{{ $orden->id }}</span>
                                </div>
                                <div class="info-group">
                                    <label><i class="fas fa-tag"></i> Tipo:</label>
                                    <span class="badge badge-secondary">
                                        {{ $orden->tipoOrden->parametro->name ?? 'N/A' }}
                                    </span>
                                </div>
                                <div class="info-group">
                                    <label><i class="fas fa-calendar"></i> Fecha Creación:</label>
                                    <span>{{ $orden->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                @if($orden->fecha_devolucion)
                                    <div class="info-group">
                                        <label><i class="fas fa-calendar-check"></i> Fecha Devolución:</label>
                                        <span>{{ $orden->fecha_devolucion->format('d/m/Y') }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label><i class="fas fa-user"></i> Solicitante:</label>
                                    <span>{{ $orden->userCreate->name }}</span>
                                </div>
                                <div class="info-group">
                                    <label><i class="fas fa-envelope"></i> Email:</label>
                                    <span>{{ $orden->userCreate->email }}</span>
                                </div>
                                <div class="info-group">
                                    <label><i class="fas fa-graduation-cap"></i> Programa:</label>
                                    <span>{{ $orden->programa_formacion }}</span>
                                </div>
                                <div class="info-group">
                                    <label><i class="fas fa-ticket-alt"></i> Ficha:</label>
                                    <span>{{ $orden->ficha }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="description-box mt-4">
                            <label><i class="fas fa-align-left"></i> Descripción:</label>
                            <p>{{ $orden->descripcion_orden }}</p>
                        </div>

                        {{-- Lista de productos --}}
                        <div class="products-section mt-4">
                            <h4><i class="fas fa-box"></i> Productos Solicitados</h4>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Producto</th>
                                            <th>Cantidad</th>
                                            <th>Estado</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($orden->detalles as $detalle)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $detalle->producto->nombre }}</td>
                                                <td>{{ $detalle->cantidad }}</td>
                                                <td>
                                                    @php
                                                        $estadoClass = [
                                                            'PENDIENTE' => 'warning',
                                                            'APROBADO' => 'success',
                                                            'RECHAZADO' => 'danger'
                                                        ][$detalle->estado] ?? 'secondary';
                                                    @endphp
                                                    <span class="badge badge-{{ $estadoClass }}">
                                                        {{ $detalle->estado }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    @if($detalle->estado === 'PENDIENTE' && auth()->user()->can('APROBAR ORDEN'))
                                                        <button 
                                                            class="btn btn-sm btn-success aprobar-detalle" 
                                                            data-detalle-id="{{ $detalle->id }}"
                                                            data-toggle="tooltip" 
                                                            title="Aprobar"
                                                        >
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button 
                                                            class="btn btn-sm btn-danger rechazar-detalle"
                                                            data-detalle-id="{{ $detalle->id }}"
                                                            data-toggle="tooltip" 
                                                            title="Rechazar"
                                                        >
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @elseif($detalle->estado === 'APROBADO')
                                                        <span class="badge badge-success">
                                                            <i class="fas fa-check"></i> 
                                                            Aprobado por {{ $detalle->aprobaciones->last()->aprobador->name ?? 'N/A' }}
                                                        </span>
                                                    @elseif($detalle->estado === 'RECHAZADO')
                                                        <span class="badge badge-danger">
                                                            <i class="fas fa-times"></i>
                                                            Rechazado por {{ $detalle->aprobaciones->last()->aprobador->name ?? 'N/A' }}
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">
                                                    <i class="fas fa-info-circle"></i>
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
                        <a href="{{ route('inventario.ordenes.index') }}" class="btn btn-secondary">
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
    @vite(['public/css/inventario/shared/base.css'])
    <style>
        .info-group {
            margin-bottom: 1rem;
        }
        .info-group label {
            font-weight: bold;
            margin-right: 0.5rem;
        }
        .description-box {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 0.25rem;
        }
        .description-box label {
            font-weight: bold;
            display: block;
            margin-bottom: 0.5rem;
        }
        .description-box p {
            margin-bottom: 0;
        }
        .products-section {
            border-top: 1px solid #dee2e6;
            padding-top: 1rem;
        }
        .products-section h4 {
            margin-bottom: 1rem;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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