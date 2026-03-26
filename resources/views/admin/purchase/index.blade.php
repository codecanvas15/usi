@extends('layouts.admin.layout.index')

@php
    $main = 'purchase';
@endphp

@section('title', Str::headline("$main Order") . ' - ')

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
                        {{ Str::headline("$main Order") }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')

    @php
        $tab_active = '';

        if (Auth::user()->hasPermissionTo('view purchase-general')) {
            $tab_active = 'general';
        }
        if (!Auth::user()->hasPermissionTo('view purchase-general') && Auth::user()->hasPermissionTo('view purchase-service')) {
            $tab_active = 'service';
        }
        if (!Auth::user()->hasPermissionTo('view purchase-general') && !Auth::user()->hasPermissionTo('view purchase-service') && Auth::user()->hasPermissionTo('view purchase-order')) {
            $tab_active = 'trading';
        }
        if (!Auth::user()->hasPermissionTo('view purchase-general') && !Auth::user()->hasPermissionTo('view purchase-service') && !Auth::user()->hasPermissionTo('view purchase-order') && Auth::user()->hasPermissionTo('view purchase-transport')) {
            $tab_active = 'transport';
        }
    @endphp

    @canany(['view purchase-order', 'view purchase-transport', 'view purchase-service', 'view purchase-general'])
        <x-card-data-table title="{{ $main . ' Order' }}">
            <x-slot name="table_content">
                @include('components.validate-error')

                <ul class="nav nav-tabs customtab2 mb-10" role="tablist">
                    @can('view purchase-general')
                        <li class="nav-item">
                            <a class="nav-link rounded {{ $tab_active == 'general' ? 'active' : '' }}" data-bs-toggle="tab" href="#general-tab" id="general-btn" role="tab">
                                <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                                <span class="hidden-xs-down">General</span>
                            </a>
                        </li>
                    @endcan
                    @can('view purchase-service')
                        <li class="nav-item">
                            <a class="nav-link rounded {{ $tab_active == 'service' ? 'active' : '' }}" data-bs-toggle="tab" href="#service-tab" id="service-btn" role="tab">
                                <span class="hidden-sm-up"><i class="fa-solid fa-link"></i></span>
                                <span class="hidden-xs-down">Service</span>
                            </a>
                        </li>
                    @endcan
                    @can('view purchase-order')
                        <li class="nav-item">
                            <a class="nav-link rounded {{ $tab_active == 'trading' ? 'active' : '' }}" data-bs-toggle="tab" href="#trading-tab" id="trading-btn" role="tab">
                                <span class="hidden-sm-up"><i class="fa-solid fa-link"></i></span>
                                <span class="hidden-xs-down">Trading</span>
                            </a>
                        </li>
                    @endcan
                    @can('view purchase-transport')
                        <li class="nav-item">
                            <a class="nav-link rounded {{ $tab_active == 'transport' ? 'active' : '' }}" data-bs-toggle="tab" href="#transport-tab" id="transport-btn" role="tab">
                                <span class="hidden-sm-up"><i class="fa-solid fa-link"></i></span>
                                <span class="hidden-xs-down">Transport</span>
                            </a>
                        </li>
                    @endcan
                    @can('view purchase-down-payment')
                        <li class="nav-item">
                            <a class="nav-link rounded {{ $tab_active == 'down-payment' ? 'active' : '' }}" data-bs-toggle="tab" href="#down-payment-tab" id="down-payment-btn" role="tab">
                                <span class="hidden-sm-up"><i class="fa-solid fa-link"></i></span>
                                <span class="hidden-xs-down">Down Payment</span>
                            </a>
                        </li>
                    @endcan
                </ul>

                <div class="tab-content mt-30">
                    @can('view purchase-general')
                        <div class="tab-pane {{ $tab_active == 'general' ? 'active' : '' }}" id="general-tab" role="tabpanel">
                            @can('create purchase-general')
                                <x-button color="info" icon="plus" label="Create" dataToggle="modal" dataTarget="#create-modal" class="mb-20" />
                                <x-modal title="create new data" id="create-modal" headerColor="info" modalSize="700">
                                    <x-slot name="modal_body">
                                        <x-table>
                                            <x-slot name="table_body">
                                                <tr>
                                                    <td>
                                                        <x-button color="info" icon="plus" label="Create From Purchase Request" link="{{ route('admin.purchase-order-general.create', ['type' => 'purchase-request']) }}" class="mb-20" />
                                                    </td>
                                                    <td>
                                                        <x-button color="info" icon="plus" label="Create From Sales Order" link="{{ route('admin.purchase-order-general.create', ['type' => 'sales-order']) }}" class="mb-20" />
                                                    </td>
                                                </tr>
                                            </x-slot>
                                        </x-table>
                                    </x-slot>
                                </x-modal>
                            @endcan
                            <div class="row mb-15">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-select name="vendor_id" id="general-vendor-select" label="vendor">

                                        </x-select>
                                    </div>
                                </div>
                                @if (get_current_branch()->is_primary)
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <x-select name="customer" id="po-general-branch-select" label="branch">

                                            </x-select>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-select name="status" id="general-status-select" label="status">
                                            <option value="" selected>Pilih item</option>
                                            @foreach (purchase_general_status() as $key => $item)
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
                                        <x-button type="submit" size="sm" color="primary" id="set-general-table" icon="search" fontawesome />
                                    </div>
                                </div>
                            </div>
                            <x-table id="general-table">
                                <x-slot name="table_head">
                                    <th>{{ Str::headline('#') }}</th>
                                    <th>{{ Str::headline('tanggal') }}</th>
                                    <th>{{ Str::headline('Kode') }}</th>
                                    <th>{{ Str::headline('vendor') }}</th>
                                    <th>{{ Str::headline('Status') }}</th>
                                    <th>{{ Str::headline('dibuat pada') }}</th>
                                    <td></td>
                                </x-slot>
                                <x-slot name="table_body">

                                </x-slot>
                            </x-table>
                        </div>
                    @endcan
                    @can('view purchase-service')
                        <div class="tab-pane {{ $tab_active == 'service' ? 'active' : '' }}" id="service-tab" role="tabpanel">
                            @can('create purchase-service')
                                <x-button color="info" icon="plus" label="Create" link="{{ route('admin.purchase-order-service.create') }}" class="mb-20" />
                            @endcan
                            <div class="row mb-15">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-select name="vendor_id" id="service-vendor-select" label="vendor">

                                        </x-select>
                                    </div>
                                </div>
                                @if (get_current_branch()->is_primary)
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <x-select name="customer" id="po-service-branch-select" label="branch">

                                            </x-select>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-select name="status" id="service-status-select" label="status">
                                            <option value="" selected>Pilih item</option>
                                            @foreach (purchase_service_status() as $key => $item)
                                                <option value="{{ $key }}">{{ Str::headline($key) }}</option>
                                            @endforeach
                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input class="datepicker-input" name="from_date" label="from date" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" id="service-from-date" required />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input class="datepicker-input" name="to" label="to date" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" id="service-to-date" required />
                                    </div>
                                </div>
                                <div class="col-md-1 row align-self-end">
                                    <div class="form-group">
                                        <x-button type="submit" size="sm" color="primary" id="set-service-table" icon="search" fontawesome />
                                    </div>
                                </div>
                            </div>
                            <x-table id="service-table">
                                <x-slot name="table_head">
                                    <th>{{ Str::headline('#') }}</th>
                                    <th>{{ Str::headline('tanggal') }}</th>
                                    <th>{{ Str::headline('Kode') }}</th>
                                    <th>{{ Str::headline('vendor') }}</th>
                                    <th>{{ Str::headline('Status') }}</th>
                                    <th>{{ Str::headline('dibuat pada') }}</th>
                                    <td></td>
                                </x-slot>
                                <x-slot name="table_body">

                                </x-slot>
                            </x-table>
                        </div>
                    @endcan
                    @can('view purchase-order')
                        <div class="tab-pane {{ $tab_active == 'trading' ? 'active' : '' }}" id="trading-tab" role="tabpanel">
                            @can('create purchase-order')
                                <x-button color="info" icon="plus" label="Create" link="{{ route('admin.purchase-order.create') }}" class="mb-20" />
                            @endcan
                            <div class="row mb-15">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-select name="customer_id" id="trading-vendor-select" label="customer" required>

                                        </x-select>
                                    </div>
                                </div>
                                @if (get_current_branch()->is_primary)
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <x-select name="customer" id="po-trading-branch-select" label="branch">

                                            </x-select>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-select name="status" id="trading-status-select" label="status">
                                            <option value="" selected>Pilih item</option>
                                            @foreach (status_purchase_orders() as $key => $item)
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
                                        <x-button type="submit" size="sm" color="primary" id="set-trading-table" icon="search" fontawesome />
                                    </div>
                                </div>
                            </div>
                            <x-table id="trading-table">
                                <x-slot name="table_head">
                                    <th></th>
                                    <th>{{ Str::headline('#') }}</th>
                                    <th>{{ Str::headline('tanggal') }}</th>
                                    <th>{{ Str::headline('nomor po') }}</th>
                                    <th>{{ Str::headline('vendor') }}</th>
                                    <th>{{ Str::headline('customer') }}</th>
                                    <th>{{ Str::headline('sh No.') }}</th>
                                    <th>{{ Str::headline('jumlah') }}</th>
                                    <th>{{ Str::headline('status') }}</th>
                                    <th>{{ Str::headline('pairing status') }}</th>
                                    <th></th>
                                </x-slot>
                                <x-slot name="table_body">

                                </x-slot>
                            </x-table>
                        </div>
                    @endcan

                    @can('view purchase-transport')
                        <div class="tab-pane {{ $tab_active == 'transport' ? 'active' : '' }}" id="transport-tab" role="tabpanel">
                            @can('create purchase-transport')
                                <x-button color="info" icon="plus" label="Create" link="{{ route('admin.purchase-order-transport.create') }}" class="mb-20" />
                            @endcan
                            <div class="row mb-15">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-select name="vendor_id" id="transport-vendor-select" label="vendor">

                                        </x-select>
                                    </div>
                                </div>
                                @if (get_current_branch()->is_primary)
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <x-select name="customer" id="po-transport-branch-select" label="branch">

                                            </x-select>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-select name="status" id="transport-status-select" label="status">
                                            <option value="" selected>Pilih item</option>
                                            @foreach (purchase_transport_status() as $key => $item)
                                                <option value="{{ $key }}">{{ Str::headline($key) }}</option>
                                            @endforeach
                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input class="datepicker-input" name="from_date" label="from date" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" id="transport-from-date" required />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input class="datepicker-input" name="to" label="to date" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" id="transport-to-date" required />
                                    </div>
                                </div>
                                <div class="col-md-1 row align-self-end">
                                    <div class="form-group">
                                        <x-button type="submit" size="sm" color="primary" id="set-transport-table" icon="search" fontawesome />
                                    </div>
                                </div>
                            </div>
                            <x-table id="transport-table">
                                <x-slot name="table_head">
                                    <th>{{ Str::upper('#') }}</th>
                                    <th>{{ Str::upper('kode') }}</th>
                                    <th>{{ Str::upper('Nomor so') }}</th>
                                    <th>{{ Str::upper('vendor') }}</th>
                                    <th>{{ Str::upper('Tipe') }}</th>
                                    <th>{{ Str::upper('Status') }}</th>
                                    <th>{{ Str::upper('DIbuat pada') }}</th>
                                </x-slot>
                                <x-slot name="table_body">

                                </x-slot>
                            </x-table>
                        </div>
                    @endcan

                    @can('view purchase-down-payment')
                        <div class="tab-pane {{ $tab_active == 'down-payment' ? 'active' : '' }}" id="down-payment-tab" role="tabpanel">

                            @if (get_current_branch()->is_primary)
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-select name="vendor" id="purchase-down-payment-branch-select" label="branch">

                                            </x-select>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-select name="vendor_id" id="down-payment-vendor-select" label="vendor">

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
                                        <x-button type="submit" color="primary" id="set-down-payment-table" size="sm" icon="search" fontawesome />
                                    </div>
                                </div>
                                <div class="col-md-1 row align-self-end">
                                    <div class="form-group">
                                        @can('create purchase-down-payment')
                                            <x-button label="tambah" color="info" size="sm" icon="plus" fontawesome link="{{ route('admin.purchase-down-payment.create') }}" />
                                        @endcan
                                    </div>
                                </div>
                            </div>
                            <x-table id="down-payment-table">
                                <x-slot name="table_head">
                                    <th>{{ Str::headline('#') }}</th>
                                    <th>{{ Str::headline(Str::headline('tanggal')) }}</th>
                                    <th>{{ Str::headline(Str::headline('kode')) }}</th>
                                    <th>{{ Str::headline(Str::headline('kode po')) }}</th>
                                    <th>{{ Str::headline('vendor') }}</th>
                                    <th>{{ Str::headline('uang muka') }}</th>
                                    <th>{{ Str::headline('status') }}</th>
                                    <th>{{ Str::headline('payment status') }}</th>
                                    <th>{{ Str::headline('export') }}</th>
                                </x-slot>
                                <x-slot name="table_body">

                                </x-slot>
                            </x-table>
                        </div>
                    @endcan
                </div>
            </x-slot>

        </x-card-data-table>
    @endcanany
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>

    <script>
        $(document).ready(() => {
            var a = "";
            let [
                general,
                service,
                trading,
                transport,
                down_payment
            ] = [
                false,
                false,
                false,
                false,
                false
            ];

            @can('view purchase-order')
                const initTableTrading = () => {
                    const table = $('table#trading-table').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        destroy: true,
                        order: [
                            [0, 'desc']
                        ],
                        ajax: {
                            url: '{{ route('admin.purchase-order.data') }}',
                            data: {
                                from_date: $('#trading-from-date').val(),
                                to_date: $('#trading-to-date').val(),
                                customer_id: $('#trading-vendor-select').val(),
                                status: $('#trading-status-select').val(),
                                branch_id: $('#po-trading-branch-select').val(),
                            },
                        },
                        columns: [{
                                data: 'created_at',
                                name: 'created_at',
                                visible: false,
                                searchable: false
                            },
                            {
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
                                data: 'nomor_po',
                                name: 'nomor_po'
                            },
                            {
                                data: 'vendor_name',
                                name: 'vendors.nama'
                            },
                            {
                                data: 'customer_name',
                                name: 'customers.nama'
                            },
                            {
                                data: 'sh_number_code',
                                name: 'sh_numbers.kode'
                            },
                            {
                                data: 'jumlah',
                                name: 'purchase_order_details.jumlah'
                            },
                            {
                                data: 'status',
                                name: 'purchase_orders.status'
                            },
                            {
                                data: 'pairing_status',
                                name: 'purchase_orders.pairing_status'
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

                    initSelect2SearchPaginationData(`trading-vendor-select`, `{{ route('admin.select.customer') }}`, {
                        id: 'id',
                        text: 'nama'
                    })

                    initSelect2SearchPaginationData(`po-trading-branch-select`, `{{ route('admin.select.branch') }}`, {
                        id: 'id',
                        text: 'name'
                    })

                }

                $('#trading-btn').click(function(e) {
                    e.preventDefault();
                    initTableTrading();
                });

                $('#set-trading-table').click(function(e) {
                    e.preventDefault();
                    initTableTrading();
                });
                {{ $tab_active == 'trading' ? 'initTableTrading()' : '' }}
            @endcan
            @can('view purchase-service')
                const initTableService = () => {
                    const table = $('table#service-table').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        destroy: true,
                        order: [
                            [5, 'desc']
                        ],
                        ajax: {
                            url: '{{ route('admin.purchase-order-service.index') }}',
                            data: {
                                from_date: $('#service-from-date').val(),
                                to_date: $('#service-to-date').val(),
                                vendor_id: $('#service-vendor-select').val(),
                                status: $('#service-status-select').val(),
                                branch_id: $('#po-service-branch-select').val(),
                            }
                        },
                        columns: [{
                                data: 'DT_RowIndex',
                                name: 'spk_number',
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
                                data: 'vendor.nama',
                                name: 'vendor.nama'
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
                                data: 'action',
                                name: 'action',
                                orderable: false,
                                searchable: false
                            }
                        ]

                    });
                    $('table').css('width', '100%');

                    initSelect2SearchPaginationData(`service-vendor-select`, `{{ route('admin.select.vendor') }}`, {
                        id: 'id',
                        text: 'nama'
                    })

                    initSelect2SearchPaginationData(`po-service-branch-select`, `{{ route('admin.select.branch') }}`, {
                        id: 'id',
                        text: 'name'
                    })

                }

                $('#service-btn').click(function(e) {
                    e.preventDefault();
                    initTableService();
                });

                $('#set-service-table').click(function(e) {
                    e.preventDefault();
                    initTableService();
                });

                {{ $tab_active == 'service' ? 'initTableService()' : '' }}
            @endcan
            @can('view purchase-general')
                const initTableGeneral = () => {
                    $('#general-to-date').val();
                    const table = $('table#general-table').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        destroy: true,
                        order: [5, 'desc'],
                        ajax: {
                            url: '{{ route('admin.purchase-order-general.index') }}',
                            data: {
                                from_date: $('#general-from-date').val(),
                                to_date: $('#general-to-date').val(),
                                vendor_id: $('#general-vendor-select').val(),
                                status: $('#general-status-select').val(),
                                branch_id: $('#po-general-branch-select').val(),
                            },
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
                                data: 'vendor.nama',
                                name: 'vendor.nama'
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
                                data: 'action',
                                name: 'action',
                                orderable: false,
                                searchable: false
                            }
                        ]
                    });
                    $('table').css('width', '100%');

                    initSelect2SearchPaginationData(`general-vendor-select`, `{{ route('admin.select.vendor') }}`, {
                        id: 'id',
                        text: 'nama'
                    });

                    initSelect2SearchPaginationData(`po-general-branch-select`, `{{ route('admin.select.branch') }}`, {
                        id: 'id',
                        text: 'name'
                    })

                }

                $('#set-general-table').click(function(e) {
                    e.preventDefault();
                    initTableGeneral();
                });

                $('#general-btn').click(function(e) {
                    e.preventDefault();
                    initTableGeneral();
                });

                {{ $tab_active == 'general' ? 'initTableGeneral()' : '' }}
            @endcan
            @can('view purchase-transport')
                const initTableTransport = () => {

                    const table = $('table#transport-table').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        destroy: true,
                        order: [6, 'desc'],
                        ajax: {
                            url: '{{ route('admin.purchase-order-transport.data') }}',
                            data: {
                                from_date: $('#transport-from-date').val(),
                                to_date: $('#transport-to-date').val(),
                                vendor_id: $('#transport-vendor-select').val(),
                                status: $('#transport-status-select').val(),
                                branch_id: $('#po-transport-branch-select').val(),
                            }
                        },
                        columns: [{
                                data: 'DT_RowIndex',
                                name: 'DT_RowIndex',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'kode',
                                name: 'kode'
                            },
                            {
                                data: 'so_trading.nomor_so',
                                name: 'so_trading.nomor_so'
                            },
                            {
                                data: 'vendor.nama',
                                name: 'vendor.nama'
                            },
                            {
                                data: 'type',
                                name: 'type'
                            },
                            {
                                data: 'status',
                                name: 'status'
                            },
                            {
                                data: 'created_at',
                                name: 'created_at'
                            },
                        ]
                    });
                    $('table').css('width', '100%');
                    transport = true;

                    initSelect2SearchPaginationData(`transport-vendor-select`, `{{ route('admin.select.vendor') }}`, {
                        id: 'id',
                        text: 'nama'
                    });

                    initSelect2SearchPaginationData(`po-transport-branch-select`, `{{ route('admin.select.branch') }}`, {
                        id: 'id',
                        text: 'name'
                    })

                }

                $('#transport-btn').click(function(e) {
                    e.preventDefault();
                    initTableTransport();
                });

                $('#set-transport-table').click(function(e) {
                    e.preventDefault();
                    initTableTransport();
                });
            @endcan

            @can('view purchase-down-payment')
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
                            url: '{{ route('admin.purchase-down-payment.index') }}',
                            data: {
                                branch_id: $('#purchase-down-payment-branch-select').val(),
                                vendor_id: $('#down-payment-vendor-select').val(),
                                status: $('#down-payment-status-select').val(),
                                from_date: $('#down-payment-from-date').val(),
                                to_date: $('#down-payment-to-date').val(),
                            }
                        },
                        columns: [
                            {
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
                                data: 'po_code',
                                name: 'po_code'
                            },
                            {
                                data: 'vendor_name',
                                name: 'vendor_name'
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

                    initSelect2Search('purchase-down-payment-branch-select', '{{ route('admin.select.branch') }}', {
                        'id': 'id',
                        'text': 'name'
                    });
                    initSelect2Search('down-payment-vendor-select', '{{ route('admin.select.vendor') }}', {
                        'id': 'id',
                        'text': 'nama'
                    });
                }

                $('#down-payment-btn').click(function(e) {
                    e.preventDefault();
                    setTableDownPayment();
                });

                $('#set-down-payment-table').click(function(e) {
                    e.preventDefault();
                    setTableDownPayment();
                });

                {{ $tab_active == 'down-payment' ? 'setTableDownPayment()' : '' }}
            @endcan


        });
    </script>
    <script>
        sidebarMenuOpen('#purchase-menu');
        sidebarActive('#purchase')
    </script>
@endsection
