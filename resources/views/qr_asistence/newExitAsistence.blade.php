<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Aprendices</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
       
        <header>
            
            <a href="{{ url()->previous() }}" class="btn btn-success btn-sm mr-2 mb-3">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <div class="row">
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

                <div class="col-md-12 d-flex align-items-center justify-content-center">
                    <h5>Agregar novedad de salida</h5>
                </div>
            </div>
        </header>
        <section style="width: 100%">
            <div class="row justify-content-center mt-3">
                <div class="col-md-8">
                    <form action="{{route('asistence.setNewExit')}}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="nombres">Nombres</label>
                            <input type="text" class="form-control" name="nombres" id="nombres" value="{{ $asistencia->nombres }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="apellidos">Apellidos</label>
                            <input type="text" class="form-control" name="apellidos" id="apellidos" value="{{ $asistencia->apellidos }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="identificacion">Identificación</label>
                            <input type="text" class="form-control" name="identificacion" id="identificacion" value="{{ $asistencia->numero_identificacion }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="novedad">Novedad</label>
                            <textarea class="form-control" name="novedad" id="novedad" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
           
        </section>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>


