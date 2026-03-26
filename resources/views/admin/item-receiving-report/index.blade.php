@extends('layouts.admin.layout.index')

@php
    $main = 'item-receiving-report';
    $title = 'Laporan Penerimaan barang';

    $tab_active = '';

    if (Auth::user()->hasPermissionTo('view item-receiving-report-general')) {
        $tab_active = 'general';
    }
    if (!Auth::user()->hasPermissionTo('view item-receiving-report-general') && Auth::user()->hasPermissionTo('view item-receiving-report-general')) {
        $tab_active = 'service';
    }
    if (!Auth::user()->hasPermissionTo('view item-receiving-report-general') && !Auth::user()->hasPermissionTo('view item-receiving-report-general') && Auth::user()->hasPermissionTo('view item-receiving-report-trading')) {
        $tab_active = 'trading';
    }
    if (!Auth::user()->hasPermissionTo('view item-receiving-report-general') && !Auth::user()->hasPermissionTo('view item-receiving-report-general') && !Auth::user()->hasPermissionTo('view item-receiving-report-trading') && Auth::user()->hasPermissionTo('view item-receiving-report-transport')) {
        $tab_active = 'transport';
    }
@endphp

@section('title', Str::headline($title) . ' - ')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.13.1/datatables.min.css" />
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
                        {{ Str::headline($title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @canany(["view $main", 'view item-receiving-report-general', 'view item-receiving-report-service', 'view item-receiving-report-trading', 'view item-receiving-report-transport'])
        <x-card-data-table>
            <x-slot name="table_content">
                <ul class="nav nav-tabs customtab2 mb-10" role="tablist">
                    @can('view item-receiving-report-general')
                        <li class="nav-item">
                            <a class="nav-link rounded {{ $tab_active == 'general' ? 'active' : '' }}" data-bs-toggle="tab" href="#general-tab" id="general-btn" role="tab">
                                <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                                <span class="hidden-xs-down">General</span>
                            </a>
                        </li>
                    @endcan
                    @can('view item-receiving-report-service')
                        <li class="nav-item">
                            <a class="nav-link rounded {{ $tab_active == 'service' ? 'active' : '' }}" data-bs-toggle="tab" href="#service-tab" id="service-btn" role="tab">
                                <span class="hidden-sm-up"><i class="fa-solid fa-link"></i></span>
                                <span class="hidden-xs-down">Service</span>
                            </a>
                        </li>
                    @endcan
                    @can('view item-receiving-report-trading')
                        <li class="nav-item">
                            <a class="nav-link rounded {{ $tab_active == 'trading' ? 'active' : '' }}" data-bs-toggle="tab" href="#trading-tab" id="trading-btn" role="tab">
                                <span class="hidden-sm-up"><i class="fa-solid fa-link"></i></span>
                                <span class="hidden-xs-down">Trading</span>
                            </a>
                        </li>
                    @endcan
                    @can('view item-receiving-report-transport')
                        <li class="nav-item">
                            <a class="nav-link rounded {{ $tab_active == 'transport' ? 'active' : '' }}" data-bs-toggle="tab" href="#transport-tab" id="transport-btn" role="tab">
                                <span class="hidden-sm-up"><i class="fa-solid fa-link"></i></span>
                                <span class="hidden-xs-down">Transport</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </x-slot>
        </x-card-data-table>

        <div class="tab-content mt-30">

            @can('view item-receiving-report-general')
                <div class="tab-pane {{ $tab_active == 'general' ? 'active' : '' }}" id="general-tab" role="tabpanel">

                    <x-card-data-table :title="$title . ' General'">
                        <x-slot name="table_content">
                            @canany(['create item-receiving-report', 'create item-receiving-report-general'])
                                <x-button color="info" icon="plus" label="Create" link="{{ route('admin.item-receiving-report-general.create') }}" />
                            @endcanany

                            <div class="mt-20">
                                <div class="row">
                                    @if (get_current_branch()->is_primary)
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-select name="customer" id="general-branch-inputForm" label="branch">

                                                </x-select>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="row">

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-select name="status" id="general-status-inputForm">
                                                <option value="">Pilih item</option>
                                                @foreach (item_report_status() as $key => $item)
                                                    <option value="{{ $key }}">{{ Str::headline($key) }}</option>
                                                @endforeach
                                            </x-select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <x-select name="vendor_id" label="vendor" id="general-vendor-inputForm"></x-select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="text" class="datepicker-input" name="from_date" label="from date" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" id="general-formDate-inputForm" required />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="text" class="datepicker-input" name="to" label="to date" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" id="general-toDate-inputForm" required />
                                        </div>
                                    </div>
                                    <div class="col-md-1 row align-self-end">
                                        <div class="form-group">
                                            <x-button type="submit" color="primary" id="general-search-btn" size="sm" icon="search" fontawesome />
                                        </div>
                                    </div>

                                </div>

                                <div class="mt-10">
                                    <x-table id="table-general">
                                        <x-slot name="table_head">
                                            <th>{{ Str::headline('#') }}</th>
                                            <th>{{ Str::headline('tanggal diterima') }}</th>
                                            <th>{{ Str::headline('kode') }}</th>
                                            <th>{{ Str::headline('nomor po') }}</th>
                                            <th>{{ Str::headline('vendor ') }}</th>
                                            <th>{{ Str::headline('status') }}</th>
                                            <th>{{ Str::headline('Created At') }}</th>
                                            {{-- <th>Export</th> --}}
                                            <th></th>
                                        </x-slot>
                                        <x-slot name="table_body">
                                        </x-slot>
                                    </x-table>
                                </div>
                            </div>

                        </x-slot>
                    </x-card-data-table>

                </div>
            @endcan

            @can('view item-receiving-report-service')
                <div class="tab-pane {{ $tab_active == 'service' ? 'active' : '' }}" id="service-tab" role="tabpanel">

                    <x-card-data-table title="berita acara serah terima">
                        <x-slot name="table_content">
                            @canany(['create item-receiving-report', 'create item-receiving-report-service'])
                                <x-button color="info" icon="plus" label="Create" link="{{ route('admin.item-receiving-report-service.create') }}" />
                            @endcanany

                            <div class="mt-20">
                                <div class="row">
                                    @if (get_current_branch()->is_primary)
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-select name="customer" id="service-branch-inputForm" label="branch">

                                                </x-select>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="row">

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-select name="status" id="service-status-inputForm">
                                                <option value="">Pilih item</option>
                                                @foreach (item_report_status() as $key => $item)
                                                    <option value="{{ $key }}">{{ Str::headline($key) }}</option>
                                                @endforeach
                                            </x-select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <x-select name="vendor_id" label="vendor" id="service-vendor-inputForm"></x-select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="text" class="datepicker-input" name="from_date" label="from date" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" id="service-formDate-inputForm" required />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="text" class="datepicker-input" name="to" label="to date" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" id="service-toDate-inputForm" required />
                                        </div>
                                    </div>
                                    <div class="col-md-1 row align-self-end">
                                        <div class="form-group">
                                            <x-button type="submit" color="primary" id="service-search-btn" size="sm" icon="search" fontawesome />
                                        </div>
                                    </div>

                                </div>

                                <div class="mt-10">
                                    <x-table id="table-service">
                                        <x-slot name="table_head">
                                            <th>{{ Str::headline('#') }}</th>
                                            <th>{{ Str::headline('tanggal diterima') }}</th>
                                            <th>{{ Str::headline('kode') }}</th>
                                            <th>{{ Str::headline('nomor po') }}</th>
                                            <th>{{ Str::headline('vendor ') }}</th>
                                            <th>{{ Str::headline('status') }}</th>
                                            <th>{{ Str::headline('Created At') }}</th>
                                            {{-- <th>Export</th> --}}
                                            <th></th>
                                        </x-slot>
                                        <x-slot name="table_body">
                                        </x-slot>
                                    </x-table>
                                </div>
                            </div>
                        </x-slot>
                    </x-card-data-table>

                </div>
            @endcan

            @can('view item-receiving-report-trading')
                <div class="tab-pane {{ $tab_active == 'trading' ? 'active' : '' }}" id="trading-tab" role="tabpanel">

                    <x-card-data-table :title="$title . ' Trading'">
                        <x-slot name="table_content">
                            @canany(['create item-receiving-report', 'create item-receiving-report-trading'])
                                <x-button color="info" icon="plus" label="Create" link="{{ route('admin.item-receiving-report-trading.create') }}" />
                            @endcanany
                            <div class="mt-20">
                                <div class="row">
                                    @if (get_current_branch()->is_primary)
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-select name="customer" id="trading-branch-inputForm" label="branch">

                                                </x-select>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="row">

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-select name="status" id="trading-status-inputForm">
                                                <option value="">Pilih item</option>
                                                @foreach (item_report_status() as $key => $item)
                                                    <option value="{{ $key }}">{{ Str::headline($key) }}</option>
                                                @endforeach
                                            </x-select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <x-select name="vendor_id" label="vendor" id="trading-vendor-inputForm"></x-select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="text" class="datepicker-input" name="from_date" label="from date" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" id="trading-formDate-inputForm" required />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="text" class="datepicker-input" name="to" label="to date" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" id="trading-toDate-inputForm" required />
                                        </div>
                                    </div>
                                    <div class="col-md-1 row align-self-end">
                                        <div class="form-group">
                                            <x-button type="submit" color="primary" id="trading-search-btn" size="sm" icon="search" fontawesome />
                                        </div>
                                    </div>

                                </div>

                                <div class="mt-10">
                                    <x-table id="table-trading">
                                        <x-slot name="table_head">
                                            <th>{{ Str::headline('#') }}</th>
                                            <th>{{ Str::headline('tanggal diterima') }}</th>
                                            <th>{{ Str::headline('kode') }}</th>
                                            <th>{{ Str::headline('nomor po') }}</th>
                                            <th>{{ Str::headline('vendor ') }}</th>
                                            <th>{{ Str::headline('status') }}</th>
                                            <th>{{ Str::headline('Created At') }}</th>
                                            {{-- <th>Export</th> --}}
                                            <th></th>
                                        </x-slot>
                                        <x-slot name="table_body">
                                        </x-slot>
                                    </x-table>
                                </div>
                            </div>
                        </x-slot>
                    </x-card-data-table>

                </div>
            @endcan

            @can('view item-receiving-report-transport')
                <div class="tab-pane {{ $tab_active == 'transport' ? 'active' : '' }}" id="transport-tab" role="tabpanel">

                    <x-card-data-table title="berita acara serah terima">
                        <x-slot name="table_content">
                            @canany(['create item-receiving-report', 'create item-receiving-report-transport'])
                                <x-button color="info" icon="plus" label="Create" link="{{ route('admin.item-receiving-report-transport.create') }}" />
                            @endcanany

                            <div class="mt-20">
                                <div class="row">
                                    @if (get_current_branch()->is_primary)
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-select name="customer" id="transport-branch-inputForm" label="branch">

                                                </x-select>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="row">

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-select name="status" id="transport-status-inputForm">
                                                <option value="">Pilih item</option>
                                                @foreach (item_report_status() as $key => $item)
                                                    <option value="{{ $key }}">{{ Str::headline($key) }}</option>
                                                @endforeach
                                            </x-select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <x-select name="vendor_id" label="vendor" id="transport-vendor-inputForm"></x-select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="text" class="datepicker-input" name="from_date" label="from date" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" id="transport-formDate-inputForm" required />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="text" class="datepicker-input" name="to" label="to date" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" id="transport-toDate-inputForm" required />
                                        </div>
                                    </div>
                                    <div class="col-md-1 row align-self-end">
                                        <div class="form-group">
                                            <x-button type="submit" color="primary" id="transport-search-btn" size="sm" icon="search" fontawesome />
                                        </div>
                                    </div>

                                </div>

                                <div class="mt-10">
                                    <x-table id="table-transport">
                                        <x-slot name="table_head">
                                            <th>{{ Str::headline('#') }}</th>
                                            <th>{{ Str::headline('tanggal diterima') }}</th>
                                            <th>{{ Str::headline('kode') }}</th>
                                            <th>{{ Str::headline('nomor po') }}</th>
                                            <th>{{ Str::headline('vendor') }}</th>
                                            <th>{{ Str::headline('customer') }}</th>
                                            <th>{{ Str::headline('status') }}</th>
                                            <th>{{ Str::headline('Created At') }}</th>
                                            {{-- <th>Export</th> --}}
                                            <th></th>
                                        </x-slot>
                                        <x-slot name="table_body">
                                        </x-slot>
                                    </x-table>
                                </div>
                            </div>
                        </x-slot>
                    </x-card-data-table>

                </div>
            @endcan

        </div>
    @endcanany
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>

    <script>
        $(document).ready(function() {
            const generalTable = () => {
                const displayTableGeneral = () => {
                    const table = $('table#table-general').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        destroy: true,
                        ajax: {
                            url: '{{ route('admin.item-receiving-report-general.index') }}',
                            data: {
                                'branch_id': $('#general-branch-inputForm').val(),
                                'status': $('#general-status-inputForm').val(),
                                'vendor_id': $('#general-vendor-inputForm').val(),
                                'from_date': $('#general-formDate-inputForm').val(),
                                'to_date': $('#general-toDate-inputForm').val(),
                            },
                        },
                        order: [
                            [6, 'desc']
                        ],
                        columns: [{
                                data: 'DT_RowIndex',
                                name: 'DT_RowIndex',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'date_receive',
                                name: 'date_receive'
                            },
                            {
                                data: 'kode',
                                name: 'kode'
                            },
                            {
                                data: 'reference_code',
                                name: 'purchase_order_generals.code'
                            },
                            {
                                data: 'vendor.nama',
                                name: 'vendors.nama'
                            },
                            {
                                data: 'status',
                                name: 'status'
                            },
                            {
                                data: 'created_at',
                                name: 'created_at'
                            },
                            // {
                            //     data: 'export',
                            //     name: 'export'
                            // },
                            {
                                data: 'action',
                                name: 'action',
                                orderable: false,
                                searchable: false
                            }
                        ]
                    });

                    $('table#table-general').css('width', '100%');

                    initSelect2SearchPaginationData(`general-branch-inputForm`, `{{ route('admin.select.branch') }}`, {
                        id: 'id',
                        text: 'name'
                    })
                    initSelect2SearchPaginationData(`general-vendor-inputForm`, `{{ route('admin.select.vendor') }}`, {
                        id: 'id',
                        text: 'nama'
                    })
                };

                {{ $tab_active == 'general' ? 'displayTableGeneral()' : '' }}

                $('#general-search-btn').click(function(e) {
                    e.preventDefault();
                    displayTableGeneral();
                });

                $('#general-btn').click(function(e) {
                    e.preventDefault();
                    displayTableGeneral();
                });
            };

            const serviceTable = () => {
                const displayTableService = () => {
                    const table = $('table#table-service').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        destroy: true,
                        ajax: {
                            url: '{{ route('admin.item-receiving-report-service.index') }}',
                            data: {
                                'branch_id': $('#service-branch-inputForm').val(),
                                'status': $('#service-status-inputForm').val(),
                                'vendor_id': $('#service-vendor-inputForm').val(),
                                'from_date': $('#service-formDate-inputForm').val(),
                                'to_date': $('#service-toDate-inputForm').val(),
                            },
                        },
                        order: [
                            [6, 'desc']
                        ],
                        columns: [{
                                data: 'DT_RowIndex',
                                name: 'purchase_order_services.spk_number',
                                orderable: false,
                            },
                            {
                                data: 'date_receive',
                                name: 'date_receive'
                            },
                            {
                                data: 'kode',
                                name: 'kode'
                            },
                            {
                                data: 'reference_code',
                                name: 'purchase_order_services.code'
                            },
                            {
                                data: 'vendor.nama',
                                name: 'vendors.nama'
                            },
                            {
                                data: 'status',
                                name: 'status'
                            },
                            {
                                data: 'created_at',
                                name: 'created_at'
                            },
                            // {
                            //     data: 'export',
                            //     name: 'export'
                            // },
                            {
                                data: 'action',
                                name: 'action',
                                orderable: false,
                                searchable: false
                            }
                        ]
                    });

                    $('table#table-service').css('width', '100%');

                    initSelect2SearchPaginationData(`service-branch-inputForm`, `{{ route('admin.select.branch') }}`, {
                        id: 'id',
                        text: 'name'
                    })
                    initSelect2SearchPaginationData(`service-vendor-inputForm`, `{{ route('admin.select.vendor') }}`, {
                        id: 'id',
                        text: 'nama'
                    })
                };

                {{ $tab_active == 'service' ? 'displayTableService()' : '' }}

                $('#service-search-btn').click(function(e) {
                    e.preventDefault();
                    displayTableService();
                });

                $('#service-btn').click(function(e) {
                    e.preventDefault();
                    displayTableService();
                });
            };

            const tradingTable = () => {
                const displayTableService = () => {
                    const table = $('table#table-trading').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        destroy: true,
                        ajax: {
                            url: '{{ route('admin.item-receiving-report-trading.index') }}',
                            data: {
                                'branch_id': $('#trading-branch-inputForm').val(),
                                'status': $('#trading-status-inputForm').val(),
                                'vendor_id': $('#trading-vendor-inputForm').val(),
                                'from_date': $('#trading-formDate-inputForm').val(),
                                'to_date': $('#trading-toDate-inputForm').val(),
                            },
                        },
                        order: [
                            [6, 'desc']
                        ],
                        columns: [{
                                data: 'DT_RowIndex',
                                name: 'DT_RowIndex',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'date_receive',
                                name: 'date_receive'
                            },
                            {
                                data: 'kode',
                                name: 'kode'
                            },
                            {
                                data: 'reference_code',
                                name: 'purchase_orders.nomor_po'
                            },
                            {
                                data: 'vendor.nama',
                                name: 'vendors.nama'
                            },
                            {
                                data: 'status',
                                name: 'status'
                            },
                            {
                                data: 'created_at',
                                name: 'created_at'
                            },
                            // {
                            //     data: 'export',
                            //     name: 'export'
                            // },
                            {
                                data: 'action',
                                name: 'action',
                                orderable: false,
                                searchable: false
                            }
                        ]
                    });

                    $('table#table-trading').css('width', '100%');

                    initSelect2SearchPaginationData('trading-branch-inputForm', '{{ route('admin.select.branch') }}', {
                        'id': 'id',
                        'text': 'name'
                    });
                    initSelect2SearchPaginationData('trading-vendor-inputForm', '{{ route('admin.select.vendor') }}', {
                        'id': 'id',
                        'text': 'nama'
                    });
                };

                {{ $tab_active == 'trading' ? 'displayTableService()' : '' }}

                $('#trading-search-btn').click(function(e) {
                    e.preventDefault();
                    displayTableService();
                });

                $('#trading-btn').click(function(e) {
                    e.preventDefault();
                    displayTableService();
                });
            };

            const transportTable = () => {
                const displayTableService = () => {
                    const table = $('table#table-transport').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        destroy: true,
                        ajax: {
                            url: '{{ route('admin.item-receiving-report-transport.index') }}',
                            data: {
                                'branch_id': $('#transport-branch-inputForm').val(),
                                'status': $('#transport-status-inputForm').val(),
                                'vendor_id': $('#transport-vendor-inputForm').val(),
                                'from_date': $('#transport-formDate-inputForm').val(),
                                'to_date': $('#transport-toDate-inputForm').val(),
                            },
                        },
                        order: [
                            [6, 'desc']
                        ],
                        columns: [{
                                data: 'DT_RowIndex',
                                name: 'DT_RowIndex',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'date_receive',
                                name: 'date_receive'
                            },
                            {
                                data: 'kode',
                                name: 'kode'
                            },
                            {
                                data: 'reference_code',
                                name: 'purchase_transports.kode'
                            },
                            {
                                data: 'vendor.nama',
                                name: 'vendors.nama'
                            },
                            {
                                data: 'customer_name',
                                name: 'customers.nama'
                            },
                            {
                                data: 'status',
                                name: 'status'
                            },
                            {
                                data: 'created_at',
                                name: 'created_at'
                            },
                            // {
                            //     data: 'export',
                            //     name: 'export'
                            // },
                            {
                                data: 'action',
                                name: 'action',
                                orderable: false,
                                searchable: false
                            }
                        ]
                    });

                    $('table#table-transport').css('width', '100%');

                    initSelect2SearchPaginationData('transport-branch-inputForm', '{{ route('admin.select.branch') }}', {
                        'id': 'id',
                        'text': 'name'
                    });
                    initSelect2SearchPaginationData('transport-vendor-inputForm', '{{ route('admin.select.vendor') }}', {
                        'id': 'id',
                        'text': 'nama'
                    });
                };

                {{ $tab_active == 'transport' ? 'displayTableService()' : '' }}

                $('#transport-search-btn').click(function(e) {
                    e.preventDefault();
                    displayTableService();
                });

                $('#transport-btn').click(function(e) {
                    e.preventDefault();
                    displayTableService();
                });
            };

            generalTable();
            serviceTable();
            tradingTable();
            transportTable();
        });
    </script>

    <script>
        sidebarMenuOpen('#purchase-menu');
        sidebarActive('#item-receiving-report');
    </script>
@endsection
