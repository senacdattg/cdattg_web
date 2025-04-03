<form action="{{ route('logout') }}" method="POST" class="d-inline">
    @csrf
    <button type="submit" class="btn btn-link">
        <i class="fas fa-sign-out-alt"></i> Logout
    </button>
</form>