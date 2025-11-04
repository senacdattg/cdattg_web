<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $programaData['nombre'] }} - SENA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><i class="fas fa-graduation-cap me-2"></i>{{ $programaData['nombre'] }}</h1>
                <p class="text-muted mb-0">Detalle del programa complementario</p>
            </div>
            <div>
                <a href="{{ route('programas-complementarios.publicos') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-1"></i> Volver a Programas
                </a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="h1 text-primary mb-2">
                                <i class="{{ $programaData['icono'] }}"></i>
                            </div>
                            <h2 class="mb-1">{{ $programaData['nombre'] }}</h2>
                            <span class="badge bg-success mb-2">Con Oferta</span>
                        </div>

                        <h5>Descripción</h5>
                        <p class="text-muted">{{ $programaData['descripcion'] }}</p>

                        <div class="row mt-4">
                            <div class="col-md-6 mb-2">
                                <h6 class="mb-1">Duración</h6>
                                <p class="mb-0"><strong>{{ $programaData['duracion'] }}</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card sticky-top shadow-sm" style="top:20px;">
                    <div class="card-body text-center">
                        <h5>Inscripción</h5>
                        <p class="small text-muted">Si estás interesado, realiza tu inscripción.</p>
                        <a href="mailto:info@sena.edu.co" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-envelope me-1"></i> Consultar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>