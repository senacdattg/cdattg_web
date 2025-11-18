<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('dist/js/adminlte.min.js') }}?v=3.2.0"></script>
{{-- SweetAlert2 para notificaciones --}}
@php
    $sweetalert2Active = config('adminlte.plugins.Sweetalert2.active', false);
    $sweetalert2Location = $sweetalert2Active && isset(config('adminlte.plugins.Sweetalert2.files')[0]['location'])
        ? config('adminlte.plugins.Sweetalert2.files')[0]['location']
        : null;
@endphp
@if ($sweetalert2Active && $sweetalert2Location && file_exists(public_path($sweetalert2Location)))
    <script src="{{ asset($sweetalert2Location) }}"></script>
@else
    {{-- Fallback: cargar SweetAlert2 desde CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endif
@stack('js')

</body>

</html>
