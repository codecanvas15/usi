@extends('layouts.admin.layout.index')

@php
    $main = 'delivery-order';
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
                        <a href="{{ route('admin.delivery.index') }}">{{ Str::headline($main) }}</a>
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
    <div>
        <div class="box bg-gradient-danger-dark text-white">
            <div class="box-body">
                <div class="row justify-content-end">
                    <div class="col-md-6 align-self-center">
                        <h4 class="m-0">Detail Sales Order On DO</h4>
                        <h1 class="m-0">{{ $model->nomor_so }}</h1>
                    </div>
                    <div class="col-md-6 align-self-center">
                        <div class="row justify-content-end">
                            <div class="col-md-3 d-flex flex-column">
                                <h5 class="text-center">{{ Str::headline('status_sale_orders') }}</h5>
                                <div class="badge badge-lg badge-{{ status_sale_orders()[$model->status]['color'] }}">
                                    {{ Str::headline(status_sale_orders()[$model->status]['label']) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-card-data-table title="{{ 'sales order detail' }}">
        <x-slot name="header_content">

        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')
            <x-table theadColor="danger">
                <x-slot name="table_head">
                    <th class="col-md-4"></th>
                    <th class="col-md"></th>
                </x-slot>
                <x-slot name="table_body">
                    <tr>
                        <th>{{ Str::headline('nomor so') }}</th>
                        <td>{{ $model->nomor_so }}</td>
                    </tr>
                    <tr>
                        <th>{{ Str::headline('customer Nama') }}</th>
                        <td>{{ $model->customer->nama }}</td>
                    </tr>
                    <tr>
                        <th>{{ Str::headline('customer Alamat') }}</th>
                        <td>{{ $model->customer->alamat }}</td>
                    </tr>
                    <tr>
                        <th>{{ Str::headline('customer no_pic_customer') }}</th>
                        <td>{{ $model->customer->no_pic_customer }}</td>
                    </tr>
                    <tr>
                        <th>{{ Str::headline('tanggal dibuat') }}</th>
                        <td>{{ localDate($model->tanggal) }}</td>
                    </tr>
                    <tr>
                        <th>{{ Str::headline('status') }}</th>
                        <td>
                            <div class="badge badge-lg badge-{{ status_sale_orders()[$model->status]['color'] }}">
                                {{ Str::headline(status_sale_orders()[$model->status]['text']) }} -
                                {{ Str::headline(status_sale_orders()[$model->status]['label']) }}
                            </div>

                            @if ($model->check_available_date)
                                @if ($model->is_have_any_request_print)
                                    <x-button color="success" icon="check" fontawesome label="approve request print all" size="sm" dataToggle="modal" dataTarget="#set-approve-request-print-all-modal" />
                                    <x-modal title="approve all request print" id="set-approve-request-print-all-modal" headerColor="success">
                                        <x-slot name="modal_body">
                                            <form action='{{ route("admin.$main.approve-print-request.all", $model) }}' method="post">
                                                @csrf
                                                <input type="hidden" name="status" value="approve">
                                                <div class="mt-30 border-top pt-10">
                                                    <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                    <x-button type="submit" color="primary" label="Approve" size="sm" icon="save" fontawesome />
                                                </div>
                                            </form>
                                        </x-slot>
                                    </x-modal>
                                @endif

                                @if ($model->is_have_any_submitted)
                                    <x-button color="success" icon="check" fontawesome label="approve all submitted" size="sm" dataToggle="modal" dataTarget="#set-approve-request-print-all-modal" />
                                    <x-modal title="approve all request print" id="set-approve-request-print-all-modal" headerColor="success">
                                        <x-slot name="modal_body">
                                            <form action='{{ route("admin.$main.approve-submitted.all", $model) }}' method="post">
                                                @csrf
                                                <input type="hidden" name="status" value="approve">
                                                <div class="mt-30 border-top pt-10">
                                                    <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                    <x-button type="submit" color="primary" label="Approve" size="sm" icon="save" fontawesome />
                                                </div>
                                            </form>
                                        </x-slot>
                                    </x-modal>
                                @endif
                            @endif

                        </td>
                    </tr>
                    <tr>
                        <th>{{ Str::headline('approved_by') }}</th>
                        <td>{{ $model->approve_by?->name }}</td>
                    </tr>
                    <tr>
                        <th>{{ Str::headline('created by') }}</th>
                        <td>{{ $model->create_by->name }}</td>
                    </tr>
                    <tr>
                        <th>{{ Str::headline('last_modified') }}</th>
                        <td>{{ toDayDateTimeString($model->updated_at) }}</td>
                    </tr>
                </x-slot>
            </x-table>
        </x-slot>

    </x-card-data-table>

    <x-card-data-table title="{{ 'list ' . $main }}">
        <x-slot name="header_content">

        </x-slot>
        <x-slot name="table_content">

            <ul class="nav nav-tabs customtab2 mb-10" role="tablist">

                <li class="nav-item">
                    <a class="nav-link rounded active" data-bs-toggle="tab" href="#tax-index" id="tax-index-btn" role="tab">
                        <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                        <span class="hidden-xs-down">Bukan Sebagai Gudang</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link rounded" data-bs-toggle="tab" href="#tax-warehouse-index" id="tax-warehouse-index-btn" role="tab">
                        <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                        <span class="hidden-xs-down">Sebagai Gudang</span>
                    </a>
                </li>

            </ul>

            <div class="tab-content mt-30">

                <div class="tab-pane active" id="tax-index" role="tabpanel">
                    <ul class="nav nav-tabs customtab2 mb-10" role="tablist">
                        @foreach ($data_quantity as $item)
                            <li class="nav-item">
                                <a class="nav-link rounded {{ $loop->index == 0 ? 'active' : '' }}" data-bs-toggle="tab" href="#tax-index-{{ $loop->index }}-tab-with-warehouse" id="tax-index-{{ $loop->index }}-btn-with-warehouse" role="tab">
                                    <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                                    <span class="hidden-xs-down">{{ formatNumber($item->load_quantity) }} {{ $model->so_trading_detail->item->unit->name ?? '' }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <div class="tab-content mt-30">
                        @foreach ($data_quantity as $item)
                            <div class="tab-pane {{ $loop->index == 0 ? 'active' : '' }}" id="tax-index-{{ $loop->index }}-tab-with-warehouse" role="tabpanel">

                                <x-button type="button" color="info" label="Generate Invoice" icon="plus" fontawesome id="" class="mb-4" link="{{ route('admin.invoice-trading.generate', $model) }}" />

                                <x-table theadColor="" id="list-{{ $loop->index }}-with-warehouse">
                                    <x-slot name="table_head">
                                        <th>#</th>
                                        <th></th>
                                        <th>{{ Str::headline('Nomor do') }}</th>
                                        <th>{{ Str::headline('Dibuat Pada') }}</th>
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
                        @endforeach
                    </div>
                </div>

                <div class="tab-pane" id="tax-warehouse-index" role="tabpanel">
                    <ul class="nav nav-tabs customtab2 mb-10" role="tablist">
                        @foreach ($data_quantity_delivery_2 as $item)
                            <li class="nav-item">
                                <a class="nav-link rounded {{ $loop->index == 0 ? 'active' : '' }}" data-bs-toggle="tab" href="#tax-warehouse-index-{{ $loop->index }}-tab-with-warehouse" id="tax-index-{{ $loop->index }}-btn-with-warehouse" role="tab">
                                    <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                                    <span class="hidden-xs-down">{{ formatNumber($item->load_quantity) }} {{ $model->so_trading_detail->item->unit->name ?? '' }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <div class="tab-content mt-30">
                        @foreach ($data_quantity_delivery_2 as $item)
                            <div class="tab-pane {{ $loop->index == 0 ? 'active' : '' }}" id="tax-warehouse-index-{{ $loop->index }}-tab-with-warehouse" role="tabpanel">

                                <x-table theadColor="" id="list-{{ $loop->index }}">
                                    <x-slot name="table_head">
                                        <th>#</th>
                                        <th></th>
                                        <th>{{ Str::headline('Nomor do') }}</th>
                                        <th>{{ Str::headline('Dibuat Pada') }}</th>
                                        <th>{{ Str::headline('Target Delivery') }}</th>
                                        <th>{{ Str::headline('Tanggal muat') }}</th>
                                        <th>{{ Str::headline('Tanggal bongkar') }}</th>
                                        <th>{{ Str::headline('kuantitas_dikirim') }}</th>
                                        <th>{{ Str::headline('kuantitas_digunakan') }}</th>
                                        <th>{{ Str::headline('moda transport') }}</th>
                                        <th>{{ Str::headline('status') }}</th>
                                        <th>{{ Str::headline('Export PDF') }}</th>
                                    </x-slot>
                                    <x-slot name="table_body">

                                    </x-slot>
                                </x-table>
                            </div>
                        @endforeach
                    </div>

                </div>

        </x-slot>

    </x-card-data-table>

    @can('view journal')
        @include('components.journal-table')
    @endcan

@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script>
        $(document).ready(() => {

            @foreach ($data_quantity as $item)
                $('#list-{{ $loop->index }}-with-warehouse').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route("admin.$main.list-delivery-order", $model) }}',
                        data: {
                            load_quantity: '{{ $item->load_quantity }}',
                            is_double: "Y"
                        },
                        done: function() {

                        }
                    },
                    columns: [{
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
                            data: 'code',
                            name: 'code'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
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
                            name: 'export'
                        },
                    ]
                }).on('draw', function() {
                    $('input[type="checkboxs"]').each(function(e) {
                        $(this).attr('type', 'checkbox').css('position', 'unset').css('opacity',
                            'unset').css('position', 'unset');
                    });
                });

                $('table#list-{{ $loop->index }}-with-warehouse').css('width', '100%');
            @endforeach

            @foreach ($data_quantity_delivery_2 as $item)
                $('#list-{{ $loop->index }}').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route("admin.$main.list-delivery-order", $model) }}',
                        data: {
                            load_quantity: '{{ $item->load_quantity }}',
                            is_double: "N"
                        },
                        done: function() {

                        }
                    },
                    columns: [{
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
                            data: 'code',
                            name: 'code'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
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
                            data: 'quantity_used',
                            name: 'quantity_used'
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
                            name: 'export'
                        },
                    ]
                }).on('draw', function() {
                    $('input[type="checkboxs"]').each(function(e) {
                        $(this).attr('type', 'checkbox').css('position', 'unset').css('opacity',
                            'unset').css('position', 'unset');
                    });
                });

                $('table#list-{{ $loop->index }}').css('width', '100%');
            @endforeach
        });
    </script>
    <script>
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#trading');
        sidebarActive('#delivery-order');
    </script>
    @can('view journal')
        <script>
            get_data_journal(`App\\Models\\DeliveryOrder`, '{{ $model->id }}');
        </script>
    @endcan
@endsection
