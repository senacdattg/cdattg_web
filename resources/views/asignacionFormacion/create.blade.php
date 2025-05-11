@extends('adminlte::page')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Crear Jornada</h1>
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

    <section class="content">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Crear Nueva Jornada</h3>
            </div>
            <form action="{{ route('jornada.store') }}" method="POST">
                @csrf
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="card-body">
                    <div class="form-group">
                        <label for="jornada">Jornada</label>
                        <input type="text" class="form-control" id="jornada" name="jornada"
                            placeholder="Ingrese la jornada">
                    </div>
                    <div class="form-group">
                        <label for="hora_inicio">Hora Inicio</label>
                        <input type="time" class="form-control" id="hora_inicio" name="hora_inicio">
                    </div>
                    <div class="form-group">
                        <label for="hora_fin">Hora Fin</label>
                        <input type="time" class="form-control" id="hora_fin" name="hora_fin">
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </section>
    </div>

@endsection
