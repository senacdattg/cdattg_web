@extends('adminlte::page')

@section('title', 'Crear Programa Complementario')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2-bootstrap4.min.css') }}">
    <style>
        .form-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid #007bff;
        }

        .form-section h6 {
            color: #007bff;
            margin-bottom: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .form-section h6 i {
            margin-right: 0.5rem;
        }

        .compact-form .form-group {
            margin-bottom: 1rem;
        }

        .compact-form .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .compact-form .form-control {
            font-size: 0.9rem;
            padding: 0.5rem 0.75rem;
        }

        .compact-form .required-field::after {
            content: " *";
            color: #dc3545;
        }

        .floating-save-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Ajuste visual Select2 para que coincida con .form-control compacta */
        .select2-container {
            width: 100% !important;
        }

        .select2-container--bootstrap4 .select2-selection--single {
            font-size: 0.9rem;
            background-color: #fff;
            color: #495057;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            /* el padding lo maneja el elemento renderizado para igualar a un select nativo */
            padding: 0;
            height: calc(2.25rem + 2px);
            /* igual altura que .form-control */
            display: block;
            overflow: hidden;
            /* evita que el texto se salga debajo del control */
        }

        .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
            padding: 0.5rem 2rem 0.5rem 0.75rem;
            /* mismo padding que .form-control */
            margin: 0;
            line-height: 1.5;
            /* igual line-height que .form-control */
            width: 100%;
        }

        .select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder {
            line-height: 1.5;
            color: #6c757d;
        }

        .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
            height: 2.25rem;
            /* alto igual al control */
            right: 0.75rem;
            top: 0;
            transform: none;
        }

        /* Ocultar botón de limpiar para igualar al campo Estado (sin "x") */
        .select2-container--bootstrap4 .select2-selection__clear {
            display: none !important;
        }

        /* Dropdown consistente con .form-control */
        .select2-container--bootstrap4 .select2-dropdown {
            border-color: #ced4da;
            border-radius: 0.25rem;
        }

        /* Estados de focus/abierto como .form-control:focus */
        .select2-container--bootstrap4.select2-container--focus .select2-selection,
        .select2-container--bootstrap4.select2-container--open .select2-selection {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
        }

        /* Estado inválido coherente con Bootstrap cuando el select original tiene is-invalid */
        select.is-invalid+.select2 .select2-selection {
            border-color: #dc3545 !important;
        }

        /* Fuente consistente en opciones y optgroups */
        .select2-container--bootstrap4 .select2-results__option {
            font-size: 0.9rem;
        }

        .select2-container--bootstrap4 .select2-results__group {
            font-weight: 600;
        }

        /* Placeholder */
        .select2-container--bootstrap4 .select2-selection__placeholder {
            color: #6c757d;
        }
    </style>
@endsection

@section('content_header')
    <x-page-header icon="fa-graduation-cap" title="Programa Complementario"
        subtitle="Crear nuevo programa de formación complementaria" :breadcrumb="[
            [
                'label' => 'Gestión Programas',
                'url' => route('gestion-programas-complementarios'),
                'icon' => 'fa-graduation-cap',
            ],
            ['label' => 'Crear programa', 'icon' => 'fa-plus-circle', 'active' => true],
        ]" />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <a class="btn btn-outline-secondary btn-sm mb-3"
                        href="{{ route('gestion-programas-complementarios') }}">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>

                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-plus-circle mr-2"></i>Crear Programa Complementario
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('complementarios-ofertados.store') }}"
                                class="row compact-form" id="programaForm">
                                @csrf

                                <!-- Información Básica del Programa -->
                                <div class="col-md-12">
                                    <div class="form-section">
                                        <h6><i class="fas fa-info-circle"></i> Información Básica</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="nombre" class="form-label required-field">Nombre del
                                                        Programa</label>
                                                    <input type="text" name="nombre" id="nombre"
                                                        class="form-control @error('nombre') is-invalid @enderror"
                                                        value="{{ old('nombre') }}"
                                                        placeholder="Ingrese el nombre del programa" required>
                                                    @error('nombre')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="codigo" class="form-label required-field">Código del
                                                        Programa</label>
                                                    <input type="text" name="codigo" id="codigo"
                                                        class="form-control @error('codigo') is-invalid @enderror"
                                                        value="{{ old('codigo') }}"
                                                        placeholder="Ingrese el código del programa" required>
                                                    @error('codigo')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="descripcion"
                                            class="form-label required-field">Descripción</label>
                                            <textarea name="descripcion" id="descripcion" rows="4"
                                                class="form-control
                                                @error('descripcion') is-invalid
                                                @enderror" placeholder="Describa el programa de formación..."
                                                required>{{ old('descripcion') }}</textarea>
                                            @error('descripcion')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Configuración del Programa -->
                                <div class="col-md-6">
                                    <div class="form-section">
                                        <h6><i class="fas fa-cog"></i> Configuración</h6>
                                        <div class="form-group">
                                            <label for="duracion"
                                            class="form-label required-field">Duración (horas)</label>
                                            <input type="number" name="duracion" id="duracion"
                                                class="form-control @error('duracion') is-invalid @enderror"
                                                value="{{ old('duracion') }}" min="1" max="1000" required>
                                            @error('duracion')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="cupos" class="form-label required-field">Cupos</label>
                                            <input type="number" name="cupos" id="cupos"
                                                class="form-control @error('cupos') is-invalid @enderror"
                                                value="{{ old('cupos') }}" min="1" max="1000" required>
                                            @error('cupos')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-section">
                                        <h6><i class="fas fa-calendar-alt"></i> Modalidad y Jornada</h6>
                                        <div class="form-group">
                                            <label for="modalidad_id"
                                            class="form-label required-field">Modalidad</label>
                                            <select name="modalidad_id" id="modalidad_id"
                                                class="form-control
                                                @error('modalidad_id') is-invalid @enderror" required>
                                                <option value="">Seleccione una modalidad</option>
                                                @foreach ($modalidades as $modalidad)
                                                    <option value="{{ $modalidad->id }}"
                                                        {{ old('modalidad_id') == $modalidad->id ? 'selected' : '' }}>
                                                        {{ $modalidad->parametro->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('modalidad_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="jornada_id" class="form-label required-field">Jornada</label>
                                            <select name="jornada_id" id="jornada_id"
                                                class="form-control @error('jornada_id') is-invalid @enderror" required>
                                                <option value="">Seleccione una jornada</option>
                                                @foreach ($jornadas as $jornada)
                                                    <option value="{{ $jornada->id }}"
                                                        {{ old('jornada_id') == $jornada->id ? 'selected' : '' }}>
                                                        {{ $jornada->jornada }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('jornada_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="ambiente_id" class="form-label required-field">Ambiente</label>
                                            <select name="ambiente_id" id="ambiente_id"
                                                class="form-control select2bs4 w-100
                                                @error('ambiente_id') is-invalid @enderror"
                                                data-placeholder="Seleccione una opción" required>
                                                <option value="">Seleccione un ambiente</option>
                                                @isset($ambientes)
                                                    @php $ambientesGrouped = $ambientes->groupBy('piso_id'); @endphp
                                                    @foreach ($ambientesGrouped as $pisoId => $grupo)
                                                        @php $label = optional($grupo->first()->piso)->piso ?? "Piso {$pisoId}"; @endphp
                                                        <optgroup label="{{ $label }}">
                                                            @foreach ($grupo as $ambiente)
                                                                <option value="{{ $ambiente->id }}"
                                                                    {{ old('ambiente_id') == $ambiente->id ? 'selected' : '' }}>
                                                                    {{ $ambiente->title }}
                                                                </option>
                                                            @endforeach
                                                        </optgroup>
                                                    @endforeach
                                                @else
                                                    <optgroup label="Ambientes">
                                                        <option value="" disabled>No hay ambientes disponibles</option>
                                                    </optgroup>
                                                @endisset
                                            </select>
                                            @error('ambiente_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Estado del Programa -->
                                <div class="col-md-12">
                                    <div class="form-section">
                                        <h6><i class="fas fa-chart-line"></i> Estado del Programa</h6>
                                        <div class="form-group">
                                            <label for="estado" class="form-label required-field">Estado</label>
                                            <select name="estado" id="estado"
                                                class="form-control @error('estado') is-invalid @enderror" required>
                                                <option value="0" {{ old('estado') == '0' ? 'selected' : '' }}>Sin
                                                    Oferta</option>
                                                <option value="1" {{ old('estado') == '1' ? 'selected' : '' }}>Con
                                                    Oferta</option>
                                                <option value="2" {{ old('estado') == '2' ? 'selected' : '' }}>Cupos
                                                    Llenos</option>
                                            </select>
                                            @error('estado')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Botones -->
                                <div class="col-12">
                                    <hr class="mt-4">
                                    <div class="d-flex justify-content-end">
                                        <a href="{{ route('gestion-programas-complementarios') }}"
                                            class="btn btn-light mr-2">
                                            Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-primary" id="saveBtn">
                                            <i class="fas fa-save mr-1"></i>Crear Programa
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection

@section('js')
    <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar Select2 si está disponible
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('.select2bs4').select2({
                    theme: 'bootstrap4',
                    placeholder: function() {
                        return $(this).data('placeholder') || 'Seleccione una opción';
                    },
                    language: 'es',
                    width: '100%',
                    allowClear: false
                });

                // Quitar estado inválido cuando cambie el valor
                $('.select2bs4').on('change', function() {
                    if ($(this).val()) {
                        $(this).removeClass('is-invalid');
                    }
                });
            } else {
                console.warn('Select2 no está disponible');
            }

            // Validación del formulario
            const form = document.getElementById('programaForm');
            const saveBtn = document.getElementById('saveBtn');

            form.addEventListener('submit', function(e) {
                // Validar campos requeridos
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('is-invalid');
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    alert('Por favor complete todos los campos obligatorios.');
                    return;
                }

                // Deshabilitar botón mientras se envía
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Creando...';
            });

            // Validación en tiempo real
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.hasAttribute('required') && !this.value.trim()) {
                        this.classList.add('is-invalid');
                    } else {
                        this.classList.remove('is-invalid');
                    }
                });
            });
        });
    </script>
@endsection
