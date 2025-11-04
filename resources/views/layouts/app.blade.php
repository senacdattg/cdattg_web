@extends('adminlte::page')

@push('css')
    @vite('resources/css/shared/navbar.css')
@endpush

@section('content_top_nav_right')
    <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button">
            <i class="fas fa-bars"></i>
        </a>
    </li>
    @include('layouts.partials.user-menu')
@endsection
