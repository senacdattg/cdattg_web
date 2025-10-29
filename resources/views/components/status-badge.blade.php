@props([
    'status' => true,
    'activeText' => 'Activo',
    'inactiveText' => 'Inactivo',
    'activeClass' => 'bg-success-light text-success',
    'inactiveClass' => 'bg-danger-light text-danger',
    'showIcon' => true
])

<div class="d-inline-block px-3 py-1 rounded-pill {{ $status ? $activeClass : $inactiveClass }}">
    @if($showIcon)
        <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
    @endif
    {{ $status ? $activeText : $inactiveText }}
</div>
