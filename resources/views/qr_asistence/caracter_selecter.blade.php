@extends('adminlte::page')
@section('content')
    <section class="content-header mt-3">
        <div class="container-fluid mt-3">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Programas de formación
                    </h1>
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
    @if (session('error'))
        <div class="alert alert-danger" id="error-message">
            {{ session('error') }}
        </div>
        <script>
            setTimeout(function() {
                document.getElementById('error-message').style.display = 'none';
            }, 3000);
        </script>
    @endif

    <section class="content">
        <div class="container-fluid">

            <div class="row">
                @foreach($caracterizaciones as $caracterizacion)
                    <div class="col-md-4">
                        <div class="card" style="height: 90%">
                            <div class="card-header">
                                <h3 class="card-title"><b>N° ficha:</b> {{ $caracterizacion->ficha->ficha }}</h3>
                            </div>
                            <div class="card-body">
                                <h6><b>N° Caracterización: </b>{{$caracterizacion->id}}</h6>
                                <h6><b>Programa:</b> {{ $caracterizacion->ficha->programaFormacion->nombre }}</h6>
                                <h6><b>Instructor:</b> {{ $persona->primer_nombre }} {{ $persona->primer_apellido}}</h6>
                                <h6><b>Jornada:</b> {{ $caracterizacion->ficha->jornadaFormacion->jornada }}</h6>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-md-6">
                                        <a href="{{ route('asistence.caracterSelected', ['id' => $caracterizacion->id]) }}" class="btn btn-primary">Asistencia</a>
                                    </div>  
                                    <div class="col-md-6">
                                        <a href="{{ route('asistence.weblist', ['ficha' => $caracterizacion->ficha->ficha, 'jornada' => $caracterizacion->ficha->jornadaFormacion->jornada]) }}" class="btn btn-success">Novedades</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</div>
@endsection