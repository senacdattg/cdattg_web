@extends('adminlte::page')
@section('content')

        <section class="content-header mt-3">
            <div class="container-fluid mt-3">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Consultar fichas</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="">Inicio</a>
                            </li>
                            <li class="breadcrumb-item active">Programas de formación
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <section class="content">
            <div class="card" style="margin: 0 auto;">
                <div class="card-header">
                    <form method="get" action={{ route('ficha.search') }}>

                        <div class="input-group">
                            <input type="text" name="search" class="form-control"
                                placeholder="Buscar por número deficha " value="{{ request()->get('search') }}">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body p-0">
                    <table class="table table-responsive" style="margin: 0 auto;">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 10%;">
                                    Id
                                </th>
                                <th class="text-center" style="width: 25%;">
                                    Numero de ficha
                                </th>
                                <th class="text-center" style="width: 40%;">
                                    Programa
                                </th>

                                <th class="text-center" style="width: 15%;">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($fichas as $ficha)
                                <tr>
                                    <td class="text-center">{{ $ficha->id }}</td>
                                    <td class="text-center">{{ $ficha->ficha }}</td>
                                    <td class="text-center">{{ $ficha->programaFormacion->nombre ?? 'N/A' }}</td>

                                    <td class="text-center">
                                        @can('VER PROGRAMA DE CARACTERIZACION')
                                            <div class="btn-group d-flex justify-content-center" role="group"
                                                aria-label="Acciones" style="gap: 10px;">
                                                <a href="{{ route('ficha.edit', $ficha->id) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('ficha.destroy', $ficha->id) }}" method="POST"
                                                    style="display:inline;" class="formulario-eliminar">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer clearfix">
                {{ $fichas->links() }}
            </div>

    </div>

    @include('components.confirm-delete-modal')
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Confirmar eliminación con diseño unificado
        $('.formulario-eliminar').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const nombre = form.closest('tr').find('td:nth-child(3)').text().trim();
            
            confirmDelete(nombre, form.attr('action'), form[0]);
        });
    });
</script>
@endsection
