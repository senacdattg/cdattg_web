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
                    <input type="text" class="form-control form-control-lg" id="cedula" placeholder="Ingrese la cédula">
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
    .btn {
        border-radius: 0.375rem;
    }
</style>
@stop

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnConsultar = document.getElementById('btn-consultar');
        const btnLimpiar = document.getElementById('btn-limpiar');
        const cedulaInput = document.getElementById('cedula');

        btnConsultar.addEventListener('click', function() {
            const cedula = cedulaInput.value.trim();
            if (!cedula) {
                alert('Por favor ingrese una cédula');
                return;
            }
            // Aquí irá la funcionalidad de consulta
            console.log('Consultando cédula:', cedula);
        });

        btnLimpiar.addEventListener('click', function() {
            cedulaInput.value = '';
            // Aquí irá la funcionalidad de limpiar
            console.log('Campos limpiados');
        });
    });
</script>
@stop