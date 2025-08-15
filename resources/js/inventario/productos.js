import Swal from 'sweetalert2';

document.getElementById('imagen').addEventListener('change', function(e) {
    let reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('preview').setAttribute('src', e.target.result);
    }
    reader.readAsDataURL(this.files[0]);
});

if (document.querySelector('.alert-success')) {
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: document.querySelector('.alert-success').innerText,
        timer: 2500,
        showConfirmButton: false
    });
}

document.querySelectorAll('.btn_carrito').forEach(btn => {
    btn.addEventListener('mouseenter', () => btn.classList.add('animate__pulse'));
    btn.addEventListener('mouseleave', () => btn.classList.remove('animate__pulse'));
});

const cantidadInput = document.getElementById('cantidad_carrito');
if (cantidadInput) {
    cantidadInput.addEventListener('input', function() {
        const max = parseInt(cantidadInput.max);
        if (parseInt(cantidadInput.value) > max) {
            cantidadInput.value = max;
        }
    });
}