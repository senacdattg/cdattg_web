@props([
    'title' => '',
    'icon' => null,
    'color' => 'primary',
    'badge' => null,
    'badgeColor' => 'info'
])

<div class="card detail-card no-hover shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center" 
         style="border-top: 3px solid {{ $color === 'primary' ? '#007bff' : '#28a745' }};">
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
