@extends('layouts.admin.layout.index')

@php
    $main = 'invoice-general';
    $default_taxes = getDafaultTaxes();
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
                        <a href="{{ route('admin.invoice-trading.index') }}">{{ Str::headline($main) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Create ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <form id="form" action="{{ route("admin.$main.update", $model) }}" method="post" enctype="multipart/form-data">
        @method('PUT')
        @csrf

        <x-card-data-table title="{{ 'Edit ' . $main }}">
            <x-slot name="header_content">
            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" name="kode" label="kode" value="{{ $model->code }}" id="" required readonly />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-select label="customer" id="customer-id" required disabled>
                                @if ($model->customer_id)
                                    <option value="{{ $model->customer_id }}" selected>{{ $model->customer?->nama }}
                                    </option>
                                @endif
                            </x-select>
                            <input type="hidden" name="customer_id" value="{{ $model->customer_id }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-select name="bank_internal_ids[]" label="bank" id="bank-internal-select" required helpers="jika bank kosong silahkan isi dari master customer" multiple>
                            </x-select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-select label="term of payment" id="top-select" required disabled>
                                <option value="{{ $model->term_of_payments }}" selected>{{ $model->term_of_payments }}</option>
                            </x-select>
                            <input type="hidden" name="term_of_payments" value="{{ $model->term_of_payments }}">
                        </div>
                    </div>
                    <div class="col-md-3" id="top-days-row">
                        <div class="form-group">
                            <x-input type="number" label="top days" value="{{ $model->due }}" id="top-days" required disabled />
                            <x-input type="hidden" name="top_days" value="{{ $model->due }}" />
                        </div>
                    </div>
                </div>
                <div class="row mt-20">
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input class="datepicker-input" name="date_invoice" label="tanggal" value="{{ \Carbon\Carbon::parse($model->date)->format('d-m-Y') }}" onchange="checkClosingPeriod($(this));calculcate_due_date()" id="date" required />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input class="datepicker-input" name="due_date" label="due_date" value="{{ \Carbon\Carbon::parse($model->due_date)->format('d-m-Y') }}" id="" required readonly id="due-date" />
                        </div>
                    </div>
                    {{--                    <div class="col-md-3"> --}}
                    {{--                        <div class="form-group"> --}}
                    {{--                            <x-input type="text" name="reference" class="tax-reference-mask" label="faktur pajak" value="{{ $model->reference }}" id="" /> --}}
                    {{--                        </div> --}}
                    {{--                    </div> --}}
                </div>
                <div class="row mt-20">
                    @if (get_current_branch()->is_primary)
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select label="branch" name="branch_id" id="branch-select" required>
                                    <option value="{{ get_current_branch()->id }}">{{ get_current_branch()->name }}</option>
                                </x-select>
                            </div>
                        </div>
                    @endif
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-select name="currency_id" label="mata uang" id="currency-select" required>
                                <option value="{{ get_local_currency()->id }}" selected>{{ get_local_currency()->nama }}</option>
                            </x-select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" name="exchange_rate" label="nilai_tukar" id="exchange-rate" class="commas-form" value="1" required readonly />
                        </div>
                    </div>
                </div>
                <div class="row mt-20">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="mb-2" for="select-vendor">Sales Order <span class="text-danger">*</span></label>
                            <select class="form-control select2" id="sale-order-select" value="" required multiple disabled>
                                @if ($model->sale_order_general_id)
                                    <option value="{{ $model->sale_order_general_id }}" selected>
                                        {{ $model->sale_order_general?->kode }}</option>
                                @else
                                    @foreach ($model->invoice_general_details->unique('sale_order_general_id') as $invoiceDetail)
                                        <option value="{{ $invoiceDetail->sale_order_general->id }}" selected>{{ $invoiceDetail->sale_order_general->kode }}</option>
                                    @endforeach
                                @endif
                            </select>

                            @if ($model->sale_order_general_id)
                                <input type="hidden" name="sales_order_id[]" value="{{ $model->sale_order_general_id }}">
                            @else
                                @foreach ($model->invoice_general_details->unique('sale_order_general_id') as $invoiceDetail)
                                    <input type="hidden" name="sales_order_id[]" value="{{ $invoiceDetail->sale_order_general->id }}">
                                @endforeach
                            @endif

                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="mb-2" for="select-vendor">Delivery Order <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="delivery_order_id[]" id="delivery-order-select" value="" required multiple disabled>
                                {{--  --}}
                            </select>
                            <div id="delivery-order-select-value"></div>
                        </div>
                        {{-- <button type="button" id="btnClearDo" class="btn btn-sm btn-danger mb-4">Hapus Semua Delivery
                            Order</button> --}}
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="mb-2" for="select-vendor">Reference</label>
                            <select class="form-control select2" name="so_references[]" id="so_references" multiple>
                                @foreach ($model->invoice_general_details->unique('sale_order_general_id') as $invoiceDetail)
                                    <option value="{{ $invoiceDetail->sale_order_general->id }}" {{ in_array($invoiceDetail->sale_order_general->id, $so_references) ? 'selected' : '' }}>{{ $invoiceDetail->sale_order_general->no_po_external ?? $invoiceDetail->sale_order_general->kode }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12"></div>
                    <div class="col-md-4">
                        <x-select name="invoice_down_payment_id[]" label="Down Payment" id="invoice_down_payment_id" multiple>
                            @foreach ($down_payments as $down_payment)
                                <option value="{{ $down_payment->invoice_down_payment_id }}" selected>{{ $down_payment->invoice_down_payment->code }}</option>
                            @endforeach
                        </x-select>
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
            </x-slot>
        </x-card-data-table>
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Additional Item</h3>
            </div>
            <div class="box-body" id="additional-item-form">

            </div>
        </div>
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Summary</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <h5 class="fw-bold">Main Item</h5>
                        <div class="table-responsive">
                            <x-table isStriped='' theadColor='' id="summaryMainItem">
                                <x-slot name="table_head">
                                    <th>{{ Str::headline('kode do') }}</th>
                                    <th>{{ Str::headline('Item') }}</th>
                                    <th>{{ Str::headline('Harga') }}</th>
                                    <th>{{ Str::headline('Jumlah Dikirim') }}</th>
                                    <th>{{ Str::headline('Jumlah Untuk Invoice') }}</th>
                                    <th>{{ Str::headline('sub total') }}</th>
                                    <th>{{ Str::headline('Pajak') }}</th>
                                    <th>{{ Str::headline('Value') }}</th>
                                    <th></th>
                                </x-slot>
                                <x-slot name="table_body">

                                </x-slot>
                                <x-slot name="table_foot">
                                    <tr>
                                        <th colspan="8" class="text-end">Total DPP</th>
                                        <td class="text-end">
                                            <div class="d-flex">
                                                <span id="currency-simbol" class="me-10"></span>
                                                <span class="ms-auto w-100" id="main-dpp">0</span>
                                                <input type="hidden" name="sub_total_main" id="dpp-main" value="0">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th colspan="8" class="text-end">Total pajak</th>
                                        <td class="text-end">
                                            <div class="d-flex">
                                                <span id="currency-simbol" class="me-10"></span>
                                                <span class="ms-auto w-100" id="main-tax-total">0</span>
                                                <input type="hidden" name="total_tax_main" id="total-tax-main" value="0">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th colspan="8" class="text-end fw-bold">Total</th>
                                        <td class="bg-success text-white text-end">
                                            <div class="d-flex">
                                                <span id="currency-simbol" class="me-10"></span>
                                                <span class="ms-auto w-100" id="main-total">0</span>
                                                <input type="hidden" name="total_main" id="total-main" value="0">
                                            </div>
                                        </td>
                                    </tr>
                                </x-slot>
                            </x-table>
                        </div>
                    </div>
                    <div class="col-md-12 border-top border-primary mt-20">
                        <div class="row pt-20">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="tax_data[]" label="Pajak" id="additional-tax-select" multiple></x-select>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-self-end">
                                <div class="form-group">
                                    <x-button color="info" label="kalkulasi-ulang-additional" size="sm" icon="arrows-rotate" fontawesome id="btn-recalculate-additional"></x-button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-20">
                        <h5 class="fw-bold">Additional Item</h5>
                        <div class="table-responsive">
                            <x-table theadColor='' id="summaryAdditionalItem">
                                <x-slot name="table_head">
                                    <th>{{ Str::headline('Item') }}</th>
                                    <th>{{ Str::headline('Harga') }}</th>
                                    <th>{{ Str::headline('Jumlah') }}</th>
                                    <th>{{ Str::headline('Pajak') }}</th>
                                    <th>{{ Str::headline('Value') }}</th>
                                    <th>{{ Str::headline('total') }}</th>
                                </x-slot>
                                <x-slot name="table_body">

                                </x-slot>
                                <x-slot name="table_foot">
                                    <tr>
                                        <th colspan="5" class="text-end fw-bold">Total DPP</th>
                                        <td class="text-end" id="additional-dpp-total">
                                            <div class="d-flex">
                                                <span id="currency-simbol" class="me-10"></span>
                                                <span class="ms-auto w-100" id="additional-dpp-total">0</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th colspan="5" class="text-end fw-bold">Total pajak</th>
                                        <td class="text-end">
                                            <div class="d-flex">
                                                <span id="currency-simbol" class="me-10"></span>
                                                <span class="ms-auto w-100" id="additional-tax-total">0</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th colspan="5" class="text-end fw-bold">Total</th>
                                        <td class="bg-success text-white text-end">
                                            <div class="d-flex">
                                                <input type="hidden" name="sub_total_additional" id="total-dpp-additional" value="0">
                                                <input type="hidden" name="total_tax_additional" id="total-tax-additional" value="0">
                                                <span id="currency-simbol" class="me-10"></span>
                                                <span class="ms-auto w-100" id="additional-total">0</span>
                                                <input type="hidden" name="total_additional" id="total-additional" value="0">
                                            </div>
                                        </td>
                                    </tr>
                                </x-slot>
                            </x-table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <div class="d-flex justify-content-end">
                    <x-button link="{{ route('admin.invoice.index') }}" color="secondary" label="Cancel" class="me-2" />
                    <x-button type="submit" color="primary" label="Simpan" />
                </div>
            </div>
        </div>

    </form>
@endsection

@section('js')
    <script src="{{ asset('js/admin/select/SelectDeliveryOrderGeneral.js') }}"></script>
    <script src="{{ asset('js/admin/select/itemSelect.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script>
        let currency_symbol = 'Rp';

        let data_delivery = [];
        let main_index = 0,
            main_dpp = [],
            main_tax_value = [],
            main_total_tax = [],
            main_sub_total = [];

        let additionalIdx = 0,
            additional_item = [],
            additional_item_tax_value_total = [],
            additional_dpp_list = [],
            additional_tax_list = [],
            additional_tax_total_list = [];

        let ADDITIONAL_TAX_LIST_VALUE = [],
            ADDITIONAL_TAX_LIST_ID = [];

        let defaultAdditionalItem = [];

        let tax_data = @json($tax_data);

        $(document).ready(function() {
            checkClosingPeriod($('#date'));

            initSelect2Search(`additional-tax-select`, "{{ route('admin.select.tax') }}", {
                id: "id",
                text: "name"
            })

            if (tax_data) {
                tax_data.map((tax_item, index) => {
                    let opt = new Option(tax_item.tax.name, tax_item.tax.id, true, true);
                    $('#additional-tax-select').append(opt).trigger('change');
                })
            }

            $(`#additional-tax-select`).change(
                $.debounce(1000, function(e) {
                    ADDITIONAL_TAX_LIST_ID = $('#additional-tax-select').val();
                    ADDITIONAL_TAX_LIST_VALUE = [];

                    ADDITIONAL_TAX_LIST_ID.map((tax_id, index) => {
                        setTimeout(() => {
                            $.ajax({
                                type: "get",
                                url: `{{ route('admin.tax.detail') }}/${tax_id}`,
                                success: function({
                                    data
                                }) {
                                    ADDITIONAL_TAX_LIST_VALUE.push(data)
                                    redrawAndRecalculateAdditionalItemTax()
                                }
                            });
                        }, 500);
                    });
                })
            );

            setTimeout(() => {
                ADDITIONAL_TAX_LIST_ID = $('#additional-tax-select').val();
                ADDITIONAL_TAX_LIST_VALUE = [];

                ADDITIONAL_TAX_LIST_ID.map((tax_id, index) => {
                    setTimeout(() => {
                        $.ajax({
                            type: "get",
                            url: `{{ route('admin.tax.detail') }}/${tax_id}`,
                            success: function({
                                data
                            }) {
                                ADDITIONAL_TAX_LIST_VALUE.push(data)
                                redrawAndRecalculateAdditionalItemTax()
                            }
                        });
                    }, 500);
                });
            }, 500);

            initSelect2SearchPaginationData(`invoice_down_payment_id`, `{{ route('admin.select.invoice-down-payment') }}`, {
                id: 'id',
                text: 'code'
            }, 0, {
                customer_id: function() {
                    return $('#customer-id').val();
                },
                currency_id: function() {
                    return $('#currency-select').val();
                },
                selected_id: JSON.parse("{{ $down_payments->pluck('invoice_down_payment_id') }}")
            }, 0, false);

            const setCurrencySymbol = () => {
                $('span#currency-simbol').each(function() {
                    $(this).text(`${currency_symbol}`);
                });
            }
            setCurrencySymbol()

            const calculateDppMain = () => {
                let dpp_total = 0;

                $('.main-sub-total').each(function() {
                    let value = $(this).val();
                    dpp_total += parseFloat(value);
                });

                $('#main-dpp').text(formatRupiahWithDecimal(dpp_total));
                $('#dpp-main').val(dpp_total);
            }

            const calculateTaxMain = () => {
                let tax_total = 0;

                $('.main-tax-value').each(function() {
                    let value = $(this).val();
                    tax_total += parseFloat(value);
                });

                $('#main-tax-total').text(formatRupiahWithDecimal(tax_total));
                $('#total-tax-main').val(tax_total);
            }

            const calculateTotalMain = () => {
                let dpp_total = parseFloat($('#dpp-main').val() || 0);
                let tax_total = parseFloat($('#total-tax-main').val() || 0);

                $('#main-total').text(formatRupiahWithDecimal(dpp_total + tax_total));
                $('#total-main').val(dpp_total + tax_total);
            }

            const addAdditional = (additional_index, defaultItem = false, isInit = false) => {
                let btn = '';
                if (additional_index == 0) {
                    btn = `
                        <div class="col-md-auto d-flex align-items-end">
                            <div class="form-group">
                                <x-button type="button" color="info" icon="plus" fontawesome size="sm" id="additional-add-item" />
                            </div>
                        </div>
                    `;
                } else {
                    btn = `
                        <div class="col-md-auto d-flex align-items-end">
                            <div class="form-group">
                                <x-button type="button" color="danger" icon="trash" fontawesome size="sm" id="additional-delete-item-${additional_index}" />
                            </div>
                        </div>
                    `;
                }

                if (defaultItem) {
                    $('#additional-item-form').append(`
                        <div class="row ${additional_index == 0 ? '' : 'border-top mt-20 pt-20'}" id="additional-row-${additional_index}">
                            <input type="hidden" name="additional_id[]" value="${defaultItem.id ?? ``}">
                            <input type="hidden" name="additional_item_row_index[]" value="${additional_index}">
                            <div class="col-md-auto">
                                <div class="form-group">
                                    <x-select name="additional_item_type[]" label="item type" id="additional-item-type-${additional_index}" >
                                        <option value="" selected>Pilih tipe</option>
                                        <option value="service" ${defaultItem.item && (defaultItem.item?.type == "service") ? 'selected' : ``}>Service</option>
                                        <option value="transport" ${defaultItem.item && (defaultItem.item?.type == "transport") ? 'selected' : ``}>Transport</option>
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-select name="additional_item_id[]" label="item" id="additional-item-select-${additional_index}" >
                                        ${defaultItem.item && '<option value="' + defaultItem.item?.id + '" selected>' + defaultItem.item?.name + '</option>'}
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-input type="text" name="additional_price[]" label="harga" class="commas-form" id="additional-price-${additional_index}" value="${defaultItem.price ? formatRupiahWithDecimal(defaultItem.price) : ``}"  />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-input type="text" name="additional_quantity[]" label="jumlah" class="commas-form" id="additional-quantity-${additional_index}"  value="${defaultItem.quantity ? formatRupiahWithDecimal(defaultItem.quantity) : ``}"   />
                                </div>
                            </div>
                            ${btn}
                        </div>
                    `);
                } else {
                    $('#additional-item-form').append(`
                        <div class="row ${additional_index == 0 ? '' : 'border-top mt-20 pt-20'}" id="additional-row-${additional_index}">
                            <input type="hidden" name="additional_item_row_index[]" value="${additional_index}">
                            <div class="col-md-auto">
                                <div class="form-group">
                                    <x-select name="additional_item_type[]" label="item type" id="additional-item-type-${additional_index}" >
                                        <option value="" selected>Pilih tipe</option>
                                        <option value="service">Service</option>
                                        <option value="transport">Transport</option>
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-select name="additional_item_id[]" label="item" id="additional-item-select-${additional_index}" disabled>
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-input type="text" name="additional_price[]" label="harga" class="commas-form" id="additional-price-${additional_index}" disabled />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-input type="text" name="additional_quantity[]" label="jumlah" class="commas-form" id="additional-quantity-${additional_index}" disabled />
                                </div>
                            </div>
                            ${btn}
                        </div>
                    `);
                }

                if (!isInit) {
                    if (defaultItem) {
                        $('#summaryAdditionalItem tbody').append(`
                            <tr id="additional-table-summary-${additional_index}">
                                <td id="additional-summary-item-${additional_index}">${defaultItem.item.code + ' - ' + defaultItem.item.name}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <span id="currency-simbol" class="d-inline mb-0">${currency_symbol}</span>
                                        <p class="d-inline mb-0" id="additional-summary-price-${additional_index}">${formatRupiahWithDecimal(defaultItem.price)}</p>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <p class="d-inline mb-0" id="additional-summary-quantity-${additional_index}">${formatRupiahWithDecimal(defaultItem.quantity)}</p>
                                    </div>
                                </td>
                                <td id="additional-summary-tax-${additional_index}">

                                </td>
                                <td id="additional-summary-tax-value-${additional_index}">
                                    
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <span class="d-inline mb-0" id="currency-simbol">${currency_symbol}</span>
                                        <p class="d-inline" id="additional-summary-sub-total-${additional_index}">${formatRupiahWithDecimal(defaultItem.sub_total)}</p>
                                    </div>
                                </td>
                            </tr>
                        `);
                    } else {
                        $('#summaryAdditionalItem tbody').append(`
                            <tr id="additional-table-summary-${additional_index}">
                                <td id="additional-summary-item-${additional_index}">-</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <span id="currency-simbol" class="d-inline mb-0">${currency_symbol}</span>
                                        <p class="d-inline mb-0" id="additional-summary-price-${additional_index}">0</p>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <p class="d-inline mb-0" id="additional-summary-quantity-${additional_index}">0</p>
                                    </div>
                                </td>
                                <td id="additional-summary-tax-${additional_index}">

                                </td>
                                <td id="additional-summary-tax-value-${additional_index}">
                                    
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <span class="d-inline mb-0" id="currency-simbol">${currency_symbol}</span>
                                        <p class="d-inline" id="additional-summary-sub-total-${additional_index}">0</p>
                                    </div>
                                </td>
                            </tr>
                        `);
                    }
                }

                setCurrencySymbol();

                const addAdditionalTax = () => {
                    let item_tax_value_total = 0;
                    ADDITIONAL_TAX_LIST_VALUE.map((tax, tax_index) => {
                        $(`#additional-summary-tax-${additional_index}`).append(`<p>
                            <span>${tax.name} - ${(tax.value * 100).toFixed(2)}%</span>
                        </p>`);

                        let item_tax_value = additional_dpp_list[additional_index] ? additional_dpp_list[additional_index] * tax.value : 0;

                        $(`#additional-summary-tax-value-${additional_index}`).append(`<p>
                            <span id="tax-${tax_index}-${additional_index}">${formatRupiahWithDecimal((item_tax_value).toFixed(2))}</span>
                        </p>`)

                        item_tax_value_total += item_tax_value;
                    });
                    additional_item_tax_value_total[additional_index] = item_tax_value_total;
                }
                addAdditionalTax();

                const removeAdditional = (index) => {
                    $(`#additional-row-${index}`).remove();
                    $(`#additional-table-summary-${index}`).remove();

                    additional_item.splice(index, 1);
                    additional_item_tax_value_total.splice(index, 1);
                    additional_dpp_list.splice(index, 1);
                    additional_tax_list.splice(index, 1);
                    additional_tax_total_list.splice(index, 1);

                    redrawAndRecalculateAdditionalItemTax();
                    calculateDppAdditional();
                    calculateTaxTotalAdditional();
                    calculateTotalAdditional();
                }

                if (additional_index == 0) {
                    $('#additional-add-item').click(function(e) {
                        additionalIdx++;
                        addAdditional(additionalIdx);
                    });
                } else {
                    $(`#additional-delete-item-${additional_index}`).click(function() {
                        removeAdditional(additional_index);
                    });
                }

                let calculateSubTotal = () => {
                    let price = formatThousandToFloat($(`#additional-price-${additional_index}`).val() ||
                        0);
                    let qty = formatThousandToFloat($(`#additional-quantity-${additional_index}`).val() ||
                        0);
                    let subTotal = formatRupiahWithDecimal(price * qty);
                    let tax_list = $(`#additional-tax-select-${additional_index}`).val() ?? [];

                    additional_dpp_list[additional_index] = formatThousandToFloat(subTotal);

                    $(`#additional-summary-sub-total-${additional_index}`).html(subTotal);

                    if (tax_list.length == 0) {
                        $(`#additional-total-${additional_index}`).html(subTotal);
                    }
                }

                initCommasForm();

                $(`#additional-item-type-${additional_index}`).select2({
                    width: '100%'
                });

                $(`#additional-item-type-${additional_index}`).change(function() {
                    $(`#additional-summary-item-${additional_index}`).html('-');
                    $(`#additional-summary-price-${additional_index}`).html('0');
                    $(`#additional-summary-quantity-${additional_index}`).html('0');

                    $(`#additional-price-${additional_index}`).val(null);
                    $(`#additional-quantity-${additional_index}`).val(null);
                    $(`#additional-tax-select-${additional_index}`).val(null).trigger('change');

                    additional_tax_total_list[additional_index] = 0;

                    redrawAndRecalculateAdditionalItemTax()
                    calculateSubTotal();
                    calculateDppAdditional();
                    calculateTaxTotalAdditional();
                    calculateTotalAdditional();

                    if (this.value) {
                        $(`#additional-item-select-${additional_index}`).prop('disabled', false)
                        $(`#additional-price-${additional_index}`).prop('disabled', false)
                        $(`#additional-quantity-${additional_index}`).prop('disabled', false)
                        $(`#additional-tax-select-${additional_index}`).prop('disabled', false)

                        $(`#additional-item-select-${additional_index}`).val(null).trigger('change');

                        inititemSelect(`additional-item-select-${additional_index}`, this.value);
                        initAdditionalTax();
                    } else {
                        $(`#additional-item-select-${additional_index}`).val(null);
                        $(`#additional-price-${additional_index}`).val(null);
                        $(`#additional-quantity-${additional_index}`).val(null);
                        $(`#additional-tax-select-${additional_index}`).val(null);

                        $(`#additional-item-select-${additional_index}`).prop('disabled', true)
                        $(`#additional-price-${additional_index}`).prop('disabled', true)
                        $(`#additional-quantity-${additional_index}`).prop('disabled', true)
                        $(`#additional-tax-select-${additional_index}`).prop('disabled', true)
                    }
                });

                inititemSelect(`additional-item-select-${additional_index}`, $(
                    `#additional-item-type-${additional_index}`).val());

                $(`#additional-item-select-${additional_index}`).change(function() {
                    if (this.value) {
                        $(`#additional-price-${additional_index}`).prop('disabled', false);
                        $(`#additional-quantity-${additional_index}`).prop('disabled', false);
                        $(`#additional-tax-select-${additional_index}`).prop('disabled', false);

                        $(`#additional-summary-item-${additional_index}`).html($(this).select2('data')[
                            0].text);

                        $.ajax({
                            type: "get",
                            url: "{{ route('admin.item.price-latest') }}/" + $(this).val(),
                            success: function({
                                data
                            }) {
                                $(`#additional-price-${additional_index}`).val(
                                    formatThousandToFloat(formatRupiahWithDecimal(data
                                        .harga_jual)));
                                $(`#additional-price-${additional_index}`).trigger('focus');
                                $(`#additional-price-${additional_index}`).trigger('keyup');
                            }
                        });
                    } else {
                        $(`#additional-summary-item-${additional_index}`).html('-');
                        $(`#additional-price-${additional_index}`).val(null);
                        $(`#additional-quantity-${additional_index}`).val(null);
                        $(`#additional-tax-select-${additional_index}`).val(null).trigger('change');

                        $(`#additional-price-${additional_index}`).prop('disabled', true);
                        $(`#additional-quantity-${additional_index}`).prop('disabled', true);
                        $(`#additional-tax-select-${additional_index}`).prop('disabled', true);

                        redrawAndRecalculateAdditionalItemTax()
                        calculateSubTotal();
                        calculateDppAdditional();
                        calculateTaxTotalAdditional();
                        calculateTotalAdditional();
                    }
                });

                $(`#additional-price-${additional_index}`).keyup(debounce(function() {
                    $(`#additional-summary-price-${additional_index}`).html(formatRupiahWithDecimal(
                        $(this).val()));

                    redrawAndRecalculateAdditionalItemTax()
                    calculateSubTotal();
                    calculateDppAdditional();
                    calculateTaxTotalAdditional();
                    calculateTotalAdditional();
                }, 500));

                $(`#additional-quantity-${additional_index}`).keyup(debounce(function() {
                    $(`#additional-summary-quantity-${additional_index}`).html(
                        formatRupiahWithDecimal($(this).val()));

                    redrawAndRecalculateAdditionalItemTax()
                    calculateSubTotal();
                    calculateDppAdditional();
                    calculateTaxTotalAdditional();
                    calculateTotalAdditional();
                }, 500));

                if (defaultItem) {
                    redrawAndRecalculateAdditionalItemTax();
                    calculateSubTotal();
                    calculateDppAdditional();
                    calculateTaxTotalAdditional();
                    calculateTotalAdditional();
                }

                let item = {
                    index: additional_index
                }
                additional_item.push(item);

                additionalIdx++;
            };

            const loadDefaultAdditionalItem = () => {
                let tax = [];
                @if (count($model->invoice_general_additionals) > 0)
                    @foreach ($model->invoice_general_additionals as $key => $value)
                        tax[{{ $key }}] = []
                        @foreach ($value->invoice_general_additional_taxes as $tax_key => $value_tax)
                            tax[{{ $key }}].push({
                                id: "{{ $value_tax->id }}",
                                invoice_general_additional_id: "{{ $value_tax->invoice_general_additional_id }}",
                                tax: {
                                    id: "{{ $value_tax->tax_id }}",
                                    nama: "{{ $value_tax->tax?->name }}"
                                },
                                value: {{ $value_tax->value }},
                                total: {{ $value_tax->total }}
                            })
                        @endforeach

                        defaultAdditionalItem.push({
                            id: "{{ $value->id }}",
                            invoice_general_id: {
                                id: "{{ $value->invoice_general_id }}",
                                code: "{{ $value->invoice_general?->code }}"
                            },
                            item: {
                                id: "{{ $value->item_id }}",
                                code: "{{ $value->item?->kode }}",
                                name: "{{ $value->item?->nama }}",
                                type: "{{ $value->item?->type }}"
                            },
                            unit: {
                                id: "{{ $value->unit_id }}",
                            },
                            quantity: {{ $value->quantity }},
                            price: {{ $value->price }},
                            sub_total: {{ $value->sub_total }},
                            total_tax: {{ $value->total_tax }},
                            total: {{ $value->total }},
                            tax: tax[{{ $key }}]
                        })
                    @endforeach
                @else
                    addAdditional(additionalIdx)
                @endif

                setTimeout(() => {
                    defaultAdditionalItem.map((item, idx) => {
                        addAdditional(additionalIdx, item)
                    })
                }, 500)
            }
            loadDefaultAdditionalItem()

            const loadDeliveryOrderBySO = () => {
                const value = $('#sale-order-select').val()
                let defaultValue = []
                @if (count($model->invoice_general_details) > 0)
                    @foreach ($model->invoice_general_details as $key => $inv_detail)
                        @if ($key == 0)
                            defaultValue.push({
                                id: "{{ $inv_detail->delivery_order_general_detail?->delivery_order_general_id }}",
                                code: "{{ $inv_detail->delivery_order_general_detail?->delivery_order_general?->code }}"
                            });
                        @else
                            @if ($model->invoice_general_details[$key - 1]->delivery_order_general_detail?->delivery_order_general_id != $inv_detail->delivery_order_general_detail?->delivery_order_general_id)
                                defaultValue.push({
                                    id: "{{ $inv_detail->delivery_order_general_detail?->delivery_order_general_id }}",
                                    code: "{{ $inv_detail->delivery_order_general_detail?->delivery_order_general?->code }}"
                                });
                            @endif
                        @endif
                    @endforeach
                @endif

                if (defaultValue.length > 0) {
                    let htmlRender = ''
                    defaultValue.map((res, id) => {
                        htmlRender += `<option value="${res.id}" selected>${res.code}</option>\n`
                    })
                    $('#delivery-order-select').html(htmlRender)

                    let htmlInput = "";
                    defaultValue.map((res, id) => {
                        htmlInput += `<input type="hidden" name="delivery_order_id[]" value="${res.id}">\n`
                    })

                    $('#delivery-order-select-value').html(htmlInput)

                    setTimeout(() => {
                        $('#delivery-order-select').trigger('change')
                    }, 500)
                }

                $('#delivery-order-select').select2({
                    multiple: true,
                    allowClear: false,
                    placeholder: 'Pilih Delivery Order',
                    language: {
                        noResults: () => {
                            return "Data tidak ditemukan";
                        },
                    },
                    ajax: {
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: "{{ route('admin.delivery-order-general.get-by-customer-so') }}",
                        dataType: "json",
                        delay: 250,
                        type: "post",
                        data: ({
                            term
                        }) => {
                            let result = {};
                            result["search"] = term;
                            // result["customer_id"] = $('#customer-id').val();
                            result["sale_order_general_id"] = $('#sale-order-select').val();
                            result["customer_id"] = function() {
                                return $('#customer-id').val();
                            };
                            result['branch_id'] = function() {
                                return $('#branch-select').val();
                            };
                            result['currency_id'] = function() {
                                return $('#currency-select').val();
                            };

                            return result;
                        },
                        processResults: ({
                            data
                        }) => {
                            let final_data = data.map((data, key) => {
                                return {
                                    id: data.id,
                                    text: data.code,
                                };
                            });
                            return {
                                results: final_data,
                            };
                        },
                        cache: true,
                    },
                });
            }
            loadDeliveryOrderBySO()

            const loadBank = () => {
                const value = $('#customer-id').val()
                $.ajax({
                    url: `{{ route('admin.select.customer-detail') }}/${value}`,
                    type: "GET",
                    success: function(res) {
                        $("#bank-internal-select").empty();
                        res.data.customer_banks.map((bank, index) => {
                            let opts = null;
                            if (@json($model->bank_internal_ids ?? []).includes(bank.id)) {
                                $("#bank-internal-select").append(
                                    `<option value="${bank.id}" selected>${bank.bank_internal.nama_bank} - ${bank.bank_internal.no_rekening}</option>`
                                ).trigger('change')
                            } else {
                                opts = new Option(
                                    `${bank.bank_internal.nama_bank} - ${bank.bank_internal.no_rekening}`,
                                    bank.bank_internal.id, index == 0 ? true : false, index == 0 ?
                                    true :
                                    false);
                            }
                            $("#bank-internal-select").append(opts).trigger('change');
                        });

                        $('#top-select').change(function() {
                            if (this.value == 'by days') {
                                $('#top-days').prop('disabled', false);
                            } else {
                                $('#top-days').prop('disabled', true);
                            }
                        })
                    }
                });

                $('#summaryMainItem tbody').find('tr').remove();
            }
            loadBank()

            const display_data = () => {
                $('#summaryMainItem tbody').empty();
                $.each(data_delivery, function(do_detail_index, do_detail) {
                    let final_qty = do_detail.invoice_quantity ?? do_detail.quantity_received;
                    let sub_total = do_detail.sale_order_general_detail.price * parseFloat(final_qty);

                    $('#summaryMainItem tbody').append(`
                        <tr class="do-${do_detail.delivery_order_general.code}">
                            <td class="text-center">${do_detail.delivery_order_general.code}</td>
                            <td>${do_detail.item.kode} - ${do_detail.item.nama}</td>
                            <td>${currency_symbol} ${formatRupiahWithDecimal(parseFloat(do_detail.sale_order_general_detail.price))} / ${do_detail.unit.name}</td>
                            <td>${parseFloat(do_detail.quantity)}</td>
                            <td>
                                <input class="form-control commas-form text-end" name="invoice_quantity_${do_detail.id}" id="invoice-quantity-${do_detail_index}" value="${formatRupiahWithDecimal(final_qty)}" />
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <span class="d-inline mb-0" id="currency-simbol">${currency_symbol}</span>
                                    <p class="d-inline">${formatRupiahWithDecimal(sub_total)}</p>
                                    <input type="hidden" class="main-sub-total" value="${sub_total}" />
                                </div>
                            </td>
                            <td id="main-tax-name-${main_index}"></td>
                            <td id="main-tax-value-${main_index}"></td>
                            <td>
                                <div class="d-flex gap-2">
                                    <span class="d-inline mb-0" id="currency-simbol">${currency_symbol}</span>
                                    <p class="d-inline" id="do-total-${do_detail.delivery_order_general.code}-${do_detail_index}">0</p>
                                </div>
                            </td>
                        </tr>
                    `);

                    $(`#invoice-quantity-${do_detail_index}`).on('blur', function() {
                        data_delivery[do_detail_index].invoice_quantity = formatThousandToFloat($(this).val());

                        display_data();
                    });

                    $.each(do_detail.sale_order_general_detail
                        .sale_order_general_detail_taxes,
                        function(tax_index, tax) {

                            let tax_value = parseFloat((do_detail.sale_order_general_detail.price * tax.tax.value) * parseFloat(do_detail.quantity));
                            sub_total += parseFloat(tax_value);

                            $(`#main-tax-name-${main_index}`)
                                .append(`<p class="mb-0">${tax.tax.name} - ${tax.tax.value * 100}%</p>`);

                            $(`#main-tax-value-${main_index}`)
                                .append(`<div class="d-flex gap-2">
                                                <span class="mb-0" id="currency-simbol">${currency_symbol}</span>
                                                <p class="mb-0">${formatRupiahWithDecimal(tax_value)}</p>
                                                <input type="hidden" class="main-tax-value" value="${tax_value}" />
                                            </div>
                                        `);
                        });

                    $(`#do-total-${do_detail.delivery_order_general.code}-${do_detail_index}`)
                        .text(formatRupiahWithDecimal(
                            sub_total));

                    calculateDppMain();
                    calculateTaxMain();
                    calculateTotalMain();

                    main_index++;
                });
            }

            $('#delivery-order-select').change(function() {
                let value = $(this).val();

                $('#summaryMainItem tbody').empty();
                data_delivery = [];

                if (value.length > 0) {
                    value.map((data_do, index) => {
                        $.ajax({
                            type: "get",
                            url: `{{ route('admin.delivery-order-general.detail-for-invoice-general-detail') }}/${data_do}`,
                            success: function({
                                data
                            }) {

                                currency_symbol = data.sale_order_general?.currency
                                    ?.simbol;

                                main_dpp[main_index] = [];
                                main_total_tax[main_index] = [];

                                $.each(data.delivery_order_general_details, function(do_detail_index, do_detail) {
                                    data_delivery.push(do_detail);
                                });

                                display_data();
                            }
                        });
                    })
                }
            });

            $('#btnClearDo').click(function() {
                if ($('#delivery-order-select').val().length > 0) {
                    $('#summaryMainItem tbody').find('tr').remove();

                    calculateDppMain();
                    calculateTaxMain();
                    calculateTotalMain();

                    main_dpp = [];

                    $('#delivery-order-select').val(null).trigger('change');
                }
            });

            const calculateDppAdditional = () => {
                let additional_dpp = 0;
                additional_dpp_list.map((subTotal, index) => {
                    additional_dpp += subTotal;
                });
                $('#additional-dpp-total').html(formatRupiahWithDecimal(additional_dpp));
                $('#total-dpp-additional').val(additional_dpp);
            }

            const calculateTaxTotalAdditional = () => {
                let tax_total = 0;
                additional_item_tax_value_total.map((data, index) => {
                    tax_total += data;
                });
                $('#additional-tax-total').html(formatRupiahWithDecimal(tax_total));
                $('#total-tax-additional').val(tax_total);
            }

            const calculateTotalAdditional = () => {
                let dppTotalAdditional = parseFloat($('#total-dpp-additional').val() || 0);
                let taxTotalAdditional = parseFloat($('#total-tax-additional').val() || 0);
                $('#additional-total').text(formatRupiahWithDecimal(dppTotalAdditional + taxTotalAdditional));
                $('#total-additional').val(dppTotalAdditional + taxTotalAdditional);
            }

            $('#btn-recalculate-additional').click(function(e) {
                e.preventDefault();
                redrawAndRecalculateAdditionalItemTax()
                calculateDppAdditional()
                calculateTaxTotalAdditional()
                calculateTotalAdditional()
            })
        });

        $('#form').submit(function(e) {
            if ($('#total-tax-main').val() > 0) {
                if ($('input[name="reference"]').val() == "") {
                    e.preventDefault();
                    setTimeout(() => {
                        $('#form').find('input[type=submit]').prop('disabled', false);
                        $('#form').find('button[type=submit]').prop('disabled', false);
                    }, 1000);

                    Swal.fire({
                        icon: 'error',
                        title: '',
                        text: 'Maaf faktur pajak harus diisi',
                    });

                }
            }
        });

        function redrawAndRecalculateAdditionalItemTax() {
            additional_item.map((additional_item, additional_item_index) => {
                let additional_index = additional_item.index;
                let item_tax_value_total = 0;

                $(`#additional-summary-tax-${additional_index}`).html('')
                $(`#additional-summary-tax-value-${additional_index}`).html('')

                ADDITIONAL_TAX_LIST_VALUE.map((tax, tax_index) => {
                    $(`#additional-summary-tax-${additional_index}`).append(`<p>
                        <span>${tax.name} - ${(tax.value * 100).toFixed(2)}%</span>
                    </p>`)

                    let item_tax_value = additional_dpp_list[additional_index] ? additional_dpp_list[additional_index] * tax.value : 0;

                    $(`#additional-summary-tax-value-${additional_index}`).append(`<p>
                        <span id="tax-${tax_index}-${additional_index}">${formatRupiahWithDecimal((item_tax_value).toFixed(2))}</span>
                    </p>`)

                    item_tax_value_total += item_tax_value;
                });
                additional_item_tax_value_total[additional_index] = item_tax_value_total;

                // recalculate sub total
                let price = formatThousandToFloat($(`#additional-price-${additional_index}`).val() || 0);
                let qty = formatThousandToFloat($(`#additional-quantity-${additional_index}`).val() || 0);
                let subTotalAfterTax = formatRupiahWithDecimal((price * qty) + item_tax_value_total);
                $(`#additional-summary-sub-total-${additional_index}`).html(subTotalAfterTax);
            })
        }

        const calculcate_due_date = () => {
            let top = $('#top-select').val();
            let top_days = $('#top-days').val();
            let date = convertLocalDate($('#date').val());

            if (top == 'by days') {
                let due_date = new Date(date);
                due_date.setDate(due_date.getDate() + parseInt(top_days));

                let day = due_date.getDate();
                let month = due_date.getMonth() + 1;
                let year = due_date.getFullYear();

                if (day < 10) {
                    day = '0' + day;
                }

                if (month < 10) {
                    month = '0' + month;
                }

                due_date = year + '-' + month + '-' + day;

                $('#due-date').val(localDate(due_date));
            } else {
                $('#due-date').val(localDate(date));
            }
        }
    </script>

    <script>
        sidebarMenuOpen('#trading');
        sidebarActive('#invoice-trading')
    </script>
@endsection
