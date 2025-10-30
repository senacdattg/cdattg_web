<nav class="navbar navbar-expand-lg navbar-light bg-success">
    <div class="container-fluid">
        <a class="navbar-brand text-white" href="{{ route('programas-complementarios.publicos') }}">
            <img src="{{ asset('vendor/adminlte/dist/img/LogoSena.png') }}" alt="SENA Logo" height="40" class="me-2">
            <strong>SENA Regional Guaviare</strong>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('programas-complementarios.publicos') }}">
                        <i class="fas fa-graduation-cap me-1"></i> Programas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ url('/login') }}">
                        <i class="fas fa-sign-in-alt me-1"></i> Iniciar Sesión
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('registro') }}">
                        <i class="fas fa-user-plus me-1"></i> Registrarse
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>