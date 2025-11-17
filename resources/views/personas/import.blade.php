@php
    use Illuminate\Support\Str;
@endphp
@extends('adminlte::page')

@section('plugins.Sweetalert2', true)

@section('css')
    @vite(['resources/css/parametros.css'])
    <style>
        @media (max-width: 575.98px) {
            #select-importacion-activa {
                width: 100%;
            }
        }
    </style>
@endsection

@section('content_header')
    <x-page-header icon="fa-file-excel" title="Importar Personas"
        subtitle="Carga masiva desde Excel con validación de duplicados" :breadcrumb="[
            ['label' => 'Inicio', 'url' => route('home.index'), 'icon' => 'fa-home'],
            ['label' => 'Personas', 'url' => route('personas.index'), 'icon' => 'fa-users'],
            ['label' => 'Importar', 'icon' => 'fa-file-excel', 'active' => true],
        ]" />
@endsection

@section('content')
    <div class="row gy-4">
        <div class="col-12 col-xl-4 col-lg-5">
            <div class="card card-outline card-primary shadow-sm mb-4 mb-xl-3">
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
                            <div class="input-group">
                                <button type="button" class="btn btn-outline-secondary" id="btn-examinar">
                                    <i class="fas fa-folder-open me-1"></i> Examinar
                                </button>
                                <input type="file" class="d-none" id="archivo_excel" name="archivo_excel"
                                    accept=".xlsx,.xls" required>
                                <input type="text" class="form-control" id="archivo_excel_nombre" value="Ningún archivo"
                                    readonly>
                            </div>
                            <small class="form-text text-muted">
                                Formatos permitidos: XLSX o XLS. El archivo debe contener mínimo los campos básicos
                                para crear la persona.
                            </small>
                        </div>

                        <div class="row row-cols-1 row-cols-sm-2 g-2">
                            <div class="col">
                                <button type="submit" class="btn btn-primary w-100" id="btn-iniciar-import">
                                    <i class="fas fa-play"></i>
                                    Iniciar importación
                                </button>
                            </div>
                            <div class="col">
                                <a href="{{ asset('storage/plantillas/plantilla_personas.xlsx') }}"
                                    class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-download"></i>
                                    Descargar plantilla
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="callout callout-info shadow-sm mb-4">
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

        <div class="col-12 col-xl-8 col-lg-7">
            <div class="card card-outline card-success shadow-sm mb-3 d-none" id="panel-progreso">
                @php
                    $panelHeaderClasses =
                        'card-header d-flex flex-column flex-md-row justify-content-md-between ' .
                        'align-items-md-center gap-3';
                @endphp
                <div class="{{ $panelHeaderClasses }}">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tasks me-2"></i> Progreso de la importación
                    </h5>
                    <span class="badge bg-secondary" id="estado-importacion">PENDIENTE</span>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="row g-2 align-items-center">
                            <div class="col-12 col-md-auto">
                                <span class="fw-bold" id="contador-progreso">0 / 0</span>
                            </div>
                            <div class="col">
                                <div class="progress" style="height: 0.75rem;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                                        id="barra-progreso" style="width: 0%"></div>
                                </div>
                            </div>
                            <div class="col-12 col-md-auto text-md-right">
                                <span class="text-muted d-inline-block" id="porcentaje-progreso">0%</span>
                            </div>
                        </div>
                    </div>

                    <div class="row row-cols-1 row-cols-sm-3 g-3 text-center">
                        <div class="col">
                            <div class="info-box bg-light shadow-none h-100">
                                <span class="info-box-icon bg-success text-white"><i class="fas fa-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text text-muted text-uppercase small">Correctos</span>
                                    <span class="info-box-number h4 mb-0" id="contador-exitosos">0</span>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="info-box bg-light shadow-none h-100">
                                <span class="info-box-icon bg-warning text-white"><i class="fas fa-clone"></i></span>
                                <div class="info-box-content">
                                    <span
                                        class="info-box-text
                                        text-muted text-uppercase
                                        small">Duplicados
                                        / errores</span>
                                    <span class="info-box-number h4 mb-0 text-warning" id="contador-duplicados">0</span>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="info-box bg-light shadow-none h-100">
                                <span class="info-box-icon bg-danger
                                    text-white"><i
                                        class="fas fa-phone-slash"></i></span>
                                <div class="info-box-content">
                                    <span
                                        class="info-box-text text-muted
                                        text-uppercase small">Contactos
                                        incompletos</span>
                                    <span class="info-box-number h4 mb-0 text-danger" id="contador-faltantes">0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @php
                $claseIncidenciasHeader =
                    'card-header d-flex flex-column flex-lg-row gap-3 gap-lg-0 ' .
                    'justify-content-between align-items-lg-center';
            @endphp
            <div class="card card-outline card-warning shadow-sm mb-3">
                <div class="{{ $claseIncidenciasHeader }}">
                    <div class="w-100">
                        <div class="d-flex flex-column flex-lg-row align-items-lg-center gap-3">
                            <h5 class="card-title mb-0 flex-grow-1">
                                <i class="fas fa-exclamation-triangle me-2"></i> Incidencias recientes
                            </h5>
                            @php
                                $importacionesActivas = $importaciones->whereIn('status', ['pending', 'processing']);
                            @endphp
                            <div class="d-flex flex-column flex-sm-row gap-2 w-100 w-lg-auto">
                                <select class="form-select form-select-sm" id="select-importacion-activa"
                                    @if ($importacionesActivas->isEmpty()) disabled @endif>
                                    <option value="" selected>Selecciona importación</option>
                                    @foreach ($importacionesActivas as $activa)
                                        <option value="{{ $activa->id }}">
                                            #{{ $activa->id }} · {{ Str::limit($activa->original_name, 38) }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button class="btn btn-outline-danger" id="btn-detener-import" disabled
                                        title="Detener y borrar">
                                        <i class="fas fa-stop-circle"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" id="btn-recargar-incidencias" disabled
                                        title="Actualizar">
                                        <i class="fas fa-sync"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0"
                            aria-describedby="historial-importaciones-caption">
                            <caption id="historial-importaciones-caption" class="sr-only">
                                Historial de importaciones realizadas con detalle de archivo, fecha y estado
                            </caption>
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
                                            @php
                                                $colorEstado = match ($importacion->status) {
                                                    'completed' => 'success',
                                                    'failed' => 'danger',
                                                    default => 'secondary',
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $colorEstado }}">
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
    <input type="hidden" id="url-import-destroy"
        value="{{ route('personas.import.destroy', ['personaImport' => '__ID__']) }}">
@endsection

@section('footer')
    @include('layouts.footer')
@endsection

@section('js')
    @vite(['resources/js/pages/personas-import.js'])
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const inputFile = document.getElementById('archivo_excel');
            const inputNombre = document.getElementById('archivo_excel_nombre');
            const btnExaminar = document.getElementById('btn-examinar');

            btnExaminar?.addEventListener('click', () => {
                inputFile?.click();
            });

            inputFile?.addEventListener('change', (event) => {
                const fileName = event.target.files?.[0]?.name ?? 'Ningún archivo';
                if (inputNombre) {
                    inputNombre.value = fileName;
                }
            });
        });
    </script>
@endsection
