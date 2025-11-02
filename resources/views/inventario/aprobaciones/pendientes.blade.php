@extends('adminlte::page')

@section('title', 'Aprobaciones Pendientes')

@section('content_header')
    <x-page-header
        icon="fas fa-clipboard-check"
        title="Aprobaciones Pendientes"
        subtitle="Gestión de solicitudes de préstamos y salidas"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => route('home')],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Aprobaciones', 'active' => true]
        ]"
    />
@endsection

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
                    <span class="badge badge-light">{{ $detalles->count() }} pendiente(s)</span>
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
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Orden</th>
                                    <th>Solicitante</th>
                                    <th>Producto</th>
                                    <th class="text-center">Cantidad</th>
                                    <th>Tipo</th>
                                    <th>Fecha Solicitud</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detalles as $detalle)
                                    <tr>
                                        <td>
                                            <strong>#{{ $detalle->orden_id }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ Str::limit($detalle->orden->descripcion_orden, 50) }}
                                            </small>
                                        </td>
                                        <td>
                                            <i class="fas fa-user text-primary"></i>
                                            {{ $detalle->orden->userCreate->name ?? 'N/A' }}
                                            <br>
                                            <small class="text-muted">
                                                {{ $detalle->orden->userCreate->email ?? '' }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset($detalle->producto->imagen ?? 'img/inventario/producto-default.png') }}" 
                                                     alt="{{ $detalle->producto->producto }}"
                                                     class="img-thumbnail mr-2"
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                                <div>
                                                    <strong>{{ $detalle->producto->producto }}</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        Stock: {{ $detalle->producto->cantidad }}
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-info badge-lg">
                                                {{ $detalle->cantidad }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $tipoOrden = $detalle->orden->tipoOrden->parametro->name ?? 'N/A';
                                            @endphp
                                            <span class="badge badge-{{ strtolower($tipoOrden) === 'prestamo' ? 'warning' : 'info' }}">
                                                {{ $tipoOrden }}
                                            </span>
                                        </td>
                                        <td>
                                            <i class="fas fa-calendar"></i>
                                            {{ $detalle->created_at->format('d/m/Y') }}
                                            <br>
                                            <small class="text-muted">
                                                {{ $detalle->created_at->diffForHumans() }}
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                {{-- Botón Ver Detalles --}}
                                                <button type="button" 
                                                        class="btn btn-sm btn-info"
                                                        data-toggle="modal"
                                                        data-target="#detalleModal{{ $detalle->id }}"
                                                        title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                {{-- Botón Aprobar --}}
                                                <button type="button" 
                                                        class="btn btn-sm btn-success"
                                                        onclick="aprobarSolicitud({{ $detalle->id }}, '{{ addslashes($detalle->producto->producto) }}')"
                                                        title="Aprobar">
                                                    <i class="fas fa-check"></i>
                                                </button>

                                                {{-- Botón Rechazar --}}
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger"
                                                        onclick="rechazarSolicitud({{ $detalle->id }}, '{{ addslashes($detalle->producto->producto) }}')"
                                                        title="Rechazar">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    {{-- Modal de Detalles --}}
                                    <div class="modal fade" id="detalleModal{{ $detalle->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-info text-white">
                                                    <h5 class="modal-title">
                                                        <i class="fas fa-info-circle"></i>
                                                        Detalles de la Solicitud #{{ $detalle->orden_id }}
                                                    </h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal">
                                                        <span>&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h6><strong>Información de la Orden</strong></h6>
                                                            <p><strong>Descripción:</strong><br>
                                                                <pre style="white-space: pre-wrap; font-size: 0.9em;">{{ $detalle->orden->descripcion_orden }}</pre>
                                                            </p>
                                                            @if($detalle->orden->fecha_devolucion)
                                                                <p><strong>Fecha Devolución:</strong> {{ $detalle->orden->fecha_devolucion->format('d/m/Y') }}</p>
                                                            @endif
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6><strong>Información del Producto</strong></h6>
                                                            <p><strong>Producto:</strong> {{ $detalle->producto->producto }}</p>
                                                            <p><strong>Código:</strong> {{ $detalle->producto->codigo_barras }}</p>
                                                            <p><strong>Stock Actual:</strong> 
                                                                <span class="badge badge-{{ $detalle->producto->cantidad >= $detalle->cantidad ? 'success' : 'danger' }}">
                                                                    {{ $detalle->producto->cantidad }}
                                                                </span>
                                                            </p>
                                                            <p><strong>Cantidad Solicitada:</strong> 
                                                                <span class="badge badge-info">{{ $detalle->cantidad }}</span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('layout.footer')
@endsection

@push('js')
<script>
function aprobarSolicitud(detalleId, nombreProducto) {
    Swal.fire({
        title: '¿Aprobar solicitud?',
        html: `¿Está seguro de aprobar esta solicitud para<br><strong>${nombreProducto}</strong>?<br><br>El stock será descontado automáticamente.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-check"></i> Sí, aprobar',
        cancelButtonText: '<i class="fas fa-times"></i> Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Crear formulario y enviarlo
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/inventario/aprobaciones/${detalleId}/aprobar`;
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);

            document.body.appendChild(form);
            form.submit();
        }
    });
}

function rechazarSolicitud(detalleId, nombreProducto) {
    Swal.fire({
        title: '¿Rechazar solicitud?',
        html: `¿Está seguro de rechazar esta solicitud para<br><strong>${nombreProducto}</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-times"></i> Sí, rechazar',
        cancelButtonText: '<i class="fas fa-ban"></i> Cancelar',
        input: 'textarea',
        inputLabel: 'Motivo del rechazo (obligatorio)',
        inputPlaceholder: 'Explique el motivo del rechazo...',
        inputAttributes: {
            'aria-label': 'Motivo del rechazo',
            'required': 'required'
        },
        inputValidator: (value) => {
            if (!value) {
                return 'Debe indicar el motivo del rechazo';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Crear formulario y enviarlo
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/inventario/aprobaciones/${detalleId}/rechazar`;
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);

            const motivoInput = document.createElement('input');
            motivoInput.type = 'hidden';
            motivoInput.name = 'motivo_rechazo';
            motivoInput.value = result.value;
            form.appendChild(motivoInput);

            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush