@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <x-page-header icon="fa-cogs" title="Personas" subtitle="Gestión de personas del sistema" :breadcrumb="[
        ['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'],
        ['label' => 'Personas', 'url' => route('personas.index'), 'icon' => 'fa-cog'],
        ['label' => 'Editar Persona', 'icon' => 'fa-edit', 'active' => true],
    ]" />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a class="btn btn-outline-secondary" href="{{ route('personas.show', $persona->id) }}">
                    <i class="fas fa-arrow-left mr-1"></i> Ver persona
                </a>
                <span class="text-muted small">Actualiza los datos y guarda los cambios.</span>
            </div>

            @php
                $fechaNacimientoValor = old('fecha_nacimiento');
                if (!$fechaNacimientoValor && $persona->fecha_nacimiento) {
                    try {
                        $fechaNacimientoValor = \Illuminate\Support\Carbon::parse($persona->fecha_nacimiento)->format(
                            'Y-m-d',
                        );
                    } catch (\Throwable $th) {
                        $fechaNacimientoValor = $persona->fecha_nacimiento;
                    }
                }
            @endphp

            <form method="POST" action="{{ route('personas.update', $persona->id) }}" id="form-persona-edit">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white border-0">
                                <h5 class="card-title m-0 text-primary">
                                    <i class="fas fa-id-card mr-2"></i> Datos personales
                                </h5>
                            </div>
                            <div class="card-body pt-0">
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label for="tipo_documento" class="form-label font-weight-bold">Tipo de
                                            documento</label>
                                        <select name="tipo_documento" id="tipo_documento"
                                            class="form-control @error('tipo_documento') is-invalid @enderror" required>
                                            <option value="" disabled
                                                {{ old('tipo_documento', $persona->tipo_documento) ? '' : 'selected' }}>
                                                Seleccione un tipo de documento
                                            </option>
                                            @foreach ($documentos->parametros as $documento)
                                                <option value="{{ $documento->id }}"
                                                    {{ old('tipo_documento', $persona->tipo_documento) == $documento->id ? 'selected' : '' }}>
                                                    {{ $documento->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('tipo_documento')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label for="numero_documento" class="form-label font-weight-bold">Número de
                                            documento</label>
                                        <input type="text" id="numero_documento" name="numero_documento"
                                            class="form-control @error('numero_documento') is-invalid @enderror"
                                            value="{{ old('numero_documento', $persona->numero_documento) }}" required>
                                        @error('numero_documento')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label for="primer_nombre" class="form-label font-weight-bold">Primer nombre</label>
                                        <input type="text" id="primer_nombre" name="primer_nombre"
                                            class="form-control @error('primer_nombre') is-invalid @enderror"
                                            value="{{ old('primer_nombre', $persona->primer_nombre) }}" required>
                                        @error('primer_nombre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label for="segundo_nombre" class="form-label font-weight-bold">Segundo
                                            nombre</label>
                                        <input type="text" id="segundo_nombre" name="segundo_nombre"
                                            class="form-control @error('segundo_nombre') is-invalid @enderror"
                                            value="{{ old('segundo_nombre', $persona->segundo_nombre) }}">
                                        @error('segundo_nombre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label for="primer_apellido" class="form-label font-weight-bold">Primer
                                            apellido</label>
                                        <input type="text" id="primer_apellido" name="primer_apellido"
                                            class="form-control @error('primer_apellido') is-invalid @enderror"
                                            value="{{ old('primer_apellido', $persona->primer_apellido) }}" required>
                                        @error('primer_apellido')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label for="segundo_apellido" class="form-label font-weight-bold">Segundo
                                            apellido</label>
                                        <input type="text" id="segundo_apellido" name="segundo_apellido"
                                            class="form-control @error('segundo_apellido') is-invalid @enderror"
                                            value="{{ old('segundo_apellido', $persona->segundo_apellido) }}">
                                        @error('segundo_apellido')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label for="fecha_nacimiento" class="form-label font-weight-bold">Fecha de
                                            nacimiento</label>
                                        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required
                                            class="form-control @error('fecha_nacimiento') is-invalid @enderror"
                                            value="{{ old('fecha_nacimiento', $fechaNacimientoValor) }}" required>
                                        @error('fecha_nacimiento')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label for="genero" class="form-label font-weight-bold">Género</label>
                                        <select name="genero" id="genero"
                                            class="form-control @error('genero') is-invalid @enderror" required>
                                            <option value="" disabled
                                                {{ old('genero', $persona->genero) ? '' : 'selected' }}>
                                                Seleccione un género
                                            </option>
                                            @foreach ($generos->parametros as $genero)
                                                <option value="{{ $genero->id }}"
                                                    {{ old('genero', $persona->genero) == $genero->id ? 'selected' : '' }}>
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
                                            value="{{ old('telefono', $persona->telefono) }}">
                                        @error('telefono')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label for="celular" class="form-label font-weight-bold">Celular</label>
                                        <input type="text" id="celular" name="celular"
                                            class="form-control @error('celular') is-invalid @enderror"
                                            value="{{ old('celular', $persona->celular) }}">
                                        @error('celular')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-12 mb-0">
                                        <label for="email" class="form-label font-weight-bold">Correo
                                            electrónico</label>
                                        <input type="email" id="email" name="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            value="{{ old('email', $persona->email) }}" required>
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
                                        <select name="pais_id" id="pais_id"
                                            class="form-control @error('pais_id') is-invalid @enderror"
                                            data-initial-value="{{ $persona->pais_id }}">
                                            <option value="" disabled
                                                {{ old('pais_id', $persona->pais_id) ? '' : 'selected' }}>
                                                Seleccione un país
                                            </option>
                                            @foreach ($paises as $pais)
                                                <option value="{{ $pais->id }}"
                                                    {{ old('pais_id', $persona->pais_id) == $pais->id ? 'selected' : '' }}>
                                                    {{ $pais->pais }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('pais_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label for="departamento_id"
                                            class="form-label font-weight-bold">Departamento</label>
                                        <select name="departamento_id" id="departamento_id"
                                            class="form-control @error('departamento_id') is-invalid @enderror"
                                            data-initial-value="{{ $persona->departamento_id }}"
                                            data-url-template="{{ route('departamentos.by.pais', ['pais' => '__ID__']) }}">
                                            <option value="" disabled
                                                {{ old('departamento_id', $persona->departamento_id) ? '' : 'selected' }}>
                                                Seleccione un departamento
                                            </option>
                                            @foreach ($departamentos as $departamento)
                                                <option value="{{ $departamento->id }}"
                                                    {{ old('departamento_id', $persona->departamento_id) == $departamento->id ? 'selected' : '' }}>
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
                                            data-initial-value="{{ $persona->municipio_id }}"
                                            data-url-template="{{ route('municipios.by.departamento', ['departamento' => '__ID__']) }}">
                                            <option value="" disabled
                                                {{ old('municipio_id', $persona->municipio_id) ? '' : 'selected' }}>
                                                Seleccione un municipio
                                            </option>
                                            @foreach ($municipios as $municipio)
                                                <option value="{{ $municipio->id }}"
                                                    {{ old('municipio_id', $persona->municipio_id) == $municipio->id ? 'selected' : '' }}>
                                                    {{ $municipio->municipio }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('municipio_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-0">
                                        <label for="direccion" class="form-label font-weight-bold">Dirección</label>
                                        <input type="text" id="direccion" name="direccion"
                                            class="form-control @error('direccion') is-invalid @enderror"
                                            value="{{ old('direccion', $persona->direccion) }}">
                                        @error('direccion')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

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
                                        <span class="badge {{ $estadoBadgeClass }}">{{ $estadoLabel }}</span>
                                    </li>
                                    <li class="mb-3">
                                        <span class="text-muted text-uppercase small d-block">Registro en SOFIA</span>
                                        <span
                                            class="badge {{ $estadoSofiaBadgeClass }} text-white">{{ $estadoSofiaLabel }}</span>
                                    </li>
                                    <li class="mb-3">
                                        <span class="text-muted text-uppercase small d-block">Fecha de creación</span>
                                        <span>{{ $persona->created_at?->format('d/m/Y H:i') ?? 'Sin información' }}</span>
                                    </li>
                                    <li class="mb-0">
                                        <span class="text-muted text-uppercase small d-block">Última actualización</span>
                                        <span>{{ $persona->updated_at?->diffForHumans() ?? 'Sin información' }}</span>
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
                                    Verifica que el correo y el celular estén actualizados; se usan para notificaciones y
                                    acceso.
                                </p>
                                <p class="mb-0">
                                    Después de guardar puedes volver a la ficha para revisar los cambios aplicados.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mt-4">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Última modificación: {{ $persona->updated_at?->diffForHumans() ?? 'Sin información' }}
                        </div>
                        <div>
                            <a href="{{ route('personas.show', $persona->id) }}" class="btn btn-outline-secondary mr-2">
                                <i class="fas fa-times mr-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> Guardar cambios
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@section('footer')
    @include('layout.footer')
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/js/parametros.js'])
    @vite(['resources/js/pages/formularios-select-dinamico.js'])
@endsection
