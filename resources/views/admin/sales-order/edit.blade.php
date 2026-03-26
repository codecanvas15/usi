@extends('layouts.admin.layout.index')

@php
    $main = 'sales-order';
@endphp

@section('title', Str::headline("edit $main") . ' - ')

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
                        {{ Str::headline('edit ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("edit $main")
        <div class="box" id="loading-card">
            <box-body>
                <h2 class="text-center">Loading...</h2>
            </box-body>
        </div>

        <form action="{{ route('admin.sales-order.update', $model) }}" method="post" id="form-update" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <x-card-data-table title="{{ 'Edit ' . $main }}">
                <x-slot name="header_content">

                </x-slot>
                <x-slot name="table_content">
                    @include('components.validate-error')

                    <div id="customer-sh-number">

                    </div>

                </x-slot>
            </x-card-data-table>

            <x-card-data-table title="Trading Item">
                <x-slot name="table_content">
                    <div id="trading-item"></div>
                </x-slot>
            </x-card-data-table>

            <x-card-data-table title="Additional Item">
                <x-slot name="table_content">
                    <div id="additional-item">

                    </div>

                    <div class="row">
                        <div class="col-md-3"></div>
                        <div class="col-md-2"></div>
                        <div class="col-md-2"></div>
                        <div class="col-md-2"></div>
                        <div class="col-md-2"></div>
                        <div class="col-md-1 row align-items-end">
                            <div class="form-group">
                                <x-button color="info" size="sm" icon="plus" fontawesome id="add-additonal-item" />
                            </div>
                        </div>
                    </div>
                </x-slot>
            </x-card-data-table>

            <x-card-data-table>
                <x-slot name="table_content">
                    <div id="currency-data">

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
                                    <td id="main-item-name">-</td>
                                    <td>
                                        <span class="d-flex">
                                            <span class="me-10" id="currency-simbol"></span>
                                            <span class="fw-bold" id="price-main">0</span>
                                            <span id=""></span>
                                        </span>
                                    </td>
                                    <td id="">
                                        <span class="d-flex">
                                            <span class="me-10" id="quantity-main"></span>
                                            <span id=""></span>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="d-flex">
                                            <span class="me-10" id="currency-simbol"></span>
                                            <span class="text-end" id="sub-total-main">0</span>
                                        </span>
                                    </td>
                                </tr>
                            </x-slot>
                            <x-slot name="table_foot">
                                <tr>
                                    <td colspan="3" class="fw-bold text-end">Total</td>
                                    <td class="bg-success">
                                        <div class="align-self-end">
                                            <span class="d-flex">
                                                <span class="me-10" id="currency-simbol"></span>
                                                <span class="fw-bold text-end w-100" id="total-main">0</span>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            </x-slot>
                        </x-table>
                    </div>

                    <div class="mt-30">
                        <h4 class="fw-bold">Additional item</h4>
                        <x-table theadColor='danger' id="table-additional">
                            <x-slot name="table_head">
                                <th>#</th>
                                <th>Item</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>DPP</th>
                                <th>Tax</th>
                                <th>Value</th>
                                <th>Sub Total</th>
                            </x-slot>
                            <x-slot name="table_body">

                            </x-slot>
                            <x-slot name="table_foot">
                                <tr class="d-none">
                                    <td class="text-end" colspan="7">DPP</td>
                                    <td class="d-flex text-end">
                                        <p id="currency-simbol" class="me-10 mb-0"></p>
                                        <h5 class="mb-0 ms-auto" id="additional-dpp-total">0</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-end" colspan="7">Total Pajak</td>
                                    <td class="d-flex text-end">
                                        <p id="currency-simbol" class="me-10 mb-0"></p>
                                        <h5 class="mb-0 ms-auto" id="additional-tax-total">0</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-end" colspan="7">Total</td>
                                    <td class="bg-success text-white d-flex text-end">
                                        <p id="currency-simbol" class="me-10 mb-0"></p>
                                        <h5 class="fw-bold mb-0 ms-auto" id="additional-total">0</h5>
                                    </td>
                                </tr>
                            </x-slot>
                        </x-table>
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
    @can("edit $main")
        <script src="{{ asset('js/admin/select/itemSelect.js') }}"></script>
        <script src="{{ asset('js/form/select2search.js') }}"></script>
        <script src="{{ asset('js/helpers/helpers.js') }}"></script>
        <script>
            $(document).ready(function() {
                $('form#form-update').hide();

                let parent = [],
                    additional_data = [];

                let currency_symbol = '';

                let parent_total = 0,
                    parent_sub_total = 0,
                    parent_tax_list = [], // tax id
                    parent_tax_value = [], // tax decimal value
                    parent_tax_total = 0;

                let additional_last_index = 0,
                    additional_total = 0,
                    additional_sub_total = 0,
                    additional_sub_total_list = [], // price * amount list
                    additional_tax_list = [], // tax id
                    additional_tax_value = [], // tax decimal value
                    additional_tax_total = 0;

                let total_all = 0;

                $.ajax({
                    type: "get",
                    url: "{{ route('admin.sales-order.detail-edit', $model) }}",
                    success: function({
                        data
                    }) {
                        let {
                            model,
                            additional
                        } = data;

                        parent = model;
                        additional_data = additional;

                        initAfterGetData()
                    }
                });

                const initAfterGetData = () => {
                    $('#loading-card').hide(1000);
                    $('form#form-update').show(1000);

                    setDataAfterGetData();
                    displayData();
                    displayCalculation();

                    updateCurrencySymbol();
                    initCommasForm();
                    initCommasForm();
                };

                const setDataMainAfterGetData = () => {
                    let {
                        sub_total,
                        sub_total_after_tax,
                        other_cost,
                        so_trading_detail,
                        sale_order_taxes,
                    } = parent;

                    parent_sub_total = sub_total;
                    parent_total = sub_total_after_tax;
                    parent_tax_total = parent_total - parent_sub_total;

                    sale_order_taxes.map((data, index) => {
                        let {
                            id,
                            tax_id,
                            tax,
                            value,
                        } = data;

                        parent_tax_list.push(tax_id);
                        parent_tax_value[index] = value;
                    });
                };

                const setDataAdditionalAfterGetData = () => {
                    let {
                        other_cost
                    } = parent;

                    additional_total = other_cost;
                    additional_sub_total = 0;
                    additional_tax_total = 0;

                    additional_data.map((data, index) => {
                        additional_tax_list[index] = [];
                        additional_tax_value[index] = [];

                        let {
                            quantity,
                            price,
                            sub_total,
                            total,
                            sale_order_additional_taxes,
                        } = data;

                        additional_sub_total_list.push(parseFloat(sub_total));
                        additional_sub_total += parseFloat(sub_total);

                        sale_order_additional_taxes.map((data_taxes, index_tax) => {
                            let {
                                tax_id,
                                value,
                                total,
                            } = data_taxes;

                            additional_tax_list[index].push(tax_id);
                            additional_tax_value[index].push(value);
                            additional_tax_total += parseFloat(total);
                        });

                        additional_last_index = index;
                    });
                };

                const setDataAfterGetData = () => {
                    setDataAdditionalAfterGetData()
                    setDataMainAfterGetData();

                    total_all = parent_total + additional_total;
                }

                const updateCurrencySymbol = () => {
                    $('span#currency-simbol').each(function() {
                        $(this).html(currency_symbol);
                    });
                }

                const displayData = () => {
                    displayParent();
                    displayAdditionalItems();
                };

                const displayCalculationParent = () => {

                    const initDisplayCalculationParent = () => {
                        displayTrading();
                        displayCalculationTaxMain();
                    };

                    const displayTrading = () => {
                        let {
                            so_trading_detail
                        } = parent;
                        let {
                            harga,
                            jumlah,
                            item
                        } = so_trading_detail;

                        $('#price-main').html(formatRupiahWithDecimal(harga));
                        $('#quantity-main').html(formatRupiahWithDecimal(jumlah));
                        $('#sub-total-main').html(formatRupiahWithDecimal(parent_sub_total));
                        $('#total-main').html(formatRupiahWithDecimal(parent_total));
                        $('#main-item-name').html(item.nama);
                    };

                    const displayCalculationTaxMain = () => {
                        let {
                            sale_order_taxes
                        } = parent;

                        sale_order_taxes.map((data, index) => {
                            let {
                                tax,
                                value,
                                total
                            } = data;

                            let tax_name = tax.name;
                            let tax_total = formatRupiahWithDecimal(total);

                            let html = `
                                <tr id="tax-table-${index}">
                                    <td colspan="3" class="fw-bold text-end">${tax_name} - ${value * 100}%</td>
                                    <td>
                                        <span class="d-flex">
                                            <span class="me-10" id="currency-simbol">${currency_symbol}</span>
                                            <h5 id="tax-${index}" class="text-end w-100">${tax_total}</h5>
                                        </span>
                                    </td>
                                </tr>
                            `;

                            $('#table-total tbody').append(html);
                        });
                    };

                    initDisplayCalculationParent();
                };

                const displayCalculationAdditional = () => {

                    const initCalculationAdditional = () => {
                        displayItemTable();
                        displayTotalTable();
                    };

                    const displayItemTable = () => {
                        additional_data.map((data, index) => {
                            let {
                                quantity,
                                price,
                                sub_total,
                                total,
                                sale_order_additional_taxes,
                                item
                            } = data;

                            let html = `
                                <tr id="additional-resume-${index}">
                                    <th></th>
                                    <td>
                                        <span id="additiona-item-name-${index}">${item.kode} - ${item.nama}</span
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <p id="currency-simbol" class="me-10 mb-0">${currency_symbol}</p>
                                            <h5 class="mb-0 ms-auto" id="additiona-item-price-${index}">${formatRupiahWithDecimal(price)}</h5>
                                        </div
                                    </td>
                                    <td id="additional-jumlah-display-${index}">${formatRupiahWithDecimal(quantity)}</td>
                                    <td>
                                        <div class="d-flex">
                                            <p id="currency-simbol" class="me-10 mb-0">${currency_symbol}</p>
                                            <h5 class="mb-0 ms-auto" id="additional-sub_total_${index}">${formatRupiahWithDecimal(sub_total)}</h5>
                                        </div
                                    </td>
                                    <td id="additional_tax_data_detail_${index}">-</td>
                                    <td id="additional_tax_value_detail_${index}">-</td>
                                    <td>
                                        <div class="d-flex text-end">
                                            <p id="currency-simbol" class="me-10 mb-0">${currency_symbol}</p>
                                            <h5 class="mb-0 ms-auto" id="additiona-total-item-${index}">${formatRupiahWithDecimal(total)}</h5>
                                        </div>
                                    </td>
                                </tr>
                            `;

                            $('#table-additional tbody').append(html);

                            $(`#additional_tax_data_detail_${index}`).html('');
                            $(`#additional_tax_value_detail_${index}`).html('');

                            sale_order_additional_taxes.map((data_taxes, index_tax) => {

                                let {
                                    tax,
                                    value,
                                    total,
                                } = data_taxes;

                                let new_html = `
                                    <p>
                                        <span>${tax.name} - ${value * 100}%</span>
                                    </p>`;
                                $(`#additional_tax_data_detail_${index}`).append(new_html);

                                $(`#additional_tax_value_detail_${index}`).append(`
                                    <p>
                                        <span id="currency-simbol" class="me-10">${currency_symbol}</span>
                                        <span class="fw-700" id="tax-${index_tax}-${index}">${formatRupiahWithDecimal(total)}</span>
                                    </p>
                                `);
                            });
                        });
                    };

                    const displayTotalTable = () => {
                        $('#additional-dpp-total').html(formatRupiahWithDecimal(additional_sub_total));
                        $('#additional-tax-total').html(formatRupiahWithDecimal(additional_tax_total));
                        $('#additional-total').html(formatRupiahWithDecimal(additional_total));
                    };

                    initCalculationAdditional();
                };

                const displayCalculation = () => {
                    displayCalculationParent()
                    displayCalculationAdditional()
                }

                const displayParent = () => {
                    let {
                        branch,
                        customer,
                        currency,
                        sh_number,
                        so_trading_detail,
                        sale_order_taxes,
                        tanggal,
                        nomor_po_external,
                        exchange_rate,
                    } = parent;

                    const updatePrice = () => {
                        $.ajax({
                            type: "get",
                            url: `{{ route('admin.select.select-with-period-and-sh-number-and-search-harga-jual') }}/${$('#item_id').val()}/${$('#sh_number_id').val()}/${$('#tanggal_data').val()}`,
                            success: function({
                                data
                            }) {
                                $('#harga').val(decimalFormatterCommasWithOuNumberWithCommas(data.harga_jual ?? 0));
                                $('#harga').trigger('keyup');
                            }
                        });
                    };

                    const calculateParentData = () => {
                        let harga_trading = thousandToFloat($('#harga').val());
                        let jumlah_trading = thousandToFloat($('#jumlah').val());

                        parent_sub_total = harga_trading * jumlah_trading;
                        parent_total = parent_sub_total;

                        parent_tax_total = 0;
                        parent_tax_value.map((tax_value, tax_index) => {
                            let tax_total = parent_sub_total * tax_value;
                            parent_tax_total += tax_total;
                            parent_total += tax_total;

                            $(`#tax-${tax_index}`).html(formatRupiahWithDecimal(tax_total));
                        });

                        $('#price-main').html(formatRupiahWithDecimal(harga_trading));
                        $('#quantity-main').html(formatRupiahWithDecimal(jumlah_trading));
                        $('#sub-total-main').html(formatRupiahWithDecimal(parent_sub_total));
                        $('#total-main').text(formatRupiahWithDecimal(parent_total));
                    }

                    let parentTimeoutTax;

                    const taxFuction = () => {
                        clearTimeout(parentTimeoutTax);

                        parent_tax_list.map((tax, index) => {
                            $(`#tax-table-${index}`).remove();
                        });

                        parent_tax_list = [];
                        parent_tax_value = [];
                        let html = '';

                        $('#tax_data_detail').html('');
                        [...document.getElementById('tax_id').options].map((option, index) => {
                            if (option.selected) {
                                parent_tax_list.push(option.value);
                            }
                        });

                        parent_tax_list.map((tax_id, tax_index) => {
                            html += `<input type="hidden" name="tax_id[]" value="${tax_id}"/>`;

                            parentTimeoutTax = setTimeout(() => {
                                $.ajax({
                                    type: "get",
                                    url: `{{ route('admin.tax.detail') }}/${tax_id}`,
                                    success: function({
                                        data
                                    }) {
                                        parent_tax_value[tax_index] = data.value;
                                        let new_html = `
                                            <tr id="tax-table-${tax_index}">
                                                <td colspan="3" class="text-end">${data.name} - ${data.value * 100}%</td>
                                                <td>
                                                    <span class="d-flex">
                                                        <p class="me-10" id="currency-simbol">${currency_symbol}</p>
                                                        <h5 id="tax-${tax_index}" class="text-end w-100"></h5>
                                                    </span>
                                                </td>
                                            </tr>`;

                                        $('table#table-total tbody').append(new_html);

                                        calculateParentData()
                                        updateCurrencySymbol();
                                    }
                                });
                            }, 500);

                            calculateParentData();
                            updateCurrencySymbol();
                        });

                        $('#tax_list').html(html);
                    };

                    const displayParentData = () => {
                        displayCustomerAndShNumber();
                        displayCurrency();
                        displayTradingItem();
                    };

                    const displayCustomerAndShNumber = () => {
                        let {
                            id,
                            nama,
                            term_of_payment,
                        } = customer;
                        nomor_po_external = nomor_po_external ?? '';

                        let file_btn = ``;
                        if (parent.quotation) {
                            file_btn = `<x-button color="info" link="{{ url('storage') }}/${parent.quotation}" size="sm" icon="file" fontawesome target="_blank" />`;
                        } else {
                            file_btn = `<x-button badge color="danger" size="sm" icon="eye-slash" label="no file" fontawesome />`;
                        }

                        $('#customer-sh-number').append(`
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input class="datepicker-input" id="tanggal_data" name="tanggal" value="${localDate(tanggal)}" onchange="checkClosingPeriod($(this))"/>
                                    </div>
                                </div>
                                 <div class="col-md-4">
                                    <div class="form-group mb-0">
                                        <label class="mb-2" for="branch_id">Branch <span class="text-danger">*</span></label>
                                        <select class="form-control select2" name="branch_id" id="branch_id" value="" required>
                                            <option value="${branch.id}" selected>${branch.name}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input type="text" id="nomor_po_external" name="nomor_po_external" label="nomor_PO_external" value="${nomor_po_external}" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input type="file" label="quotation" name="quotation" />
                                    </div>
                                    ${file_btn}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <x-select name="customer_id" id="customer_id" label="customer" required disabled>
                                        <option value="${id}" selected>${nama}</option>
                                    </x-select>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input type="text" id="term_of_payment" name="term_of_payment" label="term_of_payment" value="${term_of_payment}" readonly />
                                    </div>
                                </div>
                            </div>
                        `);

                        initDatePicker();

                        checkClosingPeriod($('#tanggal_data'));

                        $('#tanggal_data').change(function(e) {
                            e.preventDefault();
                            // updatePrice();
                        });

                        let drop = '',
                            supply = '';
                        if (sh_number) {
                            let {
                                sh_number_details
                            } = sh_number;


                            sh_number_details.map((data, index) => {
                                if (data.type == 'Supply Point') {
                                    supply = data.alamat;
                                }
                                if (data.type == 'Drop Point') {
                                    drop = data.alamat;
                                }
                            });
                        }
                        $('#customer-sh-number').append(`
                            <div class="row mt-10">
                                <div class="col-md-4">
                                    <x-select name="sh_number_id" id="sh_number_id" label="SH_number" required disabled>
                                        ${sh_number ? `<option value="${sh_number.id}" selected>${sh_number.kode}</option>` : ''}
                                    </x-select>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input type="text" name="supply_point" label="supply_point" value="${supply}" id="supply_point" required readonly />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input type="text" name="drop_point" label="drop_point / ship to" value="${drop}" id="drop_point" required readonly />
                                    </div>
                                </div>
                            </div>
                        `);

                        initSelect2Search('customer_id', "{{ route('admin.select.customer') }}", {
                            id: "id",
                            text: "nama"
                        });

                        $('#customer_id').change(function(e) {
                            e.preventDefault();

                            updatePrice();
                            $('#sh_number_id').select2();
                            $('#sh_number_id').html('');
                            $('#supply_point').html('');
                            $('#drop_point').html('');

                            initSelect2Search('sh_number_id', `{{ route('admin.select.sh-number.customer') }}/${this.value}`, {
                                id: "id",
                                text: "kode,supply_point,drop_point"
                            });

                        });

                        initSelect2Search('sh_number_id', `{{ route('admin.select.sh-number.customer') }}/${id}`, {
                            id: "id",
                            text: "kode,supply_point,drop_point"
                        });

                        $('#sh_number_id').change(function(e) {
                            e.preventDefault();
                            updatePrice();

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
                                }
                            });
                        });
                    };

                    const displayCurrency = () => {
                        let {
                            id,
                            nama,
                            simbol,
                            is_local
                        } = currency;

                        $('#currency-data').append(`
                            <div class="row mt-10">
                                <div class="col-md-4">
                                    <x-select name="currency_id" id="currency_id" label="Currency" value="" required>
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

                        initCommasForm();

                        currency_symbol = simbol;

                        if (is_local) {
                            $('#exchange_rate_trading').attr('readonly', true);
                        }

                        initSelect2Search('currency_id', "{{ route('admin.select.currency') }}", {
                            id: "id",
                            text: "kode,nama,negara"
                        });

                        $('#currency_id').change(function(e) {
                            e.preventDefault();
                            $.ajax({
                                type: "get",
                                url: `{{ route('admin.currency.detail') }}/${this.value}`,
                                success: function({
                                    data
                                }) {
                                    if (data.is_local) {
                                        $('#exchange_rate').val(1);
                                        $('#exchange_rate').attr('readonly', 'readonly');
                                    } else {
                                        $('#exchange_rate').removeAttr('readonly');
                                        $('#exchange_rate').attr('readonly', false);
                                    }

                                    currency_symbol = data.simbol;
                                    updateCurrencySymbol();
                                }
                            });
                        });
                    };

                    const displayTradingItem = () => {
                        let {
                            item,
                            harga,
                            jumlah
                        } = so_trading_detail;

                        const displayTrading = () => {
                            displayTaxTrading();
                        };

                        $('#trading-item').append(`
                            <div class="item-form ">
                                <div class="row" id="item-1">
                                    <div class="col-md-3">
                                        <x-select name="item_id[]" id="item_id" label="item" value="" required>
                                            <option value="${item.id}" selected>${item.kode} - ${item.nama}</option>
                                        </x-select>
                                        <p class="wrapper_information_unit"><span class="text-primary">${item.unit.name}</span></p>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="text" name="price[]" id="harga" label="harga" value="${formatRupiahWithDecimal(harga,3)}" class="commas-form-three text-end" helpers="" required />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="col-md">
                                            <div class="form-group">
                                                <x-input type="text" name="jumlah[]" id="jumlah" label="Qty" value="${formatRupiahWithDecimal(jumlah)}" class="commas-form" helpers="" required />
                                                <p class="wrapper_information_unit"><span class="text-primary">${item.unit.name}</span></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <x-select name="tax_id_" id="tax_id" label="Pajak" value="" multiple>

                                        </x-select>
                                        <div id="tax_list">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);

                        initCommasForm();
                        initCommasFormThreeDigits();

                        inititemSelect('item_id', 'trading');

                        $('#item_id').change(function(e) {
                            e.preventDefault();
                            updatePrice();
                        });

                        const displayTaxTrading = () => {
                            sale_order_taxes.map((data, index) => {
                                $('#tax_id').append(`
                                    <option value="${data.tax_id}" selected>${data.tax.name}</option>
                                `);

                                $('#tax_list').append(`
                                    <input type="hidden" name="tax_id[]" value="${data.tax_id}">
                                `);
                            });

                            initSelect2Search('tax_id', "{{ route('admin.select.tax') }}", {
                                id: "id",
                                text: "name"
                            });

                            $('#tax_id').change(function(e) {
                                e.preventDefault();
                                taxFuction();
                            });

                        };
                        displayTrading();

                        $('#harga').keyup(function(e) {
                            e.preventDefault();
                            calculateParentData()
                        });

                        $('#jumlah').keyup(function(e) {
                            e.preventDefault();
                            calculateParentData();
                        });
                    };

                    displayParentData();
                };

                const displayAdditionalItems = () => {

                    const calculateAdditionalItem = () => {

                        additional_total = 0;
                        additional_sub_total = 0;
                        additional_tax_total = 0;

                        additional_sub_total_list.map((sub_total, sub_total_index) => {

                            let single_sub_total = sub_total;
                            let single_tax = 0;
                            additional_sub_total += sub_total;

                            additional_tax_value[sub_total_index].map((tax, tax_index) => {
                                single_tax = single_sub_total * tax;

                                let tax_total = sub_total + single_tax;
                                additional_total += tax_total;
                                additional_tax_total += single_tax;

                                $(`#tax-${tax_index}-${sub_total_index}`).html(formatRupiahWithDecimal(single_tax));

                            });

                            let single_total = single_sub_total + single_tax;

                            $(`#additional-sub_total_${sub_total_index}`).html(formatRupiahWithDecimal(single_sub_total));
                            $(`#additiona-total-item-${sub_total_index}`).html(formatRupiahWithDecimal(single_total));
                        });

                        additional_total = additional_sub_total + additional_tax_total;

                        $('#additional-dpp-total').text(formatRupiahWithDecimal(additional_sub_total));
                        $('#additional-tax-total').text(formatRupiahWithDecimal(additional_tax_total));
                        $('#additional-total').text(formatRupiahWithDecimal(additional_total));
                    };

                    additional_data.map((data, index) => {
                        let {
                            item,
                            sale_order_additional_taxes,
                            price,
                            quantity,
                        } = data;

                        additional_last_index++;

                        const displayAdditonal = () => {
                            displayTaxAdditionalItem();
                        };

                        let additionalTimeoutTax;

                        const taxFunction = () => {
                            clearTimeout(additionalTimeoutTax);

                            additional_tax_value[index] = [];
                            additional_tax_list[index] = [];
                            [...document.getElementById(`additional-tax-id-${index}`).options].map((option, selected_index) => {
                                if (option.selected) {
                                    additional_tax_list[index][selected_index] = option.value;
                                }
                            });

                            $(`#additional-tax-value-${index}`).val(additional_tax_list[index].toString());

                            $(`#additional_tax_data_detail_${index}`).html('');
                            $(`#additional_tax_value_detail_${index}`).html('')
                            additional_tax_list[index].map((tax, tax_index) => {

                                additionalTimeoutTax = setTimeout(() => {
                                    $.ajax({
                                        type: "get",
                                        url: "{{ route('admin.tax.detail') }}/" + tax,
                                        success: ({
                                            data
                                        }) => {
                                            additional_tax_value[index][tax_index] = data.value;
                                            let new_html = `<p>
                                                <span>${data.name} - ${data.value * 100}%</span>
                                            </p>`;

                                            $(`#additional_tax_data_detail_${index}`).append(new_html);
                                            $(`#additional_tax_value_detail_${index}`).append(`
                                                <p>
                                                    <span id="currency-simbol" class="me-10">${currency_symbol}</span>
                                                    <span class="fw-700" id="tax-${tax_index}-${index}">${formatRupiahWithDecimal(additional_sub_total_list[index] * data.value)}</span>
                                                </p>
                                            `);

                                            calculateAdditionalItem();
                                        }
                                    });
                                }, 500);
                            });

                            calculateAdditionalItem();
                        };

                        $('#additional-item').append(`
                            <div class="row" id="additional-item-${index}">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-select name="item_type[]" id="additional-type-${index}" label="Type">
                                            <option value="">Pilih Item</option>
                                            <option value="transport" ${item.type == 'transport' ? 'selected' : ''}>Transport</option>
                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-select name="additional_item[]" id="additional-item-id-${index}" label="item" >
                                            <option value="${item.id}" selected>${item.kode} - ${item.nama}</option>
                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input type="text" name="additional_price[]" label="harga" value="${numberWithDot(decimalFormatterWithOuNumberWithCommas(price))}" id="additional-harga-${index}" class="text-end commas-form"  />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-select name="additional_tax_id[]" id="additional-tax-id-${index}" label="Pajak" multiple >

                                        </x-select>
                                        <input type="hidden" name="additional_tax[]" id="additional-tax-value-${index}" value="">
                                    </div>
                                </div>
                                <div class="col-md-1 row align-items-end">
                                    <div class="form-group">
                                        <x-button color="danger" icon="trash" fontawesome size="sm" id="delete-additional-item-${index}" />
                                    </div>
                                </div>
                            </div>
                        `);

                        initCommasForm();

                        $(`#additional-type-${index}`).off('change').change(function(e) {
                            e.preventDefault();
                            inititemSelect(`additional-item-id-${index}`, this.value);
                        });

                        $(`#additional-item-id-${index}`).off('change').change(function(e) {
                            e.preventDefault();
                            if ($(this).val()) {
                                $(`#additiona-item-name-${item_index}`).html($(`#additional-item-id-${item_index}`).select2('data')[0].text);
                                $(`#additional-jumlah-display-${item_index}`).html(formatRupiahWithDecimal($('#jumlah').val()));
                                $.ajax({
                                    type: "get",
                                    url: `{{ route('admin.item.price-latest') }}/${this.value}`,
                                    success: function({
                                        data
                                    }) {
                                        $(`#additional-harga-${item_index}`).val(numberWithDot(decimalFormatterWithOuNumberWithCommas(data.harga_jual)));
                                        $(`#additional-harga-${item_index}`).trigger('focus');
                                        calculateAdditionalItem();
                                    }
                                });
                            } else {
                                $(`#additiona-item-name-${item_index}`).html();
                                $(`#additional-harga-${item_index}`).val(0);
                                $(`#price-${index}`).trigger('focus');
                                calculateAdditionalItem();
                            }
                        });

                        $(`#additional-type-${index}`).select2();
                        inititemSelect(`additional-item-id-${index}`, $(`#additional-type-${index}`).val());
                        $(`#additional-type-${index}`).change(function(e) {
                            e.preventDefault();
                            inititemSelect(`additional-item-id-${index}`, $(this).val());
                        });

                        const displayTaxAdditionalItem = () => {
                            let single_additional_tax_list = [];

                            sale_order_additional_taxes.map((data) => {
                                single_additional_tax_list.push(data.tax_id);
                                $(`#additional-tax-id-${index}`).append(`
                                    <option value="${data.tax_id}" selected>${data.tax.name}</option>
                                `);
                            });

                            $(`#additional-tax-value-${index}`).val(single_additional_tax_list.toString());
                            initSelect2Search(`additional-tax-id-${index}`, `{{ route('admin.select.tax') }}`, {
                                id: "id",
                                text: "name"
                            });
                        };

                        $(`#delete-additional-item-${index}`).click(function(e) {
                            e.preventDefault();
                            deleteAdditionalItem(index);
                        });

                        displayAdditonal()

                        $(`#additional-tax-id-${index}`).change(function(e) {
                            taxFunction()
                        });

                        $(`#additional-harga-${index}`).keyup(function(e) {
                            let value = thousandToFloat(this.value);
                            let quantity = thousandToFloat($(`#jumlah`).val());

                            additional_sub_total_list[index] = value * quantity;
                            calculateAdditionalItem();

                            $(`#additiona-item-price-${index}`).html(formatRupiahWithDecimal(value));
                        });
                        $(`#jumlah`).keyup(function(e) {
                            let value = thousandToFloat(this.value);

                            additional_sub_total_list[index] = value * thousandToFloat($(`#additional-harga-${index}`).val());
                            calculateAdditionalItem();

                            $(`#additional-jumlah-display-${index}`).html(formatRupiahWithDecimal(value));
                        });
                    });

                    const addAdditionalItem = (item_index) => {
                        let btn = '';
                        additional_last_index++;
                        additional_sub_total_list[item_index] = 0;
                        additional_tax_value[item_index] = [];
                        additional_tax_value[item_index] = [];

                        btn = `<x-button color="danger" icon="trash" fontawesome size="sm" id="delete-additional-item-${item_index}" />`;

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
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input type="text" name="additional_price[]" label="harga" id="additional-harga-${item_index}" class="text-end commas-form" disabled />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-select name="additional_tax_id[]" id="additional-tax-id-${item_index}" label="Tax" multiple disabled>

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

                        initCommasForm();

                        $('#additional-item').append(html);

                        // * table =============================================================================================================================
                        $('#table-additional tbody').append(`
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
                                <td>
                                    <div class="d-flex">
                                        <p id="currency-simbol" class="me-10 mb-0">${currency_symbol}</p>
                                        <h5 class="mb-0 ms-auto" id="additional-sub_total_${item_index}">${0}</h5>
                                    </div
                                </td>
                                <td id="additional_tax_data_detail_${item_index}">-</td>
                                <td id="additional_tax_value_detail_${item_index}">-</td>
                                <td>
                                    <div class="d-flex text-end">
                                        <p id="currency-simbol" class="me-10 mb-0">${currency_symbol}</p>
                                        <h5 class="mb-0 ms-auto" id="additiona-total-item-${item_index}">${0}</h5>
                                    </div>
                                </td>
                            </tr>
                        `);
                        // * /table =============================================================================================================================

                        // delete item
                        $(`#delete-additional-item-${item_index}`).click(function(e) {
                            e.preventDefault();
                            deleteAdditionalItem(item_index);
                        });

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
                                $(`#jumlah`).removeAttr('disabled');

                                inititemSelect(`additional-item-id-${item_index}`, this.value);

                                initSelect2Search(`additional-tax-id-${item_index}`, `{{ route('admin.select.tax') }}`, {
                                    id: "id",
                                    text: "name"
                                });

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
                                                $(`#additional-harga-${item_index}`).val(numberWithDot(decimalFormatterWithOuNumberWithCommas(data.harga_jual)));
                                                $(`#additional-harga-${item_index}`).trigger('focus');
                                            }
                                        });
                                    } else {
                                        $(`#additiona-item-name-${item_index}`).html();
                                        $(`#additional-harga-${item_index}`).val(0);
                                        $(`#price-${index}`).trigger('focus');
                                    }
                                });

                                const TaxOnChange = () => {
                                    additional_tax_value[item_index] = [];
                                    additional_tax_list[item_index] = [];
                                    [...document.getElementById(`additional-tax-id-${item_index}`).options].map((option, selected_index) => {
                                        if (option.selected) {
                                            additional_tax_list[item_index][selected_index] = option.value;
                                        }
                                    });

                                    $(`#additional-tax-value-${item_index}`).val(additional_tax_list[item_index].toString());

                                    $(`#additional_tax_data_detail_${item_index}`).html('');
                                    $(`#additional_tax_value_detail_${item_index}`).html('')
                                    additional_tax_list[item_index].map((tax, tax_index) => {
                                        $.ajax({
                                            type: "get",
                                            url: "{{ route('admin.tax.detail') }}/" + tax,
                                            success: ({
                                                data
                                            }) => {
                                                additional_tax_value[item_index][tax_index] = data.value;
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

                                                calculateAdditionalItem();
                                            }
                                        });
                                    });

                                    calculateAdditionalItem();

                                };

                                $(`#additional-tax-id-${item_index}`).change(function(e) {
                                    TaxOnChange();
                                });

                            } else {
                                $(`#additional-item-id-${item_index}`).attr('disabled');
                                $(`#additional-jumlah`).attr('disabled');
                                $(`#additional-tax-id-${item_index}`).attr('disabled');
                                $(`#additional-harga-${item_index}`).attr('disabled', 'disabled');
                                $(`#jumlah`).attr('disabled', 'disabled');

                                $(`#additional-item-id-${item_index}`).select2('destroy');
                                $(`#additional-tax-id-${item_index}`).select2('destroy');
                                $(`#additional-tax-id-${item_index}`).select2();
                            }
                        });

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
                            calculateAdditionalItem();
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
                            calculateAdditionalItem();
                        });

                    };

                    $('#add-additonal-item').click(function(e) {
                        e.preventDefault();
                        addAdditionalItem(additional_last_index += 1);
                    });

                    if (additional_last_index == 0) {
                        addAdditionalItem(0);
                    }

                    const deleteAdditionalItem = (index) => {
                        $(`#additional-item-${index}`).remove();
                        $(`#additional-resume-${index}`).remove();
                        additional_sub_total_list[index] = 0;
                        additional_tax_list[index] = [];
                        additional_tax_value[index] = [];

                        calculateAdditionalItem();
                    };
                };

            });
        </script>
    @endcan
    <script>
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#trading');
        sidebarActive('#sales-order');
    </script>

    @if (get_current_branch()->is_primary == 1)
        <script>
            setTimeout(() => {
                initSelect2Search(`branch_id`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                });
            }, 3000);
        </script>
    @endif
@endsection
