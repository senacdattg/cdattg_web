@extends('adminlte::page')

@section('title', 'Gestión de Aspirantes')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h1 class="mb-0"><i class="fas fa-users me-2"></i>Gestión de Aspirantes</h1>
            <p class="text-muted mb-0">Administre los aspirantes a programas de formación complementaria</p>
        </div>
        <a href="#" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Aspirante
        </a>
    </div>
@stop

@section('content')
    <div class="card shadow-sm">
        <div class="card-body pb-2">
            <form class="row g-3 align-items-end">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" placeholder="Buscar por nombre o número de identidad">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select">
                        <option selected>Todos los programas</option>
                        <option>Desarrollo de Software</option>
                        <option>Análisis de Datos</option>
                        <option>Diseño Gráfico</option>
                        <option>Gestión de Redes</option>
                        <option>Administración de Empresas</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select">
                        <option selected>Todos los años</option>
                        <option>2025</option>
                        <option>2024</option>
                    </select>
                </div>
            </form>
            <div class="mt-3">
                <button class="btn btn-outline-secondary btn-sm me-2">Todos</button>
                <button class="btn btn-outline-warning btn-sm me-2">En Proceso</button>
                <button class="btn btn-outline-success btn-sm me-2">Aceptados</button>
                <button class="btn btn-outline-danger btn-sm">Rechazados</button>
            </div>
        </div>
    </div>

    <div class="card mt-3 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
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
                            <td><span class="badge bg-warning text-dark">EN PROCESO</span></td>
                            <td>
                                <a href="#" class="btn btn-info btn-sm me-1" title="Ver"><i class="fas fa-eye"></i></a>
                                <a href="#" class="btn btn-warning btn-sm me-1" title="Editar"><i class="fas fa-edit"></i></a>
                                <a href="#" class="btn btn-danger btn-sm" title="Eliminar"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Ana María López</td>
                            <td>1087654321</td>
                            <td>Análisis de Datos</td>
                            <td>10/02/2025</td>
                            <td><span class="badge bg-success">ACEPTADO</span></td>
                            <td>
                                <a href="#" class="btn btn-info btn-sm me-1" title="Ver"><i class="fas fa-eye"></i></a>
                                <a href="#" class="btn btn-warning btn-sm me-1" title="Editar"><i class="fas fa-edit"></i></a>
                                <a href="#" class="btn btn-danger btn-sm" title="Eliminar"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Juan Pablo Rodríguez</td>
                            <td>1076543210</td>
                            <td>Diseño Gráfico</td>
                            <td>05/04/2025</td>
                            <td><span class="badge bg-danger">RECHAZADO</span></td>
                            <td>
                                <a href="#" class="btn btn-info btn-sm me-1" title="Ver"><i class="fas fa-eye"></i></a>
                                <a href="#" class="btn btn-warning btn-sm me-1" title="Editar"><i class="fas fa-edit"></i></a>
                                <a href="#" class="btn btn-danger btn-sm" title="Eliminar"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>Luisa Fernanda Gómez</td>
                            <td>1054321098</td>
                            <td>Gestión de Redes</td>
                            <td>20/01/2025</td>
                            <td><span class="badge bg-success">ACEPTADO</span></td>
                            <td>
                                <a href="#" class="btn btn-info btn-sm me-1" title="Ver"><i class="fas fa-eye"></i></a>
                                <a href="#" class="btn btn-warning btn-sm me-1" title="Editar"><i class="fas fa-edit"></i></a>
                                <a href="#" class="btn btn-danger btn-sm" title="Eliminar"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>Pedro José Ramírez</td>
                            <td>1054321098</td>
                            <td>Administración de Empresas</td>
                            <td>12/03/2025</td>
                            <td><span class="badge bg-warning text-dark">EN PROCESO</span></td>
                            <td>
                                <a href="#" class="btn btn-info btn-sm me-1" title="Ver"><i class="fas fa-eye"></i></a>
                                <a href="#" class="btn btn-warning btn-sm me-1" title="Editar"><i class="fas fa-edit"></i></a>
                                <a href="#" class="btn btn-danger btn-sm" title="Eliminar"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Paginación -->
            <nav class="mt-3 d-flex justify-content-center">
                <ul class="pagination mb-0">
                    <li class="page-item disabled"><a class="page-link" href="#">Anterior</a></li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#">Siguiente</a></li>
                </ul>
            </nav>
        </div>
    </div>
@stop

@section('css')
    <style>
        .badge.bg-warning {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }
        .badge.bg-success {
            background-color: #28a745 !important;
        }
        .badge.bg-danger {
            background-color: #dc3545 !important;
        }
        .btn-info {
            background-color: #17a2b8 !important;
            border: none;
            color: #fff;
        }
        .btn-warning {
            background-color: #ffc107 !important;
            border: none;
            color: #212529;
        }
        .btn-danger {
            background-color: #dc3545 !important;
            border: none;
            color: #fff;
        }
        .btn-primary {
            background-color: #0d6efd !important;
            border: none;
        }
        .btn-outline-secondary {
            border-color: #ced4da;
            color: #495057;
        }
        .btn-outline-warning {
            border-color: #ffc107;
            color: #ffc107;
        }
        .btn-outline-success {
            border-color: #28a745;
            color: #28a745;
        }
        .btn-outline-danger {
            border-color: #dc3545;
            color: #dc3545;
        }
        .card {
            border-radius: 0.75rem;
        }
        .input-group-text {
            background: #fff;
        }
    </style>
@stop