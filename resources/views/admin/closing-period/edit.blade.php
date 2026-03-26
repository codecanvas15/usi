@extends('layouts.admin.layout.index')

@php
    $main = 'closing-period';
    $title = 'closing';
@endphp

@section('title', Str::headline('edit closing') . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        {{ Str::headline('keuangan & akuntansi') }}
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('edit ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("create $main")
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
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarActive('#closing-period');
    </script>
@endsection
