@php
    $isEdit = isset($persona);
    $fechaNacimientoValor = old('fecha_nacimiento');
    if ($isEdit && !$fechaNacimientoValor && $persona->fecha_nacimiento) {
        try {
            $fechaNacimientoValor = \Illuminate\Support\Carbon::parse($persona->fecha_nacimiento)->format('Y-m-d');
        } catch (\Throwable $th) {
            $fechaNacimientoValor = $persona->fecha_nacimiento;
        }
    }
    $caracterizacionesSeleccionadas = [];
    if ($isEdit) {
        $caracterizacionesSeleccionadas = collect(
            old('caracterizacion_ids', $persona->caracterizacionesComplementarias->pluck('id')->toArray()),
        )
            ->filter()
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    } else {
        // En modo create, preservar valores de old() después de error de validación
        $caracterizacionesSeleccionadas = collect(old('caracterizacion_ids', []))
            ->filter()
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }
    $departamentoUrlTemplate = route('departamentos.by.pais', ['pais' => '__ID__']);
    $municipioUrlTemplate = route('municipios.by.departamento', ['departamento' => '__ID__']);
    $oldDepartamentoId = old('departamento_id', $isEdit ? $persona->departamento_id : null);
    $oldMunicipioId = old('municipio_id', $isEdit ? $persona->municipio_id : null);
@endphp

<div class="row">
    <div class="col-lg-{{ $isEdit ? '8' : '12' }}">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-0">
                <h5 class="card-title m-0 text-primary">
                    <i class="fas fa-id-card mr-2"></i> Datos personales
                </h5>
            </div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="tipo_documento" class="form-label font-weight-bold">
                            Tipo de documento
                        </label>
                        <select name="tipo_documento" id="tipo_documento"
                            class="form-control @error('tipo_documento') is-invalid @enderror" required>
                            @php
                                $oldTipoDoc = old('tipo_documento', $isEdit ? $persona->tipo_documento : null);
                            @endphp
                            <option value="" disabled {{ $oldTipoDoc ? '' : 'selected' }}>
                                Seleccione un tipo de documento
                            </option>
                            @foreach ($documentos->parametros as $documento)
                                <option value="{{ $documento->id }}"
                                    {{ $oldTipoDoc == $documento->id ? 'selected' : '' }}>
                                    {{ $documento->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('tipo_documento')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="numero_documento" class="form-label font-weight-bold">
                            Número de documento
                        </label>
                        <input type="text" id="numero_documento" name="numero_documento"
                            class="form-control @error('numero_documento') is-invalid @enderror"
                            value="{{ old('numero_documento', $isEdit ? $persona->numero_documento : '') }}" required>
                        @error('numero_documento')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="primer_nombre" class="form-label font-weight-bold">
                            Primer nombre
                        </label>
                        <input type="text" id="primer_nombre" name="primer_nombre"
                            class="form-control @error('primer_nombre') is-invalid @enderror"
                            value="{{ old('primer_nombre', $isEdit ? $persona->primer_nombre : '') }}" required>
                        @error('primer_nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="segundo_nombre" class="form-label font-weight-bold">
                            Segundo nombre
                        </label>
                        <input type="text" id="segundo_nombre" name="segundo_nombre"
                            class="form-control @error('segundo_nombre') is-invalid @enderror"
                            value="{{ old('segundo_nombre', $isEdit ? $persona->segundo_nombre : '') }}">
                        @error('segundo_nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="primer_apellido" class="form-label font-weight-bold">
                            Primer apellido
                        </label>
                        <input type="text" id="primer_apellido" name="primer_apellido"
                            class="form-control @error('primer_apellido') is-invalid @enderror"
                            value="{{ old('primer_apellido', $isEdit ? $persona->primer_apellido : '') }}" required>
                        @error('primer_apellido')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="segundo_apellido" class="form-label font-weight-bold">
                            Segundo apellido
                        </label>
                        <input type="text" id="segundo_apellido" name="segundo_apellido"
                            class="form-control @error('segundo_apellido') is-invalid @enderror"
                            value="{{ old('segundo_apellido', $isEdit ? $persona->segundo_apellido : '') }}">
                        @error('segundo_apellido')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="fecha_nacimiento" class="form-label font-weight-bold">
                            Fecha de nacimiento
                        </label>
                        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento"
                            class="form-control @error('fecha_nacimiento') is-invalid @enderror"
                            value="{{ old('fecha_nacimiento', $fechaNacimientoValor) }}" required>
                        @error('fecha_nacimiento')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="genero" class="form-label font-weight-bold">Género</label>
                        <select name="genero" id="genero" class="form-control @error('genero') is-invalid @enderror"
                            required>
                            @php
                                $oldGenero = old('genero', $isEdit ? $persona->genero : null);
                            @endphp
                            <option value="" disabled {{ $oldGenero ? '' : 'selected' }}>
                                Seleccione un género
                            </option>
                            @foreach ($generos->parametros as $genero)
                                <option value="{{ $genero->id }}" {{ $oldGenero == $genero->id ? 'selected' : '' }}>
                                    {{ $genero->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('genero')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-0">
                <h5 class="card-title m-0 text-primary">
                    <i class="fas fa-address-book mr-2"></i> Contacto
                </h5>
            </div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="telefono" class="form-label font-weight-bold">Teléfono</label>
                        <input type="text" id="telefono" name="telefono"
                            class="form-control @error('telefono') is-invalid @enderror"
                            value="{{ old('telefono', $isEdit ? $persona->telefono : '') }}">
                        @error('telefono')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="celular" class="form-label font-weight-bold">Celular</label>
                        <input type="text" id="celular" name="celular"
                            class="form-control @error('celular') is-invalid @enderror"
                            value="{{ old('celular', $isEdit ? $persona->celular : '') }}">
                        @error('celular')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-12 mb-0">
                        <label for="email" class="form-label font-weight-bold">
                            Correo electrónico
                        </label>
                        <input type="email" id="email" name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $isEdit ? $persona->email : '') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-0">
                <h5 class="card-title m-0 text-primary">
                    <i class="fas fa-map-marked-alt mr-2"></i> Ubicación
                </h5>
            </div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="pais_id" class="form-label font-weight-bold">País</label>
                        @php
                            $oldPaisId = old('pais_id', $isEdit ? $persona->pais_id : null);
                        @endphp
                        <select name="pais_id" id="pais_id"
                            class="form-control @error('pais_id') is-invalid @enderror"
                            data-initial-value="{{ $oldPaisId }}">
                            <option value="" disabled {{ $oldPaisId ? '' : 'selected' }}>
                                Seleccione un país
                            </option>
                            @foreach ($paises as $pais)
                                <option value="{{ $pais->id }}" {{ $oldPaisId == $pais->id ? 'selected' : '' }}>
                                    {{ $pais->pais }}
                                </option>
                            @endforeach
                        </select>
                        @error('pais_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="departamento_id" class="form-label font-weight-bold">
                            Departamento
                        </label>
                        <select name="departamento_id" id="departamento_id"
                            class="form-control @error('departamento_id') is-invalid @enderror"
                            data-initial-value="{{ $oldDepartamentoId }}"
                            data-url-template="{{ $departamentoUrlTemplate }}">
                            <option value="" disabled {{ $oldDepartamentoId ? '' : 'selected' }}>
                                Seleccione un departamento
                            </option>
                            @foreach ($departamentos as $departamento)
                                <option value="{{ $departamento->id }}"
                                    {{ $oldDepartamentoId == $departamento->id ? 'selected' : '' }}>
                                    {{ $departamento->departamento }}
                                </option>
                            @endforeach
                        </select>
                        @error('departamento_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="municipio_id" class="form-label font-weight-bold">Municipio</label>
                        <select name="municipio_id" id="municipio_id"
                            class="form-control @error('municipio_id') is-invalid @enderror"
                            data-initial-value="{{ $oldMunicipioId }}"
                            data-url-template="{{ $municipioUrlTemplate }}">
                            <option value="" disabled selected>
                                Seleccione un municipio
                            </option>
                            {{-- Los municipios se cargarán dinámicamente por JavaScript --}}
                        </select>
                        @error('municipio_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-0">
                        <label for="direccion" class="form-label font-weight-bold">Dirección</label>
                        <input type="text" id="direccion" name="direccion"
                            class="form-control @error('direccion') is-invalid @enderror"
                            value="{{ old('direccion', $isEdit ? $persona->direccion : '') }}">
                        @error('direccion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        @php
            $showCaracterizacion = $showCaracterizacion ?? $isEdit;
        @endphp

        @if ($showCaracterizacion)
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h5 class="card-title m-0 text-primary">
                        <i class="fas fa-layer-group mr-2"></i> Caracterización
                    </h5>
                    <div class="btn-group btn-group-sm" role="group" aria-label="Acciones caracterización">
                        <button type="button" class="btn btn-outline-secondary"
                            data-action="caracterizacion-select-all">
                            <i class="fas fa-check-double mr-1"></i> Seleccionar todo
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-action="caracterizacion-clear">
                            <i class="fas fa-eraser mr-1"></i> Limpiar
                        </button>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <p class="text-muted small mb-3">
                        Marca una o varias categorías que describan la caracterización de la persona.
                        Puedes combinar múltiples opciones según corresponda.
                    </p>

                    <div class="accordion" id="caracterizacionesAccordion">
                        @php
                            $parametrosCaracterizacion = $caracterizaciones->parametros ?? collect();
                        @endphp

                        @if ($parametrosCaracterizacion->isEmpty())
                            <div class="alert alert-light mb-0">
                                No hay categorías de caracterización disponibles por el momento.
                            </div>
                        @else
                            <div class="row caracterizacion-group">
                                @foreach ($parametrosCaracterizacion as $parametro)
                                    <div class="col-md-6 col-lg-4 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            @php
                                                $isChecked = in_array(
                                                    $parametro->id,
                                                    $caracterizacionesSeleccionadas,
                                                    true,
                                                );
                                            @endphp
                                            <input type="checkbox" class="custom-control-input"
                                                id="caracterizacion-{{ $parametro->id }}"
                                                name="caracterizacion_ids[]" value="{{ $parametro->id }}"
                                                {{ $isChecked ? 'checked' : '' }}>
                                            <label class="custom-control-label"
                                                for="caracterizacion-{{ $parametro->id }}">
                                                {{ $parametro->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    @error('caracterizacion_ids')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        @endif
    </div>

    @if ($isEdit)
        <div class="col-lg-4">
            @php
                $estadoLabel = $persona->status === 1 ? 'Activo' : 'Inactivo';
                $estadoBadgeClass = $persona->status === 1 ? 'badge-success' : 'badge-danger';
                $estadoSofiaLabel = $persona->estado_sofia_label ?? 'Sin información';
                $estadoSofiaBadgeClass = $persona->estado_sofia_badge_class ?? 'bg-secondary';
            @endphp

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title m-0 text-primary">
                        <i class="fas fa-info-circle mr-2"></i> Resumen actual
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <span class="text-muted text-uppercase small d-block">Estado del usuario</span>
                            <span class="badge {{ $estadoBadgeClass }}">
                                {{ $estadoLabel }}
                            </span>
                        </li>
                        <li class="mb-3">
                            <span class="text-muted text-uppercase small d-block">Registro en SOFIA</span>
                            <span class="badge {{ $estadoSofiaBadgeClass }} text-white">
                                {{ $estadoSofiaLabel }}
                            </span>
                        </li>
                        <li class="mb-3">
                            <span class="text-muted text-uppercase small d-block">Fecha de creación</span>
                            <span>
                                {{ $persona->created_at?->format('d/m/Y H:i') ?? 'Sin información' }}
                            </span>
                        </li>
                        <li class="mb-0">
                            <span class="text-muted text-uppercase small d-block">
                                Última actualización
                            </span>
                            <span>
                                {{ $persona->updated_at?->diffForHumans() ?? 'Sin información' }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title m-0 text-primary">
                        <i class="fas fa-lightbulb mr-2"></i> Consejos rápidos
                    </h5>
                </div>
                <div class="card-body text-muted small">
                    <p class="mb-2">
                        Verifica que el correo y el celular estén actualizados; se usan para notificaciones y acceso.
                    </p>
                    <p class="mb-0">
                        Después de guardar puedes volver a la ficha para revisar los cambios aplicados.
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ==========================================
            // Manejo de caracterización (seleccionar todo/limpiar)
            // ==========================================
            const selectAllBtn = document.querySelector('[data-action="caracterizacion-select-all"]');
            const clearBtn = document.querySelector('[data-action="caracterizacion-clear"]');
            const groups = document.querySelectorAll('.caracterizacion-group');

            const toggleCheckboxes = (checked) => {
                groups.forEach(group => {
                    group.querySelectorAll('input[type="checkbox"]').forEach(chk => {
                        chk.checked = checked;
                    });
                });
            };

            if (selectAllBtn) {
                selectAllBtn.addEventListener('click', function() {
                    toggleCheckboxes(true);
                });
            }

            if (clearBtn) {
                clearBtn.addEventListener('click', function() {
                    toggleCheckboxes(false);
                });
            }

            // ==========================================
            // Validación "NINGUNA" vs otras caracterizaciones
            // ==========================================
            const caracterizacionCheckboxes = document.querySelectorAll('input[name="caracterizacion_ids[]"]');
            const ningunaCbx = document.querySelector('input[name="caracterizacion_ids[]"][value="235"]');

            if (ningunaCbx && caracterizacionCheckboxes.length > 0) {
                caracterizacionCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        if (this.value === '235' && this.checked) {
                            // Si marcó "NINGUNA", desmarcar todas las demás
                            caracterizacionCheckboxes.forEach(cbx => {
                                if (cbx.value !== '235') {
                                    cbx.checked = false;
                                }
                            });
                        } else if (this.value !== '235' && this.checked) {
                            // Si marcó otra opción, desmarcar "NINGUNA"
                            if (ningunaCbx) {
                                ningunaCbx.checked = false;
                            }
                        }
                    });
                });
            }

            // ==========================================
            // Conversión automática a mayúsculas
            // ==========================================
            const camposTexto = ['primer_nombre', 'segundo_nombre', 'primer_apellido', 'segundo_apellido'];
            camposTexto.forEach(campo => {
                const elemento = document.getElementsByName(campo)[0];
                if (elemento) {
                    elemento.addEventListener('input', function() {
                        this.value = this.value.toUpperCase();
                    });
                }
            });

            // ==========================================
            // Validación de solo números
            // ==========================================
            function soloNumeros(event) {
                const key = event.key;
                if (event.ctrlKey || event.altKey || event.metaKey) {
                    return true;
                }
                if (!/^\d$/.test(key)) {
                    event.preventDefault();
                    return false;
                }
                return true;
            }

            const camposNumericos = ['numero_documento', 'telefono', 'celular'];
            camposNumericos.forEach(campo => {
                const elemento = document.getElementsByName(campo)[0];
                if (elemento) {
                    elemento.addEventListener('keypress', soloNumeros);
                }
            });
        });
    </script>
@endpush
