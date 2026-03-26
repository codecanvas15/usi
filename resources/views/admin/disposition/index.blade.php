@extends('layouts.admin.layout.index')

@php
    $main = 'disposition';
    $title = 'disposisi aset';
@endphp

@section('title', Str::headline($title) . ' - ')

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
                        {{ Str::headline('asset') }}
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($main) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('tambah ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can('view asset-disposition')
        <x-card-data-table title="{{ $title }}">
            <x-slot name="header_content">
                <div class="row justify-content-between mb-4">
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <div class="form-group">
                                <x-input class="datepicker-input" id="from_date" name="from_date" label="tanggal awal" value="" required />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <x-input class="datepicker-input" id="to_date" name="to" label="tanggal akhir" value="" required />
                            </div>
                        </div>
                        <div class="col-md-3 row align-self-end">
                            <div class="form-group">
                                <x-button type="button" color="info" id="set-service-table" icon="search" fontawesome onclick="table.ajax.reload()" />
                                @can('create asset-disposition')
                                    <x-button color="info" icon="plus" label="tambah" link='{{ route("admin.$main.create") }}' />
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')

                <x-table>
                    <x-slot name="table_head">
                        <th>{{ Str::headline('#') }}</th>
                        <th>{{ Str::headline('tanggal') }}</th>
                        <th>{{ Str::headline('aset') }}</th>
                        <th>{{ Str::headline('nilai akhir') }}</th>
                        <th>{{ Str::headline('nilai jual') }}</th>
                        <th>{{ Str::headline('ket.') }}</th>
                        <th>{{ Str::headline('status') }}</th>
                        <th>{{ Str::headline('action') }}</th>
                    </x-slot>
                    <x-slot name="table_body">
                    </x-slot>
                </x-table>
            </x-slot>

        </x-card-data-table>
    @endcan
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('js/admin/disposition/datatable.js') }}"></script>
    <script>
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarMenuOpen('#asset-finance-sidebar');
        sidebarActive('#disposition');
    </script>
@endsection
