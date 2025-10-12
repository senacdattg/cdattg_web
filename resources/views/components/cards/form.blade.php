{{--
    Componente: Card de Formulario
    Uso:
    <x-cards.form 
        title="Asignar Instructores" 
        icon="fas fa-user-plus" 
        color="warning"
        action="{{ route('ficha.asignar') }}" 
        method="POST">
        <!-- Contenido del formulario -->
    </x-cards.form>
--}}

@props([
    'title' => '',
    'icon' => 'fas fa-edit',
    'color' => 'primary',
    'action' => '',
    'method' => 'POST',
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
        <form action="{{ $action }}" method="{{ $method }}" {{ $attributes->whereStartsWith('form-') }}>
            @csrf
            @if($method !== 'GET' && $method !== 'POST')
                @method($method)
            @endif
            {{ $slot }}
        </form>
    </div>
</div>
