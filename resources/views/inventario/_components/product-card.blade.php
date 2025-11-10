{{-- 
    Componente: Tarjeta de Producto
    Props: 
    - $producto (object): Objeto con datos del producto
    - $showActions (bool): Mostrar o no las acciones (default: true)
--}}
@props(['producto', 'showActions' => true])

<div 
    class="product-card" 
    data-product-id="{{ $producto->id }}"
>
    <div class="product-image-container">
        @if($producto->imagen)
            <img 
                src="{{ asset($producto->imagen) }}" 
                alt="{{ $producto->producto }}" 
                class="product-image img-expandable"
            >
        @else
            <div class="placeholder-image">
                <img 
                    src="https://placehold.co/300x300?text={{ urlencode($producto->producto) }}" 
                    alt="Vista previa de {{ $producto->producto }}" 
                    class="product-image img-expandable"
                >
            </div>
        @endif
        
        @if($producto->cantidad <= 5)
            <span class="badge badge-warning stock-badge">
                <i class="fas fa-exclamation-triangle"></i> Stock bajo
            </span>
        @endif
    </div>
    
    <div class="product-info">
        <h3 class="product-title">{{ $producto->producto }}</h3>
        
        <div class="product-details">
            <span class="badge badge-secondary">
                <i class="fas fa-box"></i> {{ $producto->cantidad }} unidades
            </span>
            @if($producto->categoria)
                <span class="badge badge-info">
                    <i class="fas fa-tag"></i> {{ $producto->categoria->name }}
                </span>
            @endif
        </div>

        @if(!empty($producto->codigo_barras))
            <div class="mt-2">
                <small class="text-muted">Código: {{ $producto->codigo_barras }}</small>
                <svg 
                    class="barcode-inline" 
                    id="barcode-card-{{ $producto->id }}" 
                    style="width:100%"
                ></svg>
            </div>
        @endif
        
        @if($showActions)
            <div class="product-actions">
                <a 
                    href="{{ route('inventario.productos.show', $producto->id) }}" 
                    class="btn btn-sm btn-info action-btn" 
                    title="Ver detalles"
                >
                    <i class="fas fa-eye"></i>
                </a>
                
                @can('EDITAR PRODUCTO')
                    <a 
                        href="{{ route('inventario.productos.edit', $producto->id) }}" 
                        class="btn btn-sm btn-warning action-btn" 
                        title="Editar"
                    >
                        <i class="fas fa-edit"></i>
                    </a>
                @endcan
                
                @can('ELIMINAR PRODUCTO')
                    <form 
                        action="{{ route('inventario.productos.destroy', $producto->id) }}" 
                        method="POST" 
                        class="d-inline"
                    >
                        @csrf
                        @method('DELETE')
                        <button 
                            type="submit" 
                            class="btn btn-sm btn-danger action-btn" 
                            title="Eliminar"
                            onclick="return confirm('¿Estás seguro de eliminar este producto?')"
                        >
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                @endcan
            </div>
        @endif
    </div>
</div>
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(!empty($producto->codigo_barras))
            try {
                JsBarcode(
                    '#barcode-card-{{ $producto->id }}', 
                    '{{ $producto->codigo_barras }}', 
                    {
                        format: 'code128', 
                        width: 1.6, 
                        height: 40, 
                        displayValue: false
                    }
                );
            } catch(e) {}
            @endif
        });
    </script>
@endpush

