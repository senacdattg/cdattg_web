@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
    <style>
        @media (max-width: 575.98px) {
            #select-importacion-activa {
                width: 100%;
            }
        }
    </style>
@endsection

@section('content_header')
    <x-page-header icon="fa-file-excel" title="Importar Personas"
        subtitle="Carga masiva desde Excel con validación de duplicados" :breadcrumb="[
            ['label' => 'Inicio', 'url' => route('home.index'), 'icon' => 'fa-home'],
            ['label' => 'Personas', 'url' => route('personas.index'), 'icon' => 'fa-users'],
            ['label' => 'Importar', 'icon' => 'fa-file-excel', 'active' => true],
        ]" />
@endsection

@section('content')
    @livewire('persona-import-component')
@endsection

@section('footer')
    @include('layouts.footer')
@endsection
