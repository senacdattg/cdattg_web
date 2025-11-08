<div>
    <!-- Imagen clickeable para expandir -->
    @php
        $imagenProducto = $producto->imagen ? asset($producto->imagen) : asset('img/inventario/producto-default.png');
    @endphp
    <div class="modal-img-container" style="cursor: pointer; position: relative; border-radius: 8px; overflow: hidden;" 
         onclick="$('#imageModal').modal('show'); $('#expandedImage').attr('src', '{{ $imagenProducto }}');">
        <img src="{{ $imagenProducto }}" 
             alt="{{ $producto->producto }}" 
             class="modal-img"
             style="width: 100%; height: 100%; object-fit: contain; border-radius: 8px;"
             title="Haz clic para ampliar">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; font-size: 2rem; opacity: 0; transition: opacity 0.3s; text-shadow: 0 2px 4px rgba(0,0,0,0.5);" 
             class="expand-icon" 
             onmouseover="this.style.opacity='1'" 
             onmouseout="this.style.opacity='0'">
            <i class="fas fa-expand"></i>
        </div>
    </div>
    
    <!-- Modal de Imagen Expandible -->
    @include('inventario._components.image-modal')

    <!-- Nombre del producto -->
    <h5 class="modal-title">{{ $producto->producto }}</h5>

    <!-- Tabla de información -->
    <table class="modal-table">
        <tr>
            <td>Código:</td>
            <td>{{ $producto->codigo_barras ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td>Marca:</td>
            <td>{{ $producto->marca?->name ?? 'Sin marca' }}</td>
        </tr>
        <tr>
            <td>Categoría:</td>
            <td>{{ $producto->categoria?->name ?? 'Sin categoría' }}</td>
        </tr>
        <tr>
            <td>Tipo:</td>
            <td>{{ $producto->tipoProducto?->parametro?->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td>Stock:</td>
            <td>
                <span class="modal-badge {{ $producto->cantidad > 0 ? 'badge-success' : 'badge-danger' }}">
                    {{ $producto->cantidad }} unidades
                </span>
            </td>
        </tr>
        <tr>
            <td>Peso:</td>
            <td>
                @if($producto->peso)
                    {{ $producto->peso }} kg
                @else
                    N/A
                @endif
            </td>
        </tr>
        <tr>
            <td>Estado:</td>
            <td>
                <span class="modal-badge {{ $producto->estado?->parametro?->name === 'DISPONIBLE' ? 'badge-success' : 'badge-danger' }}">
                    {{ $producto->estado?->parametro?->name ?? 'N/A' }}
                </span>
            </td>
        </tr>
        <tr>
            <td>Ambiente:</td>
            <td>{{ $producto->ambiente?->title ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td>Proveedor:</td>
            <td>{{ $producto->proveedor?->proveedor ?? 'N/A' }}</td>
        </tr>
        @if($producto->contratoConvenio)
        <tr>
            <td>Contrato/Convenio:</td>
            <td>{{ $producto->contratoConvenio->name }}</td>
        </tr>
        @endif
    </table>

    <!-- Descripción -->
    @if($producto->descripcion)
    <div class="modal-description">
        <strong>Descripción:</strong>
        <p id="descripcion">{{ $producto->descripcion }}</p>
    </div>
    @endif

    <!-- Botones -->
    <div class="modal-buttons">
        @if($producto->cantidad > 0)
            <button type="button" 
                    class="modal-btn modal-btn-success"
                    onclick="agregarAlCarritoDesdeModal({{ $producto->id }}, '{{ addslashes($producto->producto) }}', {{ $producto->cantidad }})">
                <i class="fas fa-cart-plus"></i> Agregar al Carrito
            </button>
        @else
            <button type="button" class="modal-btn modal-btn-disabled" disabled>
                Stock Agotado
            </button>
        @endif
        <button type="button" 
                class="modal-btn modal-btn-secondary"
                onclick="closeProductModal()">
            Cerrar
        </button>
    </div>
</div>

