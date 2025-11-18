<div>
    {{-- Búsqueda de Persona --}}
    <div class="card card-outline card-primary shadow-sm mb-3">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-search mr-2"></i>Búsqueda de Persona
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="form-group">
                        <label for="numero_documento">
                            Número de Documento
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group input-group-md input-group-lg-md">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-id-card"></i>
                                </span>
                            </div>
                            <input
                                type="text"
                                class="form-control @error('numeroDocumento') is-invalid @enderror"
                                id="numero_documento"
                                wire:model.live.debounce.1000ms="numeroDocumento"
                                placeholder="Número de documento"
                                autocomplete="off"
                                inputmode="numeric">
                            <div class="input-group-append">
                                <button
                                    type="button"
                                    class="btn btn-secondary btn-clear"
                                    wire:click="limpiarBusqueda">
                                    <i class="fas fa-eraser"></i>
                                    <span class="d-none d-sm-inline"> Limpiar</span>
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle mr-1"></i>
                            La búsqueda se realiza automáticamente mientras escribe
                        </small>
                        @error('numeroDocumento')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Información de la Persona --}}
    @if($mostrarFormulario && $personaEncontrada)
        <div class="card card-outline card-success shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="row align-items-center">
                    {{-- Información de la persona - Izquierda --}}
                    <div class="col-12 col-md-6 mb-3 mb-md-0">
                <div class="d-flex align-items-center">
                            <div class="mr-3 d-none d-sm-block">
                                <div
                                    class="bg-success-light rounded-circle
                                    d-flex align-items-center justify-content-center"
                                    style="width: 50px; height: 50px;">
                                    <i
                                        class="fas fa-user text-success"
                                        style="font-size: 1.5rem;"></i>
                                </div>
                </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                    <h4 class="card-title mb-0 text-dark font-weight-bold">
                                        Información de la Persona
                                    </h4>
                                    @if($estaDentro && $entradaActiva)
                                        @php
                                            $sedeNombre = $entradaActiva->sede
                                                ? $entradaActiva->sede->sede
                                                : 'Sede desconocida';
                                        @endphp
                                        <span
                                            class="badge badge-success badge-pill px-2 py-1"
                                            title="La persona está actualmente dentro de {{ $sedeNombre }}">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            <span class="d-none d-sm-inline">Dentro: </span>{{ $sedeNombre }}
                        </span>
                    @else
                                        <span
                                            class="badge badge-secondary badge-pill px-2 py-1"
                                            title="La persona no está dentro de ninguna sede actualmente">
                            <i class="fas fa-times-circle mr-1"></i>Fuera
                        </span>
                    @endif
                                </div>
                                <small class="text-muted d-block">
                                    @php
                                        $nombreCompleto = trim(
                                            ($personaEncontrada->primer_nombre ?? '') . ' ' .
                                            ($personaEncontrada->segundo_nombre ?? '') . ' ' .
                                            ($personaEncontrada->primer_apellido ?? '') . ' ' .
                                            ($personaEncontrada->segundo_apellido ?? '')
                                        );
                                    @endphp
                                    {{ $nombreCompleto }}
                                </small>
                            </div>
                        </div>
                    </div>
                    {{-- Botones de acción - Derecha --}}
                    <div class="col-12 col-md-6">
                        <div class="d-flex flex-column flex-md-row justify-content-md-end align-items-md-center">
                            @if(!$estaDentro)
                                {{-- Solo mostrar botón de entrada si NO tiene entrada activa --}}
                    <button
                        type="button"
                                    class="btn btn-success btn-action-principal w-100 w-md-auto mb-2 mb-md-0"
                        wire:click="abrirModalEntrada"
                        wire:loading.attr="disabled"
                        @if($procesando) disabled @endif>
                                    <i class="fas fa-sign-in-alt mr-2"></i>
                        <span wire:loading.remove wire:target="abrirModalEntrada,registrarIngresoSalida">
                            Registrar Entrada
                        </span>
                        <span wire:loading wire:target="abrirModalEntrada,registrarIngresoSalida">
                                        <i class="fas fa-spinner fa-spin mr-2"></i>
                                        Procesando...
                        </span>
                    </button>
                            @else
                                {{-- Solo mostrar botón de salida si tiene entrada activa --}}
                    <button
                        type="button"
                                    class="btn btn-danger btn-action-principal w-100 w-md-auto mb-2 mb-md-0"
                        wire:click="abrirModalSalida"
                        wire:loading.attr="disabled"
                        @if($procesando) disabled @endif>
                                    <i class="fas fa-sign-out-alt mr-2"></i>
                        <span wire:loading.remove wire:target="abrirModalSalida,registrarIngresoSalida">
                            Registrar Salida
                        </span>
                        <span wire:loading wire:target="abrirModalSalida,registrarIngresoSalida">
                                        <i class="fas fa-spinner fa-spin mr-2"></i>
                                        Procesando...
                        </span>
                    </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small font-weight-bold text-uppercase mb-1">
                            Número de Documento
                        </label>
                        <div class="form-control-plaintext bg-light rounded p-2 border">
                            <i class="fas fa-id-card text-muted mr-2"></i>
                            {{ $personaEncontrada->numero_documento }}
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small font-weight-bold text-uppercase mb-1">
                            Nombre Completo
                        </label>
                        <div class="form-control-plaintext bg-light rounded p-2 border">
                            <i class="fas fa-user text-muted mr-2"></i>
                            {{ $nombreCompleto }}
                        </div>
                    </div>
                    @if($personaEncontrada->email)
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small font-weight-bold text-uppercase mb-1">
                                Email
                            </label>
                            <div class="form-control-plaintext bg-light rounded p-2 border">
                                <i class="fas fa-envelope text-muted mr-2"></i>
                                {{ $personaEncontrada->email }}
                            </div>
                        </div>
                    @endif
                    @if($personaEncontrada->celular)
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small font-weight-bold text-uppercase mb-1">
                                Celular
                            </label>
                            <div class="form-control-plaintext bg-light rounded p-2 border">
                                <i class="fas fa-phone text-muted mr-2"></i>
                                {{ $personaEncontrada->celular }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Modal para Seleccionar Sede --}}
    @if($mostrarModalSede)
        <div
            class="modal fade show d-block"
            tabindex="-1"
            role="dialog"
            style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-building mr-2"></i>
                            Seleccionar Sede para {{ $accionPendiente === 'entrada' ? 'Entrada' : 'Salida' }}
                        </h5>
                        <button
                            type="button"
                            class="close"
                            wire:click="cerrarModalSede"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="select_sede">
                                Sede <span class="text-danger">*</span>
                            </label>
                            <select
                                class="form-control @error('sedeId') is-invalid @enderror"
                                id="select_sede"
                                wire:model="sedeId"
                                required>
                                <option value="">Seleccione una sede...</option>
                                @foreach($sedes as $sede)
                                    <option value="{{ $sede->id }}">{{ $sede->sede }}</option>
                                @endforeach
                            </select>
                            @error('sedeId')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="observaciones">Observaciones (Opcional)</label>
                            <textarea
                                class="form-control"
                                id="observaciones"
                                wire:model="observaciones"
                                rows="3"
                                placeholder="Ingrese observaciones adicionales..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            wire:click="cerrarModalSede">
                            Cancelar
                        </button>
                        <button
                            type="button"
                            class="btn btn-primary"
                            wire:click="registrarIngresoSalida"
                            wire:loading.attr="disabled"
                            @if($procesando) disabled @endif>
                            <span wire:loading.remove wire:target="registrarIngresoSalida">
                                <i class="fas fa-check mr-1"></i>Confirmar
                            </span>
                            <span wire:loading wire:target="registrarIngresoSalida">
                                <i class="fas fa-spinner fa-spin mr-1"></i>Procesando...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Mensajes de éxito/error --}}
    @if($mensaje)
        @php
            $alertClass = $tipoMensaje === 'success'
                ? 'success'
                : ($tipoMensaje === 'error' ? 'danger' : 'warning');
            $iconClass = $tipoMensaje === 'success'
                ? 'check-circle'
                : ($tipoMensaje === 'error' ? 'exclamation-circle' : 'exclamation-triangle');
        @endphp
        <div class="alert alert-{{ $alertClass }} alert-dismissible fade show mt-3" role="alert">
            <i class="fas fa-{{ $iconClass }} mr-2"></i>
            {{ $mensaje }}
            <button
                type="button"
                class="close"
                wire:click="$set('mensaje', '')"
                aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
</div>

@push('js')
<script>
    document.addEventListener('livewire:init', () => {
        // Configuración base para toasts (sin allowOutsideClick que es incompatible)
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        Livewire.on('entrada-registrada', (data) => {
            const persona = data.persona || 'la persona';
            const sede = data.sede || 'la sede';
            
            Toast.fire({
                icon: 'success',
                title: '¡Entrada Registrada!',
                text: `La entrada de ${persona} en ${sede} ha sido registrada correctamente.`,
                timer: 3000
            });
            
            // Forzar actualización del componente después de un breve delay
            setTimeout(() => {
                @this.$refresh();
            }, 500);
        });

        Livewire.on('salida-registrada', (data) => {
            const persona = data.persona || 'la persona';
            const sede = data.sede || 'la sede';
            
            Toast.fire({
                icon: 'success',
                title: '¡Salida Registrada!',
                text: `La salida de ${persona} de ${sede} ha sido registrada correctamente.`,
                timer: 3000
            });
            
            // Forzar actualización del componente después de un breve delay
            setTimeout(() => {
                @this.$refresh();
            }, 500);
        });

        Livewire.on('mostrar-mensaje', (data) => {
            const tipo = data.tipo === 'success' ? 'success' : (data.tipo === 'error' ? 'error' : 'warning');
            
            Toast.fire({
                icon: tipo,
                title: data.tipo === 'success' ? '¡Éxito!' : (data.tipo === 'error' ? 'Error' : 'Atención'),
                text: data.mensaje,
                timer: 5000
            });
        });
    });
</script>
@endpush
