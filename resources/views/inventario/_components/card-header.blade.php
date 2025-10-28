{{-- 
    Componente: Header de card con icono y título
    Props:
    - $title (string): Título del card
    - $icon (string): Icono de FontAwesome
    - $subtitle (string): Subtítulo opcional
    - $bgClass (string): Clase de fondo (bg-primary, bg-success, etc.)
    - $textClass (string): Clase de texto (text-white, text-dark, etc.)
--}}
@props([
    'title',
    'icon' => 'fas fa-list',
    'subtitle' => null,
    'bgClass' => 'bg-primary',
    'textClass' => 'text-white'
])

<div class="card-header {{ $bgClass }} {{ $textClass }}">
    <h3 class="card-title mb-0">
        <i class="{{ $icon }} mr-2"></i>
        {{ $title }}
        @if($subtitle)
            <small class="d-block">{{ $subtitle }}</small>
        @endif
    </h3>
</div>

