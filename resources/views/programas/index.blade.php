@extends('adminlte::page')

@section('title', 'Programas de Formación')

@section('content_header')
    <h1>
        <i class="fas fa-graduation-cap"></i>
        Programas de Formación
        @can('programa.create')
            <a href="{{ route('programa.create') }}" class="btn btn-primary btn-sm float-right">
                <i class="fas fa-plus"></i> Nuevo Programa
            </a>
        @endcan
    </h1>
@stop

@section('content')
    <div class="container-fluid">
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
        <!-- Filtros y búsqueda -->
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter"></i> Filtros y Búsqueda
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="get" action="{{ route('programa.search') }}" class="row">
                    <div class="col-md-8">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control"
                                placeholder="Buscar por código, nombre, red de conocimiento o nivel de formación..." 
                                value="{{ request()->get('search') }}">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('programa.index') }}" class="btn btn-secondary">
                            <i class="fas fa-refresh"></i> Limpiar Filtros
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla de programas -->
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i> Lista de Programas de Formación
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 8%">
                                    <i class="fas fa-hashtag"></i> ID
                                </th>
                                <th style="width: 12%">
                                    <i class="fas fa-code"></i> Código
                                </th>
                                <th style="width: 25%">
                                    <i class="fas fa-graduation-cap"></i> Nombre del Programa
                                </th>
                                <th style="width: 20%">
                                    <i class="fas fa-network-wired"></i> Red de Conocimiento
                                </th>
                                <th style="width: 15%">
                                    <i class="fas fa-layer-group"></i> Nivel de Formación
                                </th>
                                <th style="width: 10%">
                                    <i class="fas fa-toggle-on"></i> Estado
                                </th>
                                <th style="width: 10%">
                                    <i class="fas fa-cogs"></i> Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($programas as $programa)
                                <tr>
                                    <td>
                                        <span class="badge badge-info">{{ $programa->id }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">{{ $programa->codigo }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $programa->nombre }}</strong>
                                    </td>
                                    <td>
                                        @if($programa->redConocimiento)
                                            <span class="text-primary">
                                                <i class="fas fa-network-wired"></i> 
                                                {{ $programa->redConocimiento->nombre }}
                                            </span>
                                        @else
                                            <span class="text-muted">Sin asignar</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($programa->nivelFormacion)
                                            <span class="text-success">
                                                <i class="fas fa-layer-group"></i> 
                                                {{ $programa->nivelFormacion->name }}
                                            </span>
                                        @else
                                            <span class="text-muted">Sin asignar</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($programa->status)
                                            <span class="badge badge-success">
                                                <i class="fas fa-check"></i> Activo
                                            </span>
                                        @else
                                            <span class="badge badge-danger">
                                                <i class="fas fa-times"></i> Inactivo
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @can('programa.show')
                                                <a href="{{ route('programa.show', $programa->id) }}" 
                                                   class="btn btn-info btn-sm" 
                                                   title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endcan
                                            @can('programa.edit')
                                                <a href="{{ route('programa.edit', $programa->id) }}" 
                                                   class="btn btn-warning btn-sm" 
                                                   title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan
                                            @can('programa.delete')
                                                <button type="button" 
                                                        class="btn btn-danger btn-sm btn-delete" 
                                                        title="Eliminar"
                                                        data-id="{{ $programa->id }}"
                                                        data-name="{{ $programa->nombre }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p class="mb-0">No hay programas de formación disponibles.</p>
                                            @can('programa.create')
                                                <a href="{{ route('programa.create') }}" class="btn btn-primary mt-2">
                                                    <i class="fas fa-plus"></i> Crear primer programa
                                                </a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($programas->hasPages())
                <div class="card-footer clearfix">
                    {{ $programas->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de confirmación de eliminación -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        Confirmar Eliminación
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar el programa de formación <strong id="programaName"></strong>?</p>
                    <p class="text-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        Esta acción no se puede deshacer.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        $('.btn-delete').on('click', function() {
            const id = $(this).data('id');
            const nombre = $(this).data('name');
            
            document.getElementById('programaName').textContent = nombre;
            document.getElementById('deleteForm').action = '{{ route("programa.destroy", ":id") }}'.replace(':id', id);
            $('#deleteModal').modal('show');
        });
    });
</script>
@endsection
