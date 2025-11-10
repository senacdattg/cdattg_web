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
        <div class="alert alert-danger alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Mostrar errores de validación en bloque --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @yield('content')
</div>
@include('layout.footer-registro')
