@extends('layouts.admin.layout.index')

@php
    $main = 'purchase-order-service';
    $title = Str::headline('Purchase Order service');
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
    @can('create purchase-service')
        <form action="{{ route('admin.purchase-order-service.store') }}" method="post" id="create-form" enctype="multipart/form-data">
            @csrf
            <x-card-data-table :title='"Create $main"'>
                <x-slot name="header_content">

                </x-slot>
                <x-slot name="table_content">
                    @include('components.validate-error')

                    @if (get_current_branch()->is_primary)
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select label="branch" name="branch_id" id="branch-select" required>
                                        <option value="{{ get_current_branch()->id }}">{{ get_current_branch()->name }}</option>
                                    </x-select>
                                </div>
                            </div>
                        </div>
                    @endif

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
                                <x-input type="number" name="term_of_payment_days" label="term of payment day" id="termOfPayment-day" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" name="payment_description" label="keterangan pembayaran" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <x-input-checkbox label="Buat SPK" name="is_spk" id="is_spk" value="1" />
                        </div>
                        <div class="col-md-12 d-none" id="spk-form">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input type="text" name="pic" id="pic" label="PIC" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <x-input-checkbox label="Include PPN" name="is_include_tax" id="is_include_tax" value="1" />
                        </div>
                    </div>

                </x-slot>

            </x-card-data-table>

            <div id="purchase-request-row">

            </div>

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
                                    <x-select name="tax_data" label="Pajak Pembelian" id="tax-selectForm" multiple></x-select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="tax_data" label="Pajak transaksi tambahan" id="taxAdditional-selectForm" multiple></x-select>
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

                        {{-- <x-table id="resume-all-item">
                            <x-slot name="table_"></x-slot>
                            <x-slot name="table_"></x-slot>
                        </x-table> --}}

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
@endsection

@section('js')
    <script src="{{ asset('js/admin/select/branch.js') }}"></script>
    <script src="{{ asset('js/admin/select/itemSelect.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>

    <script>
        $(document).ready(function() {
            checkClosingPeriod($('#date->input'))
            let mainItemRowIndex = 0;
            let additionalItemIndex = 0;

            let purchaseRequests = [];
            let additionalItems = [];

            let BRANCH_ID = "{{ get_current_branch_id() }}";

            let currency = {!! get_local_currency() !!};

            let TAX_LIST_VALUE = [],
                TAX_LIST_ID = [];

            let TAX_LIST_ADDITIONAL_VALUE = [],
                TAX_LIST_ADDITIONAL_ID = [];

            const init = () => {
                initializeFirstCard();
                initializeSecondCard();
                initializeThirdCard();
                initializeFourthcard();
                handleSubmit();
            };

            const resetData = () => {
                //
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
                        text: 'kode,nama,negara'
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
                            text: "Anda mengganti branch, tolong reset data purchase (purchase request) anda menjadi branch yang di pilih",
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

            const initializeSecondCard = () => {

                const initSecondCard = () => {
                    addAnotherPurchaseRequest(mainItemRowIndex);
                };

                const addAnotherPurchaseRequest = (row_index) => {
                    mainItemRowIndex++;

                    purchaseRequests[row_index] = [];

                    const handleAdd = (row_index) => {
                        handleParentForm(row_index);
                        handleButton(row_index);
                        handlePurchaseRequestSelect(row_index);
                        handleModal(row_index);

                        $(`#btn-purchaseRequest-modal-${row_index}`).click(function(e) {
                            e.preventDefault();

                            $(`#modalSelectPurchaseRequestDetail-${row_index}`).modal('show');
                            displayPurchaseRequestDetail(row_index);
                        });
                    };

                    const handleParentForm = (row_index) => {
                        let html = `
                            <x-card-data-table id="purchase-request-card-${row_index}">
                                <x-slot name="table_content">

                                    <x-modal title="Pilih purchase request item" id="modalSelectPurchaseRequestDetail-${row_index}" headerColor="info" modalSize="1000">
                                        <x-slot name="modal_body">
                                            <div class="row mb-30">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label">{{ Str::headline('tanggal') }}</label>
                                                        <p id="purchase-request-parent-date-${row_index}"></p>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label">{{ Str::headline('kode') }}</label>
                                                        <p id="purchase-request-parent-code-${row_index}"></p>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label">{{ Str::headline('divisi') }}</label>
                                                        <p id="purchase-request-parent-divisi-${row_index}"></p>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label">{{ Str::headline('dibuat oleh') }}</label>
                                                        <p id="purchase-request-parent-user-${row_index}"></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <x-table id="purchase-request-table-${row_index}">
                                                <x-slot name="table_head">
                                                    <th>#</th>
                                                    <th>item</th>
                                                    <th>Jumlah Dipesan</th>
                                                    <th>Jumlah Di Lock</th>
                                                    <th>Attachment</th>
                                                    <th></th>
                                                </x-slot>
                                                <x-slot name="table_body">
                                                </x-slot>
                                            </x-table>
                                        </x-slot>
                                        <x-slot name="modal_footer">
                                            <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" />
                                            <x-button type="button" color="primary" label="Save" id="btn-save-modalSelectPurchaseRequestDetail-${row_index}"/>
                                        </x-slot>
                                    </x-modal>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-select name="purchase_request_id[]" label="Purchase request" id="purchaseRequest-select-${row_index}" required></x-select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 d-flex align-self-end">
                                            <div class="form-group">
                                                <x-button color="info" id="btn-purchaseRequest-modal-${row_index}" label="pilih purchase request item" icon="plus" fontawesome size="sm" dataToggle="modal" dataTarget="#modalSelectPurchaseRequestDetail-${row_index}" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-10 pt-10 border-top border-primary" id="purchase-request-detail-list-${row_index}">

                                    </div>

                                    <div id="purchase-btn-${row_index}" class="d-flex justify-content-end gap-3">
                                        <x-button color="info" label="Tambah purchase request" icon="plus" fontawesome id="btn-addOtherPurchaseRequest-${row_index}" />
                                        <x-button color="danger" icon="trash" fontawesome id="btn-deleteOtherPurchaseRequest-${row_index}" />
                                    </div>

                                </x-slot>
                            </x-card-data-table>
                        `;
                        $('#purchase-request-row').append(html);
                    };

                    const handleButton = (row_index) => {
                        $(`#btn-deleteOtherPurchaseRequest-${row_index}`).hide();
                        $(`#btn-addOtherPurchaseRequest-${row_index}`).click(function(e) {
                            e.preventDefault();

                            $(this).hide();
                            $(`#btn-deleteOtherPurchaseRequest-${row_index}`).show();
                            addAnotherPurchaseRequest(mainItemRowIndex);
                        });

                        $(`#btn-deleteOtherPurchaseRequest-${row_index}`).click(function(e) {
                            e.preventDefault();
                            removePurchaseRequest(row_index);
                        });
                    };

                    const handlePurchaseRequestSelect = (row_index) => {
                        const initPurchaseRequestSelect = (element) => {
                            var select2Option = {
                                placeholder: "Pilih purchase request",
                                allowClear: true,
                                width: "100%",
                                language: {
                                    noResults: () => {
                                        return "Data tidak ditemukan";
                                    },
                                },
                                ajax: {
                                    url: `{{ route('admin.select.purchase-request') }}/jasa`,
                                    dataType: "json",
                                    delay: 250,
                                    type: "get",
                                    data: (params) => {
                                        let result = {};
                                        result["search"] = params.term;
                                        result["page_limit"] = 10;
                                        result["page"] = params.page;
                                        result["branch_id"] = function() {
                                            return @json(get_current_branch()->is_primary) ? null : BRANCH_ID
                                        };
                                        // result[target] = target_value;
                                        return result;
                                    },
                                    processResults: (data, params) => {
                                        params.page = params.page || 1;
                                        let final_data = data.data.map((data, key) => {
                                            return {
                                                id: data.id,
                                                text: `${data.kode} - ${data.tanggal}`,
                                            };
                                        });
                                        return {
                                            results: final_data,
                                            pagination: {
                                                more: params.page * 10 < data.total,
                                            },
                                        };
                                    },
                                    cache: true,
                                },
                            };

                            let elements = $(element);
                            if (elements.length > 1) {
                                $.each(elements, function(e) {
                                    $(this).select2(select2Option);
                                });
                            } else {
                                $(element).select2(select2Option);
                            }
                        };

                        initPurchaseRequestSelect(`#purchaseRequest-select-${row_index}`);
                        $(`#purchaseRequest-select-${row_index}`).change(function(e) {
                            e.preventDefault();

                            // * reset card
                            let {
                                parent,
                                children
                            } = purchaseRequests[row_index];
                            $(`#purchase-request-table-${row_index} tbody`).html('');
                            $(`#purchase-request-detail-list-${row_index}`).html('');

                            // * reset data
                            purchaseRequests[row_index] = {
                                parent: [],
                                children: [],
                            };

                            if (this.value) {
                                $.ajax({
                                    type: "get",
                                    url: `{{ route('admin.purchase-request.detail') }}/${this.value}`,
                                    success: function({
                                        data
                                    }) {
                                        let purchaseRequestDate = parseDate(localDate(data.model.tanggal));
                                        let purchaseOrderDate = parseDate($('#date-input').val());

                                        if (purchaseRequestDate > purchaseOrderDate) {
                                            alert('Tanggal purchase request tidak boleh lebih besar dari tanggal pembelian')
                                            $(`#purchaseRequest-select-${row_index}`).html('')
                                            $(`#purchaseRequest-select-${row_index}`).html(null);
                                            $(`#purchaseRequest-select-${row_index}`).trigger('change');

                                            return;
                                        } else {
                                            let {
                                                model,
                                                purchase_request_details
                                            } = data;

                                            let details = purchase_request_details.map((detail) => {
                                                return {
                                                    data: detail,
                                                    item: [],
                                                    selected: false,
                                                    quantity: 0,
                                                    price: 0
                                                }
                                            })

                                            purchaseRequests[row_index] = {
                                                parent: model,
                                                children: details,
                                            }
                                        }
                                    }
                                });
                            }

                        });
                    };

                    const handleModal = (row_index) => {

                        $(`#btn-save-modalSelectPurchaseRequestDetail-${row_index}`).click(function(e) {
                            e.preventDefault();

                            purchaseRequests[row_index].children.map((purchase_request, purchase_request_index) => {
                                purchase_request.selected = $(`#purchase-request-${row_index}-${purchase_request_index}`).is(':checked');
                            });

                            $(`#modalSelectPurchaseRequestDetail-${row_index}`).modal('hide');
                            displayPurchaseRequestDetailForm(row_index);
                        });

                        $(`#modalSelectPurchaseRequestDetail-${row_index}`).on('modal.bs.hide', function(e) {
                            purchaseRequests[row_index].children.map((purchase_request, purchase_request_index) => {
                                $(`#purchase-request-${row_index}-${purchase_request_index}`).unbind('click');
                            });
                        });
                    };

                    const displayPurchaseRequestDetail = (row_index) => {
                        $(`#purchase-request-table-${row_index} tbody`).html('');

                        let purchase_requst_this_index = purchaseRequests[row_index];

                        let {
                            parent
                        } = purchase_requst_this_index;

                        let {
                            kode,
                            tanggal,
                            division,
                            created_by_user
                        } = parent

                        $(`#purchase-request-parent-date-${row_index}`).html(tanggal);
                        $(`#purchase-request-parent-code-${row_index}`).html(kode);
                        $(`#purchase-request-parent-divisi-${row_index}`).html(division.name);
                        $(`#purchase-request-parent-user-${row_index}`).html(`${created_by_user.name} - ${created_by_user.email}`);

                        let html = ``;
                        purchase_requst_this_index.children.map((purchase_request, purchase_request_index) => {

                            let data = purchase_request.data;
                            let item_value = data.item_id ? data.item_data.nama + " - " + data.item_data.kode : data.item;
                            html = `
                                <tr>
                                    <td>${purchase_request_index + 1}</td>
                                    <td>${item_value} - ${data.unit?.name}</td>
                                    <td>${data.jumlah_diapprove}</td>
                                    <td>${data.qty_lock}</td>
                                    <td>
                                        <x-button color="info" icon="file" fontawesome size="sm" link="{{ url('storage/') }}/${data.file}" target="blank" />
                                    </td>
                                    <td>
                                        <x-input-checkbox label="-" name="check" id="purchase-request-${row_index}-${purchase_request_index}" hideAsterix="1"/>
                                    </td>
                                </tr>
                            `;

                            $(`#purchase-request-table-${row_index} tbody`).append(html);
                        });

                    };

                    function calculate_final_price(row_index, index) {
                        let price_before_discount = $(`#main-item-price-before-discount-${row_index}-${index}`).val();
                        let discount = $(`#main-item-discount-${row_index}-${index}`).val();
                        let price_after_discount = thousandToFloat(price_before_discount) - thousandToFloat(discount);

                        $(`#main-item-price-${row_index}-${index}`).val(formatRupiahWithDecimal(price_after_discount)).trigger('keyup');
                    }

                    const displayPurchaseRequestDetailForm = (row_index) => {
                        $(`#purchase-request-detail-list-${row_index}`).html('');

                        purchaseRequests[row_index].children.map((purchase_request, purchase_request_index) => {

                            // * reset event listerner
                            $(`#main-item-select-${row_index}-${purchase_request_index}`).unbind('change')
                            $(`#main-item-price-${row_index}-${purchase_request_index}`).unbind('keyup')
                            $(`#main-item-quantity-${row_index}-${purchase_request_index}`).unbind('keyup')
                            $(`#purchase-requestDetail-btnmodal-${row_index}-${purchase_request_index}`).unbind('click')

                            if (purchase_request.selected) {
                                let {
                                    data
                                } = purchase_request;

                                let item_value = data.item_id ? data.item_data.nama + " - " + data.item_data.kode : data.item;

                                let item_html = ``;
                                if (data.item_id) {
                                    purchase_request.item = data.item_data;
                                    item_html = `
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-select name="main_item_id[]" label="item" id="main-item-select-${row_index}-${purchase_request_index}" required disabled>
                                                    <option value="${data.item_data.id}" selected>${data.item_data.kode} - ${data.item_data.nama}</option>
                                                </x-select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-input type="text" name="main_unit[]" label="satuan" id="main-item-unit-${row_index}-${purchase_request_index}" value="${purchase_request.item.unit.name}" required readonly />
                                            </div>
                                        </div>
                                        `;
                                } else if (!data.item_id) {

                                    item_html = `
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-select name="main_item_id[]" label="item" id="main-item-select-${row_index}-${purchase_request_index}" required>
                                                    <option value="${purchase_request?.item?.id}" selected>${purchase_request?.item?.kode} - ${purchase_request?.item?.nama}</option>
                                                </x-select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-input type="text" name="main_unit[]" label="satuan" id="main-item-unit-${row_index}-${purchase_request_index}" value="${purchase_request?.item?.unit?.name}" required readonly />
                                            </div>
                                        </div>
                                    `;
                                } else {
                                    item_html = `
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-select name="main_item_id[]" label="item" id="main-item-select-${row_index}-${purchase_request_index}" required>

                                                </x-select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-input type="text" name="main_unit[]" label="satuan" id="main-item-unit-${row_index}-${purchase_request_index}" value="" required readonly />
                                            </div>
                                        </div>
                                    `;
                                }

                                if (purchase_request.quantity === 0) {
                                    purchase_request.quantity = data.jumlah_diapprove - data.qty_po - data.qty_lock;
                                }

                                $(`#purchase-request-detail-list-${row_index}`).append(`
                                    <div class="row">
                                        ${item_html}
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-input type="text" name="main_price_before_discount[]" label="harga sebelum diskon" value="${purchase_request.price}" class="commas-form" id="main-item-price-before-discount-${row_index}-${purchase_request_index}" required />
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-input type="text" name="main_discount[]" label="diskon" value="" class="commas-form" id="main-item-discount-${row_index}-${purchase_request_index}" required />
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-input type="text" name="main_price[]" value="${purchase_request.price}" label="harga" class="commas-form" id="main-item-price-${row_index}-${purchase_request_index}" required readonly />
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-input type="text" name="main_jumlah_diminta[]" label="jumlah diminta" value="${data.jumlah_diapprove} / ${data.qty_po} / ${data.qty_lock} - ${data.unit.name}" helpers="Qty Dipesan / Qty Dibelikan / Qty di lock" id="main-item-quantityRequested-${row_index}-${purchase_request_index}" required readonly/>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-input type="text" name="main_quantity[]" value="${purchase_request.quantity}" label="jumlah" class="commas-form main-quantity" id="main-item-quantity-${row_index}-${purchase_request_index}" useCustomError required />
                                            </div>
                                        </div>
                                        <div class="col-md-1 d-flex align-self-center">
                                            <div class="form-group">
                                                <x-button color="info" icon="eye" fontawesome size="sm" id="purchase-requestDetail-btnmodal-${row_index}-${purchase_request_index}" />
                                                <x-modal title="Detail purchase request item" id="modalpurchase-requestDetail-modal-${row_index}-${purchase_request_index}" headerColor="info" modalSize="1000">
                                                    <x-slot name="modal_body">
                                                        <x-table id="">
                                                            <x-slot name="table_head">
                                                                <th>item</th>
                                                                <th>Jumlah Dipesan</th>
                                                                <th>Jumlah Di Lock</th>
                                                                <th>Attachment</th>
                                                            </x-slot>
                                                            <x-slot name="table_body">
                                                                <tr>
                                                                    <td>${item_value} - ${data.unit?.name}</td>
                                                                    <td>${data.jumlah_diapprove}</td>
                                                                    <td>${data.qty_lock}</td>
                                                                    <td>
                                                                        <x-button color="info" icon="file" fontawesome size="sm" link="{{ url('storage/') }}/${data.file}" target="blank" />
                                                                    </td>
                                                                </tr>
                                                            </x-slot>
                                                        </x-table>
                                                    </x-slot>
                                                </x-modal>
                                            </div>
                                        </div>
                                    </div>
                                `);

                                initCommasForm();
                                $(`#main-item-price-before-discount-${row_index}-${purchase_request_index}`).on('blur', function(e) {
                                    calculate_final_price(row_index, purchase_request_index);
                                    purchase_request.price_before_discount = thousandToFloat(this.value);
                                });

                                $(`#main-item-discount-${row_index}-${purchase_request_index}`).on('blur', function(e) {
                                    calculate_final_price(row_index, purchase_request_index);
                                    purchase_request.discount = thousandToFloat(this.value);
                                });

                                initSelect2SearchPaginationData(`main-item-select-${row_index}-${purchase_request_index}`, `{{ route('admin.select.item.type') }}/service`, {
                                    id: 'id',
                                    text: 'kode,nama'
                                })

                                $(`#main-item-select-${row_index}-${purchase_request_index}`).change(function(e) {
                                    e.preventDefault();

                                    if (this.value) {
                                        $.ajax({
                                            type: "get",
                                            url: `{{ route('admin.item.price-latest') }}/${this.value}`,
                                            success: function({
                                                data
                                            }) {
                                                $(`#main-item-price-${row_index}-${purchase_request_index}`).val(decimalFormatterCommasWithOuNumberWithCommas(data.harga_beli));
                                                $(`#main-item-price-${row_index}-${purchase_request_index}`).trigger('keyup');
                                                $(`#main-item-price-${row_index}-${purchase_request_index}`).trigger('blur');
                                            }
                                        });

                                        $.ajax({
                                            type: "get",
                                            url: `{{ route('admin.item.item-unit') }}/${this.value}`,
                                            success: function({
                                                data
                                            }) {
                                                purchase_request.item = data;
                                                $(`#main-item-unit-${row_index}-${purchase_request_index}`).val(data.unit.name);
                                            }
                                        });
                                    } else {
                                        $(`#main-item-price-${row_index}-${purchase_request_index}`).val(0);
                                        $(`#main-item-price-${row_index}-${purchase_request_index}`).trigger('keyup');
                                        $(`#main-item-price-${row_index}-${purchase_request_index}`).trigger('blur');
                                    }
                                });

                                $(`#main-item-price-${row_index}-${purchase_request_index}`).keyup(function(e) {
                                    e.preventDefault();
                                    purchase_request.price = thousandToFloat(this.value);
                                    purchase_request.original_price = thousandToFloat(this.value);
                                })

                                $(`#main-item-quantity-${row_index}-${purchase_request_index}`).data('testQty', data.jumlah_diapprove - data.qty_po - data.qty_lock);
                                $(`#main-item-quantity-${row_index}-${purchase_request_index}`).keyup(function(e) {
                                    e.preventDefault();

                                    let req_qty = $(this).data('testQty');
                                    purchase_request.quantity = thousandToFloat(this.value);

                                    if (purchase_request.quantity > req_qty) {
                                        $(this).addClass('is-invalid');
                                        $(`#error-message-for-main-item-quantity-${row_index}-${purchase_request_index}`).text('Jumlah tidak boleh lebih dari jumlah diminta!');
                                    } else {
                                        $(this).removeClass('is-invalid');
                                        $(`#error-message-for-main-item-quantity-${row_index}-${purchase_request_index}`).text(null);
                                    }
                                })

                                $(`#purchase-requestDetail-btnmodal-${row_index}-${purchase_request_index}`).click(function(e) {
                                    e.preventDefault();
                                    $(`#modalpurchase-requestDetail-modal-${row_index}-${purchase_request_index}`).modal('show');
                                });

                                $(`#main-item-quantityRequested-${row_index}-${purchase_request_index}`).trigger('keyup');
                                $(`#main-item-quantityRequested-${row_index}-${purchase_request_index}`).trigger('blur');
                                $(`#main-item-quantity-${row_index}-${purchase_request_index}`).trigger('keyup');
                                $(`#main-item-quantity-${row_index}-${purchase_request_index}`).trigger('blur');
                            }
                        });
                    };

                    handleAdd(row_index);
                };

                const removePurchaseRequest = (row_index) => {
                    $(`#purchase-request-card-${row_index}`).remove();
                    purchaseRequests[row_index] = null;
                };

                initSecondCard();
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
                                            <option value="transport">Transport</option>
                                            <option value="service">Service</option>
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

                                initSelect2SearchPaginationData(`additional-item-select-${row_index}`, `{{ route('admin.select.item') }}/${this.value}`, {
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
                    calculatePurchaseRequest();
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

                const calculatePurchaseRequest = () => {
                    let iteration = 1;
                    let currency_symbol = currency.simbol
                    total_main = 0;

                    $('#resume-purchase-request tbody').html('');
                    purchaseRequests.map((purchase_request, purchase_request_index) => {

                        if (purchase_request != null) {
                            let {
                                parent,
                                children
                            } = purchase_request;


                            children?.map((purchase_request_detail, purchase_request_detail_index) => {
                                if (purchase_request_detail.selected) {
                                    let {
                                        data,
                                        item,
                                        quantity,
                                        price_before_discount,
                                        discount,
                                        price,
                                        original_price,
                                    } = purchase_request_detail;

                                    if ($('#is_include_tax').is(':checked')) {
                                        price = original_price
                                        let tax_percentage = 0;
                                        TAX_LIST_VALUE.map((tax_value, tax_value_index) => {
                                            tax_percentage += tax_value.value * 100
                                        });

                                        price -= (original_price - (100 / (100 + tax_percentage) * original_price));
                                        purchaseRequests[purchase_request_index].children[purchase_request_detail_index].price = price;
                                    } else {
                                        price = original_price;
                                        purchaseRequests[purchase_request_index].children[purchase_request_detail_index].price = original_price;
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

                                        html_tax_name += `<p class="my-0">${name} - ${value *100}%</p>`;
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
                                    total_main += single_total;

                                    $('#resume-purchase-request tbody').append(html);
                                    iteration++;
                                }
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

                                html_tax_name += `<p class="my-0">${name} - ${value *100}%</p>`;
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
                    purchaseRequests[row_index].children.map((purchase_request, purchase_request_index) => {
                        if ($(`#main-item-quantity-${row_index}-${purchase_request_index}`).val() == 0) {
                            $(`#main-item-quantity-${row_index}-${purchase_request_index}`).val('')
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

                            main: purchaseRequests.map((purchase_request, purchase_request_detail) => {

                                if (purchase_request != null) {
                                    let {
                                        parent,
                                        children
                                    } = purchase_request;
                                    return {
                                        purchase_request_id: parent.id,
                                        purchase_request_detail_id: children.map((purchase_request_detail, purchase_request_detail_index) => {
                                            if (purchase_request_detail.selected) {
                                                let {
                                                    data,
                                                    item,
                                                    selected,
                                                    quantity,
                                                    price_before_discount,
                                                    discount,
                                                    price,
                                                } = purchase_request_detail;

                                                return {
                                                    purchase_request_detail_id: data.id,
                                                    item_id: item.id,
                                                    quantity: quantity,
                                                    price: price,
                                                    price_before_discount: price_before_discount,
                                                    discount: discount,
                                                    tax_id: TAX_LIST_ID
                                                };
                                            }
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
                });
            };

            init();
        });

        $('#is_spk').click(function(e) {
            if (this.checked) {
                $('#spk-form').removeClass('d-none');
                $('#pic').attr('required', true);
            } else {
                $('#spk-form').addClass('d-none');
                $('#pic').attr('required', false);
            }
        });
    </script>

    <script>
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#purchase-menu');
        sidebarActive('#purchase');
    </script>
@endsection
