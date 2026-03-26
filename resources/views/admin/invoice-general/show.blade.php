@extends('layouts.admin.layout.index')

@php
    $main = 'invoice-general';
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
                        <a href="{{ route('admin.invoice.index') }}">{{ Str::headline($main) }}</a>
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
        <div class="box bg-gradient-secondary-dark text-white">
            <div class="box-body">
                <div class="row justify-content-end">
                    <div class="col-md-6 align-self-center">
                        <h4 class="m-0">Detail Invoice General</h4>
                        <h1 class="m-0">{{ $model->code }}</h1>
                    </div>
                    <div class="col-md-6 align-self-center">
                        <div class="row justify-content-end">
                            <div class="col-md-3 d-flex flex-column">
                                <h5 class="text-center">{{ Str::headline('status_invoice_general') }}</h5>
                                <div class="badge badge-lg badge-{{ get_invoice_status()[$model->status]['color'] }}">
                                    {{ Str::headline(get_invoice_status()[$model->status]['label']) }}
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
            <x-card-data-table title='{{ "Detail $main" }}'>
                <x-slot name="header_content">
                </x-slot>
                <x-slot name="table_content">
                    @include('components.validate-error')
                    <x-table theadColor="danger">
                        <x-slot name="table_head">
                            <th></th>
                            <th></th>
                        </x-slot>
                        <x-slot name="table_body">
                            <tr>
                                <th>{{ Str::headline('kode') }}</th>
                                <td>{{ $model->code }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('sale order general') }}</th>
                                <td>{!! $model->sale_order_general?->kode
                                    ? "<a href='" . route('admin.sales-order-general.show', ['sales_order_general' => $model->sale_order_general->id]) . "' target='_blank'>" . $model->sale_order_general->kode . '</a>'
                                    : $model->invoice_general_details->map(function ($data) {
                                            return "<a href='" . route('admin.sales-order-general.show', ['sales_order_general' => $data->sale_order_general->id]) . "' target='_blank'>" . $data->sale_order_general->kode . '</a>';
                                        })->unique()->implode('<br>') !!}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('customer') }}</th>
                                <td>{{ $model->customer?->nama }} - {{ $model->customer?->code }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('tanggal') }}</th>
                                <td>{{ localDate($model->date) }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('due date') }}</th>
                                <td>{{ localDate($model->due_date) }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('due') }}</th>
                                <td>{{ $model->due }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('term of payment') }}</th>
                                <td>{{ $model->term_of_payments }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('mata uang') }}</th>
                                <td>{{ $model->currency?->nama }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('nilai tukar') }}</th>
                                <td>{{ formatNumber($model->exchange_rate) }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('status') }}</th>
                                <td>
                                    <div class="badge badge-lg badge-{{ get_invoice_status()[$model->status]['color'] }}">
                                        {{ Str::headline(get_invoice_status()[$model->status]['text']) }} -
                                        {{ Str::headline(get_invoice_status()[$model->status]['label']) }}
                                    </div>

                                </td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('payment_status') }}</th>
                                <td>
                                    <div class="badge badge-lg badge-{{ get_invoice_status()[$model->payment_status]['color'] }}">
                                        {{ Str::headline(get_invoice_status()[$model->payment_status]['text']) }} -
                                        {{ Str::headline(get_invoice_status()[$model->payment_status]['label']) }}
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('dibuat pada') }}</th>
                                <td>{{ toDayDateTimeString($model->created_at) }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('faktur pajak') }}</th>
                                <td>
                                    <div id="reference-text-container" class="d-flex">
                                        <p id="reference-text">{{ $model->reference }}</p>
                                        @can('edit faktur pajak invoice-general')
                                            <button class="btn btn-warning btn-xs ml-2 align-self-start" onclick="handleClickEditTaxReference()"><i class="fas fa-pencil"></i></button>
                                        @endcan
                                    </div>
                                    <div id="reference-input-container" class="d-none">
                                        <input type="text" name="reference" id="reference-input" class="form-control tax-reference-mask" value="{{ $model->reference }}" />
                                        &nbsp;
                                        <button class="btn btn-primary btn-sm ml-2 align-self-start" onclick="handleSubmitEditTaxReference()"><i class="fas fa-check"></i></button>
                                    </div>
                                </td>
                            </tr>
                            @if ($model->attachment)
                                <tr>
                                    <th>{{ Str::headline('Lampiran') }}</th>
                                    <td><a href="{{ asset('storage/' . $model->attachment) }}" target="_blank"><i class="fa fa-file" aria-hidden="true"></i> Lihat File Lampiran </a></td>
                                </tr>
                            @endif
                        </x-slot>
                    </x-table>
                </x-slot>
                <x-slot name="footer">
                    <div class="d-flex justify-content-between gap-1">
                        @can("lock $main")
                            @if ($model->invoice_parent()->lock_status)
                                <x-input-checkbox name="lock_status" id="lock_status" label="lock invoice" value="1" checked />
                            @else
                                <x-input-checkbox name="lock_status" id="lock_status" label="lock invoice" value="1" />
                            @endif
                        @endcan
                        <div>
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
                            <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route('admin.invoice.index') }}' />

                            @if (in_array($model->status, ['pending', 'revert']))
                                @if ($model->check_available_date)
                                    @can("edit $main")
                                        <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
                                    @endcan
                                @endif
                            @endif

                            @role('super_admin')
                                @if (in_array($model->status, ['approve', 'done']) && checkAvailableDate($model->date))
                                    @include('components.generate_journal_button', ['model' => get_class($model), 'id' => $model->id, 'type' => 'invoice-general'])
                                @endif
                            @endrole
                        </div>
                    </div>
                </x-slot>
            </x-card-data-table>

            <x-card-data-table title='{{ "Detail item $main" }}'>
                <x-slot name="header_content">
                </x-slot>
                <x-slot name="table_content">
                    <x-table>
                        <x-slot name="table_head">
                            <th>{{ Str::headline('#') }}</th>
                            <th>{{ Str::headline('delivery order') }}</th>
                            <th>{{ Str::headline('Item') }}</th>
                            <th>{{ Str::headline('jumlah Dikirim') }}</th>
                            <th>{{ Str::headline('jumlah Untuk Invoice') }}</th>
                            <th>{{ Str::headline('harga') }}</th>
                            <th>{{ Str::headline('pajak') }}</th>
                            <th>{{ Str::headline('nilai pajak') }}</th>
                            <th>{{ Str::headline('-') }}</th>
                        </x-slot>
                        <x-slot name="table_body">
                            @foreach ($model->invoice_general_details as $invoice_general_detail)
                                <tr>
                                    <th>{{ $loop->iteration }}</th>
                                    <th>{{ $invoice_general_detail->delivery_order_general_detail?->delivery_order_general?->code }}
                                    </th>
                                    <td>{{ $invoice_general_detail->item?->nama }} -
                                        {{ $invoice_general_detail->item?->kode }}
                                    </td>
                                    <td>{{ formatNumber($invoice_general_detail->quantity) }}
                                        {{ $invoice_general_detail->unit?->name }}</td>
                                    <td>{{ formatNumber($invoice_general_detail->invoice_quantity) }}
                                        {{ $invoice_general_detail->unit?->name }}</td>
                                    <td>{{ $model->currency->simbol }} {{ formatNumber($invoice_general_detail->price) }}
                                        /
                                        {{ $invoice_general_detail->unit?->name }}</td>
                                    <td>
                                        @foreach ($invoice_general_detail->invoice_general_detail_taxes as $item)
                                            <span class="d-flex justify-content-between">
                                                <span class="me-10">{{ $item->tax?->name }}</span>
                                                <span>{{ $item->value * 100 }}% </span>
                                            </span>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($invoice_general_detail->invoice_general_detail_taxes as $item)
                                            <span class="text-end">{{ $model->currency?->simbol }}
                                                {{ formatNumber($item->total) }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <span class="d-flex justify-content-between">
                                            <span class="me-10">{{ $model->currency->simbol }}</span>
                                            <span>{{ formatNumber($invoice_general_detail->total) }} </span>
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </x-slot>

                        <x-slot name="table_foot">
                            <tr>
                                <th colspan="8" class="text-end">{{ Str::headline('total') }}</th>
                                <th>
                                    <span class="d-flex justify-content-between">
                                        <span class="me-10">{{ $model->currency->simbol }}</span>
                                        <span>{{ formatNumber($model->total_main) }} </span>
                                    </span>
                                </th>
                            </tr>
                        </x-slot>
                    </x-table>

                    @if ($model->invoice_general_additionals->count() > 0)
                        <x-table>
                            <x-slot name="table_head">
                                <th>#</th>
                                <th>{{ Str::headline('Item') }}</th>
                                <th>{{ Str::headline('Harga') }}</th>
                                <th>{{ Str::headline('Jumlah') }}</th>
                                <th>{{ Str::headline('Pajak') }}</th>
                                <th>{{ Str::headline('Value') }}</th>
                                <th>{{ Str::headline('sub total') }}</th>
                            </x-slot>
                            <x-slot name="table_body">
                                @foreach ($model->invoice_general_additionals as $invoice_general_additional)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $invoice_general_additional->item?->nama }} -
                                            {{ $invoice_general_additional->item?->kode }}</td>
                                        <td>{{ $model->currency?->simbol }}
                                            {{ formatNumber($invoice_general_additional->price) }}
                                            / {{ $invoice_general_additional->unit?->name }}</td>
                                        <td>{{ formatNumber($invoice_general_additional->quantity) }}</td>
                                        <td>
                                            @foreach ($invoice_general_additional->invoice_general_additional_taxes as $tax_item)
                                                <span class="d-flex justify-content-between">
                                                    <span class="me-10">{{ $tax_item->tax?->name }}</span>
                                                    <span>{{ $tax_item->value * 100 }}%</span>
                                                </span>
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach ($invoice_general_additional->invoice_general_additional_taxes as $tax_item)
                                                <span class="d-flex justify-content-between">
                                                    <span class="me-10">{{ $model->currency?->simbol }}</span>
                                                    <span>{{ formatNumber($tax_item->total) }}</span>
                                                </span>
                                            @endforeach
                                        </td>
                                        <td>
                                            <span class="d-flex justify-content-between">
                                                <span class="me-10">{{ $model->currency?->simbol }}</span>
                                                <span>{{ formatNumber($invoice_general_additional->sub_total) }}</span>
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </x-slot>
                            <x-slot name="table_foot">
                                <tr>
                                    <th colspan="6" class="text-end">{{ Str::headline('total pajak') }}</th>
                                    <th>
                                        <span class="d-flex justify-content-between">
                                            <span class="me-10">{{ $model->currency?->simbol }}</span>
                                            <span>{{ formatNumber($model->total_tax_additional) }}</span>
                                        </span>
                                    </th>
                                </tr>
                                <tr>
                                    <th colspan="6" class="text-end">{{ Str::headline('total') }}</th>
                                    <th>
                                        <span class="d-flex justify-content-between">
                                            <span class="me-10">{{ $model->currency?->simbol }}</span>
                                            <span>{{ formatNumber($model->total_additional) }}</span>
                                        </span>
                                    </th>
                                </tr>
                            </x-slot>
                        </x-table>
                    @endif

                    @if ($down_payments)
                        <h2>Down Payment</h2>
                        <x-table theadColor=''>
                            <x-slot name="table_head">
                                <th>Tanggal</th>
                                <th>Kode</th>
                                <th>Nominal</th>
                                <th>Keterangan</th>
                            </x-slot>
                            <x-slot name="table_body">
                                @foreach ($down_payments as $down_payment)
                                    <tr>
                                        <td>{{ localDate($down_payment->invoice_down_payment->date) }}</td>
                                        <td>{{ $down_payment->invoice_down_payment->code }}</td>
                                        <td class="text-end">{{ $down_payment->invoice_down_payment->currency->simbol }} {{ formatNumber($down_payment->invoice_down_payment->down_payment) }}</td>
                                        <td>{{ $down_payment->invoice_down_payment->note }}</td>
                                    </tr>
                                @endforeach
                            </x-slot>
                        </x-table>
                    @endif
                </x-slot>
            </x-card-data-table>

            <x-card-data-table title="payment information">
                <x-slot name="table_content">
                    <x-table theadColor=''>
                        <x-slot name="table_head">
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Bayar</th>
                            <th>Keterangan</th>
                        </x-slot>
                        <x-slot name="table_body">
                            @foreach ($model->invoice_payment() as $invoice_payment)
                                <tr>
                                    <td>{{ localDate($invoice_payment->date) }}</td>
                                    <td class="text-end">{{ $model->currency->simbol }}
                                        {{ formatNumber($invoice_payment->amount_to_receive) }}</td>
                                    <td class="text-end">{{ $model->currency->simbol }}
                                        {{ formatNumber($invoice_payment->receive_amount) }}</td>
                                    <td>{{ $invoice_payment->note }}</td>
                                </tr>
                            @endforeach
                        </x-slot>
                        <x-slot name="table_foot">
                            <tr>
                                <th>TOTAL</th>
                                <th class="text-end">{{ $model->currency->simbol }}
                                    {{ formatNumber($model->invoice_payment()->sum('amount_to_receive')) }}</th>
                                <th class="text-end">{{ $model->currency->simbol }}
                                    {{ formatNumber($model->invoice_payment()->sum('receive_amount')) }}</th>
                                <th></th>
                            </tr>
                            <tr>
                                <th>SISA</th>
                                <th class="text-end"></th>
                                <th class="text-end">{{ $model->currency->simbol }}
                                    {{ formatNumber($model->invoice_payment()->sum('amount_to_receive') - $model->invoice_payment()->sum('receive_amount')) }}
                                </th>
                                <th></th>
                            </tr>
                        </x-slot>
                    </x-table>
                </x-slot>
            </x-card-data-table>

            @can('view journal')
                @include('components.journal-table')
            @endcan
        </div>
        <div class="col-md-4">
            {!! $authorization_log_view !!}

            <div id="print-request-container"></div>
            <div id="receipt-print-request"></div>
            <div id="tax-print-request"></div>
            <div id="invoice_general_with_do-print-request"></div>

            <x-card-data-table title="{{ 'Action' }}">
                <x-slot name="header_content">

                </x-slot>
                <x-slot name="table_content">
                    @if ($model->check_available_date)
                        @if ($model->status == 'approve')
                            @can("close $main")
                                <x-button color="success" icon="circle-xmark" fontawesome label="close" size="sm" dataToggle="modal" dataTarget="#close-modal" />
                                <x-modal title="close invoice" id="close-modal" headerColor="success">
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
                                                <x-button type="submit" color="primary" label="close" size="sm" icon="save" fontawesome />
                                            </div>
                                        </form>
                                    </x-slot>
                                </x-modal>
                            @endcan
                        @endif
                    @endif

                </x-slot>
            </x-card-data-table>

            <x-card-data-table>
                <x-slot name="table_content">
                    <a color="info" class="mb-1 btn btn-info" target="_blank" onclick="show_print_out_modal(event)" href="{{ route('invoice-general.export.id', ['id' => encryptId($model->id)]) }}" @authorize_print('invoice_general') data-model="{{ \App\Models\InvoiceGeneral::class }}" data-id="{{ $model->id }}" data-print-type="invoice_general" data-link="{{ route('admin.invoice-general.show', ['invoice_general' => $model->id]) }}" data-code="{{ $model->code }}" @endauthorize_print><i class="fa fa-file"></i> Export</a>
                    <a color="info" class="mb-1 btn btn-info" target="_blank" onclick="show_print_out_modal(event)" href="{{ route('invoice-general.export.id.with-delivery-order', ['id' => encryptId($model->id)]) }}" @authorize_print('invoice_general') data-model="{{ \App\Models\InvoiceGeneral::class }}" data-id="{{ $model->id }}" data-print-type="invoice_general_with_do" data-link="{{ route('admin.invoice-general.show', ['invoice_general' => $model->id]) }}" data-code="{{ $model->code }}" @endauthorize_print><i class="fa fa-file"></i> Export With DO</a>
                    <a color="info" class="mb-1 btn btn-info" target="_blank" onclick="show_print_out_modal(event)" href="{{ route('invoice-general.export-tax.id', ['id' => encryptId($model->id)]) }}" @authorize_print('invoice_general_tax') data-model="{{ \App\Models\InvoiceGeneral::class }}" data-id="{{ $model->id }}" data-print-type="invoice_general_tax" data-link="{{ route('admin.invoice-general.show', ['invoice_general' => $model->id]) }}" data-code="{{ $model->code }}" @endauthorize_print><i class="fa fa-file"></i> Export Faktur Pajak</a>
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
                                <small class="text-secondary">{{ Str::headline($item->user?->name) }} -
                                    {{ toDayDateTimeString($item->created_at) }}</small>
                            </li>
                        @endforeach
                    </ul>
                </x-slot>
            </x-card-data-table>
        </div>

    </div>

    {{-- <x-card-data-table title='{{ "Detail $main" }}'>
        <x-slot name="header_content">
        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')
            <x-table>
                <x-slot name="table_head">
                    <th></th>
                    <th></th>
                </x-slot>
                <x-slot name="table_body">
                </x-slot>
            </x-table>
        </x-slot>
    </x-card-data-table> --}}
@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js?v=1.2') }}"></script>
    <script>
        sidebarMenuOpen('#trading');
        sidebarActive('#invoice-trading')

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

        function handleClickEditTaxReference() {
            $('#reference-text-container').addClass('d-none');
            $('#reference-text-container').removeClass('d-flex');
            $('#reference-input-container').removeClass('d-none');
            $('#reference-input-container').addClass('d-flex');

            initMaskTaxReference()
        }

        function handleSubmitEditTaxReference() {
            $.post('{{ route('admin.invoice-general.update-reference', $model->id) }}', {
                    _token: '{{ csrf_token() }}',
                    reference: $('#reference-input').val(),
                    id: '{{ $model->id }}'
                }, function({
                    status,
                    message
                }) {
                    $('#reference-text').text($('#reference-input').val());
                    $('#reference-text-container').addClass('d-flex');
                    $('#reference-text-container').removeClass('d-none');
                    $('#reference-input-container').removeClass('d-flex');
                    $('#reference-input-container').addClass('d-none');

                    showAlert(status, message, 'success')
                })
                .catch(({
                    responseJSON
                }) => showAlert('error', responseJSON.message))
        }

        $('#lock_status').click(function(el) {
            $.ajax({
                url: "{{ route('admin.invoice-general.lock', $model->id) }}",
                method: "POST",
                data: {
                    _token: token
                },
                beforeSend: function() {
                    el.target.disabled = true;
                },
                success: function(res) {
                    el.target.disabled = false;
                },
                error: function(xhr, status, error) {
                    el.target.disabled = false;

                    Swal.fire({
                        icon: 'error',
                        title: '',
                        text: xhr.responseJSON.message,
                    });
                }
            })
        })

        get_request_print_approval(`App\\Models\\InvoiceGeneral`, '{{ $model->id }}', 'invoice_general');
        get_request_print_approval(`App\\Models\\InvoiceGeneral`, '{{ $model->id }}', 'invoice_general_receipt', 'receipt-print-request');
        get_request_print_approval(`App\\Models\\InvoiceGeneral`, '{{ $model->id }}', 'invoice_general_tax', 'tax-print-request');
        get_request_print_approval(`App\\Models\\InvoiceGeneral`, '{{ $model->id }}', 'invoice_general_with_do', 'invoice_general_with_do-print-request');
    </script>
    @can('view journal')
        <script>
            get_data_journal(`App\\Models\\InvoiceGeneral`, '{{ $model->id }}');
        </script>
    @endcan
@endsection
