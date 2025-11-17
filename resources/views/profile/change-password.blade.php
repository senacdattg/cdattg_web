@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <x-page-header icon="fa-key" title="Cambiar Contraseña" subtitle="Actualiza tu contraseña de acceso" :breadcrumb="[
        ['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'],
        ['label' => 'Mi Perfil', 'url' => route('profile.index'), 'icon' => 'fa-user'],
        ['label' => 'Cambiar Contraseña', 'icon' => 'fa-key', 'active' => true],
    ]" />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">
                    <div class="mb-3">
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('profile.index') }}" title="Volver">
                            <i class="fas fa-arrow-left mr-1"></i> Volver
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle mr-2"></i>
                            <strong>Éxito:</strong> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <strong>Error:</strong> {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-key mr-2"></i>Actualizar Contraseña
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('password.change') }}" autocomplete="off">
                                @csrf
                                @method('PUT')

                                {{-- Contraseña Actual --}}
                                <div class="form-group">
                                    <label for="current_password">Contraseña Actual</label>
                                    <div class="input-group">
                                        <input type="password" name="current_password" id="current_password"
                                            class="form-control @error('current_password') is-invalid @enderror"
                                            placeholder="Ingrese su contraseña actual" autocomplete="current-password">
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <span class="fas fa-lock"></span>
                                            </div>
                                        </div>
                                        @error('current_password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Nueva Contraseña --}}
                                <div class="form-group">
                                    <label for="password">Nueva Contraseña</label>
                                    <div class="input-group">
                                        <input type="password" name="password" id="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            placeholder="Ingrese su nueva contraseña" autocomplete="new-password">
                                        <div class="input-group-append">
                                            <button type="button" id="passwordToggle" class="btn btn-outline-secondary"
                                                aria-label="Mostrar u ocultar contraseña">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <small class="form-text text-muted">
                                        La contraseña debe tener al menos 8 caracteres.
                                    </small>
                                </div>

                                {{-- Confirmar Contraseña --}}
                                <div class="form-group">
                                    <label for="password_confirmation">Confirmar Nueva Contraseña</label>
                                    <div class="input-group">
                                        <input type="password" name="password_confirmation" id="password_confirmation"
                                            class="form-control @error('password_confirmation') is-invalid @enderror"
                                            placeholder="Confirme su nueva contraseña" autocomplete="new-password">
                                        <div class="input-group-append">
                                            <button type="button" id="passwordConfirmToggle"
                                                class="btn btn-outline-secondary"
                                                aria-label="Mostrar u ocultar confirmación">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        @error('password_confirmation')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <hr class="mt-4">
                                <div class="d-flex flex-column flex-sm-row justify-content-center gap-2">
                                    <a href="{{ route('profile.index') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-times mr-1"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-save mr-1"></i> Actualizar Contraseña
                                    </button>
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

@push('js')
    <script>
        (function() {
            const toggle = (inputId, btnId) => {
                const input = document.getElementById(inputId);
                const btn = document.getElementById(btnId);
                if (!input || !btn) return;
                btn.addEventListener('click', function() {
                    const isPwd = input.getAttribute('type') === 'password';
                    input.setAttribute('type', isPwd ? 'text' : 'password');
                    const icon = this.querySelector('i');
                    if (icon) {
                        icon.classList.toggle('fa-eye');
                        icon.classList.toggle('fa-eye-slash');
                    }
                    input.focus();
                });
            };
            toggle('password', 'passwordToggle');
            toggle('password_confirmation', 'passwordConfirmToggle');
        })();
    </script>
@endpush
