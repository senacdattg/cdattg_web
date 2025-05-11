@extends('adminlte::page')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Lista de Asignaciones de formacion</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home.index') }}">Inicio</a>
                        </li>

                    </ol>
                </div>
            </div>
        </div>
    </section>

    @if(session('success'))
    <div class="alert alert-success" id="success-alert">
        {{ session('success') }}
    </div>
    @endif
    <script>
        setTimeout(function() {
            document.getElementById('success-alert').style.display = 'none';
        }, 3000);
    </script>


    @if(session('error'))
    <div class="alert alert-danger" id="error-alert">
        {{ session('error') }}
    </div>
    <script>
        setTimeout(function() {
            document.getElementById('error-alert').style.display = 'none';
        }, 3000);
    </script>
@endif
    <section class="content">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Jornadas</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center">Instructor</th>
                            <th class="text-center">Ficha</th>
                            <th class="text-center">Ambiente</th>
                            <th class="text-center">Jornada</th>
                            <th class="text-center">Fecha De Inicio</th>
                            <th class="text-center">Fecha De Fin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($asignaciones as $asignacion)
                        <tr>
                            <td>{{ $asignacion->instructor->persona->nombre_completo }}</td>
                            <td>{{ $asignacion->ficha->ficha }}</td>
                            <td>{{ $asignacion->ambiente->title }}</td>
                            <td>{{ $asignacion->jornada->jornada }}</td>
                            <td>{{ $asignacion->fecha_inicio }}</td>
                            <td>{{ $asignacion->fecha_fin }}</td>
                            <td class="text-center">
                                <a href="{{ route('asignacionDeFormacion.edit', $asignacion->id) }}" class="btn btn-success btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('asignacionDeFormacion.destroy', $asignacion->id) }}" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

@endsection

