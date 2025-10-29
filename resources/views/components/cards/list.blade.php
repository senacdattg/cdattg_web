{{--
    Componente: Card de Lista con Items
    Uso:
    <x-cards.list 
        title="Instructores Asignados" 
        icon="fas fa-users" 
        color="success"
        :items="$instructores" 
        emptyMessage="No hay instructores asignados">
        <x-slot name="item" :item="$item">
            <!-- Contenido del item -->
        </x-slot>
    </x-cards.list>
--}}

@props([
    'title' => '',
    'icon' => 'fas fa-list',
    'color' => 'primary',
    'items' => null,
    'emptyMessage' => 'No hay elementos para mostrar',
    'emptyIcon' => 'fas fa-inbox',
    'shadow' => true
])

<div {{ $attributes->merge(['class' => 'card shadow-sm no-hover ' . ($shadow ? 'shadow-sm' : '')]) }}>
    <div class="card-header bg-white py-3">
        <h5 class="card-title m-0 font-weight-bold text-{{ $color }}">
            @if($icon)
                <i class="{{ $icon }} mr-2"></i>
            @endif
            {{ $title }}
        </h5>
    </div>
    <div class="card-body">
        @if($items && $items->count() > 0)
            @foreach($items as $item)
                <x-cards.list-item>
                    {{ $slot->with(['item' => $item]) }}
                </x-cards.list-item>
            @endforeach
        @else
            <div class="text-center text-muted py-4">
                <i class="{{ $emptyIcon }} fa-3x mb-3"></i>
                <p>{{ $emptyMessage }}</p>
            </div>
        @endif
    </div>
</div>
