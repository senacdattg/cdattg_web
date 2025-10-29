document.addEventListener('DOMContentLoaded', function() {
    const imagenInput = document.getElementById('imagen');
    const imgBox = document.querySelector('.inventario-img-box');
    const previewImg = document.getElementById('preview');

    if (imgBox && imagenInput) {
        // Manejar el clic en la caja de imagen del formulario
        imgBox.addEventListener('click', function(e) {
            const isDefaultImage = previewImg.src.includes('default.png') || previewImg.src.includes('imagen_default.png');
            
            if (isDefaultImage) {
                // Si es imagen por defecto, solo abrir selector de archivo
                e.stopPropagation();
                e.preventDefault();
                imagenInput.click();
            }
            // Si hay imagen real, permitir que el modal se abra (no hacer nada aquí)
        });

        // Interceptar específicamente el evento de la imagen del preview para el modal
        if (previewImg) {
            previewImg.addEventListener('click', function(e) {
                const isDefaultImage = this.src.includes('default.png') || this.src.includes('imagen_default.png');
                
                if (isDefaultImage) {
                    // Si es imagen por defecto, evitar que se abra el modal
                    e.stopPropagation();
                    e.preventDefault();
                    imagenInput.click();
                }
             
            });
        }
    }

    if (imagenInput) {
        imagenInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.setAttribute('src', e.target.result);
                    imgBox.classList.add('has-image');
                    imgBox.style.cursor = 'pointer';
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    // Verificar al cargar la página si ya hay una imagen
    if (previewImg) {
        const isDefaultImage = previewImg.src.includes('default.png') || previewImg.src.includes('imagen_default.png');
        if (!isDefaultImage) {
            imgBox.classList.add('has-image');
            imgBox.style.cursor = 'pointer';
        } else {
            imgBox.style.cursor = 'copy';
        }
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

    // Funcionalidad de búsqueda de productos
    const searchInput = document.getElementById('searchProducts');
    const clearButton = document.getElementById('clearSearch');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            
            // Mostrar/ocultar botón de limpiar
            if (searchTerm.length > 0) {
                clearButton.style.display = 'block';
            } else {
                clearButton.style.display = 'none';
            }

            // Filtrar productos
            filterProducts(searchTerm);
        });

        // Limpiar búsqueda
        if (clearButton) {
            clearButton.addEventListener('click', function() {
                searchInput.value = '';
                clearButton.style.display = 'none';
                filterProducts('');
                searchInput.focus();
            });
        }
    }

    function filterProducts(searchTerm) {
        let visibleCount = 0;
        
        productCards.forEach(card => {
            const productName = card.querySelector('.product-title')?.textContent.toLowerCase() || '';
            const productInfo = card.querySelector('.product-info')?.textContent.toLowerCase() || '';
            
            const isVisible = productName.includes(searchTerm) || 
                            productInfo.includes(searchTerm);
            
            if (isVisible) {
                card.style.display = 'block';
                card.style.animation = 'fadeIn 0.3s ease';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Mostrar mensaje si no hay resultados
        showNoResultsMessage(visibleCount === 0 && searchTerm.length > 0);
    }

    function showNoResultsMessage(show) {
        let noResultsMsg = document.getElementById('no-results-message');
        
        if (show && !noResultsMsg) {
            noResultsMsg = document.createElement('div');
            noResultsMsg.id = 'no-results-message';
            noResultsMsg.className = 'no-results-message';
            noResultsMsg.innerHTML = `
                <div class="no-results-content">
                    <i class="fas fa-search fa-3x"></i>
                    <h3>No se encontraron productos</h3>
                    <p>Intenta con otros términos de búsqueda</p>
                </div>
            `;
            
            const productGrid = document.querySelector('.product-grid');
            if (productGrid) {
                productGrid.parentNode.insertBefore(noResultsMsg, productGrid.nextSibling);
            }
        } else if (!show && noResultsMsg) {
            noResultsMsg.remove();
        }
    }

    // Animación CSS para fade in
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .no-results-message {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }
        
        .no-results-content i {
            color: #dee2e6;
            margin-bottom: 20px;
        }
        
        .no-results-content h3 {
            margin-bottom: 10px;
            color: #495057;
        }
    `;
    document.head.appendChild(style);

    console.log('Sistema de productos cargado correctamente');
    
    
document.addEventListener('DOMContentLoaded', function() {
    const inputCodigo = document.getElementById('inputCodigoBarras');
    const resultadoDiv = document.getElementById('resultadoBusqueda');

    // Enfocar automáticamente al abrir el modal
    $('#modalEscanear').on('shown.bs.modal', function () {
        inputCodigo.focus();
        inputCodigo.value = '';
        resultadoDiv.innerHTML = '';
    });

    // Detectar "Enter" después de escanear
    inputCodigo.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            const codigo = inputCodigo.value.trim();

            if (codigo === '') return;

            resultadoDiv.innerHTML = '<p class="text-info">🔍 Buscando producto...</p>';

            fetch(`/inventario/productos/buscar/${codigo}`)
                .then(response => {
                    if (!response.ok) throw new Error('Producto no encontrado');
                    return response.json();
                })
                .then(producto => {
                    resultadoDiv.innerHTML = `
                        <div class="alert alert-success mt-3">
                            ✅ Producto encontrado: <strong>${producto.producto}</strong>. Redirigiendo...
                        </div>
                    `;
                    setTimeout(() => {
                        window.location.href = `/inventario/productos/${producto.id}`;
                    }, 1000);
                })
                .catch(error => {
                    resultadoDiv.innerHTML = `
                        <div class="alert alert-danger mt-3">
                                No se encontró ningún producto con el código <strong>${codigo}</strong>.
                        </div>
                    `;
                });
        }
    });
});

});
