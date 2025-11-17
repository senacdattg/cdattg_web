@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-cogs" 
        title="Municipio"
        subtitle="Edición del municipio"
        :breadcrumb="[['label' => 'Municipios', 'url' => route('municipio.index') , 'icon' => 'fa-cog'], ['label' => 'Editar municipio', 'icon' => 'fa-edit', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('municipio.index') }}">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>

                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-edit mr-2"></i>Editar Municipio
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('municipio.update', $municipio->id) }}" class="row">
                                @csrf
                                @method('PUT')

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="municipio" class="form-label font-weight-bold">Municipio</label>
                                        <input type="text" name="municipio"
                                            class="form-control @error('municipio') is-invalid @enderror"
                                            value="{{ old('municipio', $municipio->municipio) }}" required>
                                        @error('municipio')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status" class="form-label font-weight-bold">Estado</label>
                                        <select name="status" class="form-control @error('status') is-invalid @enderror"
                                            required>
                                            <option value="1" {{ $municipio->status == 1 ? 'selected' : '' }}>Activo
                                            </option>
                                            <option value="0" {{ $municipio->status == 0 ? 'selected' : '' }}>Inactivo
                                            </option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <hr class="mt-4">
                                    <div class="d-flex justify-content-end">
                                        <a href="{{ route('municipio.index') }}" class="btn btn-light mr-2">
                                            Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save mr-1"></i>Guardar Cambios
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection