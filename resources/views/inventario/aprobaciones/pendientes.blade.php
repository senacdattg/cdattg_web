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
    @vite(['public/css/inventario/shared/base.css'])
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
                                    <th width="4%">#</th>
                                    <th width="6%">Orden</th>
                                    <th width="12%">Solicitante</th>
                                    <th width="10%">Email</th>
                                    <th width="12%">Producto</th>
                                    <th width="6%" class="text-center">Stock</th>
                                    <th width="6%" class="text-center">Cantidad</th>
                                    <th width="7%">Tipo</th>
                                    <th width="15%">Motivo</th>
                                    <th width="9%">Fecha</th>
                                    <th width="13%" class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detalles as $detalle)
                                    @php
                                        $descripcion = $detalle->orden->descripcion_orden ?? '';
                                        preg_match('/MOTIVO:\s*(.+?)$/s', $descripcion, $matchMotivo);
                                        $motivoCorto = isset($matchMotivo[1]) ? trim($matchMotivo[1]) : 'N/A';
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <span class="badge badge-secondary">
                                                #{{ $detalle->orden_id }}
                                            </span>
                                        </td>
                                        <td>
                                            <i class="fas fa-user-circle text-primary"></i>
                                            {{ $detalle->orden->userCreate->name ?? 'N/A' }}
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <i class="fas fa-envelope"></i>
                                                {{ $detalle->orden->userCreate->email ?? 'N/A' }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset($detalle->producto->imagen ?? 'img/inventario/producto-default.png') }}" 
                                                     alt="{{ $detalle->producto->producto }}"
                                                     class="img-thumbnail mr-2"
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                                <span>{{ Str::limit($detalle->producto->producto, 20) }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-{{ $detalle->producto->cantidad >= $detalle->cantidad ? 'success' : 'danger' }}">
                                                <i class="fas fa-boxes"></i>
                                                {{ $detalle->producto->cantidad }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-info badge-lg">
                                                <i class="fas fa-hashtag"></i>
                                                {{ $detalle->cantidad }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $tipoOrden = $detalle->orden->tipoOrden->parametro->name ?? 'N/A';
                                                $tipoClass = $tipoOrden === 'PRÉSTAMO' ? 'warning' : 'info';
                                            @endphp
                                            <span class="badge badge-{{ $tipoClass }}">
                                                <i class="fas fa-{{ $tipoOrden === 'PRÉSTAMO' ? 'handshake' : 'sign-out-alt' }}"></i>
                                                {{ $tipoOrden }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted" title="{{ $motivoCorto }}">
                                                <i class="fas fa-comment-dots"></i>
                                                {{ Str::limit($motivoCorto, 50) }}
                                            </small>
                                        </td>
                                        <td>
                                            <i class="fas fa-calendar-alt text-muted"></i>
                                            {{ $detalle->created_at->format('d/m/Y') }}
                                            <br>
                                            <small class="text-muted">
                                                {{ $detalle->created_at->format('H:i') }}
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group-vertical btn-group-sm" role="group">
                                                {{-- Botón Ver Detalles --}}
                                                <button type="button" 
                                                        class="btn btn-info mb-1"
                                                        data-toggle="modal"
                                                        data-target="#detalleModal{{ $detalle->id }}"
                                                        title="Ver detalles">
                                                    <i class="fas fa-eye"></i> Ver
                                                </button>

                                                {{-- Botón Aprobar --}}
                                                <button type="button" 
                                                        class="btn btn-success mb-1"
                                                        onclick="aprobarSolicitud({{ $detalle->id }}, '{{ addslashes($detalle->producto->producto) }}')"
                                                        title="Aprobar">
                                                    <i class="fas fa-check"></i> Aprobar
                                                </button>

                                                {{-- Botón Rechazar --}}
                                                <button type="button" 
                                                        class="btn btn-danger"
                                                        onclick="rechazarSolicitud({{ $detalle->id }}, '{{ addslashes($detalle->producto->producto) }}')"
                                                        title="Rechazar">
                                                    <i class="fas fa-times"></i> Rechazar
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    {{-- Modal de Detalles --}}
                                    <div class="modal fade modal-orden" id="detalleModal{{ $detalle->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">
                                                        <i class="fas fa-clipboard-list"></i>
                                                        Solicitud #{{ $detalle->orden_id }}
                                                    </h5>
                                                    <button type="button" class="close" data-dismiss="modal">
                                                        <span>&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <div class="info-card">
                                                                <h6>
                                                                    <i class="fas fa-user-graduate"></i>
                                                                    Información del Solicitante
                                                                </h6>
                                                                @php
                                                                    $descripcion = $detalle->orden->descripcion_orden ?? '';
                                                                    preg_match('/MOTIVO:\s*(.+?)$/s', $descripcion, $matchMotivo);
                                                                    preg_match('/Programa de Formación:\s*(.+?)[\n\r]/i', $descripcion, $matchPrograma);
                                                                    preg_match('/Rol:\s*(.+?)[\n\r]/i', $descripcion, $matchRol);
                                                                    preg_match('/Email:\s*(.+?)[\n\r]/i', $descripcion, $matchEmail);
                                                                    
                                                                    $motivo = isset($matchMotivo[1]) ? trim($matchMotivo[1]) : 'N/A';
                                                                    $programa = isset($matchPrograma[1]) ? trim($matchPrograma[1]) : 'N/A';
                                                                    $rol = isset($matchRol[1]) ? trim($matchRol[1]) : 'N/A';
                                                                    $email = isset($matchEmail[1]) ? trim($matchEmail[1]) : 'N/A';
                                                                @endphp
                                                                <div class="info-item">
                                                                    <strong><i class="fas fa-graduation-cap"></i> Programa:</strong><br>
                                                                    <span class="text-muted">{{ $programa }}</span>
                                                                </div>
                                                                <div class="info-item">
                                                                    <strong><i class="fas fa-id-badge"></i> Rol:</strong><br>
                                                                    <span class="text-muted">{{ $rol }}</span>
                                                                </div>
                                                                <div class="info-item">
                                                                    <strong><i class="fas fa-envelope"></i> Email:</strong><br>
                                                                    <span class="text-muted">{{ $email }}</span>
                                                                </div>
                                                                <div class="info-item">
                                                                    <strong><i class="fas fa-exchange-alt"></i> Tipo de Orden:</strong><br>
                                                                    @php
                                                                        $tipoOrdenModal = $detalle->orden->tipoOrden->parametro->name ?? 'N/A';
                                                                        $tipoClassModal = $tipoOrdenModal === 'PRÉSTAMO' ? 'warning' : 'info';
                                                                        $tipoIconModal = $tipoOrdenModal === 'PRÉSTAMO' ? 'handshake' : 'sign-out-alt';
                                                                    @endphp
                                                                    <span class="badge badge-{{ $tipoClassModal }}">
                                                                        <i class="fas fa-{{ $tipoIconModal }}"></i>
                                                                        {{ $tipoOrdenModal }}
                                                                    </span>
                                                                </div>
                                                                @if($detalle->orden->fecha_devolucion)
                                                                    <div class="info-item">
                                                                        <strong><i class="fas fa-calendar-check"></i> Fecha Devolución:</strong><br>
                                                                        <span class="badge badge-warning">
                                                                            <i class="fas fa-clock"></i>
                                                                            {{ $detalle->orden->fecha_devolucion->format('d/m/Y') }}
                                                                        </span>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="col-md-6 mb-3">
                                                            <div class="info-card">
                                                                <h6>
                                                                    <i class="fas fa-box"></i>
                                                                    Información del Producto
                                                                </h6>
                                                                <div class="info-item">
                                                                    <strong><i class="fas fa-tag"></i> Producto:</strong><br>
                                                                    <span class="text-muted">{{ $detalle->producto->producto }}</span>
                                                                </div>
                                                                <div class="info-item">
                                                                    <strong><i class="fas fa-barcode"></i> Código:</strong><br>
                                                                    <span class="text-muted">{{ $detalle->producto->codigo_barras ?? 'N/A' }}</span>
                                                                </div>
                                                                <div class="info-item">
                                                                    <strong><i class="fas fa-warehouse"></i> Stock Actual:</strong><br>
                                                                    <span class="badge badge-{{ $detalle->producto->cantidad >= $detalle->cantidad ? 'success' : 'danger' }}">
                                                                        <i class="fas fa-cubes"></i>
                                                                        {{ $detalle->producto->cantidad }} unidades
                                                                    </span>
                                                                </div>
                                                                <div class="info-item">
                                                                    <strong><i class="fas fa-shopping-cart"></i> Cantidad Solicitada:</strong><br>
                                                                    <span class="badge badge-info">
                                                                        <i class="fas fa-hashtag"></i>
                                                                        {{ $detalle->cantidad }} unidades
                                                                    </span>
                                                                </div>
                                                                @if($detalle->producto->cantidad < $detalle->cantidad)
                                                                    <div class="stock-alert">
                                                                        <i class="fas fa-exclamation-triangle"></i>
                                                                        <strong>¡Stock insuficiente!</strong>
                                                                        <p class="mb-0 mt-1 text-muted">
                                                                            No hay suficiente inventario disponible
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="divider"></div>
                                                    
                                                    <div class="motivo-section">
                                                        <strong><i class="fas fa-comment-dots"></i> Motivo de la Solicitud:</strong>
                                                        <div class="motivo-text">{{ $motivo }}</div>
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