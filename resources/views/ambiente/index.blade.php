@extends('adminlte::page')

@section('content_header')
    <x-page-header 
        icon="fa-door-open" 
        title="Ambientes"
        subtitle="Gestión de ambientes del sistema"
        :breadcrumb="[['label' => 'Inicio', 'url' => route('home.index'), 'icon' => 'fa-home'], ['label' => 'Ambientes', 'active' => true, 'icon' => 'fa-door-open']]"
    />
@endsection

@section('content')

        <section class="content">
            <div class="card">

                <div class="card-body p-0">
                    <table class="table table-responsive">
                        <thead>
                            <tr>
                                <th style="width: 1%">
                                    #
                                </th>
                                <th style="width: 20%">
                                    Nombre
                                </th>
                                <th style="width: 30%">
                                    Sede
                                </th>
                                <th style="width: 40%">
                                    bloque
                                </th>
                                <th style="width: 50%">
                                    piso
                                </th>
                                <th style="width: 60%">
                                    Estado
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            ?>
                            @forelse ($ambientes as $ambiente)
                                <tr>
                                    <td>
                                        {{ $i++ }}
                                    </td>
                                    <td>
                                        {{ $ambiente->title }}
                                    </td>
                                    <td>
                                        {{ $ambiente->piso->bloque->sede->sede }}
                                    </td>
                                    <td>
                                        {{ $ambiente->piso->bloque->bloque }}
                                    </td>
                                    <td>
                                        {{ $ambiente->piso->piso }}
                                    </td>

                                    <td>
                                        <span class="badge badge-{{ $ambiente->status === 1 ? 'success' : 'danger' }}">
                                            @if ($ambiente->status === 1)
                                                ACTIVO
                                            @else
                                                INACTIVO
                                            @endif
                                        </span>
                                    </td>
                                    @can('EDITAR AMBIENTE')
                                        <td>

                                            <form id="cambiarEstadoForm" class=" d-inline"
                                                action="{{ route('ambiente.cambiarEstado', ['ambiente' => $ambiente->id]) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-success btn-sm"><i
                                                        class="fas fa-sync"></i></button>
                                            </form>
                                        </td>
                                    @endcan
                                    @can('VER AMBIENTE')
                                        <td>
                                            <a class="btn btn-warning btn-sm"
                                                href="{{ route('ambiente.show', ['ambiente' => $ambiente->id]) }}">
                                                <i class="fas fa-eye"></i>

                                            </a>
                                        </td>
                                    @endcan
                                    @can('EDITAR AMBIENTE')
                                        <td>
                                            <a class="btn btn-info btn-sm"
                                                href="{{ route('ambiente.edit', ['ambiente' => $ambiente->id]) }}">
                                                <i class="fas fa-pencil-alt">
                                                </i>
                                            </a>
                                        </td>
                                    @endcan
                                    @can('ELIMINAR AMBIENTE')
                                        <td>
                                            <form class="formulario-eliminar btn"
                                                action="{{ route('ambiente.destroy', ['ambiente' => $ambiente->id]) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-danger btn-sm">

                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    @endcan
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="4">No hay ambientes registrados</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
    </div>

    <div class="card-footer">
        <div class="float-right">
            {{ $ambientes->links() }}
        </div>
    </div>
@endsection
