@extends('adminlte::page')

@section('title', 'Caracterización')

@section('content')
    <div class="content-wrapper mt-3">
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
                    <form action="{{ route('caracterizacion.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ficha_text">Ficha de Caracterización</label>
                                    <input type="hidden" name="ficha_id" id="ficha_id" value="{{ $ficha->id }}">
                                    <input type="text" id="ficha_text" class="form-control" value="{{ $ficha->ficha }}"
                                        readonly disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="programa_text">Programa de Formación</label>
                                    <input type="hidden" name="programa_id" id="programa_id"
                                        value="{{ $ficha->programaFormacion->id }}">
                                    <input type="text" id="programa_text" class="form-control"
                                        value="{{ $ficha->programaFormacion->nombre }}" readonly disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sede_text">Sede</label>
                                    <input type="hidden" name="sede_id" id="sede_id" value="{{ $sede->id }}">
                                    <input type="text" id="sede_text" class="form-control" value="{{ $sede->sede }}"
                                        readonly disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="jornada_id">Jornada de formación</label>
                                    <select name="jornada_id" class="form-control" id="jornada_id" required>
                                        <option value="">Seleccione una jornada</option>
                                        @if (count($jornadas) > 0)
                                            @foreach ($jornadas as $jornada)
                                                <option value="{{ $jornada->id }}">{{ $jornada->jornada }}</option>
                                            @endforeach
                                        @else
                                            <option value="">No hay Jornadas disponibles</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="persona_id">Instructor</label>
                                    <select name="persona_id" class="form-control select2" id="persona_id" required>
                                        <option value="">Seleccionar instructor</option>
                                        @foreach ($instructors as $instructor)
                                            <option value="{{ $instructor->persona_id }}">
                                                {{ $instructor->persona->primer_nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary"
                                        style="margin-top: 32px;">Guardar</button>
                                </div>
                            </div>
                        </div>
                </div>
                </form>
            </div>
    </div>
    </div>
    </section>
    </div>
@stop

@section('footer')
    @include('layouts.footer')
@stop

@push('js')
    @vite(['resources/js/pages/formularios-generico.js'])
@endpush
