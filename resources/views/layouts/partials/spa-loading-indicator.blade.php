{{-- Indicador de carga SPA --}}
@if (config('adminlte.livewire'))
    <div class="sena-loading-indicator"></div>
    <div class="sena-mini-logo">
        <img src="{{ asset(config('adminlte.preloader.img.path', 'vendor/adminlte/dist/img/LogoSena.png')) }}"
            alt="SENA" width="30" height="30">
    </div>
@endif
