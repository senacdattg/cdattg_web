{{-- Notificaciones globales con SweetAlert2 (AdminLTE nativo) --}}
{{-- Este script maneja automáticamente todas las notificaciones flash de sesión --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Esperar a que SweetAlert2 esté disponible (cargado por AdminLTE)
        function showNotifications() {
            if (typeof Swal === 'undefined' || typeof Swal.fire !== 'function') {
                setTimeout(showNotifications, 100);
                return;
            }

            // Notificación de éxito
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: '{{ session('success') }}',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#28a745',
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            // Notificación de error
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '{{ session('error') }}',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#d33'
                });
            @endif

            // Notificación de advertencia
            @if (session('warning'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Advertencia',
                    text: '{{ session('warning') }}',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#ffc107'
                });
            @endif

            // Notificación de información
            @if (session('info'))
                Swal.fire({
                    icon: 'info',
                    title: 'Información',
                    text: '{{ session('info') }}',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#17a2b8',
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            // Errores de validación
            @if ($errors->any())
                var errorsHtml = '<ul class="text-left mb-0">';
                @foreach ($errors->all() as $error)
                    errorsHtml += '<li>{{ $error }}</li>';
                @endforeach
                errorsHtml += '</ul>';

                Swal.fire({
                    icon: 'error',
                    title: 'Errores de validación',
                    html: errorsHtml,
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#d33'
                });
            @endif
        }

        showNotifications();

        // Manejar confirmaciones de eliminación para formularios con clase .formulario-eliminar
        document.addEventListener('submit', function(e) {
            if (e.target.matches('.formulario-eliminar')) {
                e.preventDefault();
                var form = e.target;

                Swal.fire({
                    title: '¿Eliminar registro?',
                    text: "Esta acción no se puede revertir",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-trash-alt"></i> Eliminar',
                    cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                    customClass: {
                        confirmButton: 'btn btn-danger mx-2',
                        cancelButton: 'btn btn-secondary mx-2',
                        popup: 'animated fadeInDown',
                        actions: 'gap-2',
                        container: 'swal2-backdrop-blur'
                    },
                    buttonsStyling: false,
                    background: '#fff',
                    backdrop: 'rgba(0,0,0,0.4)',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    padding: '1.25em',
                    width: '360px'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }
        });
    });
</script>

<style>
    .swal2-backdrop-blur {
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
    }
</style>
