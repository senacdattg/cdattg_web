{{-- 
    Componente: Tarjeta de estadística para dashboard
    Props:
    - $title (string): Título de la tarjeta
    - $value (mixed): Valor a mostrar
    - $icon (string): Icono de FontAwesome
    - $bgClass (string): Clase de fondo (bg-info, bg-success, etc.)
    - $link (string): Enlace opcional
    - $linkText (string): Texto del enlace
--}}
@props([
    'title',
    'value',
    'icon' => 'fas fa-chart-bar',
    'bgClass' => 'bg-info',
    'link' => null,
    'linkText' => 'Más información'
])

<div class="col-lg-3 col-6">
    <div class="small-box {{ $bgClass }}">
        <div class="inner">
            <h3>{{ $value }}</h3>
            <p>{{ $title }}</p>
        </div>
        <div class="icon">
            <i class="{{ $icon }}"></i>
        </div>
        @if($link)
            <a href="{{ $link }}" class="small-box-footer">
                {{ $linkText }} <i class="fas fa-arrow-circle-right"></i>
            </a>
        @endif
    </div>
</div>

