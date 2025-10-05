@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <section class="content-header dashboard-header py-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 d-flex align-items-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width: 48px; height: 48px;">
                        <i class="fas fa-file-alt text-white fa-lg"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Fichas de Caracterización</h1>
                        <p class="text-muted mb-0 font-weight-light">Gestión de fichas de caracterización del SENA</p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent mb-0 justify-content-end">
                            <li class="breadcrumb-item">
                                <a href="{{ url('/') }}" class="link_right_header">
                                    <i class="fas fa-home"></i> Inicio
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <i class="fas fa-file-alt"></i> Fichas de Caracterización
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content')
    <section class="content mt-4">
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

            <div class="row">
                <div class="col-12">
                    @can('CREAR PROGRAMA DE CARACTERIZACION')
                        <div class="card shadow-sm mb-4 no-hover">
                            <div class="card-header bg-white py-3 d-flex align-items-center">
                                <a href="{{ route('fichaCaracterizacion.create') }}" class="card-title m-0 font-weight-bold text-primary d-flex align-items-center flex-grow-1 text-decoration-none">
                                    <i class="fas fa-plus-circle mr-2"></i> Crear Ficha de Caracterización
                                </a>
                            </div>
                        </div>
                    @endcan

                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3 d-flex align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary d-flex flex-grow-1">Lista de Fichas de Caracterización</h6>
                            <div class="input-group w-25">
                                <form action="{{ route('fichaCaracterizacion.index') }}" method="GET" class="input-group">
                                    <input type="text" name="search" id="searchFicha"
                                        class="form-control form-control-sm" placeholder="Buscar ficha..."
                                        value="{{ request('search') }}" autocomplete="off">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary btn-sm" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-borderless table-striped mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="px-4 py-3" style="width: 5%">#</th>
                                            <th class="px-4 py-3" style="width: 15%">Ficha</th>
                                            <th class="px-4 py-3" style="width: 25%">Programa</th>
                                            <th class="px-4 py-3" style="width: 20%">Instructor Líder</th>
                                            <th class="px-4 py-3" style="width: 15%">Sede</th>
                                            <th class="px-4 py-3" style="width: 10%">Estado</th>
                                            <th class="px-4 py-3" style="width: 10%">Aprendices</th>
                                            <th class="px-4 py-3 text-center" style="width: 10%">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="fichasTableBody">
                                        @forelse ($fichas as $ficha)
                                            <tr>
                                                <td class="px-4">{{ $loop->iteration }}</td>
                                                <td class="px-4">
                                                    <span class="badge badge-info">{{ $ficha->ficha }}</span>
                                                </td>
                                                <td class="px-4 font-weight-medium">
                                                    <strong>{{ $ficha->programaFormacion->nombre ?? 'N/A' }}</strong>
                                                    @if($ficha->programaFormacion->codigo ?? false)
                                                        <br><small class="text-muted">{{ $ficha->programaFormacion->codigo }}</small>
                                                    @endif
                                                </td>
                                                <td class="px-4">
                                                    @if($ficha->instructor && $ficha->instructor->persona)
                                                        {{ $ficha->instructor->persona->primer_nombre }} {{ $ficha->instructor->persona->primer_apellido }}
                                                    @else
                                                        <span class="text-muted">Sin asignar</span>
                                                    @endif
                                                </td>
                                                <td class="px-4">{{ $ficha->sede->sede ?? 'N/A' }}</td>
                                                <td class="px-4">
                                                    <div class="d-inline-block px-3 py-1 rounded-pill {{ $ficha->status ? 'bg-success-light text-success' : 'bg-danger-light text-danger' }}">
                                                        <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                                        {{ $ficha->status ? 'Activa' : 'Inactiva' }}
                                                    </div>
                                                </td>
                                                <td class="px-4 text-center">
                                                    @if($ficha->aprendices && $ficha->aprendices->count() > 0)
                                                        <span class="badge badge-primary">{{ $ficha->aprendices->count() }}</span>
                                                    @else
                                                        <span class="badge badge-secondary">0</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 text-center">
                                                    <div class="btn-group">
                                                        @can('VER PROGRAMA DE CARACTERIZACION')
                                                            <a href="{{ route('fichaCaracterizacion.show', $ficha->id) }}"
                                                                class="btn btn-light btn-sm" data-toggle="tooltip"
                                                                title="Ver detalles">
                                                                <i class="fas fa-eye text-warning"></i>
                                                            </a>
                                                        @endcan
                                                        @can('EDITAR PROGRAMA DE CARACTERIZACION')
                                                            <a href="{{ route('fichaCaracterizacion.edit', $ficha->id) }}"
                                                                class="btn btn-light btn-sm" data-toggle="tooltip"
                                                                title="Editar">
                                                                <i class="fas fa-pencil-alt text-info"></i>
                                                            </a>
                                                        @endcan
                                                        @can('ELIMINAR PROGRAMA DE CARACTERIZACION')
                                                            <button type="button" class="btn btn-light btn-sm" 
                                                                    data-ficha="{{ $ficha->ficha }}" 
                                                                    data-url="{{ route('fichaCaracterizacion.destroy', $ficha->id) }}"
                                                                    onclick="confirmarEliminacion(this.dataset.ficha, this.dataset.url)"
                                                                    data-toggle="tooltip" title="Eliminar">
                                                                <i class="fas fa-trash text-danger"></i>
                                                            </button>
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-5">
                                                    <img src="{{ asset('img/no-data.svg') }}" alt="No data"
                                                        style="width: 120px" class="mb-3">
                                                    <p class="text-muted">No hay fichas de caracterización registradas</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer bg-white">
                            <div class="float-right">
                                {{ $fichas->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footer')
    @include('layout.footer')
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
                // Crear formulario para enviar DELETE
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

    // Auto-hide alerts after 5 seconds
    $(document).ready(function() {
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
</script>
@endsection