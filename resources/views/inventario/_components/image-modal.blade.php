{{-- 
    Componente: Modal para imagen expandible
    Modal moderno con controles interactivos
    Requiere: imagen.css y imagen.js
--}}
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content image-modal-modern">
            {{-- Header --}}
            <div class="modal-header image-modal-header">
                <h5 class="modal-title text-white">
                    <i class="fas fa-search-plus mr-2"></i>
                    Vista Previa de Imagen
                </h5>
                <div class="modal-header-controls">
                    <button type="button" class="btn btn-sm btn-light" id="zoomIn" title="Acercar (Tecla +)">
                        <i class="fas fa-search-plus"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-light" id="zoomOut" title="Alejar (Tecla -)">
                        <i class="fas fa-search-minus"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-light" id="resetZoom" title="Restablecer (Tecla 0)">
                        <i class="fas fa-redo"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-light" data-dismiss="modal" aria-label="Cerrar" title="Cerrar (ESC)">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            {{-- Body --}}
            <div class="modal-body image-modal-body">
                <div class="image-container" id="imageContainer">
                    <img id="expandedImage" src="" alt="Imagen expandida" class="expanded-image">
                </div>
                <div class="image-modal-info">
                    <span id="imageInfo"></span>
                </div>
            </div>
            
            {{-- Footer --}}
            <div class="modal-footer image-modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Instrucciones de uso (visible solo en hover del botón de ayuda) --}}
<div class="d-none" id="modalInstructions">
    <p><strong>Controles del Modal:</strong></p>
    <ul>
        <li><kbd>+</kbd> o <kbd>=</kbd> - Acercar</li>
        <li><kbd>-</kbd> - Alejar</li>
        <li><kbd>0</kbd> - Restablecer vista</li>
        <li><kbd>ESC</kbd> - Cerrar modal</li>
        <li><strong>Doble click</strong> - Zoom rápido</li>
        <li><strong>Arrastrar</strong> - Mover imagen (cuando está ampliada)</li>
        <li><strong>Scroll del mouse</strong> - Zoom continuo</li>
    </ul>
</div>
