@extends('layouts.admin.layout.index')

@php
    $main = 'purchase-order-general';
    $title = Str::headline('Purchase Order general');
@endphp

@section('title', Str::headline("edit $title") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route('admin.purchase.index') }}">{{ Str::headline('purchase') }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route('admin.purchase-order-general.show', ['purchase_order_general' => $model->id]) }}">{{ Str::headline($title) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Edit ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can('edit purchase-general')
        <x-card-data-table id="loading-section">
            <x-slot name="table_content">
                <h4 class="text-center">Loading...</h4>
            </x-slot>
        </x-card-data-table>

        <form action="{{ route('admin.purchase-order-general.update', ['purchase_order_general' => $model->id]) }}" enctype="multipart/form-data" id="form-update" method="post">
            @csrf
            @method('put')
            <input type="hidden" name="type" value="purchase-request">

            <div id="parent-section"></div>
            <div id="purchaseRequesr-section"></div>
            <div id="additional-section"></div>
            <div id="resume-section"></div>
            <div id="btn-section">
                <div id="submit-btn-card">
                    <x-card-data-table>
                        <x-slot name="table_content">
                            <div class="d-flex justify-content-end gap-3">
                                <input type="hidden" name="values" id="value-final-form">
                                <x-button type="submit" color="primary" label="Save" icon="save" fontawesome id="btn-submit" />
                            </div>
                        </x-slot>
                    </x-card-data-table>
                </div>
            </div>
        </form>
    @endcan
@endsection

@section('js')
    <script src="{{ asset('js/admin/select/branch.js') }}"></script>
    <script src="{{ asset('js/admin/select/itemSelect.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    @can('edit purchase-general')
        <script>
            $(document).ready(function() {

                let parentData = [],
                    main_data_lists = [],
                    additional_data_lists = [],
                    additionalItemsCount = 0,
                    additionalItemsCountDisplay = 0;

                let MainDataCalculate = [],
                    additionalDataCalculate = [];

                let currency_symbol = '';

                let TAX_LIST_VALUE = [],
                    TAX_LIST_ID = [],
                    TAX_OPTION = [];

                let TAX_LIST_ADDITIONAL_VALUE = [],
                    TAX_LIST_ADDITIONAL_ID = [],
                    TAX_LIST_ADDITIONAL_OPTION = [];

                const init = () => {
                    getDataPurchaseOrder();

                    $('#parent-section').hide();
                    $('#purchaseRequesr-section').hide();
                    $('#additional-section').hide();
                    $('#resume-section').hide();
                    $('#btn-section').hide();
                };

                const getDataPurchaseOrder = () => {
                    $.ajax({
                        type: "post",
                        url: "{{ route('admin.purchase-order-general.data-for-edit', ['id' => $model]) }}",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function({
                            data
                        }) {
                            let {
                                model,
                                main,
                                additional
                            } = data;

                            parentData = model;
                            main_data_lists = main;
                            additional_data_lists = additional;

                            currency_symbol = model.currency.simbol;

                            displayFirstCard();
                            displayMainData();
                            displayAdditional();
                            displayResumeData();
                            handleSubmit();
                            calculate();
                        }
                    });
                };

                const displayFirstCard = () => {

                    $('#loading-section').hide();

                    const displayCardAndForm = () => {

                        let {
                            code,
                            date,
                            quotation,
                            term_of_payment,
                            term_of_payment_days,
                            payment_description,

                            exchange_rate,
                            branch,
                            vendor,
                            currency,
                        } = parentData;

                        let is_include_tax = parentData.is_include_tax ? 'checked' : '';

                        let includeTaxForm = '';
                        if (parentData.is_include_tax) {
                            includeTaxForm = `<x-input-checkbox label="Include PPN" name="is_include_tax" id="is_include_tax" value="1"  checked />`;
                        } else {
                            includeTaxForm = `<x-input-checkbox label="Include PPN" name="is_include_tax" id="is_include_tax" value="1"  />`;
                        }

                        let html = `<x-card-data-table :title='"edit $main"'>
                                        <x-slot name="header_content">

                                        </x-slot>
                                        <x-slot name="table_content">
                                            @include('components.validate-error')

                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <x-input type="text" name="code" label="kode" value="${code}" id="code-input" required readonly/>
                                                    </div>
                                                </div>
                                            </div>
                                            @if (get_current_branch()->is_primary)
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <x-select label="branch" name="branch_id" id="branch-select" required disabled>
                                                                <option value="${branch.id}" selected>${branch.name}</option>
                                                            </x-select>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <x-input class="datepicker-input" name="date" label="tanggal" value="${localDate(date)}" id="date-input" required onchange="checkClosingPeriod($(this))" readonly/>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <x-select name="currency_id" label="mata uang" id="currency-select" required>
                                                            <option value="${currency.id}" selected>${currency.nama}</option>
                                                        </x-select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <x-input type="text" name="exchange_rate" label="nilai_tukar" class="commas-form" id="exchange-rate" class="commas-form" value="${exchange_rate}" required />
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <x-input type="file" name="quotation" id="quotation-input"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 d-flex align-self-end">
                                                    <div class="form-group">
                                                        ${
                                                            quotation ? `<x-button color="info" icon="file" fontawesome size="sm" link="{{ url('storage/') }}/${quotation}" target="blank" />` : `<x-button color="danger" icon="file" label="no file" fontawesome size="sm" badge />`
                                                        }
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <x-select name="vendor_id" label="vendor" id="vendor-select" required>
                                                            <option value="${vendor.id}" selected>${vendor.nama}</option>
                                                        </x-select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <x-select name="term_of_payment" id="termOfPayment-select" required>
                                                            <option value="" selected>- Pilih term of payment -</option>
                                                            <option value="cash" ${term_of_payment == 'cash' ? 'selected' : ''}>Cash</option>
                                                            <option value="by days" ${term_of_payment == 'by days' ? 'selected' : ''}>By Days</option>
                                                        </x-select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <x-input type="number" name="term_of_payment_days" label="term of payment day" value="${term_of_payment_days}" id="termOfPayment-day" required />
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <x-input type="text" name="payment_description" id="payment_description" label="keterangan pembayaran" value="${payment_description ? payment_description : ''}" />
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    ${includeTaxForm}
                                                </div>
                                            </div>
                                        </x-slot>
                                    </x-card-data-table>`;

                        $('#parent-section').html(html);



                        initDatePicker();

                        handleFormFirstCard();
                        $('#parent-section').show(500);
                    };

                    const handleFormFirstCard = () => {

                        const initializeSelect = () => {
                            initSelect2SearchPaginationData(`branch-select`, `{{ route('admin.select.branch') }}`, {
                                id: 'id',
                                text: 'name'
                            })
                            initSelect2SearchPaginationData(`currency-division`, `{{ route('admin.select.currency') }}`, {
                                id: 'id',
                                text: 'kode,nama,negara'
                            })

                            initSelect2SearchPaginationData(`vendor-select`, `{{ route('admin.select.vendor') }}`, {
                                id: 'id',
                                text: 'nama'
                            })

                            $('#termOfPayment-select').select2({
                                width: "100%"
                            });
                        };

                        const eventListenerForm = () => {

                            $('#currency-select').change(function(e) {
                                e.preventDefault();

                                $.ajax({
                                    type: "get",
                                    url: `{{ route('admin.currency.detail') }}/${this.value}`,
                                    success: function({
                                        data
                                    }) {

                                        if (data.is_local) {
                                            $('#exchange-rate').val(1);
                                            $('#exchange-rate').attr('readonly', true);
                                        } else {
                                            $('#exchange-rate').val(null);
                                            $('#exchange-rate').attr('readonly', false);
                                        }

                                        currency_symbol = data.simbol;

                                        $('#exchange-rate').trigger('keyup');
                                        $('#exchange-rate').trigger('blur');
                                    }
                                });
                            });

                            $('#vendor-select').change(function(e) {
                                e.preventDefault();

                                $.ajax({
                                    type: "get",
                                    url: `{{ route('admin.select.vendor-detail') }}/${$(this).val()}`,
                                    success: function({
                                        data
                                    }) {
                                        let {
                                            term_of_payment,
                                            top_days
                                        } = data;

                                        if (data.vendor_banks.length == 0) {
                                            alert('Vendor belum memiliki bank');
                                        }

                                        if (term_of_payment) {
                                            $('#termOfPayment-select').val(term_of_payment);
                                            $('#termOfPayment-select').trigger('change');
                                            $('#termOfPayment-select').trigger('blur');
                                        }

                                        if (top_days) {
                                            $('#termOfPayment-day').val(top_days);
                                        }
                                    }
                                });
                            });

                            $('#termOfPayment-select').change(function(e) {
                                e.preventDefault();

                                if ($(this).val() == 'cash') {
                                    $('#termOfPayment-day').val(0);
                                    $('#termOfPayment-day').attr('readonly', true);
                                } else {
                                    $('#termOfPayment-day').attr('readonly', false);
                                    $('#termOfPayment-day').val(null);
                                }
                            });

                            $('#currency-select').trigger('change');
                            // $('#termOfPayment-select').trigger('change');
                        };

                        initializeSelect();
                        eventListenerForm();
                    };

                    displayCardAndForm();
                };

                const displayMainData = () => {

                    const displayData = (row_index) => {
                        let {
                            purchase_request,
                            purchase_order_general_detail_items
                        } = main_data_lists[row_index];

                        MainDataCalculate[row_index] = {
                            "purchase_request_id": purchase_request.id,
                            "purchase_order_general_detail_id": main_data_lists[row_index].id,
                            "purchase_order_general_detail_item": [],
                        };

                        const displayCard = () => {

                            let {
                                kode,
                                tanggal,
                                division
                            } = purchase_request;


                            let html = `<x-card-data-table id="purchase-request-card-${row_index}">
                                            <x-slot name="table_content">

                                                <x-modal title="detail purchase request item" id="modalPurchaseRequestDetail-${row_index}" headerColor="info" modalSize="800">
                                                    <x-slot name="modal_body">
                                                        <div class="row mb-30">
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="fw-bold">{{ Str::headline('tanggal') }}</label>
                                                                    <p id="purchase-request-parent-date-${row_index}">${tanggal}</p>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="fw-bold">{{ Str::headline('kode') }}</label>
                                                                    <p id="purchase-request-parent-code-${row_index}">${kode}</p>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="fw-bold">{{ Str::headline('divisi') }}</label>
                                                                    <p id="purchase-request-parent-divisi-${row_index}">${division.name}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </x-slot>
                                                </x-modal>

                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <x-select name="purchase_request_id[]" label="Purchase request" id="purchaseRequest-select-${row_index}" required disabled>
                                                                <option value="${purchase_request.id}">${kode} - ${tanggal}</option>
                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 d-flex align-self-end">
                                                        <div class="form-group">
                                                            <x-button color="info" id="btn-purchaseRequest-modal-${row_index}" label="detail purchase request" icon="plus" fontawesome size="sm" dataToggle="modal" dataTarget="#modalPurchaseRequestDetail-${row_index}" />
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mt-10 pt-10 border-top border-primary" id="purchase-request-detail-list-${row_index}">

                                                </div>

                                            </x-slot>
                                        </x-card-data-table>`;

                            $('#purchaseRequesr-section').append(html);

                            initCommasForm();
                            $(`#btn-purchaseRequest-modal-${row_index}`).click(function(e) {
                                e.preventDefault();
                            });

                            displayItemForm()
                        };

                        function calculate_final_price(row_index, index) {
                            let price_before_discount = $(`#main-item-price-before-discount-${row_index}-${index}`).val();
                            let discount = $(`#main-item-discount-${row_index}-${index}`).val();
                            let price_after_discount = thousandToFloat(price_before_discount) - thousandToFloat(discount);

                            $(`#main-item-price-${row_index}-${index}`).val(formatRupiahWithDecimal(price_after_discount)).trigger('keyup');
                        }

                        const displayItemForm = () => {

                            $(`#purchase-request-detail-list-${row_index}`).html('');
                            purchase_order_general_detail_items.map((purchase_order_general_detail_item, purchase_order_general_detail_item_index) => {

                                MainDataCalculate[row_index].purchase_order_general_detail_item[purchase_order_general_detail_item_index] = {
                                    "purchase_order_general_detail_item_id": purchase_order_general_detail_item.id,
                                    "purchase_request_detail_id": purchase_order_general_detail_item.purchase_request_detail_id,
                                    "item": purchase_order_general_detail_item.item,
                                    "quantity": purchase_order_general_detail_item.quantity,
                                    "price_before_discount": purchase_order_general_detail_item.price_before_discount,
                                    "discount": purchase_order_general_detail_item.discount,
                                    "price": purchase_order_general_detail_item.price,
                                    "original_price": parentData.is_include_tax ? purchase_order_general_detail_item.price_before_discount - purchase_order_general_detail_item.discount : purchase_order_general_detail_item.price,
                                };

                                let original_price = parentData.is_include_tax ? purchase_order_general_detail_item.price_before_discount - purchase_order_general_detail_item.discount : purchase_order_general_detail_item.price;

                                let {
                                    quantity,
                                    price_before_discount,
                                    discount,
                                    price,
                                    purchase_request_detail,
                                    item,
                                    purchase_order_general_detail_item_taxes,
                                } = purchase_order_general_detail_item;

                                TAX_OPTION = purchase_order_general_detail_item_taxes.map((purchase_order_general_detail_item_tax) => {
                                    return `<option value="${purchase_order_general_detail_item_tax.tax.id}" selected>${purchase_order_general_detail_item_tax.tax.name}</option>`
                                }).join('');

                                TAX_LIST_ID = purchase_order_general_detail_item_taxes.map((purchase_order_general_detail_item_tax) => {
                                    return purchase_order_general_detail_item_tax.tax.id
                                });

                                TAX_LIST_VALUE = purchase_order_general_detail_item_taxes.map((purchase_order_general_detail_item_tax) => {
                                    return purchase_order_general_detail_item_tax.tax;
                                });

                                let item_value = purchase_order_general_detail_item.item_id ? purchase_order_general_detail_item.item.nama + " - " + purchase_order_general_detail_item.item.kode : purchase_order_general_detail_item.item;

                                let html = `
                                    <div class="row" id="row-${row_index}-${purchase_order_general_detail_item_index}">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-select name="main_item_id[]" label="item" id="main-item-select-${row_index}-${purchase_order_general_detail_item_index}" required disabled>
                                                    <option value="${item.id}" selected>${item.kode} - ${item.nama}</option>
                                                </x-select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-input type="text" name="main_unit[]" label="satuan" id="main-item-unit-${row_index}-${purchase_order_general_detail_item_index}" value="${item.unit.name}" required readonly />
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-input type="text" name="main_price_before_discount[]" label="harga sebelum diskon" class="commas-form" id="main-item-price-before-discount-${row_index}-${purchase_order_general_detail_item_index}" value="${formatRupiahWithDecimal(price_before_discount)}" required />
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-input type="text" name="main_discount[]" label="diskon" class="commas-form" id="main-item-discount-${row_index}-${purchase_order_general_detail_item_index}" value="${formatRupiahWithDecimal(discount)}" required />
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-input type="text" name="main_price[]" label="harga" class="commas-form" id="main-item-price-${row_index}-${purchase_order_general_detail_item_index}" value="${formatRupiahWithDecimal(original_price)}" readonly />
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-input type="text" name="main_jumlah_diminta[]" label="jumlah diminta" class="commas-form" value="${formatRupiahWithDecimal(parseFloat(purchase_request_detail.jumlah_diapprove).toFixed(2))}" id="main-item-quantityRequested-${row_index}-${purchase_order_general_detail_item_index}" required readonly helpers="${purchase_request_detail.unit.name}"/>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-input type="text" name="main_quantity[]" label="jumlah" class="commas-form main-quantity" id="main-item-quantity-${row_index}-${purchase_order_general_detail_item_index}" value="${quantity}" useCustomError required />
                                            </div>
                                        </div>
                                        <div class="col-md-1 d-flex align-self-center">
                                            <div class="form-group">
                                                <x-button color="info" icon="eye" fontawesome size="sm" id="purchase-requestDetail-btnmodal-${row_index}-${purchase_order_general_detail_item_index}" dataToggle="modal" dataTarget="#modalpurchase-requestDetail-modal-${row_index}-${purchase_order_general_detail_item_index}"/>
                                                <x-button type="" color="danger" icon="trash" fontawesome size="sm" id="purchase-requestDetail-delete-${row_index}-${purchase_order_general_detail_item_index}"/>
                                                <x-modal title="Detail purchase request item" id="modalpurchase-requestDetail-modal-${row_index}-${purchase_order_general_detail_item_index}" headerColor="info" modalSize="1000">
                                                    <x-slot name="modal_body">
                                                        <x-table id="">
                                                            <x-slot name="table_head">
                                                                <th>item</th>
                                                                <th>Jumlah Dipesan</th>
                                                                <th>Attachment</th>
                                                            </x-slot>
                                                            <x-slot name="table_body">
                                                                <tr>
                                                                    <td>${item_value} - ${purchase_request_detail.unit?.name}</td>
                                                                    <td>${purchase_request_detail.jumlah_diapprove}</td>
                                                                    <td>
                                                                        <x-button color="info" icon="file" fontawesome size="sm" link="{{ url('storage/') }}/${purchase_request_detail.file}" target="blank" />
                                                                    </td>
                                                                </tr>
                                                            </x-slot>
                                                        </x-table>
                                                    </x-slot>
                                                </x-modal>
                                            </div>
                                        </div>
                                    </div>
                                `;

                                $(`#purchase-request-detail-list-${row_index}`).append(html);

                                $(`#purchase-requestDetail-btnmodal-${row_index}-${purchase_order_general_detail_item_index}`).click(function(e) {
                                    e.preventDefault();
                                });

                                $(`#purchase-requestDetail-delete-${row_index}-${purchase_order_general_detail_item_index}`).click(function(e) {
                                    e.preventDefault();

                                    $(`#row-${row_index}-${purchase_order_general_detail_item_index}`).remove();
                                    MainDataCalculate[row_index].purchase_order_general_detail_item.splice(purchase_order_general_detail_item_index, 1);
                                    calculateResumeData();
                                });

                                $(`#main-item-quantity-${row_index}-${purchase_order_general_detail_item_index}`).data('testQty', purchase_request_detail.jumlah_diapprove);

                                initCommasForm();
                                eventListenerForm(row_index, purchase_order_general_detail_item_index);
                            })

                        };

                        const eventListenerForm = (row_index, purchase_order_general_detail_item_index) => {

                            initSelect2SearchPaginationData(`main-item-select-${row_index}-${purchase_order_general_detail_item_index}`, `{{ route('admin.select.item.type') }}/general`, {
                                id: 'id',
                                text: 'kode,nama'
                            })

                            $(`#main-item-select-${row_index}-${purchase_order_general_detail_item_index}`).change(function(e) {
                                e.preventDefault();

                                if (this.value) {
                                    $.ajax({
                                        type: "get",
                                        url: `{{ route('admin.item.price-latest') }}/${this.value}`,
                                        success: function({
                                            data
                                        }) {
                                            $(`#main-item-price-${row_index}-${purchase_order_general_detail_item_index}`).val(decimalFormatterCommasWithOuNumberWithCommas(data.harga_beli));
                                            $(`#main-item-price-${row_index}-${purchase_order_general_detail_item_index}`).trigger('keyup');
                                            $(`#main-item-price-${row_index}-${purchase_order_general_detail_item_index}`).trigger('blur');
                                        }
                                    });

                                    $.ajax({
                                        type: "get",
                                        url: `{{ route('admin.item.item-unit') }}/${this.value}`,
                                        success: function({
                                            data
                                        }) {
                                            MainDataCalculate[row_index].purchase_order_general_detail_item[purchase_order_general_detail_item_index].item = data;
                                            $(`#main-item-unit-${row_index}-${purchase_order_general_detail_item_index}`).val(data.unit.name);
                                        }
                                    });
                                } else {
                                    $(`#main-item-price-${row_index}-${purchase_order_general_detail_item_index}`).val(0);
                                    $(`#main-item-price-${row_index}-${purchase_order_general_detail_item_index}`).trigger('keyup');
                                    $(`#main-item-price-${row_index}-${purchase_order_general_detail_item_index}`).trigger('blur');
                                }
                            });

                            $(`#main-item-price-${row_index}-${purchase_order_general_detail_item_index}`).keyup(function(e) {
                                e.preventDefault();
                                MainDataCalculate[row_index].purchase_order_general_detail_item[purchase_order_general_detail_item_index].price = thousandToFloat(this.value);
                                MainDataCalculate[row_index].purchase_order_general_detail_item[purchase_order_general_detail_item_index].original_price = thousandToFloat(this.value);
                            })

                            $(`#main-item-price-before-discount-${row_index}-${purchase_order_general_detail_item_index}`).on('blur', function(e) {
                                calculate_final_price(row_index, purchase_order_general_detail_item_index);
                                MainDataCalculate[row_index].purchase_order_general_detail_item[purchase_order_general_detail_item_index].price_before_discount = thousandToFloat(this.value);
                            });

                            $(`#main-item-discount-${row_index}-${purchase_order_general_detail_item_index}`).on('blur', function(e) {
                                calculate_final_price(row_index, purchase_order_general_detail_item_index);
                                MainDataCalculate[row_index].purchase_order_general_detail_item[purchase_order_general_detail_item_index].discount = thousandToFloat(this.value);
                            });

                            $(`#main-item-quantity-${row_index}-${purchase_order_general_detail_item_index}`).keyup(function(e) {
                                e.preventDefault();

                                let req_qty = parseFloat($(this).data('testQty')).toFixed(2);
                                let inputQty = thousandToFloat(this.value).toFixed(2);

                                if (parseFloat(inputQty) > parseFloat(req_qty)) {
                                    $(this).addClass('is-invalid');
                                    $(`#error-message-for-main-item-quantity-${row_index}-${purchase_order_general_detail_item_index}`).text('Jumlah tidak boleh lebih dari jumlah diminta!');
                                } else {
                                    $(this).removeClass('is-invalid');
                                    $(`#error-message-for-main-item-quantity-${row_index}-${purchase_order_general_detail_item_index}`).text(null);
                                }

                                MainDataCalculate[row_index].purchase_order_general_detail_item[purchase_order_general_detail_item_index].quantity = thousandToFloat(this.value);
                            })

                            setTimeout(() => {
                                $(`#main-item-price-${row_index}-${purchase_order_general_detail_item_index}`).trigger('keyup');
                                $(`#main-item-price-${row_index}-${purchase_order_general_detail_item_index}`).trigger('blur');
                                $(`#main-item-quantityRequested-${row_index}-${purchase_order_general_detail_item_index}`).trigger('blur');
                                $(`#main-item-quantityRequested-${row_index}-${purchase_order_general_detail_item_index}`).trigger('blur');
                                $(`#main-item-quantity-${row_index}-${purchase_order_general_detail_item_index}`).trigger('keyup');
                                $(`#main-item-quantity-${row_index}-${purchase_order_general_detail_item_index}`).trigger('blur');
                            }, 1000)
                        };

                        displayCard();
                        displayItemForm();
                        eventListenerForm();
                    };

                    const display = () => {
                        $('#purchaseRequesr-section').show();
                        $('#purchaseRequesr-section').html('');
                        main_data_lists.map((purchase_order_detail, purchase_order_detail_index) => {
                            displayData(purchase_order_detail_index);
                        });
                    };

                    display();
                };

                const displayAdditional = () => {

                    const displayAdditionalData = (additional_index) => {

                        let {
                            purchase_order_general_detail_items
                        } = additional_data_lists[additional_index];

                        additionalDataCalculate = {
                            "purchase_order_general_detail_id": additional_data_lists[additional_index].id,
                            "purchase_order_general_detail_items": []
                        };

                        const displayAdditionalItemForm = (row_index) => {
                            let btn = '';

                            if (row_index == 0) {
                                btn = `<x-button color="info" id="btn-add-additionalItem" class="float-end" icon="plus" fontawesome size="sm" />`;
                            } else {
                                btn = `<x-button color="danger" id="btn-add-removeItem-${row_index}" class="float-end" icon="trash" fontawesome size="sm" />`;
                            }

                            let {
                                quantity,
                                price,
                                item,
                                purchase_order_general_detail_item_taxes
                            } = purchase_order_general_detail_items[row_index];

                            additionalDataCalculate.purchase_order_general_detail_items[row_index] = {
                                item: item,
                                quantity: quantity,
                                price: price
                            }

                            TAX_LIST_ADDITIONAL_OPTION = purchase_order_general_detail_item_taxes.map((tax) => {
                                return `<option value="${tax.tax.id}" selected>${tax.tax.name}</option>`;
                            }).join('');

                            TAX_LIST_ADDITIONAL_ID = purchase_order_general_detail_item_taxes.map((tax) => tax.tax_id);
                            TAX_LIST_ADDITIONAL_VALUE = purchase_order_general_detail_item_taxes.map((tax) => tax.tax);

                            let html = `
                                <div class="row" id="additional-row-${row_index}">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-select name="additional_item_type_id[]" label="item type item" id="additional-itemType-select-${row_index}">
                                                <option value="">-----</option>
                                                <option value="service" ${item.type == 'service' ? "selected" : '' }>Service</option>
                                                <option value="transport" ${item.type == 'transport' ? "selected" : '' }>Transport</option>
                                            </x-select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <x-select name="additional_item_id[]" label="item" id="additional-item-select-${row_index}">
                                                <option value="${item.id}">${item.kode} - ${item.nama}</option>
                                            </x-select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <x-input type="text" name="additional_unit[]" label="satuan" value="${item.unit.name}" id="additional-item-unit-${row_index}" readonly />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <x-input type="text" name="additional_price[]" label="harga" class="commas-form" value="${price}" id="additional-item-price-${row_index}" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <x-input type="text" name="additional_quantity[]" label="jumlah" class="commas-form" value="${quantity}" id="additional-item-quantity-${row_index}"/>
                                        </div>
                                    </div>
                                    <div class="col-md-1 d-flex align-self-end">
                                        <div class="form-group">
                                            ${btn}
                                        </div>
                                    </div>
                                </div>
                            `;

                            $('#additional-item-row').append(html);
                            $(`#additional-itemType-select-${row_index}`).select2();

                            if (row_index == 0) {
                                $(`#btn-add-additionalItem`).click(function(e) {
                                    e.preventDefault();
                                    addAnotherAdditionalItem(additionalItemsCount);
                                });
                            } else {
                                $(`#btn-add-removeItem-${row_index}`).click(function(e) {
                                    e.preventDefault();
                                    removeAdditionalItem(row_index);
                                });
                            }

                            initCommasForm();
                            handleEventListener(row_index, false);
                        };

                        const displayFormData = () => {
                            purchase_order_general_detail_items.map((purchase_order_general_detail_item, purchase_order_general_detail_item_index) => {
                                additionalItemsCount++;
                                displayAdditionalItemForm(purchase_order_general_detail_item_index);
                            });
                        };

                        displayFormData();
                    };

                    const handleEventListener = (row_index, purchase_order_general_item = true) => {
                        initCommasForm();

                        $(`#additional-item-price-${row_index}`).trigger('keyup');
                        $(`#additional-item-quantity-${row_index}`).trigger('keyup');
                        $(`#additional-item-price-${row_index}`).trigger('blur');
                        $(`#additional-item-quantity-${row_index}`).trigger('blur');

                        $(`#additional-itemType-select-${row_index}`).change(function(e) {
                            e.preventDefault();

                            if (purchase_order_general_item) {
                                resetEventListerner(row_index);
                                $(`#additional-item-select-${row_index}`).attr('required', this.value ? false : true);
                                $(`#additional-item-price-${row_index}`).attr('required', this.value ? false : true);
                                $(`#additional-item-quantity-${row_index}`).attr('required', this.value ? false : true);

                                $(`#additional-item-select-${row_index}`).attr('disabled', this.value ? false : true);
                                $(`#additional-item-price-${row_index}`).attr('readonly', this.value ? false : true);
                                $(`#additional-item-quantity-${row_index}`).attr('readonly', this.value ? false : true);

                                $(`#additional-item-select-${row_index}`).html('');
                                $(`#additional-item-select-${row_index}`).val(null);
                                $(`#additional-item-unit-${row_index}`).val(null);
                                $(`#additional-item-price-${row_index}`).val(null);
                                $(`#additional-item-quantity-${row_index}`).val(null);

                                additionalDataCalculate.purchase_order_general_detail_items[row_index] = [];

                                additionalDataCalculate.purchase_order_general_detail_items[row_index] = {
                                    item: [],
                                    price: 0,
                                    quantity: 0
                                }
                            }
                            purchase_order_general_item = true;

                            if (this.value) {

                                initSelect2SearchPaginationData(`additional-item-select-${row_index}`, `{{ route('admin.select.item.type') }}/${this.value}`, {
                                    id: 'id',
                                    text: 'nama'
                                })

                                $(`#additional-item-select-${row_index}`).change(function(e) {
                                    e.preventDefault();

                                    if (this.value) {
                                        $.ajax({
                                            type: "get",
                                            url: `{{ route('admin.item.price-latest') }}/${this.value}`,
                                            success: function({
                                                data
                                            }) {
                                                $(`#additional-item-price-${row_index}`).val(decimalFormatterCommasWithOuNumberWithCommas(data.harga_beli));
                                                $(`#additional-item-price-${row_index}`).trigger('keyup');
                                                $(`#additional-item-price-${row_index}`).trigger('blur');
                                            }
                                        });

                                        $.ajax({
                                            type: "get",
                                            url: `{{ route('admin.item.item-unit') }}/${this.value}`,
                                            success: function({
                                                data
                                            }) {
                                                additionalDataCalculate.purchase_order_general_detail_items[row_index].item = data;
                                                $(`#additional-item-unit-${row_index}`).val(data.unit.name);
                                            }
                                        });
                                    } else {
                                        $(`#additional-item-price-${row_index}`).val(0);
                                        $(`#additional-item-price-${row_index}`).trigger('keyup');
                                        $(`#additional-item-price-${row_index}`).trigger('blur');
                                    }
                                });
                            }

                            $(`#additional-item-price-${row_index}`).keyup(function(e) {
                                additionalDataCalculate.purchase_order_general_detail_items[row_index].price = thousandToFloat($(this).val());
                            });

                            $(`#additional-item-quantity-${row_index}`).keyup(function(e) {
                                additionalDataCalculate.purchase_order_general_detail_items[row_index].quantity = thousandToFloat($(this).val());
                            });
                        });

                        $(`#additional-itemType-select-${row_index}`).trigger('change');
                    };

                    const resetEventListerner = (row_index) => {
                        $(`#additional-item-select-${row_index}`).unbind('change');
                        $(`#additional-item-price-${row_index}`).unbind('keyup');
                        $(`#additional-item-quantity-${row_index}`).unbind('keyup');
                    };

                    const addAnotherAdditionalItem = (row_index) => {
                        additionalItemsCount++;

                        const handleAdd = (row_index) => {
                            let btn = '';

                            if (row_index == 0) {
                                btn = `<x-button color="info" id="btn-add-additionalItem" class="float-end" icon="plus" fontawesome size="sm" />`;
                            } else {
                                btn = `<x-button color="danger" id="btn-add-removeItem-${row_index}" class="float-end" icon="trash" fontawesome size="sm" />`;
                            }

                            let html = `
                                <div class="row" id="additional-row-${row_index}">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-select name="additional_item_type_id[]" label="item type item" id="additional-itemType-select-${row_index}">
                                                <option value="">-----</option>
                                                <option value="service">Service</option>
                                                <option value="transport">Transport</option>
                                            </x-select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <x-select name="additional_item_id[]" label="item" id="additional-item-select-${row_index}" disabled>
                                            </x-select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <x-input type="text" name="additional_unit[]" label="satuan" id="additional-item-unit-${row_index}" readonly />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <x-input type="text" name="additional_price[]" label="harga" class="commas-form" id="additional-item-price-${row_index}" readonly />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <x-input type="text" name="additional_quantity[]" label="jumlah" class="commas-form" id="additional-item-quantity-${row_index}" readonly/>
                                        </div>
                                    </div>
                                    <div class="col-md-1 d-flex align-self-center">
                                        <div class="form-group">
                                            ${btn}
                                        </div>
                                    </div>
                                </div>
                            `;

                            $('#additional-item-row').append(html);
                            $(`#additional-itemType-select-${row_index}`).select2();

                            if (row_index == 0) {
                                $(`#btn-add-additionalItem`).click(function(e) {
                                    e.preventDefault();
                                    addAnotherAdditionalItem(additionalItemsCount);
                                });
                            } else {
                                $(`#btn-add-removeItem-${row_index}`).click(function(e) {
                                    e.preventDefault();
                                    removeAdditionalItem(row_index);
                                });
                            }

                            handleEventListener(row_index);
                        };

                        handleAdd(row_index)
                    };

                    const removeAdditionalItem = (row_index) => {
                        $(`#additional-row-${row_index}`).remove();
                        additionalDataCalculate.purchase_order_general_detail_items[row_index] = null;
                    };

                    const display = () => {
                        $('#additional-section').show();
                        $('#additional-section').html(`
                                <div id="additional-item-card">
                                    <x-card-data-table title="additional item">
                                        <x-slot name="table_content">
                                            <div id="additional-item-row">

                                            </div>
                                        </x-slot>
                                    </x-card-data-table>
                                </div>
                        `);

                        if (additional_data_lists.length > 0) {
                            additional_data_lists.map((additional_item, additional_item_index) => {
                                displayAdditionalData(additional_item_index);
                            });
                        } else {
                            additionalItemsCount = 0;
                            additionalDataCalculate.purchase_order_general_detail_items = []
                            addAnotherAdditionalItem(0);
                        }

                    };

                    display();
                };

                const displayResumeData = () => {
                    $('#resume-section').show();

                    $('#resume-section').html(`
                        <div id="resume-table-calculation">
                            <x-card-data-table id="Rangkuman">
                                <x-slot name="table_content">

                                    <div class="row mt-20">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-select name="tax_data" label="Pajak Pembelian" id="tax-selectForm" multiple>${TAX_OPTION}</x-select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-select name="tax_data" label="Pajak transaksi tambahan" id="taxAdditional-selectForm" multiple>${TAX_LIST_ADDITIONAL_OPTION}</x-select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 d-flex align-self-end">
                                            <div class="form-group">
                                                <x-button color="info" label="kalkulasi-ulang" size="sm" icon="arrows-rotate" fontawesome id="btn-recalculate"></x-button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-20 mt-20">
                                        <h4>Purchase request resume</h4>
                                        <x-table id="resume-purchase-request">
                                            <x-slot name="table_head">
                                                <th>#</th>
                                                <th>{{ Str::headline('Item') }}</th>
                                                <th>{{ Str::headline('Harga Sebelum Diskon') }}</th>
                                                <th>{{ Str::headline('Diskon') }}</th>
                                                <th>{{ Str::headline('Harga') }}</th>
                                                <th>{{ Str::headline('Qty') }}</th>
                                                <th>{{ Str::headline('Sub total') }}</th>
                                                <th>{{ Str::headline('Tax') }}</th>
                                                <th>{{ Str::headline('Value') }}</th>
                                                <th>{{ Str::headline('Total') }}</th>
                                            </x-slot>
                                            <x-slot name="table_body">

                                            </x-slot>
                                            <x-slot name="table_foot">
                                                <tr>
                                                    <td class="fw-bold text-end" colspan="9">Total</td>
                                                    <td class="bg-success text-white text-end" id="total-main"></td>
                                                </tr>
                                            </x-slot>
                                        </x-table>
                                    </div>

                                    <div class="mb-20">
                                        <h4>Additional item resume</h4>
                                        <x-table id="resume-additional-item">
                                            <x-slot name="table_head">
                                                <th>#</th>
                                                <th>{{ Str::headline('Item') }}</th>
                                                <th>{{ Str::headline('Harga') }}</th>
                                                <th>{{ Str::headline('Qty') }}</th>
                                                <th>{{ Str::headline('Sub total') }}</th>
                                                <th>{{ Str::headline('Tax') }}</th>
                                                <th>{{ Str::headline('Value') }}</th>
                                                <th>{{ Str::headline('Total') }}</th>
                                            </x-slot>
                                            <x-slot name="table_body"></x-slot>
                                            <x-slot name="table_foot">
                                                <tr >
                                                    <td class="fw-bold text-end" colspan="7">Total</td>
                                                    <td class="bg-success text-white text-end" id="total-additional"></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold text-end" colspan="7">Grand Total</td>
                                                    <td class="bg-success text-white text-end" id="total-all"></td>
                                                </tr>
                                            </x-slot>
                                        </x-table>
                                    </div>

                                    {{-- <x-table id="resume-all-item">
                                        <x-slot name="table_"></x-slot>
                                        <x-slot name="table_"></x-slot>
                                    </x-table> --}}

                                </x-slot>
                            </x-card-data-table>
                        </div>
                    `);

                    calculateResumeData();
                };

                const calculateResumeData = () => {

                    let total = 0,
                        total_additional = 0,
                        total_main = 0;

                    const calculate = () => {
                        calculateMain();
                        calculateAdditional();
                        calculateTottall();
                    };

                    $('#is_include_tax').click(function(e) {
                        calculate();
                    })

                    const handleTaxSelect = () => {
                        $(`#tax-selectForm`).change(
                            $.debounce(1000, function(e) {
                                TAX_LIST_ID = $(`#tax-selectForm`).val();
                                TAX_LIST_VALUE = [];

                                TAX_LIST_ID.map((tax_id, index) => {
                                    setTimeout(() => {
                                        $.ajax({
                                            type: "get",
                                            url: `{{ route('admin.tax.detail') }}/${tax_id}`,
                                            success: function({
                                                data
                                            }) {
                                                TAX_LIST_VALUE.push(data);
                                            }
                                        });
                                    }, 500);
                                });
                            })
                        );
                        $(`#taxAdditional-selectForm`).change(
                            $.debounce(1000, function(e) {
                                TAX_LIST_ADDITIONAL_ID = $(`#taxAdditional-selectForm`).val();
                                TAX_LIST_ADDITIONAL_VALUE = [];

                                TAX_LIST_ADDITIONAL_ID.map((tax_id, index) => {
                                    setTimeout(() => {
                                        $.ajax({
                                            type: "get",
                                            url: `{{ route('admin.tax.detail') }}/${tax_id}`,
                                            success: function({
                                                data
                                            }) {
                                                TAX_LIST_ADDITIONAL_VALUE.push(data);
                                            }
                                        });
                                    }, 500);
                                });
                            })
                        );
                    };

                    const initTaxSelect = () => {
                        initSelect2SearchPaginationData(`tax-selectForm`, `{{ route('admin.select.tax') }}`, {
                            id: 'id',
                            text: 'name'
                        })
                        initSelect2SearchPaginationData(`taxAdditional-selectForm`, `{{ route('admin.select.tax') }}`, {
                            id: 'id',
                            text: 'name'
                        })
                        handleTaxSelect();
                    };
                    initTaxSelect();

                    const calculateMain = () => {
                        let iteration = 1;
                        total_main = 0;

                        $('#resume-purchase-request tbody').html('');
                        MainDataCalculate.map((main_data, main_data_index) => {

                            main_data.purchase_order_general_detail_item.map((main_data_item, main_data_item_index) => {

                                let {
                                    item,
                                    quantity,
                                    price_before_discount,
                                    discount,
                                    price,
                                    original_price,
                                } = main_data_item;

                                if ($('#is_include_tax').is(':checked')) {
                                    price = original_price
                                    let tax_percentage = 0;
                                    TAX_LIST_VALUE.map((tax_value, tax_value_index) => {
                                        tax_percentage += tax_value.value * 100
                                    });

                                    price -= (original_price - (100 / (100 + tax_percentage) * original_price));
                                    MainDataCalculate[main_data_index].purchase_order_general_detail_item[main_data_item_index].price = price;
                                } else {
                                    price = original_price;
                                    MainDataCalculate[main_data_index].purchase_order_general_detail_item[main_data_item_index].price = original_price;
                                }

                                let sub_total = price * quantity;
                                let single_total = sub_total;
                                let html_tax_name = '',
                                    html_tax_value = '';

                                TAX_LIST_VALUE.map((tax, tax_index) => {
                                    let {
                                        name,
                                        value,
                                    } = tax;

                                    let tax_amount = sub_total * value;

                                    single_total += tax_amount;

                                    html_tax_name += `<p class="my-0">${name} - ${(value *100).toFixed(2)}%</p>`;
                                    html_tax_value += `<p class="text-end my-0">${currency_symbol} ${formatRupiahWithDecimal(tax_amount)}</p> `;
                                });

                                let html = `
                                    <tr>
                                        <td>${iteration}</td>
                                        <td>${item?.nama} - ${item?.kode}</td>
                                        <td>${currency_symbol} ${formatRupiahWithDecimal(price_before_discount)}</td>
                                        <td>${currency_symbol} ${formatRupiahWithDecimal(discount)}</td>
                                        <td>${currency_symbol} ${formatRupiahWithDecimal(price)} / ${item?.unit?.name}</td>
                                        <td class="text-end">${formatRupiahWithDecimal(quantity)} ${item?.unit?.name}</td>
                                        <td class="text-end">${currency_symbol} ${formatRupiahWithDecimal(sub_total)}</td>
                                        <td>${html_tax_name}</td>
                                        <td>${html_tax_value}</td>
                                        <td class="text-end">${currency_symbol} ${formatRupiahWithDecimal(single_total)}</td>
                                    </tr>
                                `;

                                $('#resume-purchase-request tbody').append(html);

                                total_main += single_total;
                                iteration++;
                            });
                        });

                        $('#total-main').html(`${currency_symbol} ${formatRupiahWithDecimal(total_main)}`);
                    };

                    const calculateAdditional = () => {
                        let iteration = 1;
                        total_additional = 0;

                        $('#resume-additional-item tbody').html('');
                        additionalDataCalculate.purchase_order_general_detail_items.map((additional, additional_data) => {

                            if (additional != null) {
                                let {
                                    item,
                                    price,
                                    quantity
                                } = additional;

                                let sub_total = price * quantity;
                                let single_total = sub_total;
                                let html_tax_name = '',
                                    html_tax_value = '';

                                TAX_LIST_ADDITIONAL_VALUE.map((tax, tax_index) => {
                                    let {
                                        name,
                                        value,
                                    } = tax;

                                    let tax_amount = sub_total * value;
                                    single_total += tax_amount;

                                    html_tax_name += `<p class="my-0">${name} - ${(value *100).toFixed(2)}%</p>`;
                                    html_tax_value += `<p class="text-end my-0">${currency_symbol} ${formatRupiahWithDecimal(tax_amount)}</p> `;
                                });


                                let html = `
                                    <tr>
                                        <td>${iteration}</td>
                                        <td>${item?.nama} - ${item?.kode}</td>
                                        <td>${currency_symbol} ${formatRupiahWithDecimal(price)} / ${item?.unit?.name}</td>
                                        <td class="text-end">${formatRupiahWithDecimal(quantity)} ${item?.unit?.name}</td>
                                        <td class="text-end">${currency_symbol} ${formatRupiahWithDecimal(sub_total)}</td>
                                        <td>${html_tax_name}</td>
                                        <td>${html_tax_value}</td>
                                        <td class="text-end">${currency_symbol} ${formatRupiahWithDecimal(single_total)}</td>
                                    </tr>
                                `;

                                $('#resume-additional-item tbody').append(html);

                                total_additional += single_total;
                                iteration++;
                            }
                        })

                        $('#total-additional').html(`${currency_symbol} ${formatRupiahWithDecimal(total_additional)}`);
                    };

                    const calculateTottall = () => {
                        let total = total_main + total_additional;
                        $('#total-all').html(`${currency_symbol} ${formatRupiahWithDecimal(total)}`);
                    };

                    calculate();
                    $('#btn-recalculate').click(function(e) {
                        e.preventDefault();
                        calculate();
                    });

                };

                const handleSubmit = () => {
                    $('#btn-section').show();

                    $('#form-update').submit(function(e) {
                        e.preventDefault();

                        let isError = 0;
                        $('.main-quantity').each(function() {
                            if ($(this).hasClass('is-invalid')) {
                                isError++;
                            }
                        });

                        if (isError !== 0) {
                            showAlert('', 'Masih ada error yang belum diperbaiki!', 'warning');
                        } else {
                            let values = {
                                branch_id: $('#branch-select').val(),
                                date: $('#date-input').val(),
                                currency_id: $('#currency-select').val(),
                                exchange_rate: thousandToFloat($('#exchange-rate').val()),
                                vendor_id: $('#vendor-select').val(),
                                term_of_payment: $('#termOfPayment-select').val(),
                                term_of_payment_days: $('#termOfPayment-day').val(),
                                payment_description: $('#payment_description').val(),

                                main: MainDataCalculate.map((main, main_index) => {
                                    return {
                                        "purchase_request_id": main.purchase_request_id,
                                        "purchase_order_general_detail_id": main.purchase_order_general_detail_id,
                                        "purchase_order_general_detail_items": main.purchase_order_general_detail_item.map((main_data_item, main_data_index) => {
                                            return {
                                                "purchase_order_general_detail_item_id": main_data_item.purchase_order_general_detail_item_id,
                                                "purchase_request_detail_id": main_data_item.purchase_request_detail_id,
                                                "item_id": main_data_item.item.id,
                                                "quantity": main_data_item.quantity,
                                                "price_before_discount": main_data_item.price_before_discount,
                                                "discount": main_data_item.discount,
                                                "price": main_data_item.price,
                                                tax_id: TAX_LIST_ID,
                                            };
                                        }),
                                    };
                                }),

                                additional: {
                                    "purchase_order_general_detail_id": additionalDataCalculate.purchase_order_general_detail_id,
                                    "purchase_order_general_detail_items": additionalDataCalculate.purchase_order_general_detail_items.map((additional, additional_index) => {
                                        if (additional != null) {
                                            return {
                                                "item_id": additional.item.id,
                                                "quantity": additional.quantity,
                                                "price": additional.price,
                                                tax_id: TAX_LIST_ADDITIONAL_ID,
                                            };
                                        }
                                    }),
                                }
                            };

                            $('#value-final-form').val(JSON.stringify(values));

                            $.ajax({
                                type: "post",
                                url: $('form#form-update').attr('action'),
                                data: new FormData($('form#form-update')[0]),
                                contentType: false,
                                processData: false,
                                success: function(response) {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: 'Data berhasil disimpan',
                                        icon: 'success',
                                        confirmButtonColor: '#3085d6',
                                        confirmButtonText: 'Ok'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.reload();
                                        }
                                    });
                                },
                                error: function(xhr, status, error) {
                                    Swal.fire({
                                        title: 'Gagal!',
                                        text: xhr.responseJSON.message,
                                        icon: 'error',
                                        confirmButtonColor: '#3085d6',
                                        confirmButtonText: 'Ok'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            $('#btn-submit').prop('disabled', false);
                                        }
                                    });
                                }
                            })
                        }
                    });
                };


                init()
            });
        </script>
    @endcan

    <script>
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#purchase-menu');
        sidebarActive('#purchase');
    </script>
@endsection
