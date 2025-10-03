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
            <!-- Program 1 -->
            <div class="col mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="h1 text-primary mb-3">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <div class="d-flex justify-content-center">
                            <h5 class="card-title font-weight-bold">Auxiliar de Cocina</h5>
                        </div>
                        <span class="badge bg-success mb-2 w-20 text-center">Con Oferta</span>
                        <p class="card-text">Fundamentos de cocina, manipulación de alimentos y técnicas básicas de preparación.
                        </p>
                        <div class="d-flex justify-content-center mt-3 pt-3 border-top">
                            <div>
                                <small class="text-muted">Duración</small>
                                <p class="mb-0"><strong>40 horas</strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="{{ route('programa_complementario.ver', ['programa' => 'auxiliar-cocina']) }}" class="btn btn-primary w-100">
                                <i class="fas fa-eye"></i> Ver Detalles
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Program 2 -->
            <div class="col mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="h1 text-primary mb-3">
                            <i class="fas fa-hammer"></i>
                        </div>
                        <div class="d-flex justify-content-center">
                            <h5 class="card-title font-weight-bold">Acabados en Madera</h5>
                        </div>
                        <span class="badge bg-success mb-2 w-20 text-center">Con Oferta</span>
                        <p class="card-text">Técnicas de acabado, barnizado y restauración de muebles de madera.</p>
                        <div class="d-flex justify-content-center mt-3 pt-3 border-top">
                            <div>
                                <small class="text-muted">Duración</small>
                                <p class="mb-0"><strong>60 horas</strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="{{ route('programa_complementario.ver', ['programa' => 'Acabados-en-Madera']) }}" class="btn btn-primary w-100">
                                <i class="fas fa-eye"></i> Ver Detalles
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Program 3 -->
            <div class="col mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="h1 text-primary mb-3">
                            <i class="fas fa-cut"></i>
                        </div>
                        <div class="d-flex justify-content-center">
                            <h5 class="card-title font-weight-bold">Confección de Prendas</h5>
                        </div>
                        <span class="badge bg-success mb-2 w-20 text-center">Con Oferta</span>
                        <p class="card-text">Técnicas básicas de corte, confección y terminado de prendas de vestir.</p>
                        <div class="d-flex justify-content-center mt-3 pt-3 border-top">
                            <div>
                                <small class="text-muted">Duración</small>
                                <p class="mb-0"><strong>50 horas</strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="{{ route('programa_complementario.ver', ['programa' => 'Confección-de-Prendas']) }}" class="btn btn-primary w-100">
                                <i class="fas fa-eye"></i> Ver Detalles
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Program 4 -->
            <div class="col mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="h1 text-primary mb-3">
                            <i class="fas fa-car"></i>
                        </div>
                        <div class="d-flex justify-content-center">
                            <h5 class="card-title font-weight-bold">Mecánica Básica Automotriz</h5>
                        </div>
                        <span class="badge bg-success mb-2 w-20 text-center">Con Oferta</span>
                        <p class="card-text">Mantenimiento preventivo y diagnóstico básico de vehículos.</p>
                        <div class="d-flex justify-content-center mt-3 pt-3 border-top">
                            <div>
                                <small class="text-muted">Duración</small>
                                <p class="mb-0"><strong>90 horas</strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="{{ route('programa_complementario.ver', ['programa' => 'Mecánica-Básica-Automotriz']) }}" class="btn btn-primary w-100">
                                <i class="fas fa-eye"></i> Ver Detalles
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Program 5 -->
            <div class="col mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="h1 text-primary mb-3">
                            <i class="fas fa-spa"></i>
                        </div>
                        <div class="d-flex justify-content-center">
                            <h5 class="card-title font-weight-bold">Cultivos de Huertas Urbanas</h5>
                        </div>
                        <span class="badge bg-success mb-2 w-20 text-center">Con Oferta</span>
                        <p class="card-text">Técnicas de cultivo y mantenimiento de huertas en espacios urbanos.</p>
                        <div class="d-flex justify-content-center mt-3 pt-3 border-top">
                            <div>
                                <small class="text-muted">Duración</small>
                                <p class="mb-0"><strong>120 horas</strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="{{ route('programa_complementario.ver', ['programa' => 'Cultivos-de-Huertas-Urbanas']) }}" class="btn btn-primary w-100">
                                <i class="fas fa-eye"></i> Ver Detalles
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Program 6 -->
            <div class="col mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="h1 text-primary mb-3">
                            <i class="fas fa-gavel"></i>
                        </div>
                        <div class="d-flex justify-content-center">
                            <h5 class="card-title font-weight-bold">Normatividad Laboral</h5>
                        </div>
                        <span class="badge bg-success mb-2 w-20 text-center">Con Oferta</span>
                        <p class="card-text">Actualización en normatividad laboral y seguridad social.</p>
                        <div class="d-flex justify-content-center mt-3 pt-3 border-top">
                            <div>
                                <small class="text-muted">Duración</small>
                                <p class="mb-0"><strong>60 horas</strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="{{ route('programa_complementario.ver', ['programa' => 'Normatividad-Laboral']) }}" class="btn btn-primary w-100">
                                <i class="fas fa-eye"></i> Ver Detalles
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>