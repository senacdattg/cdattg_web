@include('layout.header-registro')
<div class="main-content">
    {{-- Mostrar mensajes de éxito o error --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: '¡Registro Exitoso!',
                    text: '{{ session("success") }}',
                    icon: 'success',
                    confirmButtonText: 'Continuar',
                    confirmButtonColor: '#28a745',
                    customClass: {
                        popup: 'animated fadeInDown',
                        confirmButton: 'btn btn-success'
                    },
                    buttonsStyling: false,
                    background: '#fff',
                    backdrop: `rgba(0,0,0,0.4)`,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    padding: '1.25em',
                    width: '400px'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '/';
                    }
                });
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Error',
                    text: '{{ session("error") }}',
                    icon: 'error',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#dc3545',
                    customClass: {
                        popup: 'animated fadeInDown',
                        confirmButton: 'btn btn-danger'
                    },
                    buttonsStyling: false,
                    background: '#fff',
                    backdrop: `rgba(0,0,0,0.4)`,
                    padding: '1.25em',
                    width: '400px'
                });
            });
        </script>
    @endif

    {{-- Mostrar errores de validación en bloque --}}
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Errores de validación',
                    html: `
                        <ul class="text-left" style="list-style-position: inside;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    `,
                    icon: 'error',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#dc3545',
                    customClass: {
                        popup: 'animated fadeInDown',
                        confirmButton: 'btn btn-danger'
                    },
                    buttonsStyling: false,
                    background: '#fff',
                    backdrop: `rgba(0,0,0,0.4)`,
                    padding: '1.25em',
                    width: '500px'
                });
            });
        </script>
    @endif

    @yield('content')
</div>
@include('layout.footer-registro')
