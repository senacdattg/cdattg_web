{{-- 
    Componente: Layout completo de formulario
    Props:
    - $action (string): URL de acción del formulario
    - $method (string): Método HTTP (POST, PUT, PATCH)
    - $title (string): Título del formulario
    - $icon (string): Icono del título
    - $showNote (bool): Mostrar nota informativa
    - $noteText (string): Texto de la nota
--}}
@props([
    'action',
    'method' => 'POST',
    'title',
    'icon' => 'fas fa-edit',
    'showNote' => true,
    'noteText' => 'Todos los campos marcados con * son obligatorios.'
])

<div class="container-fluid">
    <div class="card">
        @include('inventario._components.card-header', [
            'title' => $title,
            'icon' => $icon
        ])
        
        <div class="card-body">
            <form action="{{ $action }}" method="POST">
                @csrf
                @if($method !== 'POST')
                    @method($method)
                @endif
                
                {{ $slot }}
                
                @include('inventario._components.form-actions', [
                    'submitText' => $submitText ?? 'Guardar',
                    'submitIcon' => $submitIcon ?? 'fas fa-save',
                    'cancelRoute' => $cancelRoute ?? null,
                    'cancelText' => $cancelText ?? 'Cancelar',
                    'showReset' => $showReset ?? false,
                    'resetText' => $resetText ?? 'Limpiar',
                    'submitClass' => $submitClass ?? 'btn-success'
                ])
                
                @if($showNote)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Nota:</strong> {{ $noteText }}
                            </div>
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>

