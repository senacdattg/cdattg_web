@extends('adminlte::page')

@section('content_header')
    <x-page-header 
        icon="fa-cube" 
        title="Bloques"
        subtitle="Gestión de bloques del sistema"
        :breadcrumb="[['label' => 'Inicio', 'url' => route('home.index'), 'icon' => 'fa-home'], ['label' => 'Bloques', 'active' => true, 'icon' => 'fa-cube']]"
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
                                <th style="width: 50%">
                                    Estado
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $i = 1;
                            ?>
                            @forelse ($bloques as $bloque)
                                <tr>
                                    <td>
                                        {{ $i++ }}
                                    </td>
                                    <td>
                                        {{ $bloque->bloque }}
                                    </td>

                                    <td>
                                        {{ $bloque->sede->sede }}
                                    </td>

                                    <td>
                                        <span class="badge badge-{{ $bloque->status === 1 ? 'success' : 'danger' }}">
                                            @if ($bloque->status === 1)
                                                ACTIVO
                                            @else
                                                INACTIVO
                                            @endif
                                        </span>
                                    </td>
                                    @can('EDITAR BLOQUE')

                                    <td>
                                        <form id="cambiarEstadoForm" class=" d-inline"
                                        action="{{ route('bloque.cambiarEstado', ['bloque' => $bloque->id]) }}"
                                        method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-success btn-sm"><i
                                            class="fas fa-sync"></i></button>
                                        </form>
                                    </td>
                                    @endcan
                                    @can('VER BLOQUE')

                                    <td>
                                        <a class="btn btn-warning btn-sm"
                                        href="{{ route('bloque.show', ['bloque' => $bloque->id]) }}">
                                        <i class="fas fa-eye"></i>

                                    </a>
                                </td>
                                @endcan
                                @can('EDITAR BLOQUE')

                                <td>
                                    <a class="btn btn-info btn-sm"
                                    href="{{ route('bloque.edit', ['bloque' => $bloque->id]) }}">
                                    <i class="fas fa-pencil-alt">
                                    </i>
                                </a>
                            </td>
                            @endcan
                            @can('ELIMINAR BLOQUE')

                            <td>
                                <form class="formulario-eliminar btn" action="{{ route('bloque.destroy', ['bloque' => $bloque->id]) }}" method="POST"
                                    class="d-inline">
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
                                    <td colspan="4">No hay bloques registrados</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
    </div>

    <div class="card-footer">
        <div class="float-right">
            {{ $bloques->links() }}
        </div>
    </div>
@endsection
