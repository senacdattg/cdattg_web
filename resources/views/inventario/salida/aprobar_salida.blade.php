{{-- resources/views/inventario/salida/aprobar_salida.blade.php --}}
@extends('adminlte::page')

@vite(['resources/css/inventario/aprobar_salida.css', 'resources/js/inventario/aprobar_salida.js'])

@section('content')
<div class="container_show">
    <div class="div_titulo">
        <h2><i class="fas fa-file-alt"></i> Detalle de la orden</h2>
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
            <img 
                src="{{ $detalleOrden->producto->imagen ?? asset('img/inventario/imagen_default.png') }}" 
                alt="Imagen del producto" 
                class="img-fluid rounded shadow"
            >
        </div>
    </div>
    <div class="div_btn" style="gap: 20px;">
        <button type="button" class="btn_show btn_aprobar">
            <i class="fas fa-check"></i> Aprobar
        </button>
        <button type="button" class="btn_show btn_rechazar">
            <i class="fas fa-times"></i> Rechazar
        </button>
        <a href="{{ route('salida.aprobar') }}" class="btn_show">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
    <div id="mensajeAprobacion"></div>
</div>
@endsection
