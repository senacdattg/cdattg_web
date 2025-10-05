@extends('adminlte::page')
@section('content')

        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Instructores</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="{{ route('home.index') }}">Inicio</a>
                            </li>
                            <li class="breadcrumb-item active">Instructores
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <p>
                    {{ session('success') }}
                </p>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <p>
                    {{ session('error') }}
                </p>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if ($instructoresSinUsuario->count() > 0)
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <p>
                    Los instructores a continuación no tienen un usuario asociado, por favor vuelve a registrarlos o
                    eliminarlos.
                </p>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <section class="content">
            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-responsive">
                        <thead>
                            <tr>
                                <th style="width: 20%">
                                    Nombre y apellido
                                </th>
                                <th style="width: 30%">
                                    Numero de documento
                                </th>
                                <th style="width: 40%">
                                    Correo electronico
                                </th>
                                <th style="width: 10%">
                                    Opciones
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($instructoresSinUsuario as $instructor)
                            <tr>
                                <td>{{ $instructor->persona->primer_nombre }} {{ $instructor->persona->primer_apellido }}</td>
                                <td>{{ $instructor->persona->numero_documento }}</td>
                                <td>{{ $instructor->persona->email ?? 'N/A' }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('instructor.create') }}?persona_id={{ $instructor->persona->id }}" class="btn btn-primary mr-2" title="Crear Usuario">
                                            <i class="fas fa-user-plus"></i>
                                        </a>
                                        <a href="{{ route('instructor.deleteWithoudUser', ['id' => $instructor->id]) }}"
                                            class="btn btn-danger" title="Eliminar Instructor"
                                            onclick="return confirm('¿Está seguro de eliminar este instructor?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                    </table>
                </div>
            </div>
    </div>
@endsection

@section('footer')
    @include('layout.footer')
@endsection
