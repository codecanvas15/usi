@extends('layouts.admin.layout.index')

@php
    $main = 'invoice';
@endphp

@section('title', Str::headline('Invoice') . ' - ')

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
                        {{ Str::headline('Invoice') }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')

    @php
        $tab_active = '';

        if (Auth::user()->hasPermissionTo('view invoice-trading')) {
            $tab_active = 'trading';
        }
        if (!Auth::user()->hasPermissionTo('view invoice-trading') && Auth::user()->hasPermissionTo('view invoice-general')) {
            $tab_active = 'general';
        }
    @endphp

    <div class="box">
        <div class="box-body border-0">
            <ul class="nav nav-tabs customtab2" role="tablist">
                @can('view invoice-trading')
                    <li class="nav-item">
                        <a class="nav-link rounded {{ $tab_active == 'trading' ? 'active' : '' }}" data-bs-toggle="tab" href="#trading-tab" role="tab">
                            <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                            <span class="hidden-xs-down">Trading</span>
                        </a>
                    </li>
                @endcan
                @can('view invoice-general')
                    <li class="nav-item">
                        <a class="nav-link rounded {{ $tab_active == 'general' ? 'active' : '' }}" data-bs-toggle="tab" href="#general-tab" id="tab-pairing-btn" role="tab">
                            <span class="hidden-sm-up"><i class="fa-solid fa-link"></i></span>
                            <span class="hidden-xs-down">General</span>
                        </a>
                    </li>
                @endcan
                @can('view invoice-down-payment')
                    <li class="nav-item">
                        <a class="nav-link rounded {{ $tab_active == 'down-payment' ? 'active' : '' }}" data-bs-toggle="tab" href="#down-payment-tab" id="tab-pairing-btn" role="tab">
                            <span class="hidden-sm-up"><i class="fa-solid fa-link"></i></span>
                            <span class="hidden-xs-down">Down Payment</span>
                        </a>
                    </li>
                @endcan
            </ul>
        </div>
    </div>

    <div class="tab-content">
        @can('view invoice-trading')
            <div class="tab-pane {{ $tab_active == 'trading' ? 'active' : '' }}" id="trading-tab" role="tabpanel">
                <x-card-data-table title="{{ 'invoice-trading' }}">
                    <x-slot name="header_content">
                        @if (get_current_branch()->is_primary)
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-select name="customer" id="invoice-trading-branch-select" label="branch">

                                        </x-select>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="vendor_id" id="trading-customer-select" label="customer">

                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-select name="status" id="trading-status-select" label="status">
                                        <option value="" selected>Pilih item</option>
                                        @foreach (get_invoice_status() as $key => $item)
                                            <option value="{{ $key }}">{{ Str::headline($key) }}</option>
                                        @endforeach
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-input class="datepicker-input" name="from_date" label="from date" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" id="trading-from-date" required />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-input class="datepicker-input" name="to" label="to date" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" id="trading-to-date" required />
                                </div>
                            </div>
                            <div class="col-md-1 row align-self-end">
                                <div class="form-group">
                                    <x-button type="submit" color="primary" id="set-invoice-trading-table" size="sm" icon="search" fontawesome />
                                </div>
                            </div>
                        </div>
                    </x-slot>
                    <x-slot name="table_content">
                        @include('components.validate-error')

                        <x-table id="trading-table">
                            <x-slot name="table_head">
                                <th>{{ Str::headline('#') }}</th>
                                <th>{{ Str::headline('tanggal invoice') }}</th>
                                <th>{{ Str::headline('kode') }}</th>
                                <th>{{ Str::headline('customer') }}</th>
                                <th>{{ Str::headline('nomor SO') }}</th>
                                <th>{{ Str::headline('po customer') }}</th>
                                <th>{{ Str::headline('status') }}</th>
                                <th>{{ Str::headline('payment status') }}</th>
                                <th>{{ Str::headline('status print') }}</th>
                                <th>{{ Str::headline('dibuat pada') }}</th>
                                <th>{{ Str::headline('') }}</th>
                            </x-slot>
                            <x-slot name="table_body">

                            </x-slot>
                        </x-table>
                    </x-slot>

                </x-card-data-table>
            </div>
        @endcan
        @can('view invoice-general')
            <div class="tab-pane {{ $tab_active == 'general' ? 'active' : '' }}" id="general-tab" role="tabpanel">
                <x-card-data-table title="{{ 'Invoice General' }}">
                    <x-slot name="header_content">
                        @if (get_current_branch()->is_primary)
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-select name="customer" id="invoice-general-branch-select" label="branch">

                                        </x-select>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="vendor_id" id="general-customer-select" label="customer">

                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-select name="status" id="general-status-select" label="status">
                                        <option value="" selected>Pilih item</option>
                                        @foreach (get_invoice_status() as $key => $item)
                                            <option value="{{ $key }}">{{ Str::headline($key) }}</option>
                                        @endforeach
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-input class="datepicker-input" name="from_date" label="from date" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" id="general-from-date" required />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-input class="datepicker-input" name="to" label="to date" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" id="general-to-date" required />
                                </div>
                            </div>
                            <div class="col-md-1 row align-self-end">
                                <div class="form-group">
                                    <x-button type="submit" color="primary" id="set-invoice-general-table" size="sm" icon="search" fontawesome />
                                </div>
                            </div>
                            <div class="col-md-1 row align-self-end">
                                <div class="form-group">
                                    @can('create invoice-general')
                                        <x-button label="tambah invoice" color="info" size="sm" icon="plus" fontawesome link="{{ route('admin.invoice-general.create') }}" />
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </x-slot>
                    <x-slot name="table_content">
                        <x-table id="general-table">
                            <x-slot name="table_head">
                                <th>{{ Str::headline('#') }}</th>
                                <th>{{ Str::headline('tanggal') }}</th>
                                <th>{{ Str::headline('kode') }}</th>
                                <th>{{ Str::headline('faktur pajak') }}</th>
                                <th>{{ Str::headline('customer') }}</th>
                                <th>{{ Str::headline('sale order general') }}</th>
                                <th>{{ Str::headline('no po external') }}</th>
                                <th>{{ Str::headline('status') }}</th>
                                <th>{{ Str::headline('payment status') }}</th>
                                <th>{{ Str::headline('dibuat pada') }}</th>
                                <th>{{ Str::headline('export') }}</th>
                                <th>{{ Str::headline('') }}</th>
                                <th>{{ Str::headline('status print') }}</th>
                                {{-- <th>{{ Str::headline('') }}</th> --}}
                            </x-slot>
                            <x-slot name="table_body">

                            </x-slot>
                        </x-table>
                    </x-slot>
                </x-card-data-table>
            </div>
        @endcan
        @can('view invoice-down-payment')
            <div class="tab-pane {{ $tab_active == 'down-payment' ? 'active' : '' }}" id="down-payment-tab" role="tabpanel">
                <x-card-data-table title="{{ 'invoice down-payment' }}">
                    <x-slot name="header_content">
                        @if (get_current_branch()->is_primary)
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-select name="customer" id="invoice-down-payment-branch-select" label="branch">

                                        </x-select>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="vendor_id" id="down-payment-customer-select" label="customer">

                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-select name="status" id="down-payment-status-select" label="status">
                                        <option value="" selected>Pilih item</option>
                                        @foreach (get_invoice_status() as $key => $item)
                                            <option value="{{ $key }}">{{ Str::headline($key) }}</option>
                                        @endforeach
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-input class="datepicker-input" name="from_date" label="from date" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" id="down-payment-from-date" required />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-input class="datepicker-input" name="to" label="to date" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" id="down-payment-to-date" required />
                                </div>
                            </div>
                            <div class="col-md-1 row align-self-end">
                                <div class="form-group">
                                    <x-button type="submit" color="primary" id="set-invoice-down-payment-table" size="sm" icon="search" fontawesome />
                                </div>
                            </div>
                            <div class="col-md-1 row align-self-end">
                                <div class="form-group">
                                    @can('create invoice-down-payment')
                                        <x-button label="tambah invoice" color="info" size="sm" icon="plus" fontawesome link="{{ route('admin.invoice-down-payment.create') }}" />
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </x-slot>
                    <x-slot name="table_content">
                        <x-table id="down-payment-table">
                            <x-slot name="table_head">
                                <th>{{ Str::headline('#') }}</th>
                                <th>{{ Str::headline('tanggal') }}</th>
                                <th>{{ Str::headline('kode') }}</th>
                                <th>{{ Str::headline('kode so') }}</th>
                                <th>{{ Str::headline('customer') }}</th>
                                <th>{{ Str::headline('uang muka') }}</th>
                                <th>{{ Str::headline('status') }}</th>
                                <th>{{ Str::headline('payment status') }}</th>
                                <th>{{ Str::headline('export') }}</th>
                            </x-slot>
                            <x-slot name="table_body">

                            </x-slot>
                        </x-table>
                    </x-slot>
                </x-card-data-table>
            </div>
        @endcan

    </div>

@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script>
        @can('view invoice-trading')
            const setTableTrading = () => {
                $('table#trading-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    destroy: true,
                    order: [
                        [9, 'desc']
                    ],
                    ajax: {
                        url: '{{ route('admin.invoice-trading.index') }}',
                        data: {
                            branch_id: $('#invoice-trading-branch-select').val(),
                            customer_id: $('#trading-customer-select').val(),
                            status: $('#trading-status-select').val(),
                            from_date: $('#trading-from-date').val(),
                            to_date: $('#trading-to-date').val(),
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
                            data: 'kode',
                            name: 'kode'
                        },
                        {
                            data: 'so_trading.customer.nama',
                            name: 'so_trading.customer.nama'
                        },
                        {
                            data: 'nomor_so',
                            name: 'sale_orders.nomor_so'
                        },
                        {
                            data: 'nomor_po_external',
                            name: 'sale_orders.nomor_po_external'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'payment_status',
                            name: 'payment_status'
                        },
                        {
                            data: 'print_status',
                            name: 'print_status',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
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

                initSelect2Search('invoice-trading-branch-select', '{{ route('admin.select.branch') }}', {
                    'id': 'id',
                    'text': 'name'
                });
                initSelect2Search('trading-customer-select', '{{ route('admin.select.customer') }}', {
                    'id': 'id',
                    'text': 'nama'
                });
            }
        @endcan

        {{ $tab_active == 'trading' ? 'setTableTrading()' : '' }}

        @can('view invoice-general')
            const setTableGeneral = () => {
                $('table#general-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    destroy: true,
                    order: [
                        [8, 'desc']
                    ],
                    ajax: {
                        url: '{{ route('admin.invoice-general.index') }}',
                        data: {
                            branch_id: $('#invoice-general-branch-select').val(),
                            customer_id: $('#general-customer-select').val(),
                            status: $('#general-status-select').val(),
                            from_date: $('#general-from-date').val(),
                            to_date: $('#general-to-date').val(),
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
                            data: 'reference',
                            name: 'invoice_generals.reference'
                        },
                        {
                            data: 'customer.nama',
                            name: 'customer.nama'
                        },
                        {
                            data: 'sale_order_general.kode',
                            name: 'sale_order_general',
                            searchable: true
                        },
                        {
                            data: 'no_po_external',
                            name: 'no_po_external'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'payment_status',
                            name: 'payment_status'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
                        },
                        {
                            data: 'export',
                            name: 'export'
                        },
                        {
                            data: 'export_with_delivery_order',
                            name: 'export_with_delivery_order'
                        },
                        {
                            data: 'print_status',
                            name: 'print_status',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });
                $('table').css('width', '100%');

                initSelect2Search('invoice-general-branch-select', '{{ route('admin.select.branch') }}', {
                    'id': 'id',
                    'text': 'name'
                });
                initSelect2Search('general-customer-select', '{{ route('admin.select.customer') }}', {
                    'id': 'id',
                    'text': 'nama'
                });
            }
        @endcan

        {{ $tab_active == 'general' ? 'setTableGeneral()' : '' }}

        @can('view invoice-down-payment')
            const setTableDownPayment = () => {
                $('table#down-payment-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    destroy: true,
                    order: [
                        [0, 'desc']
                    ],
                    ajax: {
                        url: '{{ route('admin.invoice-down-payment.index') }}',
                        data: {
                            branch_id: $('#invoice-down-payment-branch-select').val(),
                            customer_id: $('#down-payment-customer-select').val(),
                            status: $('#down-payment-status-select').val(),
                            from_date: $('#down-payment-from-date').val(),
                            to_date: $('#down-payment-to-date').val(),
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'date',
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
                            data: 'so_code',
                            name: 'so_code',
                            orderable: false,
                            searchable: true
                        },
                        {
                            data: 'nama',
                            name: 'customers.nama'
                        },
                        {
                            data: 'down_payment',
                            name: 'down_payment'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'payment_status',
                            name: 'payment_status'
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

                initSelect2Search('invoice-down-payment-branch-select', '{{ route('admin.select.branch') }}', {
                    'id': 'id',
                    'text': 'name'
                });
                initSelect2Search('down-payment-customer-select', '{{ route('admin.select.customer') }}', {
                    'id': 'id',
                    'text': 'nama'
                });
            }
        @endcan

        {{ $tab_active == 'down-payment' ? 'setTableDownPayment()' : '' }}

        $(document).ready(() => {
            setTableTrading();
            setTableGeneral();
            setTableDownPayment();
        });

        $('#set-invoice-trading-table').click(function(e) {
            e.preventDefault();
            setTableTrading();
        });

        $('#set-invoice-general-table').click(function(e) {
            e.preventDefault();
            setTableGeneral();
        });

        $('#set-invoice-down-payment-table').click(function(e) {
            e.preventDefault();
            setTableDownPayment();
        });

        $('#print-out-submit').click(function() {
            setTimeout(() => {
                $('table#general-table').DataTable().ajax.reload();
                $('table#trading-table').DataTable().ajax.reload();
                $('table#down-payment-table').DataTable().ajax.reload();
            }, 500);
        })
    </script>
    <script>
        sidebarMenuOpen('#trading');
        sidebarActive('#invoice-trading')
    </script>
@endsection
