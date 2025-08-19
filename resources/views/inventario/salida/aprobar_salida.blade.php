{{-- resources/views/inventario/salida/aprobar_salida.blade.php --}}
@extends('adminlte::page')

@section('classes_body', 'salidas-page')

@vite(['resources/css/inventario/aprobar_salida.css', 'resources/css/inventario/shared/modal-info.css', 'resources/js/inventario/aprobar_salida.js'])

@section('content')
<div class="container_show">
    <div class="div_titulo">
        <div class="div_titulo_cerrar">
            <h2></i> Detalle de la orden</h2>
            <a href="{{ route('salida.aprobar') }}" class="close-modal custom-tooltip">
                &times;
                <span class="tooltip-text">Volver al listado</span>
            </a>
        </div>
    </div>
    <div class="div_show">
        <div class="show_info">
            <ul class="list-show">
                <li class="list-group-item">
                    <i class="fas fa-hashtag"></i>
                    <strong>N° Orden:</strong> {{ $detalleOrden->orden_id ?? 'N/A' }}
                </li>
                <li class="list-group-item">
                    <i class="fas fa-box"></i>
                    <strong>Producto:</strong> {{ $detalleOrden->producto_id ?? 'N/A' }}
                </li>
                <li class="list-group-item">
                    <i class="fas fa-sort-numeric-up"></i>
                    <strong>Cantidad:</strong> {{ $detalleOrden->cantidad ?? 'N/A' }}
                </li>
                <li class="list-group-item">
                    <i class="fas fa-info-circle"></i>
                    <strong>Estado:</strong> {{ $detalleOrden->estado_orden_id ?? 'N/A' }}
                </li>
            </ul>
            
        </div>
        <div class="show_img inventario-img-box">
            <button type="button" class="btn_show" onclick="document.getElementById('modalDetalleOrden').classList.add('show');">
                <i class="fas fa-search-plus"></i> Detalles
            </button>
        </div>
        </div>
        <div class="btn_acciones">
            <button type="button" class="btn_show btn_aprobar_modal" onclick="document.getElementById('modalAprobarRechazar').classList.add('show');">
                <i class="fas fa-check"></i> Acciones
            </button>
        </div>
</div>

<!-- Modal Aprobar/Rechazar Orden (fuera del div principal para evitar conflictos de estilos) -->
<div id="modalAprobarRechazar" class="modal-aprobar-salida modal-info-force">
    <div class="modal-content">
        <div class="modal-header">
            <h4>
                <span id="iconAprobarRechazar">
                    <i class="fas fa-check"></i>
                </span>
                Aprobar o Rechazar
            </h4>
            <button class="close-modal" onclick="document.getElementById('modalAprobarRechazar').classList.remove('show');">&times;</button>
        </div>
        <div class="modal-body">
            <form id="formAprobarSalida">
                <div class="div_btn">
                    <button type="submit" class="btn_show btn_aprobar">
                        <i class="fas fa-check"></i> Aprobar
                    </button>
                    <button type="submit" class="btn_show btn_rechazar">
                        <i class="fas fa-times"></i> Rechazar
                    </button>
                </div>
                <div id="mensajeAprobacion"></div>
                <div class="div_motivo_rechazo" style="display:none;">
                    <label for="motivo_rechazo" class="form-label"><i class="fas fa-comment-dots"></i> Motivo del rechazo:</label>
                    <textarea id="motivo_rechazo" class="form-control" rows="3" placeholder="Escribe el motivo por el cual rechazas la orden..."></textarea>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="modalDetalleOrden" class="modal-aprobar-salida modal-info-force">
    <div class="modal-content">
        <div class="modal-header">
            <h4><i class="fas fa-info-circle"></i> Información completa de la orden</h4>
            <button class="close-modal" onclick="document.getElementById('modalDetalleOrden').classList.remove('show');">&times;</button>
        </div>
        <div class="modal-body">
            <ul>
                <li><strong><i class="fas fa-hashtag"></i>ID:</strong> <span>{{ $detalleOrden->orden_id ?? 'N/A' }}</span></li>
                <li><strong><i class="fas fa-comment-alt"></i>Descripción:</strong> <span>{{ $detalleOrden->descripcion_orden ?? 'N/A' }}</span></li>
                <li><strong><i class="fas fa-box-open"></i>Tipo de orden:</strong> <span>{{ $detalleOrden->tipo_orden_id ?? 'N/A' }}</span></li>
                <li><strong><i class="fas fa-calendar-alt"></i>Fecha devolución:</strong> <span>{{ $detalleOrden->fecha_devolucion ?? 'N/A' }}</span></li>
                <li><strong><i class="fas fa-user-plus"></i>Usuario crea:</strong> <span>{{ $detalleOrden->user_create_id ?? 'N/A' }}</span></li>
                <li><strong><i class="fas fa-user-edit"></i>Usuario actualiza:</strong> <span>{{ $detalleOrden->user_update_id ?? 'N/A' }}</span></li>
                <li><strong><i class="fas fa-clock"></i>Creado en:</strong> <span>{{ $detalleOrden->create_at ?? 'N/A' }}</span></li>
                <li><strong><i class="fas fa-sync-alt"></i>Actualizado en:</strong> <span>{{ $detalleOrden->update_at ?? 'N/A' }}</span></li>
            </ul>
        </div>
    </div>
</div>
@endsection
