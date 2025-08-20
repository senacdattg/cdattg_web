@extends('adminlte::page')

@section('title', 'Gestión de Aspirantes')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1>Procesamiento de Documentos</h1>
            <p class="text-muted">Validar los documentos</p>
        </div>
    </div>
@stop
@section('css')
    {{-- Usando Vite --}}
    @vite('resources/css/complementario/procesar_documentos.css')
@stop

@section('content')
     <!-- Main Content -->
    <div class="main-content">

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number">1,245</div>
                    <div class="stat-title">Total Aspirantes</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="stat-number">875</div>
                    <div class="stat-title">Aspirantes Aceptados</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-icon">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <div class="stat-number">370</div>
                    <div class="stat-title">Aspirantes Pendientes</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="stat-number">15</div>
                    <div class="stat-title">Programas Activos</div>
                </div>
            </div>
        </div>
        <div class="content-header">
            <h2>Procesamiento de Documentos</h2>
            <p>Suba los documentos para extraer información mediante OCR</p>
        </div>
        
        
        
        <!-- Upload Section -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Subir Documentos</h5>
                <div class="upload-area" id="uploadArea">
                    <div class="upload-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <h5>Arrastre y suelte los documentos aquí</h5>
                    <p>o</p>
                    <button class="btn btn-primary">Seleccionar Archivos</button>
                    <input type="file" id="fileInput" style="display:none;" multiple>
                    <p class="mt-2 text-muted">Formatos aceptados: PDF, JPG, PNG, TIFF, ZIP</p>
                </div>
            </div>
        </div>
        
        <!-- Processing and Validation Section -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title">Procesamiento y Validación</h5>
                    <button class="btn btn-outline-secondary">
                        <i class="fas fa-history"></i> Historial de Cambios
                    </button>
                </div>
                
                <div class="row">
                    <!-- OCR Extraction -->
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2">
                                    <i class="fas fa-file-alt validation-icon text-primary"></i>
                                    Extracción OCR
                                </h6>
                                <p class="card-text">Extracción automática de información de documentos de identidad.</p>
                                <button class="btn btn-sm btn-primary">Iniciar Extracción</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Data Validation -->
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2">
                                    <i class="fas fa-check-circle validation-icon text-success"></i>
                                    Validación de Datos
                                </h6>
                                <p class="card-text">Compare los datos extraídos con la imagen original.</p>
                                <button class="btn btn-sm btn-primary">Validar Información</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Data Storage -->
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2">
                                    <i class="fas fa-database validation-icon text-info"></i>
                                    Almacenamiento
                                </h6>
                                <p class="card-text">Guardar información procesada en la base de datos.</p>
                                <button class="btn btn-sm btn-primary">Guardar Datos</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Validation Section -->
                <div class="validation-section">
                    <h6>Validación de Procesamiento</h6>
                    <div class="card">
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="fas fa-sync-alt me-2 text-primary"></i>
                                    Comparar datos extraídos con la imagen original
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="fas fa-check-double me-2 text-success"></i>
                                    Confirmar validez de la información procesada
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="fas fa-clipboard me-2 text-warning"></i>
                                    Registrar observaciones sobre los datos extraídos
                                </li>
                            </ul>
                            <div class="mt-3">
                                <button class="btn btn-primary">Confirmar Procesamiento</button>
                                <button class="btn btn-outline-secondary ms-2">Cancelar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stop


@section('js')
    <script>
        console.log('Módulo de Procesamiento de documentos');
    </script>
@stop
