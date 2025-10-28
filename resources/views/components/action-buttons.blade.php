@props([
    'show' => false,
    'edit' => false,
    'delete' => false,
    'custom' => [],
    'showUrl' => '',
    'editUrl' => '',
    'deleteUrl' => '',
    'showTitle' => 'Ver detalles',
    'editTitle' => 'Editar',
    'deleteTitle' => 'Eliminar',
    'showPermission' => '',
    'editPermission' => '',
    'deletePermission' => '',
    'modelName' => '',
    'modelId' => ''
])

<div class="btn-group">
    @if($show && $showUrl)
        @if($showPermission)
            @can($showPermission)
                <a href="{{ $showUrl }}" 
                   class="btn btn-light btn-sm" 
                   data-toggle="tooltip" 
                   title="{{ $showTitle }}">
                    <i class="fas fa-eye text-warning"></i>
                </a>
            @endcan
        @else
            <a href="{{ $showUrl }}" 
               class="btn btn-light btn-sm" 
               data-toggle="tooltip" 
               title="{{ $showTitle }}">
                <i class="fas fa-eye text-warning"></i>
            </a>
        @endif
    @endif

    @if($edit && $editUrl)
        @if($editPermission)
            @can($editPermission)
                <a href="{{ $editUrl }}" 
                   class="btn btn-light btn-sm" 
                   data-toggle="tooltip" 
                   title="{{ $editTitle }}">
                    <i class="fas fa-pencil-alt text-info"></i>
                </a>
            @endcan
        @else
            <a href="{{ $editUrl }}" 
               class="btn btn-light btn-sm" 
               data-toggle="tooltip" 
               title="{{ $editTitle }}">
                <i class="fas fa-pencil-alt text-info"></i>
            </a>
        @endif
    @endif

    @if($delete && $deleteUrl)
        @if($deletePermission)
            @can($deletePermission)
                <form action="{{ $deleteUrl }}" 
                      method="POST" 
                      class="d-inline formulario-eliminar">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="btn btn-light btn-sm" 
                            data-toggle="tooltip" 
                            title="{{ $deleteTitle }}">
                        <i class="fas fa-trash text-danger"></i>
                    </button>
                </form>
            @endcan
        @else
            <form action="{{ $deleteUrl }}" 
                  method="POST" 
                  class="d-inline formulario-eliminar">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="btn btn-light btn-sm" 
                        data-toggle="tooltip" 
                        title="{{ $deleteTitle }}">
                    <i class="fas fa-trash text-danger"></i>
                </button>
            </form>
        @endif
    @endif

    @foreach($custom as $button)
        @if(isset($button['permission']))
            @can($button['permission'])
                <a href="{{ $button['url'] }}" 
                   class="btn btn-light btn-sm" 
                   data-toggle="tooltip" 
                   title="{{ $button['title'] }}">
                    <i class="{{ $button['icon'] }} {{ $button['color'] ?? 'text-primary' }}"></i>
                </a>
            @endcan
        @else
            <a href="{{ $button['url'] }}" 
               class="btn btn-light btn-sm" 
               data-toggle="tooltip" 
               title="{{ $button['title'] }}">
                <i class="{{ $button['icon'] }} {{ $button['color'] ?? 'text-primary' }}"></i>
            </a>
        @endif
    @endforeach
</div>