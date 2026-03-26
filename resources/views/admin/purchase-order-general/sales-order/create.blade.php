@extends('layouts.admin.layout.index')

@php
    $main = 'purchase-order-general';
    $title = Str::headline('Purchase Order general');
    $default_taxes = getDafaultTaxes();
@endphp

@section('title', Str::headline("Create $title") . ' - ')

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
                        {{ Str::headline('Create ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can('create purchase-general')
        <form action="{{ route('admin.purchase-order-general.store') }}" method="post" id="create-form" enctype="multipart/form-data">

            @csrf
            <input type="hidden" name="type" value="sales-order">
            <x-card-data-table :title='"Create $main"'>
                <x-slot name="header_content">

                </x-slot>
                <x-slot name="table_content">
                    @include('components.validate-error')

                    <div id="errorRl" class="alert alert-danger" role="alert" style="display: none">
                        <span id="errorRlMessage"></span>
                    </div>

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
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input class="datepicker-input" name="date" label="tanggal" value="{{ \Carbon\Carbon::now()->format('d-m-Y') }}" id="date-input" onchange="checkClosingPeriod($(this))" required />
                            </div>
                        </div>
                    </div>

                    <div class="row">
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="file" name="quotation" id="" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="vendor_id" label="vendor" id="vendor-select" required>

                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="term_of_payment" id="termOfPayment-select" required>
                                    <option value="" selected>- Pilih term of payment -</option>
                                    <option value="cash">Cash</option>
                                    <option value="by days">By Days</option>
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="number" name="term_of_payment_days" label="term of payment day" id="termOfPayment-day" value="0" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" name="payment_description" label="keterangan pembayaran" />
                            </div>
                        </div>
                        <dic class="col-md-4">
                            <x-input-checkbox label="Include PPN" name="is_include_tax" id="is_include_tax" value="1" />
                        </dic>
                    </div>

                </x-slot>

            </x-card-data-table>

            <x-card-data-table title="Pilih Sales Order">
                <x-slot name="table_content">
                    <div class="row">
                        <div class="col-md-12 text-right align-self-end mb-2">
                            <x-button type="button" color="primary" label="SO Outstanding" data-toggle="modal" data-target="#so-outstanding-modal" />
                        </div>
                        <div class="col-md-12" id="sales-order-detail-list">

                        </div>
                    </div>
                </x-slot>
            </x-card-data-table>

            <div id="additional-item-card">

                <x-card-data-table title="additional item">
                    <x-slot name="table_content">
                        <div id="additional-item-row">

                        </div>
                    </x-slot>
                </x-card-data-table>

            </div>

            <div id="resume-table-calculation">
                <x-card-data-table id="Rangkuman">
                    <x-slot name="table_content">

                        <div class="row mt-20">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="tax_data" label="Pajak pembelian" id="tax-selectForm" multiple>
                                        @foreach ($default_taxes as $tax)
                                            <option value="{{ $tax->id }}" selected>{{ $tax->name }}</option>
                                        @endforeach
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="tax_data" label="Pajak transaksi tambahan" id="taxAdditional-selectForm" multiple>
                                        @foreach ($default_taxes as $tax)
                                            <option value="{{ $tax->id }}" selected>{{ $tax->name }}</option>
                                        @endforeach
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-self-end">
                                <div class="form-group">
                                    <x-button color="info" label="kalkulasi-ulang" size="sm" icon="arrows-rotate" fontawesome id="btn-recalculate"></x-button>
                                </div>
                            </div>
                        </div>

                        <div class="mb-20 mt-20">
                            <h4>Sales order resume</h4>
                            <x-table id="resume-sales-order">
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
                                    <tr>
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

                    </x-slot>
                </x-card-data-table>
            </div>

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
        </form>
    @endcan

    <div class="modal fade" id="so-outstanding-modal" tabindex="-1" role="dialog" aria-labelledby="so-outstanding-label" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="so-outstanding-label">SO Outstanding</h5>
                    <a href="javascript:;" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </a>
                </div>
                <!-- Modal Body -->
                <div class="modal-body">
                    <x-table id="so-outstanding-table">
                        <x-slot name="table_head">
                            <th>#</th>
                            <th>Tanggal</th>
                            <th>No. SO</th>
                            <th>Quotation</th>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Stock</th>
                            <th>Jumlah DO</th>
                            <th>Jumlah PO</th>
                            <th>Sisa</th>
                            <th>Lokasi</th>
                            <th>Customer</th>
                            <th>Checklist</th>
                        </x-slot>
                        <x-slot name="table_body">
                        </x-slot>
                    </x-table>
                </div>
                <!-- Modal Footer -->
                <div class="modal-footer text-end">
                    <a href="javascript:;" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Close</span>
                    </a>
                    <x-button type="button" color="primary" label="Save" icon="save" fontawesome id="btn-submit" onclick="get_selected_sale_order_generals()" />
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('js/admin/select/branch.js') }}"></script>
    <script src="{{ asset('js/admin/select/itemSelect.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>

    <script>
        let salesOrders = [];
        var selected_ids = [];

        function checkThis(e) {
            if ($(e).is(':checked')) {
                selected_ids.push($(e).val());
            } else {
                selected_ids.splice(selected_ids.indexOf($(e).val()), 1);
            }
        }

        function calculate_final_price(row_index, index) {
            let price_before_discount = $(`#main-item-price-before-discount-${row_index}-${index}`).val();
            let discount = $(`#main-item-discount-${row_index}-${index}`).val();
            let price_after_discount = thousandToFloat(price_before_discount) - thousandToFloat(discount);

            $(`#main-item-price-${row_index}-${index}`).val(formatRupiahWithDecimal(price_after_discount)).trigger('keyup');
        }

        function get_selected_sale_order_generals() {
            $('#so-outstanding-modal').modal('hide');

            $.ajax({
                url: "{{ route('admin.purchase-order-general.get-selected-sale-order-general') }}",
                type: "post",
                data: {
                    _token: "{{ csrf_token() }}",
                    selected_ids: selected_ids
                },
                success: function(res) {
                    salesOrders = res;
                    $('#sales-order-detail-list').html('');
                    salesOrders.map((sale_order, sale_order_index) => {
                        sale_order.children.map((child, child_index) => {
                            $(`#main-item-select-${sale_order_index}-${child_index}`).unbind('change');
                            $(`#main-item-price-${sale_order_index}-${child_index}`).unbind('keyup');
                            $(`#main-item-quantity-${sale_order_index}-${child_index}`).unbind('keyup');
                            $(`#sales-orderDetail-btnmodal-${sale_order_index}-${child_index}`).unbind('click');

                            let html = `
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <x-select name="main_item_id[]" label="item" id="main-item-select-${sale_order_index}-${child_index}" required>
                                                        <option value="${child.item.id}" selected>${child.item.nama} - ${child.item.kode}</option>
                                                    </x-select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <x-input type="text" name="main_unit[]" label="satuan" id="main-item-unit-${sale_order_index}-${child_index}" value="${child.item.unit.name}" required readonly />
                                                </div>
                                            </div>
                                             <div class="col-md-2">
                                            <div class="form-group">
                                                <x-input type="text" name="main_price_before_discount[]" label="harga sebelum diskon" value="${child.price}" class="commas-form" id="main-item-price-before-discount-${sale_order_index}-${child_index}" required />
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-input type="text" name="main_discount[]" label="diskon" value="" class="commas-form" id="main-item-discount-${sale_order_index}-${child_index}" required />
                                            </div>
                                        </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <x-input type="text" name="main_price[]" label="harga" value="${child.price}" class="commas-form" id="main-item-price-${sale_order_index}-${child_index}" required />
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <x-input type="text" name="main_jumlah_diminta[]" label="Jml SO / PO / Sisa" value="${formatRupiahWithDecimal(child.amount)} / ${formatRupiahWithDecimal(child.qty_po)} / ${formatRupiahWithDecimal(child.outstanding_amount)}" id="main-item-quantityRequested-${sale_order_index}-${child_index}" required readonly/>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <x-input type="text" name="main_quantity[]" label="jumlah" value="${child.quantity}" class="commas-form main-quantity" id="main-item-quantity-${sale_order_index}-${child_index}" useCustomError required />
                                                </div>
                                            </div>
                                        </div>
                                    `;

                            $(`#sales-order-detail-list`).append(html);

                            initCommasForm();

                            $(`#main-item-price-before-discount-${sale_order_index}-${child_index}`).on('blur', function(e) {
                                calculate_final_price(sale_order_index, child_index);
                                child.price_before_discount = thousandToFloat(this.value);
                            });

                            $(`#main-item-discount-${sale_order_index}-${child_index}`).on('blur', function(e) {
                                calculate_final_price(sale_order_index, child_index);
                                child.discount = thousandToFloat(this.value);
                            });

                            $(`#main-item-price-${sale_order_index}-${child_index}`).keyup(function(e) {
                                e.preventDefault();
                                child.price = thousandToFloat(this.value);
                                child.original_price = thousandToFloat(this.value);
                            })

                            $(`#main-item-quantity-${sale_order_index}-${child_index}`).data('testQty', child.amount - child.amount_paired);
                            $(`#main-item-quantity-${sale_order_index}-${child_index}`).keyup(function(e) {
                                e.preventDefault();

                                let req_qty = $(this).data('testQty');
                                child.quantity = thousandToFloat(this.value);

                                if (child.quantity > req_qty) {
                                    $(this).addClass('is-invalid');
                                    $(`#error-message-for-main-item-quantity-${sale_order_index}-${child_index}`).text('Jumlah tidak boleh lebih dari jumlah diminta!');
                                } else {
                                    $(this).removeClass('is-invalid');
                                    $(`#error-message-for-main-item-quantity-${sale_order_index}-${child_index}`).text(null);
                                }
                            })

                            $(`#purchase-requestDetail-btnmodal-${sale_order_index}-${child_index}`).click(function(e) {
                                e.preventDefault();
                                $(`#modalpurchase-requestDetail-modal-${sale_order_index}-${child_index}`).modal('show');
                            });

                            $(`#main-item-quantityRequested-${sale_order_index}-${child_index}`).trigger('keyup');
                            $(`#main-item-quantityRequested-${sale_order_index}-${child_index}`).trigger('blur');
                            $(`#main-item-quantity-${sale_order_index}-${child_index}`).trigger('keyup');
                            $(`#main-item-quantity-${sale_order_index}-${child_index}`).trigger('blur');
                        });
                    })
                }
            });
        }

        $(document).ready(function() {
            setTimeout(() => {
                $('#tax-selectForm').trigger('change');
                $('#taxAdditional-selectForm').trigger('change');
            }, 250)

            checkClosingPeriod($('#date-input'));
            let mainItemRowIndex = 0;
            let additionalItemIndex = 0;

            let additionalItems = [];

            let BRANCH_ID = "{{ get_current_branch_id() }}";

            let currency = {!! get_local_currency() !!};

            let TAX_LIST_VALUE = [],
                TAX_LIST_ID = [];

            let TAX_LIST_ADDITIONAL_VALUE = [],
                TAX_LIST_ADDITIONAL_ID = [];

            const init = () => {
                initializeFirstCard();
                initializeThirdCard();
                initializeFourthcard();
                handleSubmit();
            };

            const initializeFirstCard = () => {

                const initFirstCard = () => {
                    initializeSelect();
                    selectHandle();
                };

                const initializeSelect = () => {
                    initSelect2SearchPaginationData(`branch-select`, `{{ route('admin.select.branch') }}`, {
                        id: 'id',
                        text: 'name'
                    })
                    initSelect2SearchPaginationData(`currency-select`, `{{ route('admin.select.currency') }}`, {
                        id: 'id',
                        text: 'kodes,nama,negara'
                    })
                    initSelect2SearchPaginationData(`vendor-select`, `{{ route('admin.select.vendor') }}`, {
                        id: 'id',
                        text: 'nama'
                    })

                };

                const selectHandle = () => {

                    $('#branch-select').change(function(e) {
                        e.preventDefault();
                        BRANCH_ID = $(this).val();
                        Swal.fire({
                            title: "Warning",
                            text: "Anda mengganti branch, tolong reset data purchase (Sales Order) anda menjadi branch yang di pilih",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "Ya",
                        })
                        resetData();
                    });

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

                                currency = data;

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

                };

                initFirstCard();
            };

            const initializeThirdCard = () => {
                const initThirdCard = () => {
                    addAnotherAdditionalItem(additionalItemIndex);
                };

                const addAnotherAdditionalItem = (row_index) => {
                    additionalItemIndex++;

                    additionalItems[row_index] = {
                        item: [],
                        price: 0,
                        quantity: 0
                    }

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
                                addAnotherAdditionalItem(additionalItemIndex);
                            });
                        } else {
                            $(`#btn-add-removeItem-${row_index}`).click(function(e) {
                                e.preventDefault();
                                removeAdditionalItem(row_index);
                            });
                        }

                        handleEventListener(row_index);
                    };

                    const handleEventListener = (row_index) => {

                        initCommasForm();
                        $(`#additional-itemType-select-${row_index}`).change(function(e) {
                            e.preventDefault();

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

                            additionalItems[row_index] = {
                                item: [],
                                price: 0,
                                quantity: 0
                            }

                            if (this.value) {

                                initSelect2SearchPaginationData(`additional-item-select-${row_index}`, `{{ route('admin.select.item.type') }}/${this.value}`, {
                                    id: 'id',
                                    text: 'kode,nama'
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
                                                additionalItems[row_index].item = data;
                                                $(`#additional-item-unit-${row_index}`).val(data.unit.name);
                                            }
                                        });
                                    } else {
                                        $(`#additional-item-price-${row_index}`).val(0);
                                        $(`#additional-item-price-${row_index}`).trigger('keyup');
                                        $(`#additional-item-price-${row_index}`).trigger('blur');
                                    }
                                });

                                $(`#additional-item-price-${row_index}`).keyup(function(e) {
                                    additionalItems[row_index].price = thousandToFloat($(this).val());
                                });

                                $(`#additional-item-quantity-${row_index}`).keyup(function(e) {
                                    additionalItems[row_index].quantity = thousandToFloat($(this).val());
                                });
                            }
                        });
                    };

                    const resetEventListerner = (row_index) => {
                        $(`#additional-item-select-${row_index}`).unbind('change');
                        $(`#additional-item-price-${row_index}`).unbind('keyup');
                        $(`#additional-item-quantity-${row_index}`).unbind('keyup');
                    };

                    handleAdd(row_index)
                };

                const removeAdditionalItem = (row_index) => {
                    $(`#additional-row-${row_index}`).remove();
                    additionalItems[row_index] = null;
                };

                initThirdCard()
            };


            const initializeFourthcard = () => {

                let total = 0,
                    total_main = 0,
                    total_additional = 0;

                const calculate = () => {
                    calculateSalesOrder();
                    calculateAdditional();
                    calculateTotal();
                };

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

                const calculateSalesOrder = () => {
                    let iteration = 1;
                    let currency_symbol = currency.simbol
                    total_main = 0;

                    $('#resume-sales-order tbody').html('');
                    salesOrders.map((sale_order, sale_order_index) => {

                        console.log(sale_order);
                        if (sale_order != null) {
                            let {
                                parent,
                                children
                            } = sale_order;


                            children?.map((sale_order_detail, sale_order_detail_index) => {
                                let {
                                    amount,
                                    amount_paired,
                                    item_id,
                                    price_before_discount,
                                    discount,
                                    price,
                                    quantity,
                                    original_price,
                                } = sale_order_detail;

                                if ($('#is_include_tax').is(':checked')) {
                                    price = original_price
                                    let tax_percentage = 0;
                                    TAX_LIST_VALUE.map((tax_value, tax_value_index) => {
                                        tax_percentage += tax_value.value * 100
                                    });

                                    price -= (original_price - (100 / (100 + tax_percentage) * original_price));
                                    salesOrders[sale_order_index].children[sale_order_detail_index].price = price;
                                } else {
                                    price = original_price;
                                    salesOrders[sale_order_index].children[sale_order_detail_index].price = original_price;
                                }

                                let sub_total = price * quantity;
                                let single_total = sub_total;
                                let html_tax_name = '',
                                    html_tax_value = '';

                                TAX_LIST_VALUE.map((tax_value, tax_value_index) => {

                                    let {
                                        name,
                                        value,
                                    } = tax_value;

                                    let tax_amount = sub_total * value;

                                    single_total += tax_amount;

                                    html_tax_name += `<p class="my-0">${name} - ${(value *100).toFixed(2)}%</p>`;
                                    html_tax_value += `<p class="text-end my-0">${currency_symbol} ${formatRupiahWithDecimal(tax_amount)}</p> `;
                                });

                                let html = `
                                        <tr>
                                            <td>${iteration}</td>
                                            <td>${sale_order_detail.item.nama} - ${sale_order_detail.item.kode}</td>
                                            <td>${currency_symbol} ${formatRupiahWithDecimal(price_before_discount)}</td>
                                            <td>${currency_symbol} ${formatRupiahWithDecimal(discount)}</td>
                                            <td>${currency_symbol} ${formatRupiahWithDecimal(price)} / ${sale_order_detail.item.unit.name}</td>
                                            <td class="text-end">${formatRupiahWithDecimal(quantity)} ${sale_order_detail.item.unit.name}</td>
                                            <td class="text-end">${currency_symbol} ${formatRupiahWithDecimal(sub_total)}</td>
                                            <td>${html_tax_name}</td>
                                            <td>${html_tax_value}</td>
                                            <td class="text-end">${currency_symbol} ${formatRupiahWithDecimal(single_total)}</td>
                                        </tr>
                                    `;
                                total_main += single_total;

                                $('#resume-sales-order tbody').append(html);
                                iteration++;
                            });
                        }
                    });
                    $('#total-main').html(`${currency_symbol} ${formatRupiahWithDecimal(total_main)}`);
                };

                const calculateAdditional = () => {
                    let iteration = 1;
                    let currency_symbol = currency.simbol;
                    total_additional = 0;

                    $('#resume-additional-item tbody').html('');
                    additionalItems.map((additional_item, additional_item_index) => {

                        if (additional_item != null) {
                            let {
                                item,
                                price,
                                quantity
                            } = additional_item;

                            let sub_total = price * quantity;
                            let single_total = sub_total;
                            let html_tax_name = '',
                                html_tax_value = '';

                            TAX_LIST_ADDITIONAL_VALUE.map((tax_value, tax_value_index) => {

                                let {
                                    name,
                                    value,
                                } = tax_value;

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

                            total_additional += single_total;
                            $('#resume-additional-item tbody').append(html);
                            iteration++;
                        }
                    });

                    $('#total-additional').html(`${currency_symbol} ${formatRupiahWithDecimal(total_additional)}`);
                };

                const calculateTotal = () => {
                    let currency_symbol = currency.simbol;
                    let total = total_main + total_additional;
                    $('#total-all').html(`${currency_symbol} ${formatRupiahWithDecimal(total)}`);
                };

                $('#btn-recalculate').click(function(e) {
                    e.preventDefault();
                    calculate();
                });

                $('#is_include_tax').click(function(e) {
                    calculate();
                })
            };

            const handleSubmit = (row_index = 0) => {
                $('form#create-form').submit(function(e) {
                    e.preventDefault();

                    let isError = 0;
                    salesOrders[row_index].children.map((sale_order, sale_order_index) => {
                        if ($(`#main-item-quantity-${row_index}-${sale_order_index}`).val() == 0) {
                            $(`#main-item-quantity-${row_index}-${sale_order_index}`).val('')
                            isError++;
                        }
                    })

                    $('.main-quantity').each(function() {
                        if ($(this).hasClass('is-invalid')) {
                            isError++;
                        }
                    });

                    if (isError !== 0) {
                        showAlert('', 'Masih ada error yang belum diperbaiki!', 'warning');
                        $(this).find('button[type=submit]').prop('disabled', false);
                    } else {
                        $.ajax({
                            type: "post",
                            url: `${base_url}/rate-limiter/ajax`,
                            data: {
                                _token: token,
                                key: "create: " + "{{ $main }}",
                                attempts: 2, // default is 2 attempts
                                decay_seconds: 3,
                            },
                            success: function(response) {
                                if (response.is_too_many_requests == true) {
                                    let waitingTime = parseInt(response.available_at_time);

                                    $('#errorRl').show();
                                    $('#errorRlMessage').text('Terlalu banyak permintaan menyimpan data, harap tunggu ' + waitingTime + " detik lagi");

                                    let showError = setInterval(() => {
                                        waitingTime--;

                                        if (waitingTime > 0 && waitingTime <= 60) {
                                            $('#errorRlMessage').text('Terlalu banyak permintaan menyimpan data, harap tunggu ' + waitingTime + " detik lagi");
                                        }

                                        if (waitingTime == 0) {
                                            $('#errorRl').hide();
                                            $('#save-data').prop('disabled', false);
                                            clearInterval(showError);
                                        }
                                    }, 1000);
                                } else {
                                    let values = {
                                        // _token: token,
                                        branch_id: $('#branch-select').val(),
                                        date: $('#date-input').val(),
                                        currency_id: $('#currency-select').val(),
                                        exchange_rate: thousandToFloat($('#exchange-rate').val()),
                                        vendor_id: $('#vendor-select').val(),
                                        term_of_payment: $('#termOfPayment-select').val(),
                                        term_of_payment_days: $('#termOfPayment-day').val(),

                                        main: salesOrders.map((sale_order, sale_order_detail) => {

                                            if (sale_order != null) {
                                                let {
                                                    parent,
                                                    children
                                                } = sale_order;
                                                return {
                                                    sale_order_id: parent.id,
                                                    sale_order_detail_id: children.map((sale_order_detail, sale_order_detail_index) => {
                                                        let {
                                                            id,
                                                            item_id,
                                                            price_before_discount,
                                                            discount,
                                                            price,
                                                            quantity
                                                        } = sale_order_detail;

                                                        return {
                                                            sale_order_detail_id: id,
                                                            item_id: item_id,
                                                            quantity: quantity,
                                                            price: price,
                                                            price_before_discount: price_before_discount,
                                                            discount: discount,
                                                            tax_id: TAX_LIST_ID
                                                        };
                                                    })
                                                }
                                            }
                                        }),
                                        additional: additionalItems.map((additional, additional_index) => {

                                            if (additional != null) {
                                                let {
                                                    item,
                                                    price,
                                                    quantity
                                                } = additional;

                                                if (item != [] && quantity != 0 && price != 0) {
                                                    return {
                                                        item_id: item.id,
                                                        quantity: quantity,
                                                        price: price,
                                                        tax_id: TAX_LIST_ADDITIONAL_ID,
                                                    };
                                                }
                                            }
                                        })
                                    };

                                    $('#value-final-form').val(JSON.stringify(values))

                                    $.ajax({
                                        type: "post",
                                        url: $('form#create-form').attr('action'),
                                        data: new FormData($('form#create-form')[0]),
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
                                                    window.location.href = '{{ route('admin.purchase.index') }}';
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
                            }
                        });
                    }
                });
            };

            $('#so-outstanding-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                destroy: true,
                order: [1, 'desc'],
                ajax: {
                    url: '{{ route('admin.purchase-order-general.so-outstanding-data') }}',
                    type: 'POST',
                    data: {
                        _token: token,
                        selected_ids: function() {
                            return selected_ids
                        }
                    },
                    complete: function() {
                        $('.checkbox-select').css('left', 'unset').css('position', 'unset').css('opacity', 'unset');
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tanggal',
                        name: 'sale_order_generals.tanggal'
                    },
                    {
                        data: 'kode',
                        name: 'sale_order_generals.kode'
                    },
                    {
                        data: 'quotation',
                        name: 'sale_order_generals.quotation'
                    },
                    {
                        data: 'item_name',
                        name: 'items.nama'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'stock',
                        name: 'stock',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'sent_qty',
                        name: 'sent_qty',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'purchased_qty',
                        name: 'purchased_qty',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'outstanding_qty',
                        name: 'outstanding_qty',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'branch_name',
                        name: 'branches.name'
                    },
                    {
                        data: 'customer_name',
                        name: 'customers.nama'
                    },
                    {
                        data: 'check',
                        name: 'sale_order_generals.id',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            init();
        });
    </script>

    <script>
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#purchase-menu');
        sidebarActive('#purchase');
    </script>
@endsection
