@php
    $badgeClass = $persona->estado_sofia_badge_class ?? 'bg-secondary';
    $label = $persona->estado_sofia_label ?? 'Sin validar';
@endphp

<span class="badge {{ $badgeClass }} text-white px-2 py-1">
    {{ $label }}
</span>

