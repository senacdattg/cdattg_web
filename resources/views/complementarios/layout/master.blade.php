<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Programas Complementarios | SENA')</title>

    <!-- Base fonts & icons -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">

    <!-- AdminLTE core -->
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}?v=3.2.0">

    <!-- Vendor overrides -->
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2-bootstrap4.min.css') }}">

    <!-- Layout styles -->
    <style>
        body {
            background-color: #f1f5f9;
        }

        .content-wrapper {
            background: transparent;
            margin-left: 0;
        }

        .content-wrapper>.content {
            padding-inline: 0;
        }

        .preloader-active {
            overflow: hidden;
        }
    </style>

    <!-- Page level styles -->
    @hasSection('css')
        @yield('css')
    @endif
    @stack('css')

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('img/sena-logo.png') }}">
</head>

<body class="layout-top-nav">
    <div class="wrapper">
        <!-- Header -->
        @include('complementarios.layout.header')

        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center d-none" id="layout-preloader">
            @hasSection('preloader')
                @yield('preloader')
            @else
                <img src="{{ asset('img/sena-logo.png') }}" alt="SENA Logo" class="img-circle animation__shake"
                    width="80" height="80">
                <p class="mt-3 text-muted">Cargando...</p>
            @endif
        </div>

        <!-- Content -->
        <div class="content-wrapper">
            <div class="content">
                @yield('content')
            </div>
        </div>

        @includeWhen(view()->exists('complementarios.layout.footer'), 'complementarios.layout.footer')
    </div>

    <!-- Core scripts -->
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('dist/js/adminlte.min.js') }}?v=3.2.0"></script>
    <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
    {{-- SweetAlert2 cargado por AdminLTE nativo (si se migra a adminlte::page) --}}

    <script>
        (function() {
            const body = document.body;
            const preloader = document.getElementById('layout-preloader');

            const showPreloader = () => {
                if (!preloader) {
                    return;
                }
                preloader.classList.remove('d-none');
                body.classList.add('preloader-active');
            };

            const hidePreloader = () => {
                if (!preloader) {
                    return;
                }
                preloader.classList.add('d-none');
                body.classList.remove('preloader-active');
            };

            document.addEventListener('DOMContentLoaded', () => {
                hidePreloader();

                document.querySelectorAll('form').forEach((form) => {
                    form.addEventListener('submit', (event) => {
                        if (!form.hasAttribute('data-no-preloader')) {
                            showPreloader();
                        }
                    });
                });

                document.querySelectorAll('a[data-show-preloader]').forEach((link) => {
                    link.addEventListener('click', showPreloader);
                });
            });

            window.addEventListener('pageshow', hidePreloader);
        })();
    </script>

    <!-- Page level scripts -->
    @hasSection('scripts')
        @yield('scripts')
    @endif
    @stack('scripts')
    @stack('js')
</body>

</html>
