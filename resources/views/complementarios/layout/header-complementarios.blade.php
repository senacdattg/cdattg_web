<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light" style="background-color: #ffffff; margin-top: 10px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border: 1px solid #dee2e6;">
    <div class="container-fluid">
        <a class="navbar-brand text-dark d-flex align-items-center" href="{{ route('programas-complementarios.publicos') }}">
            <img src="{{ asset('vendor/adminlte/dist/img/LogoSena.png') }}" alt="SENA Logo" height="40" class="me-2 rounded-circle bg-white p-1">
            <span class="d-none d-lg-inline"><strong>SENA Regional Guaviare</strong></span>
            <span class="d-lg-none"><strong>SENA</strong></span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link text-dark" href="/home">
                        <i class="fas fa-home me-1"></i> Inicio
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="{{ route('programas-complementarios.publicos') }}">
                        <i class="fas fa-graduation-cap me-1"></i> Programas
                    </a>
                </li>
            </ul>
            <div class="mx-auto"></div>
            <ul class="navbar-nav">
                @auth
                <li class="nav-item dropdown user-menu">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                        <div class="user-avatar-circle">
                            {{ substr(Auth::user()->persona->primer_nombre, 0, 1) }}{{ substr(Auth::user()->persona->primer_apellido, 0, 1) }}
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <li class="user-header text-center" style="background-color: #ffffff; border-left: 4px solid #007bff;">
                            <div class="user-avatar-circle-large mb-2">
                                {{ substr(Auth::user()->persona->primer_nombre, 0, 1) }}{{ substr(Auth::user()->persona->primer_apellido, 0, 1) }}
                            </div>
                            <p class="mb-1 text-dark">
                                {{ Auth::user()->persona->primer_nombre }} {{ Auth::user()->persona->primer_apellido }}
                            </p>
                            <p class="mb-0">
                                <small class="text-dark">{{ ucfirst(strtolower(Auth::user()->getRoleNames()->first())) }}</small>
                            </p>
                        </li>
                        <li class="user-footer">
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-flat btn-block">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Cerrar Sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
                @else
                <li class="nav-item me-4">
                    <a class="nav-link text-dark" href="{{ url('/login') }}">
                        <i class="fas fa-sign-in-alt me-1"></i> Iniciar Sesión
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="{{ route('registro') }}">
                        <i class="fas fa-user-plus me-1"></i> Registrarse
                    </a>
                </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<link rel="stylesheet" href="{{ asset('css/header-style.css') }}">
