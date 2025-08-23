<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Desarrollo de Software - SENA</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .navbar {
            background-color: #094577;
        }
        .navbar-brand img {
            height: 40px;
        }
        .btn-primary {
            background-color: #094577;
            border-color: #094577;
        }
        .btn-primary:hover {
            background-color: #073a62;
            border-color: #073a62;
        }
        .program-icon {
            font-size: 3rem;
            color: #094577;
            margin-bottom: 1rem;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="programas_visitante.html">
                <img src="img/logo_2.png" alt="SENA Logo">
            </a>
            <div>
                <a href="programas_visitante.html" class="btn btn-outline-light me-2">
                    <i class="fas fa-search"></i> Explorar Programas
                </a>
                <div class="dropdown d-inline-block">
                    <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle"></i> Mi Cuenta
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="perfil.html"><i class="fas fa-user me-2"></i>Mi Perfil</a></li>
                        <li><a class="dropdown-item" href="inicio_sesion.html"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-5">
        <div class="row">
            <div class="col-12 mb-4">
                <a href="programas_visitante.html" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Volver a Programas
                </a>
            </div>
            
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="program-icon">
                                <i class="fas fa-laptop-code"></i>
                            </div>
                            <h2>Desarrollo de Software</h2>
                            <span class="badge bg-success mb-2">Inscripciones Abiertas</span>
                        </div>

                        <h5>Descripción del programa</h5>
                        <p>Este programa está diseñado para formar profesionales capacitados en el desarrollo de soluciones tecnológicas utilizando lenguajes de programación modernos. Los estudiantes aprenderán desde fundamentos de programación hasta técnicas avanzadas de desarrollo web y móvil, implementando arquitecturas robustas y seguras.</p>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h6>Información del Programa</h6>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Código:</span>
                                        <strong>DS-2025-01</strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Duración:</span>
                                        <strong>120 horas</strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Jornada:</span>
                                        <strong>Diurna (L-V 8:00 am - 12:00 pm)</strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Modalidad:</span>
                                        <strong>Presencial</strong>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h6>Requisitos</h6>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i>Conocimientos básicos en lógica matemática</li>
                                <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i>Comprensión de conceptos informáticos básicos</li>
                                <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i>Capacidad de trabajo en equipo</li>
                                <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i>Disponibilidad de tiempo para prácticas</li>
                            </ul>
                        </div>

                        <div class="mt-4">
                            <h6>Contenido del Programa</h6>
                            <div class="accordion" id="programContentAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                            Módulo 1: Fundamentos de programación
                                        </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#programContentAccordion">
                                        <div class="accordion-body">
                                            Introducción a la lógica de programación, algoritmos y estructuras de datos básicas.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                            Módulo 2: Desarrollo Frontend
                                        </button>
                                    </h2>
                                    <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#programContentAccordion">
                                        <div class="accordion-body">
                                            HTML5, CSS3, JavaScript y frameworks modernos como React.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                            Módulo 3: Desarrollo Backend
                                        </button>
                                    </h2>
                                    <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#programContentAccordion">
                                        <div class="accordion-body">
                                            Lenguajes y frameworks para desarrollo del lado del servidor. Bases de datos.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card sticky-top" style="top: 20px">
                    <div class="card-body text-center">
                        <h5>¿Interesado en este programa?</h5>
                        <p>Realiza tu inscripción ahora mismo</p>
                        <button class="btn btn-primary btn-lg w-100" onclick="location.href='preinscripcion.html'">
                            Inscripción Rápida
                        </button>
                        <hr>
                        <p class="small text-muted mb-0">¿Necesitas más información?</p>
                        <p class="small text-muted">Contáctanos al correo info@sena.edu.co</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>