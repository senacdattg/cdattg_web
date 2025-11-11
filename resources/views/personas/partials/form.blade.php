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
    $departamentoUrlTemplate = \Illuminate\Support\Facades\Route::has('departamentos.by.pais')
        ? route('departamentos.by.pais', ['pais' => '__ID__'])
        : null;
    $municipioUrlTemplate = \Illuminate\Support\Facades\Route::has('municipios.by.departamento')
        ? route('municipios.by.departamento', ['departamento' => '__ID__'])
        : null;
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
                        <small class="form-text text-muted">
                            Debe tener al menos 14 años para registrarse.
                        </small>
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
                            $paisesEndpoint = \Illuminate\Support\Facades\Route::has('api.paises')
                                ? route('api.paises')
                                : url('/api/paises');
                        @endphp
                        <select name="pais_id" id="pais_id"
                            class="form-control @error('pais_id') is-invalid @enderror"
                            data-initial-value="{{ $oldPaisId }}" data-url="{{ $paisesEndpoint }}">
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
                        <div class="input-group">
                            <input type="text" id="direccion" name="direccion"
                                class="form-control @error('direccion') is-invalid @enderror"
                                value="{{ old('direccion', $isEdit ? $persona->direccion : '') }}"
                                placeholder="Usa el asistente para ingresar una dirección estructurada" readonly>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-primary" id="toggleAddressForm"
                                    aria-haspopup="dialog" aria-controls="addressModal">
                                    <i class="fas fa-map-marker-alt mr-1"></i>Editar
                                </button>
                            </div>
                            @error('direccion')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="form-text text-muted">
                            Haz clic en el campo o en “Editar” para capturar la dirección de forma estructurada.
                        </small>
                    </div>
                </div>

                <div class="modal fade" id="addressModal" tabindex="-1" role="dialog"
                    aria-labelledby="addressModalLabel" aria-hidden="true" data-backdrop="static"
                    data-keyboard="false">
                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="addressModalLabel">Ingresar dirección estructurada</h5>
                                <button type="button" class="close text-white" data-dismiss="modal"
                                    aria-label="Cerrar">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-info border-info d-flex align-items-start" role="alert">
                                    <i class="fas fa-info-circle fa-lg mr-2 mt-1 text-info"></i>
                                    <div>
                                        <strong>Consejo:</strong>
                                        <p class="mb-0 small">
                                            Usa el formato estructurado para asegurar notificaciones y
                                            georreferenciación correctas.
                                        </p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tipo_via">1. Tipo de vía principal *</label>
                                            <select class="form-control address-field" id="tipo_via"
                                                data-required="true">
                                                <option value="">Seleccione...</option>
                                                <option value="Carrera">Carrera</option>
                                                <option value="Calle">Calle</option>
                                                <option value="Transversal">Transversal</option>
                                                <option value="Diagonal">Diagonal</option>
                                                <option value="Avenida">Avenida</option>
                                                <option value="Autopista">Autopista</option>
                                                <option value="Circular">Circular</option>
                                                <option value="Vía">Vía</option>
                                                <option value="Pasaje">Pasaje</option>
                                                <option value="Manzana">Manzana</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="numero_via">2. Número o nombre de vía principal *</label>
                                            <input type="text" class="form-control address-field" id="numero_via"
                                                placeholder="Ej: 9A, 7 Bis, 45" data-required="true">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="letra_via">3. Letra o complemento de vía principal</label>
                                            <input type="text" class="form-control address-field" id="letra_via"
                                                placeholder="Ej: A, B, Bis (opcional)" maxlength="5">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="via_secundaria">4. Vía secundaria o intersección</label>
                                            <select class="form-control address-field" id="via_secundaria">
                                                <option value="">Seleccione...</option>
                                                <option value="Carrera">Carrera</option>
                                                <option value="Calle">Calle</option>
                                                <option value="Transversal">Transversal</option>
                                                <option value="Diagonal">Diagonal</option>
                                                <option value="Avenida">Avenida</option>
                                                <option value="Autopista">Autopista</option>
                                                <option value="Circular">Circular</option>
                                                <option value="Vía">Vía</option>
                                                <option value="Pasaje">Pasaje</option>
                                                <option value="Manzana">Manzana</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="numero_casa">5. Número de casa o edificio *</label>
                                            <input type="text" class="form-control address-field" id="numero_casa"
                                                placeholder="Ej: 34-15, 45-20, 12" data-required="true">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="complementos">6. Complementos</label>
                                            <input type="text" class="form-control address-field"
                                                id="complementos"
                                                placeholder="Ej: Apto 301, Bloque 2, Oficina 5 (opcional)">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="barrio">7. Barrio o vereda</label>
                                            <input type="text" class="form-control address-field" id="barrio"
                                                placeholder="Ej: Centro, La Candelaria (opcional)">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="alert alert-light border mt-4">
                                            <small>
                                                <strong>Ejemplo:</strong><br>
                                                <span class="text-muted">Carrera 9A BIS #34-15 Este Apto 301,
                                                    Barrio Centro</span><br>
                                                <span class="text-muted">
                                                    Los campos marcados con * son obligatorios.
                                                </span>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" id="cancelAddress">
                                    <i class="fas fa-times mr-2"></i>Cancelar
                                </button>
                                <button type="button" class="btn btn-primary" id="saveAddress">
                                    <i class="fas fa-save mr-2"></i>Guardar dirección
                                </button>
                            </div>
                        </div>
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

@once
    @push('js')
        @vite('resources/js/personas/form.js')
    @endpush
@endonce
