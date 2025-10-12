{{--
    Componente: Card con Header
    Uso:
    <x-cards.header 
        title="Título de la Card" 
        icon="fas fa-users" 
        color="success" 
        badge="5"
        :shadow="true">
        <!-- Contenido de la card -->
    </x-cards.header>
--}}

@props([
    'title' => '',
    'icon' => null,
    'color' => 'primary',
    'shadow' => true,
    'badge' => null,
    'badgeColor' => 'info',
    'borderTop' => true
])

@php
    $borderColor = '#007bff'; // default
    switch($color) {
        case 'primary':
            $borderColor = '#007bff';
            break;
        case 'success':
            $borderColor = '#28a745';
            break;
        case 'warning':
            $borderColor = '#ffc107';
            break;
        case 'danger':
            $borderColor = '#dc3545';
            break;
        case 'info':
            $borderColor = '#17a2b8';
            break;
        default:
            $borderColor = '#007bff';
            break;
    }
@endphp

<div {{ $attributes->merge(['class' => 'card detail-card no-hover ' . ($shadow ? 'shadow-sm' : '')]) }}>
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center" 
         style="{{ $borderTop ? 'border-top: 3px solid ' . $borderColor . ';' : '' }}">
        <h5 class="card-title m-0 font-weight-bold text-{{ $color }}">
            @if($icon)
                <i class="{{ $icon }} mr-2"></i>
            @endif
            {{ $title }}
        </h5>
        @if($badge)
            <div class="badge badge-{{ $badgeColor }}">
                {{ $badge }}
            </div>
        @endif
    </div>
    <div class="card-body">
        {{ $slot }}
    </div>
</div>
