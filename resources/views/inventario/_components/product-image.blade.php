{{-- 
    Componente: Imagen de producto
    Props: $producto (object)
--}}
@props(['producto'])

<div class="show_img">
    <img
        src="{{ $producto->imagen ? asset($producto->imagen) : asset('img/no-image.png') }}"
        alt="Imagen del producto {{ $producto->producto }}"
        class="clickable-img img-expandable"
        onerror="this.onerror=null; this.src='{{ asset('img/no-image.png') }}'"
    >
</div>
