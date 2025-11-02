<div>
    <!-- Imagen -->
    <div class="modal-img-container">
        <img src="{{ $producto->imagen ? asset($producto->imagen) : asset('img/inventario/real.jpg') }}" 
             alt="{{ $producto->producto }}" 
             class="modal-img">
    </div>

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
            <td>{{ $producto->marca->name }}</td>
        </tr>
        <tr>
            <td>Categoría:</td>
            <td>{{ $producto->categoria->name }}</td>
        </tr>
        <tr>
            <td>Tipo:</td>
            <td>{{ $producto->tipoProducto->parametro->name ?? 'N/A' }}</td>
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
                <span class="modal-badge {{ $producto->estado->parametro->name === 'Activo' ? 'badge-success' : 'badge-secondary' }}">
                    {{ $producto->estado->parametro->name ?? 'N/A' }}
                </span>
            </td>
        </tr>
        <tr>
            <td>Ambiente:</td>
            <td>{{ $producto->ambiente->title }}</td>
        </tr>
        <tr>
            <td>Proveedor:</td>
            <td>{{ $producto->proveedor->proveedor ?? 'N/A' }}</td>
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
