@extends('adminlte::page')

@section('plugins.Sweetalert2', true)

@section('title', 'Talento Humano')

@section('content_header')
    <x-page-header icon="fa-users" title="Talento Humano"
        subtitle="Consulta y gestiona información del talento humano en tiempo real" :breadcrumb="[
            ['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'],
            ['label' => 'Talento Humano', 'icon' => 'fa-users', 'active' => true],
        ]" />
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-lg-10 mx-auto">
                    <div class="card card-outline card-primary shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-search mr-2"></i>Búsqueda de Persona
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8 mx-auto">
                                    <div class="form-group">
                                        <label for="numero_documento_buscar">
                                            Número de Documento
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group input-group-lg">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-id-card"></i>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control" id="numero_documento_buscar"
                                                placeholder="Ingrese el número de documento" autocomplete="off">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-secondary" id="btn-limpiar">
                                                    <i class="fas fa-eraser"></i> Limpiar
                                                </button>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            La búsqueda se realiza automáticamente mientras escribe
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="form-container" style="display: none;">
                        <div class="card card-outline card-success shadow-sm">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-edit text-success mr-2"></i>
                                    <h3 class="card-title mb-0" id="form-title">
                                        Información de la Persona
                                    </h3>
                                </div>
                                <div class="d-flex flex-wrap align-items-center">
                                    <button type="button" class="btn btn-warning mr-2 mb-2" id="btn-editar"
                                        style="display: none;">
                                        <i class="fas fa-edit mr-1"></i> Editar
                                    </button>
                                    <button type="button" class="btn btn-primary mb-2" id="btn-guardar"
                                        style="display: none;">
                                        <i class="fas fa-save mr-1"></i> Guardar
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <form id="personaForm" autocomplete="off">
                                    @csrf
                                    <input type="hidden" id="persona_id" name="persona_id">
                                    <input type="hidden" id="action_mode" name="action_mode" value="create">
                                    @include('personas.partials.form', ['showCaracterizacion' => true])
                                </form>
                            </div>
                            <div class="card-footer d-flex justify-content-end">
                                <button type="button" class="btn btn-secondary" id="btn-cancelar">
                                    <i class="fas fa-times mr-1"></i> Cancelar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('js')
    @vite(['resources/js/app.js', 'resources/js/pages/formularios-select-dinamico.js', 'resources/js/pages/talento-humano.js'])
    @stack('js')
@endsection
