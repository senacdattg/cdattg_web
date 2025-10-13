@extends('adminlte::page')

@section('content_header')
    <x-page-header 
        icon="fa-calendar-check" 
        title="Asistencia {{ $ficha->ficha ?? $ficha->nombre_curso }}"
        subtitle="Gestión de asistencias de la ficha"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => route('home.index'), 'icon' => 'fa-home'], 
            ['label' => 'Fichas de caracterización', 'url' => route('fichaCaracterizacion.index'), 'icon' => 'fa-file-alt'],
            ['label' => 'Asistencia ' . ($ficha->ficha ?? $ficha->nombre_curso), 'active' => true, 'icon' => 'fa-calendar-check']
        ]"
    />
@endsection

@section('content')
        <div class="content">
            {{-- <div class="row">
                <div class="col">
                    @include('entradaSalidas.create', ['ficha' => $ficha->id])
                </div>
                <div class="col">
                    @include('entradaSalidas.edit')
                </div>
            </div> --}}
        </div>
        <section class="content">

            <div class="card">
                <div class="card-body">
                    {{-- boton de qr --}}
                    <div class="row justify-content-center">
                        <form action="{{ route('entradaSalida.cargarDatos') }}"  >
                            @csrf
                            <select name="evento" id="evento" class="form-control">
                                <option value="1">Entrada</option>
                                <option value="0">Salida</option>
                            </select>
                            <input type="hidden" name="ambiente_id" value="{{ $ambiente->id }}">
                            <input type="hidden" name="descripcion" value="{{ $descripcion }}">
                            <input type="hidden" value="{{ $ficha->id }}" name="ficha_id">
                            <br>
                            <button type="submit" class="bnt btn-success btn-sm-2">
                                <i class="fas fa-qrcode"></i>
                            </button>
                        </form>
                    </div><br>

                    {{-- datos de la ficha y la fecha --}}
                    <div class="row">
                        <div class="col-sm-6 col-xs-12">
                            <div class="card card-body">
                                <p class="card-text">Fecha: {{ $fecha }}</p>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-12">
                            <div class="card card-body">
                                <p class="card-text">Ambiente: {{ $ambiente->title }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 col-xs-12">
                            <div class="card card-body">
                                <p class="card-text">Ficha: {{ $ficha->ficha }}</p>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-12">
                            <div class="card card-body">
                                <p class="card-text">Nombre del curso: {{ $ficha->nombre_curso }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            <div class="card card-body">
                                <p class="card-text">Descripción: {{ $descripcion }}</p>
                            </div>
                        </div>
                    </div>
                    {{-- finaliza datos --}}
                    
                    <x-data-table 
                        title="Lista de Asistencias"
                        searchable="true"
                        searchAction="{{ route('entradaSalida.index', ['ficha' => $ficha->id]) }}"
                        searchPlaceholder="Buscar aprendiz..."
                        searchValue="{{ request('search') }}"
                        :columns="[
                            ['label' => '#', 'width' => '5%'],
                            ['label' => 'Aprendiz', 'width' => '25%'],
                            ['label' => 'Entrada', 'width' => '30%'],
                            ['label' => 'Salida', 'width' => '30%'],
                            ['label' => 'Acciones', 'width' => '10%', 'class' => 'text-center']
                        ]"
                        :pagination="$registros->links()"
                    >
                                <?php $i = 1; ?>
                                @forelse ($registros as $registro)
                                    <tr>
                                        <td>
                                            {{ $i++ }}
                                            {{-- {{ $registro->id }} --}}
                                        </td>
                                        <td>
                                            {{ $registro->aprendiz }}
                                        </td>

                                        <td>
                                            {{ $registro->entrada }}
                                        </td>
                                        <td>
                                            {{ $registro->salida }}
                                        </td>
                                        <td class="text-center">
                                            <x-action-buttons 
                                                :show="false"
                                                :edit="false"
                                                :delete="true"
                                                deleteUrl="{{ route('entradaSalida.destroy', $registro->id) }}"
                                                deletePermission="ELIMINAR ASISTENCIA"
                                            />
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">No hay personas registradas</td>
                                    </tr>
                                @endforelse
                    </x-data-table>
                    
                    <div class="row align-self-center mt-3">
                        <div class="col align-self-center">
                            <a href="{{ route('entradaSalida.generarCSV', ['ficha' => $ficha->id]) }}" id="btn-generarCSV"
                                class="btn btn-warning btn-sm"><i class="fas fa-file-csv" style="font-size: 2em;"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            var btnGenerarCSV = $('#btn-generarCSV');

            btnGenerarCSV.click(function() {
                // Simular un formulario oculto y realizar la descarga
                var iframe = $('<iframe style="display: none;"></iframe>');
                $('body').append(iframe);

                iframe.attr('src', '{{ route('entradaSalida.generarCSV', ['ficha' => $ficha]) }}');

                // Redirigir después de la descarga
                setTimeout(function() {
                    window.location.href = '{{ route('fichaCaracterizacion.index') }}';
                }, 1000); // 2000 milisegundos (2 segundos) de retraso
            });
        });
    </script>
@endsection
