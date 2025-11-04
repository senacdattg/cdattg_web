{{-- 
    Componente: Encabezado con botón de crear para modales
    Props:
    - $title (string): Título principal
    - $subtitle (string): Subtítulo opcional
    - $icon (string): Icono del título
    - $modalTarget (string): ID del modal a abrir
    - $buttonText (string): Texto del botón
--}}
@props([
    'title',
    'subtitle' => null,
    'icon' => 'fas fa-list',
    'modalTarget' => null,
    'buttonText' => 'Nuevo'
])

<div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
    <div>
        <h1 class="mb-1">
            <i class="{{ $icon }}"></i> {{ $title }}
        </h1>
        @if($subtitle)
            <p class="subtitle mb-0 text-muted">{{ $subtitle }}</p>
        @endif
    </div>
    
    @if($modalTarget)
        <button type="button" 
                class="btn btn-primary btn-lg" 
                data-toggle="modal" 
                data-target="#{{ $modalTarget }}">
            <i class="fas fa-plus me-2"></i> {{ $buttonText }}
        </button>
    @endif
</div>
