document.addEventListener('DOMContentLoaded', function() {
    const imagenInput = document.getElementById('imagen');
    const imgBox = document.querySelector('.inventario-img-box');
    const previewImg = document.getElementById('preview');

    if (imgBox) {
        imgBox.addEventListener('click', function() {
            imagenInput.click();
        });
    }

    if (imagenInput) {
        imagenInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.setAttribute('src', e.target.result);
                    imgBox.classList.add('has-image');
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    // Forzar la activación de etiquetas flotantes para selects con valor
    document.querySelectorAll('.form-floating select').forEach(select => {
        if (select.value) {
            select.previousElementSibling.style.opacity = '0.65';
            select.previousElementSibling.style.transform = 'scale(0.85) translateY(-0.5rem) translateX(0.15rem)';
        }
    });

    // Animaciones de entrada para los campos del formulario
    const formElements = document.querySelectorAll('.container_form .form-control, .container_form .btn');
    formElements.forEach((el, index) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = `opacity 0.5s ease ${index * 0.05}s, transform 0.5s ease ${index * 0.05}s`;
    });

    setTimeout(() => {
        formElements.forEach(el => {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        });
    }, 500); // Retraso para que la animación del contenedor principal termine
});

document.addEventListener('DOMContentLoaded', function() {
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach(card => {
        // Efecto al pasar el mouse
        card.addEventListener('mouseenter', function() {
            const img = this.querySelector('.product-image');
            if (img) {
                img.style.transition = 'transform 0.3s ease';
            }
        });
        
        card.addEventListener('click', function(e) {
            if (!e.target.closest('.action-btn')) {
                const productId = this.getAttribute('data-product-id');
                console.log(`Producto seleccionado ID: ${productId}`);
            }
        });
    });

    // Animación para el mensaje de éxito
    const successAlert = document.querySelector('.alert-success');
    if (successAlert) {
        setTimeout(() => {
            successAlert.style.transition = 'opacity 0.5s ease';
            successAlert.style.opacity = '0';
            setTimeout(() => {
                successAlert.remove();
            }, 500);
        }, 3000);
    }

    // Filtros y búsqueda (puedes implementar esto luego)
    console.log('Sistema de productos cargado correctamente');
    
    // Aquí puedes agregar más funcionalidad según sea necesario
});
