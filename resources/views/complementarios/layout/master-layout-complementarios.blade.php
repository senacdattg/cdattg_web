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

    @yield('scripts')
</body>

</html>