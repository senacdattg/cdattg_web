@php
    $usuario = Auth::user();
    $nombreUsuario = $usuario?->name ?? 'Usuario';
    $rolUsuario = $usuario?->roles->pluck('name')->first() ?? 'Rol no asignado';
    $fechaIngreso = $usuario?->created_at?->translatedFormat('F Y');
    $avatarLetra = strtoupper(mb_substr($nombreUsuario, 0, 1));
    $avatarBg = ['bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger'][crc32($nombreUsuario) % 5];
@endphp

<li class="nav-item dropdown user-menu">
    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="user-avatar user-avatar-sm {{ $avatarBg }}">{{ $avatarLetra }}</span>
        <span class="d-none d-md-inline">{{ $nombreUsuario }}</span>
    </a>
    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <li class="user-header bg-gradient-success text-white">
            <span class="user-avatar user-avatar-lg bg-white text-success">{{ $avatarLetra }}</span>
            <p class="mt-2 mb-0">
                {{ $nombreUsuario }}
                <small class="d-block">
                    {{ $rolUsuario }}
                    @if ($fechaIngreso)
                        · Miembro desde {{ $fechaIngreso }}
                    @endif
                </small>
            </p>
        </li>

        <li class="user-footer">
            <a href="{{ route('profile.index') }}" class="btn btn-outline-success btn-flat">
                <i class="fas fa-user-cog mr-1"></i> Perfil
            </a>
            <a href="#!" class="btn btn-outline-danger btn-flat float-right"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt mr-1"></i> Cerrar sesióna
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </li>
    </ul>
</li>
