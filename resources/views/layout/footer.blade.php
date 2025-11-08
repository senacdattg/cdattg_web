<footer class="main-footer bg-body-secondary border-top">
    <div class="container-fluid py-2">
        <div class="d-flex flex-column flex-lg-row align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ asset('vendor/adminlte/dist/img/LogoSena.png') }}" alt="Logo SENA" height="40"
                    class="d-none d-sm-block">
                <div class="text-center text-lg-start">
                    <span class="d-block fw-semibold small">
                        &copy; {{ now()->format('Y') }}
                        <a href="https://www.sena.edu.co" target="_blank" rel="noopener" class="text-decoration-none">
                            SENA Regional Guaviare
                        </a>
                    </span>
                    <span class="text-muted text-xs">Industria y Tecnología</span>
                </div>
            </div>

            <div class="text-center">
                <div class="text-muted text-xs mb-1">Conéctate con nosotros</div>
                <div class="d-inline-flex align-items-center gap-1 flex-wrap justify-content-center">
                    <a href="https://www.facebook.com/SENA" target="_blank" rel="noopener"
                        class="btn btn-default btn-flat btn-xs d-flex align-items-center gap-1 px-2 py-1">
                        <i class="fab fa-facebook-f text-primary"></i>
                        <span class="d-none d-sm-inline small">Facebook</span>
                    </a>
                    <a href="https://twitter.com/SENAComunica" target="_blank" rel="noopener"
                        class="btn btn-default btn-flat btn-xs d-flex align-items-center gap-1 px-2 py-1">
                        <i class="fab fa-twitter text-info"></i>
                        <span class="d-none d-sm-inline small">Twitter</span>
                    </a>
                    <a href="https://www.linkedin.com/school/senaoficial" target="_blank" rel="noopener"
                        class="btn btn-default btn-flat btn-xs d-flex align-items-center gap-1 px-2 py-1">
                        <i class="fab fa-linkedin-in text-secondary"></i>
                        <span class="d-none d-sm-inline small">LinkedIn</span>
                    </a>
                </div>
            </div>

            <div class="text-center text-lg-end">
                <div class="text-muted text-xs">
                    <i class="fas fa-code-branch me-1"></i>
                    Versión 3.2.0
                </div>
                <div class="text-muted text-xs">
                    <i class="fas fa-life-ring me-1"></i>
                    soporte@cdattg.com
                </div>
            </div>
            <div class="mt-3">
                <a href="https://sonarqube.dataguaviare.com.co/dashboard?id=academica_web" target="_blank"
                    rel="noopener noreferrer">
                    <img src="https://sonarqube.dataguaviare.com.co/api/project_badges/measure?project=academica_web&metric=alert_status&token=sqb_113dbf278cf78c52371734be0eb6518e945d4376"
                        alt="Calidad SonarQube academica_web" class="img-fluid" style="max-height: 32px;">
                </a>
            </div>
        </div>
    </div>
</footer>
