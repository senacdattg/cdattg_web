{{-- 
    Componente: Campo de formulario reutilizable
    Props:
    - $name (string): Nombre del campo
    - $label (string): Etiqueta del campo
    - $type (string): Tipo de input (text, email, password, select, textarea, date, etc.)
    - $value (mixed): Valor actual del campo
    - $placeholder (string): Placeholder del campo
    - $required (bool): Si el campo es obligatorio
    - $icon (string): Icono de FontAwesome
    - $options (array): Opciones para select
    - $rows (int): Filas para textarea
    - $helpText (string): Texto de ayuda
    - $colSize (string): Tamaño de columna (col-md-6, col-12, etc.)
--}}
@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'icon' => null,
    'options' => [],
    'rows' => 3,
    'helpText' => null,
    'colSize' => 'col-md-6'
])

<div class="{{ $colSize }} mb-3">
    <label for="{{ $name }}" class="form-label">
        @if($icon)
            <i class="{{ $icon }} text-primary"></i>
        @endif
        {{ $label }}
        @if($required)
            <span class="text-danger">*</span>
        @endif
    </label>
    
    @if($type === 'select')
        <select class="form-control @error($name) is-invalid @enderror" 
                id="{{ $name }}" 
                name="{{ $name }}" 
                {{ $required ? 'required' : '' }}>
            <option value="">{{ $placeholder ?? 'Seleccionar...' }}</option>
            @foreach($options as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" 
                        {{ old($name, $value) == $optionValue ? 'selected' : '' }}>
                    {{ $optionLabel }}
                </option>
            @endforeach
        </select>
    @elseif($type === 'textarea')
        <textarea class="form-control @error($name) is-invalid @enderror" 
                  id="{{ $name }}" 
                  name="{{ $name }}" 
                  rows="{{ $rows }}" 
                  placeholder="{{ $placeholder }}"
                  {{ $required ? 'required' : '' }}>{{ old($name, $value) }}</textarea>
    @else
        <input type="{{ $type }}" 
               class="form-control @error($name) is-invalid @enderror" 
               id="{{ $name }}" 
               name="{{ $name }}" 
               value="{{ old($name, $value) }}" 
               placeholder="{{ $placeholder }}"
               {{ $required ? 'required' : '' }}>
    @endif
    
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    
    @if($helpText)
        <div class="form-text">{{ $helpText }}</div>
    @endif
</div>

