@extends('layouts.admin.layout.index')

@php
    $main = 'stock-card';
@endphp

@section('title', Str::headline("Detail $main") . ' - ')

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
                    <li class="breadcrumb-item active">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($main) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Detail ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <x-card-data-table title="{{ 'detail ' . $main . ' ' . ($warehouse->nama ?? '') }}">
        <x-slot name="header_content">
            <h4>{{ $item->nama }}</h4>
            <h6>{{ $item->kode }} - {{ $item->unit->name }}</h6>
            {{-- <br> --}}
            <div class="row">
                <div class="col-md-6">
                    <h4>&nbsp;</h4>
                    <div class="row">
                        <div class="col-3">
                            <p>STOCK</p>
                        </div>
                        <div class="col-3">
                            <p><b>{{ floatDotFormat($item->mainStock($warehouse->id ?? '')) }} {{ $item->unit->name }}</b></p>
                        </div>
                    </div>
                </div>
        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')

            <div class="row mt-3">
                <x-table id="table-stock">
                    <x-slot name="table_head">
                        <th>{{ Str::headline('#') }}</th>
                        <th>{{ Str::headline('Tanggal') }}</th>
                        <th>{{ Str::headline('Dokumen') }}</th>
                        <th>{{ Str::headline('Stock Awal') }}</th>
                        <th>{{ Str::headline('Masuk') }}</th>
                        <th>{{ Str::headline('Keluar') }}</th>
                        <th>{{ Str::headline('Stock Akhir') }}</th>
                        <th>{{ Str::headline('Vendor') }}</th>
                        {{-- <th>{{ Str::headline('Supp./Cust.') }}</th> --}}
                        <th>{{ Str::headline('Keterangan') }}</th>
                    </x-slot>
                    <x-slot name="table_body">

                    </x-slot>
                </x-table>
            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="d-flex justify-content-end gap-1">
                <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />
            </div>
        </x-slot>

    </x-card-data-table>
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script>
        $(document).ready(() => {
            const table = $('#table-stock').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route("admin.$main.show", ['id' => $item->id, 'warehouse_id' => $warehouse->id ?? 'null']) }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'document',
                        name: 'document'
                    },
                    {
                        data: 'stock_before',
                        name: 'stock_before'
                    },
                    {
                        data: 'in',
                        name: 'in'
                    },
                    {
                        data: 'out',
                        name: 'out'
                    },
                    {
                        data: 'left',
                        name: 'left'
                    },
                    {
                        data: 'vendor',
                        name: 'vendor'
                    },
                    // {
                    //     data: 'from',
                    //     name: 'from'
                    // },
                    {
                        data: 'note',
                        name: 'note'
                    },
                ]
            });
            $('table').css('width', '100%');
        });
    </script>
    <script>
        sidebarMenuOpen('#stock-sidebar');
        // sidebarMenuOpen('#master-user-sidebar');
        sidebarActive('#stock-card');
    </script>
@endsection
