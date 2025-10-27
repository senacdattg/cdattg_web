{{-- 
    Componente: Estado vacío
    Props:
    - $message (string): Mensaje a mostrar
    - $icon (string): Clase del icono
    - $actionRoute (string): Ruta del botón de acción
    - $actionText (string): Texto del botón
--}}
@props([
    'message' => 'No hay elementos para mostrar',
    'icon' => 'fas fa-box-open',
    'actionRoute' => null,
    'actionText' => 'Crear nuevo'
])

<div class="empty-state text-center py-5">
    <i class="{{ $icon }} fa-3x text-muted mb-3"></i>
    <h4 class="text-muted">{{ $message }}</h4>
    
    @if($actionRoute)
        <a href="{{ $actionRoute }}" class="btn btn-primary mt-3">
            <i class="fas fa-plus"></i> {{ $actionText }}
        </a>
    @endif
</div>
