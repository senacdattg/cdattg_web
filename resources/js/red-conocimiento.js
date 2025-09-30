// Funcionalidades para Red de Conocimiento
document.addEventListener('DOMContentLoaded', function() {
    
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

    // Tooltip para botones de acción
    $('[data-toggle="tooltip"]').tooltip();

    // Confirmación para eliminar
    $('form[action*="destroy"]').on('submit', function(e) {
        if (!confirm('¿Está seguro de eliminar esta red de conocimiento?')) {
            e.preventDefault();
        }
    });

    // Auto-focus en el campo de búsqueda
    const searchInput = document.getElementById('searchRedConocimiento');
    if (searchInput) {
        searchInput.focus();
    }

    // Animación suave para el collapse del formulario
    $('#createRedConocimientoForm').on('show.bs.collapse', function () {
        $(this).prev().find('i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
    });

    $('#createRedConocimientoForm').on('hide.bs.collapse', function () {
        $(this).prev().find('i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
    });

    // Convertir nombre a mayúsculas automáticamente
    const nombreInput = document.getElementById('nombre');
    if (nombreInput) {
        nombreInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }

    // Limpiar formulario después de crear exitosamente
    if (window.location.search.includes('success')) {
        const form = document.querySelector('form[action*="store"]');
        if (form) {
            form.reset();
            form.classList.remove('was-validated');
        }
    }
});
