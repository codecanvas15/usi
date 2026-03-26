@extends('layouts.admin.layout.index')

@php
    $main = 'leave';
    $title = 'Cuti/Tidak Masuk';
@endphp

@section('title', Str::headline($main) . ' - ')

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
                        <a href="{{ route('admin.' . $main . '.index') }}">{{ $title }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        {{ Str::headline("Create $title") }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')

    @can("create $main")
        <x-card-data-table title='{{ $title }}'>
            <x-slot name="header_content">

            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')
                @include("admin.$main.__fields")
            </x-slot>

        </x-card-data-table>
    @endcan
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#hrd');
        sidebarMenuOpen('#hrd-permission-sidebar');
        sidebarActive('#cuti')
    </script>
@endsection
