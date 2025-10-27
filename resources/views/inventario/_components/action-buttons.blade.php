{{-- 
    Componente: Botones de acción para tablas
    Props:
    - $routeShow (string): Ruta para ver
    - $routeEdit (string): Ruta para editar
    - $routeDelete (string): Ruta para eliminar
    - $itemId (int): ID del elemento
    - $itemName (string): Nombre del elemento para confirmar eliminación
    - $canEdit (bool): Permiso para editar
    - $canDelete (bool): Permiso para eliminar
--}}
@props([
    'routeShow' => null,
    'routeEdit' => null,
    'routeDelete' => null,
    'itemId',
    'itemName' => 'este elemento',
    'canEdit' => true,
    'canDelete' => true
])

<div class="action-buttons">
    @if($routeShow)
        <a href="{{ $routeShow }}" 
           class="btn btn-sm btn-info" 
           title="Ver detalles">
            <i class="fas fa-eye"></i>
        </a>
    @endif
    
    @if($routeEdit && $canEdit)
        <a href="{{ $routeEdit }}" 
           class="btn btn-sm btn-warning" 
           title="Editar">
            <i class="fas fa-edit"></i>
        </a>
    @endif
    
    @if($routeDelete && $canDelete)
        <form action="{{ $routeDelete }}" 
              method="POST" 
              class="d-inline"
              onsubmit="return confirm('¿Estás seguro de eliminar {{ $itemName }}?')">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    class="btn btn-sm btn-danger" 
                    title="Eliminar">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    @endif
</div>
