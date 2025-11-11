@extends('adminlte::page')

@section('css')
    @vite(['resources/css/competencias.css'])
    <style>
        .form-section { margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid #e3e6f0; }
        .form-section:last-child { border-bottom: none; }
        .form-section-title { color: #4e73df; font-size: 1.1rem; margin-bottom: 1rem; font-weight: 600; }
        .dashboard-header {
            background: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, .05);
            box-shadow: 0 2px 4px rgba(0, 0, 0, .03);
        }
        .text-gray-800 {
            color: #5a5c69 !important;
        }
        .link_right_header {
            color: #4a5568;
            text-decoration: none;
        }
        .link_right_header:hover {
            color: #4299e1;
        }
        .competencias-scroll {
            max-height: 260px;
            overflow-y: auto;
        }
    </style>
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-clipboard-list" 
        title="Competencias"
        subtitle="Gestión de competencias del SENA"
        :breadcrumb="[['label' => 'Competencias', 'url' => route('competencias.index') , 'icon' => 'fa-clipboard-list'], ['label' => 'Editar', 'icon' => 'fa-edit', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('competencias.index') }}">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>

                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-edit mr-2"></i>Editar Competencia
                            </h5>
                        </div>
                        <div class="card-body">
                            @php
                                $programasAsociados = $competencia->programasFormacion ?? collect();
                            @endphp

                            <form method="POST" action="{{ route('competencias.update', $competencia) }}">
                                @csrf
                                @method('PUT')

                                {{-- Sección: Información de la competencia --}}
                                <div class="form-section">
                                    <div class="form-section-title">
                                        <i class="fas fa-info-circle mr-1"></i> Información de la Competencia
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="descripcion" class="form-label font-weight-bold">
                                            Norma / Unidad de competencia <span class="text-danger">*</span>
                                        </label>
                                        <textarea
                                            name="descripcion"
                                            id="descripcion"
                                            rows="3"
                                            class="form-control @error('descripcion') is-invalid @enderror"
                                            placeholder="Describa la norma o unidad de competencia"
                                            required
                                        >{{ old('descripcion', $competencia->descripcion) }}</textarea>
                                        @error('descripcion')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Máximo 1000 caracteres.</small>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="codigo" class="form-label font-weight-bold">
                                                    Código de norma de competencia laboral <span class="text-danger">*</span>
                                                </label>
                                                <input
                                                    type="text"
                                                    name="codigo"
                                                    id="codigo"
                                                    class="form-control @error('codigo') is-invalid @enderror"
                                                    value="{{ old('codigo', $competencia->codigo) }}"
                                                    maxlength="50"
                                                    required
                                                >
                                                @error('codigo')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="duracion" class="form-label font-weight-bold">
                                                    Duración máxima de la competencia (horas) <span class="text-danger">*</span>
                                                </label>
                                                <input
                                                    type="number"
                                                    name="duracion"
                                                    id="duracion"
                                                    class="form-control @error('duracion') is-invalid @enderror"
                                                    value="{{ old('duracion', $competencia->duracion) }}"
                                                    min="1"
                                                    required
                                                >
                                                @error('duracion')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="nombre" class="form-label font-weight-bold">Nombre de la competencia <span class="text-danger">*</span></label>
                                        <input
                                            type="text"
                                            name="nombre"
                                            id="nombre"
                                            class="form-control @error('nombre') is-invalid @enderror"
                                            value="{{ old('nombre', $competencia->nombre) }}"
                                            maxlength="255"
                                            required
                                        >
                                        @error('nombre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Sección: Estado --}}
                                <div class="form-section">
                                    <div class="form-section-title">
                                        <i class="fas fa-toggle-on mr-1"></i> Estado
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="status" class="form-label font-weight-bold">Estado</label>
                                                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                                                    <option value="1" {{ old('status', $competencia->status) == '1' ? 'selected' : '' }}>Activa</option>
                                                    <option value="0" {{ old('status', $competencia->status) == '0' ? 'selected' : '' }}>Inactiva</option>
                                                </select>
                                                @error('status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Sección: Información de Auditoría --}}
                                <div class="form-section">
                                    <div class="form-section-title">
                                        <i class="fas fa-history mr-1"></i> Información de Auditoría
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Creado por:</strong> {{ $competencia->userCreate->name ?? 'N/A' }}</p>
                                            <p class="mb-1"><strong>Fecha de creación:</strong> {{ $competencia->created_at ? $competencia->created_at->format('d/m/Y H:i') : 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>RAPs asociados:</strong> <span class="badge badge-primary">{{ $competencia->resultadosAprendizaje->count() }}</span></p>
                                            <p class="mb-1"><strong>Última edición:</strong> {{ $competencia->updated_at ? $competencia->updated_at->format('d/m/Y H:i') : 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Botones de Acción --}}
                                <hr class="mt-4">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('competencias.index') }}" class="btn btn-light mr-2">
                                        Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-1"></i>Actualizar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card shadow-sm no-hover mt-4">
                        <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-graduation-cap mr-1"></i> Programas de formación asociados
                            </h6>
                            <span class="badge badge-primary badge-pill">
                                {{ $programasAsociados->count() }} programa(s)
                            </span>
                        </div>
                        <div class="card-body">
                            @if($programasAsociados->isEmpty())
                                <p class="text-muted mb-0">Esta competencia no tiene programas asociados actualmente.</p>
                            @else
                                <div class="table-responsive competencias-scroll">
                                    <table class="table table-sm table-striped mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="width: 18%;">Código</th>
                                                <th>Programa de formación</th>
                                                <th style="width: 15%;" class="text-center">Estado</th>
                                                <th style="width: 15%;" class="text-right">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($programasAsociados as $programa)
                                                @php
                                                    $mensajeConfirmacion = "¿Quitar la competencia del programa '{$programa->nombre}'?";
                                                    $rutaDesasociar = route('programa.competencia.detach', [$programa->id, $competencia->id]);
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <span class="badge badge-primary">{{ $programa->codigo }}</span>
                                                    </td>
                                                    <td>{{ $programa->nombre }}</td>
                                                    <td class="text-center">
                                                        <span class="badge badge-{{ $programa->status ? 'success' : 'secondary' }}">
                                                            {{ $programa->status ? 'Activo' : 'Inactivo' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-right">
                                                        <form
                                                            method="POST"
                                                            action="{{ $rutaDesasociar }}"
                                                            class="d-inline"
                                                            onsubmit="return confirmarQuitar('{{ $mensajeConfirmacion }}');"
                                                        >
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                                <i class="fas fa-times mr-1"></i>Quitar
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <small class="text-muted d-block mt-3">
                                    Para asociar esta competencia a nuevos programas utilice el proceso de creación o gestione el vínculo desde el módulo de programas.
                                </small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footer')
    @include('layout.footer')
@endsection

@section('js')
    @vite(['resources/js/pages/competencias-form.js'])
    <script>
        if (typeof window.confirmarQuitar !== 'function') {
            window.confirmarQuitar = function (mensaje) {
                return window.confirm(mensaje);
            };
        }
    </script>
@endsection
