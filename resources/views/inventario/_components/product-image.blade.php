{{-- 
    Componente: Imagen de producto
    Props: $producto (object)
--}}
@props(['producto'])

<div class="show_img">
    <img 
        src="{{ $producto->imagen ? asset($producto->imagen) : asset('img/inventario/imagen_default.png') }}" 
        alt="Imagen del producto {{ $producto->producto }}" 
        class="clickable-img img-expandable"
    >
</div>
