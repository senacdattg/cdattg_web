@props(['producto'])

<div class="cart-widget">
    {{-- Información del producto --}}
    <div class="product-info mb-3">
        <div class="row">
            <div class="col-4">
                <strong>Stock disponible:</strong>
            </div>
            <div class="col-8">
                <span class="badge badge-{{ $producto->cantidad <= 5 ? 'danger' : ($producto->cantidad <= 10 ? 'warning' : 'success') }}">
                    {{ $producto->cantidad }} unidades
                </span>
            </div>
        </div>
    </div>

    {{-- Formulario para agregar al carrito --}}
    <form id="add-to-cart-form" class="cart-form">
        @csrf
        <input type="hidden" name="producto_id" value="{{ $producto->id }}">
        <input type="hidden" name="producto_name" value="{{ $producto->producto }}">
        <input type="hidden" name="max_stock" value="{{ $producto->cantidad }}">

        <div class="form-group">
            <label for="cart-quantity" class="form-label">
                <i class="fas fa-hashtag"></i> Cantidad:
            </label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="decrease-qty">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
                <input type="number"
                       class="form-control text-center"
                       id="cart-quantity"
                       name="cantidad"
                       value="1"
                       min="1"
                       max="{{ $producto->cantidad }}"
                       required>
                <div class="input-group-append">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="increase-qty">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <small class="form-text text-muted">
                Máximo disponible: {{ $producto->cantidad }} unidades
            </small>
        </div>

        {{-- Botón de agregar al carrito --}}
        <button type="submit"
                class="btn btn-success btn-block btn-lg"
                id="add-to-cart-btn"
                {{ $producto->cantidad <= 0 ? 'disabled' : '' }}>
            <i class="fas fa-cart-plus"></i>
            {{ $producto->cantidad <= 0 ? 'Agotado' : 'Agregar al Carrito' }}
        </button>
    </form>

    {{-- Enlaces adicionales --}}
    <div class="additional-links mt-3 text-center">
        <a href="{{ route('inventario.carrito.ecommerce') }}" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-shopping-cart"></i> Ver Carrito
        </a>
        <a href="{{ route('inventario.productos.catalogo') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-store"></i> Seguir Comprando
        </a>
    </div>
</div>

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('add-to-cart-form');
    const quantityInput = document.getElementById('cart-quantity');
    const decreaseBtn = document.getElementById('decrease-qty');
    const increaseBtn = document.getElementById('increase-qty');
    const addToCartBtn = document.getElementById('add-to-cart-btn');

    if (!form || !quantityInput) return;

    // Control de cantidad
    decreaseBtn?.addEventListener('click', function() {
        const currentValue = parseInt(quantityInput.value);
        if (currentValue > 1) {
            quantityInput.value = currentValue - 1;
        }
    });

    increaseBtn?.addEventListener('click', function() {
        const currentValue = parseInt(quantityInput.value);
        const maxValue = parseInt(quantityInput.max);
        if (currentValue < maxValue) {
            quantityInput.value = currentValue + 1;
        }
    });

    // Validación de cantidad
    quantityInput.addEventListener('input', function() {
        const value = parseInt(this.value);
        const min = parseInt(this.min);
        const max = parseInt(this.max);

        if (value < min) this.value = min;
        if (value > max) this.value = max;
    });

    // Enviar formulario
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const productId = formData.get('producto_id');
        const productName = formData.get('producto_name');
        const quantity = parseInt(formData.get('cantidad'));
        const maxStock = parseInt(formData.get('max_stock'));

        // Agregar al carrito usando la función global
        if (window.inventarioCard && window.inventarioCard.addToCart) {
            window.inventarioCard.addToCart(productId, productName, maxStock, quantity);
        } else {
            // Fallback: agregar directamente al localStorage
            addToCartDirectly(productId, productName, quantity, maxStock);
        }

        // Mostrar notificación
        Swal.fire({
            icon: 'success',
            title: '¡Agregado al carrito!',
            text: `${quantity} unidad(es) de "${productName}" agregada(s) al carrito`,
            timer: 3000,
            showConfirmButton: false
        });
    });

    // Función fallback para agregar al carrito
    function addToCartDirectly(productId, productName, quantity, maxStock) {
        const STORAGE_KEY = 'inventario_carrito';
        let cart = JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];

        const existingItem = cart.find(item => item.id === productId);

        if (existingItem) {
            if (existingItem.quantity + quantity > maxStock) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Stock Insuficiente',
                    text: `Solo puedes agregar ${maxStock - existingItem.quantity} unidades más`
                });
                return;
            }
            existingItem.quantity += quantity;
        } else {
            cart.push({
                id: productId,
                name: productName,
                quantity: quantity,
                maxStock: maxStock
            });
        }

        localStorage.setItem(STORAGE_KEY, JSON.stringify(cart));

        // Actualizar contador si existe
        updateCartCount();
    }

    // Actualizar contador del carrito
    function updateCartCount() {
        const countBadge = document.getElementById('cart-count');
        if (countBadge) {
            const cart = JSON.parse(localStorage.getItem('inventario_carrito')) || [];
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            countBadge.textContent = totalItems;
        }
    }
});
</script>
@endpush</content>
<parameter name="filePath">c:\laragon\www\cdattg_asistence_web\resources\views\inventario\_components\cart-widget.blade.php
