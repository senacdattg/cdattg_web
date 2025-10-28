{{-- 
    Componente: Botones de acción para formularios
    Props:
    - $submitText (string): Texto del botón de envío
    - $submitIcon (string): Icono del botón de envío
    - $cancelRoute (string): Ruta para cancelar
    - $cancelText (string): Texto del botón cancelar
    - $showReset (bool): Mostrar botón de reset
    - $resetText (string): Texto del botón reset
    - $submitClass (string): Clase CSS adicional para el botón submit
--}}
@props([
    'submitText' => 'Guardar',
    'submitIcon' => 'fas fa-save',
    'cancelRoute' => null,
    'cancelText' => 'Cancelar',
    'showReset' => false,
    'resetText' => 'Limpiar',
    'submitClass' => 'btn-success'
])

<div class="row mt-4">
    <div class="col-12">
        <div class="form-actions">
            <button type="submit" class="btn {{ $submitClass }}">
                <i class="{{ $submitIcon }} mr-2"></i>
                {{ $submitText }}
            </button>
            
            @if($cancelRoute)
                <a href="{{ $cancelRoute }}" class="btn btn-secondary ml-2">
                    <i class="fas fa-times mr-2"></i>
                    {{ $cancelText }}
                </a>
            @endif
            
            @if($showReset)
                <button type="reset" class="btn btn-warning ml-2">
                    <i class="fas fa-undo mr-2"></i>
                    {{ $resetText }}
                </button>
            @endif
        </div>
    </div>
</div>

