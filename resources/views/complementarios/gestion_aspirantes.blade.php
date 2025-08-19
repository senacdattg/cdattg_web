@extends('adminlte::page')

@section('title', 'Gestión de Aspirantes')

@section('content_header')
    <h1>Gestión de Aspirantes</h1>
@stop

@section('content')
    <style>
        .header-section {
            background-color: white;
            border-bottom: 1px solid #e9ecef;
            padding: 1.5rem 0;
        }
        
        .status-tabs {
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 1.5rem;
        }
        
        .status-tab {
            background: none;
            border: none;
            padding: 0.75rem 1rem;
            color: #6c757d;
            text-decoration: none;
            border-bottom: 2px solid transparent;
            font-weight: 500;
        }
        
        .status-tab.active {
            color: #0d6efd;
            border-bottom-color: #0d6efd;
        }
        
        .status-tab:hover {
            color: #495057;
        }
        
        .badge-en-proceso {
            background-color: #ffc107 !important;
            color: white !important;
        }
        
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

    <!-- Header -->
    <div class="header-section">
        <div class="container-fluid px-4">
            <div class="row align-items-center">
                <div class="col">
                    <div class="d-flex align-items-center">
                        
                        <div>
                            <p class="text-muted mb-0">Administre los aspirantes a programas de formación complementaria</p>
                        </div>
                    </div>
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Nuevo Aspirante
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-fluid px-4 py-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <!-- Search and Filters -->
                <div class="row mb-4">
                    <div class="col-lg-6 mb-3 mb-lg-0">
                        <div class="search-input">
                            <i class="fas fa-search"></i>
                            <input type="text" class="form-control" placeholder="Buscar por nombre o número de identidad">
                        </div>
                    </div>
                    <div class="col-lg-3 mb-3 mb-lg-0">
                        <select class="form-select">
                            <option selected>Todos los programas</option>
                            <option>Desarrollo de Software</option>
                            <option>Análisis de Datos</option>
                            <option>Diseño Gráfico</option>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <select class="form-select">
                            <option selected>Todos los años</option>
                            <option>2025</option>
                            <option>2024</option>
                        </select>
                    </div>
                </div>

                <!-- Status Tabs -->
                <div class="status-tabs">
                    <button class="status-tab active" data-status="todos">Todos</button>
                    <button class="status-tab" data-status="en-proceso">En Proceso</button>
                    <button class="status-tab" data-status="aceptados">Aceptados</button>
                    <button class="status-tab" data-status="rechazados">Rechazados</button>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre Completo</th>
                                <th>Número de Identidad</th>
                                <th>Programa de Formación</th>
                                <th>Fecha Solicitud</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Carlos Andrés Martínez</td>
                                <td>1098765432</td>
                                <td>Desarrollo de Software</td>
                                <td>15/03/2025</td>
                                <td><span class="badge badge-en-proceso">EN PROCESO</span></td>
                                <td>
                                    <button class="action-btn action-btn-view" title="Ver">
                                        <i class="fas fa-eye text-white"></i>
                                    </button>
                                    <button class="action-btn action-btn-edit" title="Editar">
                                        <i class="fas fa-edit text-white"></i>
                                    </button>
                                    <button class="action-btn action-btn-delete" title="Eliminar">
                                        <i class="fas fa-trash text-white"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Ana María López</td>
                                <td>1087654321</td>
                                <td>Análisis de Datos</td>
                                <td>10/02/2025</td>
                                <td><span class="badge badge-aceptado">ACEPTADO</span></td>
                                <td>
                                    <button class="action-btn action-btn-view" title="Ver">
                                        <i class="fas fa-eye text-white"></i>
                                    </button>
                                    <button class="action-btn action-btn-edit" title="Editar">
                                        <i class="fas fa-edit text-white"></i>
                                    </button>
                                    <button class="action-btn action-btn-delete" title="Eliminar">
                                        <i class="fas fa-trash text-white"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Juan Pablo Rodríguez</td>
                                <td>1076543210</td>
                                <td>Diseño Gráfico</td>
                                <td>05/04/2025</td>
                                <td><span class="badge badge-rechazado">RECHAZADO</span></td>
                                <td>
                                    <button class="action-btn action-btn-view" title="Ver">
                                        <i class="fas fa-eye text-white"></i>
                                    </button>
                                    <button class="action-btn action-btn-edit" title="Editar">
                                        <i class="fas fa-edit text-white"></i>
                                    </button>
                                    <button class="action-btn action-btn-delete" title="Eliminar">
                                        <i class="fas fa-trash text-white"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>Luisa Fernanda Gómez</td>
                                <td>1065432109</td>
                                <td>Gestión de Redes</td>
                                <td>20/01/2025</td>
                                <td><span class="badge badge-aceptado">ACEPTADO</span></td>
                                <td>
                                    <button class="action-btn action-btn-view" title="Ver">
                                        <i class="fas fa-eye text-white"></i>
                                    </button>
                                    <button class="action-btn action-btn-edit" title="Editar">
                                        <i class="fas fa-edit text-white"></i>
                                    </button>
                                    <button class="action-btn action-btn-delete" title="Eliminar">
                                        <i class="fas fa-trash text-white"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td>Pedro José Ramírez</td>
                                <td>1054321098</td>
                                <td>Administración de Empresas</td>
                                <td>12/03/2025</td>
                                <td><span class="badge badge-en-proceso">EN PROCESO</span></td>
                                <td>
                                    <button class="action-btn action-btn-view" title="Ver">
                                        <i class="fas fa-eye text-white"></i>
                                    </button>
                                    <button class="action-btn action-btn-edit" title="Editar">
                                        <i class="fas fa-edit text-white"></i>
                                    </button>
                                    <button class="action-btn action-btn-delete" title="Eliminar">
                                        <i class="fas fa-trash text-white"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav aria-label="Paginación" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item">
                            <a class="page-link" href="#" aria-label="Anterior">Anterior</a>
                        </li>
                        <li class="page-item active">
                            <a class="page-link" href="#">1</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">2</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">3</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#" aria-label="Siguiente">Siguiente</a>
                        </li>
                    </ul>
                </nav>
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
