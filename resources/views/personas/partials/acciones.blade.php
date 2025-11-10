<div class="btn-group" role="group">
    @can('CAMBIAR ESTADO PERSONA')
        <form class="d-inline"
              action="{{ route('persona.cambiarEstadoPersona', $persona->id) }}"
              method="POST"
              title="Cambiar Estado"
              style="display: inline-block; margin-right: 2px;">
            @csrf
            @method('PUT')
            <button type="submit" class="btn btn-sm btn-light">
                <i class="fas fa-sync text-success"></i>
            </button>
        </form>
    @endcan

    @can('VER PERSONA')
        <a href="{{ route('personas.show', $persona->id) }}"
           class="btn btn-sm btn-light"
           title="Ver"
           style="margin-right: 2px;">
            <i class="fas fa-eye text-warning"></i>
        </a>
    @endcan

    @can('EDITAR PERSONA')
        <a href="{{ route('personas.edit', $persona->id) }}"
           class="btn btn-sm btn-light"
           title="Editar"
           style="margin-right: 2px;">
            <i class="fas fa-pencil-alt text-primary"></i>
        </a>
    @endcan

    @can('ELIMINAR PERSONA')
        <form class="d-inline eliminar-persona-form"
              action="{{ route('personas.destroy', $persona->id) }}"
              method="POST"
              title="Eliminar"
              style="display: inline-block;"
              data-persona-nombre="{{ $persona->nombre_completo }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-light">
                <i class="fas fa-trash-alt text-danger"></i>
            </button>
        </form>
    @endcan
</div>

