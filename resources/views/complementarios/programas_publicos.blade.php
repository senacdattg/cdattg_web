<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programas Complementarios - SENA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="text-center mb-5">
            <h1><i class="fas fa-graduation-cap me-3"></i>Programas Complementarios</h1>
            <p>Descubre nuestros programas de formación complementaria disponibles</p>
        </div>

        <!-- Programs Cards View -->
        <div class="row row-cols-1 row-cols-md-3 g-4 mb-4">
            @foreach($programas as $programa)
            <div class="col mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="h1 text-primary mb-3">
                            <i class="{{ $programa->icono }}"></i>
                        </div>
                        <div class="d-flex justify-content-center">
                            <h5 class="card-title font-weight-bold">{{ $programa->nombre }}</h5>
                        </div>
                        <span class="badge bg-success mb-2 w-20 text-center">Con Oferta</span>
                        <p class="card-text">{{ $programa->descripcion }}</p>
                        <div class="d-flex justify-content-center mt-3 pt-3 border-top">
                            <div>
                                <small class="text-muted">Duración</small>
                                <p class="mb-0"><strong>{{ $programa->duracion }} horas</strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="{{ route('programa_complementario.ver', ['id' => $programa->id]) }}" class="btn btn-primary w-100">
                                <i class="fas fa-eye"></i> Ver Detalles
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>