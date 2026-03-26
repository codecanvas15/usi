@extends('layouts.admin.layout.index')

@php
    $main = 'sales-order-general';
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
        <x-card-data-table title="{{ 'create ' . $main }}">
            <x-slot name="header_content">

            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')

                <form action='{{ route("admin.$main.store") }}' method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" name="no_po_external" label="nomor PO" id="no_po_external" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input class="datepicker-input" name="tanggal" label="tanggal" id="date" value="{{ \Carbon\Carbon::today()->format('d-m-Y') }}" onchange="checkClosingPeriod($(this))" required />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-select name="customer_id" label="customer" id="customer-id" required autofocus>

                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" name="drop_point" label="drop point/ship to" id="drop_point" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-select name="currency_id" label="currency" id="currency-id" required>
                                    <option value="{{ get_local_currency()->id }}" selected>{{ get_local_currency()->nama }}</option>
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" name="exchange_rate" id="exchange-rate" value="1" class="commas-form" required readonly />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="file" label="document" name="quotation" required />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <x-input-checkbox label="Include PPN" name="is_include_tax" id="is_include_tax" value="1" />
                        </div>
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
                                    @foreach ($default_taxes as $tax)
                                        <option value="{{ $tax->id }}" selected>{{ $tax->name }}</option>
                                    @endforeach
                                </x-select>
                            </div>
                        </div>
                    </div>

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

                    <div class="d-flex justify-content-end gap-2">
                        <x-button type="reset" color="secondary" label="Kembali" link="{{ url()->previous() }}" />
                        <x-button color="primary" size="sm" icon="save" fontawesome type="submit" label="save data" class="float-end" />
                    </div>
                </form>
            </x-slot>

        </x-card-data-table>

        <x-modal title="Lihat Stock" headerColor="primary" id="modal-check-stock" modalSize="900">
            <x-slot name="modal_body">
                <x-table theadColor="danger" id="table-detail-stock">
                    <x-slot name="table_head">
                        <th>#</th>
                        <th>Item</th>
                        <th>Gudang</th>
                        <th>Branch</th>
                        <th>Stock</th>
                    </x-slot>
                    <x-slot name="table_body">

                    </x-slot>
                </x-table>

            </x-slot>
            <x-slot name="modal_footer">
                <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" />
                <x-button type="button" color="primary" id="save-other-trasaction-edit" label="Save" />
            </x-slot>
        </x-modal>
    @endcan
@endsection

@section('js')
    @can("create $main")
        <script src="{{ asset('js/admin/select/itemSelect.js') }}"></script>
        <script src="{{ asset('js/form/select2search.js') }}"></script>
        <script src="{{ asset('js/helpers/helpers.js') }}"></script>

        <script>
            $(document).ready(function() {
                setTimeout(() => {
                    $('#tax-selectForm').trigger('change');
                }, 250)

                // init variables =============================================================
                let currency_symbol = '{{ get_local_currency()->simbol }}',
                    sub_total_list = [],
                    price_before_discount_list = [],
                    discount_list = [],
                    price_list = [],
                    amount_list = [],
                    total_list = [],
                    total = 0,
                    sub_total = 0,
                    total_tax = 0,
                    tax_list_value = [],
                    tax_list_id = [];

                // / init variables =============================================================

                checkClosingPeriod($('#date'));

                // currecy and customer select =========================================================================
                const updateCurrencySymbol = () => {
                    $('p#currency-simbol').each(function() {
                        $(this).html(currency_symbol);
                    });
                }

                initSelect2Search(`tax-selectForm`, "{{ route('admin.select.tax') }}", {
                    id: "id",
                    text: "name"
                })

                initSelect2Search(`customer-id`, `{{ route('admin.select.customer') }}`, {
                    id: "id",
                    text: "nama"
                });

                initSelect2Search(`currency-id`, `{{ route('admin.select.currency') }}`, {
                    id: "id",
                    text: "kode,nama,negara"
                });

                $('#currency-id').change(function(e) {
                    e.preventDefault();

                    $.ajax({
                        type: "get",
                        url: `{{ route('admin.currency.detail') }}/${$(this).val()}`,
                        success: function({
                            data
                        }) {
                            currency_symbol = data.simbol;
                            updateCurrencySymbol();

                            if (data.is_local) {
                                $('#exchange-rate').val(1);
                                $('#exchange-rate').attr('readonly', true);
                            } else {
                                $('#exchange-rate').val(data.exchange_rate);
                                $('#exchange-rate').attr('readonly', false);
                            }
                        }
                    });
                });
                // currecy and customer select =========================================================================

                // calculation  =========================================================================

                const taxFunction = () => {
                    sub_total_list.map((data, item_index) => {
                        $(`#jumlah-${item_index}`).trigger('keyup');
                    })
                    calculateAll();
                }

                $('#is_include_tax').click(function(e) {
                    taxFunction();
                })

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
                            <span class="w-100 text-end" id="total-value">${formatRupiahWithDecimal(total)}</span>
                    `);

                    update_table();
                };
                // /calculation  =========================================================================

                // main form function  =========================================================================

                const initItems = () => {
                    let index = 0;
                    let tax_list = [];

                    const deleteItem = (item_index) => {
                        $(`#sale-order-item-${item_index}`).remove();
                        $(`#row-table-${item_index}`).remove();
                        sub_total_list[item_index] = 0;

                        calculateAll();
                    }

                    $('#add-sale-order').click(function(e) {
                        e.preventDefault();
                        addItem(++index);
                    })

                    const addItem = (item_index) => {
                        sub_total_list[item_index] = 0;

                        let item_unit = '';
                        let item_stocks = [];
                        let item_data = [];

                        let btn = '';
                        let btn_modal = `<x-button color="info" id="detail-stock-${item_index}" icon="eye" fontawesome size="sm" />`;


                        btn = `<div class="col-md-2 row align-items-end">
                                    <div class="form-group">
                                        <x-button color="danger" type="button" id="delete-sale-order-${item_index}" icon="trash" fontawesome size="sm" />
                                        ${btn_modal}
                                    </div>
                                </div>`;

                        let html = `
                            <div class="row border-bottom mt-20" id="sale-order-item-${item_index}">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-select name="item_id[]" label="item" id="item-id-${item_index}" required>

                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input type="text" name="unit_name[]" label="unit" id="unit-name-${item_index}" class="commas-form" required readonly/>
                                        <input type='hidden' id="unit-id-${item_index}" />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input type="text" name="price_before_discount[]" label="sebelum diskon" id="price-before-discount-${item_index}" class="commas-form text-end" required />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input type="text" name="discount[]" label="diskon" id="discount-${item_index}" class="commas-form text-end" required />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input type="text" name="price[]" label="harga" id="harga-${item_index}" class="commas-form text-end" required readonly />
                                        <input type="hidden" name="final_price[]" id="final-price-${item_index}" />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input type="text" name="amount[]" label="jumlah" id="jumlah-${item_index}" class="commas-form text-end" required />
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

                        html = `<tr id="row-table-${item_index}">
                                    <td id="item-table-${item_index}">-</td>
                                    <td class="text-end" id="price-before-discount-table-${item_index}">-</td>
                                    <td class="text-end" id="discount-table-${item_index}">-</td>
                                    <td class="text-end" id="harga-table-${item_index}">-</td>
                                    <td id="jumlah-table-${item_index}">-</td>
                                    <td class="text-end" id="sub-total-table-${item_index}">-</td>
                                    <td id="tax-table-${item_index}">-</td>
                                    <td class="text-end" id="value-tax-table-${item_index}">-</td>
                                    <td class="text-end" id="total-${item_index}">-</td>
                                </tr>`;

                        $('table#table-calculate tbody').append(html);

                        sub_total_list[item_index] = 0;
                        price_before_discount_list[item_index] = 0;
                        discount_list[item_index] = 0;
                        price_list[item_index] = 0;
                        amount_list[item_index] = 0;
                        total_list[item_index] = 0;

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

                        taxFunction();

                        const calculateTotal = () => {
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

                        $(`#item-id-${item_index}`).change(function(e) {
                            e.preventDefault();
                            $(`#item-table-${item_index}`).html($(this).select2('data')[0].text);

                            $.ajax({
                                type: "get",
                                url: "{{ route('admin.item.price-latest') }}/" + $(this).val(),
                                success: function({
                                    data
                                }) {
                                    if (data != null) {
                                        $(`#harga-${item_index}`).val(decimalFormatterCommasWithOuformatRupiahWithDecimal(data.harga_jual));
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
                                    $(`#unit-name-${item_index}`).val(data.unit?.name);
                                    $(`#unit-id-${item_index}`).val(data.unit_id);
                                    item_unit = data.unit?.name;
                                }
                            });

                            $.ajax({
                                type: "get",
                                url: "{{ route('admin.sales-order-general.item-stock') }}/" + $(this).val(),
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

                        index++;

                        initCommasForm();
                    }

                    addItem(index);
                }

                initItems();
                // /main form function  =========================================================================

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
            });
        </script>
    @endcan
    <script>
        sidebarMenuOpen('#trading');
        sidebarActive('#sales-order')
    </script>
@endsection
