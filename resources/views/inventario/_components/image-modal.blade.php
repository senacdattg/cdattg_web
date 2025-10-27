{{-- 
    Componente: Modal para imagen expandible
    Se incluye automáticamente con el formulario de productos
    Utiliza Bootstrap modal para mostrar la imagen en pantalla completa
--}}
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="expandedImage" src="" alt="Imagen expandida" class="img-fluid">
            </div>
        </div>
    </div>
</div>
