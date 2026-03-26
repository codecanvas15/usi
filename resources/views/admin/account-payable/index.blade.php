@extends('layouts.admin.layout.index')

@php
    $main = 'account-payable';
    $menu = 'pembayaran supplier';
@endphp

@section('title', Str::headline($menu) . ' - ')

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
                        {{ Str::headline($menu) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <x-card-data-table title="{{ $menu }}">
        <x-slot name="header_content">
            <div class="row justify-content-between mb-4">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <x-select name="vendor_id" id="vendor_id" label="Vendor" onclick="initVendor()">
                        </x-select>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-input class="datepicker-input" id="from_date" name="from_date" label="tanggal awal" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" required />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-input class="datepicker-input" id="to_date" name="to_date" label="tanggal akhir" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                        </div>
                    </div>
                    <div class="col-md-3 row align-self-end">
                        <div class="form-group">
                            <x-button type="button" color="info" id="set-service-table" icon="search" fontawesome onclick="table.ajax.reload()" />
                            @can("create $main")
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
                    <th></th>
                    <th>{{ Str::headline('#') }}</th>
                    <th>{{ Str::headline('no') }}</th>
                    <th>{{ Str::headline('tanggal') }}</th>
                    <th>{{ Str::headline('customer') }}</th>
                    <th>{{ Str::headline('currency') }}</th>
                    <th>{{ Str::headline('total') }}</th>
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
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('js/admin/account-payable/datatable.js?v=1.1') }}"></script>
    <script>
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarActive('#outgoing-payment-sidebar');
        sidebarActive('#account-payable');
        initSelect2Search('vendor_id', `{{ route('admin.select.vendor') }}`, {
            id: "id",
            text: "nama"
        });
    </script>
@endsection
