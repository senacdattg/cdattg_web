<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .swal2-backdrop-blur {
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            var successMessage = @json(Session::get('success'));
            var errorMessage = @json(Session::get('error'));

            if (successMessage) {
                Toastify({
                    text: `<i class="fas fa-check-circle"></i> ${successMessage}`,
                    duration: 3000,
                    gravity: "top",
                    position: "center",
                    escapeMarkup: false,
                    style: {
                        background: "#ffffff",
                        color: "#087f23",
                        border: "1px solid #c3e6cb",
                        borderRadius: "6px",
                        boxShadow: "0 4px 12px rgba(0,0,0,0.15)",
                        minWidth: "400px",
                        padding: "16px",
                        textAlign: "center",
                        fontSize: "16px",
                        fontWeight: "500",
                        fontFamily: "'Segoe UI', Arial, sans-serif"
                    },
                    close: true
                }).showToast();
            }

            if (errorMessage) { 
                Toastify({
                    text: `<i class="fas fa-exclamation-circle"></i> ${errorMessage}`,
                    duration: 3000,
                    gravity: "top",
                    position: "center",
                    escapeMarkup: false,
                    style: {
                        background: "#ffffff",
                        color: "#c62828",
                        border: "1px solid #f5c6cb",
                        borderRadius: "6px",
                        boxShadow: "0 4px 12px rgba(0,0,0,0.15)",
                        minWidth: "400px",
                        padding: "16px",
                        textAlign: "center",
                        fontSize: "16px",
                        fontWeight: "500",
                        fontFamily: "'Segoe UI', Arial, sans-serif"
                    },
                    close: true
                }).showToast();
            }

            // Validation errors
            var validationErrors = @json($errors->all());
            if (validationErrors.length > 0) {
                validationErrors.forEach(function(error) {
                    Toastify({
                        text: `<i class="fas fa-exclamation-triangle"></i> ${error}`,
                        duration: 4000,
                        gravity: "top",
                        position: "center",
                        escapeMarkup: false,
                        style: {
                            background: "#ffffff",
                            color: "#c62828",
                            border: "1px solid #f5c6cb",
                            borderRadius: "6px",
                            boxShadow: "0 4px 12px rgba(0,0,0,0.15)",
                            minWidth: "400px",
                            padding: "16px",
                            textAlign: "center",
                            fontSize: "16px",
                            fontWeight: "500",
                            fontFamily: "'Segoe UI', Arial, sans-serif"
                        },
                        close: true
                    }).showToast();
                });
            }
        }, 350);

        // Keep the form submit listener outside the timeout
        // Replace the simple confirm with SweetAlert2
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
                    backdrop: `rgba(0,0,0,0.4)`,
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
