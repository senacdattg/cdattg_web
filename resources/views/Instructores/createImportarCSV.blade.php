@extends('adminlte::page')
@section('css')
    <style>
        #overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
    </style>
@endsection
@section('content_header')
    <x-page-header 
        icon="fa-file-csv" 
        title="Importar Instructores CSV"
        subtitle="Importar instructores desde archivo CSV"
        :breadcrumb="[['label' => 'Inicio', 'url' => route('home.index'), 'icon' => 'fa-home'], ['label' => 'Instructores', 'url' => route('instructor.index'), 'icon' => 'fa-chalkboard-teacher'], ['label' => 'Importar CSV', 'icon' => 'fa-file-csv', 'active' => true]]"
    />
@endsection

@section('content')

        <section class="content">
            <div class="card">
                <div class="card-body">
                    <div class="card-body">
                        <a class="btn btn-warning btn-sm" href="{{ route('instructor.index') }}">
                            <i class="fas fa-arrow-left"></i>
                            </i>
                            Volver
                        </a>
                    </div>
                    <form action="{{ route('instructor.storeImportarCSV') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="archivoCSV">Seleccionar archivo</label>
                            <input type="file" class="form-control @error('archivoCSV') is-invalid @enderror" id="archivoCSV" name="archivoCSV" >
                        </div>
                        <button type="submit" class="btn btn-primary" id="btn-importar" onclick="showSpinner()">Importar</button>
                    </form>
                </div>

                <div class="card-body">

                    <div class="alert alert-info" role="alert">
                        <p>Por favor importa el archivo CSV que contiene los datos de los instructores.</p>
                        <p>Recuerde que el archivo CSV debe tener el title, que es el nombre completo del instructor, el
                            id_personal, que es el número de documento del instructor y el correo institucional.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <!-- Spinner and Overlay -->
    <div id="overlay" style="display: none;">
        <div class="spinner-border text-success" role="status">
            <span class="sr-only">Cargando...</span>
        </div>
    </div>
@endsection
@section('js')
    @vite(['resources/js/pages/formularios-generico.js'])
@endsection
