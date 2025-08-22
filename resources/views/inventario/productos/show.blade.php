@extends('adminlte::page')

@section('classes_body', 'productos-page')

@vite([
    'resources/css/inventario/shared/base.css',
    'resources/css/inventario/productos.css',
    'resources/css/inventario/carrito.css',
    'resources/js/inventario/productos.js',
    'resources/css/inventario/shared/modal-imagen.css',
    'resources/js/inventario/shared/modal-imagen.js'
])

@section('content')
<div class="flex_show">
    <div class="container_show">
        {{-- Mostrar errores de validación --}}
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="div_show">
            <div class="show_info">
                <ul class="list-show">
                    <li class="list-group-item"><i class="fas fa-tag"></i> <strong>Producto:</strong> {{ $producto->producto ?? 'N/A' }}</li>
                    <li class="list-group-item"><i class="fas fa-cubes"></i> <strong>Tipo de producto:</strong> {{ $producto->tipoProducto->parametro->name ?? 'N/A' }}</li>
                    <li class="list-group-item"><i class="fas fa-align-left"></i> <strong>Descripción:</strong> {{ $producto->descripcion }}</li>
                    <li class="list-group-item"><i class="fas fa-balance-scale"></i> <strong>Magnitud:</strong> {{$producto->peso}} {{ $producto->unidadMedida->parametro->name ?? 'N/A' }}</li>
                    <li class="list-group-item"><i class="fas fa-sort-numeric-up"></i> <strong>Cantidad:</strong> {{ $producto->cantidad }}</li>
                    <li class="list-group-item"><i class="fas fa-barcode"></i> <strong>Código de barras:</strong> {{ $producto->codigo_barras }}</li>
                    <li class="list-group-item"><i class="fas fa-check-circle"></i> <strong>Estado:</strong> {{ $producto->estado->parametro->name ?? 'N/A' }}</li>
                    @if($producto->contrato_convenio)
                        <li class="list-group-item"><i class="fas fa-file-contract"></i> <strong>Contrato convenio:</strong> {{ $producto->contrato_convenio }}</li>
                    @endif
                    @if($producto->fecha_vencimiento)
                        <li class="list-group-item"><i class="fas fa-calendar-times"></i> <strong>Fecha de vencimiento:</strong> {{ \Carbon\Carbon::parse($producto->fecha_vencimiento)->format('d/m/Y') }}</li>
                    @endif
                    @if($producto->categoria)
                        <li class="list-group-item"><i class="fas fa-layer-group"></i> <strong>Categoría:</strong> {{ $producto->categoria->parametro->name ?? 'N/A' }}</li>
                    @endif
                    @if($producto->marca)
                        <li class="list-group-item"><i class="fas fa-trademark"></i> <strong>Marca:</strong> {{ $producto->marca->parametro->name ?? 'N/A' }}</li>
                    @endif
                    @if($producto->ubicacion)
                        <li class="list-group-item"><i class="fas fa-map-marker-alt"></i> <strong>Ubicación:</strong> {{ $producto->ubicacion }}</li>
                    @endif
                    <li class="list-group-item"><i class="fas fa-calendar-alt"></i> <strong>Fecha de Ingreso:</strong> {{ $producto->created_at ? $producto->created_at->format('d/m/Y H:i') : 'N/A' }}</li>
                </ul>
            </div>
            <div class="show_img">
                <img 
                    src="{{ $producto->imagen ? asset($producto->imagen) : asset('img/inventario/imagen_default.png') }}" 
                    alt="Imagen del producto" 
                    class="clickable-img img-expandable"
                >
            </div>
        </div>
        <div class="div_btn">
            <a href="{{ route('productos.index') }}" class="btn_show">Volver</a>
        </div>
    </div>
    <div class="acciones_carrito">
        <div class="carrito_box">
            <h5><i class="fas fa-shopping-cart"></i> Carrito</h5>
            <form>
                @csrf
                <div class="form-group">
                    <label for="cantidad_carrito">Cantidad:</label>
                    <input type="number" name="cantidad" id="cantidad_carrito" class="form-control" min="1" max="{{ $producto->cantidad }}" value="1">
                </div>
                <div class="div_btn_carrito">
                    <button type="submit" class="btn_carrito">
                        <i class="fas fa-cart-plus"></i> Agregar al carrito
                    </button>
                </div>
                <div class="div_btn_carrito">
                    <button type="submit" class="btn_carrito">
                        <i class="fas fa-box"></i> Pedido
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal para imagen expandida -->
<div id="modalImagen" class="modal-imagen">
    <span class="cerrar">&times;</span>
    <img class="modal-contenido" id="imgExpandida">
</div>
@endsection
