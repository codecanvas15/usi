@extends('layouts.admin.layout.index')

@php
    $main = 'invoice-trading';
@endphp

@section('title', Str::headline("Edit $main") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($main) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Edit ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @if ($errors->any())
        <x-card-data-table>
            <x-slot name="header_content">
            </x-slot>

            <x-slot name="table_content">
                @include('components.validate-error')
            </x-slot>
        </x-card-data-table>
    @endif
    <form action="{{ route('admin.invoice-trading.update', $model) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <x-card-data-table title="{{ 'edit ' . $main }}">
            <x-slot name="table_content">
                <x-table>
                    <x-slot name="table_head">
                        <th></th>
                        <th></th>
                    </x-slot>
                    <x-slot name="table_body">
                        <tr>
                            <th>{{ Str::headline('kode') }}</th>
                            <td>{{ $model->kode }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('kode sales order') }}</th>
                            <td>
                                <a class="text-primary" href="{{ route('admin.sales-order.show', $model->so_trading) }}" target="_blank" rel="noopener noreferrer">{{ $model->so_trading->nomor_so }}</a>
                            </td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('nomor po external') }}</th>
                            <td>{{ $model->nomor_po_external }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('customer') }}</th>
                            <td>{{ $model->customer->nama }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('tanggal') }}</th>
                            <td>{{ $model->so_trading->tanggal }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('sh No.') }}</th>
                            <td>{{ $model->so_trading->sh_number->kode }}</td>
                        </tr>
                        @foreach ($model->so_trading->sh_number->sh_number_details as $item)
                            <tr>
                                <th>{{ Str::headline($item->type) }}</th>
                                <td>{{ $item->alamat }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <th>{{ Str::headline('currency') }}</th>
                            <td>{{ $model->currency->nama }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('exchange_rate') }}</th>
                            <td>{{ formatNumber($model->exchange_rate) }}</td>
                        </tr>
                    </x-slot>
                </x-table>
            </x-slot>

        </x-card-data-table>

        <x-card-data-table title="list delivery order">
            <x-slot name="header_content">

            </x-slot>
            <x-slot name="table_content">
                <x-table theadColor="danger" id="delivery-order-table">
                    <x-slot name="table_head">
                        <th>#</th>
                        <th>{{ Str::headline('kode') }}</th>
                        <th>{{ Str::headline('target pengiriman') }}</th>
                        <th>{{ Str::headline('tanggal muat') }}</th>
                        <th>{{ Str::headline('tanggal bongkar') }}</th>
                        <th>{{ Str::headline('kuantitas kirim') }}</th>
                        <th>{{ Str::headline('kuantitas diterima') }}</th>
                        <th>{{ Str::headline('losses') }}</th>
                    </x-slot>
                    <x-slot name="table_body">
                        @php
                            $total_losses = 0;
                        @endphp
                        @foreach ($model->invoice_trading_details as $invoice_trading_detail)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $invoice_trading_detail->delivery_order->code }}</td>
                                <td>{{ localDate($invoice_trading_detail->delivery_order->target_delivery) }}</td>
                                <td>{{ localDate($invoice_trading_detail->delivery_order->load_date) }}</td>
                                <td>{{ localDate($invoice_trading_detail->delivery_order->unload_date) }}</td>
                                <td class="text-end">{{ formatNumber($invoice_trading_detail->jumlah_dikirim) }}</td>
                                <td class="text-end">{{ formatNumber($invoice_trading_detail->jumlah_diterima) }}</td>
                                <td class="text-end">{{ formatNumber($invoice_trading_detail->jumlah_dikirim - $invoice_trading_detail->jumlah_diterima) }}</td>
                            </tr>
                            @php
                                $total_losses += $invoice_trading_detail->jumlah_dikirim - $invoice_trading_detail->jumlah_diterima;
                            @endphp
                        @endforeach
                    </x-slot>
                    <x-slot name="table_foot">
                        <tr>
                            <td colspan="5" class="text-end">Total</td>
                            <td class="text-end">{{ formatNumber($model->invoice_trading_details->sum('jumlah_dikirim')) }}</td>
                            <td class="text-end">{{ formatNumber($model->invoice_trading_details->sum('jumlah_diterima')) }}</td>
                            <td class="text-end">{{ formatNumber($total_losses) }}</td>
                        </tr>
                    </x-slot>
                </x-table>
            </x-slot>
        </x-card-data-table>

        <x-card-data-table title="invoice trading" id="detail-item-card">
            <x-slot name="header_content">

            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')

                <div id="main-form-invoice-card">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input class="datepicker-input" name="date" label="date" id="invoice-date" required value="{{ localDate($model->date) }}" onchange="calculateDueDate()" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <x-input type="text" name="top" label="TOP" id="top" required readonly value="{{ $model->customer->term_of_payment }}" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <x-input type="number" name="due" label="jatuh tempo (hari)" id="due" required value="{{ $model->due }}" onkeyup="calculateDueDate()" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input class="datepicker-input" name="due_date" label="due_date" id="due_date" value="{{ localDate($model->due_date) }}" required readonly />
                            </div>
                        </div>
                        @if ($model->inv_trading_add_on->count() > 0)
                            <div class="col-md-3 d-flex align-self-start">
                                @if ($model->is_separate_invoice)
                                    <x-input-checkbox label="Pisah Invoice" name="is_separate_invoice" id="is_separate_invoice" value="1" checked />
                                @else
                                    <x-input-checkbox label="Pisah Invoice" name="is_separate_invoice" id="is_separate_invoice" value="1" />
                                @endif
                            </div>
                        @endif
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-select name="bank_internal_id[]" label="bank_internal" id="bank_internal_id" required multiple>
                                    @foreach ($model->bankInternals() as $bank_internal)
                                        <option value="{{ $bank_internal->id }}" selected>{{ $bank_internal->nama_bank }} - {{ $bank_internal->no_rekening }}</option>
                                    @endforeach
                                </x-select>
                                <span class="text-end text-danger">
                                    Jika bank internal kosong masukkan dari data master customer.
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input type="file" name="attachment" label="lampiran" id="attachment" accept=".jpg, .jpeg, .png, .pdf, .doc, .docx, .xls, .xlsx" helpers="jpg/jpeg/png/pdf/doc/docx/xls/xlsx | max. 5 MB" />
                            </div>
                            @if ($model->attachment)
                                <a href="{{ asset('storage/' . $model->attachment) }}" target="_blank"><i class="fa fa-file" aria-hidden="true"></i> Lihat File Lampiran </a>
                            @endif
                        </div>
                    </div>

                    <div class="row mt-20">
                        <div class="col-md-4">
                            <x-select name="invoice_down_payment_id[]" label="Down Payment" id="invoice_down_payment_id" multiple>
                                @foreach ($down_payments as $down_payment)
                                    <option value="{{ $down_payment->invoice_down_payment_id }}" selected>{{ $down_payment->invoice_down_payment->code }}</option>
                                @endforeach
                            </x-select>
                        </div>
                        <div class="col-md-12"></div>
                        <div class="col-md-4">
                            <x-select name="calculate_from" label="calculate from" id="calculate-from" required>
                                <option value="{{ $model->calculate_from }}">{{ Str::headline($model->calculate_from) }}</option>
                            </x-select>
                        </div>
                        <div class="col-md-4">
                            <x-select name="lost_tolerance_type" id="lost-tolerance-type" disabled required>
                                <option value="{{ $model->lost_tolerance_type }}">{{ Str::headline($model->lost_tolerance_type) }}</option>
                            </x-select>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <x-input type="text" name="lost_tolerance" value="{{ formatNumber($model->lost_tolerance_type == 'percent' ? $model->lost_tolerance * 100 : $model->lost_tolerance) }}" id="lost-tolerance" required />
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="form-group">
                                <x-button color="info" size="sm" icon="rotate" fontawesome label="calculate" id="calculate-btn" />
                            </div>
                        </div>
                    </div>
                </div>

                <div id="detail-invoice-resume-form">
                    <div class="mt-20">
                        <h4 class="fw-bold">Resume Item</h4>
                        <x-table theadColor='dark' id="sale-order-resume-table">
                            <x-slot name="table_head">
                                <th></th>
                                <th></th>
                            </x-slot>
                            <x-slot name="table_body">
                                <tr>
                                    <th>Kuantitas Kirim</th>
                                    <td id="resume-quantity-sended">{{ formatNumber($model->invoice_trading_details->sum('jumlah_dikirim')) }}</td>
                                </tr>
                                <tr>
                                    <th>Kuantitas Diterima</th>
                                    <td id="resume-quantity-received">{{ formatNumber($model->invoice_trading_details->sum('jumlah_diterima')) }}</td>
                                </tr>
                                <tr>
                                    <th>Kuantitas Hilang</th>
                                    <td id="resume-quantity-lost">{{ formatNumber($model->total_lost) }}</td>
                                </tr>
                                <tr>
                                    <th>Calculate From</th>
                                    <td id="resume-calculate-from">{{ Str::headline($model->calculate_from) }}</td>
                                </tr>
                                <tr>
                                    <th>Lost Tolerance</th>
                                    <td id="resume-lost-tolerance">{{ Str::headline($model->lost_tolerance_type) }}</td>
                                </tr>
                            </x-slot>
                        </x-table>
                    </div>
                </div>

                <div id="detail-item-form">
                    <div class="mt-20">
                        <h4 class="fw-bold">Trading Item</h4>
                        <x-table theadColor='dark'>
                            <x-slot name="table_head">
                                <th>Item</th>
                                <th>Harga</th>
                                <th>Kuantitas Dipesan</th>
                                <th>Kuantitas untuk invoice</th>
                                <th>Subtotal</th>
                            </x-slot>

                            <x-slot name="table_body">
                                <tr>
                                    <td>{{ $model->item->kode }} - {{ $model->item->nama }}</td>
                                    <td>{{ formatNumber($model->harga) }} / {{ $model->so_trading->so_trading_detail->item->unit->name ?? '' }} </td>
                                    <td>{{ formatNumber($model->so_trading->so_trading_detail->jumlah) }} {{ $model->so_trading->so_trading_detail->item->unit->name ?? '' }}</td>
                                    <td>
                                        <span id="quantity-final">{{ formatNumber($model->jumlah) }} {{ $model->so_trading->so_trading_detail->item->unit->name ?? '' }}</span>
                                        @can('change quantity-invoice-trading')
                                            <x-button type="button" color="primary" dataToggle="modal" dataTarget="#changeQuantityInvoice" icon="edit" fontawesome size="sm" />
                                            <x-modal title="Ubah Kuantitas Untuk Invoice" id="changeQuantityInvoice">
                                                <x-slot name="modal_body">
                                                    <div class="form-group">
                                                        <x-input type="quantity_for_invoice_default" class="commas-form" name="quantity_for_invoice_before" id="quantity-for-invoice-before" label="kuantitas awal" readonly value="{{ formatNumber($model->jumlah) }}" />
                                                    </div>
                                                    <div class="form-group">
                                                        <x-input type="quantity_for_invoice_canged" class="commas-form-five" name="quantity_for_invoice_after" id="quantity-for-invoice-after" label="kuantitas untuk invoice" value="{{ floatDotThreeDigitsFormat($model->jumlah) }}" />
                                                    </div>
                                                </x-slot>
                                            </x-modal>
                                        @else
                                            <input type="hidden" name="quantity_for_invoice_after" id="quantity-for-invoice-after" value="{{ floatDotThreeDigitsFormat($model->jumlah) }}" />
                                        @endcan
                                    </td>
                                    <td class="trading-sub-total">{{ formatNumber($model->subtotal) }}</td>
                                </tr>
                            </x-slot>

                            <x-slot name="table_foot">
                                <tr>
                                    <td colspan="4" class="text-end">Sub Total</td>
                                    <td class="text-end">
                                        <span class="d-flex justify-content-between">
                                            <span id="currency-simbol">{{ $model->currency->simbol }}</span>
                                            <span class="text-end trading-sub-total">{{ formatNumber($model->subtotal) }}</span>
                                        </span>
                                    </td>
                                </tr>
                                @foreach ($model->invoice_trading_taxes as $key => $item)
                                    <tr>
                                        <td colspan="4" class="text-end">{{ $item->tax?->name }} - {{ $item->value * 100 }}%</td>
                                        <td class="text-end">
                                            <span class="d-flex justify-content-between">
                                                <span id="currency-simbol">{{ $model->currency->simbol }}</span>
                                                <span class="text-end" id="tax-trading-{{ $key }}">{{ formatNumber($item->amount) }}</span>
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="4" class="text-end">Total</td>
                                    <td class="text-end bg-success">
                                        <span class="d-flex justify-content-between">
                                            <span id="currency-simbol">{{ $model->currency->simbol }}</span>
                                            <span class="text-end" id="trading-total">{{ formatNumber($model->subtotal_after_tax) }}</span>
                                        </span>
                                    </td>
                                </tr>
                            </x-slot>

                        </x-table>

                        @if (count($model->inv_trading_add_on) > 0)
                            <h4 class="fw-bold">Additional Item</h4>
                            <x-table theadColor='dark' id="sale-order-additional-resume-table">
                                <x-slot name="table_head">
                                    <th>#</th>
                                    <th>{{ Str::headline('Item') }}</th>
                                    <th>{{ Str::headline('Harga') }}</th>
                                    <th>{{ Str::headline('sub total') }}</th>
                                    <th>{{ Str::headline('pajak') }}</th>
                                    <th>{{ Str::headline('value') }}</th>
                                    <th>{{ Str::headline('total') }}</th>
                                </x-slot>
                                <x-slot name="table_body">
                                    @foreach ($model->inv_trading_add_on as $additional_index => $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->item->nama }} - {{ $item->item->kode }}</td>
                                            <td>{{ $model->currency->simbol }} {{ formatNumber($item->price) }}</td>
                                            <td>{{ $model->currency->simbol }} {{ formatNumber($item->sub_total) }}</td>
                                            <td>
                                                @foreach ($item->inv_trading_add_on_tax as $add_tax)
                                                    <p>
                                                        <span>{{ $add_tax->tax?->name }} - {{ $add_tax->value * 100 }}%</span>
                                                    </p>
                                                @endforeach
                                            </td>
                                            <td>
                                                @foreach ($item->inv_trading_add_on_tax as $tax_index => $add_tax)
                                                    <span class="d-flex justify-content-between">
                                                        <span id="currency-simbol">{{ $model->currency->simbol }}</span>
                                                        <span class="text-end" id="tax-additional-{{ $tax_index }}-{{ $additional_index }}">{{ formatNumber($add_tax->total) }}</span>
                                                    </span>
                                                @endforeach
                                            </td>
                                            <td>
                                                <span class="d-flex justify-content-between">
                                                    <span id="currency-simbol">{{ $model->currency->simbol }}</span>
                                                    <span class="text-end">{{ formatNumber($item->total) }}</span>
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </x-slot>

                                <x-slot name="table_foot">
                                    <tr>
                                        <td class="text-end" colspan="6">Dpp</td>
                                        <td class="text-end">
                                            <span class="d-flex justify-content-between">
                                                <span id="currency-simbol">{{ $model->currency->simbol }}</span>
                                                <span class="text-end" id="additional-sub-total">{{ formatNumber($model->inv_trading_add_on()->sum('sub_total')) }}</span>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-end" colspan="6">Total pajak</td>
                                        <td class="text-end">
                                            <span class="d-flex justify-content-between">
                                                <span id="currency-simbol">{{ $model->currency->simbol }}</span>
                                                <span class="text-end" id="additional-tax-total">{{ formatNumber($model->additional_tax_total) }}</span>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-end" colspan="6">Total</td>
                                        <td class="text-end">
                                            <span class="d-flex justify-content-between">
                                                <span id="currency-simbol">{{ $model->currency->simbol }}</span>
                                                <span class="text-end" id="additional-total">{{ formatNumber($model->total_other_cost) }}</span>
                                            </span>
                                        </td>
                                    </tr>
                                </x-slot>

                            </x-table>
                        @endif

                        @if (count($model->inv_trading_add_on) > 0)
                            <div class="row justify-content-end">
                                <div class="col-md-4">
                                    <x-table theadColor="success" id="resume-all-table">
                                        <x-slot name="table_head">
                                            <th></th>
                                            <th></th>
                                        </x-slot>
                                        <x-slot name="table_body">
                                            <tr>
                                                <th>{{ Str::headline('total trading item') }}</th>
                                                <td>
                                                    <span class="d-flex justify-content-between">
                                                        <span>{{ $model->currency->simbol }} </span>
                                                        <span id="allTrading-total">{{ formatNumber($model->subtotal_after_tax) }}</span>
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>{{ Str::headline('total additional') }}</th>
                                                <td>
                                                    <span class="d-flex justify-content-between">
                                                        <span>{{ $model->currency->simbol }} </span>
                                                        <span id="allAdditional-total">{{ formatNumber($model->total_other_cost) }}</span>
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>{{ Str::headline('total') }}</th>
                                                <td>
                                                    <span class="d-flex justify-content-between">
                                                        <span>{{ $model->currency->simbol }} </span>
                                                        <span id="all-total">{{ formatNumber($model->total) }}
                                                        </span>
                                                    </span>
                                                </td>
                                            </tr>
                                        </x-slot>
                                    </x-table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div id="detail-item-additional-form">

                </div>

                <div id="resume-form">

                </div>

                <div class="float-end mt-0">
                    <x-button type="reset" color="secondary" class="w-auto" label="cancel" size="sm" icon="backward" fontawesome link="{{ url()->previous() }}" />
                    <x-button type="submit" color="primary" size="sm" icon="save" fontawesome class="w-auto" label="Save data" />
                </div>

            </x-slot>
        </x-card-data-table>
    </form>
@endsection

@section('js')
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script>
        sidebarMenuOpen('#trading');
        sidebarActive('#invoice-trading');
    </script>
    <script>
        checkClosingPeriod($('#invoice-date'))
        const calculateDueDate = () => {

            checkClosingPeriod($('#invoice-date'))

            let invoice_date = $('input[name="date"]');
            let due = $('input[name="due"]');
            let due_date = $('input[name="due_date"]');

            var date = new Date(convertLocalDate(invoice_date.val()));
            var due_days = parseInt(due.val());
            if (isNaN(due_days)) {
                due_days = 0;
            }
            date.setDate(date.getDate() + due_days);
            date.toISOString().split('T')[0];
            due_date.val(formatDate(date));
        };

        const formatDate = (date) => {
            var d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

            if (month.length < 2)
                month = '0' + month;
            if (day.length < 2)
                day = '0' + day;

            return [day, month, year].join('-');
        };

        $(document).ready(function() {
            let currency_symbol = '',
                CUSTOMER = [],
                CURRENCY = [],
                Sh_NUMBER = [],
                SO_TRADING = [],
                so_TRADING_TAXES = [],
                SO_TRADING_ADDITIONAL = [],
                DELIVERY_ORDER = [],
                DELIVERY_ORDER_SELECTED = [],
                unit_global = '';

            let QUANTITY_SALE_ORDER = 0,
                PRICE_SALE_ORDER = 0,
                PRICE_SALE_ORDER_FINAL = 0,
                PRICE_SALE_ORDER_FINAL_CUSTOM = 0;

            let LOST_TOLERANCE = 0,
                LOST_TOLERANCE_TYPE = '',
                CALCULATE_FROM = '',
                TOTAL_SENDED = 0,
                TOTAL_RECEIVED = 0,
                TOTAL_LOST = 0;

            let SUB_TOTAL = 0,
                SUB_TOTAL_AFTER_TAX = 0,
                TOTAL_ALL = 0,
                ADDITIONAL_SUB_TOTAL = 0,
                ADDITIONAL_TOTAL = 0,
                ADDITIONAL_TOTAL_AFTER_TAX = 0;

            let MAIN_TAX_TOTAL_LIST = [],
                ADDITIONAL_TAX_TOTAL_LIST = [],
                ADDITIONAL_CALCULATE_RESULT_LIST = [];

            let QUANTITY_SALE_ORDER_FINAL = 0;
            let QUANTITY_SALE_ORDER_FINAL_CUSTOM = 0;

            initSelect2Search('bank_internal_id', '{{ route('admin.select.customer.customer-banks', $model->customer_id) }}', {
                id: "bank_internal_id",
                text: "nama_bank,no_rekening"
            });

            const init = () => {
                getData();
            };

            const getData = () => {
                $.ajax({
                    type: "get",
                    url: "{{ route('admin.invoice-trading.edit', $model) }}",
                    success: function(response) {
                        CUSTOMER = response.customer;
                        CURRENCY = response.currency;
                        Sh_NUMBER = response.sh_number;
                        SO_TRADING = response.so_trading;
                        so_TRADING_TAXES = response.so_trading_taxes;
                        SO_TRADING_ADDITIONAL = response.sale_order_additionals;
                        DELIVERY_ORDER = response.delivery_order;
                        DELIVERY_ORDER_SELECTED = response.delivery_order_selected;

                        QUANTITY_SALE_ORDER = response.quantity_sale_order;
                        PRICE_SALE_ORDER = response.price_sale_order;
                        PRICE_SALE_ORDER_FINAL = response.price_sale_order;
                        PRICE_SALE_ORDER_FINAL_CUSTOM = response.price_sale_order;
                        QUANTITY_SALE_ORDER_FINAL = response.model.jumlah;
                        QUANTITY_SALE_ORDER_FINAL_CUSTOM = response.model.jumlah;

                        currency_symbol = CURRENCY.simbol;

                        DELIVERY_ORDER.map((delivery_order, delivery_order_index) => {
                            TOTAL_SENDED += parseFloat(delivery_order.load_quantity_realization);
                            TOTAL_RECEIVED += parseFloat(delivery_order.unload_quantity_realization);
                        })

                        unit_global = response.unit;

                        initSelect2SearchPaginationData(`invoice_down_payment_id`, `{{ route('admin.select.invoice-down-payment') }}`, {
                            id: 'id',
                            text: 'code'
                        }, 0, {
                            customer_id: CUSTOMER.id,
                            currency_id: CURRENCY.id,
                            selected_id: JSON.parse("{{ $down_payments->pluck('invoice_down_payment_id') }}")
                        }, 0, false);

                        // diplayData();
                        calculateDueDate();
                        calculateData();
                    },
                    error: function({
                        responseJSON
                    }) {
                        alert("ERROR get data invoice");
                    }
                });
            };

            const calculateData = () => {
                DELIVERY_ORDER_SELECTED = [];
                MAIN_TAX_TOTAL_LIST = [];
                ADDITIONAL_TAX_TOTAL_LIST = [];
                ADDITIONAL_CALCULATE_RESULT_LIST = [];

                LOST_TOLERANCE = thousandToFloat($('#lost-tolerance').val());
                LOST_TOLERANCE_TYPE = $('#lost-tolerance-type').val();
                CALCULATE_FROM = $('#calculate-from').val();
                TOTAL_SENDED = 0;
                TOTAL_RECEIVED = 0;
                TOTAL_LOST = 0;

                SUB_TOTAL = 0;
                SUB_TOTAL_AFTER_TAX = 0;
                TOTAL_ALL = 0;
                ADDITIONAL_SUB_TOTAL = 0;
                ADDITIONAL_TOTAL = 0;
                ADDITIONAL_TOTAL_AFTER_TAX = 0;


                const displayCalculation = () => {
                    let DISPLAY_LOST_TOLARANCE = '';

                    if (LOST_TOLERANCE_TYPE == 'percent') {
                        DISPLAY_LOST_TOLARANCE = `${LOST_TOLERANCE * 100} ${LOST_TOLERANCE_TYPE} `
                    }

                    if (LOST_TOLERANCE_TYPE != 'percent') {
                        DISPLAY_LOST_TOLARANCE = `${LOST_TOLERANCE} ${LOST_TOLERANCE_TYPE} `;
                    }

                    if (QUANTITY_SALE_ORDER_FINAL_CUSTOM != 0) {
                        if (LOST_TOLERANCE_TYPE == 'percent') {
                            DISPLAY_LOST_TOLARANCE += `| ${(QUANTITY_SALE_ORDER_FINAL_CUSTOM / TOTAL_SENDED) * 100} ${unit_global}`;
                        }

                        if (LOST_TOLERANCE_TYPE != 'percent') {
                            DISPLAY_LOST_TOLARANCE += `| ${LOST_TOLERANCE / QUANTITY_SALE_ORDER_FINAL_CUSTOM * 100} Percent`;
                        }
                    } else {
                        if (LOST_TOLERANCE_TYPE == 'percent') {
                            DISPLAY_LOST_TOLARANCE += `| ${(QUANTITY_SALE_ORDER_FINAL / TOTAL_SENDED) * 100} ${unit_global}`;
                        }

                        if (LOST_TOLERANCE_TYPE != 'percent') {
                            DISPLAY_LOST_TOLARANCE += `| ${LOST_TOLERANCE / QUANTITY_SALE_ORDER_FINAL * 100 } Percent`;
                        }
                    }

                    $('#total-load-quantity').html(`${formatRupiahWithDecimal(TOTAL_SENDED)} ${unit_global}`);
                    $('#total-unload-quantity').html(`${formatRupiahWithDecimal(TOTAL_RECEIVED)} ${unit_global}`);

                    $('#resume-quantity-sended').html(`${formatRupiahWithDecimal(TOTAL_SENDED)} ${unit_global}`);
                    $('#resume-quantity-received').html(`${formatRupiahWithDecimal(TOTAL_RECEIVED)} ${unit_global}`);
                    $('#resume-quantity-lost').html(`${formatRupiahWithDecimal(TOTAL_LOST)} ${unit_global}`);
                    $('#resume-calculate-from').html(CALCULATE_FROM == 'sales_order' ? 'Penjualan' : 'Pengiriman');
                    let resume_lost_tolerance = '';
                    resume_lost_tolerance += LOST_TOLERANCE_TYPE == 'percent' ? LOST_TOLERANCE * 100 : LOST_TOLERANCE;
                    resume_lost_tolerance += ' ' + (LOST_TOLERANCE_TYPE == 'percent' ? LOST_TOLERANCE_TYPE : unit_global);
                    resume_lost_tolerance += ' ' + (LOST_TOLERANCE_TYPE == 'percent' ? '(' + TOTAL_SENDED * LOST_TOLERANCE + ' ' + unit_global + ')' : '');
                    $('#resume-lost-tolerance').html(resume_lost_tolerance);

                    // * DISPLAY TOTAL IN TABLE
                    if (PRICE_SALE_ORDER_FINAL_CUSTOM != 0) {
                        $('#price-final').html(`${formatRupiahWithDecimal(PRICE_SALE_ORDER_FINAL_CUSTOM)} / ${unit_global}`);
                    } else {
                        $('#price-final').html(`${formatRupiahWithDecimal(PRICE_SALE_ORDER_FINAL)} / ${unit_global}`);
                    }

                    if (QUANTITY_SALE_ORDER_FINAL_CUSTOM != 0) {
                        $('#quantity-final').html(`${formatRupiahWithDecimal(QUANTITY_SALE_ORDER_FINAL_CUSTOM)} ${unit_global}`);
                    } else {
                        $('#quantity-final').html(`${formatRupiahWithDecimal(QUANTITY_SALE_ORDER_FINAL)} ${unit_global}`);
                    }

                    $('#price-for-invoice-before').val(`${formatRupiahWithDecimal(PRICE_SALE_ORDER_FINAL)}`);
                    $('#quantity-for-invoice-before').val(`${formatRupiahWithDecimal(QUANTITY_SALE_ORDER_FINAL)}`);

                    $('.trading-sub-total').html(`${formatRupiahWithDecimal(SUB_TOTAL)}`);
                    $('#trading-total').html(`${formatRupiahWithDecimal(SUB_TOTAL + SUB_TOTAL_AFTER_TAX)}`);

                    MAIN_TAX_TOTAL_LIST.map((tax_data, tax_index) => {
                        let {
                            tax,
                            total
                        } = tax_data;

                        $(`#tax-trading-${tax_index}`).html(`${formatRupiahWithDecimal(total)}`);
                    });

                    ADDITIONAL_TAX_TOTAL_LIST.map((additional_taxes, additional_index) => {
                        additional_taxes.map((tax_data, tax_index) => {
                            let {
                                tax,
                                total
                            } = tax_data;

                            $(`#tax-additional-${tax_index}-${additional_index}`).html(`${formatRupiahWithDecimal(total)}`);
                        });
                    });

                    ADDITIONAL_CALCULATE_RESULT_LIST.map((result, data_index) => {
                        let {
                            sub_total,
                            total_tax
                        } = result;

                        $(`#sub-total-additional-${data_index}`).html(`${formatRupiahWithDecimal(sub_total)}`);
                        $(`#total-additional-${data_index}`).html(`${formatRupiahWithDecimal(sub_total)}`);
                    });

                    $('#additional-sub-total').html(`${formatRupiahWithDecimal(ADDITIONAL_SUB_TOTAL)}`);
                    $('#additional-tax-total').html(`${formatRupiahWithDecimal(ADDITIONAL_TOTAL_AFTER_TAX)}`);
                    $('#additional-total').html(`${formatRupiahWithDecimal(ADDITIONAL_TOTAL)}`);

                    $('#allTrading-total').html(`${formatRupiahWithDecimal(SUB_TOTAL + SUB_TOTAL_AFTER_TAX)}`);
                    $('#allAdditional-total').html(`${formatRupiahWithDecimal(ADDITIONAL_TOTAL)}`);
                    $('#subtotal-text').html(`${formatRupiahWithDecimal(SUB_TOTAL)}`);
                    $('#all-total').html(`${formatRupiahWithDecimal(SUB_TOTAL + SUB_TOTAL_AFTER_TAX + ADDITIONAL_TOTAL)}`);
                };

                const calculateFromSaleOrder = () => {
                    QUANTITY_SALE_ORDER_FINAL = 0;

                    PRICE_SALE_ORDER = PRICE_SALE_ORDER_FINAL_CUSTOM != 0 ? PRICE_SALE_ORDER_FINAL_CUSTOM : PRICE_SALE_ORDER_FINAL;

                    // * CALCULATE TOTAL SENDED AND RECEIVED
                    DELIVERY_ORDER_SELECTED = DELIVERY_ORDER.map((delivery_order, delivery_order_index) => {
                        TOTAL_SENDED += parseFloat(delivery_order.load_quantity_realization);
                        TOTAL_RECEIVED += parseFloat(delivery_order.unload_quantity_realization);
                        return delivery_order;
                    });

                    // * CALCULATE TOTAL LOST
                    TOTAL_LOST = TOTAL_SENDED - TOTAL_RECEIVED;

                    // * IF LOST TOLERANCE TYPE IS PERCENTAGE
                    if (LOST_TOLERANCE_TYPE == 'percent') {
                        LOST_TOLERANCE /= 100;

                        let QTY_TOLERANCE = (TOTAL_SENDED * LOST_TOLERANCE);

                        if (TOTAL_LOST > QTY_TOLERANCE) {
                            QUANTITY_SALE_ORDER_FINAL = (TOTAL_RECEIVED + QTY_TOLERANCE);
                            SUB_TOTAL = (TOTAL_RECEIVED + QTY_TOLERANCE) * PRICE_SALE_ORDER;
                        } else {
                            QUANTITY_SALE_ORDER_FINAL = TOTAL_SENDED;
                            SUB_TOTAL = TOTAL_SENDED * PRICE_SALE_ORDER;
                        }
                    }

                    // * IF LOST TOLERANCE TYPE IS LITER
                    if (LOST_TOLERANCE_TYPE == 'liter') {
                        if (TOTAL_LOST > LOST_TOLERANCE) {
                            SUB_TOTAL = PRICE_SALE_ORDER * (TOTAL_RECEIVED + LOST_TOLERANCE);
                            QUANTITY_SALE_ORDER_FINAL = TOTAL_RECEIVED + LOST_TOLERANCE;
                        } else {
                            SUB_TOTAL = PRICE_SALE_ORDER * TOTAL_SENDED;
                            QUANTITY_SALE_ORDER_FINAL = TOTAL_SENDED;
                        }
                    }

                    SUB_TOTAL = PRICE_SALE_ORDER * QUANTITY_SALE_ORDER_FINAL;
                    if (QUANTITY_SALE_ORDER_FINAL_CUSTOM != 0) {
                        SUB_TOTAL = PRICE_SALE_ORDER * QUANTITY_SALE_ORDER_FINAL_CUSTOM;
                    }

                    SUB_TOTAL_BEFORE_LOSSES = SUB_TOTAL;

                    // * CALCULATE TAX
                    so_TRADING_TAXES.map((tax_data, tax_index) => {
                        let {
                            tax,
                            value
                        } = tax_data;

                        SUB_TOTAL_AFTER_TAX += (SUB_TOTAL * value);

                        MAIN_TAX_TOTAL_LIST.push({
                            tax_data: tax,
                            total: SUB_TOTAL * value
                        });
                    });

                    // * CALCULATE ADDITIONAL
                    SO_TRADING_ADDITIONAL.map((additional_data, additional_index) => {
                        let {
                            price,
                            sale_order_additional_taxes
                        } = additional_data;

                        let final_qty = QUANTITY_SALE_ORDER_FINAL_CUSTOM != 0 ? QUANTITY_SALE_ORDER_FINAL_CUSTOM : QUANTITY_SALE_ORDER_FINAL;

                        let single_additional_total = price * final_qty;
                        let single_total_tax = single_additional_total;

                        ADDITIONAL_TAX_TOTAL_LIST[additional_index] = [];
                        ADDITIONAL_TOTAL += single_additional_total;
                        ADDITIONAL_SUB_TOTAL += single_additional_total;

                        // * CALCULATE ADDITIONAL TAX
                        sale_order_additional_taxes.map((additional_tax_data, additional_tax_index) => {
                            let {
                                tax,
                                value
                            } = additional_tax_data;

                            ADDITIONAL_TOTAL_AFTER_TAX += (single_additional_total * value);
                            single_total_tax += (single_additional_total * value);

                            ADDITIONAL_TAX_TOTAL_LIST[additional_index][additional_tax_index] = {
                                tax_data: tax,
                                total: single_additional_total * value
                            };

                            ADDITIONAL_TOTAL += (single_additional_total * value);
                        });

                        ADDITIONAL_CALCULATE_RESULT_LIST[additional_index] = {
                            sub_total: single_additional_total,
                            total_tax: single_total_tax
                        };
                    });

                    // * CALCULATE TOTAL ALL
                    TOTAL_ALL = SUB_TOTAL + SUB_TOTAL_AFTER_TAX + ADDITIONAL_TOTAL + ADDITIONAL_TOTAL_AFTER_TAX;
                    displayCalculation();
                };

                const calculateFromDeliveryOrder = () => {
                    // * IF LOST TOLERANCE TYPE IS PERCENTAGE
                    if (LOST_TOLERANCE_TYPE == 'percent') {
                        LOST_TOLERANCE /= 100;
                    }

                    QUANTITY_SALE_ORDER_FINAL = 0;
                    DELIVERY_ORDER_SELECTED = DELIVERY_ORDER.map((delivery_order, delivery_order_index) => {
                        TOTAL_SENDED += parseFloat(delivery_order.load_quantity_realization);
                        TOTAL_RECEIVED += parseFloat(delivery_order.unload_quantity_realization);

                        let single_sended = parseFloat(delivery_order.load_quantity_realization);
                        let single_received = parseFloat(delivery_order.unload_quantity_realization);
                        let single_lost = single_sended - single_received;

                        let TOLERANCE = (single_sended * LOST_TOLERANCE);

                        // * IF LOST TOLERANCE TYPE IS PERCENTAGE
                        if (LOST_TOLERANCE_TYPE == 'percent') {
                            let single_lost_as_percentage = single_lost / single_sended;
                            if (single_lost_as_percentage > LOST_TOLERANCE) {
                                QUANTITY_SALE_ORDER_FINAL += single_received + TOLERANCE;
                            } else {
                                QUANTITY_SALE_ORDER_FINAL += single_sended;
                            }
                        }

                        // * IF LOST TOLERANCE TYPE IS LITER
                        if (LOST_TOLERANCE_TYPE == 'liter') {
                            if (single_lost > LOST_TOLERANCE) {
                                QUANTITY_SALE_ORDER_FINAL += single_received;
                            } else {
                                QUANTITY_SALE_ORDER_FINAL += single_sended;
                            }
                        }

                        TOTAL_LOST += single_lost;
                        return delivery_order;
                    });

                    PRICE_SALE_ORDER = PRICE_SALE_ORDER_FINAL_CUSTOM != 0 ? PRICE_SALE_ORDER_FINAL_CUSTOM : PRICE_SALE_ORDER_FINAL;

                    // * CALCULATE SUB TOTAL
                    SUB_TOTAL = PRICE_SALE_ORDER * QUANTITY_SALE_ORDER_FINAL;

                    if (QUANTITY_SALE_ORDER_FINAL_CUSTOM != 0) {
                        SUB_TOTAL = PRICE_SALE_ORDER * QUANTITY_SALE_ORDER_FINAL_CUSTOM;
                    }

                    SUB_TOTAL_BEFORE_LOSSES = SUB_TOTAL;

                    // * CALCULATE TAX
                    so_TRADING_TAXES.map((tax_data, tax_index) => {
                        let {
                            tax,
                            value
                        } = tax_data;

                        SUB_TOTAL_AFTER_TAX += (SUB_TOTAL * value);

                        MAIN_TAX_TOTAL_LIST.push({
                            tax_data: tax,
                            total: SUB_TOTAL * value
                        });
                    });

                    // * CALCULATE ADDITIONAL
                    $('#sale-order-additional-resume-table tbody').html('');

                    SO_TRADING_ADDITIONAL.map((additional_data, additional_index) => {
                        let final_qty = QUANTITY_SALE_ORDER_FINAL_CUSTOM != 0 ? QUANTITY_SALE_ORDER_FINAL_CUSTOM : QUANTITY_SALE_ORDER_FINAL;

                        let {
                            price,
                            sale_order_additional_taxes
                        } = additional_data;

                        let single_additional_total = price * final_qty;
                        let single_total_tax = single_additional_total;

                        ADDITIONAL_TAX_TOTAL_LIST[additional_index] = [];
                        ADDITIONAL_TOTAL += single_additional_total;
                        ADDITIONAL_SUB_TOTAL += single_additional_total;

                        // * CALCULATE ADDITIONAL TAX
                        let html_tax_name = '',
                            html_tax_value = '';
                        sale_order_additional_taxes.map((additional_tax_data, additional_tax_index) => {
                            let {
                                tax,
                                value
                            } = additional_tax_data;

                            ADDITIONAL_TOTAL_AFTER_TAX += (single_additional_total * value);
                            single_total_tax += (single_additional_total * value);

                            ADDITIONAL_TAX_TOTAL_LIST[additional_index][additional_tax_index] = {
                                tax_data: tax,
                                total: single_additional_total * value
                            };

                            ADDITIONAL_TOTAL += (single_additional_total * value);

                            html_tax_value += `
                                    <span class="d-flex justify-content-between">
                                        <span id="currency-simbol">${currency_symbol}</span>
                                        <span class="text-end" id="tax-additional-${additional_tax_index}-${additional_index}">${formatRupiahWithDecimal(single_additional_total * value)}</span>
                                    </span>`;

                            html_tax_name += `
                                        <p>
                                            <span>${tax.name} - ${tax.value * 100}%</span>
                                        </p>
                                    `;
                        });

                        ADDITIONAL_CALCULATE_RESULT_LIST[additional_index] = {
                            sub_total: single_additional_total,
                            total_tax: single_total_tax
                        };

                        $('#sale-order-additional-resume-table tbody').append(`
                                <tr>
                                    <td>${additional_index + 1}</td>
                                    <td>${additional_data.item.nama} - ${additional_data.item.kode}</td>
                                    <td>${formatRupiahWithDecimal(additional_data.price)} / ${unit_global}</td>
                                    <td>
                                        <span class="d-flex justify-content-between">
                                            <span id="currency-simbol">${currency_symbol}</span>
                                            <span class="text-end" id="sub-total-additional-${additional_index}">${formatRupiahWithDecimal(single_additional_total)}</span>
                                        </span>
                                    </td>
                                    <td>${html_tax_name}</td>
                                    <td>${html_tax_value}</td>
                                    <td>
                                        <span class="d-flex justify-content-between">
                                            <span id="currency-simbol">${currency_symbol}</span>
                                            <span class="text-end" id="total-additional-${additional_index}">${formatRupiahWithDecimal(single_total_tax + single_additional_total)}</span>
                                        </span>
                                    </td>
                                </tr>
                            `);
                    });

                    TOTAL_ALL = SUB_TOTAL + SUB_TOTAL_AFTER_TAX + ADDITIONAL_TOTAL + ADDITIONAL_TOTAL_AFTER_TAX;
                    displayCalculation();
                };

                // * CHECK CALCULATE FROM
                if (CALCULATE_FROM == 'sales_order') {
                    calculateFromSaleOrder();
                } else if (CALCULATE_FROM == 'delivery_order') {
                    calculateFromDeliveryOrder();
                }
            };

            $('#quantity-for-invoice-after').change(function(e) {
                e.preventDefault();
                QUANTITY_SALE_ORDER_FINAL_CUSTOM = thousandToFloat(this.value ?? '0')

                calculateData();
            });

            init();

            $('#calculate-btn').click(function(e) {
                e.preventDefault();

                $('#quantity-for-invoice-after').val(0);
                QUANTITY_SALE_ORDER_FINAL_CUSTOM = 0;

                setTimeout(() => {
                    calculateData();
                }, 1000);

            });
        });
    </script>
@endsection
