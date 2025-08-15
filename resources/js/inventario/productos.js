document.getElementById('imagen').addEventListener('change', function(e) {
    let reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('preview').setAttribute('src', e.target.result);
    }
    reader.readAsDataURL(this.files[0]);
});

// Validar cantidad máxima en el input del carrito
document.addEventListener('DOMContentLoaded', function() {
    const cantidadInput = document.getElementById('cantidad_carrito');
    if (cantidadInput) {
        cantidadInput.addEventListener('input', function() {
            const max = parseInt(cantidadInput.max);
            if (parseInt(cantidadInput.value) > max) {
                cantidadInput.value = max;
            }
            if (parseInt(cantidadInput.value) < 1) {
                cantidadInput.value = 1;
            }
        });
    }

    // Animación en botones del carrito
    document.querySelectorAll('.btn_carrito').forEach(btn => {
        btn.addEventListener('mouseenter', () => btn.style.transform = 'scale(1.05)');
        btn.addEventListener('mouseleave', () => btn.style.transform = 'scale(1)');
    });
});