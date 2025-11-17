@extends('adminlte::page')

@section('title', 'Editar Caracterización')

@section('content')
    <section class="content">
        <div class="card">
            <div class="card-header">
                <div class="card-body">
                    <a class="btn btn-warning btn-sm" href="{{ route('caracterizacion.index') }}">
                        <i class="fas fa-arrow-left"></i>
                        Volver
                    </a>
                </div>
                <div class="card-body"></div>
                <form action="{{ route('caracterizacion.update', $caracterizacion->id) }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ficha_id">Ficha de Caracterización</label>
                                <select name="ficha_id" class="form-control" id="sede_id" required>
                                    @if (isset($fichas) && count($fichas) > 0)
                                        @foreach ($fichas as $ficha)
                                            <option value="{{ $ficha->id }}"
                                                {{ isset($caracterizacion) &&
                                                    $caracterizacion->ficha_id == $ficha->id
                                                        ? 'selected'
                                                        : '' }}>
                                                {{ $ficha->ficha }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="">No hay fichas disponibles</option>
                                    @endif

                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="programa_formacion_id">Programa de formación</label>
                                <select name="programa_formacion_id" class="form-control" id="programa_id" required>
                                    @if (isset($programas) && count($programas) > 0)
                                        @foreach ($programas as $programa)
                                            <option value="{{ $programa->id }}"
                                                {{ isset($caracterizacion) &&
                                                    $caracterizacion->programa_formacion_id == $programa->id
                                                        ? 'selected'
                                                        : '' }}>
                                                {{ $programa->nombre }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="">No hay programas disponibles</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="instructor_persona_id">Instructor</label>
                                <select name="instructor_persona_id" class="form-control" id="instructor_persona_id"
                                    required>
                                    @if (isset($instructores) && count($instructores) > 0)
                                        @foreach ($instructores as $instructor)
                                            <option value="{{ $instructor->persona_id }}"
                                                {{ isset($caracterizacion) &&
                                                    $caracterizacion->instructor_persona_id ==
                                                        $instructor->persona_id
                                                        ? 'selected'
                                                        : '' }}>
                                                {{ $instructor->persona->primer_nombre }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="">No hay instructores disponibles</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jornada_id">Jornada de formación</label>
                                <select name="jornada_id" class="form-control" id="jornada_id" required>
                                    @if (isset($jornadas) && count($jornadas) > 0)
                                        @foreach ($jornadas as $jornada)
                                            <option value="{{ $jornada->id }}"
                                                {{ isset($caracterizacion) &&
                                                    $caracterizacion->jornada_id == $jornada->id
                                                        ? 'selected'
                                                        : '' }}>
                                                {{ $jornada->jornada }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="">No hay jornadas disponibles</option>
                                    @endif

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sede_id">Sede</label>
                                <select name="sede_id" class="form-control" id="sede_id" required>
                                    @if (isset($sedes) && count($sedes) > 0)
                                        @foreach ($sedes as $sede)
                                            <option value="{{ $sede->id }}"
                                                {{ isset($caracterizacion) &&
                                                    $caracterizacion->sede_id == $sede->id
                                                        ? 'selected'
                                                        : '' }}>
                                                {{ $sede->sede }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="">No hay sedes disponibles</option>
                                    @endif
                                </select>
                            </div>
                        </div>

                    </div>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </form>
            </div>

        </div>
        </div>
    </section>
@stop

@section('footer')
    @include('layouts.footer')
@stop
