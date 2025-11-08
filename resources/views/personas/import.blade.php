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
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-upload me-2"></i>Subir archivo Excel
                    </h5>
                </div>
                <div class="card-body">
                    <form id="form-import-personas" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="archivo_excel" class="form-label">Archivo (.xlsx / .xls / .csv)</label>
                            <input type="file" class="form-control" id="archivo_excel" name="archivo_excel"
                                accept=".xlsx,.xls,.csv" required>
                            <div class="form-text">Estructura requerida: Tipo de Documento, Número de Documento,
                                Primer/Segundo Nombre, Primer/Segundo Apellido, Correo Electrónico, Celular.</div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" id="btn-iniciar-import">
                            <i class="fas fa-play"></i>
                            Iniciar importación
                        </button>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted">Recomendaciones</h6>
                    <ul class="small ps-3 mb-0">
                        <li>Asegúrese de que la primera fila contenga los encabezados requeridos.</li>
                        <li>Elimine filas completamente vacías antes de cargar el archivo.</li>
                        <li>Verifique que los números de documento, correos y teléfonos no estén duplicados.</li>
                        <li>Los registros sin correo o teléfono se almacenarán y se generará una alerta para completar la
                            información.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm" id="panel-progreso" style="display: none;">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-tasks me-2"></i>
                        Progreso de importación
                    </h5>
                    <span class="badge bg-light text-success" id="estado-importacion">Pendiente</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span id="contador-progreso">0 / 0</span>
                            <span id="porcentaje-progreso">0%</span>
                        </div>
                        <div class="progress" style="height: 18px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                                id="barra-progreso" style="width: 0%"></div>
                        </div>
                    </div>

                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="fw-bold" id="contador-exitosos">0</div>
                            <div class="text-muted small">Procesados correctamente</div>
                        </div>
                        <div class="col-md-4">
                            <div class="fw-bold text-warning" id="contador-duplicados">0</div>
                            <div class="text-muted small">Duplicados / errores</div>
                        </div>
                        <div class="col-md-4">
                            <div class="fw-bold text-danger" id="contador-faltantes">0</div>
                            <div class="text-muted small">Contactos incompletos</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2 text-warning"></i>Últimas incidencias</h5>
                    <button class="btn btn-outline-secondary btn-sm" id="btn-recargar-incidencias" disabled>
                        <i class="fas fa-sync"></i> Actualizar
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Fila</th>
                                    <th>Tipo</th>
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

            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Historial reciente</h5>
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
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($importaciones as $importacion)
                                    <tr>
                                        <td class="text-truncate" style="max-width: 220px;"
                                            title="{{ $importacion->original_name }}">{{ $importacion->original_name }}</td>
                                        <td>{{ $importacion->created_at?->format('d/m/Y H:i') }}</td>
                                        <td>{{ $importacion->success_count }} / {{ $importacion->total_rows }}</td>
                                        <td>{{ $importacion->duplicate_count }}</td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $importacion->status === 'completed' ? 'success' : ($importacion->status === 'failed' ? 'danger' : 'secondary') }}">
                                                {{ Str::upper($importacion->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">Aún no hay importaciones
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

@section('js')
    @vite(['resources/js/pages/personas-import.js'])
@endsection
