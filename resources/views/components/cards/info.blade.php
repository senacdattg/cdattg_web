{{--
    Componente: Card de Información con Items
    Uso:
    <x-cards.info 
        title="Información de la Ficha" 
        icon="fas fa-info-circle" 
        color="primary">
        <x-cards.info-item label="Programa" value="Desarrollo de Software" />
        <x-cards.info-item label="Fecha Inicio" value="01/01/2024" />
    </x-cards.info>
--}}

@props([
    'title' => '',
    'icon' => 'fas fa-info-circle',
    'color' => 'primary',
    'shadow' => true
])

<div {{ $attributes->merge(['class' => 'card detail-card no-hover ' . ($shadow ? 'shadow-sm' : '')]) }}>
    <div class="card-header bg-white py-3">
        <h5 class="card-title m-0 font-weight-bold text-{{ $color }}">
            @if($icon)
                <i class="{{ $icon }} mr-2"></i>
            @endif
            {{ $title }}
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            {{ $slot }}
        </div>
    </div>
</div>
