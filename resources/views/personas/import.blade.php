@php
    use Illuminate\Support\Str;
@endphp
@extends('adminlte::page')

@section('content_header')
    <x-page-header icon="fa-file-excel" title="Importar Personas"
        subtitle="Carga masiva desde Excel con validación de duplicados" :breadcrumb="[
            ['label' => 'Inicio', 'url' => route('home.index'), 'icon' => 'fa-home'],
            ['label' => 'Personas', 'url' => route('personas.index'), 'icon' => 'fa-users'],
            ['label' => 'Importar', 'icon' => 'fa-file-excel', 'active' => true],
        ]" />
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-4 col-lg-5">
            <div class="card card-outline card-primary shadow-sm mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-upload me-2"></i> Cargar archivo
                    </h5>
                </div>
                <div class="card-body">
                    <form id="form-import-personas" enctype="multipart/form-data" novalidate>
                        @csrf
                        <div class="form-group mb-3">
                            <label for="archivo_excel" class="form-label">Selecciona el archivo</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="archivo_excel" name="archivo_excel"
                                    accept=".xlsx,.xls,.csv" required>
                                <label class="custom-file-label" for="archivo_excel">Elegir archivo...</label>
                            </div>
                            <small class="form-text text-muted">
                                Formatos permitidos: XLSX, XLS o CSV. El archivo debe contener mínimo los campos básicos
                                para crear la persona.
                            </small>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary" id="btn-iniciar-import">
                                <i class="fas fa-play"></i>
                                Iniciar importación
                            </button>
                            <a href="{{ asset('storage/plantillas/plantilla_personas.xlsx') }}"
                                class="btn btn-outline-secondary">
                                <i class="fas fa-download"></i>
                                Descargar plantilla
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="callout callout-info shadow-sm">
                <h5 class="mb-3 text-primary">
                    <i class="fas fa-lightbulb me-2"></i> Buenas prácticas
                </h5>
                <ul class="small ps-3 mb-0">
                    <li>La primera fila debe incluir los encabezados estándares.</li>
                    <li>Elimine filas en blanco y valide acentos o caracteres especiales.</li>
                    <li>Verifique que el número de documento, el correo y el celular no estén repetidos.</li>
                    <li>Los registros incompletos generarán una alerta para completar datos posteriormente.</li>
                </ul>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            <div class="card card-outline card-success shadow-sm mb-3 d-none" id="panel-progreso">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tasks me-2"></i> Progreso de la importación
                    </h5>
                    <span class="badge bg-secondary" id="estado-importacion">PENDIENTE</span>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold" id="contador-progreso">0 / 0</span>
                            <span class="text-muted" id="porcentaje-progreso">0%</span>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                                id="barra-progreso" style="width: 0%"></div>
                        </div>
                    </div>

                    <div class="row g-3 text-center">
                        <div class="col-md-4">
                            <div class="info-box bg-light shadow-none">
                                <span class="info-box-icon bg-success text-white"><i class="fas fa-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text text-muted text-uppercase small">Correctos</span>
                                    <span class="info-box-number h4 mb-0" id="contador-exitosos">0</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box bg-light shadow-none">
                                <span class="info-box-icon bg-warning text-white"><i class="fas fa-clone"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text text-muted text-uppercase small">Duplicados / errores</span>
                                    <span class="info-box-number h4 mb-0 text-warning" id="contador-duplicados">0</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box bg-light shadow-none">
                                <span class="info-box-icon bg-danger text-white"><i class="fas fa-phone-slash"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text text-muted text-uppercase small">Contactos incompletos</span>
                                    <span class="info-box-number h4 mb-0 text-danger" id="contador-faltantes">0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-warning shadow-sm mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i> Incidencias recientes
                    </h5>
                    <button class="btn btn-sm btn-outline-secondary" id="btn-recargar-incidencias" disabled>
                        <i class="fas fa-sync"></i> Actualizar
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 80px;">Fila</th>
                                    <th style="width: 120px;">Tipo</th>
                                    <th>Documento</th>
                                    <th>Correo</th>
                                    <th>Teléfono</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-incidencias">
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">Sin datos para mostrar.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-secondary shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i> Historial de importaciones
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Archivo</th>
                                    <th>Fecha</th>
                                    <th>Procesados</th>
                                    <th>Duplicados</th>
                                    <th>Estado</th>
                                    <th>Usuario</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($importaciones as $importacion)
                                    <tr>
                                        <td class="text-truncate" style="max-width: 240px;"
                                            title="{{ $importacion->original_name }}">
                                            <i class="far fa-file-excel text-success me-1"></i>
                                            {{ $importacion->original_name }}
                                        </td>
                                        <td>{{ $importacion->created_at?->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <span class="badge bg-success">
                                                {{ $importacion->success_count }}
                                            </span>
                                            <span class="text-muted">de {{ $importacion->total_rows }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning text-dark">
                                                {{ $importacion->duplicate_count }}
                                            </span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $importacion->status === 'completed' ? 'success' : ($importacion->status === 'failed' ? 'danger' : 'secondary') }}">
                                                {{ Str::upper($importacion->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted small">
                                                {{ $importacion->user?->name ?? 'Usuario desconocido' }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <form action="{{ route('personas.import.destroy', $importacion) }}"
                                                method="POST" class="d-inline form-eliminar-import">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm"
                                                    title="Eliminar importación">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-3">Aún no hay importaciones
                                            registradas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="url-import-store" value="{{ route('personas.import.store') }}">
    <input type="hidden" id="url-import-status"
        value="{{ route('personas.import.status', ['personaImport' => '__ID__']) }}">
@endsection

@section('footer')
    @include('layout.footer')
@endsection

@section('js')
    @vite(['resources/js/pages/personas-import.js'])
@endsection
