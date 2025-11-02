<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Programas Complementarios | SENA')</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">

    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}?v=3.2.0">

    <!-- Custom CSS -->
    @yield('css')

    <!-- Preloader Section -->
    @section('preloader')
        <img src="{{ asset('img/sena-logo.png') }}" alt="SENA Logo"
             class="img-circle animation__shake"
             width="80" height="80"
             style="animation-iteration-count:infinite;">
        <p class="mt-3">Cargando...</p>
    @endsection

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('img/sena-logo.png') }}">
</head>

<body class="layout-fullwidth sidebar-collapse">
    <!-- Header -->
    @include('complementarios.layout.header-complementarios')

    <!-- Main Content -->
    <div class="content-wrapper" style="min-height: calc(100vh - 60px); margin-left: 0;">
        <div class="content" style="padding: 0;">
            @yield('content')
        </div>
    </div>


    <!-- Scripts -->
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('dist/js/adminlte.min.js') }}?v=3.2.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Preloader Script -->
    <script>
        // Mostrar preloader en envíos de formularios
        document.addEventListener('DOMContentLoaded', function() {
            // Interceptar envíos de formularios
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    // Solo mostrar preloader si no es un formulario con data-no-preloader
                    if (!form.hasAttribute('data-no-preloader')) {
                        // Mostrar preloader usando AdminLTE
                        $('body').addClass('preloader-active');
                    }
                });
            });

            // Interceptar clics en enlaces que podrían tardar
            const links = document.querySelectorAll('a[data-show-preloader]');
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    $('body').addClass('preloader-active');
                });
            });
        });
    </script>

    @yield('scripts')
</body>

</html>