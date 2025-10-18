{{-- Accesos Rápidos para Superadministrador --}}
<div class="container-fluid px-4 mt-4">
    <div class="row">
        <div class="col-12">
            <h4 class="mb-3">
                <i class="fas fa-bolt text-warning"></i> Accesos Rápidos
            </h4>
        </div>
    </div>
    
    <div class="row g-4">
        {{-- Gestión de Redes de Conocimiento --}}
        <div class="col-md-3 col-sm-6">
            <a href="{{ route('red-conocimiento.index') }}" class="text-decoration-none">
                <div class="card dashboard-card quick-access-card shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="quick-access-icon bg-primary">
                            <i class="fas fa-network-wired fa-2x text-white"></i>
                        </div>
                        <h5 class="card-title mt-3 mb-2">Redes de Conocimiento</h5>
                        <p class="card-text text-muted small">Gestión de redes y programas</p>
                    </div>
                </div>
            </a>
        </div>

        {{-- Gestión de Programas de Formación --}}
        <div class="col-md-3 col-sm-6">
            <a href="{{ route('programa-formacion.index') }}" class="text-decoration-none">
                <div class="card dashboard-card quick-access-card shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="quick-access-icon bg-success">
                            <i class="fas fa-graduation-cap fa-2x text-white"></i>
                        </div>
                        <h5 class="card-title mt-3 mb-2">Programas de Formación</h5>
                        <p class="card-text text-muted small">Administrar programas</p>
                    </div>
                </div>
            </a>
        </div>

        {{-- Gestión de Fichas de Caracterización --}}
        <div class="col-md-3 col-sm-6">
            <a href="{{ route('fichaCaracterizacion.index') }}" class="text-decoration-none">
                <div class="card dashboard-card quick-access-card shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="quick-access-icon bg-info">
                            <i class="fas fa-clipboard-list fa-2x text-white"></i>
                        </div>
                        <h5 class="card-title mt-3 mb-2">Fichas</h5>
                        <p class="card-text text-muted small">Fichas de caracterización</p>
                    </div>
                </div>
            </a>
        </div>

        {{-- Gestión de Instructores --}}
        <div class="col-md-3 col-sm-6">
            <a href="{{ route('instructores.index') }}" class="text-decoration-none">
                <div class="card dashboard-card quick-access-card shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="quick-access-icon bg-warning">
                            <i class="fas fa-chalkboard-teacher fa-2x text-white"></i>
                        </div>
                        <h5 class="card-title mt-3 mb-2">Instructores</h5>
                        <p class="card-text text-muted small">Gestión de instructores</p>
                    </div>
                </div>
            </a>
        </div>

        {{-- Gestión de Aprendices --}}
        <div class="col-md-3 col-sm-6">
            <a href="{{ route('aprendiz.index') }}" class="text-decoration-none">
                <div class="card dashboard-card quick-access-card shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="quick-access-icon bg-danger">
                            <i class="fas fa-user-graduate fa-2x text-white"></i>
                        </div>
                        <h5 class="card-title mt-3 mb-2">Aprendices</h5>
                        <p class="card-text text-muted small">Gestión de aprendices</p>
                    </div>
                </div>
            </a>
        </div>

        {{-- Gestión de Ambientes --}}
        <div class="col-md-3 col-sm-6">
            <a href="{{ route('ambiente.index') }}" class="text-decoration-none">
                <div class="card dashboard-card quick-access-card shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="quick-access-icon bg-secondary">
                            <i class="fas fa-door-open fa-2x text-white"></i>
                        </div>
                        <h5 class="card-title mt-3 mb-2">Ambientes</h5>
                        <p class="card-text text-muted small">Administrar ambientes</p>
                    </div>
                </div>
            </a>
        </div>

        {{-- Gestión de Asistencias --}}
        <div class="col-md-3 col-sm-6">
            <a href="{{ route('asistencia-aprendiz.index') }}" class="text-decoration-none">
                <div class="card dashboard-card quick-access-card shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="quick-access-icon bg-dark">
                            <i class="fas fa-calendar-check fa-2x text-white"></i>
                        </div>
                        <h5 class="card-title mt-3 mb-2">Asistencias</h5>
                        <p class="card-text text-muted small">Control de asistencias</p>
                    </div>
                </div>
            </a>
        </div>

        {{-- Configuración del Sistema --}}
        <div class="col-md-3 col-sm-6">
            <a href="{{ route('configuracion.index') }}" class="text-decoration-none">
                <div class="card dashboard-card quick-access-card shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="quick-access-icon bg-purple">
                            <i class="fas fa-cogs fa-2x text-white"></i>
                        </div>
                        <h5 class="card-title mt-3 mb-2">Configuración</h5>
                        <p class="card-text text-muted small">Parámetros del sistema</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<style>
.quick-access-card {
    transition: all 0.3s ease;
    border: none;
}

.quick-access-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
}

.quick-access-icon {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.bg-purple {
    background-color: #6f42c1 !important;
}
</style>

