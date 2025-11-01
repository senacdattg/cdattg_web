@extends('complementarios.layout.master-layout-complementarios')
@section('title', 'Programas Complementarios | SENA')
@section('css')
    @vite(['resources/css/programas_publicos.css'])
@endsection
@section('content')
    

    <div class="container-fluid mt-4 px-2 px-md-4">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="text-center mb-4">
                    <h2 class="text-success">Programas Complementarios</h2>
                    <p class="text-muted">Descubre nuestros programas de formación complementaria disponibles</p>
                </div>
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-graduation-cap mr-2"></i>Programas Complementarios
                        </h3>
                        <div class="card-tools">
                            <span class="badge badge-success">Disponibles</span>
                        </div>
                    </div>
                    <div class="card-body">

                        <!-- Programs Cards View -->
                        <div class="row justify-content-center g-3">
                            @foreach ($programas as $programa)
                                @include('complementarios.components.card-programas', ['programa' => $programa])
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('layout.footer')
@endsection
