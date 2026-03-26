@extends('layouts.admin.layout.index')

@php
    $main = 'purchase-order-transport';
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
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.purchase.index') }}">{{ Str::headline('Purchase') }}</a>
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
    @can('view purchase-transport')
        <div>
            <div class="box bg-gradient-success-dark text-white">
                <div class="box-body">
                    <div class="row justify-content-end">
                        <div class="col-md-6 align-self-center">
                            <h4 class="m-0">Detail Purchase Order Transport</h4>
                            <h1 class="m-0">{{ $model->kode }}</h1>
                        </div>
                        <div class="col-md-6 align-self-center">
                            <div class="row justify-content-end">
                                <div class="col-md-3 d-flex flex-column">
                                    <h5 class="text-center">{{ Str::headline('status_purchase_transport') }}</h5>
                                    <div class="badge badge-lg badge-{{ purchase_transport_status()[$model->status]['color'] }}">
                                        {{ Str::headline(purchase_transport_status()[$model->status]['label']) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <x-card-data-table title="{{ 'detail ' . $main }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        @include('components.validate-error')

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('cabang') }}</label>
                                    <p>{{ $model->branch->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('target_delivery') }}</label>
                                    <p>{{ localDate($model->target_delivery) }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('kode') }}</label>
                                    <p>{{ $model->kode }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('vendor transportir') }}</label>
                                    <p>{{ $model->vendor?->nama }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('tipe') }}</label>
                                    <p>{{ Str::headline($model->type) }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('tujuan kirim') }}</label>
                                    <p>{{ Str::headline($model->delivery_destination == 'to_warehouse' ? 'ke gudang' : 'ke customer') }}</p>
                                </div>
                            </div>
                            @if ($model->ware_house)
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">{{ Str::headline('gudang') }}</label>
                                        <p>{{ Str::headline($model->ware_house?->nama) }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if ($model->so_trading)
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">{{ Str::headline('sale order trading') }}</label>
                                        <p>{{ $model->so_trading?->nomor_so }}</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">{{ Str::headline('customer') }}</label>
                                        <p>{{ $model->so_trading?->customer?->nama }}</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">{{ Str::headline('supply point') }}</label>
                                        <p>{{ $model->so_trading?->sh_number->sh_number_details()->where('type', 'Supply Point')->first()?->alamat ?? '' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">{{ Str::headline('drop point') }}</label>
                                        <p>{{ $model->so_trading?->sh_number->sh_number_details()->where('type', 'Drop Point')->first()?->alamat ?? '' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($model->po_trading)
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">{{ Str::headline('no. purchase order trading') }}</label>
                                        <p>{{ $model->po_trading?->nomor_po }}</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">{{ Str::headline('vendor') }}</label>
                                        <p>{{ $model->po_trading?->vendor?->nama }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('mata uang') }}</label>
                                    <p>{{ $model->currency->kode . ' / ' . $model->currency->nama . ' / ' . $model->currency->negara }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('kurs') }}</label>
                                    <p>{{ formatNumber($model->exchange_rate) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('status') }}</label>
                                    <p>
                                    <div class="badge badge-lg badge-{{ purchase_transport_status()[$model->status]['color'] }} mb-1">
                                        {{ purchase_transport_status()[$model->status]['label'] }} - {{ purchase_transport_status()[$model->status]['text'] }}
                                    </div>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <x-table class="mt-20">
                            <x-slot name="table_head">
                                <th>#</th>
                                <th>{{ Str::headline('jumlah_do') }} vehicle</th>
                                <th>{{ Str::headline('jenis_kendaran') }}</th>
                                <th>{{ Str::headline('informasi_kendaraan') }}</th>
                                <th>{{ Str::headline('qty') }}</th>
                                <th>{{ Str::headline('harga') }}</th>
                                <th>{{ Str::headline('total') }}</th>
                            </x-slot>
                            <x-slot name="table_body">
                                @foreach ($model->purchase_transport_details as $item)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ formatNumber($item->jumlah_do) }} Vehicle</td>
                                        <td>{{ $item->vehicle_type ?? '-' }}</td>
                                        <td>{{ $item->vehicle_info ?? '-' }}</td>
                                        <td>{{ formatNumber($item->jumlah) }}
                                            @if ($model->so_trading)
                                                {{ $model->so_trading->so_trading_detail->item->unit->name ?? '' }}
                                            @else
                                                {{ $model->po_trading->po_trading_detail->item->unit->name ?? '' }}
                                            @endif
                                        </td>
                                        <td class="text-end">{{ $model->currency->simbol }} {{ formatNumber($model->harga) }}</td>
                                        <td class="text-end">{{ $model->currency->simbol }} {{ formatNumber($item->jumlah_do * $item->jumlah * $model->harga) }}</td>
                                    </tr>
                                @endforeach
                                @foreach ($model->purchase_transport_taxes as $item)
                                    <tr>
                                        <td colspan="5"></td>
                                        <td class="text-end">{{ $item->tax->name }} {{ $item->value * 100 }}%</td>
                                        <td class="text-end">{{ $model->currency->simbol }} {{ formatNumber($item->value * $model->sub_total) }} </td>
                                        <td></td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="5"></td>
                                    <td class="fw-bold text-end">Total</td>

                                    <td class="bg-success text-white">
                                        <div class="d-flex">
                                            <span class="me-10">
                                                {{ $model->currency->simbol }}
                                            </span>
                                            <span class="ms-auto">
                                                {{ formatNumber($model->total) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td></td>
                                </tr>
                            </x-slot>
                        </x-table>
                    </x-slot>

                    <x-slot name="footer">
                        <div class="d-flex justify-content-end gap-1">
                            {!! $auth_revert_void_button !!}
                            <x-button type="button" color='primary' fontawesome icon="history" label="riwayat transaksi" class="w-auto" size="sm" id="history-button" />
                            <x-modal title="riwayat transaksi" id="history-modal" headerColor="success">
                                <x-slot name="modal_body">
                                    @csrf
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Transaksi</th>
                                                    <th>Nomor</th>
                                                </tr>
                                            </thead>
                                            <tbody id="history-list">

                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-10 border-top pt-10">
                                        <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                    </div>
                                </x-slot>
                            </x-modal>

                            <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />

                            @if ($model->check_available_date && $model->type == 'not_double_handling')
                                @if ($model->status == 'pending' or $model->status == 'revert')
                                    @can('edit purchase-transport')
                                        <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
                                    @endcan
                                @endif
                            @endif
                        </div>
                    </x-slot>

                </x-card-data-table>

                <x-card-data-table title="{{ 'delivery order ' }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        @include('components.validate-error')
                        <x-table id="do-list">
                            <x-slot name="table_head">
                                <th>#</th>
                                <th>{{ Str::headline('Nomor do') }}</th>
                                <th>{{ Str::headline('Target Delivery') }}</th>
                                <th>{{ Str::headline('Tanggal muat') }}</th>
                                <th>{{ Str::headline('Tanggal bongkar') }}</th>
                                <th>{{ Str::headline('kuantitas_dikirim') }}</th>
                                <th>{{ Str::headline('kuantitas_diterima') }}</th>
                                <th>{{ Str::headline('moda transport') }}</th>
                                <th>{{ Str::headline('status') }}</th>
                                <th>{{ Str::headline('created at') }}</th>
                            </x-slot>
                            <x-slot name="table_body">

                            </x-slot>
                        </x-table>
                    </x-slot>
                </x-card-data-table>
            </div>
            <div class="col-md-4">
                @if (!$model->purchase_transport_id)
                    {!! $authorization_log_view !!}
                @endif

                <div id="print-request-container"></div>
                <x-card-data-table title="{{ 'Action' }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        @if ($model->purchase_transport_id)
                            <h5>Ubah Status Purchase transport di <a href="{{ route('admin.purchase-order-transport.show', $model->purchase_transport_id) }}" target="_blank" rel="noopener noreferrer">{{ $model->purchase_transport?->kode }}</a></h5>
                        @endif
                    </x-slot>
                </x-card-data-table>

                <x-card-data-table>
                    <x-slot name="table_content">
                        {{-- <x-button type="button" color="info" label="Export" target="_blank" icon="file" fontawesome soft block size="md" link="{{ route($main . '.export.id', ['id' => encryptId($model->id)]) }}" onclick="show_print_out_modal(event)" /> --}}
                        <x-button-auth-print type="purchase_order_transport" model="{{ \App\Models\PurchaseTransport::class }}" did="{{ $model->id }}" href="{{ route($main . '.export.id', ['id' => encryptId($model->id)]) }}" code="{{ $model->kode }}" label="Export" link="" />
                    </x-slot>
                </x-card-data-table>

                <x-card-data-table title="{{ 'Status Log' }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        <ul class="list-group">
                            @foreach ($status_logs as $item)
                                <li class="list-group-item">
                                    @if ($item->from_status && $item->to_status)
                                        <h5 class="fw-bold mb-0">From {{ Str::headline($item->from_status) }} To
                                            {{ Str::headline($item->to_status) }}</h5>
                                    @elseif (!$item->from_status && $item->to_status)
                                        <h5 class="fw-bold mb-0">{{ Str::headline($item->to_status) }}</h5>
                                    @endif
                                    <p class="mb-0">{{ Str::title($item->message) }}</p>
                                    <small class="text-secondary">{{ Str::headline($item->user->name ?? '-') }} - {{ toDayDateTimeString($item->created_at) }}</small>
                                </li>
                            @endforeach
                        </ul>
                    </x-slot>
                </x-card-data-table>
                <x-card-data-table title="{{ 'Data Log' }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        <ul class="list-group">
                            @foreach ($activity_logs as $item)
                                <li class="list-group-item">
                                    <h5 class="fw-bold mb-0">{{ Str::headline($item->event) }}</h5>
                                    <p class="mb-0">{{ Str::title($item->description) }}</p>
                                    <small class="text-secondary">{{ Str::headline($item->user->name ?? '-') }} - {{ toDayDateTimeString($item->created_at) }}</small>
                                </li>
                            @endforeach
                        </ul>
                    </x-slot>
                </x-card-data-table>
            </div>
        </div>
    @endcan
@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script>
        sidebarMenuOpen('#purchase-menu');
        sidebarActive('#purchase')

        $('#history-button').on('click', function() {
            $.ajax({
                url: `{{ route('admin.purchase-order-transport.history', $model->id) }}`,
                success: function({
                    data
                }) {
                    $('#history-list').html('');
                    $.each(data, function(key, value) {
                        let link = `<a href="${value.link}" target="_blank" class="text-primary text-decoration-underline hover_text-dark">${value.code}</a>`;
                        $('#history-list').append(`
                                <tr>
                                    <td>${localDate(value.date)}</td>
                                    <td class="text-capitalize">${value.menu}</td>
                                    <td>${link}</td>
                                </tr>
                            `);
                    });

                    $('#history-modal').modal('show');
                }
            });
        });
    </script>

    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script>
        $(document).ready(() => {
            const table = $('table#do-list').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: '{{ route("admin.$main.delivery-orders", $model) }}',
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
                        data: 'created_at',
                        name: 'created_at'
                    }
                ]
            });

        });

        get_request_print_approval(`App\\Models\\PurchaseTransport`, '{{ $model->id }}', 'purchase_order_transport');
    </script>
@endsection
