@extends('layout.master-layout-registro')
@section('content')
        <div class="register-box">
            <div class="card card-outline card-primary">
                <div class="card-header text-center">
                    {{-- Bienvenida al login --}}
                    {{-- <a href="{{ route('login') }}" class="h1"><b>registro de asistencias SENA </b></a> --}}
                </div>
                <div class="card-body">
                    <p class="login-box-msg">Registrarme</p>

                    <form action="{{ route('registrarme') }}" method="post">
                        @csrf
                        {{-- nombres --}}
                        <label for="primer_nombre">Primer Nombre</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" value="{{ old('primer_nombre')  }}" placeholder="Primer Nombre" name="primer_nombre" autofocus>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                        </div>
                        <label for="segundo_nombre">Segundo Nombre</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" value="{{ old('segundo_nombre')  }}" placeholder="Segundo Nombre" name="segundo_nombre" >
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>

                        </div>

                        {{-- apellidos --}}
                        <label for="primer_apellido">Primer Apellido</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" value="{{ old('primer_apellido')  }}" placeholder="Primer Apellido" name="primer_apellido">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                        </div>
                        <label for="segundo_apellido">Segundo Apellido</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" value="{{ old('segundo_apellido')  }}" placeholder="Segundo Apellido" name="segundo_apellido" >
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>

                        </div>
                        {{-- documento --}}
                        <label for="documento">Documento de identidad</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" value="{{ old('documento') }}" name="documento" placeholder="Documento de Indentidad">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-id-card"></span>
                                </div>
                            </div>
                        </div>

                        {{-- email --}}
                        <label for="email">Correo institucional</label>
                        <div class="input-group mb-3">
                            <input type="email" class="form-control" placeholder="Correo Institucional" value="{{ old('email') }}" name="email">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-envelope"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-sm-center">


                            <div class="col-6">
                                <button type="submit" class="btn btn-primary btn-block ">Registrarme</button>
                            </div>
                    </form>
                        </div>
                        <hr>
                        {{-- <a href="{{ route('login') }}" class="text-center">Ya tengo una cuenta</a> --}}
                </div>

            </div>
        </div>




@endsection
