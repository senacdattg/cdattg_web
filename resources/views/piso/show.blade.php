@extends('adminlte::page')
@section('content')
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ $piso->piso }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="{{ route('home.index') }}" wire:navigate>Inicio</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('piso.index') }}" wire:navigate>pisos</a>
                            </li>
                            <li class="breadcrumb-item active">
                                {{ $piso->piso }}
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">

            <div class="card">
                <div class="card-body">
                    <a class="btn btn-warning btn-sm" href="{{ route('piso.index') }}">
                        <i class="fas fa-arrow-left"></i>
                        </i>
                        Volver
                    </a>
                </div>
                <div class="container">
                    <div class="card-body">
                        <div class="row">
                            <div class="col ">
                                <table class="table table-bordered border border-primary">
                                    <tr>
                                        <th>
                                            <strong>piso:</strong>
                                        </th>
                                        <td>
                                            <p>
                                                {{ $piso->piso }}
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <strong>Sede:</strong>
                                        </th>
                                        <td>
                                            {{ $piso->bloque->sede->sede }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <strong>Bloque:</strong>
                                        </th>
                                        <td>
                                            {{ $piso->bloque->bloque }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <strong>Creado por:</strong>
                                        </th>
                                        <td>
                                            {{ $piso->userCreate->persona->primer_nombre }}
                                            {{ $piso->userCreate->persona->primer_apellido }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <strong>Actualizado por:</strong>
                                        </th>
                                        <td>
                                            {{ $piso->userEdit->persona->primer_nombre }}
                                            {{ $piso->userEdit->persona->primer_apellido }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <strong>Estado:</strong>
                                        </th>
                                        <td>
                                            <span class="badge badge-{{ $piso->status === 1 ? 'success' : 'danger' }}">
                                                @if ($piso->status === 1)
                                                    ACTIVO
                                                @else
                                                    INACTIVO
                                                @endif
                                            </span>
                                        </td>
                                    </tr>

                                </table>
                            </div>


                        </div>
                    </div>
                </div>
                {{-- Botones --}}
                <div class="mb-3 text-center">
                    @can('EDITAR PISO')
                        <form id="cambiarEstadoForm" class=" d-inline"
                            action="{{ route('piso.cambiarEstado', ['piso' => $piso->id]) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-sync"></i></button>
                        </form>
                        <a class="btn btn-info btn-sm" href="{{ route('piso.edit', ['piso' => $piso->id]) }}">
                            <i class="fas fa-pencil-alt">
                            </i>
                        </a>
                    @endcan
                    @can('ELIMINAR PISO')
                        <form class="formulario-eliminar btn" action="{{ route('piso.destroy', ['piso' => $piso->id]) }}"
                            method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    @endcan

                </div>
            </div>
        @endsection
