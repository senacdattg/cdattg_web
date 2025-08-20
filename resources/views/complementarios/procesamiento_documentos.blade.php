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

@section('content')
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        .main-content {
            margin-left: 20%;
            width: 75%;
            background-color: #e7e7eb;
            min-height: 100vh;
            padding: 20px;
        }
        .content-header {
            margin-bottom: 20px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .upload-area {
            border: 2px dashed #094577;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            background-color: #f8f9fa;
            cursor: pointer;
            transition: all 0.3s;
        }
        .upload-area:hover {
            background-color: #e2e6ea;
        }
        .upload-icon {
            font-size: 50px;
            color: #094577;
            margin-bottom: 15px;
        }
        .btn-primary {
            background-color: #094577;
            border-color: #094577;
        }
        .btn-primary:hover {
            background-color: #073a62;
            border-color: #073a62;
        }
        .validation-section {
            margin-top: 20px;
        }
        .validation-icon {
            font-size: 24px;
            margin-right: 10px;
        }
        .stat-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            font-size: 30px;
            color: #094577;
            margin-bottom: 10px;
        }
        .stat-number {
            font-size: 28px;
            font-weight: bold;
            color: #2f2f30;
        }
        .stat-title {
            color: #6c757d;
            font-size: 14px;
        }
    </style>
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
    <script>
        // Status tab functionality
        document.querySelectorAll('.status-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs
                document.querySelectorAll('.status-tab').forEach(t => t.classList.remove('active'));
                // Add active class to clicked tab
                this.classList.add('active');
                
                // Here you would typically filter the table based on the selected status
                const status = this.getAttribute('data-status');
                console.log('Selected status:', status);
            });
        });

        // Search functionality
        document.querySelector('.search-input input').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            console.log('Search term:', searchTerm);
            // Here you would implement the search logic
        });

        // Action button functionality
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const action = this.classList.contains('action-btn-view') ? 'view' : 
                              this.classList.contains('action-btn-edit') ? 'edit' : 'delete';
                const row = this.closest('tr');
                const id = row.cells[0].textContent;
                console.log(`${action} action for ID: ${id}`);
            });
        });
    </script>
@stop

@section('css')
@stop

@section('js')
    <script>
        console.log('Módulo de gestión de aspirantes cargado');
    </script>
@stop
