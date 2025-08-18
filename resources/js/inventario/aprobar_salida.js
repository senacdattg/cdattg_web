document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalAprobarRechazar');
    const modalInfo = document.getElementById('modalDetalleOrden');
    const form = document.getElementById('formAprobarSalida');
    const mensaje = document.getElementById('mensajeAprobacion');
    const btnAprobar = document.querySelector('.btn_aprobar');
    const btnRechazar = document.querySelector('.btn_rechazar');
    const divMotivo = document.querySelector('.div_motivo_rechazo');

    if (btnRechazar && divMotivo) {
        btnRechazar.addEventListener('click', function(e) {
            e.preventDefault();
            divMotivo.style.display = 'block';
            document.getElementById('motivo_rechazo').classList.add('rechazo-activo');
            document.getElementById('iconAprobarRechazar').innerHTML = '<i class="fas fa-times" style="color:#dc3545;"></i>';
        });
    }
    if (btnAprobar && divMotivo) {
        btnAprobar.addEventListener('click', function(e) {
            e.preventDefault();
            divMotivo.style.display = 'none';
            document.getElementById('motivo_rechazo').classList.remove('rechazo-activo');
            document.getElementById('iconAprobarRechazar').innerHTML = '<i class="fas fa-check" style="color:#28a745;"></i>';
            mensaje.innerHTML = '<div class="msg-success">Salida de orden aprobada correctamente.</div>';
            setTimeout(function(){
                modal.classList.remove('show');
                mensaje.innerHTML = '';
                form.reset();
            }, 1500);
        });
    }

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (divMotivo && divMotivo.style.display === 'block') {
                const motivo = document.getElementById('motivo_rechazo').value.trim();
                if (!motivo) {
                    mensaje.innerHTML = '<div class="msg-error">Por favor, escribe el motivo del rechazo.</div>';
                    return;
                }
                mensaje.innerHTML = '<div class="msg-success">La orden ha sido rechazada.<br><strong>Motivo:</strong> ' + motivo + '</div>';
                setTimeout(function(){
                    modal.classList.remove('show');
                    mensaje.innerHTML = '';
                    form.reset();
                    divMotivo.style.display = 'none';
                }, 1500);
            }
        });
    }

    // Cerrar modal info orden al hacer click fuera del contenido
    function cerrarModal(modalElement) {
        if (modalElement) {
            modalElement.addEventListener('mousedown', function(e) {
                if (e.target === modalElement) {
                    modalElement.classList.remove('show');
                }
            });
        }
    }
    cerrarModal(modal);
    cerrarModal(modalInfo);

});
