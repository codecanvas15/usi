@extends('layouts.admin.layout.index')

@php
    $main = 'sales-order';
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
                    <li class="breadcrumb-item active">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($main) }}</a>
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
        <form action="{{ route("admin.$main.store") }}" method="post" enctype="multipart/form-data" id="create">
            <x-card-data-table title="{{ 'create ' . $main }}">
                <x-slot name="header_content">

                </x-slot>
                <x-slot name="table_content">
                    @include('components.validate-error')
                    @csrf

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <label class="mb-2" for="branch_id">Branch <span class="text-danger">*</span></label>
                                <select class="form-control select2" name="branch_id" id="branch_id" value="" required>
                                    <option value="{{ get_current_branch()->id }}" selected>{{ get_current_branch()->name }}</option>
                                </select>
                            </div>
                            <span class="text-danger error_branch_id" style="display: none">Branch tidak boleh kosong!</span>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input class="datepicker-input" id="tanggal_data" name="tanggal" value="{{ $model->tanggal ?? \Carbon\Carbon::today()->format('d-m-Y') }}" onchange="checkClosingPeriod($(this))" />
                                <small class="text-primary">Default Today</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" id="nomor_po_external" name="nomor_po_external" label="Nomor PO" value="" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="file" label="quotation" name="quotation" required />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <x-select name="customer_id" id="customer_id" label="customer" value="" required>

                            </x-select>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" id="term_of_payment" name="term_of_payment" label="term_of_payment" value="" readonly />
                            </div>
                        </div>
                    </div>

                    <div class="row mt-10">
                        <div class="col-md-4">
                            <x-select name="sh_number_id" id="sh_number_id" label="SH_number" value="" required>

                            </x-select>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" name="supply_point" label="supply_point" id="supply_point" required readonly />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" name="drop_point" label="drop_point / ship to" id="drop_point" required readonly />
                            </div>
                        </div>
                    </div>
                </x-slot>

            </x-card-data-table>
            <x-card-data-table title="Trading Item">

                <x-slot name="table_content">
                    <div class="row" id="item-1">
                        <div class="col-md-3">
                            <x-select name="item_id[]" id="item_id" label="item" value="" required>

                            </x-select>
                            <p class="wrapper_information_unit"></p>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" name="price[]" id="harga" label="harga" class="commas-form-three text-end" helpers="" required />
                                <p id="wrapper_information_price"></p>

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="col-md">
                                <div class="form-group">
                                    <x-input type="text" name="jumlah[]" id="jumlah" label="Qty" class="commas-form" value="" helpers="" required />
                                    <p class="wrapper_information_unit"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <x-select name="tax_id_" id="tax_id" label="Pajak" multiple>
                                @foreach ($default_taxes as $item)
                                    <option value="{{ $item->id }}" selected>{{ $item->name }}</option>
                                @endforeach
                            </x-select>
                            <div id="tax_list">

                            </div>
                        </div>
                    </div>
                </x-slot>

            </x-card-data-table>

            <x-card-data-table title="Additional Item">
                <x-slot name="table_content">
                    <div id="additiional-list">

                    </div>
                </x-slot>
            </x-card-data-table>

            <x-card-data-table>
                <x-slot name="table_content">
                    <div class="row">
                        <div class="col-md-4">
                            <x-select name="currency_id" id="currency_id" label="Currency" value="{{ $model->currency_id ?? '' }}" required>
                                <option value="{{ $currency->id }}" selected>{{ $currency->nama }}</option>
                            </x-select>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" id="exchange_rate" class="commas-form" name="exchange_rate" label="kurs" value="1" required readonly />
                            </div>
                        </div>
                    </div>

                    <div class="mt-30">
                        <x-table theadColor='danger' id="table-total">
                            <x-slot name="table_head">
                                <th>Item</th>
                                <th>Harga</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                            </x-slot>
                            <x-slot name="table_body">
                                <tr>
                                    <td id="item-name">-</td>
                                    <td>
                                        <span class="d-flex">
                                            <p class="me-10" id="currency-simbol">{{ $currency->simbol }}</p>
                                            <p class="fw-bold" id="display-harga">0</p>
                                        </span>
                                    </td>
                                    <td>
                                        <p id="total-item-liter">
                                            0
                                        </p>
                                        <p></p>
                                        <!--- for layout dont remove --->
                                    </td>
                                    <td>
                                        <span class="d-flex">
                                            <span class="me-10" id="currency-simbol">{{ $currency->simbol }}</span>
                                            <span class="fw-bold w-100 text-end" id="sub_total"></span>
                                        </span>
                                    </td>
                                </tr>

                                <tr id="row-total">
                                    <td colspan="3" class="fw-bold text-end">Total</td>
                                    <td>
                                        <div class="align-self-end">
                                            <span class="d-flex">
                                                <p class="me-10" id="currency-simbol">{{ $currency->simbol }}</p>
                                                <p class="fw-bold text-end w-100" id="total">0</p>
                                            </span>
                                        </div>
                                    </td>
                                </tr>

                            </x-slot>
                        </x-table>

                        <div class="mt-30">
                            <h4 class="fw-bold">Additional item</h4>
                            <x-table theadColor='danger' id="calculate-general-additional">
                                <x-slot name="table_head">
                                    <th>#</th>
                                    <th>Item</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th class="d-none">DPP</th>
                                    <th>Tax</th>
                                    <th>Value</th>
                                    <th>Sub Total</th>
                                </x-slot>
                                <x-slot name="table_body">

                                </x-slot>
                                <x-slot name="table_foot">
                                    <tr class="d-none">
                                        <td class="text-end" colspan="6">DPP</td>
                                        <td class="d-flex text-end">
                                            <p id="currency-simbol" class="me-10 mb-0">{{ get_local_currency()->simbol }}</p>
                                            <h5 class="mb-0 ms-auto" id="additional-dpp-total">0</h5>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-end" colspan="6">Total Pajak</td>
                                        <td class="d-flex text-end">
                                            <p id="currency-simbol" class="me-10 mb-0">{{ get_local_currency()->simbol }}</p>
                                            <h5 class="mb-0 ms-auto" id="additional-tax-total">0</h5>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-end" colspan="6">Total</td>
                                        <td class="bg-success text-white d-flex text-end">
                                            <p id="currency-simbol" class="me-10 mb-0">{{ get_local_currency()->simbol }}</p>
                                            <h5 class="fw-bold mb-0 ms-auto" id="additiional-total">0</h5>
                                        </td>
                                    </tr>
                                </x-slot>
                            </x-table>
                        </div>

                        <div class="row justify-content-end mt-20">
                            <div class="col-12 col-lg-7 col-md-8">
                                <x-table theadColor='danger' id="">
                                    <x-slot name="table_head">
                                        <th></th>
                                        <th></th>
                                    </x-slot>
                                    <x-slot name="table_body">
                                        <tr>
                                            <td>Trading Total</td>
                                            <td>
                                                <div class="d-flex text-end">
                                                    <p id="currency-simbol" class="me-10 mb-0">{{ get_local_currency()->simbol }}</p>
                                                    <h5 class="mb-0 ms-auto" id="trading-item-total">0</h5>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Additional Item Total</td>
                                            <td>
                                                <div class="d-flex text-end">
                                                    <p id="currency-simbol" class="me-10 mb-0">{{ get_local_currency()->simbol }}</p>
                                                    <h5 class="mb-0 ms-auto" id="additional-item-total">0</h5>
                                                </div>
                                            </td>
                                        </tr>
                                    </x-slot>
                                    <x-slot name="table_foot">
                                        <tr>
                                            <td class="text-end">Total</td>
                                            <td class="bg-success text-white d-flex text-end">
                                                <p id="currency-simbol" class="me-10 mb-0">{{ get_local_currency()->simbol }}</p>
                                                <h5 class="fw-bold mb-0 ms-auto" id="total-all">0</h5>
                                            </td>
                                        </tr>
                                    </x-slot>
                                </x-table>
                            </div>
                        </div>

                    </div>
                </x-slot>
            </x-card-data-table>

            <x-card-data-table>
                <x-slot name="table_content">
                    <div class="float-end mt-0">
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
            let currency_symbol = '{{ $currency->simbol }}';

            $('.money').mask('000.000.000.000.000,00', {
                reverse: true
            });
            $('#wrapper_information_price').hide()

            checkClosingPeriod($('#tanggal_data'));

            setTimeout(() => {
                $('#tax_id').trigger('change');
            }, 250);

            // * ============================ price, sub total, tax =======================

            let total = 0,
                sub_total = 0,
                tax = 0;

            let price_list = [],
                sub_total_list = [],
                tax_list = [],
                tax_list_value = [];

            // additional calculattion variables
            let additional_tax_list_value = [],
                additional_jumlah_list = [],
                additional_price_list = [],
                additional_total = 0,
                additional_sub_total = 0,
                additional_tax_total = 0,
                additional_sub_total_list = [];

            const calculateTotalAdditionalAndTradingItem = () => {
                $('#additional-dpp-total').html(formatRupiahWithDecimal(additional_sub_total))
                $('#additional-tax-total').html(formatRupiahWithDecimal(additional_tax_total))
                $('#additional-item-total').html(formatRupiahWithDecimal(additional_total));
                $('#total').html(formatRupiahWithDecimal(total));
                $('#trading-item-total').html(formatRupiahWithDecimal(total));
                $('#total-all').html(formatRupiahWithDecimal(total + additional_total));
            };

            const updateValuePrices = () => {
                $('#tax_data').html(tax);
                if (tax != 0) {
                    $('#tax_total').html(formatRupiahWithDecimal(sub_total * (tax / 100)));
                }

                $('#total').html(formatRupiahWithDecimal(total));
            }

            const calculateTotal = () => {

                let with_tax = 0;
                total = 0;
                if (sub_total_list.length > 0) {
                    sub_total = sub_total_list.reduce((a, b) => a + b);
                } else {
                    sub_total = 0;
                }

                $('#sub_total').html(formatRupiahWithDecimal(sub_total));

                let total_tax = 1;
                if (tax_list_value.length > 0) {
                    total_tax = tax_list_value.reduce((a, b) => parseFloat(a) + parseFloat(b));

                    tax_list_value.map((data_tax, index) => {
                        $(`#tax-${index}`).html(formatRupiahWithDecimal(sub_total * data_tax));
                    })
                }

                if (total_tax != 1) {
                    sub_total += sub_total * total_tax;
                }

                total = sub_total;
                calculateTotalAdditionalAndTradingItem();
                $('#total').html(formatRupiahWithDecimal(total));
            }

            const updateCurrencySymbol = () => {
                $('p#currency-simbol').each(function() {
                    $(this).html(currency_symbol);
                });
            }

            let parentTimeoutTax;

            const tax_function = () => {
                clearTimeout(parentTimeoutTax);

                tax_list.map((tax, index) => {
                    $(`#tax-table-${index}`).remove();
                });

                tax_list = [];
                tax_list_value = [];
                let html = '';

                [...document.getElementById('tax_id').options].map((option, index) => {
                    if (option.selected) {
                        tax_list.push(option.value);

                    }
                });

                tax_list.map((tax, index) => {
                    let num = 1;
                    html += `<input type="hidden" name="tax_id[]" value="${tax}"/>`;

                    parentTimeoutTax = setTimeout(() => {
                        $.ajax({
                            type: "get",
                            url: "{{ route('admin.tax.detail') }}/" + tax,
                            success: ({
                                data
                            }) => {
                                tax_list_value[index] = data.value;
                                let new_html = `
                                <tr id="tax-table-${index}">
                                    <td colspan="3" class="fw-bold text-end">${data.name} - ${data.value * 100}%</td>
                                    <td>
                                        <span class="d-flex">
                                            <p class="me-10" id="currency-simbol">${currency_symbol}</p>
                                            <h5 id="tax-${index}" class="text-end w-100"></h5>
                                        </span>
                                    </td>
                                </tr>`;
                                $(new_html).insertBefore(`#row-total`);
                                updateCurrencySymbol();
                                calculateTotal();
                                updateValuePrices();

                                num++;
                            }
                        });
                    }, 500);
                })

                $('#tax_list').html(html);
            }

            $('#tax_id').on('change click', function(e) {
                tax_function();
                calculateTotal();
                updateValuePrices();
            })

            updateValuePrices();

            $('#jumlah').keyup(function(e) {
                let value = thousandToFloat(this.value);
                $('#total-item-liter').html(`${formatRupiahWithDecimal(value)}`);

                $('input[name="additional_quantity[]"]').each(function() {
                    $(this).val(formatRupiahWithDecimal(value));
                });

                if (value != '') {
                    sub_total_list[0] = price_list[0] * value;
                } else {
                    sub_total_list[0] = 0;
                }

                if (sub_total_list[0]) {
                    calculateTotal();
                    updateValuePrices();
                }
            });

            $('#harga').keyup(function(e) {
                price_list[0] = thousandToFloat(this.value);
                $('#display-harga').html(`${formatRupiahWithDecimal(price_list[0])}`);

                let value = thousandToFloat($('#jumlah').val());
                sub_total_list[0] = price_list[0] * value;
                calculateTotal()
                updateValuePrices();
            });
            // * ============================ price, sub total, tax =======================

            initSelect2Search('tax_id', "{{ route('admin.select.tax') }}", {
                id: "id",
                text: "name"
            });

            inititemSelect('item_id', 'trading')

            // get item price when item select form updated or selected
            $('#item_id').change(function(e) {
                e.preventDefault();
                updatePrice();

                if (this.value) {
                    var data = $(this).select2('data');
                    $('#item-name').html(data[0].text);

                    $.ajax({
                        type: "get",
                        url: `${base_url}/item/${this.value}`,
                        success: function({
                            data
                        }) {
                            $('.wrapper_information_unit').html(`<span class="text-primary">${data.unit.name}</span>`);
                        }
                    });

                    return;
                }

                $('#item-name').html('-');

            });

            const getTermsOfPayment = () => {
                $.ajax({
                    type: "get",
                    url: `{{ route('admin.select.customer-detail') }}/${$('#customer_id').val()}`,
                    success: function({
                        data
                    }) {
                        let customer_top = data.term_of_payment;
                        if (customer_top != "cash") {
                            customer_top += ` - ${data.top_days} hari`;
                        }
                        $('#term_of_payment').val(customer_top);
                    }
                });
            }

            const updatePrice = () => {
                $.ajax({
                    type: "get",
                    url: `{{ route('admin.select.select-with-period-and-sh-number-and-search-harga-jual') }}/${$('#item_id').val()}/${$('#sh_number_id').val()}/${$('#tanggal_data').val()}`,
                    success: function({
                        data
                    }) {
                        if (data.harga_jual ?? null) {
                            price_list[0] = data.harga_jual;
                            $('#harga').val(formatRupiahWithDecimal(data.harga_jual));
                            $('#harga').trigger('keyup');
                            $('#wrapper_information_price').show()
                            $('#wrapper_information_price').html(`
                                <small class="text-danger">Harga diambil dari ${data?.nama} periode ${data?.period?.value}</small>
                            `)
                        } else {
                            $('#wrapper_information_price').hide()
                        }

                    }
                });
            };


            initSelect2Search('customer_id', "{{ route('admin.select.customer') }}", {
                id: "id",
                text: "nama"
            });

            initSelect2Search('currency_id', "{{ route('admin.select.currency') }}", {
                id: "id",
                text: "kode,nama,negara"
            });

            $('#currency_id').change(function(e) {
                e.preventDefault();
                if ($(this).val()) {
                    $.ajax({
                        type: "get",
                        url: "{{ route('admin.currency.detail') }}/" + this.value,
                        success: function({
                            data
                        }) {
                            currency_symbol = data.simbol;

                            if (data.is_local) {
                                $('#exchange_rate').val(1);
                                $('#exchange_rate').trigger('change');
                                $('#exchange_rate').attr('readonly', true);
                            } else {
                                $('#exchange_rate').val('');
                                $('#exchange_rate').trigger('change');
                                $('#exchange_rate').attr('readonly', false);
                            }
                            updateCurrencySymbol();
                            calculateTotal();
                        }
                    });
                } else {
                    $('#exchange_rate').val('');
                    calculateTotal()
                }
                calculateTotal();
                return;
            });

            $('#tanggal_data').change(function(e) {
                e.preventDefault();
                // updatePrice();
            });

            $('#sh_number_id').change(function(e) {
                e.preventDefault();
                updatePrice();
                $.ajax({
                    type: "get",
                    url: `{{ route('admin.sh-number.detail') }}/${this.value}`,
                    success: function({
                        data
                    }) {

                        data.sh_number_details.map((item, index) => {
                            if (item.type == 'Supply Point') {
                                $('#supply_point').val(item.alamat);
                            } else if (item.type == 'Drop Point') {
                                $('#drop_point').val(item.alamat);
                            }
                        });
                    }
                });
            });

            $('#customer_id').change(function(e) {

                updatePrice();
                getTermsOfPayment();
                $('#sh_number_id').removeAttr('disabled');
                $('#sh_number_id').select2('close');

                initSelect2Search('sh_number_id', `{{ route('admin.select.customer.sh-numbers') }}/${this.value}`, {
                    id: "id",
                    text: "kode,supply_point,drop_point"
                });

                $('#drop_point').val('');
                $('#supply_point').val('');
            });

            // additioan item ===========================================================================================
            const initAdditionalItem = () => {
                let index = 0;

                const calculateAllAdditional = () => {
                    let total_all_additional = 0;
                    additional_sub_total = additional_sub_total_list.reduce((a, b) => a + b, 0);

                    additional_tax_total = 0;
                    additional_sub_total_list.map((sub_total_data, sub_total_index) => {
                        total_all_additional += sub_total_data;

                        let total_single = sub_total_data;
                        $(`#additional-sub_total_${sub_total_index}`).html(formatRupiahWithDecimal(total_single));

                        additional_tax_list_value[sub_total_index].map((tax, tax_index) => {
                            total_all_additional += sub_total_data * tax;

                            additional_tax_total += sub_total_data * tax;
                            total_single += sub_total_data * tax;
                            $(`#tax-${sub_total_index}-${tax_index}`).html(formatRupiahWithDecimal(sub_total_data * tax));
                        })

                        $(`#additiona-total-item-${sub_total_index}`).html(formatRupiahWithDecimal(total_single));
                    });

                    additional_total = total_all_additional;
                    $(`#additiional-total`).html(formatRupiahWithDecimal(total_all_additional));
                    calculateTotalAdditionalAndTradingItem()
                }

                const deleteItem = (item_index) => {
                    $(`#additional-item-${item_index}`).remove();
                    $(`#additional-resume-${item_index}`).remove();

                    additional_jumlah_list[item_index] = 0;
                    additional_price_list[item_index] = 0;
                    additional_tax_list_value[item_index] = [];
                    additional_sub_total_list[item_index] = 0;
                };

                const addItem = (item_index) => {
                    let btn = '';
                    let tax_list = [];
                    additional_jumlah_list[item_index] = 0;
                    additional_price_list[item_index] = 0;
                    additional_tax_list_value[item_index] = [];
                    additional_sub_total_list[item_index] = 0;

                    if (item_index == 0) {
                        btn = `<x-button color="primary" icon="plus" fontawesome size="sm" id="add-additional-item" />`;
                    } else {
                        btn = `<x-button color="danger" icon="trash" fontawesome size="sm" id="delete-additional-item-${index}" />`;
                    }

                    let html = `
                        <div class="row" id="additional-item-${item_index}">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="item_type[]" id="additional-type-${item_index}" label="Type">
                                        <option value="">Pilih Item</option>
                                        <option value="transport">Transport</option>
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-select name="additional_item[]" id="additional-item-id-${item_index}" label="item" disabled>

                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-input type="text" name="additional_price[]" label="harga" id="additional-harga-${item_index}" class="text-end commas-form" disabled />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-input type="text" name="additional_quantity[]" label="Qty" id="additional-quantity-${item_index}" class="text-end commas-form" readonly value="${$('#jumlah').val()}" />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-select name="additional_tax_id[]" id="additional-tax-id-${item_index}" label="Pajak" multiple disabled>

                                    </x-select>
                                    <input type="hidden" name="additional_tax[]" id="additional-tax-value-${item_index}" value="">
                                </div>
                            </div>
                            <div class="col-md-1 row align-items-end">
                                <div class="form-group">
                                    ${btn}
                                </div>
                            </div>
                        </div>
                    `;
                    $('#additiional-list').append(html);

                    // * table =============================================================================================================================
                    $('#calculate-general-additional tbody').append(`
                        <tr id="additional-resume-${item_index}">
                            <th></th>
                            <td>
                                <span id="additiona-item-name-${item_index}">-</span
                            </td>
                            <td>
                                <div class="d-flex">
                                    <p id="currency-simbol" class="me-10 mb-0">${currency_symbol}</p>
                                    <h5 class="mb-0 ms-auto" id="additiona-item-price-${item_index}">0</h5>
                                </div
                            </td>
                            <td id="additional-jumlah-display-${item_index}">0</td>
                            <td class="d-none">
                                <div class="d-flex">
                                    <p id="currency-simbol" class="me-10 mb-0">${currency_symbol}</p>
                                    <h5 class="mb-0 ms-auto" id="additional-sub_total_${item_index}">${additional_price_list[item_index] ?? 0}</h5>
                                </div
                            </td>
                            <td id="additional_tax_data_detail_${item_index}">-</td>
                            <td id="additional_tax_value_detail_${item_index}">-</td>
                            <td>
                                <div class="d-flex text-end">
                                    <p id="currency-simbol" class="me-10 mb-0">${currency_symbol}</p>
                                    <h5 class="mb-0 ms-auto" id="additiona-total-item-${item_index}">${additional_sub_total_list[item_index]}</h5>
                                </div>
                            </td>
                        </tr>
                    `);
                    // * /table =============================================================================================================================

                    // delete item
                    if (item_index == 0) {
                        $('#add-additional-item').click(function(e) {
                            e.preventDefault();
                            addItem(++index);
                        });
                    } else {
                        $(`#delete-additional-item-${item_index}`).click(function(e) {
                            e.preventDefault();
                            deleteItem(item_index);
                        });
                    }

                    $('#jumlah').keyup(function(e) {
                        if ($(`#additional-type-${item_index}`).val() == 'transport') {
                            $(`#additional-quantity-${item_index}`).val(this.value)
                        }
                    })


                    $(`#additional-type-${item_index}`).select2();
                    $(`#additional-item-id-${item_index}`).select2();
                    $(`#additional-jumlah`).select2();
                    $(`#additional-tax-id-${item_index}`).select2();

                    $(`#additional-type-${item_index}`).change(function(e) {
                        e.preventDefault();

                        if (this.value) {
                            $(`#additional-item-id-${item_index}`).removeAttr('disabled');
                            $(`#additional-jumlah`).removeAttr('disabled');
                            $(`#additional-tax-id-${item_index}`).removeAttr('disabled');
                            $(`#additional-harga-${item_index}`).removeAttr('disabled');

                            if (this.value == 'transport') {
                                $(`#additional-quantity-${item_index}`).attr('readonly', 'readonly');
                            } else {
                                $(`#additional-quantity-${item_index}`).removeAttr('readonly');
                            }
                            $(`#jumlah`).removeAttr('disabled');

                            inititemSelect(`additional-item-id-${item_index}`, this.value);

                            initSelect2Search(`additional-tax-id-${item_index}`, `{{ route('admin.select.tax') }}`, {
                                id: "id",
                                text: "name"
                            });

                            let optionTaxes = '';

                            defaultTaxes.map((tax, index) => {
                                optionTaxes += `<option value="${tax.id}" selected>${tax.name}</option>`;
                            });

                            setTimeout(() => {
                                $(`#additional-tax-id-${item_index}`).html(optionTaxes).trigger('change');
                            }, 250);

                            $(`#additional-item-id-${item_index}`).change(function(e) {
                                e.preventDefault();
                                $(`#additional-jumlah-display-${item_index}`).html(formatRupiahWithDecimal($('#jumlah').val()));

                                if ($(this).val()) {
                                    $(`#additiona-item-name-${item_index}`).html($(`#additional-item-id-${item_index}`).select2('data')[0].text);
                                    $.ajax({
                                        type: "get",
                                        url: `{{ route('admin.item.price-latest') }}/${this.value}`,
                                        success: function({
                                            data
                                        }) {
                                            if (data.harga_jual != 0) {
                                                $(`#additional-harga-${item_index}`).val(formatRupiahWithDecimal(data.harga_jual));
                                            }
                                            $(`#additional-harga-${item_index}`).trigger('focus');
                                        }
                                    });
                                } else {
                                    $(`#additiona-item-name-${item_index}`).html();
                                    $(`#additional-harga-${item_index}`).val('');
                                    $(`#price-${index}`).trigger('focus');
                                }
                            });

                            const TaxOnChange = () => {
                                additional_tax_list_value[item_index] = [];
                                tax_list = [];
                                [...document.getElementById(`additional-tax-id-${item_index}`).options].map((option, selected_index) => {
                                    if (option.selected) {
                                        tax_list[selected_index] = option.value;
                                    }
                                });

                                tax_list_value[item_index] = tax_list;
                                $(`#additional-tax-value-${item_index}`).val(tax_list.toString());

                                $(`#additional_tax_data_detail_${item_index}`).html('');
                                $(`#additional_tax_value_detail_${item_index}`).html('')
                                tax_list.map((tax, tax_index) => {
                                    $.ajax({
                                        type: "get",
                                        url: "{{ route('admin.tax.detail') }}/" + tax,
                                        success: ({
                                            data
                                        }) => {
                                            additional_tax_list_value[item_index][tax_index] = data.value;
                                            let new_html = `
                                                    <p>
                                                        <span>${data.name} - ${data.value * 100}%</span>
                                                    </p>`;
                                            $(`#additional_tax_data_detail_${item_index}`).append(new_html);
                                            $(`#additional_tax_value_detail_${item_index}`).append(`
                                                <p>
                                                    <span id="currency-simbol" class="me-10">${currency_symbol}</span>
                                                    <span class="fw-700" id="tax-${tax_index}-${item_index}">${formatRupiahWithDecimal(additional_sub_total_list[item_index] * data.value)}</span>
                                                </p>
                                            `);

                                            calculateSingleAdditional();
                                        }
                                    });
                                });

                                calculateSingleAdditional();

                            };


                            $(`#additional-tax-id-${item_index}`).off('change');
                            initSelect2Search(`additional-tax-id-${item_index}`, `{{ route('admin.select.tax') }}`, {
                                id: "id",
                                text: "name"
                            });
                            $(`#additional-tax-id-${item_index}`).on('change', function(e) {
                                tax_list[index] = this.value ?? null;
                                TaxOnChange();
                            });

                        } else {
                            $(`#additional-item-id-${item_index}`).attr('disabled');
                            $(`#additional-jumlah`).attr('disabled');
                            $(`#additional-tax-id-${item_index}`).attr('disabled');
                            $(`#additional-harga-${item_index}`).attr('disabled', 'disabled');

                            $(`#additional-item-id-${item_index}`).select2('destroy');
                            $(`#additional-tax-id-${item_index}`).select2('destroy');
                            $(`#additional-tax-id-${item_index}`).select2();


                            $(`#additional-tax-id-${item_index}`).html('');
                            $(`#additional-tax-id-${item_index}`).trigger('change');

                            $(`#additiona-item-name-${item_index}`).html('');
                            $(`#additiona-item-price-${item_index}`).html('');
                            $(`#additional-jumlah-display-${item_index}`).html('');
                            $(`#additional-sub_total_${item_index}`).html('');
                            $(`#additional_tax_data_detail_${item_index}`).html('');
                            $(`#additional_tax_value_detail_${item_index}`).html('');
                            $(`#additiona-total-item-${item_index}`).html('');
                        }
                    });

                    const calculateSingleAdditional = () => {
                        let single_total = additional_sub_total_list[item_index];
                        $(`#additionaL-sub_total_${item_index}`).val(single_total);

                        $(`#additional_tax_value_detail_${item_index}`).html('');
                        additional_tax_list_value[item_index].map((value, index) => {
                            single_total += additional_sub_total_list[item_index] * value;

                            $(`#additional_tax_value_detail_${item_index}`).append(`
                                                <p>
                                                    <span id="currency-simbol" class="me-10">${currency_symbol}</span>
                                                    <span class="fw-700" id="tax-${index}-${item_index}">${formatRupiahWithDecimal(additional_sub_total_list[item_index] * value)}</span>
                                                </p>
                                            `);
                        })
                        $(`#additional-total-item-${item_index}`).html(formatRupiahWithDecimal(single_total));

                        calculateAllAdditional();
                    };

                    initCommasForm();

                    $(`#additional-harga-${item_index}`).on('change keyup focus', function(e) {
                        $(`#additiona-item-price-${item_index}`).html(formatRupiahWithDecimal($(this).val() ?? 0));

                        let amount = thousandToFloat($(`#jumlah`).val() ?? 0);
                        let price = thousandToFloat($(this).val() ?? 0);

                        if (price) {
                            additional_sub_total_list[item_index] = amount * price;
                        } else {
                            additional_sub_total_list[item_index] = 0;
                        }

                        calculateSingleAdditional();
                    });

                    $(`#jumlah`).on('change keyup focus', function(e) {
                        $(`#additional-jumlah-display-${item_index}`).html(formatRupiahWithDecimal(this.value));

                        let amount = thousandToFloat($(this).val());
                        let price = thousandToFloat($(`#additional-harga-${item_index}`).val());

                        if (amount) {
                            additional_sub_total_list[item_index] = amount * price;
                        } else {
                            additional_sub_total_list[item_index] = 0;
                        }
                        calculateSingleAdditional();
                    });

                    index++;
                };

                addItem(index);
            };

            initAdditionalItem();
            // additioan item ===========================================================================================
        });
    </script>

    <script>
        sidebarMenuOpen('#trading');
        sidebarActive('#sales-order')
    </script>

    @if (get_current_branch()->is_primary == 1)
        <script>
            initSelect2Search(`branch_id`, `{{ route('admin.select.branch') }}`, {
                id: "id",
                text: "name"
            });
        </script>
    @endif

@endsection
