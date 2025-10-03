@extends('adminlte::page')

@section('title', 'Gestión de Aspirantes')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1>Gestión de Aspirantes</h1>
            <p class="text-muted">Administre los aspirantes a programas de formación complementaria</p>
        </div>
        <button class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Aspirante
        </button>
    </div>
@stop

@section('content')
    <style>
        .course-card {
            background-color: white;
            border: 1px solid #e9ecef;
            border-radius: 0.5rem;
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 1.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .course-card .card-icon {
            font-size: 2rem;
            color: #6c757d;
            margin-bottom: 1rem;
        }
        .course-card .card-title {
            font-size: 1.25rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        .course-card .card-status {
            display: inline-block;
            padding: 0.25em 0.6em;
            font-size: 75%;
            font
        .badge-aceptado {
            background-color: #198754 !important;
            color: white !important;
        }
        
        .badge-rechazado {
            background-color: #dc3545 !important;
            color: white !important;
        }
        
        .action-btn {
            width: 32px;
            height: 32px;
            padding: 0;
            border-radius: 4px;
            border: none;
            margin-right: 0.25rem;
        }
        
        .action-btn-view {
            background-color: #17a2b8;
        }
        
        .action-btn-edit {
            background-color: #ffc107;
        }
        
        .action-btn-delete {
            background-color: #dc3545;
        }
        
        .action-btn:hover {
            opacity: 0.8;
        }
        
        .table th {
            font-weight: 500;
            color: #495057;
            border-bottom: 1px solid #dee2e6;
        }
        
        .table td {
            vertical-align: middle;
        }
        
        .search-input {
            position: relative;
        }
        
        .search-input .fa-search {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 10;
        }
        
        .search-input input {
            padding-left: 2.5rem;
        }
        
        .pagination .page-link {
            color: #6c757d;
            border: 1px solid #dee2e6;
        }
        
        .pagination .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
    </style>

    <!-- Main Content -->
    <div class="container-fluid px-4 py-4">
        <div class="row">
            <!-- Ejemplo de tarjeta -->
            <div class="col-md-4 mb-4">
                <div class="course-card">
                    <div class="card-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div class="card-title">Auxiliar de Cocina</div>
                    <span class="badge badge-aceptado mb-2">Activo</span>
                    <p>Fundamentos de cocina, manipulación de alimentos y técnicas básicas de preparación.</p>
                    <p><strong>Aspirantes:</strong> 15</p>
                    <a href="{{ route('aspirantes.ver', ['curso' => 'auxiliar-cocina']) }}" class="btn btn-primary w-100">
                        <i class="fas fa-users"></i> Ver Aspirantes
                    </a>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="course-card">
                    <div class="card-icon">
                        <i class="fas fa-hammer"></i>
                    </div>
                    <div class="card-title">Acabados en Madera</div>
                    <span class="badge badge-aceptado mb-2">Activo</span>
                    <p>Técnicas de acabado, barnizado y restauración de muebles de madera.</p>
                    <p><strong>Aspirantes:</strong> 8</p>
                    <a href="{{ route('aspirantes.ver', ['curso' => 'acabados-madera']) }}" class="btn btn-primary w-100">
                        <i class="fas fa-users"></i> Ver Aspirantes
                    </a>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="course-card">
                    <div class="card-icon">
                        <i class="fas fa-cut"></i>
                    </div>
                    <div class="card-title">Confección de Prendas</div>
                    <span class="badge badge-aceptado mb-2">Activo</span>
                    <p>Técnicas básicas de corte, confección y terminado de prendas de vestir.</p>
                    <p><strong>Aspirantes:</strong> 12</p>
                    <a href="{{ route('aspirantes.ver', ['curso' => 'confeccion-prendas']) }}" class="btn btn-primary w-100">
                        <i class="fas fa-users"></i> Ver Aspirantes
                    </a>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="course-card">
                    <div class="card-icon">
                        <i class="fas fa-car"></i>
                    </div>
                    <div class="card-title">Mecánica Básica Automotriz</div>
                    <span class="badge badge-aceptado mb-2">Activo</span>
                    <p>Mantenimiento preventivo y diagnóstico básico de vehículos.</p>
                    <p><strong>Aspirantes:</strong> 20</p>
                    <a href="{{ route('aspirantes.ver', ['curso' => 'mecanica-basica-automotriz']) }}" class="btn btn-primary w-100">
                        <i class="fas fa-users"></i> Ver Aspirantes
                    </a>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="course-card">
                    <div class="card-icon">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <div class="card-title">Cultivo de Huertas Urbanas</div>
                    <span class="badge badge-aceptado mb-2">Activo</span>
                    <p>Técnicas de cultivo y mantenimiento de huertas en espacios urbanos.</p>
                    <p><strong>Aspirantes:</strong> 10</p>
                    <a href="{{ route('aspirantes.ver', ['curso' => 'cultivo-huertas-urbanas']) }}" class="btn btn-primary w-100">
                        <i class="fas fa-users"></i> Ver Aspirantes
                    </a>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="course-card">
                    <div class="card-icon">
                        <i class="fas fa-gavel"></i>
                    </div>
                    <div class="card-title">Normatividad Laboral</div>
                    <span class="badge badge-aceptado mb-2">Activo</span>
                    <p>Actualización en normatividad laboral y seguridad social.</p>
                    <p><strong>Aspirantes:</strong> 5</p>
                    <a href="{{ route('aspirantes.ver', ['curso' => 'normatividad-laboral']) }}" class="btn btn-primary w-100">
                        <i class="fas fa-users"></i> Ver Aspirantes
                    </a>
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