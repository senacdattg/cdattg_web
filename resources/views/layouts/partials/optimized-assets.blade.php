{{-- Optimizaciones de carga de assets no críticos --}}
@if (!config('adminlte.enabled_laravel_mix', false) && config('adminlte.laravel_asset_bundling', false) === false)
    {{-- FontAwesome: carga asíncrona --}}
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" media="print"
        onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    </noscript>

    {{-- Google Fonts: preconnect + carga asíncrona --}}
    @if (config('adminlte.google_fonts.allowed', true))
        @php
            $googleFontsUrl =
                'https://fonts.googleapis.com/css?family=Source+Sans+Pro:' .
                '300,400,600,700,300italic,400italic,600italic';
        @endphp
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="stylesheet" href="{{ $googleFontsUrl }}" media="print" onload="this.media='all'">
        <noscript>
            <link rel="stylesheet" href="{{ $googleFontsUrl }}">
        </noscript>
    @endif
@endif
