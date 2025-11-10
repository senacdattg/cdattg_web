@extends('adminlte::page')

@section('title', 'Talento Humano')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h1 class="mb-0"><i class="fas fa-users me-2"></i>Talento Humano</h1>
            <p class="text-muted mb-0">Consulta información del talento humano</p>
        </div>
    </div>
@stop

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <form class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Cédula</label>
                    <input type="text" class="form-control form-control-lg" id="cedula"
                        placeholder="Ingrese la cédula">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-primary btn-lg w-100" id="btn-consultar">
                        <i class="fas fa-search me-1"></i>Consultar
                    </button>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-secondary btn-lg w-100" id="btn-limpiar">
                        <i class="fas fa-eraser me-1"></i>Limpiar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Formulario de datos personales (inicialmente oculto) -->
    <div id="form-container" class="mt-4" style="display: none;">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title" id="form-title">Información de la Persona</h3>
            </div>
            <div class="card-body">
                <form id="personaForm" action="{{ route('talento-humano.consultar') }}" method="POST"
                    enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <input type="hidden" id="action_type" name="action_type" value="consultar">
                    @include('personas.partials.form', ['showCaracterizacion' => true])
                </form>
            </div>
            <div class="card-footer">
                <button type="button" class="btn btn-secondary" id="btn-cancelar" style="display: none;">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="btn-crear-persona" style="display: none;">
                    <i class="fas fa-save me-1"></i>Crear Persona
                </button>
                <button type="button" class="btn btn-success" id="btn-guardar-cambios" style="display: none;">
                    <i class="fas fa-save me-1"></i>Guardar Cambios
                </button>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .card {
            border: none;
            border-radius: 0.375rem;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .form-control.is-invalid:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        .btn {
            border-radius: 0.375rem;
        }
    </style>
@stop

@section('js')
    @vite(['resources/js/pages/formularios-select-dinamico.js'])
    @vite(['resources/js/pages/talento-humano.js'])
    @stack('js')

    <!-- CSRF Token para AJAX -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop
