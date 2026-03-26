@extends('layouts.admin.layout.index')

@php
    $main = 'customer';
@endphp

@section('title', Str::headline($main) . ' - ')

@section('css')
    @can("view $main")
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.12.1/datatables.min.css" />
        <style>
            #DataTables_Table_0_wrapper .row:nth-child(1) {
                justify-content: start;
            }
        </style>
    @endcan
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
                        {{ Str::headline($main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("view $main")
        <x-card-data-table title="{{ $main }}">
            <x-slot name="header_content">
                <div class="row justify-content-between mb-4">
                    <div class="col-md-12">
                        @can("create $main")
                            <x-button color="info" icon="plus" label="Create" link='{{ route("admin.$main.create") }}' />
                        @endcan
                        @can("export $main")
                            <x-button link='{{ route("admin.$main.export") }}' color="info" icon="upload" label="export" />
                        @endcan
                        @can("import $main")
                            <x-button link='{{ route("admin.$main.import") }}' color="info" icon="download" label="import" dataToggle="modal" dataTarget="#import-modal" />

                            <x-modal title="import" id="import-modal" headerColor="info">
                                <x-slot name="modal_body">
                                    <x-button link='{{ route("admin.$main.import-format") }}' color="info" icon="download" label="import format" />

                                    <div class="mt-30">
                                        <form action='{{ route("admin.$main.import") }}' method="post" enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-group">
                                                <x-input type="file" label="file" name="file" required />
                                            </div>
                                            <x-button color="info" icon="download" label="import" />
                                        </form>
                                    </div>

                                </x-slot>
                            </x-modal>
                        @endcan
                        @can("import $main")
                            <x-button color="dark" icon="download" label="Saldo Awal" link="{{ route('admin.customer-receivables.create') }}" />
                        @endcan
                    </div>
                </div>
            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')

                <x-table id="table_customer">
                    <x-slot name="table_head">
                        <th>{{ Str::headline('#') }}</th>
                        <th>{{ Str::headline('kode') }}</th>
                        <th>{{ Str::headline('Nama') }}</th>
                        <th>{{ Str::headline('tipe') }}</th>
                        <th>{{ Str::headline('mobile_phone') }}</th>
                        <th>{{ Str::headline('bussiness_phone') }}</th>
                        <th>{{ Str::headline('npwp') }}</th>
                        <th>{{ Str::headline('bank') }}</th>
                        <th></th>
                    </x-slot>
                    <x-slot name="table_body">

                    </x-slot>
                </x-table>
            </x-slot>

        </x-card-data-table>
    @endcan
@endsection

@section('js')
    @can("view $main")
        <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
        <script>
            $(document).ready(() => {
                const table = $('table#table_customer').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    columnDefs: [{
                            "width": "10%",
                            "targets": 1
                        },
                        {
                            "width": "20%",
                            "targets": 3
                        },
                        {
                            "width": "20%",
                            "targets": 4
                        },
                    ],
                    ajax: '{{ route("admin.$main.index") }}',
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'code',
                            name: 'code'
                        },
                        {
                            data: 'nama',
                            name: 'nama'
                        },
                        {
                            data: 'type',
                            name: 'type'
                        },
                        {
                            data: 'mobile_phone',
                            name: 'mobile_phone'
                        },
                        {
                            data: 'bussiness_phone',
                            name: 'bussiness_phone'
                        },
                        {
                            data: 'npwp',
                            name: 'npwp'
                        },
                        {
                            data: 'banks',
                            name: 'id',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });
                $('table').css('width', '100%');
            });
        </script>
    @endcan

    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarActive('#customer-sidebar')
    </script>
@endsection
