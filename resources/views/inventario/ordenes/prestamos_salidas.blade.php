@extends('adminlte::page')

@section('title', 'Préstamo o Salida')

@section('content_header')
    <x-page-header
        icon="fas fa-exchange-alt"
        title="Préstamo o Salida"
        subtitle="Gestión de préstamos y salidas del inventario"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true]
        ]"
    />
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card">
            @include('inventario._components.card-header', [
                'title' => 'Préstamo o Salida',
                'icon' => 'fas fa-exchange-alt'
            ])
            
            <div class="card-body">
                <form action="{{ route('inventario.prestamos-salidas') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        @include('inventario._components.form-field', [
                            'name' => 'nombre',
                            'label' => 'Nombre',
                            'type' => 'text',
                            'value' => old('nombre'),
                            'required' => true,
                            'icon' => 'fas fa-user'
                        ])

                        @include('inventario._components.form-field', [
                            'name' => 'documento',
                            'label' => 'Documento',
                            'type' => 'text',
                            'value' => old('documento'),
                            'required' => true,
                            'icon' => 'fas fa-id-card'
                        ])
                    </div>

                    <div class="row">
                        @include('inventario._components.form-field', [
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

                        @include('inventario._components.form-field', [
                            'name' => 'programa_formacion',
                            'label' => 'Nombre del programa formación',
                            'type' => 'text',
                            'value' => old('programa_formacion'),
                            'required' => true,
                            'icon' => 'fas fa-graduation-cap'
                        ])
                    </div>

                    <div class="row">
                        @include('inventario._components.form-field', [
                            'name' => 'ficha',
                            'label' => 'Ficha',
                            'type' => 'text',
                            'value' => old('ficha'),
                            'required' => true,
                            'icon' => 'fas fa-ticket-alt'
                        ])

                        @include('inventario._components.form-field', [
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
                    </div>

                    <div class="row">
                        @include('inventario._components.form-field', [
                            'name' => 'fecha_adquirido',
                            'label' => 'Fecha de adquisición',
                            'type' => 'date',
                            'value' => old('fecha_adquirido'),
                            'required' => true,
                            'icon' => 'fas fa-calendar-plus'
                        ])

                        @include('inventario._components.form-field', [
                            'name' => 'fecha_devolucion',
                            'label' => 'Fecha de devolución',
                            'type' => 'date',
                            'value' => old('fecha_devolucion'),
                            'required' => true,
                            'icon' => 'fas fa-calendar-minus'
                        ])
                    </div>

                    <div class="row">
                        @include('inventario._components.form-field', [
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
                    </div>

                    @include('inventario._components.form-actions', [
                        'submitText' => 'Crear Préstamo/Salida',
                        'submitIcon' => 'fas fa-save',
                        'cancelRoute' => route('inventario.ordenes.index'),
                        'cancelText' => 'Cancelar',
                        'showReset' => true,
                        'resetText' => 'Limpiar'
                    ])
                </form>
            </div>
        </div>
    </div>
    
    {{-- Alertas --}}
    @include('layout.alertas')
    
    {{-- Footer SENA --}}
    @include('inventario._components.sena-footer')
@endsection

@push('css')
    @vite(['resources/css/style.css'])
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

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
