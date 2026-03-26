@extends('layouts.admin.layout.index')

@php
    $main = 'tax-reconciliation';
    $title = 'rekonsiliasi pajak';
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
                    <div class="col row align-self-end">
                        <div class="form-group">
                            <x-button type="button" color="info" id="set-service-table" icon="search" fontawesome onclick="table.ajax.reload()" />
                            @can("create $main")
                                <x-button color="info" icon="plus" label="tambah" link='{{ route("admin.$main.create") }}' />
                            @endcan
                            <x-button color="success" fontawesome label="reset rekonsiliasi pajak" dataToggle="modal" dataTarget="#regenerate-modal" />
                            <x-modal title="print purchase order" id="regenerate-modal" headerColor="success">
                                <x-slot name="modal_body">
                                    <form action='{{ route('admin.tax-reconciliation.regenerate') }}' method="post">
                                        @csrf
                                        <div class="form-group">
                                            <x-input type="text" id="period" label="period" name="period" class="month-year-picker-input" />
                                            <span class="text-danger">Jika periode tidak dipilih, maka semua periode yang belum closing akan di refresh</span>
                                        </div>
                                        <div class="mt-10 border-top pt-10">
                                            <x-button type="button" color="secondary" dataDismiss="modal" label="Batal" size="sm" icon="times" fontawesome />
                                            <x-button type="submit" color="primary" label="Ya, Yakin" size="sm" icon="save" fontawesome />
                                        </div>
                                    </form>
                                </x-slot>
                            </x-modal>
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
                    <th>{{ Str::headline('masa pajak') }}</th>
                    <th>{{ Str::headline('code') }}</th>
                    <th>{{ Str::headline('masukan') }}</th>
                    <th>{{ Str::headline('keluaran') }}</th>
                    <th>{{ Str::headline('status') }}</th>
                    <th>{{ Str::headline('action') }}</th>
                </x-slot>
                <x-slot name="table_body">
                </x-slot>
            </x-table>
        </x-slot>

    </x-card-data-table>
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('js/admin/tax-reconciliation/datatable.js') }}"></script>
    <script>
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarActive('#tax-reconciliation');
    </script>
@endsection
