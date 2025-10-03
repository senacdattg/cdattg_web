@extends('adminlte::page')

@section('title', 'Fichas de Caracterización')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-file-alt"></i> Fichas de Caracterización</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}">Inicio</a>
                </li>
                <li class="breadcrumb-item active">Fichas de Caracterización</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <!-- Alertas -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Botón de crear nueva ficha -->
    @can('CREAR PROGRAMA DE CARACTERIZACION')
        <div class="row mb-3">
            <div class="col-12">
                <a href="{{ route('fichaCaracterizacion.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nueva Ficha de Caracterización
                </a>
            </div>
        </div>
    @endcan

    <!-- Card principal -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list"></i> Lista de Fichas de Caracterización
            </h3>
            
            <!-- Filtros avanzados -->
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>

        <div class="card-body">
            <!-- Filtros -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <form method="GET" action="{{ route('fichaCaracterizacion.index') }}" class="form-inline">
                        <div class="form-group mr-3">
                            <label for="search" class="sr-only">Buscar</label>
                            <input type="text" name="search" class="form-control" placeholder="Buscar por número de ficha..." 
                                   value="{{ request()->get('search') }}">
                        </div>
                        
                        <div class="form-group mr-3">
                            <label for="estado" class="sr-only">Estado</label>
                            <select name="estado" class="form-control">
                                <option value="">Todos los estados</option>
                                <option value="1" {{ request()->get('estado') == '1' ? 'selected' : '' }}>Activas</option>
                                <option value="0" {{ request()->get('estado') == '0' ? 'selected' : '' }}>Inactivas</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary mr-2">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        
                        <a href="{{ route('fichaCaracterizacion.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    </form>
                </div>
            </div>

            <!-- Tabla responsiva -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th width="5%">ID</th>
                            <th width="15%">Número de Ficha</th>
                            <th width="25%">Programa de Formación</th>
                            <th width="15%">Sede</th>
                            <th width="15%">Modalidad</th>
                            <th width="10%">Estado</th>
                            <th width="15%">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($fichas as $ficha)
                            <tr>
                                <td>{{ $ficha->id }}</td>
                                <td>
                                    <span class="badge badge-info">{{ $ficha->ficha }}</span>
                                </td>
                                <td>{{ $ficha->programaFormacion->nombre ?? 'N/A' }}</td>
                                <td>{{ $ficha->sede->nombre ?? 'N/A' }}</td>
                                <td>{{ $ficha->modalidadFormacion->name ?? 'N/A' }}</td>
                                <td>
                                    @if($ficha->status)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check"></i> Activa
                                        </span>
                                    @else
                                        <span class="badge badge-danger">
                                            <i class="fas fa-times"></i> Inactiva
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @can('VER PROGRAMA DE CARACTERIZACION')
                                            <a href="{{ route('fichaCaracterizacion.show', $ficha->id) }}" 
                                               class="btn btn-info btn-sm" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endcan

                                        @can('EDITAR PROGRAMA DE CARACTERIZACION')
                                            <a href="{{ route('fichaCaracterizacion.edit', $ficha->id) }}" 
                                               class="btn btn-warning btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan

                                        @can('ELIMINAR PROGRAMA DE CARACTERIZACION')
                                            <button type="button" class="btn btn-danger btn-sm" 
                                                    onclick="confirmarEliminacion('{{ $ficha->ficha }}', '{{ route('fichaCaracterizacion.destroy', $ficha->id) }}')"
                                                    title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> No se encontraron fichas de caracterización.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($fichas->hasPages())
            <div class="card-footer">
                <div class="row">
                    <div class="col-sm-5">
                        <div class="dataTables_info">
                            Mostrando {{ $fichas->firstItem() }} a {{ $fichas->lastItem() }} 
                            de {{ $fichas->total() }} resultados
                        </div>
                    </div>
                    <div class="col-sm-7">
                        {{ $fichas->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmarEliminacion(nombre, url) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: `¿Deseas eliminar la ficha "${nombre}"? Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Crear formulario para enviar la petición DELETE
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    $(document).ready(function() {
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
</script>
@endsection