@extends('layouts.admin.layout.index')

@php
    $main = 'role';
    $title = 'akses';
@endphp

@section('title', Str::headline("Edit $title") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($title) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Edit ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can('edit role')
        <x-card-data-table title="{{ 'edit ' . $title }}">
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
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-user-sidebar');
        sidebarActive('#role-sidebar')
    </script>
@endsection
