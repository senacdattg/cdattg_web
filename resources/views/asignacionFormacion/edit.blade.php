@extends('adminlte::page')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edición de Jornada</h1>
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
            <h3 class="card-title">Editar jornada</h3>
        </div>
        <form action="{{route('jornada.update', $jornada->id)}}" method="post">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label for="jornada">Jornada</label>
                    <input type="text" class="form-control" id="jornada" name="jornada" value="{{$jornada->jornada}}">
                </div>
                <div class="form-group">
                    <label for="hora_inicio">Hora Inicio</label>
                    <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" value="{{$jornada->hora_inicio}}">
                </div>
                <div class="form-group">
                    <label for="hora_fin">Hora Fin</label>
                    <input type="time" class="form-control" id="hora_fin" name="hora_fin" value="{{$jornada->hora_fin}}">
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Actualizar</button>
            </div>
        </form>
    </div>
    </section>
</div>

@endsection

