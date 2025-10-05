<!-- Tabla de Instructores -->
<div class="table-responsive">
    <table class="table table-custom">
        <thead>
            <tr>
                <th><i class="fas fa-user mr-1"></i>Instructor</th>
                <th><i class="fas fa-id-card mr-1"></i>Documento</th>
                <th><i class="fas fa-map-marker-alt mr-1"></i>Regional</th>
                <th><i class="fas fa-graduation-cap mr-1"></i>Especialidades</th>
                <th><i class="fas fa-clipboard-list mr-1"></i>Fichas</th>
                <th><i class="fas fa-toggle-on mr-1"></i>Estado</th>
                <th><i class="fas fa-cogs mr-1"></i>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($instructores as $instructor)
                @php
                    $especialidades = $instructor->especialidades ?? [];
                    $especialidadPrincipal = $especialidades['principal'] ?? null;
                    $especialidadesSecundarias = $especialidades['secundarias'] ?? [];
                    $totalFichas = $instructor->instructorFichas->count();
                @endphp
                
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle mr-3">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <strong>{{ $instructor->nombre_completo }}</strong>
                                <br>
                                <small class="text-muted">{{ $instructor->email }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-light">{{ $instructor->numero_documento }}</span>
                    </td>
                    <td>
                        @if($instructor->regional)
                            <span class="badge badge-info">{{ $instructor->regional->nombre }}</span>
                        @else
                            <span class="text-muted">Sin asignar</span>
                        @endif
                    </td>
                    <td>
                        @if($especialidadPrincipal)
                            <div class="mb-1">
                                <span class="badge badge-primary">
                                    <i class="fas fa-star mr-1"></i>{{ $especialidadPrincipal }}
                                </span>
                            </div>
                        @endif
                        @if(count($especialidadesSecundarias) > 0)
                            <div>
                                @foreach(array_slice($especialidadesSecundarias, 0, 2) as $especialidad)
                                    <span class="badge badge-secondary mr-1">{{ $especialidad }}</span>
                                @endforeach
                                @if(count($especialidadesSecundarias) > 2)
                                    <span class="badge badge-light">+{{ count($especialidadesSecundarias) - 2 }}</span>
                                @endif
                            </div>
                        @endif
                        @if(!$especialidadPrincipal && count($especialidadesSecundarias) === 0)
                            <span class="text-muted">Sin especialidades</span>
                        @endif
                    </td>
                    <td>
                        @if($totalFichas > 0)
                            <span class="badge badge-success">
                                <i class="fas fa-clipboard-list mr-1"></i>{{ $totalFichas }}
                            </span>
                        @else
                            <span class="badge badge-light">Sin fichas</span>
                        @endif
                    </td>
                    <td>
                        @if($instructor->status)
                            <span class="badge badge-success">
                                <i class="fas fa-check-circle mr-1"></i>Activo
                            </span>
                        @else
                            <span class="badge badge-danger">
                                <i class="fas fa-times-circle mr-1"></i>Inactivo
                            </span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            @can('VER INSTRUCTOR')
                                <a href="{{ route('instructor.show', $instructor->id) }}" 
                                   class="btn btn-sm btn-outline-primary btn-action" 
                                   title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                            @endcan
                            
                            @can('EDITAR INSTRUCTOR')
                                <a href="{{ route('instructor.edit', $instructor->id) }}" 
                                   class="btn btn-sm btn-outline-warning btn-action" 
                                   title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endcan
                            
                            @can('GESTIONAR ESPECIALIDADES INSTRUCTOR')
                                <a href="{{ route('instructor.gestionarEspecialidades', $instructor->id) }}" 
                                   class="btn btn-sm btn-outline-info btn-action" 
                                   title="Gestionar especialidades">
                                    <i class="fas fa-graduation-cap"></i>
                                </a>
                            @endcan
                            
                            @can('VER FICHAS ASIGNADAS')
                                <a href="{{ route('instructor.fichasAsignadas', $instructor->id) }}" 
                                   class="btn btn-sm btn-outline-secondary btn-action" 
                                   title="Ver fichas asignadas">
                                    <i class="fas fa-clipboard-list"></i>
                                </a>
                            @endcan
                            
                            @can('CAMBIAR ESTADO INSTRUCTOR')
                                <form method="POST" action="{{ route('instructor.cambiarEstado', $instructor->id) }}" 
                                      class="d-inline" 
                                      onsubmit="return confirm('¿Está seguro de cambiar el estado del instructor?')">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="estado" value="{{ $instructor->status ? 'inactivo' : 'activo' }}">
                                    <button type="submit" 
                                            class="btn btn-sm btn-outline-{{ $instructor->status ? 'danger' : 'success' }} btn-action" 
                                            title="{{ $instructor->status ? 'Desactivar' : 'Activar' }}">
                                        <i class="fas fa-{{ $instructor->status ? 'toggle-off' : 'toggle-on' }}"></i>
                                    </button>
                                </form>
                            @endcan
                            
                            @can('ELIMINAR INSTRUCTOR')
                                <form method="POST" action="{{ route('instructor.destroy', $instructor->id) }}" 
                                      class="d-inline" 
                                      onsubmit="return confirm('¿Está seguro de eliminar este instructor?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger btn-action" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <div class="empty-state">
                            <i class="fas fa-search" style="font-size: 3rem; opacity: 0.3; color: #6c757d;"></i>
                            <h5 class="mt-3 text-muted">No se encontraron instructores</h5>
                            <p class="text-muted">Intenta ajustar los filtros de búsqueda</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Paginación -->
@if($instructores->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $instructores->links() }}
    </div>
@endif
