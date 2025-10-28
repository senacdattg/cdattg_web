@extends('inventario.layouts.base')

@push('styles')
    @vite([
        'resources/css/inventario/shared/base.css',
        'resources/css/inventario/ordenes.css'
    ])
@endpush

@section('content_header')
    <h1><i class="fas fa-exchange-alt mr-2"></i> Préstamo o Salida</h1>
@endsection

@section('main-content')
    @component('inventario._components.form-wrapper', [
        'action' => route('inventario.prestamos-salidas'),
        'method' => 'POST',
        'title' => 'Préstamo o Salida',
        'icon' => 'fas fa-exchange-alt',
        'submitText' => 'Crear Préstamo/Salida',
        'submitIcon' => 'fas fa-save',
        'cancelRoute' => route('inventario.ordenes.index'),
        'cancelText' => 'Cancelar',
        'showReset' => true,
        'resetText' => 'Limpiar',
        'noteText' => 'Todos los campos marcados con * son obligatorios. Asegúrese de seleccionar todos los elementos antes de proceder.'
    ])
                    <div class="row">
            @component('inventario._components.form-field-wrapper', [
                'name' => 'nombre',
                'label' => 'Nombre',
                'type' => 'text',
                'value' => old('nombre'),
                'required' => true,
                'icon' => 'fas fa-user'
            ])
            @endcomponent

            @component('inventario._components.form-field-wrapper', [
                'name' => 'documento',
                'label' => 'Documento',
                'type' => 'text',
                'value' => old('documento'),
                'required' => true,
                'icon' => 'fas fa-id-card'
            ])
            @endcomponent
                    </div>

                    <div class="row">
            @component('inventario._components.form-field-wrapper', [
                'name' => 'rol',
                'label' => 'Rol',
                'type' => 'select',
                'value' => old('rol'),
                'required' => true,
                'icon' => 'fas fa-user-tag',
                'placeholder' => 'Seleccionar rol...',
                'options' => [
                    'estudiante' => 'Estudiante',
                    'instructor' => 'Instructor',
                    'coordinador' => 'Coordinador',
                    'administrativo' => 'Administrativo'
                ]
            ])
            @endcomponent

            @component('inventario._components.form-field-wrapper', [
                'name' => 'programa_formacion',
                'label' => 'Nombre del programa formación',
                'type' => 'text',
                'value' => old('programa_formacion'),
                'required' => true,
                'icon' => 'fas fa-graduation-cap'
            ])
            @endcomponent
                    </div>

                    <div class="row">
            @component('inventario._components.form-field-wrapper', [
                'name' => 'ficha',
                'label' => 'Ficha',
                'type' => 'text',
                'value' => old('ficha'),
                'required' => true,
                'icon' => 'fas fa-ticket-alt'
            ])
            @endcomponent

            @component('inventario._components.form-field-wrapper', [
                'name' => 'tipo',
                'label' => 'Tipo',
                'type' => 'select',
                'value' => old('tipo'),
                'required' => true,
                'icon' => 'fas fa-tags',
                'placeholder' => 'Seleccionar tipo...',
                'options' => [
                    'prestamo' => 'Préstamo',
                    'salida' => 'Salida'
                ]
            ])
            @endcomponent
                    </div>

                    <div class="row">
            @component('inventario._components.form-field-wrapper', [
                'name' => 'fecha_adquirido',
                'label' => 'Fecha de adquisición',
                'type' => 'date',
                'value' => old('fecha_adquirido'),
                'required' => true,
                'icon' => 'fas fa-calendar-plus'
            ])
            @endcomponent

            @component('inventario._components.form-field-wrapper', [
                'name' => 'fecha_devolucion',
                'label' => 'Fecha de devolución',
                'type' => 'date',
                'value' => old('fecha_devolucion'),
                'required' => true,
                'icon' => 'fas fa-calendar-minus'
            ])
            @endcomponent
                    </div>

                    <div class="row">
            @component('inventario._components.form-field-wrapper', [
                'name' => 'descripcion',
                'label' => 'Descripción',
                'type' => 'textarea',
                'value' => old('descripcion'),
                'required' => true,
                'icon' => 'fas fa-comment-alt',
                'placeholder' => 'Describe el motivo del préstamo/salida, condiciones especiales, etc.',
                'rows' => 4,
                'colSize' => 'col-12'
            ])
            @endcomponent
        </div>
    @endcomponent
@endsection

@push('scripts')
    <script>
        // Validación de fechas
        document.addEventListener('DOMContentLoaded', function() {
            const fechaAdquirido = document.getElementById('fecha_adquirido');
            const fechaDevolucion = document.getElementById('fecha_devolucion');
            
            fechaAdquirido.addEventListener('change', function() {
                fechaDevolucion.min = this.value;
            });
            
            fechaDevolucion.addEventListener('change', function() {
                if (fechaAdquirido.value && this.value < fechaAdquirido.value) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Fecha inválida',
                        text: 'La fecha de devolución no puede ser anterior a la fecha de adquisición'
                    });
                    this.value = '';
                }
            });
        });
    </script>
@endpush
