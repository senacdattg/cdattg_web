<div class="btn-group" role="group">
    @can('CAMBIAR ESTADO PERSONA')
        <form class="d-inline" action="{{ route('persona.cambiarEstadoPersona', $persona->id) }}" method="POST"
            title="Cambiar Estado" style="display: inline-block; margin-right: 2px;">
            @csrf
            @method('PUT')
            <button type="submit" class="btn btn-sm btn-light">
                <i class="fas fa-sync text-success"></i>
            </button>
        </form>
    @endcan

    @can('VER PERSONA')
        <a href="{{ route('personas.show', $persona->id) }}" class="btn btn-sm btn-light" title="Ver"
            style="margin-right: 2px;" @if (config('adminlte.livewire')) wire:navigate @endif>
            <i class="fas fa-eye text-warning"></i>
        </a>
    @endcan

    @can('RESTABLECER PASSWORD')
        <form class="d-inline reset-password-form" action="{{ route('personas.reset-password', $persona->id) }}"
            method="POST" title="Restablecer contraseña" style="display: inline-block; margin-right: 2px;"
            data-persona-nombre="{{ $persona->nombre_completo }}" data-numero-documento="{{ $persona->numero_documento }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-light">
                <i class="fas fa-key text-secondary"></i>
            </button>
        </form>
    @endcan

    @can('EDITAR PERSONA')
        @php
            $tieneUsuario = $persona->user !== null;
            $faltanCorreo = empty($persona->email);
            $faltanDocumento = empty($persona->numero_documento);
            $puedeCrearUsuario = !$tieneUsuario && !$faltanCorreo && !$faltanDocumento;
            $motivosCrear = collect([
                $tieneUsuario ? 'La persona ya tiene un usuario asociado.' : null,
                $faltanCorreo ? 'La persona no tiene correo registrado.' : null,
                $faltanDocumento ? 'La persona no tiene número de documento registrado.' : null,
            ])
                ->filter()
                ->implode(' ');
        @endphp
        <form class="d-inline create-user-form" action="{{ route('personas.create-user', $persona->id) }}" method="POST"
            title="{{ $puedeCrearUsuario ? 'Crear usuario' : ($motivosCrear ?: 'No es posible crear el usuario.') }}"
            style="display: inline-block; margin-right: 2px;" data-persona-nombre="{{ $persona->nombre_completo }}"
            data-persona-email="{{ $persona->email }}" data-numero-documento="{{ $persona->numero_documento }}"
            data-error="{{ $motivosCrear }}" data-disabled="{{ $puedeCrearUsuario ? 'false' : 'true' }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-light" @if (!$puedeCrearUsuario) disabled @endif>
                <i class="fas fa-user-plus text-success"></i>
            </button>
        </form>
    @endcan

    @can('EDITAR PERSONA')
        <a href="{{ route('personas.edit', $persona->id) }}" class="btn btn-sm btn-light" title="Editar"
            style="margin-right: 2px;" @if (config('adminlte.livewire')) wire:navigate @endif>
            <i class="fas fa-pencil-alt text-primary"></i>
        </a>
    @endcan

    @can('ELIMINAR PERSONA')
        <form class="d-inline eliminar-persona-form" action="{{ route('personas.destroy', $persona->id) }}" method="POST"
            title="Eliminar" style="display: inline-block;" data-persona-nombre="{{ $persona->nombre_completo }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-light">
                <i class="fas fa-trash-alt text-danger"></i>
            </button>
        </form>
    @endcan
</div>
