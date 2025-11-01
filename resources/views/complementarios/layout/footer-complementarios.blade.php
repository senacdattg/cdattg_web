<!-- Footer -->
<footer class="main-footer" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border-top: none;">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <strong>&copy; 2024 Servicio Nacional de Aprendizaje - SENA</strong>
                <br>
                <small>Todos los derechos reservados</small>
            </div>
            <div class="col-md-6 text-md-right">
                <div class="d-flex justify-content-end align-items-center">
                    <img src="{{ asset('img/sena-logo.png') }}" alt="SENA Logo" style="height: 30px; margin-right: 10px;">
                    <div>
                        <small>Centro de Desarrollo Tecnológico</small>
                        <br>
                        <small>Formación Complementaria</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
.main-footer {
    position: relative;
    bottom: 0;
    width: 100%;
    margin-top: auto;
}

@media (max-width: 768px) {
    .main-footer .row {
        text-align: center !important;
    }

    .main-footer .col-md-6.text-md-right {
        text-align: center !important;
        margin-top: 1rem;
    }

    .main-footer .d-flex {
        justify-content: center !important;
    }
}
</style>