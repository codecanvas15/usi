@extends('layouts.admin.layout.index')

@php
    $main = 'invoice-trading';
@endphp

@section('title', Str::headline("generate $main") . ' - ')

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
                        {{ Str::headline('generate ' . $main) }}
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

    <x-card-data-table id="loading-card">
        <x-slot name="header_content">
        </x-slot>

        <x-slot name="table_content">
            <h2 class="text-center">Loading...</h2>
        </x-slot>
    </x-card-data-table>

    <form action="{{ route("admin.$main.generate.store", $model) }}" method="post" id="main-form" enctype="multipart/form-data">
        @csrf

        <x-card-data-table title="generate {{ $main }}" id="parent-form">
            <x-slot name="header_content">

            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')

                <div id="customer-parent-form">
                </div>

            </x-slot>
        </x-card-data-table>

        <x-card-data-table title="list delivery order" id="delivery-order-form">
            <x-slot name="header_content">

            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')

                <div id="delivery-order-list-form">

                </div>

            </x-slot>
        </x-card-data-table>

        <x-card-data-table title="invoice trading" id="detail-item-card">
            <x-slot name="header_content">

            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')

                <div id="main-form-invoice-card">

                </div>

                <div id="detail-invoice-resume-form">

                </div>

                <div id="detail-item-form">

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
        $(document).ready(function() {

            $('#main-form').hide();

            // init variables ##########################################################################################
            let currency_symbol = '',
                CUSTOMER = [],
                CURRENCY = [],
                Sh_NUMBER = [],
                SO_TRADING = [],
                so_TRADING_TAXES = [],
                SO_TRADING_ADDITIONAL = [],
                DELIVERY_ORDER = [],
                DELIVERY_ORDER_SELECTED = [],
                unit = '';

            let QUANTITY_SALE_ORDER = 0,
                PRICE_SALE_ORDER = 0;

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

            const init = () => {
                getData();
            };

            const getData = () => {
                $.ajax({
                    type: "get",
                    url: "{{ route('admin.sales-order.get-detail-for-invoice', $model) }}",
                    success: function({
                        data
                    }) {
                        let {
                            model,
                            delivery_orders
                        } = data;

                        CUSTOMER = model.customer;
                        CURRENCY = model.currency;
                        Sh_NUMBER = model.sh_number;
                        SO_TRADING = model;
                        so_TRADING_TAXES = model.sale_order_taxes;
                        SO_TRADING_ADDITIONAL = model.sale_order_additionals;
                        DELIVERY_ORDER = delivery_orders;
                        DELIVERY_ORDER_SELECTED = delivery_orders;

                        QUANTITY_SALE_ORDER = model.so_trading_detail.jumlah;
                        PRICE_SALE_ORDER = model.so_trading_detail.harga;

                        unit = model.so_trading_detail.item.unit.name ?? '';

                        currency_symbol = CURRENCY.simbol;

                        diplayData();
                        calculateDueDate();
                    },
                    error: function({
                        responseJSON
                    }) {
                        alert("ERROR get data invoice");
                    }
                });
            };

            const diplayData = () => {

                const initalizeDisplayData = () => {

                    $('#loading-card').hide(500);
                    $('#main-form').fadeIn(500);

                    displayParentData();
                    displayDeliveryOrder();
                    displayItem();

                    calculateData();
                };

                /**
                 * display parent data
                 */
                const displayParentData = () => {
                    $('#customer-parent-form').html('');

                    let sh_number_detail_html = Sh_NUMBER.sh_number_details.map((sh_number_detail, sh_number_index) => {
                        return `
                            <tr>
                                <th>${sh_number_detail.type}</th>
                                <td>${sh_number_detail.alamat}</td>
                            </tr>
                        `;
                    });

                    let nomor_po_external = SO_TRADING.nomor_po_external;
                    if (!SO_TRADING.nomor_po_external) {
                        nomor_po_external = `<x-input name="nomor_po_external" id="nomor_po_external" label="nomor po external" required />`;
                    }
                    $('#customer-parent-form').append(`
                        <x-table theadColor="danger">
                            <x-slot name="table_head">
                                <th class="col-md-4"></th>
                                <th class="col-md"></th>
                            </x-slot>
                            <x-slot name="table_body">
                                <tr>
                                    <th>Nomor SO</th>
                                    <td>${SO_TRADING.nomor_so}</td>
                                </tr>
                                <tr>
                                    <th>Nomor PO External</th>
                                    <td>
                                        ${nomor_po_external}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Nama customer</th>
                                    <td>${CUSTOMER.nama}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal</th>
                                    <td>${localDate(SO_TRADING.tanggal)}</td>
                                </tr>
                                <tr>
                                    <th>Sh No.</th>
                                    <td>${Sh_NUMBER.kode}</td>
                                </tr>
                                ${sh_number_detail_html}
                                <tr>
                                    <th>Cur.</th>
                                    <td>${CURRENCY.nama}</td>
                                </tr>
                                <tr>
                                    <th>kurs</th>
                                    <td>${formatRupiahWithDecimal(SO_TRADING.exchange_rate)}</td>
                                </tr>
                            </x-slot>
                        </x-table>
                    `);

                };

                const displayDeliveryOrder = () => {
                    $('#delivery-order-list-form').html('');

                    $('#delivery-order-list-form').append(`
                        <div class="row">
                            <div class="col-md-4">
                                <x-input-checkbox label="Check All" checked name="select_all" class="check-delivery" id="check-delivery-all" hideAsterix />
                            </div>
                        </div>
                        <x-table theadColor="danger" id="delivery-order-table">
                            <x-slot name="table_head">
                                <th></th>
                                <th>#</th>
                                <th>{{ Str::headline('kode') }}</th>
                                <th>{{ Str::headline('target pengiriman') }}</th>
                                <th>{{ Str::headline('tanggal muat') }}</th>
                                <th>{{ Str::headline('tanggal bongkar') }}</th>
                                <th>{{ Str::headline('Realisasi kuantitas kirim') }}</th>
                                <th>{{ Str::headline('Realisasi kuantitas diterima') }}</th>
                                <th>{{ Str::headline('losses') }}</th>
                            </x-slot>
                            <x-slot name="table_body">
                            </x-slot>
                            <x-slot name="table_foot">
                            </x-slot>
                        </x-table>
                    `);

                    TOTAL_SENDED = 0;
                    TOTAL_RECEIVED = 0;
                    TOTAL_LOSSES = 0;
                    DELIVERY_ORDER.map((delivery_order, delivery_order_index) => {
                        let {
                            code,
                            date,
                            target_delivery,
                            load_date,
                            unload_date,
                            load_quantity_realization,
                            unload_quantity_realization
                        } = delivery_order;

                        TOTAL_SENDED += parseFloat(load_quantity_realization);
                        TOTAL_RECEIVED += parseFloat(unload_quantity_realization);
                        TOTAL_LOSSES += parseFloat(load_quantity_realization - unload_quantity_realization);


                        $('#delivery-order-table tbody').append(`
                            <tr>
                                <td>
                                    <x-input-checkbox label="-" checked class="check-delivery check-delivery-to-invoice" id="delivery-order-checkbox-${delivery_order_index}" hideAsterix/>
                                    <input type="hidden" name="delivery_order_transport_id[]" value="${delivery_order.id}">
                                </td>
                                <td>${delivery_order_index + 1 }</td>
                                <td>${code}</td>
                                <td>${target_delivery}</td>
                                <td>${localDate(load_date)}</td>
                                <td>${localDate(unload_date)}</td>
                                <td>${formatRupiahWithDecimal(load_quantity_realization)} ${unit}</td>
                                <td>${formatRupiahWithDecimal(unload_quantity_realization)} ${unit}</td>
                                <td>${formatRupiahWithDecimal(load_quantity_realization - unload_quantity_realization)} ${unit}</td>
                            </tr>
                        `)
                    });

                    $('#delivery-order-table tfoot').append(`
                        <tr>
                            <td colspan="6" class="text-end">Total</td>
                            <td class="bg-success" id="total-load-quantity">${formatRupiahWithDecimal(TOTAL_SENDED)} ${unit}</td>
                            <td class="bg-success" id="total-unload-quantity">${formatRupiahWithDecimal(TOTAL_RECEIVED)} ${unit}</td>
                            <td class="bg-danger" id="total-loses-quantity">${formatRupiahWithDecimal(TOTAL_LOSSES)} ${unit}</td>
                        </tr>
                    `);

                    $('#check-delivery-all').on('change', function() {
                        if ($(this).is(':checked')) {
                            $('.check-delivery').prop('checked', true);
                        } else {
                            $('.check-delivery').prop('checked', false);
                        }
                    });
                };

                const displayItem = () => {

                    $('#main-form-invoice-card').html('');
                    $('#detail-item-form').html('');
                    $('#detail-item-additional-form').html('');
                    $('#detail-invoice-resume-form').html('');

                    const initializeDisplayItem = () => {
                        displayInvoicementResume();
                        displayFormInvoiceCard();
                        displayMainItem();

                        if (SO_TRADING_ADDITIONAL.length > 0) {
                            displayAdditionalItem();
                        }
                    };

                    const displayInvoicementResume = () => {

                        $('#detail-invoice-resume-form').append(`
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
                                            <td id="resume-quantity-sended"></td>
                                        </tr>
                                        <tr>
                                            <th>Kuantitas Diterima</th>
                                            <td id="resume-quantity-received"></td>
                                        </tr>
                                        <tr>
                                            <th>Kuantitas Hilang</th>
                                            <td id="resume-quantity-lost"></td>
                                        </tr>
                                        <tr>
                                            <th>Calculate From</th>
                                            <td id="resume-calculate-from"></td>
                                        </tr>
                                        <tr>
                                            <th>Lost Tolerance</th>
                                            <td id="resume-lost-tolerance"></td>
                                        </tr>
                                    </x-slot>
                                </x-table>
                            </div>
                        `);
                    };

                    const displayFormInvoiceCard = () => {
                        let bank_internal_html = CUSTOMER.customer_banks.map((bank_internal, bank_internal_index) => {
                            return `
                                <option value="${bank_internal.bank_internal_id}" ${bank_internal_index == 0 ? 'selected' : ''}>${bank_internal.bank_internal.nama_bank} - ${bank_internal.bank_internal.no_rekening}</option>
                            `;
                        });

                        let {
                            lost_tolerance,
                            lost_tolerance_type
                        } = CUSTOMER;

                        LOST_TOLERANCE = lost_tolerance;
                        LOST_TOLERANCE_TYPE = lost_tolerance_type;
                        if (lost_tolerance_type == 'percent') {
                            LOST_TOLERANCE = lost_tolerance * 100;
                        }


                        $('#main-form-invoice-card').append(`
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input class="datepicker-input" name="date" label="date" id="invoice-date" required value="{{ date('d-m-Y') }}" onchange="calculateDueDate()" />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input type="text" name="top" label="TOP" id="top" required readonly value="{{ $model->customer->term_of_payment }}" />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input type="number" name="due" label="jatuh tempo (hari)" id="due" required value="{{ $model->customer->top_days ?? 0 }}" onkeyup="calculateDueDate()" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input class="datepicker-input" name="due_date" label="due_date" id="due_date" required readonly/>
                                    </div>
                                </div>
                                ${SO_TRADING_ADDITIONAL.length > 0 ? `<div class="col-md-3 d-flex align-self-start">
                                                                                                                                                                                                <x-input-checkbox label="Pisah Invoice" name="is_separate_invoice" id="is_separate_invoice" value="1" />
                                                                                                                                                                                            </div>` : ``}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-select name="bank_internal_id[]" label="bank_internal" id="bank_internal_id" required multiple>
                                            ${bank_internal_html.length > 0 ? bank_internal_html.join(' ') : '<option value="">No Bank Internal</option>'}
                                        </x-select>
                                        <span class="text-end text-danger">
                                            Jika bank internal kosong masukkan dari data master customer.
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input type="file" name="attachment" label="lampiran" id="attachment" accept=".jpg, .jpeg, .png, .pdf, .doc, .docx, .xls, .xlsx" helpers="jpg/jpeg/png/pdf/doc/docx/xls/xlsx | max. 5 MB" />
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-20">
                                <div class="col-md-4">
                                     <x-select name="invoice_down_payment_id[]" label="Down Payment" id="invoice_down_payment_id" multiple>

                                    </x-select>
                                </div>
                                <div class="col-md-12"></div>
                                <div class="col-md-4">
                                    <x-select name="calculate_from" label="calculate from" id="calculate-from" required>
                                        <option value=""></option>
                                        <option value="sales_order" selected>{{ Str::headline('sales_order') }}</option>
                                        <option value="delivery_order">{{ Str::headline('delivery_order') }}</option>
                                    </x-select>
                                </div>
                                <div class="col-md-4">
                                    <x-select name="lost_tolerance_type" id="lost-tolerance-type" required>
                                        <option value=""></option>
                                        <option value="percent" ${LOST_TOLERANCE_TYPE == 'percent' ? 'selected' : '' }>{{ Str::headline('percent') }}</option>
                                        <option value="liter" ${LOST_TOLERANCE_TYPE == 'liter' ? 'selected' : '' }>${unit}</option>
                                    </x-select>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input type="text" name="lost_tolerance" value="${formatRupiahWithDecimal(LOST_TOLERANCE ?? 0)}" id="lost-tolerance" required />
                                    </div>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <div class="form-group">
                                        <x-button color="info" size="sm" icon="rotate" fontawesome label="calculate" id="calculate-btn" />
                                    </div>
                                </div>
                            </div>
                        `);

                        $('.datepicker-input:not([readonly])').datepicker({
                            format: 'dd-mm-yyyy',
                        });

                        $('.select2').select2();

                        $('#calculate-btn').click(function(e) {
                            e.preventDefault();
                            calculateData();
                        });

                        $('#due').bind('keyup mouseup', function(e) {
                            calculateDueDate()
                        })

                        $('#invoice-date').bind('keyup mouseup', function(e) {
                            calculateDueDate()
                        })

                        initSelect2SearchPaginationData(`invoice_down_payment_id`, `{{ route('admin.select.invoice-down-payment') }}`, {
                            id: 'id',
                            text: 'code'
                        }, 0, {
                            customer_id: CUSTOMER.id,
                            currency_id: CURRENCY.id,
                        }, 0, false);
                    };

                    const displayMainItem = () => {
                        let {
                            item,
                            harga,
                            jumlah
                        } = SO_TRADING.so_trading_detail;

                        let sub_total = harga * jumlah
                        let total_tax = 0;

                        let html_tax = so_TRADING_TAXES.map((tax_data, tax_index) => {
                            let {
                                tax,
                                value
                            } = tax_data;

                            let total_single_tax = sub_total * value;
                            total_tax += total_single_tax;

                            return `
                                <tr>
                                    <td colspan="4" class="text-end">${tax.name} - ${value * 100}%</td>
                                    <td>
                                        <span class="d-flex justify-content-between">
                                            <span id="currency-simbol">${currency_symbol}</span>
                                            <span class="text-end" id="tax-trading-${tax_index}">${formatRupiahWithDecimal(total_single_tax)}</span>
                                        </span>
                                    </td>
                                </tr>
                            `;
                        });

                        $('#detail-item-form').append(`

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
                                            <td>${item.nama} - ${item.kode}</td>
                                            <td>${formatRupiahWithDecimal(harga)} / ${unit} </td>
                                            <td>${formatRupiahWithDecimal(jumlah)} ${unit}</td>
                                            <td>
                                                <span id="quantity-final">${formatRupiahWithDecimal(QUANTITY_SALE_ORDER_FINAL)} ${unit}</span>

                                                @can('change quantity-invoice-trading')
                                                    <x-button type="button" color="primary" dataToggle="modal" dataTarget="#changeQuantityInvoice" icon="edit" fontawesome size="sm" />
                                                    <x-modal title="Ubah Kuantitas Untuk Invoice" id="changeQuantityInvoice">
                                                        <x-slot name="modal_body">
                                                            <div class="form-group">
                                                                <x-input type="quantity_for_invoice_default" class="commas-form" name="quantity_for_invoice_before" id="quantity-for-invoice-before" label="kuantitas awal"  readonly />
                                                            </div>
                                                            <div class="form-group">
                                                                <x-input type="quantity_for_invoice_canged" class="commas-form-five" name="quantity_for_invoice_after" id="quantity-for-invoice-after" label="kuantitas untuk invoice" />
                                                            </div>
                                                        </x-slot>
                                                    </x-modal>
                                                @endcan
                                            </td>
                                            <td id="trading-subtotal">${formatRupiahWithDecimal(sub_total)}</td>
                                        </tr>
                                    </x-slot>

                                    <x-slot name="table_foot">
                                        ${html_tax}
                                        <tr>
                                            <td colspan="4" class="text-end">Sub total</td>
                                            <td class="text-end bg-success">
                                                <span class="d-flex justify-content-between">
                                                    <span id="currency-simbol">${currency_symbol}</span>
                                                    <span class="text-end" id="trading-total">${formatRupiahWithDecimal(sub_total + total_tax)}</span>
                                                </span>
                                            </td>
                                        </tr>
                                    </x-slot>

                                </x-table>
                            </div>
                        `);


                        $('#quantity-for-invoice-after').change(function(e) {
                            e.preventDefault();
                            QUANTITY_SALE_ORDER_FINAL_CUSTOM = thousandToFloat(this.value ?? '0')
                            calculateData();
                        });

                        initCommasForm();
                        initCommasFormFiveDigits();
                    };

                    const displayAdditionalItem = () => {
                        $('#detail-item-additional-form').append(`
                            <div class="mt-20">
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

                                    </x-slot>

                                    <x-slot name="table_foot">
                                        <tr>
                                            <td class="text-end" colspan="6">Dpp</td>
                                            <td class="text-end">
                                                <span class="d-flex justify-content-between">
                                                    <span id="currency-simbol">${currency_symbol}</span>
                                                    <span class="text-end" id="additional-sub-total"></span>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-end" colspan="6">Total pajak</td>
                                            <td class="text-end">
                                                <span class="d-flex justify-content-between">
                                                    <span id="currency-simbol">${currency_symbol}</span>
                                                    <span class="text-end" id="additional-tax-total"></span>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-end" colspan="6">Total</td>
                                            <td class="bg-success text-white text-end">
                                                <span class="d-flex justify-content-between">
                                                    <span id="currency-simbol">${currency_symbol}</span>
                                                    <span class="text-end" id="additional-total"></span>
                                                </span>
                                            </td>
                                        </tr>
                                    </x-slot>

                                </x-table>
                            </div>
                        `);

                        $('#resume-form').html(`
                            <div class="mt-20">
                                <h4 class="fw-bold">Additional Item</h4>

                                <div classs="row justify-content-end">
                                    <div classs="col-md-4">
                                        <x-table theadColor='dark' id="resume-all-table">
                                            <x-slot name="table_head">
                                                <th class="col-8"></th>
                                                <th></th>
                                            </x-slot>
                                            <x-slot name="table_body">
                                                <tr>
                                                    <td class="text-end">Total Trading</td>
                                                    <td class="text-end">
                                                        <span class="d-flex justify-content-between">
                                                            <span id="currency-simbol">${currency_symbol}</span>
                                                            <span class="text-end" id="allTrading-total"></span>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-end">Total additional</td>
                                                    <td class="text-end">
                                                        <span class="d-flex justify-content-between">
                                                            <span id="currency-simbol">${currency_symbol}</span>
                                                            <span class="text-end" id="allAdditional-total"></span>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-end">Total</td>
                                                    <td class="text-end">
                                                        <span class="d-flex justify-content-between">
                                                            <span id="currency-simbol">${currency_symbol}</span>
                                                            <span class="text-end" id="all-total"></span>
                                                        </span>
                                                    </td>
                                                </tr>
                                            </x-slot>
                                        </x-table>
                                    </div>
                                </div>
                            </div>
                        `);

                        let total_additional = 0;
                        SO_TRADING_ADDITIONAL.map((additional, additional_index) => {
                            let {
                                ítem,
                                price,
                                sale_order_additional_taxes
                            } = additional;

                            let html_tax_name = '',
                                html_tax_value = '';

                            let sub_total = price * PRICE_SALE_ORDER;
                            let total_tax = 0;

                            sale_order_additional_taxes.map((tax_data, tax_index) => {
                                let {
                                    tax,
                                    value
                                } = tax_data;

                                let total_single_tax = sub_total * value;

                                html_tax_value = `
                                    <span class="d-flex justify-content-between">
                                        <span id="currency-simbol">${currency_symbol}</span>
                                        <span class="text-end" id="tax-additional-${tax_index}-${additional_index}">${formatRupiahWithDecimal(total_single_tax)}</span>
                                    </span>
                                `;

                                html_tax_name += `
                                    <p>
                                        <span>${tax.name} - ${tax.value * 100}%</span>
                                    </p>
                                `;

                                total_tax += total_single_tax;
                            });

                            $('#sale-order-additional-resume-table tbody').append(`
                                <tr>
                                    <td>${additional_index + 1}</td>
                                    <td>${additional.item.nama} - ${additional.item.kode}</td>
                                    <td>${formatRupiahWithDecimal(price)} / ${unit}</td>
                                    <td>
                                        <span class="d-flex justify-content-between">
                                            <span id="currency-simbol">${currency_symbol}</span>
                                            <span class="text-end" id="sub-total-additional-${additional_index}">${formatRupiahWithDecimal(sub_total)}</span>
                                        </span>
                                    </td>
                                    <td>${html_tax_name}</td>
                                    <td>${html_tax_value}</td>
                                    <td>
                                        <span class="d-flex justify-content-between">
                                            <span id="currency-simbol">${currency_symbol}</span>
                                            <span class="text-end" id="total-additional-${additional_index}">${formatRupiahWithDecimal(total_tax + sub_total)}</span>
                                        </span>
                                    </td>
                                </tr>
                            `);

                            total_additional += sub_total + total_tax;
                        });

                    };

                    initializeDisplayItem();

                };

                initalizeDisplayData();
            };

            const calculateData = () => {

                DELIVERY_ORDER_SELECTED = [];
                MAIN_TAX_TOTAL_LIST = [];
                ADDITIONAL_TAX_TOTAL_LIST = [];
                ADDITIONAL_CALCULATE_RESULT_LIST = [];

                LOST_TOLERANCE = 0;
                LOST_TOLERANCE_TYPE = '';
                CALCULATE_FROM = '';
                TOTAL_SENDED = 0;
                TOTAL_RECEIVED = 0;
                TOTAL_LOST = 0;

                SUB_TOTAL = 0;
                SUB_TOTAL_AFTER_TAX = 0;
                TOTAL_ALL = 0;
                ADDITIONAL_SUB_TOTAL = 0;
                ADDITIONAL_TOTAL = 0;
                ADDITIONAL_TOTAL_AFTER_TAX = 0;

                // GET DATA LOST TOLERANCE, LOST TOLERANCE TYPE AND CALCULATE FROM
                LOST_TOLERANCE = thousandToFloat($('#lost-tolerance').val());
                LOST_TOLERANCE_TYPE = $('#lost-tolerance-type').val();
                CALCULATE_FROM = $('#calculate-from').val();

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
                            DISPLAY_LOST_TOLARANCE += `| ${(QUANTITY_SALE_ORDER_FINAL_CUSTOM / TOTAL_SENDED) * 100} ${unit}`;
                        }

                        if (LOST_TOLERANCE_TYPE != 'percent') {
                            DISPLAY_LOST_TOLARANCE += `| ${LOST_TOLERANCE / QUANTITY_SALE_ORDER_FINAL_CUSTOM * 100} Percent`;
                        }
                    } else {
                        if (LOST_TOLERANCE_TYPE == 'percent') {
                            DISPLAY_LOST_TOLARANCE += `| ${(QUANTITY_SALE_ORDER_FINAL / TOTAL_SENDED) * 100} ${unit}`;
                        }

                        if (LOST_TOLERANCE_TYPE != 'percent') {
                            DISPLAY_LOST_TOLARANCE += `| ${LOST_TOLERANCE / QUANTITY_SALE_ORDER_FINAL * 100 } Percent`;
                        }
                    }

                    $('#total-load-quantity').html(`${formatRupiahWithDecimal(TOTAL_SENDED)} ${unit}`);
                    $('#total-unload-quantity').html(`${formatRupiahWithDecimal(TOTAL_RECEIVED)} ${unit}`);

                    $('#resume-quantity-sended').html(`${formatRupiahWithDecimal(TOTAL_SENDED)} ${unit}`);
                    $('#resume-quantity-received').html(`${formatRupiahWithDecimal(TOTAL_RECEIVED)} ${unit}`);
                    $('#resume-quantity-lost').html(`${formatRupiahWithDecimal(TOTAL_LOST)} ${unit}`);
                    $('#resume-calculate-from').html(CALCULATE_FROM == 'sales_order' ? 'Penjualan' : 'Pengiriman');

                    // $('#resume-lost-tolerance').html(DISPLAY_LOST_TOLARANCE);
                    let resume_lost_tolerance = '';
                    resume_lost_tolerance += LOST_TOLERANCE_TYPE == 'percent' ? LOST_TOLERANCE * 100 : LOST_TOLERANCE;
                    resume_lost_tolerance += ' ' + (LOST_TOLERANCE_TYPE == 'liter' ? unit : LOST_TOLERANCE_TYPE);
                    resume_lost_tolerance += ' ' + (LOST_TOLERANCE_TYPE == 'percent' ? '(' + TOTAL_SENDED * LOST_TOLERANCE + ' ' + unit + ' )' : '');

                    $('#resume-lost-tolerance').html(resume_lost_tolerance);

                    // * DISPLAY TOTAL IN TABLE
                    if (QUANTITY_SALE_ORDER_FINAL_CUSTOM != 0) {
                        $('#quantity-final').html(`${formatRupiahWithDecimal(QUANTITY_SALE_ORDER_FINAL_CUSTOM)} ${unit}`);
                    } else {
                        $('#quantity-final').html(`${formatRupiahWithDecimal(QUANTITY_SALE_ORDER_FINAL)} ${unit}`);
                    }

                    $('#quantity-for-invoice-before').val(`${formatRupiahWithDecimal(QUANTITY_SALE_ORDER_FINAL)}`);

                    $('#trading-subtotal').html(`${formatRupiahWithDecimal(SUB_TOTAL)}`);
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
                    $('#all-total').html(`${formatRupiahWithDecimal(SUB_TOTAL + SUB_TOTAL_AFTER_TAX + ADDITIONAL_TOTAL)}`);

                };

                const calculateFromSaleOrder = () => {

                    QUANTITY_SALE_ORDER_FINAL = 0;

                    // * CALCULATE TOTAL SENDED AND RECEIVED
                    DELIVERY_ORDER_SELECTED = DELIVERY_ORDER.map((delivery_order, delivery_order_index) => {
                        if ($(`#delivery-order-checkbox-${delivery_order_index}`).prop('checked')) {
                            TOTAL_SENDED += parseFloat(delivery_order.load_quantity_realization);
                            TOTAL_RECEIVED += parseFloat(delivery_order.unload_quantity_realization);

                            return delivery_order;
                        }
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

                    if (QUANTITY_SALE_ORDER_FINAL_CUSTOM != 0) {
                        SUB_TOTAL = PRICE_SALE_ORDER * QUANTITY_SALE_ORDER_FINAL_CUSTOM;
                    }

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
                        if ($(`#delivery-order-checkbox-${delivery_order_index}`).prop('checked')) {

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
                        }
                    });

                    // * CALCULATE SUB TOTAL
                    SUB_TOTAL = PRICE_SALE_ORDER * QUANTITY_SALE_ORDER_FINAL;

                    if (QUANTITY_SALE_ORDER_FINAL_CUSTOM != 0) {
                        SUB_TOTAL = PRICE_SALE_ORDER * QUANTITY_SALE_ORDER_FINAL_CUSTOM;
                    }

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

                // * CHECK CALCULATE FROM
                if (CALCULATE_FROM == 'sales_order') {
                    calculateFromSaleOrder();
                } else if (CALCULATE_FROM == 'delivery_order') {
                    calculateFromDeliveryOrder();
                }
            };

            init();
        });

        checkClosingPeriod($('#invoice-date'));
        const calculateDueDate = () => {

            checkClosingPeriod($('#invoice-date'));

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
        }

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
        }

        document.getElementById("main-form").addEventListener("submit", function(event) {
            $('input[name="delivery_order_transport[]"]').remove();
            // get checkbox with class "check-delivery-to-invoice"
            const checkboxes = document.querySelectorAll('.check-delivery-to-invoice');

            console.log(checkboxes);

            // Loop through all checkboxes
            checkboxes.forEach(function(checkbox) {
                // Create a hidden input field with the same name and a fixed value ("on" or "off")
                const hiddenInput = document.createElement("input");
                hiddenInput.type = "hidden";
                hiddenInput.name = 'delivery_order_transport[]';
                if (checkbox.checked) {
                    hiddenInput.value = "on"; // You can set a fixed value here
                } else {
                    hiddenInput.value = "off";

                }

                // Append the hidden input field to the form
                document.getElementById("main-form").appendChild(hiddenInput);
            });

            initSelect2SearchPaginationData(`invoice_down_payment_id`, `{{ route('admin.select.invoice-down-payment') }}`, {
                id: 'id',
                text: 'name'
            }, 0, {}, "", false, true)
        })
    </script>

    <script>
        sidebarMenuOpen('#trading');
        sidebarActive('#invoice-trading');
    </script>
@endsection
