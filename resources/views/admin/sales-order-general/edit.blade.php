@extends('layouts.admin.layout.index')

@php
    $main = 'sales-order-general';
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
                        <a href="{{ route('admin.sales-order.index') }}">{{ Str::headline($main) }}</a>
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
    @can("create $main")
        <div class="box" id="loading-card">
            <div class="box-body">
                <h2 class="text-center">Loading...</h2>
            </div>
        </div>

        <form action="{{ route("admin.$main.update", $model) }}" method="post" id="form-update" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <x-card-data-table title="{{ 'edit ' . $main }}">
                <x-slot name="header_content">

                </x-slot>
                <x-slot name="table_content">
                    @include('components.validate-error')

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" name="kode" label="kode" value="{{ $model->kode }}" id="" required readonly />
                            </div>
                        </div>
                    </div>

                    <div id="customer-form">

                    </div>

                    <div id="currency-form">

                    </div>

                    <div id="sales-order-items" class="mt-30 py-30">

                    </div>

                    <div class="row">
                        <div class="col-md-12 text-end">
                            <x-button type="button" color="info" id="add-sale-order" label="tambah item" icon="plus" fontawesome size="sm" />
                        </div>
                    </div>

                    <div class="row mt-20">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="tax_data[]" label="Pajak" id="tax-selectForm" multiple>
                                    @foreach ($tax_data as $tax)
                                        <option value="{{ $tax->tax_id }}" selected>{{ $tax->tax->name }}</option>
                                    @endforeach
                                </x-select>
                            </div>
                        </div>
                    </div>

                    <div id="table_counter_sog">

                    </div>

                </x-slot>
                <x-slot name="footer">
                    <div class="d-flex justify-content-end gap-2">
                        <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" />
                        <x-button type="submit" color="primary" id="save-other-trasaction-edit" label="Save" />
                    </div>
                </x-slot>

            </x-card-data-table>
        </form>
    @endcan
@endsection

@section('js')

    @can("create $main")
        <script src="{{ asset('js/admin/select/itemSelect.js') }}"></script>
        <script src="{{ asset('js/form/select2search.js') }}"></script>
        <script src="{{ asset('js/helpers/helpers.js') }}"></script>

        <script>
            $(document).ready(function() {
                // init variables =============================================================
                let currency_symbol = '{{ get_local_currency()->simbol }}',
                    sub_total_list = [],
                    price_list = [],
                    amount_list = [],
                    price_before_discount_list = [],
                    discount_list = [],
                    total_list = [],
                    total = 0,
                    sub_total = 0,
                    total_tax = 0,
                    tax_list_value = [],
                    tax_list_id = [];

                $('form#form-update').hide();

                let parent = [],
                    child = [];

                const taxFunction = () => {
                    calculateAll();
                }

                const displayAllData = () => {
                    initSelect2Search(`tax-selectForm`, "{{ route('admin.select.tax') }}", {
                        id: "id",
                        text: "name"
                    })
                    displayParent();
                    displatAdditionalData();
                    calculateAll()
                };

                $.ajax({
                    type: "get",
                    url: "{{ route('admin.sale-order-general.detail-edit', $model) }}",
                    success: function({
                        data
                    }) {
                        let {
                            model,
                            items
                        } = data;

                        if (items.length > 0) {
                            items.map((data, i) => {
                                sub_total_list[i + 500] = data.sub_total
                            })
                        }

                        tax_list_value = model.tax_data.map((data) => data.tax);
                        tax_list_id = model.tax_data.map((data) => data.tax_id);

                        parent = model;
                        child = items;

                        $('#loading-card').hide(1000);
                        $('form#form-update').show(1000);
                        displayAllData()
                    }
                });

                const displayParent = () => {
                    let {
                        customer,
                        currency,
                        exchange_rate,
                        tanggal,
                        no_po_external,
                        quotation,
                        drop_point,
                    } = parent;

                    const displayParentData = () => {
                        displayCurrency();
                        displayCustomer();
                        displayTable();
                    };

                    const displayCustomer = () => {
                        let {
                            id,
                            nama,
                        } = customer;

                        $('#customer-form').append(`
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input type="text" name="no_po_external" label="nomor po" value="${no_po_external ?? ''}" id="no_po_external" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-select name="customer_id" label="customer" id="customer-id" required autofocus>
                                            <option value="${id}">${nama}</option>
                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input class="datepicker-input" name="tanggal" label="tanggal" id="date" value="${localDate(tanggal)}" onchange="checkClosingPeriod($(this))" required />
                                    </div>
                                </div>
                                 <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input type="text" name="drop_point" label="drop point/ship to" id="drop_point" value="${drop_point ?? ''}" />
                                    </div>
                                </div>
                            </div>
                        `);

                        initDatePicker();

                        checkClosingPeriod($('#date'));
                        initSelect2Search(`customer-id`, `{{ route('admin.select.customer') }}`, {
                            id: "id",
                            text: "nama"
                        });
                    };

                    const displayTable = () => {
                        $('#table_counter_sog').html(`
                        <div class="mt-30">
                            <x-table theadColor="danger" id="table-calculate">
                                <x-slot name="table_head">
                                    <th>Item</th>
                                    <th>Harga Sebelum Diskon</th>
                                    <th>Diskon</th>
                                    <th>Harga</th>
                                    <th>Qty</th>
                                    <th>Sub Total</th>
                                    <th>Tax</th>
                                    <th>Value</th>
                                    <th>Total</th>
                                </x-slot>
                                <x-slot name="table_body">

                                </x-slot>
                                <x-slot name="table_foot">
                                    <tr>
                                        <th colspan="8" class="text-end">DPP</th>
                                        <td class="text-end" id="dpp-total">0</th>
                                    </tr>
                                    <tr>
                                        <th colspan="8" class="text-end">Total pajak</th>
                                        <td class="text-end" id="pajak-total">0</th>
                                    </tr>
                                    <tr>
                                        <th colspan="8" class="text-end fw-bold">Total</th>
                                        <th class="bg-success text-white text-end" id="total">0</th>
                                    </tr>
                                </x-slot>
                            </x-table>
                        </div>
                        `)

                        calculateAll()

                        child.map((data, index) => {
                            let {
                                unit,
                                item,
                                price_before_discount,
                                discount,
                                price,
                                sale_order_general_detail_taxes,
                                amount,
                                sub_total,
                                total
                            } = data

                            price_before_discount_list[index + 500] = price_before_discount;
                            discount_list[index + 500] = discount;
                            price_list[index + 500] = price;
                            amount_list[index + 500] = amount;
                            sub_total_list[index + 500] = sub_total;
                            total_list[index + 500] = total;

                            html = `
                            <tr id="row-table-${index + 500}">
                                <td id="item-table-${index + 500}">${item.kode} - ${item.nama}</td>
                                <td class="text-end" id="price-before-discount-table-${index + 500}">${formatRupiahWithDecimal(price_before_discount)}</td>
                                <td class="text-end" id="discount-table-${index + 500}">${formatRupiahWithDecimal(discount)}</td>
                                <td class="text-end" id="harga-table-${index + 500}">${formatRupiahWithDecimal(price)}</td>
                                <td class="text-end" id="jumlah-table-${index + 500}">${formatRupiahWithDecimal(amount)}</td>
                                <td class="text-end" id="sub-total-table-${index + 500}">${formatRupiahWithDecimal(sub_total)}</td>
                                <td id="tax-table-${index + 500}">-</td>
                                <td class="text-end" id="value-tax-table-${index + 500}">-</td>
                                <td class="text-end" id="total-${index + 500}"></td>
                            </tr>
                        `;

                            $('table#table-calculate tbody').append(html);

                            const displayTaxAdditional = () => {
                                let tax_additional_list_sigle = [];
                                $(`#tax-table-${index + 500}`).html('')
                                $(`#value-tax-table-${index + 500}`).html('')
                                sale_order_general_detail_taxes.map((data, index2) => {
                                    tax_additional_list_sigle.push(data.tax_id);
                                    $(`#tax-table-${index + 500}`).append(`
                                        <p>${data?.tax?.name}</p>
                                    `);
                                    $(`#value-tax-table-${index + 500}`).append(`
                                        <p>${data?.tax?.value}</p>
                                    `)
                                });
                                initSelect2Search(`tax-id-${index}`,
                                    `{{ route('admin.select.tax') }}`, {
                                        id: "id",
                                        text: "name"
                                    });
                            }

                            displayTaxAdditional()

                        })
                    }

                    const displayCurrency = () => {
                        let {
                            id,
                            nama,
                            simbol,
                            is_local
                        } = currency;


                        var quotation_input = `<x-input type="file" label="quotation" name="quotation" required />`;
                        if (quotation) {
                            quotation_input = `<x-input type="file" label="quotation" name="quotation" />`;
                            var quotation_url = base_url + '/storage/' + quotation;
                            quotation_input += `<a href="${quotation_url}" target="_blank" class="btn btn-sm btn-primary">Show Document</a>`
                        }

                        var include_tax_html = `<x-input-checkbox label="Include PPN" name="is_include_tax" id="is_include_tax" value="1" />`;
                        if (parent.is_include_tax) {
                            include_tax_html = `<x-input-checkbox label="Include PPN" name="is_include_tax" id="is_include_tax" value="1" checked />`;
                        }

                        $('#currency-form').append(`
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-select name="currency_id" label="currency" id="currency-id" required>
                                            <option value="${id}" selected>${nama}</option>
                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input type="text" name="exchange_rate" id="exchange-rate" value="${formatRupiahWithDecimal(exchange_rate)}" class="commas-form" required />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        ${quotation_input}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    ${include_tax_html}
                                </div>
                            </div>
                        `);

                        $('#is_include_tax').click(function(e) {
                            taxFunction();
                        })

                        if (is_local) {
                            $('#exchange-rate').attr('readonly', true);
                        }

                        currency_symbol = simbol;

                        initSelect2Search('currency-id', "{{ route('admin.select.currency') }}", {
                            id: "id",
                            text: "kode,nama,negara"
                        });

                        $('#currency-id').change(function(e) {
                            e.preventDefault();
                            $.ajax({
                                type: "get",
                                url: `{{ route('admin.currency.detail') }}/${this.value}`,
                                success: function({
                                    data
                                }) {
                                    if (data.is_local) {
                                        $('#exchange-rate').val(1);
                                        $('#exchange-rate').attr('readonly', 'readonly');
                                    } else {
                                        $('#exchange-rate').removeAttr('readonly');
                                        $('#exchange-rate').attr('readonly', false);
                                    }

                                    currency_symbol = data.simbol;
                                    updateCurrencySymbol();
                                }
                            });
                        });
                    };

                    displayParentData();
                };

                const update_table = () => {
                    sub_total_list.map((sub_total, index) => {
                        $('#price-before-discount-table-' + index).html(formatRupiahWithDecimal(price_before_discount_list[index] ?? 0));
                        $('#discount-table-' + index).html(formatRupiahWithDecimal(discount_list[index] ?? 0));
                        $('#harga-table-' + index).html(formatRupiahWithDecimal(price_list[index] ?? 0));
                        $('#sub-total-table-' + index).html(formatRupiahWithDecimal(sub_total_list[index] ?? 0));

                        $(`#tax-table-${index}`).html('');
                        $(`#value-tax-table-${index}`).html('');

                        tax_list_value.map((tax, tax_index) => {
                            $(`#tax-table-${index}`).append(`<p>
                                                                <span>${tax.name} - ${(tax.value * 100).toFixed(2)}%</span>
                                                            </p>`);

                            $(`#value-tax-table-${index}`).append(`<p>
                                                                <span id="tax-${tax_index}-${index}">${formatRupiahWithDecimal((sub_total_list[index] * tax.value).toFixed(2))}</span>
                                                            </p>`)
                        });

                        $('#total-' + index).html(formatRupiahWithDecimal(total_list[index] ?? 0));
                    })
                }

                const calculateAll = () => {
                    total = 0;
                    sub_total = 0;
                    total_tax = 0;

                    sub_total_list.map((sub_total_data, index) => {
                        let original_price = price_before_discount_list[index] - discount_list[index];
                        let price = original_price;


                        if ($('#is_include_tax').is(':checked')) {
                            let tax_percentage = 0;
                            tax_list_value.map((tax, tax_index) => {
                                tax_percentage += tax.value * 100
                            });

                            price -= (original_price - (100 / (100 + tax_percentage) * original_price));
                            price_list[index] = price;
                            $(`#final-price-${index}`).val(formatRupiahWithDecimal(price));
                        } else {
                            price_list[index] = original_price;
                            $(`#final-price-${index}`).val(formatRupiahWithDecimal(original_price));
                        }

                        let single_total = price_list[index] * amount_list[index];
                        sub_total_list[index] = single_total;
                        sub_total += single_total

                        if (tax_list_value) {
                            let single_total_tax = 0;
                            tax_list_value.map((tax, tax_index) => {
                                single_total_tax += price_list[index] * amount_list[index] * tax.value;
                            })
                            single_total += single_total_tax;
                            total_tax += single_total_tax;
                            total_list[index] = single_total;
                        }

                        total += single_total;
                    })


                    $('#dpp-total').html(`
                            <span class="w-100 text-end" id="total-value">${formatRupiahWithDecimal(sub_total)}</span>
                    `);

                    $('#pajak-total').html(`
                            <span class="w-100 text-end" id="total-value">${formatRupiahWithDecimal(total_tax)}</span>
                    `);

                    $('#total').html(`
                            <span class="w-100 text-end" id="total-value">${formatRupiahWithDecimal(sub_total + total_tax)}</span>
                    `);

                    update_table();
                };

                const addItem = (item_index) => {
                    sub_total_list[item_index] = 0;

                    let item_unit = '';
                    let item_stocks = [];
                    let item_data = [];

                    let btn = '';
                    let btn_modal =
                        `<x-button color="info" id="detail-stock-${item_index}" icon="eye" fontawesome size="sm" />`;


                    btn = `<div class="col-md-2 row align-items-end">
                                        <div class="form-group">
                                            <x-button color="danger" id="delete-sale-order-${item_index}" icon="trash" fontawesome size="sm" />
                                            ${btn_modal}
                                        </div>
                                    </div>`;

                    let html = `
                            <div class="row border-bottom mt-20" id="sale-order-item-${item_index}">
                                <input type="hidden" name="id_so_item[${item_index}]" value="" />
                                <input type="hidden" name="sale_order_general_detail_id[${item_index}]" value="" />
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-select name="item_id[${item_index}]" label="item" id="item-id-${item_index}" required>

                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                         <x-select name="unit_id[${item_index}]" label="satuan" id="unit-id-${item_index}" required>
                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input type="text" name="price_before_discount[${item_index}]" label="harga sebelum diskon" id="price-before-discount-${item_index}" class="commas-form" required />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input type="text" name="discount[${item_index}]" label="diskon" id="discount-${item_index}" class="commas-form" required />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input type="text" name="price[${item_index}]" label="harga" id="harga-${item_index}" class="commas-form" required readonly />
                                        <input type='hidden' name="final_price[${item_index}]" id="final-price-${item_index}" />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input type="text" name="amount[${item_index}]" label="jumlah" id="jumlah-${item_index}" class="commas-form" required />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <x-text-area name="notes[]" label="keterangan" id="keterangan-${item_index}"></x-text-area>
                                        </div>
                                    </div>
                                </div>
                                ${btn}
                            </div>
                        `;

                    $('#sales-order-items').append(html);

                    html = `
                            <tr id="row-table-${item_index}">
                                <td id="item-table-${item_index}"></td>
                                <td class="text-end" id="price-before-discount-table-${item_index}">-</td>
                                <td class="text-end" id="discount-table-${item_index}">-</td>
                                <td class="text-end" id="harga-table-${item_index}">-</td>
                                <td class="text-end" id="jumlah-table-${item_index}">-</td>
                                <td class="text-end" id="sub-total-table-${item_index}">-</td>
                                <td id="tax-table-${item_index}">-</td>
                                <td class="text-end" id="value-tax-table-${item_index}">-</td>
                                <td class="text-end" id="total-${item_index}">-</td>
                            </tr>
                        `;

                    $('table#table-calculate tbody').append(html);

                    $(`#price-before-discount-${item_index}`).on('keyup', function(e) {
                        let discount = $(`#discount-${item_index}`).val() ?? 0;
                        let price = thousandToFloat($(this).val()) - thousandToFloat(discount);
                        price_before_discount_list[item_index] = thousandToFloat($(this).val());
                        price_list[item_index] = price;
                        discount_list[item_index] = thousandToFloat(discount);

                        $(`#harga-${item_index}`).val(formatRupiahWithDecimal(price)).trigger('keyup');
                        $(`#price-before-discount-table-${item_index}`).html(formatRupiahWithDecimal(price_before_discount_list[item_index]));
                        $(`#discount-table-${item_index}`).html(formatRupiahWithDecimal(discount_list[item_index]));
                    });

                    $(`#discount-${item_index}`).on('keyup', function(e) {
                        let price_before_discount = $(`#price-before-discount-${item_index}`).val();
                        let price = thousandToFloat(price_before_discount) - thousandToFloat($(this).val());

                        price_before_discount_list[item_index] = thousandToFloat(price_before_discount);
                        price_list[item_index] = price;
                        discount_list[item_index] = thousandToFloat($(this).val());

                        $(`#harga-${item_index}`).val(formatRupiahWithDecimal(price)).trigger('keyup');
                        $(`#price-before-discount-table-${item_index}`).html(formatRupiahWithDecimal(price_before_discount_list[item_index]));
                        $(`#discount-table-${item_index}`).html(formatRupiahWithDecimal(discount_list[item_index]));
                    });

                    const deleteItem = (item_index) => {
                        $(`#sale-order-item-${item_index}`).remove();
                        $(`#row-table-${item_index}`).remove();
                        sub_total_list[item_index] = 0;
                        tax_list_value[item_index] = [];

                        calculateTotal();
                        calculateAll();
                    }

                    const calculateTotal = () => {
                        let single_sub_total = sub_total_list[item_index];
                        let single_total = single_sub_total;

                        if (tax_list_value) {
                            tax_list_value.map((tax, index) => {
                                console.log(tax);
                                single_total += single_sub_total * tax.value;

                                $(`#tax-${index}-${item_index}`).html(formatRupiahWithDecimal(single_sub_total * tax.value));
                            })
                        }

                        $(`#sub-total-table-${item_index}`).html(formatRupiahWithDecimal(single_sub_total));
                        $(`#total-${item_index}`).html(`
                                <div class="d-flex">
                                    <span id="currency-simbol" class="me-10">${currency_symbol}</span>
                                    <span class="w-100 text-end" id="total-value-${item_index}">${formatRupiahWithDecimal(single_total)}</span>
                                </div>
                            `);

                        calculateAll();
                    };


                    $(`#delete-sale-order-${item_index}`).click(function(e) {
                        e.preventDefault();
                        deleteItem(item_index);
                    })

                    $(`#detail-stock-${item_index}`).click(function(e) {
                        e.preventDefault();

                        $('#table-detail-stock tbody').html('');

                        if (item_stocks.lenght == 0) {
                            $('#table-detail-stock tbody').append(`
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada data</td>
                                    </tr>
                                `)
                        } else {
                            item_stocks.map((item, index) => {
                                let {
                                    warehouse,
                                    stock
                                } = item;

                                $('#table-detail-stock tbody').append(`
                                        <tr>
                                            <td>${index+=1}</td>
                                            <td>${$(`#item-id-${item_index}`).select2('data')[0].text}</td>
                                            <td>${warehouse.nama}</td>
                                            <td>${warehouse.branch?.name}</td>
                                            <td>${decimalFormatter(stock)} ${item_unit}</td>
                                        </tr>
                                    `)
                            });
                        }

                        $('#modal-check-stock').modal('show');
                    });

                    inititemSelect(`item-id-${item_index}`, '', 'purchase item, service');

                    initSelect2Search(`tax-id-${item_index}`, `{{ route('admin.select.tax') }}`, {
                        id: "id",
                        text: "name"
                    });

                    $(`#tax-id-${item_index}`).change(function(e) {
                        e.preventDefault();

                        let tax_id = $(this).val();
                        taxFunction();
                    });

                    $(`#item-id-${item_index}`).change(function(e) {
                        e.preventDefault();
                        $(`#item-table-${item_index}`).html($(this).select2('data')[0].text);

                        $.ajax({
                            type: "get",
                            url: "{{ route('admin.item.price-latest') }}/" + $(this).val(),
                            success: function({
                                data
                            }) {

                                if (data) {
                                    $(`#harga-${item_index}`).val(
                                        formatRupiahWithDecimal(data.harga_jual));
                                    $(`#harga-${item_index}`).trigger('keyup');
                                }
                            }
                        });

                        $.ajax({
                            type: "get",
                            url: "{{ route('admin.item.item-unit') }}/" + $(this).val(),
                            success: function({
                                data
                            }) {
                                $(`#unit-id-${item_index}`).html('')
                                $(`#unit-id-${item_index}`).append(`
                                    <option value="${data.unit?.id}">${data.unit?.name}</option>
                                `);
                                item_unit = data.unit?.name;
                            }
                        });

                        $.ajax({
                            type: "get",
                            url: "{{ route('admin.sales-order-general.item-stock') }}/" +
                                $(this).val(),
                            success: function({
                                data
                            }) {
                                item_stocks = data;
                            }
                        });
                    });

                    $(`#jumlah-${item_index}`).keyup(function(e) {
                        e.preventDefault();

                        amount_list[item_index] = thousandToFloat($(this).val());

                        let price = thousandToFloat($(`#harga-${item_index}`).val());
                        let amount = thousandToFloat($(this).val());

                        if (price && amount) {
                            sub_total_list[item_index] = price * amount;
                        } else {
                            sub_total_list[item_index] = 0;
                        }
                        calculateTotal();

                        $(`#jumlah-table-${item_index}`).html(formatRupiahWithDecimal(amount));
                    });

                    $(`#harga-${item_index}`).keyup(function(e) {
                        e.preventDefault();

                        let price = thousandToFloat($(this).val());
                        let amount = thousandToFloat($(`#jumlah-${item_index}`).val());

                        if (price && amount) {
                            sub_total_list[item_index] = price * amount;
                        } else {
                            sub_total_list[item_index] = 0;
                        }
                        calculateTotal();
                    });

                    taxFunction();

                    initCommasForm();
                }

                let index = 0;
                $('#add-sale-order').click(function(e) {
                    e.preventDefault();

                    addItem(++index);
                })

                const initItems = () => {
                    let index = 0;
                    let tax_list = [];

                    addItem(index);
                }


                const displatAdditionalData = () => {
                    if (!child || child.length <= 0) {
                        initItems();
                    }

                    child.map((data, index) => {
                        const displayDataAdditionalInside = () => {
                            displayTaxAdditional();
                        };

                        let {
                            id,
                            unit,
                            item,
                            price_before_discount,
                            discount,
                            price,
                            amount,
                            notes,
                            sale_order_general_detail_taxes,
                        } = data;

                        let btn = '';
                        let btn_modal =
                            `<x-button color="info" id="detail-stock-${index + 500}" icon="eye" fontawesome size="sm" />`;


                        btn = `<div class="col-md-2 row align-items-end">
                                    <div class="form-group">
                                        <x-button color="danger" id="delete-sale-order-${index + 500}" icon="trash" fontawesome size="sm" />
                                    </div>
                                </div>`;

                        let display_price = parent.is_include_tax ? price_before_discount - discount : price;
                        $('#sales-order-items').append(`
                            <div class="row border-bottom mt-20" id="sale-order-item-${index + 500}">
                                <input type="hidden" name="id_so_item[${index+500}]" value="${item.id}" />
                                <input type="hidden" name="sale_order_general_detail_id[${index+500}]" value="${id}" />
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-select name="item_id[${index+500}]" label="item" id="item-id-${index + 500}" required>
                                            <option value="${item.id}">${item.kode} - ${item.nama}</option>
                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-select name="unit_id[${index+500}]" label="satuan" id="unit-id-${index + 500}" required>
                                            <option value="${unit.id}">${unit.name}</option>
                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input type="text" name="price_before_discount[${index+500}]" label="harga sebelum diskon" id="price-before-discount-${index + 500}" value="${formatRupiahWithDecimal(price_before_discount)}" class="commas-form" required />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input type="text" name="discount[${index+500}]" label="diskon" id="discount-${index + 500}" value="${formatRupiahWithDecimal(discount)}" class="commas-form" required />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input type="text" name="price[${index+500}]" label="harga" id="harga-${index + 500}" value="${formatRupiahWithDecimal(display_price)}" class="commas-form" required readonly />
                                        <input type="hidden" name="final_price[${index+500}]" id="final-price-${index + 500}" value="${price}" />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input type="text" name="amount[${index+500}]" label="jumlah" id="jumlah-${index + 500}" value="${formatRupiahWithDecimal(amount)}" class="commas-form" required />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <x-text-area name="notes[]" label="keterangan" id="keterangan-${index + 500}">${notes}</x-text-area>
                                        </div>
                                    </div>
                                </div>
                                ${btn}
                            </div>
                        `);

                        $(`#price-before-discount-${index + 500}`).on('keyup', function(e) {
                            let discount = $(`#discount-${index + 500}`).val() ?? 0;
                            let price = thousandToFloat($(this).val()) - thousandToFloat(discount);
                            price_before_discount_list[index + 500] = thousandToFloat($(this).val());
                            price_list[index + 500] = price;
                            discount_list[index + 500] = thousandToFloat(discount);

                            $(`#harga-${index + 500}`).val(formatRupiahWithDecimal(price)).trigger('keyup');
                            $(`#price-before-discount-table-${index + 500}`).html(formatRupiahWithDecimal(price_before_discount_list[index + 500]));
                            $(`#discount-table-${index + 500}`).html(formatRupiahWithDecimal(discount_list[index + 500]));
                        });

                        $(`#discount-${index + 500}`).on('keyup', function(e) {
                            let price_before_discount = $(`#price-before-discount-${index + 500}`).val();
                            let price = thousandToFloat(price_before_discount) - thousandToFloat($(this).val());

                            price_before_discount_list[index + 500] = thousandToFloat(price_before_discount);
                            price_list[index + 500] = price;
                            discount_list[index + 500] = thousandToFloat($(this).val());

                            $(`#harga-${index + 500}`).val(formatRupiahWithDecimal(price)).trigger('keyup');
                            $(`#price-before-discount-table-${index + 500}`).html(formatRupiahWithDecimal(price_before_discount_list[index + 500]));
                            $(`#discount-table-${index + 500}`).html(formatRupiahWithDecimal(discount_list[index + 500]));
                        });

                        const calculateTotal = () => {
                            calculateAll();
                        };

                        const deleteItem = (item_index) => {
                            $(`#sale-order-item-${item_index}`).remove();
                            $(`#row-table-${item_index}`).remove();
                            sub_total_list[item_index] = 0;
                            price_list[item_index] = 0;
                            discount_list[item_index] = 0;
                            price_before_discount_list[item_index] = 0;
                            amount_list[item_index] = 0;
                            total_list[item_index] = 0;

                            calculateTotal();
                            calculateAll();
                        }

                        $(`#delete-sale-order-${index + 500}`).click(function(e) {
                            e.preventDefault();
                            deleteItem(index + 500);
                        })

                        $(`#detail-stock-${index + 500}`).click(function(e) {
                            e.preventDefault();

                            $('#table-detail-stock tbody').html('');

                            if (item_stocks.lenght == 0) {
                                $('#table-detail-stock tbody').append(`
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada data</td>
                                    </tr>
                                `)
                            } else {
                                item_stocks.map((item, index) => {
                                    let {
                                        warehouse,
                                        stock
                                    } = item;

                                    $('#table-detail-stock tbody').append(`
                                        <tr>
                                            <td>${index+=1}</td>
                                            <td>${$(`#item-id-${index + 500}`).select2('data')[0].text}</td>
                                            <td>${warehouse.nama}</td>
                                            <td>${warehouse.branch?.name}</td>
                                            <td>${decimalFormatter(stock)} ${item_unit}</td>
                                        </tr>
                                    `)
                                });
                            }

                            $('#modal-check-stock').modal('show');
                        });

                        inititemSelect(`item-id-${index + 500}`, '', 'purchase item, service');

                        initSelect2Search(`tax-id-${index + 500}`, `{{ route('admin.select.tax') }}`, {
                            id: "id",
                            text: "name"
                        });

                        $(`#tax-id-${index + 500}`).change(function(e) {
                            e.preventDefault();

                            let tax_id = $(this).val();
                            taxFunction();
                        });

                        $(`#item-id-${index + 500}`).change(function(e) {
                            e.preventDefault();
                            $(`#item-table-${index + 500}`).html($(this).select2('data')[0].text);

                            $.ajax({
                                type: "get",
                                url: "{{ route('admin.item.price-latest') }}/" + $(this).val(),
                                success: function({
                                    data
                                }) {
                                    if (data) {
                                        $(`#harga-${index + 500}`).val(formatRupiahWithDecimal(data.harga_jual));
                                        $(`#harga-${index + 500}`).trigger('keyup');
                                    }
                                }
                            });

                            $.ajax({
                                type: "get",
                                url: "{{ route('admin.item.item-unit') }}/" + $(this).val(),
                                success: function({
                                    data
                                }) {
                                    $(`#unit-id-${index + 500}`).html('');
                                    $(`#unit-id-${index + 500}`).append(`
                                        <option value="${data.unit?.id}" selected>${data.unit?.name}</option>
                                    `);
                                    item_unit = data.unit?.name;
                                }
                            });

                            $.ajax({
                                type: "get",
                                url: "{{ route('admin.sales-order-general.item-stock') }}/" +
                                    $(this).val(),
                                success: function({
                                    data
                                }) {
                                    item_stocks = data;
                                }
                            });
                        });

                        $(`#jumlah-${index + 500}`).on('change keyup', function(e) {
                            e.preventDefault();

                            amount_list[index + 500] = thousandToFloat($(this).val());

                            let price = thousandToFloat($(`#harga-${index + 500}`).val());
                            let amount = thousandToFloat($(this).val());

                            if (price && amount) {
                                sub_total_list[index + 500] = price * amount;
                            } else {
                                sub_total_list[index + 500] = 0;
                            }

                            calculateTotal();

                            $(`#jumlah-table-${index + 500}`).html($(this).val());
                        });

                        $(`#harga-${index + 500}`).on('change keyup', function(e) {
                            e.preventDefault();

                            let price = thousandToFloat($(this).val());
                            let amount = thousandToFloat($(`#jumlah-${index + 500}`).val());

                            if (price && amount) {
                                sub_total_list[index + 500] = price * amount;
                            } else {
                                sub_total_list[index + 500] = 0;
                            }
                            calculateTotal();
                        });

                        inititemSelect(`item-id-${index + 500}`, '', 'purchase item, service');

                        // initSelect2Search(`unit-id-${index + 500}`, `{{ route('admin.select.unit') }}`, {
                        //     id: "id",
                        //     text: "name"
                        // });

                        const displayTaxAdditional = () => {
                            let tax_additional_list_sigle = [];

                            sale_order_general_detail_taxes.map((data, index2) => {
                                tax_additional_list_sigle.push(data.tax_id);
                                $(`#tax-id-${index + 500}`).append(`
                                    <option value="${data.tax_id}" selected>${data?.tax?.name}</option>
                                `);
                            });

                            initSelect2Search(`tax-id-${index + 500}`,
                                `{{ route('admin.select.tax') }}`, {
                                    id: "id",
                                    text: "name"
                                });
                        };

                        $(`#delete-sale-order-${index + 500}`).click(function(e) {
                            e.preventDefault();
                            deleteAdditional(index);
                        });

                        displayDataAdditionalInside();

                        taxFunction()
                    });
                    initCommasForm();

                    const handleTaxSelect = () => {
                        $(`#tax-selectForm`).change(
                            $.debounce(1000, function(e) {
                                tax_list_id = $(`#tax-selectForm`).val();
                                tax_list_value = [];

                                tax_list_id.map((tax_id, index) => {
                                    setTimeout(() => {
                                        $.ajax({
                                            type: "get",
                                            url: `{{ route('admin.tax.detail') }}/${tax_id}`,
                                            success: function({
                                                data
                                            }) {
                                                tax_list_value.push(data);
                                            }
                                        });
                                    }, 500);
                                });

                                setTimeout(() => {
                                    taxFunction();
                                }, 1000);


                            })
                        );
                    };

                    handleTaxSelect();
                };

                const deleteAdditional = (index) => {
                    $(`#sale-order-item-${index + 500}`).remove();
                }
            });
        </script>
    @endcan

    <script>
        sidebarMenuOpen('#trading');
        sidebarActive('#sales-order')
    </script>
@endsection
