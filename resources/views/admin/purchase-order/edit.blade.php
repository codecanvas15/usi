@extends('layouts.admin.layout.index')

@php
    $main = 'purchase-order';
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
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.purchase.index') }}">{{ Str::headline('Purchase') }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline("Detail $main") }}</a>
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
    @can("edit $main")
        <div class="box" id="loading-card">
            <div class="box-body">
                <h2 class="text-center">Loading...</h2>
            </div>
        </div>

        <form action="{{ route('admin.purchase-order.update', $model) }}" method="post" id="form-update">
            <input type="hidden" name="purchase_type" value="trading">
            @csrf
            @method('PUT')
            <x-card-data-table title="{{ 'edit purchase order' }}" id="trading-type-card">
                <x-slot name="header_content">

                </x-slot>
                <x-slot name="table_content">
                    @include('components.validate-error')
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select label="branch" name="branch_id" id="branch-select" required>
                                    <option value="{{ $model->branch->id }}">{{ $model->branch->name }}</option>
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input class="datepicker-input" id="tanggal" name="tanggal" label="tanggal" value="{{ localDate($model->tanggal) }}" onchange="checkClosingPeriod($(this))" helpers="Default Today" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="reference" id="reference" label="referensi PO" autofocus required onchange="init_purchase_order_reference(this)">
                                    <option value="purchase_request" {{ $model->purchase_request_trading ? 'selected' : '' }}>{{ Str::headline('purchase request') }}</option>
                                    <option value="sale_order" {{ $model->sale_order ? 'selected' : '' }}>{{ Str::headline('sales order') }}</option>
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3 {{ $model->sale_order ? 'd-none' : '' }}" id="purchase_request_trading_form">
                            <div class="form-group">
                                <x-select name="purchase_request_trading_id" id="purchase_request_trading_id" label="purchase request" hideAsterix>
                                    @if ($model->purchase_request_trading)
                                        <option value="{{ $model->purchase_request_trading_id }}">{{ $model->purchase_request_trading->code }}</option>
                                    @endif
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3 {{ $model->purchase_request_trading ? 'd-none' : '' }}"" id="sale_order_form">
                            <div class="form-group">
                                <x-select name="sale_order_id" id="sale_order_id" label="sales order" hideAsterix>
                                    @if ($model->sale_order)
                                        <option value="{{ $model->sale_order_id }}">{{ $model->sale_order->nomor_so }}</option>
                                    @endif
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input type="text" id="note" name="note" label="keterangan" value="{{ $model->note }}" />
                            </div>
                        </div>
                    </div>
                    <div id="main" class="row">

                    </div>
                </x-slot>
            </x-card-data-table>

            <x-card-data-table title="Trading Item">
                <x-slot name="table_content">
                    <div id="main-item"></div>
                </x-slot>
            </x-card-data-table>

            <x-card-data-table title="Additional Item">
                <x-slot name="table_content">
                    <div id="additional-item"></div>
                </x-slot>
            </x-card-data-table>

            <x-card-data-table title="">
                <x-slot name="table_content">
                    <div id="row-currency">

                    </div>
                    <div class="row mt-30">
                        <h4 class="fw-bold">Trading item</h4>
                        <x-table theadColor='danger' id="trading-item-table">
                            <x-slot name="table_head">
                                <th>Item</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Sub Total</th>
                            </x-slot>
                            <x-slot name="table_body">
                                <tr>
                                    <td id="trading-item-name">-</td>
                                    <td>
                                        <div class="d-flex">
                                            <p class="me-10 mb-0" id="currency-simbol">{{ get_local_currency()->simbol }}</p>
                                            <p class="mb-0" id="display-harga">0,00</p>
                                        </div>
                                    </td>
                                    <td>
                                        <span id="trading-item-total-liter">
                                            0,00
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex text-end">
                                            <p class="mb-0" id="currency-simbol">{{ get_local_currency()->simbol }}</p>
                                            <h5 class="ms-auto mb-0" id="trading-item-sub-total" data-value="0">0,00</h5>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <p class="text-end mb-0" id="main_ppn_name"></p>
                                    </td>
                                    <td>
                                        <div class="d-flex text-end">
                                            <p class="mb-0" id="currency-simbol">{{ get_local_currency()->simbol }}</p>
                                            <h5 class="ms-auto mb-0" id="main_ppn_value"></h5>
                                        </div>
                                    </td>
                                </tr>
                            </x-slot>
                            <x-slot name="table_foot">
                                <tr>
                                    <td class="text-end" colspan="3">Total</td>
                                    <td class="bg-success text-white d-flex text-end">
                                        <p id="currency-simbol" class="me-10 mb-0">{{ get_local_currency()->simbol }}</p>
                                        <h5 class="fw-bold mb-0 ms-auto" id="main-total" data-value="0">0,00</h5>
                                    </td>
                                </tr>
                            </x-slot>
                        </x-table>
                    </div>
                    <div class="mt-30">
                        <h4 class="fw-bold">Additional item</h4>
                        <x-table theadColor='danger' id="additional-item-table">
                            <x-slot name="table_head">
                                <th>Item</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>DPP</th>
                                <th>Tax</th>
                                <th>Value</th>
                                <th>Total</th>
                            </x-slot>
                            <x-slot name="table_body">

                            </x-slot>
                            <x-slot name="table_foot">
                                <tr>
                                    <td class="text-end" colspan="6">DPP</td>
                                    <td class="d-flex text-end">
                                        <p id="currency-simbol" class="me-10 mb-0">{{ get_local_currency()->simbol }}</p>
                                        <h5 class="mb-0 ms-auto" id="additional-dpp-total" data-value="0">0,00</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-end" colspan="6">Total Pajak</td>
                                    <td class="d-flex text-end">
                                        <p id="currency-simbol" class="me-10 mb-0">{{ get_local_currency()->simbol }}</p>
                                        <h5 class="mb-0 ms-auto" id="additional-tax-total" data-value="0">0,00</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-end" colspan="6">Total</td>
                                    <td class="bg-success text-white">
                                        <div class="d-flex text-end">
                                            <p id="currency-simbol" class="me-10 mb-0">{{ get_local_currency()->simbol }}</p>
                                            <h5 class="fw-bold mb-0 ms-auto" id="additional-total" data-value="0">0,00</h5>
                                        </div>
                                    </td>
                                </tr>
                            </x-slot>
                        </x-table>
                    </div>
                    <div class="mt-30">
                        <div class="row justify-content-end">
                            <div class="col-md-6 col-lg-4">
                                <x-table theadColor='danger'>
                                    <x-slot name="table_head">
                                        <th></th>
                                        <th></th>
                                    </x-slot>
                                    <x-slot name="table_body">
                                        <tr>
                                            <th>Trading Total</th>
                                            <td class="d-flex text-end">
                                                <p id="currency-simbol" class="me-10 mb-0">{{ get_local_currency()->simbol }}</p>
                                                <p class="fw-bold mb-0 text-end ms-auto" id="calculate-main-total">0</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Additional Total</th>
                                            <td class="d-flex text-end">
                                                <p id="currency-simbol" class="me-10 mb-0">{{ get_local_currency()->simbol }}</p>
                                                <p class="fw-bold mb-0 text-end ms-auto" id="calculate-additional-total">0</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Grand Total</th>
                                            <td class="bg-success text-white d-flex text-end">
                                                <p id="currency-simbol" class="me-10 mb-0">{{ get_local_currency()->simbol }}</p>
                                                <h5 class="fw-bold mb-0 text-end ms-auto" id="calculate-grand-total">0</h5>
                                            </td>
                                        </tr>
                                    </x-slot>
                                </x-table>
                            </div>
                        </div>
                    </div>
                </x-slot>
            </x-card-data-table>

            <x-card-data-table title="">
                <x-slot name="table_content">
                    <div class="float-end">
                        <x-button type="reset" color="secondary" class="w-auto" label="cancel" size="sm" icon="backward" fontawesome link="{{ url()->previous() }}" />
                        <x-button type="submit" color="primary" size="sm" icon="save" fontawesome class="w-auto" label="Save data" />
                    </div>
                </x-slot>
            </x-card-data-table>
        </form>
    @endcan
@endsection

@section('js')
    @can("edit $main")
        <script src="{{ asset('js/admin/select/itemSelect.js') }}"></script>
        <script src="{{ asset('js/form/select2search.js') }}"></script>
        <script src="{{ asset('js/helpers/helpers.js') }}"></script>
        <script>
            $(document).ready(function() {
                let currency_symbol = '{{ get_local_currency()->simbol }}';

                let model_data = [],
                    main_data = [],
                    additional_data = [];

                // main
                let main_ppn = 0,
                    ppn = [],
                    main_tax_total = [];

                // additional
                let additionalIdx = 0;

                let additional_dpp_list = [],
                    additional_tax_list = [],
                    additional_tax_total_list = [];

                // trading total
                let main_total = 0,
                    additional_total = 0;

                $('form#form-update').hide();

                $.ajax({
                    type: "get",
                    url: "{{ route('admin.purchase-order.detail-edit', $model) }}",
                    success: function({
                        data
                    }) {
                        let {
                            model,
                            additional
                        } = data;

                        model_data = model;
                        additional_data = additional;

                        $('#loading-card').hide(1000);
                        $('form#form-update').show(1000);

                        init();
                    }
                });

                const init = () => {
                    let {
                        customer,
                        vendor,
                        sh_number,
                        currency,
                        po_trading_detail,
                        purchase_order_taxes,
                        tanggal,
                        exchange_rate,
                        sale_confirmation,
                    } = model_data;

                    const calculateGrandTotal = () => {
                        let mainTotal = $('#main-total').attr('data-value');
                        let additionalTotal = $('#additional-total').attr('data-value');

                        $('#calculate-main-total').html(formatRupiahWithDecimal(mainTotal));
                        $('#calculate-additional-total').html(formatRupiahWithDecimal(additionalTotal));
                        $('#calculate-grand-total').html(formatRupiahWithDecimal(parseFloat(mainTotal) + parseFloat(additionalTotal)));
                    }

                    const updateCurrencySymbol = () => {
                        $('span#currency-simbol').each(function() {
                            $(this).html(currency_symbol);
                        });
                    }

                    const updateCurrentPrice = () => {
                        $.ajax({
                            type: "get",
                            url: `{{ route('admin.select.select-with-period-and-sh-number-and-search-harga-beli') }}/${$('#item_id').val()}/${$('#sh_number_id').val()}/${$('#tanggal').val()}`,
                            success: function({
                                data
                            }) {
                                $('#harga_trading').val(formatRupiahWithDecimal(data.harga_beli ?? 0));
                                $('#harga_trading').trigger('keyup');
                            }
                        });
                    }

                    const displayCustomer = () => {
                        let {
                            id,
                            nama
                        } = customer;

                        let {
                            sh_number_details
                        } = sh_number;

                        let drop, supply;

                        sh_number_details.map((data, index) => {
                            if (data.type == 'Supply Point') {
                                supply = data.alamat;
                            }
                            if (data.type == 'Drop Point') {
                                drop = data.alamat;
                            }
                        });

                        let selected_request = '';
                        if (model_data.purchase_request_trading_id) {
                            selected_request = `<option value="${model_data.purchase_request_trading_id}" selected>${model_data.purchase_request_trading?.code}</option>`;
                        }

                        $('#main').append(`
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-select name="customer_id" id="customer_id" label="customer" value="" required>
                                            <option value="${id}" selected>${nama}</option>
                                        </x-select>
                                    </div>
                                </div>
                                 <div class="col-md-3">
                                    <div class="form-group">
                                        <x-select name="sh_number_id" id="sh_number_id" label="sh_number" value="" required>
                                            <option value="${sh_number.id}" selected>${sh_number.kode}</option>
                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input type="text" id="supply_point" name="" label="supply point" value="${supply}" disabled hideAsterix />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input type="text" id="drop_point" name="" label="drop point" value="${drop}" disabled hideAsterix />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-select name="vendor_id" id="vendor_id" label="vendor" value="" required disabled>
                                            <option value="${vendor.id}" selected>${vendor.nama}</option>
                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-select name="top" id="top" label="term of payment" required>
                                            <option value="cash" ${model_data.top == 'cash' ? 'selected' : ''}>Cash</option>
                                            <option value="by days" ${model_data.top == 'by days' ? 'selected' : ''}>By Days</option>
                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input type="number" id="top_day" name="top_day" label="hari" value="${model_data.top_day}" required />
                                    </div>
                                </div>
                            </div>

                            <div class="mt-20 pt-20 border-top border-primary">
                                <div class="row ">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="keterangan"> SCO Supplier<span class="text-primary">*</span></label>
                                            <input class="form-control mt-2" type="text" id="sale_confirmation" name="sale_confirmation" ${sale_confirmation == null ? "value=''" : "value='"+ sale_confirmation +"'"} />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="file" label="quotation" name="quotation" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);

                        initDatePicker();

                        if (model_data.purchase_request_trading_id) {
                            initSelect2SearchPaginationData(`purchase_request_trading_id`, `{{ route('admin.select.purchase-request-trading') }}`, {
                                id: 'id',
                                text: 'code,customer_name'
                            })

                            $('#purchase_request_trading_id').change(function(e) {
                                $('#customer_id').val('').html('');
                                $('#sh_number_id').val('').html('');
                                $('#supply_point').val('');
                                $('#drop_point').val('');
                                $('#item_id').val('').html('');
                                $('#jumlah').val(0);
                                $('#outstanding_qty').val(0);

                                if ($(this).val() == '' || $(this).val() == null) {

                                    return false;
                                }
                                $.ajax({
                                    url: base_url + "/purchase-request-trading/" + $(this).val(),
                                    type: "GET",
                                    success: function(data) {
                                        $('#customer_id').html(`<option value="${data.data.customer_id}" selected>${data.data.customer.nama}</option>`);
                                        $('#sh_number_id').html(`<option value="${data.data.sh_number_id}" selected>${data.data.sh_number.kode}</option>`);
                                        let sh_number_details = data.data.sh_number.sh_number_details;

                                        sh_number_details.map((data, index) => {
                                            if (data.type == 'Supply Point') {
                                                $('#supply_point').val(data.alamat);
                                            }
                                            if (data.type == 'Drop Point') {
                                                $('#drop_point').val(data.alamat);
                                            }
                                        });
                                        updateCurrentPrice();

                                        let purchase_request_trading_detail = data.data.purchase_request_trading_details[0];
                                        let outstanding_qty = parseFloat(purchase_request_trading_detail.qty - purchase_request_trading_detail.ordered_qty);

                                        if (data.data.id == model_data.purchase_request_trading_id) {
                                            outstanding_qty += parseFloat(po_trading_detail.jumlah);
                                        }
                                        $('#item_id').append(`<option value="${purchase_request_trading_detail.item.id}">${purchase_request_trading_detail.item.kode} - ${purchase_request_trading_detail.item.nama}</option>`);
                                        $('#item_id').trigger('change');
                                        $('#jumlah').val(formatRupiahWithDecimal(outstanding_qty)).trigger('keyup');
                                        $('#outstanding_qty').val(outstanding_qty);
                                    }
                                });
                            });

                        } else {
                            initSelect2SearchPaginationData(`customer_id`, `{{ route('admin.select.customer') }}`, {
                                id: 'id',
                                text: 'nama'
                            })

                            initSelect2SearchPaginationData(`sh_number_id`, `{{ route('admin.select.sh-number.customer') }}/${customer.id}`, {
                                id: 'id',
                                text: "kode,supply_point,drop_point"
                            })

                            $('#customer_id').change(function(e) {
                                updateCurrentPrice();

                                $('#sh_number_id').select2();
                                $('#sh_number_id').html('');
                                $('#supply_point').html('');
                                $('#drop_point').html('');

                                initSelect2SearchPaginationData(`sh_number_id`, `{{ route('admin.select.sh-number.customer') }}/${this.value}`, {
                                    id: 'id',
                                    text: "kode,supply_point,drop_point"
                                })
                            });

                            $('#sh_number_id').change(function(e) {
                                updateCurrentPrice();

                                $.ajax({
                                    type: "get",
                                    url: "{{ route('admin.sh-number.detail') }}/" + $(this).val(),
                                    success: function({
                                        data
                                    }) {
                                        let {
                                            sh_number_details
                                        } = data;

                                        sh_number_details.map((data, index) => {
                                            if (data.type == 'Supply Point') {
                                                $('#supply_point').val(data.alamat);
                                            }
                                            if (data.type == 'Drop Point') {
                                                $('#drop_point').val(data.alamat);
                                            }
                                        });

                                        updateCurrentPrice();
                                    }
                                });
                            });
                        }

                        initSelect2SearchPaginationData(`purchase_request_trading_id`, `{{ route('admin.select.purchase-request-trading') }}`, {
                            id: 'id',
                            text: 'code,customer_name'
                        }, 0, {
                            purchase_request_trading_id: function(data) {
                                return model_data.purchase_request_trading_id
                            }
                        });

                        $('#purchase_request_trading_id').change(function(e) {
                            $('#customer_id').val('').html('');
                            $('#sh_number_id').val('').html('');
                            $('#supply_point').val('');
                            $('#drop_point').val('');
                            $('#item_id').val('').html('');
                            $('#jumlah').val(0);
                            $('#outstanding_qty').val(0);
                            $('#customer_id').attr('disabled', false);
                            $('#sh_number_id').attr('disabled', false);
                            $('#item_id').attr('disabled', false);
                            $('#pr-remaining').addClass('d-none').text('');

                            if ($(this).val() == '' || $(this).val() == null) {
                                return false;
                            }

                            $.ajax({
                                url: base_url + "/purchase-request-trading/" + $(this).val(),
                                type: "GET",
                                success: function(data) {
                                    $('#customer_id').attr('disabled', true);
                                    $('#sh_number_id').attr('disabled', true);
                                    $('#item_id').attr('disabled', true);
                                    $('#customer_id').html(`<option value="${data.data.customer_id}" selected>${data.data.customer.nama}</option>`);
                                    $('#sh_number_id').html(`<option value="${data.data.sh_number_id}" selected>${data.data.sh_number.kode}</option>`);
                                    let sh_number_details = data.data.sh_number.sh_number_details;

                                    sh_number_details.map((data, index) => {
                                        if (data.type == 'Supply Point') {
                                            $('#supply_point').val(data.alamat);
                                        }
                                        if (data.type == 'Drop Point') {
                                            $('#drop_point').val(data.alamat);
                                        }
                                    });
                                    updateCurrentPrice();

                                    let purchase_request_trading_detail = data.data.purchase_request_trading_details[0];
                                    let outstanding_qty = parseFloat(purchase_request_trading_detail.qty - purchase_request_trading_detail.ordered_qty);

                                    if (data.data.id == model_data.purchase_request_trading_id) {
                                        outstanding_qty += parseFloat(po_trading_detail.jumlah);
                                    }
                                    $('#item_id').append(`<option value="${purchase_request_trading_detail.item.id}">${purchase_request_trading_detail.item.kode} - ${purchase_request_trading_detail.item.nama}</option>`);
                                    $('#item_id').trigger('change');
                                    $('#jumlah').val(formatRupiahWithDecimal(outstanding_qty)).trigger('keyup');
                                    $('#outstanding_qty').val(outstanding_qty);
                                    $('#pr-remaining').removeClass('d-none').text(`Sisa PR: ${formatRupiahWithDecimal(outstanding_qty)}`);
                                }
                            });
                        });

                    }

                    $('#sale_order_id').change(function(e) {
                        $('#customer_id').val('').html('');
                        $('#sh_number_id').val('').html('');
                        $('#supply_point').val('');
                        $('#drop_point').val('');
                        $('#item_id').val('').html('');
                        $('#jumlah').val(0);
                        $('#outstanding_qty').val(0);
                        $('#customer_id').attr('disabled', false);
                        $('#sh_number_id').attr('disabled', false);
                        $('#item_id').attr('disabled', false);
                        $('#pr-remaining').addClass('d-none').text('');

                        if ($(this).val() == '' || $(this).val() == null) {

                            return false;
                        }
                        $.ajax({
                            url: base_url + "/sales-order/" + $(this).val(),
                            type: "GET",
                            success: function(data) {
                                data = data.data.model;

                                $('#customer_id').attr('disabled', true);
                                $('#customer_id').html(`<option value="${data.customer_id}" selected>${data.customer.nama}</option>`);
                                $('#sh_number_id').attr('disabled', false);
                                $('#sh_number_id').html(`<option value="${data.sh_number_id}" selected>${data.sh_number.kode}</option>`);
                                let sh_number_details = data.sh_number.sh_number_details;
                                sh_number_details.map((data, index) => {
                                    if (data.type == 'Supply Point') {
                                        $('#supply_point').val(data.alamat);
                                    }
                                    if (data.type == 'Drop Point') {
                                        $('#drop_point').val(data.alamat);
                                    }
                                });

                                initSelect2Search('sh_number_id', `{{ route('admin.select.customer.sh-numbers') }}/${$('#customer_id').val()}`, {
                                    id: "id",
                                    text: "kode,supply_point,drop_point"
                                });

                                $('#item_id').attr('disabled', true);
                                $('#item_id').append(`<option value="${data.so_trading_detail.item.id}">${data.so_trading_detail.item.kode} - ${data.so_trading_detail.item.nama}</option>`);
                                $('#item_id').trigger('change');
                                $('#jumlah').val(formatRupiahWithDecimal(data.so_trading_detail.jumlah)).trigger('keyup');
                                updateCurrentPrice();
                            }
                        });
                    });


                    displayCustomer();

                    const displayCurrency = () => {
                        let {
                            id,
                            nama,
                            simbol,
                            is_local
                        } = currency;

                        $('#row-currency').append(`
                            <div class="row mt-30">
                                <div class="col-md-4">
                                    <x-select name="currency_id" id="currency_id_trading" label="Currency" required>
                                        <option value="${id}" selected>${nama}</option>
                                    </x-select>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input type="text" name="exchange_rate" class="commas-form" label="kurs" id="exchange_rate_trading" value="${numberWithDot(decimalFormatterWithOuNumberWithCommas(exchange_rate))}" required />
                                    </div>
                                </div>
                            </div>
                        `);

                        currency_symbol = simbol;

                        if (is_local) {
                            $('#exchange_rate_trading').attr('readonly', true);
                        }

                        initSelect2SearchPaginationData(`currency_id_trading`, `{{ route('admin.select.currency') }}`, {
                            id: 'id',
                            text: 'kode,nama,negara'
                        })

                        $('#currency_id_trading').change(function(e) {
                            $.ajax({
                                type: "get",
                                url: `{{ route('admin.currency.detail') }}/${this.value}`,
                                success: function({
                                    data
                                }) {
                                    if (data.is_local) {
                                        $('#exchange_rate_trading').val(1);
                                        $('#exchange_rate_trading').attr('readonly', 'readonly');
                                    } else {
                                        $('#exchange_rate_trading').removeAttr('readonly');
                                        $('#exchange_rate_trading').attr('readonly', false);
                                    }

                                    currency_symbol = data.simbol;

                                    updateCurrencySymbol();
                                }
                            });
                        });
                    }
                    displayCurrency();

                    const displayMainItem = (sale_confirmation) => {
                        let {
                            item,
                            jumlah,
                            discount_per_liter,
                            harga,
                            type,
                            keterangan,
                        } = po_trading_detail;

                        let formatted_harga = formatRupiahWithDecimal(harga);
                        let formatted_jumlah = formatRupiahWithDecimal(jumlah);
                        let formatted_discount = formatRupiahWithDecimal(discount_per_liter);
                        let purchase_request_trading_detail = model_data.purchase_request_trading?.purchase_request_trading_details[0];
                        let outstanding_qty = jumlah;
                        if (purchase_request_trading_detail) {
                            outstanding_qty = parseFloat(purchase_request_trading_detail.qty) - parseFloat(purchase_request_trading_detail.ordered_qty) + parseFloat(jumlah);
                        }


                        $('#main-item').append(`
                            <div  id="item-1">

                                <div class="row">
                                    <div class="col-md-3">
                                        <x-select name="item_id" id="item_id" label="item" value="" required>
                                            <option value="${item.id}" selected>${item.kode} ${item.nama}</option>
                                        </x-select>
                                        <div class="unit-info mb-2"><span class="text-primary">${item.unit.name}</span></div>
                                        <input type="hidden" name="type" id="type" value="Liter">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="text" id="harga_trading" name="harga" class="commas-form" value="${formatted_harga}" label="harga" required />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="text" id="jumlah" name="jumlah" class="commas-form" label="jumlah" value="${formatted_jumlah}" required />
                                            <small class="text-secondary d-none" id="pr-remaining">Sisa PR : ${formatRupiahWithDecimal(outstanding_qty)}</small>
                                            <input type="hidden" id="outstanding_qty" value="${outstanding_qty}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="text" id="discount_trading" name="discount" class="commas-form" label="Diskon" helpers="Rp" value="${formatted_discount}" required />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <x-input type="text" class="commas-form" label="DPP" name="dpp_trading" id="dppTrading" value="" readonly/>
                                    </div>
                                    <div class="col-md-3">
                                        <x-select name="tax_id_trading[]" id="tax_id_trading" label="pajak sebelum diskon" multiple>

                                        </x-select>
                                        <button type="button" id="clearTaxTrading" class="btn btn-sm btn-danger mb-4">Hapus Semua Tax</button>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-select name="ppn_id" id="ppn_id" label="pajak setelah diskon">

                                            </x-select>
                                        </div>
                                    </div>
                                </div>
                            </div>`);

                        $('#harga_trading').mask('#.##0,00', {reverse: true});

                        initSelect2SearchPaginationData(`item_id`, `{{ route('admin.select.item.type') }}/trading`, {
                            id: 'id',
                            text: 'nama,kode'
                        })

                        $('#item_id').change(function(e) {
                            let value = $(this).val();
                            let item = $(this).select2('data');

                            if (value) {
                                $('#trading-item-name').html(item[0].text);
                                $.ajax({
                                    type: "get",
                                    url: `${base_url}/item/${this.value}`,
                                    success: function({
                                        data
                                    }) {
                                        $('.unit-info').html(`<span class="text-primary">${data.unit.name}</span>`);
                                    }
                                });

                            } else {
                                $('#trading-item-name').html('-');
                            }
                        });

                        initCommasForm();

                        if (model_data.purchase_request_trading_id) {
                            $('#customer_id').attr('disabled', true);
                            $('#sh_number_id').attr('disabled', true);
                            $('#item_id').attr('disabled', true);
                            $('#pr-remaining').removeClass('d-none');
                        }

                        const displayQtyMain = () => {
                            let jumlah = formatThousandToFloat($('#jumlah').val() || 0);
                            let type = $('#type').val();

                            if (type == 'Kilo Liter') {
                                jumlah = jumlah * 1000;
                            }

                            $('#trading-item-total-liter').html(formatRupiahWithDecimal(jumlah));
                        }

                        const displayPriceMain = () => {
                            let harga = formatThousandToFloat($('#harga_trading').val() ?? 0);
                            $('#display-harga').html(formatRupiahWithDecimal(harga));
                        }

                        const main_dpp_amount = () => {
                            let harga = formatThousandToFloat($('#harga_trading').val() ?? 0);
                            let discount = formatThousandToFloat($('#discount_trading').val() || 0);
                            let dpp = harga - discount;
                            return parseFloat(dpp);
                        }

                        const displayDppMain = () => {
                            let dpp = main_dpp_amount();
                            $('#dppTrading').val(formatRupiahWithDecimal(dpp));
                            $('#display-harga').html(formatRupiahWithDecimal(dpp));
                        }

                        const calculatePpnMain = () => {
                            let type = $('#type').val();
                            let jumlah = formatThousandToFloat($('#jumlah').val() || 0);
                            let dpp = main_dpp_amount();

                            if (type == 'Kilo Liter') {
                                jumlah = jumlah * 1000;
                            }

                            if ($('#ppn_id').val() != null && $('#ppn_id').val() != '') {
                                $.ajax({
                                    type: "get",
                                    url: "{{ route('admin.tax.detail') }}/" + $('#ppn_id').val(),
                                    success: ({
                                        data
                                    }) => {
                                        $('#main_ppn_name').html(`${data.name} - ${data.value * 100}%`);
                                        $('#main_ppn_value').html(`${dpp == 0 ? '0,00' : formatRupiahWithDecimal((dpp * data.value) * jumlah)}`);
                                        main_ppn = (dpp * data.value) * jumlah;

                                        calculateTotalMain();
                                        calculateGrandTotal();
                                    }
                                });
                            } else {
                                $('#main_ppn_name').html('PPN');
                                $('#main_ppn_value').html(`0,00`);
                                main_ppn = 0;

                            }
                        }

                        const calculateTaxMain = () => {
                            main_tax_total = [];

                            let type = $('#type').val();
                            let harga = formatThousandToFloat($('#harga_trading').val() ?? 0);
                            let jumlah = formatThousandToFloat($('#jumlah').val() || 0);

                            if (type == 'Kilo Liter') {
                                jumlah = jumlah * 1000;
                            }

                            let dpp = main_dpp_amount();

                            $('#trading-item-table tbody').children('tr:not(:nth-child(-n+2))').remove();

                            let taxes = $('#tax_id_trading').val();
                            if (taxes.length !== 0) {
                                $('#trading-item-tax').html('');
                                $('#trading-item-tax-value').html('');

                                let tax_total_amount = 0;
                                $.each(taxes, function(index, value) {
                                    $.ajax({
                                        type: "get",
                                        url: "{{ route('admin.tax.detail') }}/" + value,
                                        success: ({
                                            data
                                        }) => {
                                            let html = `
                                                <tr>
                                                    <td colspan="3">
                                                        <p class="text-end mb-0">${data.name} - ${data.value * 100}%</p>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex text-end">
                                                            <p class="mb-0" id="currency-simbol">${currency_symbol}</p>
                                                            <h5 class="ms-auto mb-0">${formatRupiahWithDecimal((harga * data.value) * jumlah)}</h5>
                                                        </div>
                                                    </td>
                                                </tr>
                                            `;

                                            $('#trading-item-table tbody').append(html);

                                            updateCurrencySymbol();

                                            main_tax_total[index] = (harga * data.value) * jumlah;

                                            calculateSubTotalMain();
                                            calculateTotalMain();
                                            calculateGrandTotal();
                                        }
                                    });
                                });
                            } else {
                                calculateSubTotalMain();
                                calculateTotalMain();
                                calculateGrandTotal();
                            }
                        }

                        const calculateTotalMain = () => {
                            let sub_total = $('#trading-item-sub-total').attr('data-value');
                            let tax_total = main_tax_total.reduce((a, b) => a + b, 0);
                            let total = parseFloat(sub_total) + parseFloat(main_ppn) + parseFloat(tax_total);

                            $('#main-total').html(formatRupiahWithDecimal(total));
                            $('#main-total').attr('data-value', total);
                        }

                        const calculateSubTotalMain = () => {
                            let type = $('#type').val();
                            let jumlah = formatThousandToFloat($('#jumlah').val() || 0);
                            let dpp = main_dpp_amount();

                            if (type == 'Kilo Liter') {
                                jumlah = jumlah * 1000;
                            }


                            $('#trading-item-sub-total').html(formatRupiahWithDecimal(jumlah * dpp));
                            $('#trading-item-sub-total').attr('data-value', jumlah * dpp);
                        }

                        // display table
                        $('#trading-item-name').html(`${item.kode} ${item.nama}`);
                        $('#display-harga').html(`${formatted_harga}`);
                        $('#trading-item-total-liter').html(`${formatRupiahWithDecimal(jumlah)}`);

                        initCommasForm();
                        initCommasForm();

                        initSelect2SearchPaginationData(`general-division`, `{{ route('admin.select.item.type') }}/trading`, {
                            id: 'id',
                            text: 'nama,kode'
                        })

                        const initTaxMainPpn = () => {
                            let target_value = $(`#ppn_id`).val();

                            let taxSelector = {
                                id: "id",
                                text: "name"
                            };

                            let selectTaxOptsPpn = {
                                placeholder: "Pilih Data",
                                minimumInputLength: 0,
                                allowClear: true,
                                width: "100%",
                                language: {
                                    noResults: () => {
                                        return "Data can't be found";
                                    },
                                },
                                ajax: {
                                    url: "{{ route('admin.select.tax-post') }}",
                                    dataType: "json",
                                    delay: 250,
                                    type: "post",
                                    data: ({
                                        term
                                    }) => {
                                        let selected_tax = [];
                                        $('#tax_id_trading').val().map((data, index) => {
                                            selected_tax.push(data);
                                        });

                                        let result = {};
                                        result["search"] = term;
                                        result["selected_item"] = selected_tax;
                                        result[`ppn_id`] = target_value;
                                        result["_token"] = token

                                        return result;
                                    },
                                    processResults: ({
                                        data
                                    }) => {
                                        let final_data = data.map((data, key) => {
                                            let return_text = "";
                                            let split_text = taxSelector.text.split(",");
                                            $.each(split_text, function(index, value) {
                                                if (index != 0) {
                                                    return_text += ` - ${data[value]}`;
                                                } else {
                                                    return_text += data[value];
                                                }
                                            });
                                            return {
                                                id: data[taxSelector.id],
                                                text: return_text,
                                            };
                                        });
                                        return {
                                            results: final_data,
                                        };
                                    },
                                    cache: true,
                                },
                            };

                            $('#ppn_id').select2(selectTaxOptsPpn);
                        }
                        initTaxMainPpn();

                        $('#type').change(function() {
                            displayQtyMain();
                            displayDppMain();
                            calculateSubTotalMain();
                            calculatePpnMain();
                            calculateTaxMain();
                            calculateTotalMain();
                            calculateGrandTotal();
                        });

                        $('#jumlah').keyup(debounce(function() {
                            if (formatThousandToFloat($(this).val()) > formatThousandToFloat($('#outstanding_qty').val()) && $('#purchase_request_trading_id').val() != null && $('#purchase_request_trading_id').val() != '') {
                                alert('Jumlah melebihi purchase request');
                                $(this).val(formatThousandToFloat($('#outstanding_qty').val()));
                            }

                            displayQtyMain();
                            calculateSubTotalMain();
                            calculateTotalMain();
                            calculateGrandTotal();
                        }, 750));

                        $('#harga_trading').keyup(debounce(function() {
                            displayPriceMain();
                            displayDppMain();
                            calculateSubTotalMain();
                            calculatePpnMain();
                            calculateTaxMain();
                            calculateTotalMain();
                            calculateGrandTotal();
                        }, 750));

                        $('#discount_trading').keyup(debounce(function() {
                            displayDppMain();
                            calculatePpnMain();
                            calculateTaxMain();
                            calculateTotalMain();
                            calculateGrandTotal();
                        }, 750));
                        $('#discount_trading').trigger('keyup');

                        const displayTradingTax = () => {
                            $('#trading-item-table tbody').children('tr:not(:nth-child(-n+2))').remove();

                            purchase_order_taxes.map((data, index) => {
                                let {
                                    id,
                                    tax_id,
                                    total,
                                    tax_trading_id,
                                    tax,
                                    tax_trading,
                                } = data

                                if (!tax_trading_id) {
                                    // display form
                                    $('#tax_id_trading').append(`
                                        <option value="${tax_id}" selected>${tax.name}</option>
                                    `);

                                    // display table
                                    $('#trading-item-tax').append(`
                                        <p class="mb-0">${tax.name} - ${tax.value * 100}%</p>
                                    `);
                                } else {
                                    ppn['id'] = tax_trading.id;
                                    ppn['name'] = tax_trading.name;
                                    ppn['value'] = tax_trading.value;

                                    $('#ppn_id').append(`<option value="${tax_trading.id}" selected>${tax_trading.name} - ${tax_trading.value * 100}%</option>`);

                                    // display table
                                    $('#trading-item-tax-value').append(`
                                        <div class="d-flex">
                                            <p class="me-10 mb-0" id="currency-simbol">${currency_symbol}</p>
                                            <p class="mb-0">${formatRupiahWithDecimal(total)}</p>
                                        </div>
                                    `);
                                }
                            });

                            let type = $('#type').val();
                            let jumlah = formatThousandToFloat($('#jumlah').val() || 0);
                            let dpp = main_dpp_amount();

                            if (type == 'Kilo Liter') {
                                jumlah = jumlah * 1000;
                            }

                            if (ppn.id == undefined) {
                                $('#isPPN').trigger('click');
                                $('#ppn_id').val(null);
                                $('#main_ppn_value').html('0,00');
                            } else {
                                $('#isPPN').trigger('click');
                                $('#ppn_id').val(ppn.id);
                                $('#main_ppn_name').html(`${ppn.name} - ${ppn.value * 100}%`);
                                $('#main_ppn_value').html(`${dpp == 0 ? '0,00' : formatRupiahWithDecimal((dpp * ppn.value) * jumlah)}`);

                                main_ppn = (dpp * ppn.value) * jumlah;
                            }

                            // tax
                            let target_value = $(`#tax_id_trading`).val();

                            let taxSelector = {
                                id: "id",
                                text: "name"
                            };

                            let selectTaxOpts = {
                                placeholder: "Pilih Data",
                                minimumInputLength: 0,
                                allowClear: true,
                                width: "100%",
                                language: {
                                    noResults: () => {
                                        return "Data can't be found";
                                    },
                                },
                                ajax: {
                                    url: "{{ route('admin.select.tax-post') }}",
                                    dataType: "json",
                                    delay: 250,
                                    type: "post",
                                    data: ({
                                        term
                                    }) => {
                                        let selected_tax = [];
                                        if ($('#ppn_id').val() != null && $('#ppn_id').val() != '') {
                                            selected_tax.push($('#ppn_id').val());
                                        }

                                        let result = {};
                                        result["_token"] = token;
                                        result["search"] = term;
                                        result["selected_item"] = selected_tax;
                                        result[`tax_id_trading`] = target_value;

                                        return result;
                                    },
                                    processResults: ({
                                        data
                                    }) => {
                                        let final_data = data.map((data, key) => {
                                            let return_text = "";
                                            let split_text = taxSelector.text.split(",");
                                            $.each(split_text, function(index, value) {
                                                if (index != 0) {
                                                    return_text += ` - ${data[value]}`;
                                                } else {
                                                    return_text += data[value];
                                                }
                                            });
                                            return {
                                                id: data[taxSelector.id],
                                                text: return_text,
                                            };
                                        });
                                        return {
                                            results: final_data,
                                        };
                                    },
                                    cache: true,
                                },
                            };

                            $(`#tax_id_trading`).select2(selectTaxOpts);

                            $('#tax_id_trading').on('change', function(e) {
                                calculateTaxMain();
                            });

                            $('#clearTaxTrading').click(function(e) {
                                $('#tax_id_trading').val(null).trigger('change');
                            });

                            calculateTaxMain();
                        };
                        displayTradingTax();

                        $('#ppn_id').change(function(e) {
                            calculatePpnMain();
                            calculateTotalMain();
                            calculateGrandTotal();
                        });
                    }
                    displayMainItem(sale_confirmation);

                    const addAdditional = (item_index) => {
                        if (item_index == 0) {
                            btn = `
                                <div class="col-md-1 row align-items-end">
                                    <div class="form-group">
                                        <x-button type="button" color="info" icon="plus" fontawesome size="sm" id="add-additional-item" />
                                    </div>
                                </div>`;
                        } else {
                            btn = `
                                <div class="col-md-1 row align-items-end">
                                    <div class="form-group">
                                        <x-button type="button" color="danger" icon="x" fontawesome size="sm" id="delete-additional-item-${item_index}" />
                                    </div>
                                </div>`;
                        }
                        let html = `
                            <div class="row mt-10" id="additional-item-${item_index}">
                                <input type="hidden" name="additional_item_row_index[]" value="${item_index}" />
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-select name="item_type[]" id="additional-type-${item_index}" label="Type">
                                            <option value="">Pilih Item</option>
                                            <option value="service">Service</option>
                                            <option value="transport">Transport</option>
                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <x-select name="additional_item_id[]" id="additional-item-id-${item_index}" label="item" disabled></x-select>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input type="text" name="additional_price[]" label="harga" id="additional-harga-${item_index}" class="commas-form" disabled/>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input type="text" name="additional_qty[]" label="jumlah" id="additional-jumlah-${item_index}" class="commas-form" disabled/>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <x-select name="additional_tax_id_${item_index}[]" id="additional-tax-id-${item_index}" label="Tax" multiple disabled></x-select>
                                    <button type="button" id="clearTaxAdditional${item_index}" class="btn btn-sm btn-danger mb-4">Hapus Semua Tax</button>
                                </div>
                                ${btn}
                            </div>
                        `;

                        $('#additional-item').append(html);

                        $('#additional-item-table tbody').append(`
                            <tr id="additional-resume-${item_index}">
                                <td>
                                    <span id="additional-item-name-${item_index}">-</span
                                </td>
                                <td>
                                    <div class="d-flex">
                                        <p id="currency-simbol" class="me-10 mb-0">${currency_symbol}</p>
                                        <h5 class="mb-0" id="additional-item-price-${item_index}">0,00</h5>
                                    </div
                                </td>
                                <td>
                                    <div class="d-flex">
                                        <h5 class="mb-0" id="additional-item-qty-${item_index}">0,00</h5>
                                    </div
                                </td>
                                <td>
                                    <div class="d-flex">
                                        <p id="currency-simbol" class="me-10 mb-0">${currency_symbol}</p>
                                        <h5 class="mb-0" id="additional-sub-total-${item_index}">0,00</h5>
                                    </div
                                </td>
                                <td id="additional_tax_data_detail_${item_index}">-</td>
                                <td id="additional_tax_value_detail_${item_index}">${currency_symbol} 0,00</td>
                                <td>
                                    <div class="d-flex text-end">
                                        <p id="currency-simbol" class="me-10 mb-0">${currency_symbol}</p>
                                        <h5 class="mb-0 ms-auto" id="additional-total-item-${item_index}">0,00</h5>
                                    </div>
                                </td>
                            </tr>
                        `);

                        $(`#delete-additional-item-${item_index}`).click(function() {
                            additional_dpp_list.splice(item_index, 1);
                            additional_tax_list.splice(item_index, 1);
                            additional_tax_total_list.splice(item_index, 1);

                            calculateDppAdditional();
                            calculateTaxTotalAdditional();
                            calculateTotalAdditional();
                            calculateGrandTotal();

                            removeAdditional(item_index);
                        });

                        const calculateDppAdditional = () => {
                            let additional_dpp = 0;

                            additional_dpp_list.map((data, index) => {
                                additional_dpp += data;
                            });

                            $('#additional-dpp-total').html(formatRupiahWithDecimal(additional_dpp));
                            $('#additional-dpp-total').attr('data-value', additional_dpp);
                        }

                        const calculateTaxTotalAdditional = () => {
                            let tax_total = 0;

                            additional_tax_total_list.map((data, index) => {
                                tax_total += data;
                            });
                            $('#additional-tax-total').html(formatRupiahWithDecimal(tax_total));
                            $('#additional-tax-total').attr('data-value', tax_total);
                        }

                        const calculateTotalAdditional = () => {
                            let dpp = $('#additional-dpp-total').attr('data-value');
                            let tax = $('#additional-tax-total').attr('data-value');
                            let total = parseFloat(dpp) + parseFloat(tax);

                            $('#additional-total').html(formatRupiahWithDecimal(total));
                            $('#additional-total').attr('data-value', total);
                            $('#additional-grand-total').html(formatRupiahWithDecimal(total));
                        };

                        const calculateTaxAdditional = () => {
                            additional_tax_list = [];

                            let qty = $(`#additional-jumlah-${item_index}`).val() || 0;
                            qty = formatThousandToFloat(qty);
                            let dpp = $(`#additional-harga-${item_index}`).val() || 0;
                            dpp = formatThousandToFloat(dpp);
                            let tax_list = $(`#additional-tax-id-${item_index}`).val();

                            $(`#additional_tax_data_detail_${item_index}`).html('');
                            $(`#additional_tax_value_detail_${item_index}`).html('');

                            if (tax_list.length > 0) {
                                additional_tax_list[item_index] = [];

                                let tax_total_amount = 0;

                                $.each(tax_list, function(tax_index, value) {
                                    $.ajax({
                                        type: "get",
                                        url: "{{ route('admin.tax.detail') }}/" + value,
                                        success: ({
                                            data
                                        }) => {
                                            $(`#additional_tax_data_detail_${item_index}`).append(`
                                                <p class="mb-0">${data.name} - ${data.value * 100}%</p>
                                            `);

                                            $(`#additional_tax_value_detail_${item_index}`).append(`
                                                <div class="d-flex">
                                                    <p class="me-10 mb-0" id="currency-simbol">${currency_symbol}</p>
                                                    <p class="mb-0">${formatRupiahWithDecimal(dpp * qty * data.value)}</p>
                                                </div>
                                            `);

                                            tax_total_amount += dpp * qty * data.value;

                                            updateCurrencySymbol();

                                            additional_tax_list[item_index][tax_index] = {
                                                detail_id: value,
                                                tax_id: data.id,
                                                name: data.name,
                                                value: data.value
                                            };

                                            additional_tax_total_list[item_index] = tax_total_amount;
                                            $(`#additional-total-item-${item_index}`).html(formatRupiahWithDecimal(dpp * qty + tax_total_amount));
                                            calculateDppAdditional();
                                            calculateTaxTotalAdditional();
                                            calculateTotalAdditional();
                                            calculateGrandTotal();
                                        }
                                    });
                                });
                            } else {
                                $(`#additional_tax_data_detail_${item_index}`).html('-');
                                $(`#additional_tax_value_detail_${item_index}`).html('-');
                                $(`#additional-total-item-${item_index}`).html(formatRupiahWithDecimal(dpp * qty));

                                calculateDppAdditional();
                                calculateTaxTotalAdditional();
                                calculateTotalAdditional();
                                calculateGrandTotal();
                            }
                        };
                        calculateTaxAdditional();

                        const removeAdditional = (index) => {
                            $(`#additional-item-${index}`).remove();
                            $(`#additional-resume-${index}`).remove();
                        }

                        $(`#additional-type-${item_index}`).select2();
                        $(`#additional-type-${item_index}`).change(function() {
                            additional_dpp_list[item_index] = 0;
                            additional_tax_list[item_index] = [];
                            additional_tax_total_list[item_index] = 0;

                            $(`#additional-item-id-${item_index}`).removeAttr('disabled');
                            $(`#additional-tax-id-${item_index}`).removeAttr('disabled');
                            $(`#additional-harga-${item_index}`).removeAttr('disabled');
                            $(`#additional-jumlah-${item_index}`).removeAttr('disabled');

                            $(`#additional-item-name-${item_index}`).html('-');
                            $(`#additional-item-price-${item_index}`).html('0,00');
                            $(`#additional-sub-total-${item_index}`).html('0,00');
                            $(`#additional_tax_data_detail_${item_index}`).html('-');
                            $(`#additional_tax_value_detail_${item_index}`).html(`${currency_symbol} 0,00`);
                            $(`#additional-total-item-${item_index}`).html('0,00');

                            $(`#additional-jumlah-${item_index}`).val(null).trigger('change');
                            $(`#additional-harga-${item_index}`).val(null).trigger('change');
                            $(`#additional-item-id-${item_index}`).val(null).trigger('change');

                            if (this.value) {
                                inititemSelect(`additional-item-id-${item_index}`, $(`#additional-type-${item_index}`).val());

                                $(`#additional-item-id-${item_index}`).change(function(e) {
                                    let value = $(this).val();
                                    if (value) {
                                        $(`#additional-item-name-${item_index}`).html($(`#additional-item-id-${item_index}`).select2('data')[0].text);
                                        $.ajax({
                                            type: "get",
                                            url: `{{ route('admin.item.price-latest') }}/${this.value}`,
                                            success: function({
                                                data
                                            }) {
                                                $(`#additional-harga-${item_index}`).val(formatRupiahWithDecimal(numberWithDot(decimalFormatterWithOuNumberWithCommas(data.harga_jual ?? 0))));
                                                $(`#additional-item-price-${item_index}`).html(formatRupiahWithDecimal(numberWithDot(decimalFormatterWithOuNumberWithCommas(data.harga_jual ?? 0))));
                                                $(`#additional-sub-total-${item_index}`).html(formatRupiahWithDecimal(numberWithDot(decimalFormatterWithOuNumberWithCommas(data.harga_jual ?? 0))));
                                                $(`#additional-total-item-${item_index}`).html(formatRupiahWithDecimal(numberWithDot(decimalFormatterWithOuNumberWithCommas(data.harga_jual ?? 0))));

                                                additional_dpp_list[item_index] = parseFloat(data.harga_jual) ?? 0;

                                                calculateTaxAdditional();
                                            }
                                        });
                                    } else {
                                        $(`#additional-item-name-${item_index}`).html('-');
                                    }
                                });
                            } else {
                                $(`#additional-item-id-${item_index}`).attr('disabled', 'disabled');
                                $(`#additional-tax-id-${item_index}`).val(null).trigger('change');
                                $(`#additional-tax-id-${item_index}`).attr('disabled', 'disabled');
                                $(`#additional-harga-${item_index}`).attr('disabled', 'disabled');
                                $(`#additional-jumlah-${item_index}`).attr('disabled', 'disabled');
                            }

                            calculateTaxAdditional();
                        });

                        // initSelect2SearchPaginationData(`additional-item-id-${item_index}`, `{{ route('admin.select.item-type') }}/${$(`#additional-type-${item_index}`).val()}`, {
                        //     id: 'id',
                        //     text: 'nama,kode'
                        // })

                        inititemSelect(`additional-item-id-${item_index}`, $(`#additional-type-${item_index}`).val());

                        initCommasForm();

                        $(`#additional-harga-${item_index}`).keyup(debounce(function() {
                            let value = formatThousandToFloat($(this).val() || 0);
                            let qty = formatThousandToFloat($(`#additional-jumlah-${item_index}`).val() || 0);

                            $(`#additional-item-qty-${item_index}`).html(formatRupiahWithDecimal(qty));
                            $(`#additional-item-price-${item_index}`).html(formatRupiahWithDecimal(value));
                            $(`#additional-sub-total-${item_index}`).html(formatRupiahWithDecimal(value * qty));

                            additional_dpp_list[item_index] = value * qty;

                            calculateTaxAdditional();
                        }, 750));

                        $(`#additional-jumlah-${item_index}`).keyup(debounce(function() {
                            let qty = formatThousandToFloat($(this).val() || 0);
                            let value = formatThousandToFloat($(`#additional-harga-${item_index}`).val() || 0);

                            $(`#additional-item-qty-${item_index}`).html(formatRupiahWithDecimal(qty));
                            $(`#additional-item-price-${item_index}`).html(formatRupiahWithDecimal(value));
                            $(`#additional-sub-total-${item_index}`).html(formatRupiahWithDecimal(value * qty));

                            additional_dpp_list[item_index] = value * qty;

                            calculateTaxAdditional();
                        }, 750));

                        const initAdditionalTax = () => {
                            additional_tax_list[item_index] = [];

                            let selected_tax = [];
                            $(`select[id="#additional-tax-id-${item_index}"]`)
                                .toArray()
                                .map(function() {
                                    if ($(this).val() != null) {
                                        selected_tax.push($(this).val());
                                    }
                                });

                            let target_value = $(`#additional-tax-id-${item_index}`).val();

                            let taxSelector = {
                                id: "id",
                                text: "name"
                            };

                            let selectTaxOpts = {
                                placeholder: "Pilih Data",
                                minimumInputLength: 0,
                                allowClear: true,
                                width: "100%",
                                language: {
                                    noResults: () => {
                                        return "Data can't be found";
                                    },
                                },
                                ajax: {
                                    url: "{{ route('admin.select.tax') }}",
                                    dataType: "json",
                                    delay: 250,
                                    type: "get",
                                    data: ({
                                        term
                                    }) => {
                                        let result = {};
                                        result["search"] = term;
                                        result["selected_item"] = selected_tax;
                                        result[`additional-tax-id-${item_index}`] = target_value;

                                        return result;
                                    },
                                    processResults: ({
                                        data
                                    }) => {
                                        let final_data = data.map((data, key) => {
                                            let return_text = "";
                                            let split_text = taxSelector.text.split(",");
                                            $.each(split_text, function(index, value) {
                                                if (index != 0) {
                                                    return_text += ` - ${data[value]}`;
                                                } else {
                                                    return_text += data[value];
                                                }
                                            });
                                            return {
                                                id: data[taxSelector.id],
                                                text: return_text,
                                            };
                                        });
                                        return {
                                            results: final_data,
                                        };
                                    },
                                    cache: true,
                                },
                            };

                            $(`#additional-tax-id-${item_index}`).select2(selectTaxOpts);

                            $(`#additional-tax-id-${item_index}`).change(function(e) {
                                calculateTaxAdditional();
                            });

                            $(`#clearTaxAdditional${item_index}`).click(function(e) {
                                additional_tax_list[item_index] = [];
                                additional_tax_total_list[item_index] = 0;
                                $(`#additional-tax-id-${item_index}`).val(null).trigger('change');
                            });
                        }
                        initAdditionalTax();
                    }

                    const displayAdditional = () => {
                        let tax_additional_list = [];

                        if (additional_data.length > 0) {
                            additional_data.map((data, index) => {
                                let {
                                    item,
                                    harga,
                                    jumlah,
                                    sub_total,
                                    purchase_order_additional_taxes,
                                } = data;

                                let formatted_harga = formatRupiahWithDecimal(harga);
                                let formatted_jumlah = formatRupiahWithDecimal(jumlah);
                                let formatted_sub_total = formatRupiahWithDecimal(sub_total);

                                additional_dpp_list[index] = sub_total;

                                let btn = '';

                                if (index == 0) {
                                    btn = `<div class="col-md-1 row align-items-end">
                                            <div class="form-group">
                                                <x-button type="button" color="info" icon="plus" fontawesome size="sm" id="add-additional-item" />
                                            </div>
                                        </div>`;
                                } else {
                                    btn = `<div class="col-md-1 row align-items-end">
                                            <div class="form-group">
                                                <x-button type="button" color="danger" icon="x" fontawesome size="sm" id="delete-additional-item-${index}" />
                                            </div>
                                        </div>`;
                                }

                                $('#additional-item').append(`
                                    <div class="row mt-10" id="additional-item-${index}">
                                        <input type="hidden" name="additional_item_row_index[]" value="${index}" />
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-select name="item_type[]" id="additional-type-${index}" label="Type">
                                                    <option value="">Pilih Item</option>
                                                    <option value="service" ${item.type == 'service' ? 'selected' : ''}>Service</option>
                                                    <option value="transport" ${item.type == 'transport' ? 'selected' : ''}>Transport</option>
                                                </x-select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <x-select name="additional_item_id[]" id="additional-item-id-${index}" label="item">
                                                <option value="${item.id}" selected>${item.kode} ${item.nama}</option>
                                            </x-select>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-input type="text" name="additional_price[]" label="harga" id="additional-harga-${index}" value="${formatted_harga}" class="commas-form"/>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-input type="text" name="additional_qty[]" label="jumlah" id="additional-jumlah-${index}" value="${formatted_jumlah}" class="commas-form"/>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <x-select name="additional_tax_id_${index}[]" id="additional-tax-id-${index}" label="Tax" multiple>

                                            </x-select>
                                            <button type="button" id="clearTaxAdditional${index}" class="btn btn-sm btn-danger mb-4">Hapus Semua Tax</button>
                                        </div>
                                        ${btn}
                                    </div>
                                `);

                                $('#additional-item-table tbody').append(`
                                    <tr id="additional-resume-${index}">
                                        <td>
                                            <span id="additional-item-name-${index}">${item.kode} ${item.nama}</span
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <p id="currency-simbol" class="me-10 mb-0">${currency_symbol}</p>
                                                <h5 class="mb-0" id="additional-item-price-${index}">${formatted_harga}</h5>
                                            </div
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <h5 class="mb-0" id="additional-item-qty-${index}">${formatted_jumlah}</h5>
                                            </div
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <p id="currency-simbol" class="me-10 mb-0">${currency_symbol}</p>
                                                <h5 class="mb-0" id="additional-sub-total-${index}">${formatted_sub_total}</h5>
                                            </div
                                        </td>
                                        <td id="additional_tax_data_detail_${index}"></td>
                                        <td id="additional_tax_value_detail_${index}"></td>
                                        <td>
                                            <div class="d-flex text-end">
                                                <p id="currency-simbol" class="me-10 mb-0">${currency_symbol}</p>
                                                <h5 class="mb-0 ms-auto" id="additional-total-item-${index}"></h5>
                                            </div>
                                        </td>
                                    </tr>
                                `);

                                const calculateDppAdditional = () => {
                                    let additional_dpp = 0;

                                    additional_dpp_list.map((data, index) => {
                                        additional_dpp += data;
                                    });
                                    $('#additional-dpp-total').html(formatRupiahWithDecimal(additional_dpp));
                                    $('#additional-dpp-total').attr('data-value', additional_dpp);
                                }

                                const calculateTaxTotalAdditional = () => {
                                    let tax_total = 0;

                                    additional_tax_total_list.map((data, index) => {
                                        tax_total += data;
                                    });

                                    $('#additional-tax-total').html(formatRupiahWithDecimal(tax_total));
                                    $('#additional-tax-total').attr('data-value', tax_total);
                                }

                                const calculateTotalAdditional = () => {
                                    let dpp = $('#additional-dpp-total').attr('data-value');
                                    let tax = $('#additional-tax-total').attr('data-value');
                                    let total = parseFloat(dpp) + parseFloat(tax);

                                    $('#additional-total').html(formatRupiahWithDecimal(total));
                                    $('#additional-total').attr('data-value', total);
                                    $('#additional-grand-total').html(formatRupiahWithDecimal(total));
                                };

                                const calculateTaxAdditional = () => {
                                    additional_tax_list = [];

                                    let qty = formatThousandToFloat($(`#additional-jumlah-${index}`).val() || 0);
                                    let dpp = formatThousandToFloat($(`#additional-harga-${index}`).val() || 0);
                                    let tax_list = $(`#additional-tax-id-${index}`).val();

                                    $(`#additional_tax_data_detail_${index}`).html('');
                                    $(`#additional_tax_value_detail_${index}`).html('');

                                    if (tax_list.length > 0) {
                                        additional_tax_list[index] = [];

                                        let tax_total_amount = 0;

                                        $.each(tax_list, function(tax_index, value) {
                                            $.ajax({
                                                type: "get",
                                                url: "{{ route('admin.tax.detail') }}/" + value,
                                                success: ({
                                                    data
                                                }) => {
                                                    $(`#additional_tax_data_detail_${index}`).append(`
                                                        <p class="mb-0">${data.name} - ${data.value * 100}%</p>
                                                    `);

                                                    $(`#additional_tax_value_detail_${index}`).append(`
                                                        <div class="d-flex">
                                                            <p class="me-10 mb-0" id="currency-simbol">${currency_symbol}</p>
                                                            <p class="mb-0">${formatRupiahWithDecimal(dpp * qty * data.value)}</p>
                                                        </div>
                                                    `);

                                                    tax_total_amount += dpp * qty * data.value;

                                                    updateCurrencySymbol();

                                                    additional_tax_list[index][tax_index] = {
                                                        detail_id: value,
                                                        tax_id: data.id,
                                                        name: data.name,
                                                        value: data.value
                                                    };

                                                    additional_tax_total_list[index] = tax_total_amount;
                                                    $(`#additional-total-item-${index}`).html(formatRupiahWithDecimal(dpp * qty + tax_total_amount));

                                                    calculateDppAdditional();
                                                    calculateTaxTotalAdditional();
                                                    calculateTotalAdditional();
                                                    calculateGrandTotal();
                                                }
                                            });
                                        });
                                    } else {
                                        $(`#additional_tax_data_detail_${index}`).html('-');
                                        $(`#additional_tax_value_detail_${index}`).html('-');
                                        $(`#additional-total-item-${index}`).html(formatRupiahWithDecimal(dpp * qty));

                                        calculateDppAdditional();
                                        calculateTaxTotalAdditional();
                                        calculateTotalAdditional();
                                        calculateGrandTotal();
                                    }
                                };
                                calculateTaxAdditional();

                                const removeAdditional = (index) => {
                                    $(`#additional-item-${index}`).remove();
                                    $(`#additional-resume-${index}`).remove();
                                }

                                if (index == 0) {
                                    $('#add-additional-item').click(function() {
                                        additionalIdx++;
                                        addAdditional(additionalIdx);
                                    });
                                } else {
                                    $(`#delete-additional-item-${index}`).click(function() {
                                        additional_dpp_list.splice(index, 1);
                                        additional_tax_list.splice(index, 1);
                                        additional_tax_total_list.splice(index, 1);

                                        calculateDppAdditional();
                                        calculateTaxTotalAdditional();
                                        calculateTotalAdditional();
                                        calculateGrandTotal();

                                        removeAdditional(index);
                                    });
                                }

                                $(`#additional-type-${index}`).select2();
                                $(`#additional-type-${index}`).change(function() {
                                    additional_dpp_list[index] = 0;
                                    additional_tax_list[index] = [];
                                    additional_tax_total_list[index] = 0;

                                    $(`#additional-item-id-${index}`).removeAttr('disabled');
                                    $(`#additional-tax-id-${index}`).removeAttr('disabled');
                                    $(`#additional-harga-${index}`).removeAttr('disabled');
                                    $(`#additional-jumlah-${index}`).removeAttr('disabled');

                                    $(`#additional-item-name-${index}`).html('-');
                                    $(`#additional-item-price-${index}`).html('0,00');
                                    $(`#additional-sub-total-${index}`).html('0,00');
                                    $(`#additional_tax_data_detail_${index}`).html('-');
                                    $(`#additional_tax_value_detail_${index}`).html(`${currency_symbol} 0,00`);
                                    $(`#additional-total-item-${index}`).html('0,00');

                                    $(`#additional-harga-${index}`).val(null).trigger('change');
                                    $(`#additional-jumlah-${index}`).val(null).trigger('change');
                                    $(`#additional-item-id-${index}`).val(null).trigger('change');

                                    if (this.value) {
                                        inititemSelect(`additional-item-id-${index}`, this.value);

                                        $(`#additional-item-id-${index}`).change(function(e) {
                                            let value = $(this).val();
                                            if (value) {
                                                $(`#additional-item-name-${index}`).html($(`#additional-item-id-${index}`).select2('data')[0].text);
                                                $.ajax({
                                                    type: "get",
                                                    url: `{{ route('admin.item.price-latest') }}/${this.value}`,
                                                    success: function({
                                                        data
                                                    }) {
                                                        $(`#additional-harga-${index}`).val(formatRupiahWithDecimal(numberWithDot(decimalFormatterWithOuNumberWithCommas(data.harga_jual ?? 0))));
                                                        $(`#additional-item-price-${index}`).html(formatRupiahWithDecimal(numberWithDot(decimalFormatterWithOuNumberWithCommas(data.harga_jual ?? 0))));
                                                        $(`#additional-sub-total-${index}`).html(formatRupiahWithDecimal(numberWithDot(decimalFormatterWithOuNumberWithCommas(data.harga_jual ?? 0))));
                                                        $(`#additional-total-item-${index}`).html(formatRupiahWithDecimal(numberWithDot(decimalFormatterWithOuNumberWithCommas(data.harga_jual ?? 0))));

                                                        additional_dpp_list[index] = parseFloat(data.harga_jual) ?? 0;

                                                        calculateTaxAdditional();
                                                    }
                                                });
                                            } else {
                                                $(`#additional-item-name-${index}`).html('-');
                                            }
                                        });
                                    } else {
                                        $(`#additional-item-id-${index}`).attr('disabled', 'disabled');
                                        $(`#additional-tax-id-${index}`).val(null).trigger('change');
                                        $(`#additional-tax-id-${index}`).attr('disabled', 'disabled');
                                        $(`#additional-harga-${index}`).attr('disabled', 'disabled');
                                    }

                                    calculateTaxAdditional();
                                });

                                inititemSelect(`additional-item-id-${index}`, $(`#additional-type-${index}`).val());

                                initCommasForm();

                                $(`#additional-harga-${index}`).keyup(debounce(function() {
                                    let value = formatThousandToFloat($(this).val() || 0);
                                    let qty = formatThousandToFloat($(`#additional-jumlah-${index}`).val() || 0);

                                    $(`#additional-item-qty-${index}`).html(formatRupiahWithDecimal(qty));
                                    $(`#additional-item-price-${index}`).html(formatRupiahWithDecimal(value));
                                    $(`#additional-sub-total-${index}`).html(formatRupiahWithDecimal(value * qty));

                                    additional_dpp_list[index] = value * qty;

                                    calculateTaxAdditional();
                                }, 750));

                                $(`#additional-jumlah-${index}`).keyup(debounce(function() {
                                    let qty = formatThousandToFloat($(this).val() || 0);
                                    let value = formatThousandToFloat($(`#additional-harga-${index}`).val() || 0);

                                    $(`#additional-item-qty-${index}`).html(formatRupiahWithDecimal(qty));
                                    $(`#additional-item-price-${index}`).html(formatRupiahWithDecimal(value));
                                    $(`#additional-sub-total-${index}`).html(formatRupiahWithDecimal(value * qty));

                                    additional_dpp_list[index] = value * qty;

                                    calculateTaxAdditional();
                                }, 750));

                                const initAdditionalTax = () => {
                                    additional_tax_list[index] = [];

                                    let selected_tax = [];
                                    $(`select[id="#additional-tax-id-${index}"]`)
                                        .toArray()
                                        .map(function() {
                                            if ($(this).val() != null) {
                                                selected_tax.push($(this).val());
                                            }
                                        });

                                    let target_value = $(`#additional-tax-id-${index}`).val();

                                    let taxSelector = {
                                        id: "id",
                                        text: "name"
                                    };

                                    let selectTaxOpts = {
                                        placeholder: "Pilih Data",
                                        minimumInputLength: 0,
                                        allowClear: true,
                                        width: "100%",
                                        language: {
                                            noResults: () => {
                                                return "Data can't be found";
                                            },
                                        },
                                        ajax: {
                                            url: "{{ route('admin.select.tax') }}",
                                            dataType: "json",
                                            delay: 250,
                                            type: "get",
                                            data: ({
                                                term
                                            }) => {
                                                let result = {};
                                                result["search"] = term;
                                                result["selected_item"] = selected_tax;
                                                result[`tax_id_trading`] = target_value;

                                                return result;
                                            },
                                            processResults: ({
                                                data
                                            }) => {
                                                let final_data = data.map((data, key) => {
                                                    let return_text = "";
                                                    let split_text = taxSelector.text.split(",");
                                                    $.each(split_text, function(index, value) {
                                                        if (index != 0) {
                                                            return_text += ` - ${data[value]}`;
                                                        } else {
                                                            return_text += data[value];
                                                        }
                                                    });
                                                    return {
                                                        id: data[taxSelector.id],
                                                        text: return_text,
                                                    };
                                                });
                                                return {
                                                    results: final_data,
                                                };
                                            },
                                            cache: true,
                                        },
                                    };

                                    $(`#additional-tax-id-${index}`).select2(selectTaxOpts);

                                    $(`#additional-tax-id-${index}`).change(function(e) {
                                        calculateTaxAdditional();
                                    });

                                    $(`#clearTaxAdditional${index}`).click(function(e) {
                                        additional_tax_list[index] = [];
                                        additional_tax_total_list[index] = 0;
                                        $(`#additional-tax-id-${index}`).val(null).trigger('change');
                                    });

                                    let qty = formatThousandToFloat($(`#additional-jumlah-${index}`).val());
                                    let dpp = formatThousandToFloat($(`#additional-harga-${index}`).val());
                                    let tax_total = 0;

                                    $(`#additional_tax_data_detail_${index}`).html('');
                                    $(`#additional_tax_value_detail_${index}`).html('');

                                    purchase_order_additional_taxes.map((data, tax_index) => {
                                        $(`#additional-tax-id-${index}`).append(`
                                            <option value="${data.tax_id}" selected>${data.tax.name}</option>
                                        `);

                                        $(`#additional_tax_data_detail_${index}`).append(`
                                            <p class="mb-0">${data.tax.name} - ${data.value * 100}%</p>
                                        `);

                                        $(`#additional_tax_value_detail_${index}`).append(`
                                            <div class="d-flex">
                                                <p class="me-10 mb-0" id="currency-simbol">${currency_symbol}</p>
                                                <p class="mb-0">${formatRupiahWithDecimal(data.total)}</p>
                                            </div>
                                        `);

                                        tax_total += data.total;

                                        additional_tax_list[index][tax_index] = {
                                            id: data.tax_id,
                                            name: data.name,
                                            value: data.value
                                        };
                                    });

                                    $(`#additional-total-item-${index}`).html(formatRupiahWithDecimal(qty * dpp + tax_total));

                                    additional_tax_total_list[index] = tax_total;
                                }
                                initAdditionalTax();

                                calculateTaxTotalAdditional();
                                calculateTotalAdditional();
                                calculateGrandTotal();

                                additionalIdx++;
                            });
                        } else {
                            addAdditional(0);
                        }
                    };
                    displayAdditional();

                    initSelect2SearchPaginationData(`branch-select`, `{{ route('admin.select.branch') }}`, {
                        id: 'id',
                        text: 'name'
                    })

                }
            });

            const init_purchase_order_reference = (element) => {
                if ($(element).val() == "purchase_request") {
                    $('#purchase_request_trading_form').removeClass('d-none');
                    $('#purchase_request_trading_id').attr('required', true);

                    $('#sale_order_form').addClass('d-none');
                    $('#sale_order_id').attr('required', false).val('').trigger('change');
                } else {
                    $('#purchase_request_trading_form').addClass('d-none');
                    $('#purchase_request_trading_id').attr('required', false).val('').trigger('change');

                    $('#sale_order_form').removeClass('d-none');
                    $('#sale_order_id').attr('required', true);

                }
            }
        </script>
    @endcan
    <script>
        sidebarMenuOpen('#purchase-menu');
        sidebarMenuOpen('#purchase');
    </script>
@endsection
