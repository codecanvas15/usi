@extends('layouts.admin.layout.index')

@php
    $main = 'attendance';
    $title = 'presensi';
@endphp

@section('title', Str::headline("Tambah $title") . ' - ')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.12.1/datatables.min.css" />
@endsection

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($title) }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        {{ Str::headline("Tambah $title") }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can('create presensi')
        <x-card-data-table :title="'Tambah ' . $title">
            <x-slot name="header_content">

            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')
                @include("admin.$main.__create_bulk")
            </x-slot>

        </x-card-data-table>
    @endcan
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script>
        sidebarMenuOpen('#hrd');
        sidebarMenuOpen('#hrd-permission-sidebar');
        sidebarActive('#{{ $main }}');
    </script>
@endsection
