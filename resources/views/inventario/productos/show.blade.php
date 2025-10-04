@extends('adminlte::page')

@section('classes_body', 'productos-page')

@section('plugins.Sweetalert2', true)

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

                    <li class="list-group-item"><i class="fas fa-solid fa-tag"></i> <strong>Marca:</strong> MARCA 1 </li>
                    <li class="list-group-item"><i class="fas fa-solid fa-list"></i> <strong>Categoría:</strong> CATEGORÍA 1 </li>
                    <li class="list-group-item"><i class="fas fa-solid fa-map-marker-alt"></i> <strong>Ubicación:</strong> UBICACIÓN 1 </li>
                    <li class="list-group-item"><i class="fas fa-solid fa-handshake"></i> <strong>Contrato convenio:</strong> CONTRATO 1 </li>
                    <li class="list-group-item"><i class="fas fa-calendar-alt"></i> <strong>Fecha de vencimiento:</strong> 01/01/2025 </li>

                                        
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
            <a href="{{ route('inventario.productos.index') }}" class="btn_show">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <a href="{{ route('inventario.productos.edit', $producto->id) }}" class="btn_show btn_warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            <button type="button" class="btn_show btn_danger" onclick="confirmarEliminacion()">
                <i class="fas fa-trash"></i> Eliminar
            </button>
            
            <!-- Formulario oculto para eliminar -->
            <form id="form-eliminar" action="{{ route('inventario.productos.destroy', $producto->id) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
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
                    <a href="{{ route('inventario.carrito.index') }}" class="btn_carrito">
                        <i class="fas fa-cart-plus"></i> Agregar al carrito
                    </a>
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

@push('js')
<script>
    function confirmarEliminacion() {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-eliminar').submit();
            }
        });
    }
</script>
@endpush
@endsection
