@extends('adminlte::page')

@section('title', 'Aprendices')

@section('content')
    <!-- Encabezado de la página -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Aprendices</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('verificarLogin') }}">Inicio</a>
                        </li>
                        <li class="breadcrumb-item active">Aprendices</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Contenido principal -->
    <section class="content">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Aprendices</h3>
                <div class="card-tools">
                    @can('CREAR APRENDIZ')
                        <a href="{{ route('aprendices.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Crear Aprendiz
                        </a>
                    @endcan
                </div>
            </div>

            <!-- Filtros -->
            <div class="card-body">
                <form action="{{ route('aprendices.index') }}" method="GET" class="mb-3">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="search">Buscar por nombre o documento</label>
                                <input type="text" name="search" id="search" class="form-control" 
                                    placeholder="Ingrese nombre o número de documento" 
                                    value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="ficha_id">Filtrar por ficha</label>
                                <select name="ficha_id" id="ficha_id" class="form-control">
                                    <option value="">Todas las fichas</option>
                                    @foreach($fichas as $ficha)
                                        <option value="{{ $ficha->id }}" 
                                            {{ request('ficha_id') == $ficha->id ? 'selected' : '' }}>
                                            {{ $ficha->ficha }} - {{ $ficha->programaFormacion->nombre ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-info btn-block">
                                        <i class="fas fa-search"></i> Buscar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped projects text-center">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre y Apellido</th>
                                <th>Número de Documento</th>
                                <th>Ficha Principal</th>
                                <th>Correo Electrónico</th>
                                <th>Estado</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($aprendices as $aprendiz)
                                <tr>
                                    <td>{{ $loop->iteration + ($aprendices->currentPage() - 1) * $aprendices->perPage() }}</td>
                                    <td>{{ $aprendiz->persona->nombre_completo }}</td>
                                    <td>{{ $aprendiz->persona->numero_documento }}</td>
                                    <td>
                                        @if($aprendiz->fichaCaracterizacion)
                                            <span class="badge badge-info">
                                                {{ $aprendiz->fichaCaracterizacion->ficha }}
                                            </span>
                                        @else
                                            <span class="text-muted">Sin asignar</span>
                                        @endif
                                    </td>
                                    <td>{{ $aprendiz->persona->email }}</td>
                                    <td class="project-state">
                                        <span class="badge badge-{{ $aprendiz->estado ? 'success' : 'danger' }}">
                                            {{ $aprendiz->estado ? 'ACTIVO' : 'INACTIVO' }}
                                        </span>
                                    </td>
                                    <td class="project-actions">
                                        @can('VER APRENDIZ')
                                            <a href="{{ route('aprendices.show', $aprendiz->id) }}"
                                                class="btn btn-warning btn-sm" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endcan
                                        @can('EDITAR APRENDIZ')
                                            <a href="{{ route('aprendices.edit', $aprendiz->id) }}"
                                                class="btn btn-info btn-sm" title="Editar">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        @endcan
                                        @can('ELIMINAR APRENDIZ')
                                            <form class="d-inline eliminar-aprendiz-form"
                                                action="{{ route('aprendices.destroy', $aprendiz->id) }}" method="POST"
                                                title="Eliminar">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No hay aprendices registrados</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Paginación -->
            <div class="card-footer">
                <div class="float-right">
                    {{ $aprendices->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Confirmación de eliminación
            const forms = document.querySelectorAll('.eliminar-aprendiz-form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: '¿Está seguro de eliminar este aprendiz?',
                        text: "¡Esta acción no se podrá revertir!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // Mostrar alertas de éxito/error
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: '{{ session('success') }}',
                    timer: 3000
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '{{ session('error') }}',
                    timer: 3000
                });
            @endif
        });
    </script>
@endsection

