@extends('layouts.admin.layout.index')

@php
    $main = 'sales-order';
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

        if (Auth::user()->hasPermissionTo('view sales-order')) {
            $tab_active = 'trading';
        }
        if (!Auth::user()->hasPermissionTo('view sales-order') && Auth::user()->hasPermissionTo('view sales-order-general')) {
            $tab_active = 'general';
        }
    @endphp

    @canany(["view $main", "pairing $main"])
        <div class="box">
            <div class="box-body border-0">
                <ul class="nav nav-tabs customtab2" role="tablist">
                    @can("view $main")
                        <li class="nav-item">
                            <a class="nav-link rounded active" data-bs-toggle="tab" href="#sales-order-tab" role="tab">
                                <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                                <span class="hidden-xs-down">Sale Order</span>
                            </a>
                        </li>
                    @endcan
                    @can("pairing $main")
                        <li class="nav-item">
                            <a class="nav-link rounded" data-bs-toggle="tab" href="#pairing-sale-order" id="tab-pairing-btn" role="tab">
                                <span class="hidden-sm-up"><i class="fa-solid fa-link"></i></span>
                                <span class="hidden-xs-down">Pairing</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </div>
        </div>
    @endcanany

    <div class="tab-content">
        <div class="tab-pane active" id="sales-order-tab" role="tabpanel">
            @canany(["view $main", 'view sales-order-general'])
                <x-card-data-table title="{{ $main }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        @include('components.validate-error')
                        <ul class="nav nav-tabs customtab2 mb-10" role="tablist">
                            @can('view sales-order')
                                <li class="nav-item">
                                    <a class="nav-link rounded {{ $tab_active == 'trading' ? 'active' : '' }}" data-bs-toggle="tab" href="#trading-tab" id="trading-btn" role="tab">
                                        <span class="hidden-sm-up"><i class="fa-solid fa-link"></i></span>
                                        <span class="hidden-xs-down">Trading</span>
                                    </a>
                                </li>
                            @endcan
                            @can('view sales-order-general')
                                <li class="nav-item">
                                    <a class="nav-link rounded {{ $tab_active == 'general' ? 'active' : '' }}" data-bs-toggle="tab" href="#general-tab" id="general-btn" role="tab">
                                        <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                                        <span class="hidden-xs-down">General</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                        <div class="tab-content mt-30">
                            <div class="tab-pane {{ $tab_active == 'trading' ? 'active' : '' }}" id="trading-tab" role="tabpanel">
                                <ul class="nav nav-tabs customtab2 mb-10" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link rounded active" data-bs-toggle="tab" href="#trading-base" id="trading-base-btn" role="tab">
                                            <span class="hidden-sm-up"><i class="fa-solid fa-link"></i></span>
                                            <span class="hidden-xs-down">Sale Order List</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link rounded" data-bs-toggle="tab" href="#trading-not-have-invoice" id="trading-not-have-invoice-btn" role="tab">
                                            <span class="hidden-sm-up"><i class="fa-solid fa-link"></i></span>
                                            <span class="hidden-xs-down">Sale Order Belum Invoice</span>
                                        </a>
                                    </li>
                                </ul>
                                <div class="tab-content mt-20">
                                    <div class="tab-pane active" role="tabpanel" id="trading-base">
                                        <div class="row mb-4">
                                            @can("create $main")
                                                <div class="col-md-3 col-xl-2">
                                                    <x-button color="info" icon="plus" label="Create" link='{{ route("admin.$main.create") }}' />
                                                </div>
                                            @endcan
                                        </div>
                                        <div class="row mb-15">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <x-select name="customer" id="sale-order-customer-select" label="customer">

                                                    </x-select>
                                                </div>
                                            </div>
                                            @if (get_current_branch()->is_primary)
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <x-select name="customer" id="sale-order-branch-select" label="branch">

                                                        </x-select>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="col-md-2">
                                                <x-select name="customer" id="sale-order-status-select" label="status">
                                                    <option value="">Pilih item</option>
                                                    @foreach (status_sale_orders() as $key => $item)
                                                        <option value="{{ $key }}">{{ Str::headline($key) }}</option>
                                                    @endforeach
                                                </x-select>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <x-input class="datepicker-input" name="from_date" label="from date" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" id="sale-order-from-date" required />
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <x-input class="datepicker-input" name="to" label="to date" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" id="sale-order-to-date" required />
                                                </div>
                                            </div>
                                            <div class="col-md-1 row align-self-end">
                                                <div class="form-group">
                                                    <x-button type="submit" color="primary" id="set-sale-order-table" icon="search" fontawesome size="sm" />
                                                </div>
                                            </div>
                                        </div>

                                        <x-table id="sale-order-table">
                                            <x-slot name="table_head">
                                                <th>created_at</th>
                                                <th>{{ Str::headline('#') }}</th>
                                                <th>{{ Str::headline('so number') }}</th>
                                                <th>{{ Str::headline('PO customer') }}</th>
                                                <th>{{ Str::headline('customer') }}</th>
                                                <th>{{ Str::headline('tanggal') }}</th>
                                                <th>{{ Str::headline('qty') }}</th>
                                                <th>{{ Str::headline('qty do') }}</th>
                                                <th>{{ Str::headline('qty dikirim') }}</th>
                                                <th>{{ Str::headline('qty invoice') }}</th>
                                                <th>{{ Str::headline('Status') }}</th>
                                                <th>{{ Str::headline('pairing status') }}</th>
                                                <th>{{ Str::headline('action') }}</th>
                                            </x-slot>
                                            <x-slot name="table_body">

                                            </x-slot>
                                        </x-table>
                                    </div>
                                    <div class="tab-pane" role="tabpanel" id="trading-not-have-invoice">
                                        <div class="row mb-15">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <x-select name="customer" id="sale-order-invoice-customer-select" label="customer">

                                                    </x-select>
                                                </div>
                                            </div>
                                            @if (get_current_branch()->is_primary)
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <x-select name="customer" id="sale-order-invoice-branch-select" label="branch">

                                                        </x-select>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="col-md-2">
                                                <x-select name="customer" id="sale-order-invoice-status-select" label="status">
                                                    <option value="">Pilih item</option>
                                                    @foreach (status_sale_orders() as $key => $item)
                                                        <option value="{{ $key }}">{{ Str::headline($key) }}
                                                        </option>
                                                    @endforeach
                                                </x-select>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <x-input class="datepicker-input" name="from_date" label="from date" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" id="sale-order-invoice-from-date" required />
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <x-input class="datepicker-input" name="to" label="to date" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" id="sale-order-invoice-to-date" required />
                                                </div>
                                            </div>
                                            <div class="col-md-1 row align-self-end">
                                                <div class="form-group">
                                                    <x-button type="submit" color="primary" id="set-sale-order-invoice-table" icon="search" fontawesome size="sm" />
                                                </div>
                                            </div>
                                        </div>
                                        <x-table id="trading-invoice-not-created-table">
                                            <x-slot name="table_head">
                                                <th>{{ Str::headline('#') }}</th>
                                                <th>{{ Str::headline('so number') }}</th>
                                                <th>{{ Str::headline('po customer') }}</th>
                                                <th>{{ Str::headline('customer') }}</th>
                                                <th>{{ Str::headline('tanggal') }}</th>
                                                <th>{{ Str::headline('qty') }}</th>
                                                <th>{{ Str::headline('qty do') }}</th>
                                                <th>{{ Str::headline('qty dikirim') }}</th>
                                                <th>{{ Str::headline('Status') }}</th>
                                                <th>{{ Str::headline('pairing status') }}</th>
                                                <th>{{ Str::headline('') }}</th>
                                            </x-slot>
                                            <x-slot name="table_body">

                                            </x-slot>
                                        </x-table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane {{ $tab_active == 'general' ? 'active' : '' }}" id="general-tab" role="tabpanel">
                                <ul class="nav nav-tabs customtab2 mb-10" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link rounded active" data-bs-toggle="tab" href="#general-base" id="general-base-btn" role="tab">
                                            <span class="hidden-sm-up"><i class="fa-solid fa-link"></i></span>
                                            <span class="hidden-xs-down">Sale Order List</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link rounded" data-bs-toggle="tab" href="#general-not-have-invoice" id="general-not-have-invoice-btn" role="tab">
                                            <span class="hidden-sm-up"><i class="fa-solid fa-link"></i></span>
                                            <span class="hidden-xs-down">Sale Order Belum Invoice</span>
                                        </a>
                                    </li>
                                </ul>
                                <div class="tab-content mt-20">
                                    <div class="tab-pane active" role="tabpanel" id="general-base">
                                        <div class="row mb-4">
                                            @can('create sales-order-general')
                                                <div class="col-md-3 col-xl-2">
                                                    <x-button color="info" icon="plus" label="Create" link="{{ route('admin.sales-order-general.create') }}" />
                                                </div>
                                            @endcan
                                        </div>
                                        <div class="row mb-15">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <x-select name="customer" id="sale-order-general-customer-select" label="customer">

                                                    </x-select>
                                                </div>
                                            </div>
                                            @if (get_current_branch()->is_primary)
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <x-select name="customer" id="sale-order-general-branch-select" label="branch">

                                                        </x-select>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="col-md-2">
                                                <x-select name="customer" id="sale-order-general-status-select" label="status">
                                                    <option value="">Pilih item</option>
                                                    @foreach (sale_order_general_status() as $key => $item)
                                                        <option value="{{ $key }}">{{ Str::headline($key) }}</option>
                                                    @endforeach
                                                </x-select>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <x-input class="datepicker-input" name="from_date" label="from date" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" id="sale-order-general-from-date" required />
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <x-input class="datepicker-input" name="to" label="to date" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" id="sale-order-general-to-date" required />
                                                </div>
                                            </div>
                                            <div class="col-md-1 row align-self-end">
                                                <div class="form-group">
                                                    <x-button type="submit" color="primary" id="set-sale-order-general-table" icon="search" fontawesome size="sm" />
                                                </div>
                                            </div>
                                        </div>
                                        <x-table id="sale-order-general-table">
                                            <x-slot name="table_head">
                                                {{-- <th>created_at</th> --}}
                                                <th>{{ Str::headline('#') }}</th>
                                                <th>{{ Str::headline('tanggal') }}</th>
                                                <th>{{ Str::headline('kode') }}</th>
                                                <th>{{ Str::headline('po customer') }}</th>
                                                <th>{{ Str::headline('customer') }}</th>
                                                <th>{{ Str::headline('Status') }}</th>
                                                <th>{{ Str::headline('created at') }}</th>
                                                <th>{{ Str::headline('export pdf') }}</th>
                                                <th>{{ Str::headline('') }}</th>
                                            </x-slot>
                                            <x-slot name="table_body">

                                            </x-slot>
                                        </x-table>
                                    </div>
                                    <div class="tab-pane" role="tabpanel" id="general-not-have-invoice">
                                        <div class="row mb-15">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <x-select name="customer" id="sale-order-no-invoice-customer-select" label="customer">

                                                    </x-select>
                                                </div>
                                            </div>
                                            @if (get_current_branch()->is_primary)
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <x-select name="customer" id="sale-order-no-invoice-branch-select" label="branch">

                                                        </x-select>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="col-md-2">
                                                <x-select name="customer" id="sale-order-no-invoice-status-select" label="status">
                                                    <option value="">Pilih item</option>
                                                    @foreach (status_sale_orders() as $key => $item)
                                                        <option value="{{ $key }}">{{ Str::headline($key) }}
                                                        </option>
                                                    @endforeach
                                                </x-select>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <x-input class="datepicker-input" name="from_date" label="from date" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" id="sale-order-no-invoice-from-date" required />
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <x-input class="datepicker-input" name="to" label="to date" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" id="sale-order-no-invoice-to-date" required />
                                                </div>
                                            </div>
                                            <div class="col-md-1 row align-self-end">
                                                <div class="form-group">
                                                    <x-button type="submit" color="primary" id="set-sale-order-no-invoice-table" icon="search" fontawesome size="sm" />
                                                </div>
                                            </div>
                                        </div>
                                        <x-table id="general-invoice-not-created-table">
                                            <x-slot name="table_head">
                                                <th>{{ Str::headline('#') }}</th>
                                                <th>{{ Str::headline('tanggal') }}</th>
                                                <th>{{ Str::headline('kode') }}</th>
                                                <th>{{ Str::headline('po customer') }}</th>
                                                <th>{{ Str::headline('customer') }}</th>
                                                <th>{{ Str::headline('Status') }}</th>
                                                <th>{{ Str::headline('created at') }}</th>
                                            </x-slot>
                                            <x-slot name="table_body">

                                            </x-slot>
                                        </x-table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-slot>
                </x-card-data-table>
            @endcan
        </div>
        <div class="tab-pane" id="pairing-sale-order" role="tabpanel">
            @can("pairing $main")
                <x-card-data-table title="{{ 'pairing ' . $main . ' to purchase order' }}">
                    <x-slot name="header_content">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-input class="datepicker-input" name="from_date" label="from date" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" id="so-pairing-from-date" required />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-input class="datepicker-input" name="to" label="to date" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" id="so-pairing-to-date" required />
                                </div>
                            </div>
                            <div class="col-md-1 row align-self-end">
                                <div class="form-group">
                                    <x-button type="submit" color="primary" id="set-so-pairing-table" size="sm" icon="search" fontawesome />
                                </div>
                            </div>
                        </div>
                    </x-slot>
                    <x-slot name="table_content">
                        <x-table id="pairing">
                            <x-slot name="table_head">
                                <th>{{ Str::headline('#') }}</th>
                                <th>{{ Str::headline('so number') }}</th>
                                <th>{{ Str::headline('item') }}</th>
                                <th>{{ Str::headline('alokasi tersedia') }}</th>
                                <th></th>
                            </x-slot>
                            <x-slot name="table_body">

                            </x-slot>
                        </x-table>
                    </x-slot>

                </x-card-data-table>
            @endcan
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script>
        $(document).ready(() => {
            let pairing_click = false;

            @canany(["view $main", 'view sales-order-general'])
                const setTableTrading = () => {
                    const table = $('table#sale-order-table').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        destroy: true,
                        order: [
                            [0, 'desc']
                        ],
                        ajax: {
                            url: '{{ route("admin.$main.data") }}',
                            data: {
                                from_date: $('#sale-order-from-date').val(),
                                to_date: $('#sale-order-to-date').val(),
                                customer_id: $('#sale-order-customer-select').val(),
                                branch_id: $('#sale-order-branch-select').val(),
                                status: $('#sale-order-status-select').val(),
                            },
                        },
                        columns: [{
                                data: 'created_at',
                                name: 'created_at',
                                visible: false,
                                searchable: false
                            }, {
                                data: 'DT_RowIndex',
                                name: 'DT_RowIndex',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'nomor_so',
                                name: 'nomor_so'
                            },
                            {
                                data: 'nomor_po_external',
                                name: 'nomor_po_external'
                            },
                            {
                                data: 'customer.nama',
                                name: 'customers.nama'
                            },
                            {
                                data: 'tanggal',
                                name: 'tanggal'
                            },
                            {
                                data: 'jumlah',
                                name: 'jumlah'
                            },
                            {
                                data: 'jumlah_sudah_do',
                                name: 'jumlah_sudah_do'
                            },
                            {
                                data: 'jumlah_selesai_dikirim',
                                name: 'jumlah_selesai_dikirim'
                            },
                            {
                                data: 'jumlah_invoice',
                                name: 'jumlah_invoice'
                            },
                            {
                                data: 'status',
                                name: 'status'
                            },
                            {
                                data: 'pairing_status',
                                name: 'pairing_status'
                            },
                            {
                                data: 'action',
                                name: 'action',
                                orderable: false,
                                searchable: false
                            },
                        ]
                    });

                    initSelect2Search('sale-order-customer-select', '{{ route('admin.select.customer') }}', {
                        'id': 'id',
                        'text': 'nama'
                    });

                    initSelect2Search('sale-order-branch-select', '{{ route('admin.select.branch') }}', {
                        'id': 'id',
                        'text': 'name'
                    });

                }
                $('table#sale-order-table').css('width', '100%');

                $('#set-sale-order-table').click(function(e) {
                    e.preventDefault();
                    setTableTrading();
                });

                $('#trading-btn').click(function(e) {
                    e.preventDefault();
                    setTableTrading();
                });

                const setTableTradingDontHaveInvoice = () => {
                    const table = $('table#trading-invoice-not-created-table').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        destroy: true,
                        ajax: {
                            url: '{{ route('admin.sales-order.get-sale-order-trading-invoice') }}',
                            data: {
                                from_date: $('#sale-order-invoice-from-date').val(),
                                to_date: $('#sale-order-invoice-to-date').val(),
                                customer_id: $('#sale-order-invoice-customer-select').val(),
                                branch_id: $('#sale-order-invoice-branch-select').val(),
                                status: $('#sale-order-invoice-status-select').val(),
                            },
                        },
                        order: [
                            [4, 'desc']
                        ],
                        columns: [{
                                data: 'DT_RowIndex',
                                name: 'DT_RowIndex',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'nomor_so',
                                name: 'nomor_so'
                            },
                            {
                                data: 'nomor_po_external',
                                name: 'nomor_po_external'
                            },
                            {
                                data: 'customer.nama',
                                name: 'customer.nama'
                            },
                            {
                                data: 'tanggal',
                                name: 'tanggal'
                            },
                            {
                                data: 'jumlah',
                                name: 'jumlah'
                            },
                            {
                                data: 'jumlah_sudah_do',
                                name: 'jumlah_sudah_do'
                            },
                            {
                                data: 'jumlah_selesai_dikirim',
                                name: 'jumlah_selesai_dikirim'
                            },
                            {
                                data: 'status',
                                name: 'status'
                            },
                            {
                                data: 'pairing_status',
                                name: 'pairing_status'
                            },
                            {
                                data: 'generate_invoice',
                                name: 'generate_invoice',
                                orderable: false,
                                searchable: false
                            }
                        ]
                    });

                    initSelect2Search('sale-order-invoice-customer-select',
                        '{{ route('admin.select.customer') }}', {
                            'id': 'id',
                            'text': 'nama'
                        });

                    initSelect2Search('sale-order-invoice-branch-select',
                        '{{ route('admin.select.branch') }}', {
                            'id': 'id',
                            'text': 'name'
                        });
                };

                $('#trading-not-have-invoice-btn').click(function(e) {
                    e.preventDefault();
                    setTableTradingDontHaveInvoice();
                });

                $('#set-sale-order-invoice-table').click(function(e) {
                    e.preventDefault();
                    setTableTradingDontHaveInvoice();
                });

                {{ $tab_active == 'trading' ? 'setTableTrading()' : '' }}

                const setTablegeneral = () => {
                    const table = $('table#sale-order-general-table').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        destroy: true,
                        order: [
                            [5, 'desc']
                        ],
                        ajax: {
                            url: '{{ route('admin.sale-order-general.data') }}',
                            data: {
                                from_date: $('#sale-order-general-from-date').val(),
                                to_date: $('#sale-order-general-to-date').val(),
                                customer_id: $('#sale-order-general-customer-select').val(),
                                branch_id: $('#sale-order-general-branch-select').val(),
                                status: $('#sale-order-general-status-select').val(),
                            },
                        },
                        columns: [{
                                data: 'DT_RowIndex',
                                name: 'DT_RowIndex',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'tanggal',
                                name: 'tanggal'
                            },
                            {
                                data: 'kode',
                                name: 'kode'
                            },
                            {
                                data: 'no_po_external',
                                name: 'no_po_external'
                            },
                            {
                                data: 'customer',
                                name: 'customer'
                            },
                            {
                                data: 'status',
                                name: 'status'
                            },
                            {
                                data: 'created_at',
                                name: 'created_at'
                            },
                            {
                                data: 'export',
                                name: 'export',
                                searchable: false,
                                orderable: false
                            },
                            {
                                data: 'action',
                                name: 'action',
                                searchable: false,
                                orderable: false
                            }
                        ]
                    });

                    initSelect2Search('sale-order-general-customer-select',
                        '{{ route('admin.select.customer') }}', {
                            'id': 'id',
                            'text': 'nama'
                        });
                    initSelect2Search('sale-order-general-branch-select',
                        '{{ route('admin.select.branch') }}', {
                            'id': 'id',
                            'text': 'name'
                        });
                }
                $('table#sale-order-general-table').css('width', '100%');

                $('#set-sale-order-general-table').click(function(e) {
                    e.preventDefault();
                    setTablegeneral()
                });

                $('#general-btn').click(function(e) {
                    e.preventDefault();
                    setTablegeneral()
                });

                const setTableGeneralDontHaveInvoice = () => {
                    const table = $('table#general-invoice-not-created-table').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        destroy: true,
                        order: [
                            [5, 'desc']
                        ],
                        ajax: {
                            url: '{{ route('admin.sales-order-general.get-sale-order-invoice') }}',
                            data: {
                                from_date: $('#sale-order-no-invoice-from-date').val(),
                                to_date: $('#sale-order-no-invoice-to-date').val(),
                                customer_id: $('#sale-order-no-invoice-customer-select').val(),
                                branch_id: $('#sale-order-no-invoice-branch-select').val(),
                                status: $('#sale-order-no-invoice-status-select').val(),
                            },
                        },
                        columns: [{
                                data: 'DT_RowIndex',
                                name: 'DT_RowIndex',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'tanggal',
                                name: 'tanggal'
                            },
                            {
                                data: 'kode',
                                name: 'kode'
                            },
                            {
                                data: 'no_po_external',
                                name: 'no_po_external'
                            },
                            {
                                data: 'customer',
                                name: 'customer'
                            },
                            {
                                data: 'status',
                                name: 'status'
                            },
                            {
                                data: 'created_at',
                                name: 'created_at'
                            }
                        ]
                    });

                    initSelect2Search('sale-order-no-invoice-customer-select',
                        '{{ route('admin.select.customer') }}', {
                            'id': 'id',
                            'text': 'nama'
                        });

                    initSelect2Search('sale-order-no-invoice-branch-select',
                        '{{ route('admin.select.branch') }}', {
                            'id': 'id',
                            'text': 'name'
                        });
                };

                $('#general-not-have-invoice-btn').click(function(e) {
                    e.preventDefault();
                    setTableGeneralDontHaveInvoice();
                });

                $('#set-sale-order-no-invoice-table').click(function(e) {
                    e.preventDefault();
                    setTableGeneralDontHaveInvoice();
                });

                {{ $tab_active == 'general' ? 'setTablegeneral()' : '' }}
            @endcanany

            @can("pairing $main")
                $('#tab-pairing-btn').click(function(e) {
                    if (!pairing_click) {
                        pairing_click = true;

                        const tableSoPairing = () => {
                            $('table#pairing').DataTable({
                                processing: true,
                                serverSide: true,
                                responsive: true,
                                destroy: true,
                                ajax: {
                                    url: '{{ route('admin.pairing.so_not_pairing_completely') }}',
                                    data: {
                                        from_date: $('#so-pairing-from-date').val(),
                                        to_date: $('#so-pairing-to-date').val()
                                    }
                                },
                                columns: [{
                                        data: 'DT_RowIndex',
                                        name: 'DT_RowIndex',
                                        orderable: false,
                                        searchable: false
                                    },
                                    {
                                        data: 'nomor_so',
                                        name: 'nomor_so'
                                    },
                                    {
                                        data: 'item',
                                        name: 'item'
                                    },
                                    {
                                        data: 'alokasi_tersedia',
                                        name: 'alokasi_tersedia'
                                    },
                                    // {data: 'type', name: 'type'}  ,
                                    {
                                        data: 'action',
                                        name: 'action',
                                        orderable: false,
                                        searchable: false
                                    },
                                ]
                            });
                            $('table#pairing').css('width', '100%');
                        }

                        tableSoPairing()

                        $('#set-so-pairing-table').click(function(e) {
                            e.preventDefault();
                            tableSoPairing()
                        })
                    }
                });
            @endcan
        });
    </script>
    <script>
        sidebarMenuOpen('#trading');
        sidebarActive('#sales-order')
    </script>
@endsection
