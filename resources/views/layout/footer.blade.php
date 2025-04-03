@section('css')
@vite(['resources/css/style.css'])
@endsection 
<footer class="footer py-4">
        <div class="row align-items-center">
            <div class="col-lg-4 pl-4">
                <strong>&copy; {{ date('Y') }} <a href="#">SENA Regional Guaviare</a></strong>
                <span class="d-block text-muted small">Industria y Tecnología</span>
            </div>
            <div class="col-lg-4 text-lg-center">
                <img src="{{ asset('vendor/adminlte/dist/img/LogoSena.png') }}" alt="SENA Logo" class="footer-logo" height="50">
            </div>
            <div class="col-lg-4 text-right pr-5">
                <div class="social-links">
                    <a href="#" class="me-3"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="me-3"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="me-3"><i class="fab fa-linkedin"></i></a>
                </div>
                <div class="mt-2 text-muted small">
                    <span>Versión 3.2.0</span>
                </div>
            </div>
        </div>
</footer>

{{-- Scripts optimizados --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@include('layout.alertas')

