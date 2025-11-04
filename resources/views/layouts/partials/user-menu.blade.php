{{-- User Menu --}}
<li class="nav-item dropdown user-menu">
    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
        <i class="far fa-user"></i>
        <span>{{ Auth::user()->name ?? 'Usuario' }}</span>
    </a>
    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        {{-- User image --}}
        <li class="user-header">
            <i class="fas fa-user-circle fa-4x"></i>
            <p>
                {{ Auth::user()->name ?? 'Usuario' }}
                <small>{{ Auth::user()->roles->pluck('name')->first() ?? 'Rol no asignado' }}</small>
            </p>
        </li>
        {{-- Menu Footer--}}
        <li class="user-footer">
            <a href="{{ route('profile.index') }}" class="btn btn-default btn-flat">
                <i class="fas fa-user-cog"></i> Perfil
            </a>
            <a href="{{ route('logout') }}" class="btn btn-default btn-flat float-right">
                <i class="fas fa-sign-out-alt"></i> Cerrar sesión
            </a>
        </li>
    </ul>
</li>
