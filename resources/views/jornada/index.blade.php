@extends('adminlte::page')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Lista de Jornadas</h1>
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
    @if(session('error'))
    <div class="alert alert-danger" id="error-alert">
        {{ session('error') }}
    </div>
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
                            <th class="text-center">Jornada</th>
                            <th class="text-center">Hora Inicio</th>
                            <th class="text-center">Hora Fin</th>
                            <th class="text-center">Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jornadas as $jornada)
                        <tr>
                            <td>{{ $jornada->jornada }}</td>
                            <td>{{ $jornada->hora_inicio }}</td>
                            <td>{{ $jornada->hora_fin }}</td>
                            <td class="text-center">
                                <a href="{{ route('jornada.edit', $jornada->id) }}" class="btn btn-success btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('jornada.destroy', $jornada->id) }}" class="btn btn-danger btn-sm">
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

