@extends('layouts.admin.layout.index')

@php
    $main = 'stock-usage';
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
                @can("create $main")
                    <div class="row justify-content-between mb-4">
                        <div class="col-md-3 col-md-6 col-xl-4">
                            <x-button color="info" icon="plus" label="Create" dataToggle="modal" dataTarget="#create-modal" />
                            <x-modal title="create new data" id="create-modal" headerColor="info">
                                <x-slot name="modal_body">
                                    <x-button :link="route('admin.stock-usage.create', ['type' => 'pegawai'])" color="success" label="pegawai" />
                                    <x-button :link="route('admin.stock-usage.create', ['type' => 'kendaraan'])" color="primary" label="kendaraan" />
                                </x-slot>
                            </x-modal>
                        </div>
                    </div>
                @endcan
                <div class="row align-items-center">
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-input class="" name="from_date" label="from date" value="" id="stock-usage-from-date" required />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-input class="datepicker-input" name="to" label="to date" value="" id="stock-usage-to-date" required />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-button type="submit" color="primary" id="stock-usage-table" icon="search" fontawesome />
                        </div>
                    </div>
                </div>
            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')

                <x-table id="stock-usage">
                    <x-slot name="table_head">
                        <th>{{ Str::headline('#') }}</th>
                        <th>{{ Str::headline('Tanggal') }}</th>
                        <th>{{ Str::headline('Gudang') }}</th>
                        <th>{{ Str::headline('Kode') }}</th>
                        <th>{{ Str::headline('Status') }}</th>
                        <th>{{ Str::headline('Keterangan') }}</th>
                        <th>Aksi</th>
                        <th>{{ Str::headline('export') }}</th>
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
    <script>
        $(document).ready(() => {
            const stockUsageTable = () => {
                const table = $('table#stock-usage').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    destroy: true,
                    ajax: {
                        url: '{{ route("admin.$main.index") }}',
                        data: {
                            from_date: $('#stock-usage-from-date').val(),
                            to_date: $('#stock-usage-to-date').val(),
                        }
                    },
                    order: [
                        [1, "desc"]
                    ],
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
                            data: 'ware_house',
                            name: 'ware_house'
                        },
                        {
                            data: 'code',
                            name: 'code'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'note',
                            name: 'note'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'export',
                            name: 'export',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });
                $('table').css('width', '100%');
            }

            stockUsageTable();

            $('#stock-usage-table').click(function(e) {
                e.preventDefault();
                stockUsageTable();
            })
        });
    </script>
    <script>
        sidebarMenuOpen('#stock-sidebar');
        // sidebarMenuOpen('#master-user-sidebar');
        sidebarActive('#stock-usage');
    </script>
@endsection
