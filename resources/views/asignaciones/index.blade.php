@extends('adminlte::page')

@section('title', 'Asignaciones de instructores')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h4 mb-0">
            <i class="fas fa-user-check mr-2 text-primary"></i>
            Asignaciones de instructores
        </h1>
        <a href="{{ route('asignaciones.instructores.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus mr-1"></i>
            Nueva asignación
        </a>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Ficha</th>
                                <th>Programa</th>
                                <th>Instructor</th>
                                <th>Competencia</th>
                                <th>Resultados asignados</th>
                                <th class="text-right">Creada</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($asignaciones as $asignacion)
                                <tr>
                                    <td>
                                        <strong>{{ $asignacion->ficha->ficha ?? 'N/A' }}</strong>
                                    </td>
                                    <td>{{ $asignacion->ficha->programaFormacion->nombre ?? 'N/A' }}</td>
                                    <td>{{ optional($asignacion->instructor)->nombre_completo ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ $asignacion->competencia->codigo }}
                                        </span>
                                        {{ $asignacion->competencia->nombre }}
                                    </td>
                                    <td>
                                        @if ($asignacion->resultadosAprendizaje->isEmpty())
                                            <span class="text-muted">Sin resultados</span>
                                        @else
                                            <ul class="list-unstyled mb-0">
                                                @foreach ($asignacion->resultadosAprendizaje as $resultado)
                                                    <li>
                                                        <span class="badge badge-secondary">{{ $resultado->codigo }}</span>
                                                        {{ $resultado->nombre }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        {{ optional($asignacion->created_at)->format('d/m/Y H:i') ?? 'N/A' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        No hay asignaciones registradas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($asignaciones instanceof \Illuminate\Pagination\AbstractPaginator)
                <div class="card-footer d-flex justify-content-end">
                    {{ $asignaciones->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

