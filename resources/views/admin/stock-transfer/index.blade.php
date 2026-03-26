@extends('layouts.admin.layout.index')

@php
    $main = 'stock-transfer';
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
    @php
        $tab_active = '';

        if (Auth::user()->hasPermissionTo('view stock-transfer')) {
            $tab_active = 'stock-transfer';
        }
        if (!Auth::user()->hasPermissionTo('view stock-transfer') && Auth::user()->hasPermissionTo('view stock-transfer-receiving')) {
            $tab_active = 'stock-transfer-receiving';
        }
    @endphp
    @can("view $main")
        <x-card-data-table title="{{ $main }}">
            <x-slot name="header_content">
                @can("create $main")
                    <div class="row justify-content-between mb-4">
                        <div class="col-md-3 col-md-6 col-xl-4">
                            <x-button color="info" icon="plus" label="Create" link='{{ route("admin.$main.create") }}' />
                        </div>
                    </div>
                @endcan
                <div class="row align-items-center" id="stock_transfer">
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-input class="datepicker-input" name="from_date" label="from date" value="" id="stock-transfer-from-date" required />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-input class="datepicker-input" name="to" label="to date" value="" id="stock-transfer-to-date" required />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-button type="submit" color="primary" id="stock-transfer-table" icon="search" fontawesome />
                        </div>
                    </div>
                </div>
                <div class="row align-items-center" id="stock_transfer_receiving">
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-input class="datepicker-input" name="from_date" label="from date" value="" id="stock-transfer-receiving-from-date" required />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-input class="datepicker-input" name="to" label="to date" value="" id="stock-transfer-receiving-to-date" required />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-button type="submit" color="primary" id="stock-transfer-receiving-table" icon="search" fontawesome />
                        </div>
                    </div>
                </div>
            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')

                <ul class="nav nav-tabs customtab2 mb-10" role="tablist">
                    @can('view stock-transfer')
                        <li class="nav-item {{ $tab_active == 'stock-transfer' ? 'active' : '' }}">
                            <a class="nav-link rounded {{ $tab_active == 'stock-transfer' ? 'active' : '' }}" data-bs-toggle="tab" href="#stock-transfer-tab" id="stock-transfer-btn" role="tab">
                                <span class="hidden-sm-up"><i class="fa-solid fa-link"></i></span>
                                <span class="hidden-xs-down">Stock Transfer</span>
                            </a>
                        </li>
                    @endcan
                    @can('view stock-transfer-receiving')
                        <li class="nav-item">
                            <a class="nav-link rounded {{ $tab_active == 'stock-transfer-receiving' ? 'active' : '' }}" data-bs-toggle="tab" href="#stock-transfer-receiving-tab" id="stock-transfer-receiving-btn" role="tab">
                                <span class="hidden-sm-up"><i class="fa-solid fa-link"></i></span>
                                <span class="hidden-xs-down">Waiting Receiving Report</span>
                            </a>
                        </li>
                    @endcan
                </ul>

                <div class="tab-content mt-30">
                    @can('view stock-transfer')
                        <div class="tab-pane {{ $tab_active == 'stock-transfer' ? 'active' : '' }}" id="stock-transfer-tab" role="tabpanel">
                            <x-table id="stock-transfer-table">
                                <x-slot name="table_head">
                                    <th>{{ Str::headline('#') }}</th>
                                    <th>{{ Str::headline('Tanggal') }}</th>
                                    <th>{{ Str::headline('Kode') }}</th>
                                    <th>{{ Str::headline('Dari') }}</th>
                                    <th>{{ Str::headline('Ke') }}</th>
                                    <th>{{ Str::headline('Status') }}</th>
                                    <th>{{ Str::headline('Created By') }}</th>
                                    <th>{{ Str::headline('Keterangan') }}</th>
                                    <th>{{ Str::headline('Action') }}</th>
                                    <th>{{ Str::headline('Export') }}</th>
                                </x-slot>
                                <x-slot name="table_body">

                                </x-slot>
                            </x-table>
                        </div>
                    @endcan
                    @can('view stock-transfer-receiving')
                        <div class="tab-pane {{ $tab_active == 'stock-transfer-receiving' ? 'active' : '' }}" id="stock-transfer-receiving-tab" role="tabpanel">
                            <x-table id="stock-transfer-receiving-table">
                                <x-slot name="table_head">
                                    <th>{{ Str::headline('#') }}</th>
                                    <th>{{ Str::headline('Tanggal') }}</th>
                                    <th>{{ Str::headline('Kode') }}</th>
                                    <th>{{ Str::headline('Dari') }}</th>
                                    <th>{{ Str::headline('Ke') }}</th>
                                    <th>{{ Str::headline('Status') }}</th>
                                    <th>{{ Str::headline('Created By') }}</th>
                                    <th>{{ Str::headline('Keterangan') }}</th>
                                </x-slot>
                                <x-slot name="table_body">

                                </x-slot>
                            </x-table>
                        </div>
                    @endcan
                </div>
            </x-slot>

        </x-card-data-table>
    @endcan
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script>
        $(document).ready(() => {
            let [
                stock_transfer,
                stock_transfer_receiving,
            ] = [
                false,
                false,
            ];

            const initStockTransferTable = () => {
                const table = $('table#stock-transfer-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    destroy: true,
                    order: [
                        [1, 'desc']
                    ],
                    ajax: {
                        url: '{{ route("admin.$main.index") }}',
                        data: {
                            from_date: $('#stock-transfer-from-date').val(),
                            to_date: $('#stock-transfer-to-date').val(),
                        }
                    },
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
                            data: 'code',
                            name: 'code'
                        },
                        {
                            data: 'fromWarehouse.nama',
                            name: 'fromWarehouse.nama'
                        },
                        {
                            data: 'toWarehouse.nama',
                            name: 'toWarehouse.nama'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'creator.name',
                            name: 'creator.name'
                        },
                        {
                            data: 'note',
                            name: 'note'
                        },
                        {
                            data: 'action',
                            searchable: false,
                            orderable: false
                        },
                        {
                            "data": "export",
                            searchable: false,
                            orderable: false
                        },
                    ]
                });

                if (!stock_transfer) {
                    $('table').css('width', '100%');
                    stock_transfer = true;
                }
                return;
            }

            $('#stock-transfer-btn').click(function(e) {
                e.preventDefault();
                initStockTransferTable();
                $('#stock_transfer').show();
                $('#stock_transfer_receiving').hide();
            });
            {{ $tab_active == 'stock-transfer' ? 'initStockTransferTable()' : '' }}

            const initStockTransferReceivingTable = () => {
                const table = $('table#stock-transfer-receiving-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    destroy: true,
                    order: [
                        [1, 'desc']
                    ],
                    ajax: {
                        url: '{{ route('admin.stock-transfer.receiving') }}',
                        data: {
                            from_date: $('#stock-transfer-receiving-from-date').val(),
                            to_date: $('#stock-transfer-receiving-to-date').val(),
                        }
                    },
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
                            data: 'code',
                            name: 'code'
                        },
                        {
                            data: 'fromWarehouse.nama',
                            name: 'fromWarehouse.nama'
                        },
                        {
                            data: 'toWarehouse.nama',
                            name: 'toWarehouse.nama'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'creator.name',
                            name: 'creator.name'
                        },
                        {
                            data: 'note',
                            name: 'note'
                        },
                    ]
                });

                if (!stock_transfer_receiving) {
                    $('table').css('width', '100%');
                    stock_transfer_receiving = true;
                }
                return;
            }


            $('#stock_transfer_receiving').hide();

            $('#stock-transfer-receiving-btn').click(function(e) {
                e.preventDefault();
                initStockTransferReceivingTable();
                $('#stock_transfer').hide();
                $('#stock_transfer_receiving').show();
            });

            $('#stock-waiting-receiving-table').click(function(e) {
                e.preventDefault()
                initStockTransferReceivingTable()
            })

            $('#stock-transfer-table').click(function(e) {
                e.preventDefault()
                initStockTransferTable()
            })
            {{ $tab_active == 'stock-transfer-receiving' ? 'initStockTransferReceivingTable()' : '' }}
        });
    </script>
    <script>
        sidebarMenuOpen('#stock-sidebar');
        // sidebarMenuOpen('#master-user-sidebar');
        sidebarActive('#stock-transfer');
    </script>
@endsection
