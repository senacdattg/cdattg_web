<div class="table-responsive">
    <table class="table table-borderless table-striped mb-0">
        <thead class="thead-light">
            <tr>
                <th class="px-4 py-3" style="width: 5%">#</th>
                <th class="px-4 py-3" style="width: 25%">Nombre</th>
                <th class="px-4 py-3" style="width: 15%">Documento</th>
                <th class="px-4 py-3" style="width: 20%">Especialidades</th>
                <th class="px-4 py-3" style="width: 10%">Estado</th>
                <th class="px-4 py-3 text-center" style="width: 25%">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($instructores as $instructor)
                <tr>
                    <td class="px-4">{{ $loop->iteration }}</td>
                    <td class="px-4 font-weight-medium">
                        {{ $instructor->persona->primer_nombre }} 
                        {{ $instructor->persona->primer_apellido }}
                    </td>
                    <td class="px-4">{{ $instructor->persona->numero_documento }}</td>
                    <td class="px-4">
                        @php
                            $especialidades = $instructor->especialidades ?? [];
                            $especialidadPrincipalId = $especialidades['principal'] ?? null;
                            $especialidadesSecundariasIds = $especialidades['secundarias'] ?? [];
                            
                            // Convertir IDs a nombres
                            $especialidadPrincipalNombre = null;
                            if ($especialidadPrincipalId) {
                                $redConocimiento = \App\Models\RedConocimiento::find($especialidadPrincipalId);
                                $especialidadPrincipalNombre = $redConocimiento ? $redConocimiento->nombre : null;
                            }
                            
                            $especialidadesSecundariasNombres = [];
                            if (!empty($especialidadesSecundariasIds)) {
                                $redesConocimiento = \App\Models\RedConocimiento::whereIn('id', $especialidadesSecundariasIds)->get();
                                $especialidadesSecundariasNombres = $redesConocimiento->pluck('nombre')->toArray();
                            }
                        @endphp
                        @if($especialidadPrincipalNombre)
                            <div class="d-inline-block px-2 py-1 rounded-pill bg-primary-light text-primary mr-1 mb-1 font-weight-medium">
                                {{ $especialidadPrincipalNombre }}
                            </div>
                        @endif
                        @if(count($especialidadesSecundariasNombres) > 0)
                            @foreach(array_slice($especialidadesSecundariasNombres, 0, 2) as $especialidadNombre)
                                <div class="d-inline-block px-2 py-1 rounded-pill bg-secondary-light text-secondary mr-1 mb-1 font-weight-medium">{{ $especialidadNombre }}</div>
                            @endforeach
                            @if(count($especialidadesSecundariasNombres) > 2)
                                <div class="d-inline-block px-2 py-1 rounded-pill bg-light text-muted mr-1 mb-1 font-weight-medium">+{{ count($especialidadesSecundariasNombres) - 2 }}</div>
                            @endif
                        @endif
                        @if(!$especialidadPrincipalNombre && count($especialidadesSecundariasNombres) === 0)
                            <span class="text-muted">Sin especialidades</span>
                        @endif
                    </td>
                    <td class="px-4">
                        <div class="d-inline-block px-3 py-1 rounded-pill {{ $instructor->status ? 'bg-success-light text-success' : 'bg-danger-light text-danger' }}">
                            <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                            {{ $instructor->status ? 'Activo' : 'Inactivo' }}
                        </div>
                    </td>
                    <td class="px-4 text-center">
                        <div class="btn-group">
                            @can('VER INSTRUCTOR')
                                <a href="{{ route('instructor.show', $instructor->id) }}" 
                                   class="btn btn-light btn-sm" 
                                   data-toggle="tooltip" 
                                   title="Ver detalles">
                                    <i class="fas fa-eye text-warning"></i>
                                </a>
                            @endcan
                            @can('EDITAR INSTRUCTOR')
                                <a href="{{ route('instructor.edit', $instructor->id) }}" 
                                   class="btn btn-light btn-sm" 
                                   data-toggle="tooltip" 
                                   title="Editar">
                                    <i class="fas fa-pencil-alt text-info"></i>
                                </a>
                            @endcan
                            @can('GESTIONAR ESPECIALIDADES INSTRUCTOR')
                                <a href="{{ route('instructor.gestionarEspecialidades', $instructor->id) }}" 
                                   class="btn btn-light btn-sm" 
                                   data-toggle="tooltip" 
                                   title="Gestionar especialidades">
                                    <i class="fas fa-graduation-cap text-primary"></i>
                                </a>
                            @endcan
                            @can('VER FICHAS ASIGNADAS')
                                <a href="{{ route('instructor.fichasAsignadas', $instructor->id) }}" 
                                   class="btn btn-light btn-sm" 
                                   data-toggle="tooltip" 
                                   title="Ver fichas asignadas">
                                    <i class="fas fa-clipboard-list text-success"></i>
                                </a>
                            @endcan
                            @can('ELIMINAR INSTRUCTOR')
                                <form action="{{ route('instructor.destroy', $instructor->id) }}" 
                                      method="POST" class="d-inline formulario-eliminar">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-light btn-sm" 
                                            data-toggle="tooltip" 
                                            title="Eliminar">
                                        <i class="fas fa-trash text-danger"></i>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <img src="{{ asset('img/no-data.svg') }}" alt="No data"
                            style="width: 120px" class="mb-3">
                        <p class="text-muted">No hay instructores registrados</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>