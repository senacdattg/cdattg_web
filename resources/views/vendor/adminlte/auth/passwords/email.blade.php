@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@php
    $passEmailUrl = View::getSection('password_email_url') ?? route('password.email');
    $loginUrl = View::getSection('login_url') ?? route('login.index');
@endphp

@section('auth_header')
    <div class="text-center">
        @if (config('adminlte.auth_logo.enabled', false))
            <img src="{{ asset(config('adminlte.auth_logo.img.path')) }}"
                 alt="{{ config('adminlte.auth_logo.img.alt') }}"
                 @if (config('adminlte.auth_logo.img.class', null))
                    class="{{ config('adminlte.auth_logo.img.class') }} mb-3"
                 @else
                    class="mb-3"
                 @endif
                 style="max-width: 200px; height: auto;">
        @else
            <img src="{{ asset(config('adminlte.logo_img')) }}"
                 alt="{{ config('adminlte.logo_img_alt') }}"
                 class="mb-3"
                 style="max-width: 200px; height: auto;">
        @endif
        <h4 class="mb-0 mt-2">{{ __('adminlte::adminlte.password_reset_message') }}</h4>
    </div>
@stop

@section('auth_body')
    {{-- Mostrar mensajes de éxito o error --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('status') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <strong>Por favor corrija los siguientes errores:</strong>
            <ul class="mb-0 mt-2 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <form action="{{ $passEmailUrl }}" method="post">
        @csrf

        {{-- Email field --}}
        <div class="input-group mb-3">
            <input type="email" name="email" id="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email') }}"
                placeholder="{{ __('adminlte::adminlte.email') }}" autofocus>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Submit button --}}
        <button type="submit" class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
            <span class="fas fa-paper-plane"></span>
            {{ __('adminlte::adminlte.send_password_reset_link') }}
        </button>
    </form>
@stop

@section('auth_footer')
    <p class="my-0">
        <a href="{{ $loginUrl }}">
            {{ __('adminlte::adminlte.i_already_have_a_membership') }}
        </a>
    </p>
@stop
