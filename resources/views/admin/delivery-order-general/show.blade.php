@extends('layouts.admin.layout.index')

@php
    $main = 'delivery-order-general';
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
                        <a href="{{ route('admin.delivery.index') }}">{{ Str::headline($main) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('detail ' . $main) }}
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
                        <h4 class="m-0">Delivery Order</h4>
                        <h1 class="m-0">{{ $model->code }}</h1>
                    </div>
                    <div class="col-md-6 align-self-center">
                        <div class="row justify-content-end">
                            <div class="col-md-3 d-flex flex-column">
                                <h5 class="text-center">{{ Str::headline('status_DO_general') }}</h5>
                                <div class="badge badge-lg badge-{{ delivery_order_general_status()[$model->status]['color'] }}">
                                    {{ Str::headline(delivery_order_general_status()[$model->status]['label']) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-9">
            <x-card-data-table title="{{ 'Delivery order' }}">
                <x-slot name="table_content">
                    @include('components.validate-error')
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ Str::headline('kode') }}</label>
                                <p>
                                    {{ $model->code }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ Str::headline('tanggal') }}</label>
                                <p>
                                    {{ $model->date ? localDate($model->date) : '' }}
                                </p>
                            </div>
                        </div>
                        @if ($model->ware_house)
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('warehouse') }}</label>
                                    <p>
                                        {{ $model->ware_house?->nama }}
                                    </p>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ Str::headline('target pengiriman') }}</label>
                                <p>
                                    {{ localDate($model->target_delivery) }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ Str::headline('tanggal pengiriman') }}</label>
                                <p>
                                    {{ $model->date_send ? localDate($model->date_send) : '-' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ Str::headline('tanggal diterima') }}</label>
                                <p>
                                    {{ $model->date_receive ? localDate($model->date_receive) : '-' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ Str::headline('kode sale order general') }}</label>
                                <p>
                                    {{ $model->sale_order_general?->kode }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ Str::headline('customer') }}</label>
                                <p>
                                    {{ $model->customer->nama }} - {{ $model->customer?->code }}
                                </p>
                            </div>
                        </div>
                        {{-- <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ Str::headline('supply') }}</label>
                                <p>
                                    {{ $model->supply }}
                                </p>
                            </div>
                        </div> --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ Str::headline('drop / ship to') }}</label>
                                <p>
                                    {{ $model->drop }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ Str::headline('status') }}</label>
                                <p>
                                <div class="badge badge-lg badge-{{ delivery_order_general_status()[$model->status]['color'] }}">
                                    {{ Str::headline(delivery_order_general_status()[$model->status]['text']) }} -
                                    {{ Str::headline(delivery_order_general_status()[$model->status]['label']) }}
                                </div>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ Str::headline('deskripsi') }}</label>
                                <p>
                                    {{ $model->deskripsi }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">{{ Str::headline('dibuat pada') }}</label>
                                <p>
                                    {{ toDayDateTimeString($model->created_at) }}
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

                        <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route('admin.delivery-order.index') }}' />

                        @if ($model->check_available_date)
                            @if (!in_array($model->status, ['done', 'reject', 'void']) && $model->check_available_date && $model->is_invoice_created == 0)
                                <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
                            @endif
                        @endif
                    </div>
                </x-slot>
            </x-card-data-table>

            <x-card-data-table title="{{ 'Detail Item' }}">
                <x-slot name="header_content">

                </x-slot>
                <x-slot name="table_content">
                    <x-table theadColor="danger">
                        <x-slot name="table_head">
                            <th>#</th>
                            <th>Item</th>
                            <th>Jumlah Dikirim</th>
                            <th>Jumlah Diterima</th>
                            <th>Jumlah Hilang</th>
                            <th>Jumlah Dikembalikan</th>
                            <th>Jumlah Rusak</th>
                        </x-slot>
                        <x-slot name="table_body">
                            @foreach ($model->delivery_order_general_details as $item)
                                <tr>
                                    <th>{{ $loop->iteration }}</th>
                                    <td>{{ $item->item?->nama }} - {{ $item->item?->kode }}</td>
                                    <td>{{ formatNumber($item->quantity) }} {{ $item->unit?->name }}
                                        ({{ $item->unit?->sort }})
                                    </td>
                                    <td>{{ formatNumber($item->quantity_received) }} {{ $item->unit?->name }}
                                        ({{ $item->unit?->sort }})</td>
                                    <td>{{ formatNumber($item->quantity_lost) }} {{ $item->unit?->name }}
                                        ({{ $item->unit?->sort }})</td>
                                    <td>{{ formatNumber($item->quantity_returned) }} {{ $item->unit?->name }}
                                        ({{ $item->unit?->sort }})</td>
                                    <td>{{ formatNumber($item->quantity_damage) }} {{ $item->unit?->name }}
                                        ({{ $item->unit?->sort }})</td>
                                </tr>
                            @endforeach
                        </x-slot>
                    </x-table>
                </x-slot>
            </x-card-data-table>

            @can('view journal')
                @include('components.journal-table')
            @endcan
        </div>

        <div class="col-md-3">
            {!! $authorization_log_view !!}

            <div id="print-request-container"></div>
            {{-- <x-card-data-table title="{{ 'Action' }}">
                <x-slot name="table_content">
                    @if ($model->check_available_date)
                        @if ($model->status == 'approve')
                            @can("close $main")
                                <x-button color="success" icon="circle-xmark" fontawesome label="close" size="sm" dataToggle="modal" dataTarget="#close-modal" />
                                <x-modal title="close delivery-order" id="close-modal" headerColor="success">
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
                            @endcan
                        @endif
                    @endif
                </x-slot>
            </x-card-data-table> --}}

            <x-card-data-table>
                <x-slot name="table_content">
                    <button type="button" class="btn btn-info" target="_blank" size="md" href="{{ route('delivery-order-general.export.id', ['id' => encryptId($model->id)]) }}" onclick="show_print_out_modal(event)" @authorize_print('delivery_order_general') data-model="{{ \App\Models\DeliveryOrderGeneral::class }}" data-id="{{ $model->id }}" data-print-type="delivery_order_general" data-link="{{ route('admin.delivery-order-general.show', ['delivery_order_general' => encryptId($model->id)]) }}" data-code="{{ $model->code }}" @endauthorize_print><i class="fa fa-file"></i> Export</button>
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

@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script>
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#trading');
        sidebarActive('#delivery-order')

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


        get_request_print_approval(`App\\Models\\DeliveryOrderGeneral`, '{{ $model->id }}', 'delivery_order_general');
    </script>
    @can('view journal')
        <script>
            get_data_journal(`App\\Models\\DeliveryOrderGeneral`, '{{ $model->id }}');
        </script>
    @endcan
@endsection
