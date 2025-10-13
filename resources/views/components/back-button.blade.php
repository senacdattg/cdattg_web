@props([
    'url' => '',
    'text' => 'Volver',
    'icon' => 'fa-arrow-left',
    'class' => 'btn-outline-secondary btn-sm mb-3'
])

<a href="{{ $url }}" class="btn {{ $class }}">
    <i class="fas {{ $icon }} mr-1"></i> {{ $text }}
</a>
