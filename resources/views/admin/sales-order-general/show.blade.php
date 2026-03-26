@extends('layouts.admin.layout.index')

@php
    $main = 'sales-order-general';
@endphp

@section('title', Str::headline("Detail $main") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route('admin.sales.index') }}">{{ Str::headline($main) }}</a>
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
    @can("view $main")
        <div>
            <div class="box bg-gradient-info-dark text-white">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6 align-self-center">
                            <h4 class="m-0">Detail Sales Order General</h4>
                            <h1 class="m-0">{{ $model->kode }}</h1>
                        </div>
                        <div class="col-md-6 align-self-center">
                            <div class="row justify-content-end">
                                <div class="col-md-3 d-flex flex-column">
                                    <h5 class="text-center">{{ Str::headline('status_SO_general') }}</h5>
                                    <div class="badge badge-lg badge-{{ sale_order_general_status()[$model->status]['color'] }}">
                                        {{ Str::headline(sale_order_general_status()[$model->status]['label']) }}
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('cabang') }}</label>
                                    <p>{{ $model->branch?->name }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('kode') }}</label>
                                    <p>{{ $model->kode }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('tanggal') }}</label>
                                    <p>{{ localDate($model->tanggal) }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('customer') }}</label>
                                    <p>{{ $model->customer->nama }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('no. po customer') }}</label>
                                    <p>{{ $model->no_po_external }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('ship to/drop point') }}</label>
                                    <p>{{ $model->drop_point }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('status') }}</label>
                                    <br>
                                    <div class="badge badge-lg badge-{{ sale_order_general_status()[$model->status]['color'] }} mb-1">
                                        {{ Str::headline(sale_order_general_status()[$model->status]['text']) }} -
                                        {{ Str::headline(sale_order_general_status()[$model->status]['label']) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('mata uang') }}</label>
                                    <p>{{ $model->currency->kode . ' / ' . $model->currency->nama . ' / ' . $model->currency->negara }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('nilai tukar') }}</label>
                                    <p>{{ formatNumber($model->exhange_rate) }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('quotation') }}</label>
                                    <p>
                                        @if ($model->quotation)
                                            <x-button color="info" link="{{ url('storage/' . $model->quotation) }}" size="sm" icon="file" fontawesome />
                                        @else
                                            <x-button badge color="danger" size="sm" icon="eye-slash" label="no file" fontawesome />
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

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
                            <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link="{{ route('admin.sales-order.index') }}" />

                            @if ($model->check_available_date)
                                @if (in_array($model->status, ['approve', 'partial-sent']))
                                    <x-button color='info' fontawesome icon="file-invoice" class="w-auto" size="sm" link="{{ route('admin.invoice-general.create') }}" />
                                    <x-button color='success' fontawesome icon="truck-fast" class="w-auto" size="sm" link="{{ route('admin.delivery-order-general.create') }}" />
                                @endif

                                @if (in_array($model->status, ['pending', 'revert']))
                                    @can("edit $main")
                                        <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
                                    @endcan
                                @endif
                            @endif
                        </div>
                    </x-slot>

                </x-card-data-table>

                <x-card-data-table title="item Details">
                    <x-slot name="table_content">
                        <x-table>
                            <x-slot name="table_head">
                                <th>{{ Str::headline('#') }}</th>
                                <th>{{ Str::headline('item') }}</th>
                                <th>{{ Str::headline('harga') }}</th>
                                <th>{{ Str::headline('qty') }}</th>
                                <th>{{ Str::headline('qty dikirim') }}</th>
                                <th>{{ Str::headline('sub_total') }}</th>
                                <th>{{ Str::headline('tax') }}</th>
                                <th>{{ Str::headline('tax value') }}</th>
                                <th>{{ Str::headline('total') }}</th>
                                <th>{{ Str::headline('keterangan') }}</th>
                            </x-slot>
                            <x-slot name="table_body">
                                @foreach ($model->sale_order_general_details as $item)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $item->item?->nama }} - {{ $item->item?->kode }}</td>
                                        <td>{{ get_currency_symbol($model->currency_id) }} {{ formatNumber($item->price) }}
                                        </td>
                                        <td>{{ formatNumber($item->amount) }} {{ $item->unit?->name }}</td>
                                        <td>{{ formatNumber($item->sended) }} {{ $item->unit?->name }}</td>
                                        <td class="text-end">{{ get_currency_symbol($model->currency_id) }}
                                            {{ formatNumber($item->sub_total) }}</td>
                                        <td>
                                            @forelse($item->sale_order_general_detail_taxes as $tax_item)
                                                <div>
                                                    {{ $tax_item->tax?->name }} - {{ $tax_item->value * 100 }}%
                                                </div>
                                            @empty
                                                <div class="badge badge-lg badge-danger">
                                                    {{ Str::headline('no tax') }}
                                                </div>
                                            @endforelse
                                        </td>
                                        <td>
                                            @forelse($item->sale_order_general_detail_taxes as $tax_item)
                                                <div class="text-end">
                                                    {{ get_currency_symbol($model->currency_id) }}
                                                    {{ formatNumber($tax_item->total) }}
                                                </div>
                                            @empty
                                                <div class="badge badge-lg badge-danger">
                                                    {{ Str::headline('no tax') }}
                                                </div>
                                            @endforelse
                                        </td>
                                        <td class="text-end">{{ get_currency_symbol($model->currency_id) }}
                                            {{ formatNumber($item->total) }}</td>
                                            <td>
                                            <x-button color="primary" dataToggle="modal" dataTarget="#detail-modal-{{ $item->id }}" icon="align-left" fontawesome size="sm" />
                                            <x-modal title="keterangan item" id="detail-modal-{{ $item->id }}">
                                                <x-slot name="modal_body">
                                                    <h4>keterangan</h4>
                                                    <p>{{ $item->notes }}</p>
                                                </x-slot>
                                                <x-slot name="modal_footer">
                                                    <x-button type="button" color="secondary" dataDismiss="modal" label="close" />
                                                </x-slot>
                                            </x-modal>
                                        </td>
                                    </tr>
                                @endforeach
                            </x-slot>

                            <x-slot name="table_foot">
                                <tr>
                                    <th colspan="8" class="text-end">{{ Str::headline('DPP') }}</th>
                                    <td class="text-end">{{ get_currency_symbol($model->currency_id) }}
                                        {{ formatNumber($model->sale_order_general_details->sum('sub_total')) }}</td>
                                </tr>
                                <tr>
                                    <th colspan="8" class="text-end">{{ Str::headline('total pajak') }}</th>
                                    <td class="text-end">{{ get_currency_symbol($model->currency_id) }}
                                        {{ formatNumber($model->total - $model->sale_order_general_details->sum('sub_total')) }}</td>
                                </tr>
                                <tr>
                                    <th colspan="8" class="text-end">{{ Str::headline('total') }}</th>
                                    <th class="bg-success fw-bold text-end">{{ get_currency_symbol($model->currency_id) }}
                                        {{ formatNumber($model->total) }}</th>
                                </tr>
                            </x-slot>
                        </x-table>
                    </x-slot>
                </x-card-data-table>
            </div>
            <div class="col-md-4">
                {!! $authorization_log_view !!}
                <div id="print-request-container"></div>

                <x-card-data-table title="{{ 'Action' }}">
                    <x-slot name="table_content">
                        @if ($model->check_available_date)
                            @if (in_array($model->status, ['approve', 'partial-sent']))
                                @can("close $main")
                                    @if ($model->check_available_date)
                                        <x-button color="success" icon="circle-xmark" fontawesome label="close" size="sm" dataToggle="modal" dataTarget="#close-modal" />
                                        <x-modal title="close sale order general" id="close-modal" headerColor="success">
                                            <x-slot name="modal_body">
                                                <form action='{{ route("admin.$main.update-status", $model) }}' method="post">
                                                    @csrf
                                                    <input type="hidden" name="status" value="done">
                                                    <div class="mt-10">
                                                        <div class="form-group">
                                                            <x-input type="text" id="message" label="message" name="message" required />
                                                        </div>
                                                    </div>
                                                    <div class="mt-10 border-top pt-10">
                                                        <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                        <x-button type="submit" color="primary" label="save" size="sm" icon="save" fontawesome />
                                                    </div>
                                                </form>
                                            </x-slot>
                                        </x-modal>
                                    @endif
                                @endcan
                            @endif
                        @endif
                    </x-slot>
                </x-card-data-table>

                <x-card-data-table>
                    <x-slot name="table_content">
                        <a class="btn btn-info mb-1" target="_blank" href="{{ route('sales-order-general.export.id', ['id' => encryptId($model->id)]) }}" onclick="show_print_out_modal(event)" @authorize_print('sale_order_general') data-model="{{ \App\Models\SaleOrderGeneral::class }}" data-id="{{ $model->id }}" data-print-type="sale_order_general" data-link="{{ route('admin.sales-order-general.show', ['sales_order_general' => $model->id]) }}" data-code="{{ $model->kode }}" @endauthorize_print><i class="fa fa-file"></i> Export</a>
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
                                    <small class="text-secondary">{{ Str::headline($item->user->name ?? '-') }} -
                                        {{ toDayDateTimeString($item->created_at) }}</small>
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
                                    <small class="text-secondary">{{ Str::headline($item->user->name ?? '-') }} -
                                        {{ toDayDateTimeString($item->created_at) }}</small>
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
        $('body').addClass('sidebar-collapse')
        sidebarMenuOpen('#trading');
        sidebarActive('#sales-order');

        $('#history-button').on('click', function() {
            $.ajax({
                url: '{{ route("admin.$main.history", $model->id) }}',
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

        get_request_print_approval(`App\\Models\\SaleOrderGeneral`, '{{ $model->id }}', 'sale_order_general');
    </script>
@endsection
