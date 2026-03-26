@extends('layouts.admin.layout.index')

@php
    $main = 'purchase-order';
    $default_taxes = getDafaultTaxes();
@endphp

@section('title', Str::headline("Create $main") . ' - ')

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
                        {{ Str::headline('Create ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("create $main")
        <form action="{{ route('admin.purchase-order.store') }}" method="post" id="po_trading" enctype="multipart/form-data">
            <input type="hidden" name="purchase_type" value="trading">
            @csrf
            {{-- trading ==================================================================================================================================== --}}
            <x-card-data-table title="{{ 'create purchase order' }}" id="trading-type-card">
                <x-slot name="header_content">

                </x-slot>
                <x-slot name="table_content">
                    @include('components.validate-error')

                    <div class="row">
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
                                <x-input class="datepicker-input" id="tanggal" name="tanggal" label="tanggal" value="{{ \Carbon\Carbon::today()->format('d-m-Y') }}" onchange="checkClosingPeriod($(this))" helpers="Default Today" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="reference" id="reference" label="referensi PO" autofocus required onchange="init_purchase_order_reference(this)">
                                    <option value="purchase_request">{{ Str::headline('purchase request') }}</option>
                                    <option value="sale_order">{{ Str::headline('sales order') }}</option>
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3" id="purchase_request_trading_form">
                            <div class="form-group">
                                <x-select name="purchase_request_trading_id" id="purchase_request_trading_id" label="purchase request" hideAsterix>

                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3 d-none" id="sale_order_form">
                            <div class="form-group">
                                <x-select name="sale_order_id" id="sale_order_id" label="sales order" hideAsterix>

                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input type="text" id="note" name="note" label="keterangan" value="" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="customer_id" id="customer_id" label="customer" value="" required autofocus>

                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="sh_number_id" id="sh_number_id" label="sh_number" value="" required>

                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" id="supply_point" name="" label="supply point" value="" disabled hideAsterix />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" id="drop_point" name="" label="drop point / ship to" value="" disabled hideAsterix />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="vendor_id" id="vendor-id" label="vendor" required>
                                    <option value="1" selected>{{ \App\Models\Vendor::find(1)?->nama }}</option>
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="top" id="top" label="term of payment" required>
                                    <option value="cash" selected>Cash</option>
                                    <option value="by days">By Days</option>
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="number" id="top_day" name="top_day" label="hari" value="0" required />
                            </div>
                        </div>
                    </div>

                    <div class="mt-20 pt-20 border-top border-primary">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" id="sale_confirmation" name="sale_confirmation" label="SCO Supplier" value="" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="file" label="quotation" name="quotation" />
                                </div>
                            </div>
                        </div>
                    </div>
                </x-slot>
            </x-card-data-table>

            <x-card-data-table title="Trading Item">
                <x-slot name="table_content">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="item_id" id="item_id" label="item" value="" required>

                                </x-select>
                                <div class="unit-info mb-2"></div>
                            </div>
                            <input type="hidden" name="type" value="Liter">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" id="harga_trading" name="harga" class="commas-form" label="harga" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" id="jumlah" name="jumlah" class="commas-form" label="jumlah" value="" required />
                                <small class="text-secondary d-none" id="pr-remaining"></small>
                                <input type="hidden" id="outstanding_qty">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" id="discount_trading" name="discount" class="commas-form" label="Diskon" helpers="" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" class="commas-form" label="DPP" name="dpp_trading" id="dppTrading" value="" readonly />
                            </div>
                        </div>
                    </div>
                </x-slot>
            </x-card-data-table>

            <x-card-data-table title="Additional Item">
                <x-slot name="table_content">
                    <div id="additional-item"></div>
                </x-slot>
            </x-card-data-table>

            <x-card-data-table title="">
                <x-slot name="table_content">

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="tax_id_trading[]" id="tax_id_trading" label="pajak sebelum diskon" multiple>
                                    @foreach ($default_taxes as $tax)
                                        <option value="{{ $tax->id }}" selected>{{ $tax->name }}</option>
                                    @endforeach
                                </x-select>
                                <button type="button" id="clearTaxTrading" class="btn btn-sm btn-danger mb-4">Hapus Semua Tax</button>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="ppn_id" id="ppn_id" label="pajak setelah diskon">

                                </x-select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="row mt-10">
                            <div class="col-md-3">
                                <x-select name="currency_id" id="currency_id_trading" label="Currency" required>
                                    <option value="{{ get_local_currency()->id }}">{{ get_local_currency()->nama }}</option>
                                </x-select>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" name="exchange_rate" class="commas-form" label="kurs" id="exchange_rate_trading" value="1" required readonly />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-20">
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
                                <th>Qty</th>
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

                    <div class="row justify-content-end mt-20">
                        <div class="col-12 col-md-4">
                            <x-table theadColor='danger' id="">
                                <x-slot name="table_head">
                                    <th></th>
                                    <th></th>
                                </x-slot>
                                <x-slot name="table_body">
                                    <tr>
                                        <td class="text-end">Trading Total</td>
                                        <td>
                                            <div class="d-flex text-end">
                                                <p id="currency-simbol" class="me-10 mb-0">{{ get_local_currency()->simbol }}</p>
                                                <h5 class="mb-0 ms-auto" id="trading-total">0,00</h5>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-end">Additional Total</td>
                                        <td>
                                            <div class="d-flex text-end">
                                                <p id="currency-simbol" class="me-10 mb-0">{{ get_local_currency()->simbol }}</p>
                                                <h5 class="mb-0 ms-auto" id="additional-grand-total">0,00</h5>
                                            </div>
                                        </td>
                                    </tr>
                                </x-slot>
                                <x-slot name="table_foot">
                                    <tr>
                                        <td class="text-end">Grand Total</td>
                                        <td class="bg-success text-white d-flex text-end">
                                            <p id="currency-simbol" class="me-10 mb-0">{{ get_local_currency()->simbol }}</p>
                                            <h5 class="fw-bold mb-0 ms-auto" id="grand-total">0,00</h5>
                                        </td>
                                    </tr>
                                </x-slot>
                            </x-table>
                        </div>
                    </div>
                </x-slot>
            </x-card-data-table>

            <x-card-data-table title="">
                <x-slot name="table_content">
                    <div class="float-end mt-0" id="button_submit">
                        <x-button type="reset" color="secondary" class="w-auto" label="cancel" size="sm" icon="backward" fontawesome link="{{ url()->previous() }}" />
                        <x-button type="submit" color="primary" size="sm" icon="save" fontawesome class="w-auto" label="Save data" />
                    </div>
                </x-slot>
            </x-card-data-table>
        </form>
    @endcan
@endsection

@section('js')
    <script src="{{ asset('js/admin/select/itemSelect.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>

    <script>
        const defaultTaxes = @json($default_taxes);

        $(document).ready(function() {
            setTimeout(() => {
                $('#tax_id_trading').trigger('change');
            }, 250);

            checkClosingPeriod($('#tanggal'))
            let currency_symbol = '{{ get_local_currency()->simbol }}';

            let main_ppn = 0,
                main_tax_total = [];

            let additional_dpp_list = [],
                additional_tax_list = [],
                additional_tax_total_list = [];

            $('#trading-type-card').fadeIn(500);

            $('#discount_trading').val(0);

            $('#harga_trading').mask('#.##0,00', {reverse: true});

            const displayPriceMain = () => {
                let harga = formatThousandToFloat($('#harga_trading').val() ?? 0);
                $('#display-harga').html(formatRupiahWithDecimal(harga));
            }

            const displayQtyMain = () => {
                let jumlah = formatThousandToFloat($('#jumlah').val() || 0);
                let type = $('#type').val();

                if (type == 'Kilo Liter') {
                    jumlah = jumlah * 1000;
                }

                $('#trading-item-total-liter').html(formatRupiahWithDecimal(jumlah));
            }

            const main_dpp_amount = () => {
                let harga = formatThousandToFloat($('#harga_trading').val() || 0);
                let discount = formatThousandToFloat($('#discount_trading').val() || 0);
                let dpp = harga - discount;
                return parseFloat(dpp);
            }

            const displayDppMain = () => {
                let dpp = main_dpp_amount();
                $('#dppTrading').val(formatRupiahWithDecimal(dpp));
                $('#display-harga').html(formatRupiahWithDecimal(dpp));
            }

            const calculateSubTotal = () => {
                let type = $('#type').val();
                let jumlah = formatThousandToFloat($('#jumlah').val() || 0);
                let dpp = main_dpp_amount();

                if (type == 'Kilo Liter') {
                    jumlah = jumlah * 1000;
                }

                $('#trading-item-sub-total').html(formatRupiahWithDecimal(jumlah * dpp));
                $('#trading-item-sub-total').attr('data-value', jumlah * dpp);
            }

            const calculateTotalMain = () => {
                let sub_total = $('#trading-item-sub-total').attr('data-value');
                let tax_total = main_tax_total.reduce((a, b) => a + b, 0);
                let main_total = parseFloat(sub_total) + parseFloat(main_ppn) + parseFloat(tax_total);

                $('#main-total').html(formatRupiahWithDecimal(main_total));
                $('#main-total').attr('data-value', main_total);
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

                            calculateSubTotal();
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
                let taxes = $('#tax_id_trading').val();
                let harga = formatThousandToFloat($('#harga_trading').val() || 0);
                let jumlah = formatThousandToFloat($('#jumlah').val() || 0);

                if (type == 'Kilo Liter') {
                    jumlah = jumlah * 1000;
                }

                let dpp = main_dpp_amount();

                $('#trading-item-table tbody').children('tr:not(:nth-child(-n+2))').remove();

                if (taxes.length !== 0) {
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

                                main_tax_total[index] = (harga * data.value) * jumlah;

                                updateCurrencySymbol();

                                calculateSubTotal();
                                calculateTotalMain();
                                calculateGrandTotal();
                            }
                        });
                    });
                } else {
                    calculateSubTotal();
                    calculateTotalMain();
                    calculateGrandTotal();
                }
            }

            initSelect2SearchPaginationData(`branch-select`, `{{ route('admin.select.branch') }}`, {
                id: 'id',
                text: 'name'
            })

            initSelect2SearchPaginationData(`purchase_request_trading_id`, `{{ route('admin.select.purchase-request-trading') }}`, {
                id: 'id',
                text: 'code,customer_name'
            })

            initSelect2SearchPaginationData(`sale_order_id`, `{{ route('admin.select.sos-for-purchase-order') }}`, {
                id: 'id',
                text: 'nomor_so,customer_name'
            }, 0, {
                branch_id: function() {
                    return $('#branch-select').val();
                },
            });

            initSelect2SearchPaginationData(`vendor-id`, `{{ route('admin.select.vendor') }}`, {
                id: 'id',
                text: 'nama'
            })

            initSelect2SearchPaginationData(`ppn_id`, `{{ route('admin.select.tax') }}`, {
                id: 'id',
                text: 'name'
            })

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
                        let outstanding_qty = purchase_request_trading_detail.qty - purchase_request_trading_detail.ordered_qty;
                        $('#item_id').append(`<option value="${purchase_request_trading_detail.item.id}">${purchase_request_trading_detail.item.kode} - ${purchase_request_trading_detail.item.nama}</option>`);
                        $('#item_id').trigger('change');
                        $('#jumlah').val(formatRupiahWithDecimal(outstanding_qty)).trigger('keyup');
                        $('#outstanding_qty').val(outstanding_qty);
                        $('#pr-remaining').removeClass('d-none').text(`Sisa PR: ${formatRupiahWithDecimal(outstanding_qty)}`);
                    }
                });
            });

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

            initSelect2SearchPaginationData(`customer_id`, `{{ route('admin.select.customer') }}`, {
                id: 'id',
                text: 'nama'
            })

            $('#customer_id').change(function(e) {
                updateCurrentPrice();

                $('#sh_number_id').select2();
                $('#sh_number_id').html('');
                $('#supply_point').html('');
                $('#drop_point').html('');

                initSelect2Search('sh_number_id', `{{ route('admin.select.customer.sh-numbers') }}/${this.value}`, {
                    id: "id",
                    text: "kode,supply_point,drop_point"
                });
            });

            initSelect2SearchPaginationData(`item_id`, `{{ route('admin.select.item.type') }}/trading`, {
                id: 'id',
                text: 'nama,kode'
            })

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

            const updateCurrencySymbol = () => {
                $('p#currency-simbol').each(function() {
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
                        $('#harga_trading').val(data.harga_beli ?? 0);
                        $('#harga_trading').trigger('keyup');
                    }
                });
            }

            $('#item_id').change(function(e) {
                let value = $(this).val();
                let item = $(this).select2('data');

                updateCurrentPrice();

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

                $('#jumlah').val('').trigger('change');
                $('#tax_id_trading').val(null).trigger('change');
            });

            $('#type').change(function() {
                displayQtyMain();
                displayDppMain();
                calculateSubTotal();
                calculatePpnMain();
                calculateTaxMain();
                calculateTotalMain();
                calculateGrandTotal();
            });

            $('#harga_trading').keyup(debounce(function() {
                displayPriceMain();
                displayDppMain();
                calculateSubTotal();
                calculatePpnMain();
                calculateTaxMain();
                calculateTotalMain();
                calculateGrandTotal();
            }, 750));

            $('#jumlah').keyup(debounce(function() {
                if (formatThousandToFloat($(this).val()) > formatThousandToFloat($('#outstanding_qty').val()) && $('#purchase_request_trading_id').val() != null && $('#purchase_request_trading_id').val() != '') {
                    alert('Jumlah melebihi purchase request');
                    $(this).val(formatThousandToFloat($('#outstanding_qty').val()));
                }

                displayQtyMain();
                calculateSubTotal();
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

            const initTaxMain = () => {
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
                            result["search"] = term;
                            result["selected_item"] = selected_tax;
                            result[`tax_id_trading`] = target_value;
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

                $('#tax_id_trading').select2(selectTaxOpts);
            }
            initTaxMain();

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

            $('#tax_id_trading').change(function(e) {
                calculateTaxMain();
            });

            $('#clearTaxTrading').click(function(e) {
                $('#tax_id_trading').val(null).trigger('change');
            });

            $('#ppn_id').change(function(e) {
                calculatePpnMain();
                calculateTotalMain();
                calculateGrandTotal();
            });

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
                        currency_symbol = data.simbol;
                        updateCurrencySymbol();
                        if (data.is_local) {
                            $('#exchange_rate_trading').val(1);
                            $('#exchange_rate_trading').attr('readonly', 'readonly');
                        } else {
                            $('#exchange_rate_trading').removeAttr('readonly');
                            $('#exchange_rate_trading').attr('readonly', false);
                        }
                    }
                });
            });

            // ==================== additional ==================== //

            let additionalItemIdx = 0;

            const addItem = (item_index) => {
                let btn = ``;

                if (item_index == 0) {
                    btn = `
                            <div class="col-md-1 row align-items-center">
                                <div class="form-group">
                                    <x-button type="button" color="info" icon="plus" fontawesome size="sm" id="add-additioal-item" />
                                </div>
                            </div>`;
                } else {
                    btn = `
                            <div class="col-md-1 row align-items-center">
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
                        <div class="col-md-3">
                            <x-select name="additional_tax_id_${item_index}[]" id="additional-tax-id-${item_index}" label="Tax" multiple disabled></x-select>
                            <button type="button" id="clearTaxAdditional${item_index}" class="btn btn-sm btn-danger mb-4">Hapus Semua Tax</button>
                        </div>
                        ${btn}
                    </div>
                `;

                $('#additional-item').append(html);

                initCommasForm();

                // * table =============================================================================================================================
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
                        <td id="additional_tax_value_detail_${item_index}">-</td>
                        <td>
                            <div class="d-flex text-end">
                                <p id="currency-simbol" class="me-10 mb-0">${currency_symbol}</p>
                                <h5 class="mb-0 ms-auto" id="additional-total-item-${item_index}">0,00</h5>
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

                const calculateTaxAdditional = () => {
                    additional_tax_list = [];

                    let qty = formatThousandToFloat($(`#additional-jumlah-${item_index}`).val() || 0);
                    let dpp = formatThousandToFloat($(`#additional-harga-${item_index}`).val() || 0);
                    let tax_list = $(`#additional-tax-id-${item_index}`).val();

                    $(`#additional_tax_data_detail_${item_index}`).html('');
                    $(`#additional_tax_value_detail_${item_index}`).html('');

                    if (tax_list.length > 0) {
                        additional_tax_list[item_index] = [];

                        let tax_total_amount = 0;

                        $.each(tax_list, function(index, value) {
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

                                    additional_tax_list[item_index][index] = {
                                        detail_id: value,
                                        tax_id: data.id,
                                        name: data.name,
                                        value: data.value
                                    };

                                    additional_tax_total_list[item_index] = tax_total_amount;

                                    $(`#additional-total-item-${item_index}`).html(formatRupiahWithDecimal(dpp + tax_total_amount));

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

                        $(`#additional-total-item-${item_index}`).html(formatRupiahWithDecimal(dpp));

                        calculateDppAdditional();
                        calculateTaxTotalAdditional();
                        calculateTotalAdditional();
                        calculateGrandTotal();
                    }
                };

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

                const removeItem = (index) => {
                    $(`#additional-item-${index}`).remove();
                    $(`#additional-resume-${index}`).remove();
                }

                if (item_index == 0) {
                    $('#add-additioal-item').click(function(e) {
                        additionalItemIdx++;
                        addItem(additionalItemIdx);
                    });
                } else {
                    $(`#delete-additional-item-${item_index}`).click(function(e) {
                        additional_dpp_list.splice(item_index, 1);
                        additional_tax_list.splice(item_index, 1);
                        additional_tax_total_list.splice(item_index, 1);

                        calculateDppAdditional();
                        calculateTaxTotalAdditional();
                        calculateTotalAdditional();
                        calculateGrandTotal();

                        removeItem(item_index);
                    });
                }

                $(`#additional-type-${item_index}`).select2();
                $(`#additional-type-${item_index}`).change(function(e) {
                    additional_dpp_list[item_index] = 0;
                    additional_tax_list[item_index] = [];
                    additional_tax_total_list[item_index] = 0;

                    $(`#additional-item-id-${item_index}`).removeAttr('disabled');
                    $(`#additional-tax-id-${item_index}`).removeAttr('disabled');
                    $(`#additional-harga-${item_index}`).removeAttr('disabled');
                    $(`#additional-jumlah-${item_index}`).removeAttr('disabled');

                    $(`#additional-item-name-${item_index}`).html('-');
                    $(`#additional-item-price-${item_index}`).html('0,00');
                    $(`#additional-item-qty-${item_index}`).html('0,00');
                    $(`#additional-sub-total-${item_index}`).html('0,00');
                    $(`#additional_tax_data_detail_${item_index}`).html('-');
                    $(`#additional_tax_value_detail_${item_index}`).html(`${currency_symbol} 0,00`);
                    $(`#additional-total-item-${item_index}`).html('0,00');

                    $(`#additional-harga-${item_index}`).val(null).trigger('change');
                    $(`#additional-item-id-${item_index}`).val(null).trigger('change');

                    if (this.value) {
                        initSelect2SearchPaginationData(`additional-item-id-${item_index}`, `{{ route('admin.select.item.type') }}/${this.value}`, {
                            id: 'id',
                            text: 'nama,kode'
                        })

                        let optionTaxes = '';

                        defaultTaxes.map((tax, index) => {
                            optionTaxes += `<option value="${tax.id}" selected>${tax.name}</option>`;
                        });

                        $(`#additional-tax-id-${item_index}`).html(optionTaxes);

                        setTimeout(() => {
                            $('#additional-tax-id-' + item_index).trigger('change');
                        }, 250);

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
                                        $(`#additional-harga-${item_index}`).val(formatRupiahWithDecimal(data.harga_jual ?? 0));
                                        $(`#additional-item-price-${item_index}`).html(formatRupiahWithDecimal(data.harga_jual ?? 0));
                                        $(`#additional-sub-total-${item_index}`).html(formatRupiahWithDecimal(data.harga_jual ?? 0));
                                        $(`#additional-total-item-${item_index}`).html(formatRupiahWithDecimal(data.harga_jual ?? 0));

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

                initSelect2SearchPaginationData(`additional-item-id-${item_index}`, `{{ route('admin.select.item.type') }}/${$(`#additional-type-${item_index}`).val()}`, {
                    id: 'id',
                    text: 'nama,kode'
                })

                initCommasFormThreeDigits();

                $(`#additional-harga-${item_index}`).keyup(debounce(function() {
                    let value = formatThousandToFloat($(this).val() || 0);
                    let qty = formatThousandToFloat($(`#additional-jumlah-${item_index}`).val() || 0);
                    let subtotal = value * qty;

                    $(`#additional-item-qty-${item_index}`).html(formatRupiahWithDecimal(qty));
                    $(`#additional-item-price-${item_index}`).html(formatRupiahWithDecimal(value));
                    $(`#additional-sub-total-${item_index}`).html(formatRupiahWithDecimal(subtotal));

                    additional_dpp_list[item_index] = subtotal;

                    calculateTaxAdditional();
                }, 750));

                $(`#additional-jumlah-${item_index}`).keyup(debounce(function() {
                    let qty = formatThousandToFloat($(this).val() || 0);
                    let value = formatThousandToFloat($(`#additional-harga-${item_index}`).val() || 0);
                    let subtotal = value * qty;

                    $(`#additional-item-qty-${item_index}`).html(formatRupiahWithDecimal(qty));
                    $(`#additional-item-price-${item_index}`).html(formatRupiahWithDecimal(value));
                    $(`#additional-sub-total-${item_index}`).html(formatRupiahWithDecimal(subtotal));

                    additional_dpp_list[item_index] = subtotal;

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

                additionalItemIdx++;
            }
            addItem(additionalItemIdx);

            const handleSubmit = (e) => {
                $(`form#po_trading`).submit(function(e) {
                    if ($(`#jumlah`).val() == 0) {
                        e.preventDefault();
                        showAlert('', 'Jumlah tidak boleh 0')
                        $(`#button_submit`).html('')
                        $(`#button_submit`).html(`
                        <x-button type="reset" color="secondary" class="w-auto" label="cancel" size="sm" icon="backward" fontawesome link="{{ url()->previous() }}" />
                        <x-button type="submit" color="primary" size="sm" icon="save" fontawesome class="w-auto" label="Save data" />
                        `)
                    } else if ($(`#jumlah`).val() == "NaN") {
                        e.preventDefault();
                        showAlert('', 'Jumlah tidak boleh string')
                        $(`#button_submit`).html('')
                        $(`#button_submit`).html(`
                        <x-button type="reset" color="secondary" class="w-auto" label="cancel" size="sm" icon="backward" fontawesome link="{{ url()->previous() }}" />
                        <x-button type="submit" color="primary" size="sm" icon="save" fontawesome class="w-auto" label="Save data" />
                        `)
                    }
                })
            }

            handleSubmit()

            const calculateGrandTotal = () => {
                let mainTotal = $('#main-total').attr('data-value');
                let additionalTotal = $('#additional-total').attr('data-value');

                $('#trading-total').html(formatRupiahWithDecimal(mainTotal));
                $('#additional-total').html(formatRupiahWithDecimal(additionalTotal));
                $('#grand-total').html(formatRupiahWithDecimal(parseFloat(mainTotal) + parseFloat(additionalTotal)));
            };

            $('#vendor-id').change(function(e) {
                $.ajax({
                    url: base_url + "/vendor/" + $(this).val(),
                    type: "get",
                    success: function({
                        data
                    }) {
                        $('#top').val(data.term_of_payment).trigger('change');
                        $('#top_day').val(data.top_days);
                    }
                })
            });

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
    <script>
        sidebarMenuOpen('#purchase-menu');
        sidebarActive('#purchase')
    </script>
@endsection
