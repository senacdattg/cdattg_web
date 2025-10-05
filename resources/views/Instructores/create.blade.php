@extends('adminlte::page')

@section('title', 'Crear Instructor')

@section('css')
    <style>
        .form-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }
        .form-section h5 {
            color: #007bff;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .required-field::after {
            content: " *";
            color: #dc3545;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        .card-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
        }
        .btn-create {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .btn-create:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .alert-custom {
            border-radius: 8px;
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .breadcrumb-custom {
            background: transparent;
            padding: 0;
        }
        .breadcrumb-custom .breadcrumb-item a {
            color: #007bff;
            text-decoration: none;
        }
        .breadcrumb-custom .breadcrumb-item.active {
            color: #6c757d;
        }
    </style>
@endsection

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="text-primary">
                        <i class="fas fa-chalkboard-teacher mr-2"></i>
                        Crear Instructor
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb breadcrumb-custom float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home.index') }}">
                                <i class="fas fa-home mr-1"></i>Inicio
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('instructor.index') }}">
                                <i class="fas fa-chalkboard-teacher mr-1"></i>Instructores
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            <i class="fas fa-plus mr-1"></i>Crear Instructor
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Alertas -->
            @if (session('success'))
                <div class="alert alert-success alert-custom alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-custom alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title text-white">
                                <i class="fas fa-user-plus mr-2"></i>
                                Formulario de Registro de Instructor
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('instructor.index') }}" class="btn btn-light btn-sm">
                                    <i class="fas fa-arrow-left mr-1"></i>
                                    Volver
                                </a>
                            </div>
                        </div>

                        <form action="{{ route('instructor.store') }}" method="post" id="instructorForm">
                            @csrf
                            
                            <div class="card-body">
                                <!-- Información de Documento -->
                                <div class="form-section">
                                    <h5>
                                        <i class="fas fa-id-card mr-2"></i>
                                        Información de Documento
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="tipo_documento" class="required-field">Tipo de Documento</label>
                                                <select class="form-control @error('tipo_documento') is-invalid @enderror"
                                                    name="tipo_documento" autofocus required>
                                                    <option value="" disabled selected>Seleccione un tipo de documento</option>
                                                    @forelse ($documentos->parametros as $parametro)
                                                        <option value="{{ $parametro->id }}" 
                                                            {{ old('tipo_documento') == $parametro->id ? 'selected' : '' }}>
                                                            {{ $parametro->name }}
                                                        </option>
                                                    @empty
                                                        <option value="" disabled>No hay tipos de documento disponibles</option>
                                                    @endforelse
                                                </select>
                                                @error('tipo_documento')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="numero_documento" class="required-field">Número de Documento</label>
                                                <input type="text"
                                                    class="form-control @error('numero_documento') is-invalid @enderror"
                                                    value="{{ old('numero_documento') }}" 
                                                    name="numero_documento"
                                                    placeholder="Ingrese el número de documento"
                                                    required>
                                                @error('numero_documento')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Información Personal -->
                                <div class="form-section">
                                    <h5>
                                        <i class="fas fa-user mr-2"></i>
                                        Información Personal
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="primer_nombre" class="required-field">Primer Nombre</label>
                                                <input type="text"
                                                    class="form-control @error('primer_nombre') is-invalid @enderror"
                                                    value="{{ old('primer_nombre') }}" 
                                                    placeholder="Ingrese el primer nombre"
                                                    name="primer_nombre"
                                                    required>
                                                @error('primer_nombre')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="segundo_nombre">Segundo Nombre</label>
                                                <input type="text"
                                                    class="form-control @error('segundo_nombre') is-invalid @enderror"
                                                    value="{{ old('segundo_nombre') }}" 
                                                    placeholder="Ingrese el segundo nombre (opcional)"
                                                    name="segundo_nombre">
                                                @error('segundo_nombre')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="primer_apellido" class="required-field">Primer Apellido</label>
                                                <input type="text"
                                                    class="form-control @error('primer_apellido') is-invalid @enderror"
                                                    value="{{ old('primer_apellido') }}" 
                                                    placeholder="Ingrese el primer apellido"
                                                    name="primer_apellido"
                                                    required>
                                                @error('primer_apellido')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="segundo_apellido">Segundo Apellido</label>
                                                <input type="text"
                                                    class="form-control @error('segundo_apellido') is-invalid @enderror"
                                                    value="{{ old('segundo_apellido') }}" 
                                                    placeholder="Ingrese el segundo apellido (opcional)"
                                                    name="segundo_apellido">
                                                @error('segundo_apellido')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Información Demográfica -->
                                <div class="form-section">
                                    <h5>
                                        <i class="fas fa-calendar-alt mr-2"></i>
                                        Información Demográfica
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="genero" class="required-field">Género</label>
                                                <select class="form-control @error('genero') is-invalid @enderror" 
                                                    name="genero" required>
                                                    <option value="" disabled selected>Seleccione un género</option>
                                                    @forelse ($generos->parametros as $parametro)
                                                        <option value="{{ $parametro->id }}"
                                                            {{ old('genero') == $parametro->id ? 'selected' : '' }}>
                                                            {{ $parametro->name }}
                                                        </option>
                                                    @empty
                                                        <option value="" disabled>No hay géneros disponibles</option>
                                                    @endforelse
                                                </select>
                                                @error('genero')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="fecha_de_nacimiento" class="required-field">Fecha de Nacimiento</label>
                                                <input type="date"
                                                    class="form-control @error('fecha_de_nacimiento') is-invalid @enderror"
                                                    value="{{ old('fecha_de_nacimiento') }}" 
                                                    name="fecha_de_nacimiento"
                                                    required>
                                                @error('fecha_de_nacimiento')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Información de Contacto -->
                                <div class="form-section">
                                    <h5>
                                        <i class="fas fa-envelope mr-2"></i>
                                        Información de Contacto
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email" class="required-field">Correo Electrónico</label>
                                                <input type="email" 
                                                    class="form-control @error('email') is-invalid @enderror"
                                                    placeholder="Ingrese el correo electrónico"
                                                    value="{{ old('email') }}" 
                                                    name="email"
                                                    required>
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="telefono">Teléfono</label>
                                                <input type="tel" 
                                                    class="form-control @error('telefono') is-invalid @enderror"
                                                    placeholder="Ingrese el número de teléfono (opcional)"
                                                    value="{{ old('telefono') }}" 
                                                    name="telefono">
                                                @error('telefono')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="celular">Celular</label>
                                                <input type="tel" 
                                                    class="form-control @error('celular') is-invalid @enderror"
                                                    placeholder="Ingrese el número de celular (opcional)"
                                                    value="{{ old('celular') }}" 
                                                    name="celular">
                                                @error('celular')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="direccion">Dirección</label>
                                                <textarea class="form-control @error('direccion') is-invalid @enderror"
                                                    placeholder="Ingrese la dirección (opcional)"
                                                    name="direccion"
                                                    rows="2">{{ old('direccion') }}</textarea>
                                                @error('direccion')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Información Institucional -->
                                <div class="form-section">
                                    <h5>
                                        <i class="fas fa-building mr-2"></i>
                                        Información Institucional
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="regional_id" class="required-field">Regional</label>
                                                <select name="regional_id"
                                                    class="form-control @error('regional_id') is-invalid @enderror" required>
                                                    <option value="" disabled selected>Seleccione una regional</option>
                                                    @foreach ($regionales as $regional)
                                                        <option value="{{ $regional->id }}"
                                                            {{ old('regional_id') == $regional->id ? 'selected' : '' }}>
                                                            {{ $regional->regional }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('regional_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer bg-light">
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <button type="button" class="btn btn-secondary mr-2" onclick="history.back()">
                                            <i class="fas fa-times mr-1"></i>
                                            Cancelar
                                        </button>
                                        <button type="submit" class="btn btn-create">
                                            <i class="fas fa-save mr-1"></i>
                                            Crear Instructor
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Validación del formulario
            $('#instructorForm').on('submit', function(e) {
                let isValid = true;
                
                // Validar campos requeridos
                $('input[required], select[required]').each(function() {
                    if (!$(this).val()) {
                        $(this).addClass('is-invalid');
                        isValid = false;
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Campos Requeridos',
                        text: 'Por favor, complete todos los campos obligatorios marcados con *',
                        confirmButtonColor: '#007bff'
                    });
                }
            });

            // Limpiar validación al escribir
            $('input, select').on('input change', function() {
                $(this).removeClass('is-invalid');
            });

            // Confirmación antes de enviar
            $('#instructorForm').on('submit', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: '¿Crear Instructor?',
                    text: '¿Está seguro de que desea crear este instructor?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#dc3545',
                    confirmButtonText: 'Sí, crear',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    </script>
@endsection