{{--
    Componente: Item de Lista
    Uso:
    <x-cards.list-item class="border-primary">
        <!-- Contenido del item -->
    </x-cards.list-item>
--}}

@props([
    'class' => 'mb-3'
])

<div class="card {{ $class }}">
    <div class="card-body">
        {{ $slot }}
    </div>
</div>
