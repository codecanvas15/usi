@extends('layouts.admin.layout.index')

@php
    $main = 'invoice-trading';
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
    @can("view $main")
        <div>
            <div class="box bg-gradient-secondary-dark text-white">
                <div class="box-body">
                    <div class="row justify-content-end">
                        <div class="col-md-6 align-self-center">
                            <h4 class="m-0">Detail Invoice Trading</h4>
                            <h1 class="m-0">{{ $model->kode }}</h1>
                        </div>
                        <div class="col-md-6 align-self-center">
                            <div class="row justify-content-end">
                                <div class="col-md-3 d-flex flex-column">
                                    <h5 class="text-center">{{ Str::headline('status_invoice_trading') }}</h5>
                                    <div class="badge badge-lg badge-{{ get_invoice_status()[$model->status]['color'] }}">
                                        {{ Str::headline(get_invoice_status()[$model->status]['label']) }}
                                    </div>
                                </div>
                                <div class="col-md-3 d-flex flex-column">
                                    <h5 class="text-center">{{ Str::headline('payment_status') }}</h5>
                                    <div class="badge badge-lg badge-{{ get_invoice_status()[$model->payment_status]['color'] }}">
                                        {{ Str::headline(get_invoice_status()[$model->payment_status]['label']) }}
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
                                    <label for="">{{ Str::headline('kode') }}</label>
                                    <p>
                                        {{ $model->kode }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('kode sales order') }}</label>
                                    <p>
                                        <a class="text-primary" href="{{ route('admin.sales-order.show', $model->so_trading) }}" target="_blank" rel="noopener noreferrer">{{ $model->so_trading->nomor_so }}</a>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('faktur pajak') }}</label>
                                    <div id="reference-text-container" class="d-flex">
                                        <p id="reference-text">{{ $model->reference }}</p>
                                        @can('edit faktur pajak invoice-trading')
                                            &nbsp;
                                            <button class="btn btn-warning btn-xs ml-2 align-self-start" id="edit-reference-button"><i class="fas fa-pencil"></i></button>
                                        @endcan
                                    </div>
                                    <div id="reference-input-container" class="d-flex d-none">
                                        <input type="text" name="reference" id="reference-input" class="form-control tax-reference-mask" value="{{ $model->reference }}" />
                                        @if ($model->is_separate_invoice)
                                            <div class="mx-1">/</div>
                                            <input type="text" name="reference_second" id="reference-input-second" class="form-control tax-reference-mask" value="{{ $model->reference }}" />
                                        @endif
                                        &nbsp;
                                        <button class="btn btn-primary btn-sm ml-2 align-self-start" id="save-reference-button"><i class="fas fa-check"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('nomor po external') }}</label>
                                    <p>
                                        {{ $model->nomor_po_external }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('customer') }}</label>
                                    <p>
                                        {{ $model->customer->nama }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('tanggal') }}</label>
                                    <p>
                                        {{ localDate($model->date) }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('due') }}</label>
                                    <p>
                                        {{ $model->due }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('due_date') }}</label>
                                    <p>
                                        {{ localDate($model->due_date) }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('calculate_from') }}</label>
                                    <p>
                                        {{ Str::headline($model->calculate_from) }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('lost_tolerance') }}</label>
                                    <p>
                                        {{ $model->lost_tolerance_type == 'percent' ? formatNumber($model->lost_tolerance * 100) : formatNumber($model->lost_tolerance) }} {{ Str::headline($model->lost_tolerance_type) }}
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('bank_internal') }}</label>
                                    <p>
                                        {{ $model->bank_internal->nama_bank }} - {{ $model->bank_internal->no_rekening }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('currency') }}</label>
                                    <p>
                                        {{ $model->currency->nama }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('exchange_rate') }}</label>
                                    <p>
                                        {{ formatNumber($model->exchange_rate) }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('status') }}</label>
                                    <p>
                                    <div class="badge badge-lg badge-{{ get_invoice_status()[$model->status]['color'] }}">
                                        {{ Str::headline(get_invoice_status()[$model->status]['text']) }} -
                                        {{ Str::headline(get_invoice_status()[$model->status]['label']) }}
                                    </div>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('payment_status') }}</label>
                                    <p>
                                    <div class="badge badge-lg badge-{{ get_invoice_status()[$model->payment_status]['color'] }}">
                                        {{ Str::headline(get_invoice_status()[$model->payment_status]['text']) }} -
                                        {{ Str::headline(get_invoice_status()[$model->payment_status]['label']) }}
                                    </div>
                                    </p>
                                </div>
                            </div>
                            @if ($model->attachment)
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <a href="{{ asset('storage/' . $model->attachment) }}" target="_blank"><i class="fa fa-file" aria-hidden="true"></i> Lihat File Lampiran </a>
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('created_at') }}</label>
                                    <p>
                                        {{ toDayDateTimeString($model->created_at) }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('last modified') }}</label>
                                    <p>
                                        {{ toDayDateTimeString($model->updated_at) }}
                                    </p>
                                </div>
                            </div>
                        </div>

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
                                <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />

                                @if ($model->check_available_date)
                                    @if ($model->status == 'pending' or $model->status == 'revert')
                                        @can("edit $main")
                                            <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
                                        @endcan
                                        @if ($model->status == 'pending')
                                            @can("delete $main")
                                                <x-button color='danger' fontawesome icon="trash" class="w-auto" size="sm" dataToggle='modal' dataTarget='#delete-modal-{{ $model->id }}' />
                                                <x-modal-delete id="delete-modal-{{ $model->id }}" url='{{ "admin.$main.destroy" }}' dataId="{{ $model->id }}" />
                                            @endcan
                                        @endif
                                    @endif

                                @endif

                                @role('super_admin')
                                    @if (in_array($model->status, ['approve', 'done']) && checkAvailableDate($model->date_receive))
                                        @include('components.generate_journal_button', ['model' => get_class($model), 'id' => $model->id, 'type' => 'invoice-trading'])
                                    @endif
                                @endrole
                            </div>
                        </div>
                    </x-slot>

                </x-card-data-table>

                <x-card-data-table title="invoice trading details">
                    <x-slot name="table_content">
                        <x-table theadColor=''>
                            <x-slot name="table_head">
                                <th></th>
                                <th></th>
                            </x-slot>
                            <x-slot name="table_body">
                                <tr>
                                    <th>Kuantitas Kirim</th>
                                    <td>{{ formatNumber($model->total_jumlah_dikirim) }} {{ $model->so_trading->so_trading_detail->item->unit->name ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>Kuantitas Diterima</th>
                                    <td>{{ formatNumber($model->total_jumlah_diterima) }} {{ $model->so_trading->so_trading_detail->item->unit->name ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>Kuantitas Hilang</th>
                                    <td>{{ formatNumber($model->total_lost) }} {{ $model->so_trading->so_trading_detail->item->unit->name ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('calculate_from') }}</th>
                                    <td>{{ Str::headline($model->calculate_from) }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('lost_tolerance') }}</th>
                                    <td>
                                        {{ $model->lost_tolerance_type == 'percent' ? formatNumber($model->lost_tolerance * 100) : formatNumber($model->lost_tolerance) }} {{ Str::headline($model->lost_tolerance_type) }}
                                        @if ($model->lost_tolerance_type == 'percent')
                                            <p>({{ $model->total_jumlah_dikirim * $model->lost_tolerance }} {{ $model->so_trading->so_trading_detail->item->unit->name ?? '' }})</p>
                                        @endif
                                    </td>
                                </tr>
                            </x-slot>
                        </x-table>

                        <x-table theadColor=''>
                            <x-slot name="table_head">
                                <th>item</th>
                                <th>Harga</th>
                                <th>Kuantitas dipesan</th>
                                <th>Kuantitas untuk invoice</th>
                                <th>Subtotal</th>
                            </x-slot>
                            @php
                                $subtotal = $model->subtotal + $model->other_cost;
                            @endphp
                            <x-slot name="table_body">
                                <tr>
                                    <td>{{ $model->item?->nama }} - {{ $model->item?->kode }}</td>
                                    <td class="text-end">{{ $model->currency->simbol }} {{ formatNumber($model->harga) }}</td>
                                    <td class="text-end">{{ formatNumber($model->so_trading?->so_trading_detail?->jumlah) }} {{ $model->so_trading->so_trading_detail->item->unit->name ?? '' }}</td>
                                    <td class="text-end">{{ formatNumber($model->jumlah) }} {{ $model->so_trading->so_trading_detail->item->unit->name ?? '' }}</td>
                                    <td class="text-end">{{ $model->currency->simbol }} {{ formatNumber($model->subtotal) }}</td>
                                </tr>
                                @foreach ($model->inv_trading_add_on as $item)
                                    <tr>
                                        <td>{{ $item->item->nama }} - {{ $item->item->kode }}</td>
                                        <td class="text-end">{{ $model->currency->simbol }} {{ formatNumber($item->price) }}</td>
                                        <td class="text-end">{{ formatNumber($model->so_trading?->so_trading_detail?->jumlah) }}</td>
                                        <td class="text-end">{{ formatNumber($item->quantity) }}</td>
                                        <td class="text-end">{{ $model->currency->simbol }} {{ formatNumber($item->sub_total) }}</td>
                                    </tr>
                                @endforeach
                            </x-slot>
                            <x-slot name="table_foot">
                                <tr>
                                    <td colspan="4" class="text-end">Sub total</td>
                                    <td class="text-end">
                                        <span class="d-flex justify-content-between">
                                            <span id="currency-simbol">{{ $model->currency->simbol }}</span>
                                            <span class="text-end" id="trading-sub-total">{{ formatNumber($subtotal) }}</span>
                                        </span>
                                    </td>
                                </tr>
                                @foreach ($taxes_id as $item)
                                    <tr>
                                        <td colspan="4" class="text-end">{{ $item['name'] }} - {{ $item['value'] * 100 }} %</td>
                                        <td class="text-end">
                                            <span class="d-flex justify-content-between">
                                                <span id="currency-simbol">{{ $model->currency->simbol }}</span>
                                                <span class="text-end" id="trading-tax-{{ $item['tax_id'] }}">{{ formatNumber($item['amount']) }}</span>
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="4" class="text-end">Total</td>
                                    <td class="text-end bg-success text-white">
                                        <span class="d-flex justify-content-between">
                                            <span id="currency-simbol">{{ $model->currency->simbol }}</span>
                                            <span class="text-end" id="trading-total">{{ formatNumber($model->total) }}</span>
                                        </span>
                                    </td>
                            </x-slot>
                        </x-table>

                        @if ($down_payments)
                            <h2>Down Payment</h2>
                            <x-table theadColor=''>
                                <x-slot name="table_head">
                                    <th>Tanggal</th>
                                    <th>Nominal</th>
                                    <th>Keterangan</th>
                                </x-slot>
                                <x-slot name="table_body">
                                    @foreach ($down_payments as $down_payment)
                                        <tr>
                                            <td>{{ localDate($down_payment->invoice_down_payment->date) }}</td>
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
                                        <td class="text-end">{{ $model->currency->simbol }} {{ formatNumber($invoice_payment->amount_to_receive) }}</td>
                                        <td class="text-end">{{ $model->currency->simbol }} {{ formatNumber($invoice_payment->receive_amount) }}</td>
                                        <td>{{ $invoice_payment->note }}</td>
                                    </tr>
                                @endforeach
                            </x-slot>
                            <x-slot name="table_foot">
                                <tr>
                                    <th>TOTAL</th>
                                    <th class="text-end">{{ $model->currency->simbol }} {{ formatNumber($model->invoice_payment()->sum('amount_to_receive')) }}</th>
                                    <th class="text-end">{{ $model->currency->simbol }} {{ formatNumber($model->invoice_payment()->sum('receive_amount')) }}</th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th>SISA</th>
                                    <th class="text-end"></th>
                                    <th class="text-end">{{ $model->currency->simbol }} {{ formatNumber($model->invoice_payment()->sum('amount_to_receive') - $model->invoice_payment()->sum('receive_amount')) }}</th>
                                    <th></th>
                                </tr>
                            </x-slot>
                        </x-table>
                    </x-slot>
                </x-card-data-table>

                <x-card-data-table title="{{ 'delivery order ' }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        <x-table id="do-list">
                            <x-slot name="table_head">
                                <th>#</th>
                                <th>{{ Str::headline('Nomor do') }}</th>
                                <th>{{ Str::headline('Target Delivery') }}</th>
                                <th>{{ Str::headline('Tanggal muat') }}</th>
                                <th>{{ Str::headline('Tanggal bongkar') }}</th>
                                <th>{{ Str::headline('kuantitas_diterima') }}</th>
                            </x-slot>
                            <x-slot name="table_body">

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
                <div id="invoice_trading_receipt"></div>
                <div id="invoice_trading_tax"></div>
                <div id="invoice_trading_tax_transport"></div>
                <div id="invoice_trading_transport"></div>

                <x-card-data-table>
                    <x-slot name="table_content">
                        <a target="_blank" class="btn btn-info mb-1" onclick="show_print_out_modal(event)" href="{{ route('invoice-trading.export.id', ['id' => encryptId($model->id)]) }}" @authorize_print('invoice_trading') data-model="{{ \App\Models\InvoiceTrading::class }}" data-id="{{ $model->id }}" data-print-type="invoice_trading" data-link="{{ route('admin.invoice-trading.show', ['invoice_trading' => $model->id]) }}" data-code="{{ $model->kode }}" @endauthorize_print>Export</a>
                        <a target="_blank" class="btn btn-info mb-1" onclick="show_print_out_modal(event)" href="{{ route('invoice-trading.export.id.with-delivery-order', ['id' => encryptId($model->id)]) }}" @authorize_print('invoice_with_do') data-model="{{ \App\Models\InvoiceTrading::class }}" data-id="{{ $model->id }}" data-print-type="invoice_with_do" data-link="{{ route('admin.invoice-trading.show', ['invoice_trading' => $model->id]) }}" data-code="{{ $model->kode }}" @endauthorize_print>Export With DO</a>
                        <a target="_blank" class="btn btn-info mb-1" onclick="show_print_out_modal(event)" href="{{ route('invoice-trading.export-tax.id', ['id' => encryptId($model->id)]) }}" @authorize_print('invoice_trading_tax') data-model="{{ \App\Models\InvoiceTrading::class }}" data-id="{{ $model->id }}" data-print-type="invoice_trading_tax" data-link="{{ route('admin.invoice-trading.show', ['invoice_trading' => $model->id]) }}" data-code="{{ $model->kode }}" @endauthorize_print>Export Faktur Pajak</a>
                        @if ($model->is_separate_invoice)
                            <a target="_blank" class="btn btn-info mb-1" onclick="show_print_out_modal(event, '&')" href="{{ route('invoice-trading.export-tax.id', ['id' => encryptId($model->id)]) }}?type=transport" @authorize_print('invoice_trading_tax_transport') data-model="{{ \App\Models\InvoiceTrading::class }}" data-id="{{ $model->id }}" data-print-type="invoice_trading_tax" data-link="{{ route('admin.invoice-trading.show', ['invoice_trading' => $model->id]) }}" data-code="{{ $model->kode }}" @endauthorize_print>Export Faktur Pajak Transport</a>
                        @endif
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
    <script src="{{ asset('js/helpers/helpers.js?v=1.1') }}"></script>
    @can("view $main")
        <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.js"></script>
        <script>
            $(document).ready(() => {
                const table = $('table#do-list').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: '{{ route('admin.invoice-trading.list-delivery-order', $model) }}',
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
                            data: 'unload_quantity_realization',
                            name: 'unload_quantity_realization'
                        }
                    ]
                });
            });
        </script>
    @endcan
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script>
        $('body').addClass('sidebar-collapse')
        sidebarMenuOpen('#trading');
        sidebarActive('#invoice-trading');

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

        get_request_print_approval(`App\\Models\\InvoiceTrading`, '{{ $model->id }}', 'invoice_trading');
        get_request_print_approval(`App\\Models\\InvoiceTrading`, '{{ $model->id }}', 'invoice_trading_receipt', 'invoice_trading_receipt');
        get_request_print_approval(`App\\Models\\InvoiceTrading`, '{{ $model->id }}', 'invoice_trading_tax', 'invoice_trading_tax');
        get_request_print_approval(`App\\Models\\InvoiceTrading`, '{{ $model->id }}', 'invoice_trading_tax_transport', 'invoice_trading_tax_transport');
        get_request_print_approval(`App\\Models\\InvoiceTrading`, '{{ $model->id }}', 'invoice_trading_transport', 'invoice_trading_transport');

        $('#edit-reference-button').click(function() {
            $('#reference-input-container').removeClass('d-none');
            $('#reference-text-container').addClass('d-none');

            initMaskTaxReference()
        })

        $('#save-reference-button').click(function() {
            if ($('#reference-input').val() == '') {
                $('#reference-input').addClass('is-invalid');
                return false;
            } else {
                $('#reference-input').removeClass('is-invalid');
            }

            $.ajax({
                url: "{{ route('admin.invoice-trading.update-reference', $model->id) }}",
                method: "POST",
                data: {
                    reference: $('#reference-input').val(),
                    reference_second: $('#reference-input-second').val(),
                    _token: token
                },
                success: function(res) {
                    $('#reference-input-container').addClass('d-none');
                    $('#reference-text-container').removeClass('d-none');
                    var reference = $('#reference-input').val();
                    if ($('#reference-input-second').val() != '') {
                        reference += '/' + $('#reference-input-second').val();
                    }
                    $('#reference-text').html(reference);
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: '',
                        text: xhr.responseJSON.message,
                    });
                }
            })
        })

        $('#lock_status').click(function(el) {
            $.ajax({
                url: "{{ route('admin.invoice-trading.lock', $model->id) }}",
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
    </script>
    @can('view journal')
        <script>
            get_data_journal(`App\\Models\\InvoiceTrading`, '{{ $model->id }}');
        </script>
    @endcan
@endsection
