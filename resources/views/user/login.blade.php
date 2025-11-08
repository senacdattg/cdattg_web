@extends('layout.master-layout-registro')

@section('content')
    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                {{-- Logo del SENA --}}
                <img src="{{ asset('dist/img/LogoSena.png') }}" alt="Logo del sena" style="width: 150px; height: auto;">
                <h1>Bienvenido</h1>
            </div>

            <div class="card-body">
                <div class="d-flex justify-content-center">
                    <p class="login-box-msg"><strong>¡Para comenzar, inicie sesión!</strong></p>
                </div>

                {{-- Mostrar mensajes de éxito o error --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                {{-- Mostrar errores de validación en bloque --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                {{-- Formulario de inicio de sesión --}}
                <form action="{{ route('iniciarSesion') }}" method="POST">
                    @csrf
                    {{-- Campo hidden para el parámetro de redirección --}}
                    <input type="hidden" name="redirect" value="{{ request()->query('redirect') }}">

                    {{-- Correo Institucional --}}
                    <div class="form-group">
                        <label for="email">Usuario</label>
                        <div class="input-group">
                            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                                id="email" placeholder="Correo" value="{{ old('email') }}" required
                                autofocus>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-envelope"></span>
                                </div>
                            </div>
                        </div>
                        @error('email')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Contraseña --}}
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                name="password" id="password" placeholder="Contraseña" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
                        @error('password')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Botón de inicio de sesión --}}
                    <div class="row d-flex justify-content-center">
                        <div class="col-6">
                            <button type="submit" class="btn btn-outline-success btn-block">Iniciar Sesión</button>
                        </div>
                    </div>

                </form>

                {{-- Botón para volver a la página principal --}}
                <div class="row mt-3 d-flex justify-content-center">
                    <div class="col-6">
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-block">
                            <i class="fas fa-arrow-left mr-2"></i>Volver al Inicio
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
