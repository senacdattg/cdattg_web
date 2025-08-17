document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formAprobarSalida');
    const mensaje = document.getElementById('mensajeAprobacion');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        mensaje.innerHTML = '<div class="msg-success">Salida de orden aprobada correctamente.</div>';
        form.reset();
    });
});
