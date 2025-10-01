// Funcionalidades para Aprendices
import Swal from 'sweetalert2';

document.addEventListener('DOMContentLoaded', function() {
    
    // Tooltip para botones de acción
    $('[data-toggle="tooltip"]').tooltip();

    // Confirmación para eliminar aprendiz
    const formsEliminar = document.querySelectorAll('.formulario-eliminar');
    formsEliminar.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Está seguro de eliminar este aprendiz?',
                text: "¡Esta acción no se podrá revertir!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Mostrar alertas de éxito/error desde sesión
    if (window.sessionSuccess) {
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: window.sessionSuccess,
            timer: 3000,
            timerProgressBar: true
        });
    }

    if (window.sessionError) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: window.sessionError,
            timer: 3000,
            timerProgressBar: true
        });
    }

    // Animación suave para el collapse del formulario de filtros
    $('#filtrosForm').on('show.bs.collapse', function () {
        $(this).prev().find('i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
    });

    $('#filtrosForm').on('hide.bs.collapse', function () {
        $(this).prev().find('i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
    });

    // Auto-focus en el campo de búsqueda
    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.focus();
    }

    // Validación de formularios
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // Limpiar formulario después de crear exitosamente
    if (window.location.search.includes('success')) {
        const form = document.querySelector('form[action*="store"]');
        if (form) {
            form.reset();
            form.classList.remove('was-validated');
        }
    }
});

