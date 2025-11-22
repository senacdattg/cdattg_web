@extends('adminlte::page')

{{-- Activar plugins de AdminLTE --}}
@section('plugins.Datatables', true)
{{-- SweetAlert2 activado globalmente en config/adminlte.php --}}
@section('plugins.Select2', true)

@section('title', 'Crear Programa Complementario')

@section('css')
    @vite(['resources/css/parametros.css'])
    <style>
        .helper-text {
            font-size: .8rem;
            color: #6c757d;
        }

        .character-count {
            font-size: .75rem;
            color: #6c757d;
        }

        .nav-form-steps .nav-link {
            border-radius: .5rem;
            border: 1px solid transparent;
        }

        .nav-form-steps .nav-link.active {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .sidebar-guidance {
            position: sticky;
            top: 5.5rem;
        }
    </style>
@endsection

@section('content_header')
    <x-page-header icon="fa-graduation-cap" title="Programa Complementario"
        subtitle="Crear nuevo programa de formación complementaria" :breadcrumb="[
            [
                'label' => 'Gestión Programas',
                'url' => route('complementarios-ofertados.index'),
                'icon' => 'fa-graduation-cap',
            ],
            ['label' => 'Crear programa', 'icon' => 'fa-plus-circle', 'active' => true],
        ]" />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-lg-9">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
                        <a href="{{ route('complementarios-ofertados.index') }}"
                            class="btn btn-outline-secondary btn-sm mb-2 mb-md-0">
                            <i class="fas fa-arrow-left mr-1"></i> Volver al listado
                        </a>
                        <div class="text-muted small">
                            <span class="badge badge-primary mr-1">Nuevo</span>
                            Completa los tres pasos para publicar el programa en la oferta complementaria.
                        </div>
                    </div>

                    <form method="POST" action="{{ route('complementarios-ofertados.store') }}" id="programaForm"
                        class="card card-outline card-primary shadow-sm">
                        @csrf
                        <div class="card-header bg-white border-0 pb-2">
                            <h5 class="card-title mb-1">
                                <i class="fas fa-plus-circle mr-2"></i>Creación de programa complementario
                            </h5>
                            <p class="text-muted mb-0">Utiliza la navegación por pestañas para completar la información
                                del programa.</p>
                        </div>

                        <div class="card-body">
                            <ul class="nav nav-pills nav-form-steps flex-column flex-md-row mb-4" id="formTabs"
                                role="tablist">
                                <li class="nav-item flex-md-fill mr-md-2 mb-2 mb-md-0" role="presentation">
                                    <a class="nav-link active d-flex align-items-center justify-content-center"
                                        id="tab-general-tab" data-toggle="tab" href="#tab-general" role="tab"
                                        aria-controls="tab-general" aria-selected="true">
                                        <i class="fas fa-layer-group mr-2"></i> Información general
                                    </a>
                                </li>
                                <li class="nav-item flex-md-fill mr-md-2 mb-2 mb-md-0" role="presentation">
                                    <a class="nav-link d-flex align-items-center justify-content-center" id="tab-config-tab"
                                        data-toggle="tab" href="#tab-config" role="tab" aria-controls="tab-config"
                                        aria-selected="false">
                                        <i class="fas fa-sliders-h mr-2"></i> Configuración académica
                                    </a>
                                </li>
                                <li class="nav-item flex-md-fill" role="presentation">
                                    <a class="nav-link d-flex align-items-center justify-content-center" id="tab-estado-tab"
                                        data-toggle="tab" href="#tab-estado" role="tab" aria-controls="tab-estado"
                                        aria-selected="false">
                                        <i class="fas fa-traffic-light mr-2"></i> Estado operativo
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content" id="formTabsContent">
                                <div class="tab-pane fade show active" id="tab-general" role="tabpanel"
                                    aria-labelledby="tab-general-tab">
                                    <div class="form-row">
                                        <div class="form-group col-md-7">
                                            <label for="nombre" class="form-label font-weight-semibold">Nombre del
                                                programa<span class="text-danger"> *</span></label>
                                            <input type="text" name="nombre" id="nombre"
                                                class="form-control @error('nombre') is-invalid @enderror"
                                                value="{{ old('nombre') }}"
                                                placeholder="Ej. Curso de fortalecimiento en matemáticas" required>
                                            <small class="helper-text">Utiliza un nombre descriptivo y fácil de reconocer
                                                por los aspirantes.</small>
                                            @error('nombre')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-5">
                                            <label for="codigo" class="form-label font-weight-semibold">Código
                                                interno<span class="text-danger"> *</span></label>
                                            <input type="text" name="codigo" id="codigo"
                                                class="form-control @error('codigo') is-invalid @enderror"
                                                value="{{ old('codigo') }}" placeholder="Ej. CMP-2025-001" required>
                                            <small class="helper-text">Respeta la convención usada por planeación
                                                académica.</small>
                                            @error('codigo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label for="descripcion" class="form-label font-weight-semibold mb-0">
                                                Descripción del programa<span class="text-danger"> *</span>
                                            </label>
                                            <span id="descripcionCounter" class="character-count">0/600</span>
                                        </div>
                                        <textarea name="descripcion" id="descripcion" rows="4"
                                            class="form-control @error('descripcion') is-invalid @enderror"
                                            placeholder="Describe objetivos, alcance y requisitos de participación" maxlength="600" required>{{ old('descripcion') }}</textarea>
                                        <small class="helper-text">Máximo 600 caracteres. La información se mostrará en la
                                            vista pública.</small>
                                        @error('descripcion')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="tab-config" role="tabpanel"
                                    aria-labelledby="tab-config-tab">
                                    <p class="text-muted">Define la logística académica del programa.</p>
                                    <div class="form-row">
                                        <div class="form-group col-sm-6">
                                            <label for="duracion" class="form-label font-weight-semibold">
                                                Duración (horas)<span class="text-danger"> *</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number" name="duracion" id="duracion"
                                                    class="form-control @error('duracion') is-invalid @enderror"
                                                    value="{{ old('duracion') }}" min="1" max="1000"
                                                    step="1" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">h</span>
                                                </div>
                                            </div>
                                            <small class="helper-text">Tiempo total planeado para completar el
                                                contenido.</small>
                                            @error('duracion')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <label for="cupos" class="form-label font-weight-semibold">
                                                Cupos ofertados<span class="text-danger"> *</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number" name="cupos" id="cupos"
                                                    class="form-control @error('cupos') is-invalid @enderror"
                                                    value="{{ old('cupos') }}" min="1" max="1000"
                                                    step="1" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i
                                                            class="fas fa-user-friends"></i></span>
                                                </div>
                                            </div>
                                            <small class="helper-text">Número máximo de participantes por cohorte.</small>
                                            @error('cupos')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label for="modalidad_id" class="form-label font-weight-semibold">
                                                Modalidad<span class="text-danger"> *</span>
                                            </label>
                                            @php
                                                $modalidadSelectClass = 'form-control select2';
                                                if ($errors->has('modalidad_id')) {
                                                    $modalidadSelectClass .= ' is-invalid';
                                                }
                                            @endphp
                                            <select name="modalidad_id" id="modalidad_id"
                                                class="{{ $modalidadSelectClass }}" required>
                                                <option value="">Seleccione una modalidad</option>
                                                @foreach ($modalidades as $modalidad)
                                                    <option value="{{ $modalidad->id }}"
                                                        {{ old('modalidad_id') == $modalidad->id ? 'selected' : '' }}>
                                                        {{ $modalidad->parametro->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="helper-text">Ejemplo: Presencial, virtual, mixta.</small>
                                            @error('modalidad_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="jornada_id" class="form-label font-weight-semibold">
                                                Jornada<span class="text-danger"> *</span>
                                            </label>
                                            @php
                                                $jornadaSelectClass = 'form-control select2';
                                                if ($errors->has('jornada_id')) {
                                                    $jornadaSelectClass .= ' is-invalid';
                                                }
                                            @endphp
                                            <select name="jornada_id" id="jornada_id" class="{{ $jornadaSelectClass }}"
                                                required>
                                                <option value="">Seleccione una jornada</option>
                                                @foreach ($jornadas as $jornada)
                                                    <option value="{{ $jornada->id }}"
                                                        {{ old('jornada_id') == $jornada->id ? 'selected' : '' }}>
                                                        {{ $jornada->jornada }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="helper-text">Franja horaria en la que se ofertará.</small>
                                            @error('jornada_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="ambiente_id" class="form-label font-weight-semibold">
                                                Ambiente<span class="text-danger"> *</span>
                                            </label>
                                            @php
                                                $ambienteSelectClass = 'form-control select2';
                                                if ($errors->has('ambiente_id')) {
                                                    $ambienteSelectClass .= ' is-invalid';
                                                }
                                                $ambienteSeleccionadoId = old('ambiente_id');
                                            @endphp
                                            <select name="ambiente_id" id="ambiente_id"
                                                class="{{ $ambienteSelectClass }}"
                                                data-placeholder="Seleccione una opción" required>
                                                <option value="">Seleccione un ambiente</option>
                                                @isset($ambientes)
                                                    @php
                                                        $ambientesGrouped = $ambientes->groupBy('piso_id');
                                                    @endphp
                                                    @foreach ($ambientesGrouped as $pisoId => $grupo)
                                                        @php
                                                            $primerPiso = optional($grupo->first()->piso)->piso;
                                                            $label = $primerPiso ?? "Piso {$pisoId}";
                                                        @endphp
                                                        <optgroup label="{{ $label }}">
                                                            @foreach ($grupo as $ambiente)
                                                                @php
                                                                    $esAmbienteSeleccionado =
                                                                        $ambienteSeleccionadoId == $ambiente->id;
                                                                @endphp
                                                                <option value="{{ $ambiente->id }}"
                                                                    @if ($esAmbienteSeleccionado) selected @endif>
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
                                            <small class="helper-text">Ambiente físico o virtual asociado al
                                                programa.</small>
                                            @error('ambiente_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="tab-estado" role="tabpanel"
                                    aria-labelledby="tab-estado-tab">
                                    <p class="text-muted">Selecciona el estado del programa en la planeación actual.</p>
                                    <div class="form-group mb-4">
                                        <label for="estado" class="form-label font-weight-semibold">Estado del
                                            programa<span class="text-danger"> *</span></label>
                                        <select name="estado" id="estado"
                                            class="form-control @error('estado') is-invalid @enderror" required>
                                            <option value="0" {{ old('estado') == '0' ? 'selected' : '' }}>Sin Oferta
                                            </option>
                                            <option value="1" {{ old('estado') == '1' ? 'selected' : '' }}>Con Oferta
                                            </option>
                                            <option value="2" {{ old('estado') == '2' ? 'selected' : '' }}>Cupos
                                                Llenos</option>
                                        </select>
                                        <small class="helper-text">Puedes actualizar este estado más adelante desde la
                                            gestión de programas.</small>
                                        @error('estado')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="alert alert-light border text-muted">
                                        <h6 class="mb-2"><i class="fas fa-info-circle mr-2"></i>Consejo</h6>
                                        <p class="mb-1 small">Utiliza <strong>Sin oferta</strong> mientras asignas
                                            horarios,
                                            ambientes o gestores.</p>
                                        <p class="mb-0 small">Marca <strong>Con oferta</strong> solo cuando esté listo para
                                            la
                                            inscripción en la vista pública.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer d-flex justify-content-end">
                            <a href="{{ route('complementarios-ofertados.index') }}" class="btn btn-light mr-2">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary" id="saveBtn">
                                <i class="fas fa-save mr-1"></i> Guardar programa
                            </button>
                        </div>
                    </form>
                </div>

                <div class="col-12 col-lg-3">
                    <div class="sidebar-guidance">
                        <div class="card card-outline card-info shadow-sm mb-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><i class="fas fa-lightbulb mr-2"></i> Recomendaciones</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0 small text-muted">
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success mr-1"></i>
                                        Verifica que el código no se repita en programas activos.
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-calendar-alt text-primary mr-1"></i>
                                        Confirma la disponibilidad del ambiente en la agenda de planeación.
                                    </li>
                                    <li>
                                        <i class="fas fa-bullhorn text-warning mr-1"></i>
                                        Describe beneficios y requisitos para aumentar la conversión.
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="card card-outline card-secondary shadow-sm">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><i class="fas fa-clipboard-list mr-2"></i> Estados</h5>
                            </div>
                            <div class="card-body small text-muted">
                                <p class="mb-2">
                                    <span class="badge badge-secondary mr-1">Sin oferta</span>
                                    Preparación interna, no visible al público.
                                </p>
                                <p class="mb-2">
                                    <span class="badge badge-success mr-1">Con oferta</span>
                                    Disponible para inscripciones en la vista pública.
                                </p>
                                <p class="mb-0">
                                    <span class="badge badge-warning mr-1">Cupos llenos</span>
                                    Capacidad completa, no admite más inscripciones.
                                </p>
                            </div>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('.select2').select2({
                    theme: 'bootstrap4',
                    width: '100%',
                    language: 'es',
                    allowClear: false
                }).on('change', function() {
                    if ($(this).val()) {
                        $(this).removeClass('is-invalid');
                    }
                });
            }

            const form = document.getElementById('programaForm');
            const saveBtn = document.getElementById('saveBtn');
            const descripcion = document.getElementById('descripcion');
            const descripcionCounter = document.getElementById('descripcionCounter');

            const updateDescripcionCounter = () => {
                descripcionCounter.textContent = `${descripcion.value.length}/600`;
            };
            updateDescripcionCounter();
            descripcion.addEventListener('input', updateDescripcionCounter);

            $('#formTabs a[data-toggle="tab"]').on('shown.bs.tab', function(event) {
                event.target.classList.add('active');
            });

            const activarTabDeCampo = (campo) => {
                const pane = campo.closest('.tab-pane');
                if (pane && !pane.classList.contains('active')) {
                    const selector = `#formTabs a[href="#${pane.id}"]`;
                    $(selector).tab('show');
                }
            };

            form.addEventListener('submit', function(e) {
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;
                let primerCampoInvalido = null;

                requiredFields.forEach(field => {
                    if (!field.value || !field.value.trim()) {
                        field.classList.add('is-invalid');
                        isValid = false;
                        if (!primerCampoInvalido) {
                            primerCampoInvalido = field;
                        }
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    if (primerCampoInvalido) {
                        activarTabDeCampo(primerCampoInvalido);
                        primerCampoInvalido.focus();
                    }
                    Swal.fire({
                        icon: 'warning',
                        title: 'Campos obligatorios',
                        text: 'Verifica los campos resaltados para continuar.',
                        confirmButtonText: 'Entendido'
                    });
                    return;
                }

                saveBtn.disabled = true;
                saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Guardando...';
            });
        });
    </script>
@endsection
