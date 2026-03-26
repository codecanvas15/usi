@extends('layouts.admin.layout.index')

@php
    $main = 'delivery-order';
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

        if (Auth::user()->hasPermissionTo('view delivery-order')) {
            $tab_active = 'trading';
        }
        if (!Auth::user()->hasPermissionTo('view delivery-order') && Auth::user()->hasPermissionTo('view delivery-order-general')) {
            $tab_active = 'general';
        }
    @endphp

    @canany(['view delivery-order', 'view delivery-order-general'])
        <div class="box">
            <div class="box-body border-0">
                <ul class="nav nav-tabs customtab2" role="tablist">
                    @can("view $main")
                        <li class="nav-item">
                            <a class="nav-link rounded {{ $tab_active == 'trading' ? 'active' : '' }}" data-bs-toggle="tab" href="#trading-tab" role="tab">
                                <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                                <span class="hidden-xs-down">Trading</span>
                            </a>
                        </li>
                    @endcan

                    @can('view delivery-order-general')
                        <li class="nav-item">
                            <a class="nav-link rounded {{ $tab_active == 'general' ? 'active' : '' }}" data-bs-toggle="tab" href="#general-tab" id="tab-pairing-btn" role="tab">
                                <span class="hidden-sm-up"><i class="fa-solid fa-link"></i></span>
                                <span class="hidden-xs-down">General</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </div>
        </div>
    @endcanany
    <div class="tab-content">
        <div class="tab-pane {{ $tab_active == 'trading' ? 'active' : '' }}" id="trading-tab" role="tabpanel">
            @canany(['view delivery-order'])
                <x-card-data-table title="{{ $main . ' Trading' }}">
                    <x-slot name="header_content">
                    </x-slot>
                    <x-slot name="table_content">

                        <ul class="nav nav-tabs customtab2" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link rounded active" data-bs-toggle="tab" href="#delivery-orders-tab" role="tab" id="delivery-orders-tab-btn">
                                    <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                                    <span class="hidden-xs-down">Delivery Orders</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link rounded" data-bs-toggle="tab" href="#sales-orders-tab" role="tab" id="sales-orders-tab-btn">
                                    <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                                    <span class="hidden-xs-down">Sales Order</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link rounded" data-bs-toggle="tab" href="#delivery-order-receiving-tab" role="tab" id="delivery-order-receiving-tab-btn">
                                    <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                                    <span class="hidden-xs-down">Penerimaan Delivery Order</span>
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane active" id="delivery-orders-tab">
                                <div class="row justify-content-between my-4">
                                    <div class="col-md-2 col-md-6 col-xl-4">
                                        @can('create delivery-order')
                                            <x-button color="info" icon="plus" label="Create" link='{{ route("admin.$main.create") }}' />
                                        @endcan
                                    </div>
                                </div>

                                <ul class="nav nav-tabs customtab2" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link rounded active" data-bs-toggle="tab" href="#delivery-orders-not-finished-yet-tab" role="tab" id="delivery-orders-not-finished-yet-tab-btn">
                                            <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                                            <span class="hidden-xs-down">Belum Selesai</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link rounded" data-bs-toggle="tab" href="#delivery-orders-done-tab" role="tab" id="delivery-orders-done-tab-btn">
                                            <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                                            <span class="hidden-xs-down">Selesai</span>
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    <div class="tab-pane active" id="delivery-orders-not-finished-yet-tab">
                                        <div class="mb-4 mt-3">
                                            <div class="row">
                                                @if (get_current_branch()->is_primary)
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <x-select name="customer" id="branch-delivery-orders" label="branch">

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <x-select name="customer" id="customer-delivery-orders" label="customer">

                                                        </x-select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <x-select name="customer" id="shNumber-delivery-orders" label="sh no.">

                                                        </x-select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <x-select name="customer" id="status-delivery-orders" label="status">
                                                            <option value="" selected>Pilih item</option>
                                                            @foreach (get_delivery_order_status() as $key => $item)
                                                                <option value="{{ $key }}">{{ Str::headline($key) }}</option>
                                                            @endforeach
                                                        </x-select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12"></div>

                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <x-input class="datepicker-input" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" helpers="target delivery" label="from date" id="fromDate-delivery-orders" />
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <x-input class="datepicker-input" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" helpers="target delivery" label="to date" id="to-delivery-orders" />
                                                    </div>
                                                </div>
                                                <div class="col-md-1 row align-self-center">
                                                    <div class="form-group">
                                                        <x-button type="submit" size="sm" color="primary" id="set-delivery-orders-table" icon="search" fontawesome />
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <x-table theadColor="" id="list-delivery-orders">
                                            <x-slot name="table_head">
                                                <th></th>
                                                <th>#</th>
                                                <th></th>
                                                <th>{{ Str::headline('customer') }}</th>
                                                <th>{{ Str::headline('Nomor do') }}</th>
                                                <th>{{ Str::headline('Nomor SO') }}</th>
                                                <th>{{ Str::headline('Target Delivery') }}</th>
                                                <th>{{ Str::headline('Tanggal muat') }}</th>
                                                <th>{{ Str::headline('Tanggal bongkar') }}</th>
                                                <th>{{ Str::headline('kuantitas_dikirim') }}</th>
                                                <th>{{ Str::headline('kuantitas_diterima') }}</th>
                                                <th>{{ Str::headline('moda transport') }}</th>
                                                <th>{{ Str::headline('status') }}</th>
                                                <th>{{ Str::headline('Export PDF') }}</th>
                                            </x-slot>
                                            <x-slot name="table_body">

                                            </x-slot>
                                        </x-table>
                                    </div>
                                    <div class="tab-pane" id="delivery-orders-done-tab">
                                        <div class="mb-4 mt-3">
                                            <div class="row">
                                                @if (get_current_branch()->is_primary)
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <x-select name="customer" id="branch-delivery-orders-done" label="branch">

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <x-select name="customer" id="customer-delivery-orders-done" label="customer">

                                                        </x-select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <x-select name="customer" id="shNumber-delivery-orders-done" label="sh no.">

                                                        </x-select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <x-select name="customer" id="status-delivery-orders-done" label="status">
                                                            <option value="" selected>Pilih item</option>
                                                            @foreach (get_delivery_order_status() as $key => $item)
                                                                <option value="{{ $key }}">{{ Str::headline($key) }}</option>
                                                            @endforeach
                                                        </x-select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12"></div>

                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <x-input class="datepicker-input" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" helpers="target delivery" label="from date" id="fromDate-delivery-orders-done" />
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <x-input class="datepicker-input" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" helpers="target delivery" label="to date" id="to-delivery-orders-done" />
                                                    </div>
                                                </div>
                                                <div class="col-md-1 row align-self-center">
                                                    <div class="form-group">
                                                        <x-button type="submit" size="sm" color="primary" id="set-delivery-orders-done-table" icon="search" fontawesome />
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <x-table theadColor="" id="list-delivery-orders-done">
                                            <x-slot name="table_head">
                                                <th></th>
                                                <th>#</th>
                                                <th></th>
                                                <th>{{ Str::headline('customer') }}</th>
                                                <th>{{ Str::headline('Nomor do') }}</th>
                                                <th>{{ Str::headline('Nomor SO') }}</th>
                                                <th>{{ Str::headline('Target Delivery') }}</th>
                                                <th>{{ Str::headline('Tanggal muat') }}</th>
                                                <th>{{ Str::headline('Tanggal bongkar') }}</th>
                                                <th>{{ Str::headline('kuantitas_dikirim') }}</th>
                                                <th>{{ Str::headline('kuantitas_diterima') }}</th>
                                                <th>{{ Str::headline('moda transport') }}</th>
                                                <th>{{ Str::headline('status') }}</th>
                                                <th>{{ Str::headline('Export PDF') }}</th>
                                            </x-slot>
                                            <x-slot name="table_body">

                                            </x-slot>
                                        </x-table>
                                    </div>
                                </div>

                            </div>
                            <div class="tab-pane" id="sales-orders-tab">
                                @include('components.validate-error')
                                <div class="row justify-content-between my-4">
                                    <div class="col-md-3 col-md-6 col-xl-4">
                                        @can('create sales-order')
                                            <x-button color="info" icon="plus" label="Create" link='{{ route("admin.$main.create") }}' />
                                        @endcan
                                    </div>
                                </div>
                                @if (get_current_branch()->is_primary)
                                    <div class="row mb-15">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-select name="customer" id="delivery-order-branch-select" label="branch">

                                                </x-select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-input class="datepicker-input" name="from_date" label="from date" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" id="delivery-order-so-from-date" required />
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-input class="datepicker-input" name="to" label="to date" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" id="delivery-order-so-to-date" required />
                                            </div>
                                        </div>
                                        <div class="col-md-1 row align-self-end">
                                            <div class="form-group">
                                                <x-button type="submit" color="primary" id="set-delivery-order-table" icon="search" fontawesome />
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <x-table id="trading-table">
                                    <x-slot name="table_head">
                                        <th>{{ Str::headline('#') }}</th>
                                        <th>{{ Str::headline('reference') }}</th>
                                        <th>{{ Str::headline('Customer') }}</th>
                                        <th>{{ Str::headline('jumlah_do') }}</th>
                                        <th>{{ Str::headline('Status') }}</th>
                                    </x-slot>
                                    <x-slot name="table_body">

                                    </x-slot>
                                </x-table>
                            </div>
                            <div class="tab-pane" id="delivery-order-receiving-tab">
                                <div class="row justify-content-between my-4">
                                    <div class="col-md-3 col-md-6 col-xl-4">
                                        @can('create delivery-order')
                                            <x-button color="info" icon="plus" label="Create" link='{{ route("admin.$main.create") }}' />
                                        @endcan
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="row">
                                        @if (get_current_branch()->is_primary)
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <x-select name="customer" id="branch-delivery-order-receiving" label="branch">

                                                    </x-select>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-select name="customer" id="customer-delivery-order-receiving" label="customer">

                                                </x-select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-select name="customer" id="shNumber-delivery-order-receiving" label="sh no.">

                                                </x-select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-select name="customer" id="status-delivery-order-receiving" label="status">
                                                    <option value="" selected>Pilih item</option>
                                                    @foreach (get_delivery_order_status() as $key => $item)
                                                        <option value="{{ $key }}">{{ Str::headline($key) }}</option>
                                                    @endforeach
                                                </x-select>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <x-input class="datepicker-input" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" helpers="target delivery" label="from date" id="fromDate-delivery-order-receiving" />
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <x-input class="datepicker-input" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" helpers="target delivery" label="to date" id="to-delivery-order-receiving" />
                                                </div>
                                            </div>
                                            <div class="col-md-1 row align-self-end">
                                                <div class="form-group">
                                                    <x-button type="submit" size="sm" color="primary" id="set-delivery-order-receiving-table" icon="search" fontawesome />
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <x-table theadColor="" id="list-delivery-order-receiving">
                                    <x-slot name="table_head">
                                        <th></th>
                                        <th>#</th>
                                        <th></th>
                                        <th>{{ Str::headline('customer') }}</th>
                                        <th>{{ Str::headline('Nomor do') }}</th>
                                        <th>{{ Str::headline('Nomor SO') }}</th>
                                        <th>{{ Str::headline('Target Delivery') }}</th>
                                        <th>{{ Str::headline('Tanggal muat') }}</th>
                                        <th>{{ Str::headline('Tanggal bongkar') }}</th>
                                        <th>{{ Str::headline('kuantitas_dikirim') }}</th>
                                        <th>{{ Str::headline('kuantitas_diterima') }}</th>
                                        <th>{{ Str::headline('moda transport') }}</th>
                                        <th>{{ Str::headline('status') }}</th>
                                        <th>{{ Str::headline('Export PDF') }}</th>
                                    </x-slot>
                                    <x-slot name="table_body">

                                    </x-slot>
                                </x-table>
                            </div>
                        </div>

                    </x-slot>

                </x-card-data-table>
            @endcanany
        </div>

        <div class="tab-pane {{ $tab_active == 'general' ? 'active' : '' }}" id="general-tab" role="tabpanel">
            @canany(['view delivery-order-general'])
                <x-card-data-table title="{{ $main . ' General' }}">
                    <x-slot name="header_content">
                        <div class="row justify-content-between mb-4">
                            <div class="col-md-3 col-md-6 col-xl-4">
                                @can('create delivery-order-general')
                                    <x-button color="info" icon="plus" label="Create" link="{{ route('admin.delivery-order-general.create') }}" />
                                @endcan
                            </div>
                        </div>
                        @if (get_current_branch()->is_primary)
                            <div class="row mb-15">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-select name="customer" id="delivery-order-general-branch-select" label="branch">

                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input class="datepicker-input" name="from_date" label="from date" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" id="do-general-from-date" required />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input class="datepicker-input" name="to" label="to date" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" id="do-general-to-date" required />
                                    </div>
                                </div>
                                <div class="col-md-1 row align-self-end">
                                    <div class="form-group">
                                        <x-button type="submit" color="primary" id="set-delivery-order-general-table" icon="search" fontawesome />
                                    </div>
                                </div>
                            </div>
                        @endif
                    </x-slot>
                    <x-slot name="table_content">
                        @include('components.validate-error')

                        <x-table id="general-table">
                            <x-slot name="table_head">
                                <th>#</th>
                                <th>{{ Str::headline('Tanggal') }}</th>
                                <th>{{ Str::headline('kode') }}</th>
                                <th>{{ Str::headline('customer') }}</th>
                                <th>{{ Str::headline('kode sale order') }}</th>
                                <th>{{ Str::headline('status') }}</th>
                                <th>{{ Str::headline('dibuat pada') }}</th>
                                <th>{{ Str::headline('export') }}</th>
                                <th>{{ Str::headline('action') }}</th>
                            </x-slot>
                            <x-slot name="table_body">
                            </x-slot>
                        </x-table>
                    </x-slot>

                </x-card-data-table>
            @endcanany
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script>
        const setTableTrading = () => {
            $('table#trading-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                destroy: true,
                ajax: {
                    url: '{{ route('admin.delivery-order.index') }}',
                    data: {
                        branch_id: $('#delivery-order-branch-select').val(),
                        from_date: $('#delivery-order-so-from-date').val(),
                        to_date: $('#delivery-order-so-to-date').val(),
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
                        data: 'customer',
                        name: 'customer'
                    },
                    {
                        data: 'jumlah_do',
                        name: 'jumlah_do'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    }
                ]
            });
            $('table').css('width', '100%');
            initSelect2Search('delivery-order-branch-select', '{{ route('admin.select.branch') }}', {
                'id': 'id',
                'text': 'name'
            });
        }

        const setTableGeneral = () => {
            $('table#general-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                destroy: true,
                order: [
                    [2, 'desc'],
                ],
                ajax: {
                    url: '{{ route('admin.delivery-order-general.index') }}',
                    data: {
                        branch_id: $('#delivery-order-general-branch-select').val(),
                        from_date: $('#do-general-from-date').val(),
                        to_date: $('#do-general-to-date').val(),
                    }
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
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'customer.nama',
                        name: 'customers.nama'
                    },
                    {
                        data: 'sale_order_general.kode',
                        name: 'sale_order_general.kode'
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
                        name: 'export'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        searchable: false,
                        orderable: false
                    }
                ]
            });
            $('table').css('width', '100%');
            initSelect2Search('delivery-order-general-branch-select', '{{ route('admin.select.branch') }}', {
                'id': 'id',
                'text': 'name'
            });
        }
        {{ $tab_active == 'general' ? 'setTableGeneral()' : '' }}


        const deliveryOrders = () => {
            $('#list-delivery-orders').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    url: '{{ route("admin.$main.delivery-orders") }}',
                    data: {
                        branch_id: $('#branch-delivery-orders').val(),
                        customer_id: $('#customer-delivery-orders').val(),
                        sh_number_id: $('#shNumber-delivery-orders').val(),
                        status: $('#status-delivery-orders').val(),
                        from_date: $('#fromDate-delivery-orders').val(),
                        to_date: $('#to-delivery-orders').val(),
                        is_done: "false",
                    }
                },
                order: [
                    [0, 'desc']
                ],
                columns: [{
                        data: 'created_at',
                        name: 'created_at',
                        visible: false,
                        searchable: false,
                    },
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },
                    {
                        data: 'customer_name',
                        name: 'customers.nama',
                    },
                    {
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'nomor_so',
                        name: 'sale_orders.nomor_so'
                    },
                    {
                        data: 'target_delivery',
                        name: 'target_delivery'
                    },
                    {
                        data: 'load_date',
                        name: 'load_date'
                    },
                    {
                        data: 'unload_date',
                        name: 'unload_date'
                    },
                    {
                        data: 'load_quantity',
                        name: 'load_quantity'
                    },
                    {
                        data: 'unload_quantity_realization',
                        name: 'unload_quantity_realization'
                    },
                    {
                        data: 'moda_transport',
                        name: 'moda_transport'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'export',
                        name: 'export',
                        searchable: false,
                        orderable: false
                    },
                ]
            });
            initSelect2Search('customer-delivery-orders', "{{ route('admin.select.customer') }}", {
                id: "id",
                text: "nama"
            });

            initSelect2Search('branch-delivery-orders', "{{ route('admin.select.branch') }}", {
                id: "id",
                text: "name"
            });

            $('#customer-delivery-orders').change(function(e) {
                e.preventDefault();

                if (this.value) {
                    initSelect2Search(`shNumber-delivery-orders`, `{{ route('admin.select.sh-number.customer') }}/${this.value}`, {
                        id: "id",
                        text: "kode,supply_point,drop_point"
                    });

                    return;
                }

                $('#shNumber-delivery-orders').select2('destroy');
            });

            $('table#list-delivery-orders').css('width', '100%');
        };

        {{ $tab_active == 'trading' ? 'deliveryOrders()' : '' }}

        const deliveryOrdersDone = () => {
            $('#list-delivery-orders-done').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    url: '{{ route("admin.$main.delivery-orders") }}',
                    data: {
                        branch_id: $('#branch-delivery-orders-done').val(),
                        customer_id: $('#customer-delivery-orders-done').val(),
                        sh_number_id: $('#shNumber-delivery-orders-done').val(),
                        status: $('#status-delivery-orders-done').val(),
                        from_date: $('#fromDate-delivery-orders-done').val(),
                        to_date: $('#to-delivery-orders-done').val(),
                        is_done: "true",
                    }
                },
                order: [
                    [0, 'desc']
                ],
                columns: [{
                        data: 'created_at',
                        name: 'created_at',
                        visible: false,
                        searchable: false,
                    },
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },
                    {
                        data: 'customer_name',
                        name: 'customers.nama',
                    },
                    {
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'nomor_so',
                        name: 'sale_orders.nomor_so'
                    },
                    {
                        data: 'target_delivery',
                        name: 'target_delivery'
                    },
                    {
                        data: 'load_date',
                        name: 'load_date'
                    },
                    {
                        data: 'unload_date',
                        name: 'unload_date'
                    },
                    {
                        data: 'load_quantity',
                        name: 'load_quantity'
                    },
                    {
                        data: 'unload_quantity_realization',
                        name: 'unload_quantity_realization'
                    },
                    {
                        data: 'moda_transport',
                        name: 'moda_transport'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'export',
                        name: 'export',
                        searchable: false,
                        orderable: false
                    },
                ]
            });
            initSelect2Search('customer-delivery-orders-done', "{{ route('admin.select.customer') }}", {
                id: "id",
                text: "nama"
            });

            initSelect2Search('branch-delivery-orders-done', "{{ route('admin.select.branch') }}", {
                id: "id",
                text: "name"
            });

            $('#customer-delivery-orders-done').change(function(e) {
                e.preventDefault();

                if (this.value) {
                    initSelect2Search(`shNumber-delivery-orders-done`, `{{ route('admin.select.sh-number.customer') }}/${this.value}`, {
                        id: "id",
                        text: "kode,supply_point,drop_point"
                    });

                    return;
                }

                $('#shNumber-delivery-orders-done').select2('destroy');
            });

            $('table#list-delivery-orders-done').css('width', '100%');
        };

        {{ $tab_active == 'trading' ? 'deliveryOrdersDone()' : '' }}

        const deliveryOrdersReceiving = () => {
            $('#list-delivery-order-receiving').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    url: '{{ route("admin.$main.list-received") }}',
                    data: {
                        branch_id: $('#branch-delivery-order-receiving').val(),
                        customer_id: $('#customer-delivery-order-receiving').val(),
                        sh_number_id: $('#shNumber-delivery-order-receiving').val(),
                        status: $('#status-delivery-order-receiving').val(),
                        from_date: $('#fromDate-delivery-order-receiving').val(),
                        to_date: $('#to-delivery-order-receiving').val(),
                    }
                },
                order: [
                    [0, 'desc']
                ],
                columns: [{
                        data: 'created_at',
                        name: 'created_at',
                        visible: false,
                        searchable: false,
                    },
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },
                    {
                        data: 'customer_name',
                        name: 'customers.nama',
                    },
                    {
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'nomor_so',
                        name: 'sale_orders.nomor_so'
                    },
                    {
                        data: 'target_delivery',
                        name: 'target_delivery'
                    },
                    {
                        data: 'load_date',
                        name: 'load_date'
                    },
                    {
                        data: 'unload_date',
                        name: 'unload_date'
                    },
                    {
                        data: 'load_quantity',
                        name: 'load_quantity'
                    },
                    {
                        data: 'unload_quantity_realization',
                        name: 'unload_quantity_realization'
                    },
                    {
                        data: 'moda_transport',
                        name: 'moda_transport'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'export',
                        name: 'export',
                        searchable: false,
                        orderable: false
                    },
                ]
            });
            initSelect2Search('customer-delivery-orders', "{{ route('admin.select.customer') }}", {
                id: "id",
                text: "nama"
            });

            initSelect2Search('branch-delivery-orders', "{{ route('admin.select.branch') }}", {
                id: "id",
                text: "name"
            });

            $('#customer-delivery-orders').change(function(e) {
                e.preventDefault();

                if (this.value) {
                    initSelect2Search(`shNumber-delivery-orders`, `{{ route('admin.select.sh-number.customer') }}/${this.value}`, {
                        id: "id",
                        text: "kode,supply_point,drop_point"
                    });

                    return;
                }

                $('#shNumber-delivery-orders').select2('destroy');
            });

            $('table#list-delivery-orders').css('width', '100%');
        };

        {{ $tab_active == 'trading' ? 'deliveryOrdersReceiving()' : '' }}

        $(document).ready(() => {
            setTableTrading();
            setTableGeneral();
        });

        $('#set-delivery-order-table').click(function(e) {
            e.preventDefault();
            setTableTrading();
        });

        $('#set-delivery-order-general-table').click(function(e) {
            e.preventDefault();
            setTableGeneral();
        });

        $('#delivery-orders-tab-btn').click(function(e) {
            e.preventDefault();
            deliveryOrders();
        });

        $('#set-delivery-orders-table').click(function(e) {
            e.preventDefault();
            deliveryOrders();
        });

        $('#set-delivery-orders-done-table').click(function(e) {
            e.preventDefault();
            deliveryOrdersDone();
        });

        $('#delivery-order-receiving-tab-btn').click(function(e) {
            e.preventDefault();
            deliveryOrdersReceiving();
        });

        $('#set-delivery-order-receiving-table').click(function(e) {
            e.preventDefault();
            deliveryOrdersReceiving();
        });

        $('#sales-orders-tab-btn').click(function(e) {
            e.preventDefault();
            setTableTrading();
        });
    </script>
    <script>
        sidebarMenuOpen('#trading');
        sidebarActive('#delivery-order');
    </script>
@endsection
