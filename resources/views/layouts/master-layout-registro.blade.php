@include('layouts.header-registro')
<div class="main-content">
    {{-- Mostrar mensajes de éxito o error --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Esperar a que SweetAlert2 esté disponible
                function showSuccessAlert() {
                    if (typeof Swal === 'undefined' || typeof Swal.fire !== 'function') {
                        setTimeout(showSuccessAlert, 100);
                        return;
                    }
                    Swal.fire({
                        title: '¡Registro Exitoso!',
                        text: {!! json_encode(session('success')) !!},
                        icon: 'success',
                        confirmButtonText: 'Continuar',
                        confirmButtonColor: '#28a745',
                        customClass: {
                            popup: 'animated fadeInDown',
                            confirmButton: 'btn btn-success'
                        },
                        buttonsStyling: false,
                        background: '#fff',
                        backdrop: 'rgba(0,0,0,0.4)',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        padding: '1.25em',
                        width: '400px'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '/';
                        }
                    });
                }
                showSuccessAlert();
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Esperar a que SweetAlert2 esté disponible
                function showErrorAlert() {
                    if (typeof Swal === 'undefined' || typeof Swal.fire !== 'function') {
                        setTimeout(showErrorAlert, 100);
                        return;
                    }
                    Swal.fire({
                        title: 'Error',
                        text: {!! json_encode(session('error')) !!},
                        icon: 'error',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#dc3545',
                        customClass: {
                            popup: 'animated fadeInDown',
                            confirmButton: 'btn btn-danger'
                        },
                        buttonsStyling: false,
                        background: '#fff',
                        backdrop: 'rgba(0,0,0,0.4)',
                        padding: '1.25em',
                        width: '400px'
                    });
                }
                showErrorAlert();
            });
        </script>
    @endif

    {{-- Mostrar errores de validación en bloque --}}
    @if ($errors->any())
        @php
            $errorList = $errors->all();
        @endphp
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Esperar a que SweetAlert2 esté disponible
                function showValidationErrors() {
                    if (typeof Swal === 'undefined' || typeof Swal.fire !== 'function') {
                        setTimeout(showValidationErrors, 100);
                        return;
                    }
                    var errors = {!! json_encode($errorList) !!};
                    var errorsHtml = '<ul class="text-left" style="list-style-position: inside;">';
                    errors.forEach(function(error) {
                        errorsHtml += '<li>' + error + '</li>';
                    });
                    errorsHtml += '</ul>';
                    
                    Swal.fire({
                        title: 'Errores de validación',
                        html: errorsHtml,
                        icon: 'error',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#dc3545',
                        customClass: {
                            popup: 'animated fadeInDown',
                            confirmButton: 'btn btn-danger'
                        },
                        buttonsStyling: false,
                        background: '#fff',
                        backdrop: 'rgba(0,0,0,0.4)',
                        padding: '1.25em',
                        width: '500px'
                    });
                }
                showValidationErrors();
            });
        </script>
    @endif

    @yield('content')
</div>
@include('layouts.footer-registro')
