{{-- 
    Componente: Widget de carrito para agregar producto
    Props: $producto (object)
--}}
@props(['producto'])

<div class="acciones_carrito">
    <div class="carrito_box">
        <h5><i class="fas fa-shopping-cart"></i> Carrito</h5>
        <form action="{{ route('inventario.carrito.agregar') }}" method="POST">
            @csrf
            <input type="hidden" name="producto_id" value="{{ $producto->id }}">
            
            <div class="form-group">
                <label for="cantidad_carrito">Cantidad:</label>
                <input type="number" 
                       name="cantidad" 
                       id="cantidad_carrito" 
                       class="form-control" 
                       min="1" 
                       max="{{ $producto->cantidad }}" 
                       value="1"
                       {{ $producto->cantidad == 0 ? 'disabled' : '' }}>
                
                @if($producto->cantidad == 0)
                    <small class="text-danger">
                        <i class="fas fa-exclamation-circle"></i> Sin stock disponible
                    </small>
                @elseif($producto->cantidad <= 5)
                    <small class="text-warning">
                        <i class="fas fa-exclamation-triangle"></i> Stock bajo ({{ $producto->cantidad }} disponibles)
                    </small>
                @endif
            </div>
            
            <div class="div_btn_carrito">
                <button type="submit" 
                        class="btn_carrito" 
                        {{ $producto->cantidad == 0 ? 'disabled' : '' }}>
                    <i class="fas fa-cart-plus"></i> Agregar al carrito
                </button>
            </div>
            
            <div class="div_btn_carrito">
                <a href="{{ route('inventario.carrito.index') }}" class="btn_carrito btn-secondary">
                    <i class="fas fa-shopping-cart"></i> Ver carrito
                </a>
            </div>
        </form>
    </div>
</div>
