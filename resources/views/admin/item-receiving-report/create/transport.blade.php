@extends('layouts.admin.layout.index')

@php
    $main = 'item-receiving-report-transport';
    $title = 'berita acara serah terima';
@endphp

@section('title', Str::headline("Tambah $title") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($title) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Tambah ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can('create item-receiving-report-transport')
        <form action="{{ route("admin.$main.store") }}" method="post" enctype="multipart/form-data">
            @csrf
            <x-card-data-table title="{{ $title }}">
                <x-slot name="table_content">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-select name="branch_id" id="branch_id" label="branch" required>
                                    <option value="{{ get_current_branch()->id }}" selected>
                                        {{ get_current_branch()->name }}</option>
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input class="datepicker-input" name="date_receive" id="dateReceive-form" label="tanggal diterima" value="{{ \Carbon\Carbon::today()->format('d-m-Y') }}" required onchange="checkClosingPeriod($(this))" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="file" name="file" label="file" id="" accept=".jpg, .jpeg, .png, .pdf, .doc, .docx, .xls, .xlsx" helpers="jpg/jpeg/png/pdf/doc/docx/xls/xlsx | max. 5 MB" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-select name="purchase_transport_id" label="purchase-transport" id="purchase-transport-id" required>

                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" name="so" label="sales-order" id="sale-order-form-data" required readonly />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" name="vendor" label="vendor" id="vendor-form-data" required readonly />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" name="loss_tolerance" label="loss_tolerance" id="loss_tolerance-form-data" required class="comma-form" />
                            </div>
                        </div>
                    </div>
                </x-slot>
            </x-card-data-table>

            <x-card-data-table title="item" id="details-card">
                <x-slot name="table_content">
                    <div id="main-form-purchase-transport"></div>
                    <div class="row">
                        <div class="col-md-4">
                            <x-input type="text" name="lost_discount" label="potongan losses" id="lost_discount" class="commas-form text-end" />
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <x-select name="tax_option" id="tax_option" label="Opsi Pajak" required autofocus>
                                <option value="normal">Normal</option>
                                <option value="full">Full</option>
                                <option value="by_po">Dari PO</option>
                            </x-select>
                        </div>
                    </div>
                </x-slot>
            </x-card-data-table>

            <x-card-data-table>
                <x-slot name="table_content">
                    <div class="d-flex justify-content-end gap-3">
                        <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
                        <x-button class="save-data" type="submit" color="primary" label="Save data" />
                    </div>
                </x-slot>
            </x-card-data-table>
        </form>
    @endcan
@endsection

@section('js')
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>

    <script>
        $(document).ready(function() {

            checkClosingPeriod($('#dateReceive-form'));

            let data_purchase = [],
                data_vendor = [],
                data_purchase_transports = [],
                data_delivery_orders = [],
                purchase_transport_details = [],
                data_sale_order = [],
                data_checked = [],
                unit_global = '';

            let TOTAL_SEND = 0,
                TOTAL_SENDED = 0,
                TOTAL_RECEIVED = 0;

            const init = () => {
                initSelectPurchaseTransport();
            };

            const initSelectPurchaseTransport = () => {

                const initSelect2SearchPurchaseTranport = (target, route, min_char = 0) => {
                    let selected_item = [];

                    $(`select[id="#${target}"]`)
                        .toArray()
                        .map(function() {
                            if ($(this).val() != null) {
                                selected_item.push($(this).val());
                            }
                        });

                    let target_value = $(`#${target}`).val();

                    var itemSelect = {
                        placeholder: "Pilih Data",
                        minimumInputLength: min_char,
                        allowClear: true,
                        language: {
                            inputTooShort: () => {
                                return "Insert at least 3 characters";
                            },
                            noResults: () => {
                                return "Data can't be found";
                            },
                        },
                        ajax: {
                            url: route,
                            dataType: "json",
                            delay: 250,
                            type: "get",
                            data: (params) => {
                                let result = {};
                                result["search"] = params.term;
                                result["selected_item"] = selected_item;
                                result["page_limit"] = 10;
                                result["page"] = params.page;
                                result[target] = target_value;
                                result['branch_id'] = function() {
                                    return $('#branch_id').val();
                                }
                                return result;
                            },
                            processResults: (data, params) => {
                                params.page = params.page || 1;
                                let final_data = data.data.map((data, key) => {
                                    return {
                                        id: data.id,
                                        text: `${data.kode} - ${data.customer_name} - ${data?.vendor?.nama}`,
                                    };
                                });
                                return {
                                    results: final_data,
                                    pagination: {
                                        more: params.page * 10 < data.total,
                                    }
                                };
                            },
                            cache: true,
                        },
                    };

                    $(`#${target}`).select2(itemSelect);
                    return;
                };

                initSelect2SearchPurchaseTranport('purchase-transport-id', "{{ route('admin.purchase-order-transport-lpbs') }}")

                $('#purchase-transport-id').change(function(e) {
                    e.preventDefault();

                    $('#main-form-purchase-transport').html('');

                    if (this.value) {
                        getDataPurchaseDetail(this.value);
                        return;
                    }

                    $('#main-form-purchase-transport').hide();
                });

                $('#branch_id').change(function(e) {
                    e.preventDefault();
                    $('#purchase-transport-id').val(null).trigger('change');
                });
            };

            const getDataPurchaseDetail = (id) => {
                $.ajax({
                    url: `{{ route('admin.purchase-order-transport.detail-lpb') }}/${id}`,
                    success: function({
                        data
                    }) {
                        console.log(data);
                        let {
                            model,
                            delivery_orders
                        } = data;

                        let ItemReceivingReportDate = parseDate($('#dateReceive-form').val());
                        let PurchaseOrderDate = parseDate(data.model?.target_delivery);

                        if (ItemReceivingReportDate < PurchaseOrderDate) {
                            alert('Tanggal terima barang tidak boleh kurang dari tanggal PO');
                            $('#purchase_order_general_id').val(null).trigger('change');
                            return;
                        }

                        let {
                            vendor,
                            so_trading
                        } = model;

                        data_purchase = model;
                        data_vendor = vendor;
                        data_sale_order = so_trading;
                        data_delivery_orders = delivery_orders;
                        purchase_transport_details = data.model.purchase_transport_details

                        displayData();
                    }
                });
            };

            const displayData = () => {
                $('#main-form-purchase-transport').show();
                $('#main-form-purchase-transport').html('');

                const initilizeDisplayData = () => {
                    displayParent();
                    if (data_delivery_orders.length > 0) {
                        displayDeliverOrder();
                        calculateTotalSended()
                    }
                    if (!data_sale_order && data_delivery_orders.length == 0) {
                        displayPurchaseTransportDetails();

                    }
                };

                const displayParent = () => {
                    if (data_sale_order) {
                        $('#sale-order-form-data').val(`${data_sale_order?.nomor_so} - ${data_sale_order?.customer.nama}`);
                    } else {
                        $('#sale-order-form-data').val('-');
                    }
                    $('#vendor-form-data').val(data_vendor.nama);
                    if (!data_vendor.loss_tolerance) {
                        // $('.save-data').attr('disabled', true);
                        alert('Harap isi loss tolerance');
                        $('#loss_tolerance-form-data').val(formatRupiahWithDecimal(0));
                    } else {
                        // $('.save-data').attr('disabled', false);
                        $('#loss_tolerance-form-data').val(formatRupiahWithDecimal(data_vendor?.loss_tolerance));
                    }
                };

                const displayDeliverOrder = () => {
                    const initializeDisplayDeliveryOrderData = () => {
                        createtable()
                        displayListData();
                    };

                    const createtable = () => {
                        $('#main-form-purchase-transport').append(`
                                    <div class="row">
                                        <div class="col-md-4">
                                            <x-input-checkbox label="Check All" checked name="select_all" class="check-delivery" id="check-delivery-all" hideAsterix />
                                        </div>
                                    </div>
                                    <x-table theadColor="danger" id="delivery-order-table">
                                        <x-slot name="table_head">
                                            <th></th>
                                            <th>#</th>
                                            <th>{{ Str::headline('kode') }}</th>
                                            <th>{{ Str::headline('target pengiriman') }}</th>
                                            <th>{{ Str::headline('tanggal muat') }}</th>
                                            <th>{{ Str::headline('tanggal bongkar') }}</th>
                                            <th>{{ Str::headline('qty kirim') }}</th>
                                            <th>{{ Str::headline('realisasi qty kirim') }}</th>
                                            <th>{{ Str::headline('realisasi qty diterima') }}</th>
                                            <th>{{ Str::headline('losses') }}</th>
                                        </x-slot>
                                        <x-slot name="table_body">
                                        </x-slot>
                                        <x-slot name="table_foot">
                                        </x-slot>
                                    </x-table>
                                `);
                    };

                    const displayListData = () => {
                        TOTAL_SEND = 0;
                        TOTAL_SENDED = 0;
                        TOTAL_RECEIVED = 0;

                        data_delivery_orders.map((delivery_order, delivery_order_index) => {
                            let {
                                code,
                                external_number,
                                date,
                                target_delivery,
                                load_date,
                                unload_date,
                                load_quantity,
                                load_quantity_realization,
                                unload_quantity_realization,
                                unit
                            } = delivery_order;

                            unit_global = unit;

                            TOTAL_SEND += parseFloat(load_quantity);
                            TOTAL_SENDED += parseFloat(load_quantity_realization);
                            TOTAL_RECEIVED += parseFloat(unload_quantity_realization);

                            let losses = parseFloat(load_quantity_realization) - parseFloat(unload_quantity_realization);
                            let losses_percent = (losses / parseFloat(load_quantity_realization)) * 100;

                            $(`table#delivery-order-table tbody`).append(`
                                        <tr>
                                            <td>
                                                <x-input-checkbox label="-" checked name="delivery_order[]" class="check-delivery" id="delivery-order-checkbox-${delivery_order_index}" hideAsterix/>
                                                <input type="hidden" name="delivery_order_id[]" value="${delivery_order.id}">
                                            </td>
                                            <td>${delivery_order_index + 1 }</td>
                                            <td>${code} / ${external_number}</td>
                                            <td>${localDate(target_delivery)}</td>
                                            <td>${localDate(load_date)}</td>
                                            <td>${localDate(unload_date)}</td>
                                            <td>${decimalFormatter(load_quantity)} ${unit}</td>
                                            <td>${decimalFormatter(load_quantity_realization)} ${unit}</td>
                                            <td>${decimalFormatter(unload_quantity_realization)} ${unit}</td>
                                            <td>${decimalFormatter(losses)} - ${formatRupiahWithDecimal(losses_percent)}%</td>
                                        </tr>
                                    `);

                            initCommasForm();
                            $(`#delivery-order-checkbox-${delivery_order_index}`).click(function(e) {
                                calculateTotalSended();
                            });
                        });

                        $('#delivery-order-table tfoot').append(`
                                    <tr>
                                        <td colspan="6" class="text-end">Total</td>
                                        <td class="bg-success" id="total-send-quantity">${decimalFormatter(TOTAL_SEND)} ${unit_global}</td>
                                        <td class="bg-success" id="total-load-quantity">${decimalFormatter(TOTAL_SENDED)} ${unit_global}</td>
                                        <td class="bg-success" id="total-unload-quantity">${decimalFormatter(TOTAL_RECEIVED)} ${unit_global}</td>
                                        <td></td>
                                    </tr>
                                `);

                        $('#check-delivery-all').on('change', function() {
                            if ($(this).is(':checked')) {
                                $('.check-delivery').prop('checked', true);
                            } else {
                                $('.check-delivery').prop('checked', false);
                            }

                            calculateTotalSended();
                        });
                    };

                    initializeDisplayDeliveryOrderData();
                };

                const displayPurchaseTransportDetails = () => {
                    const initializeDisplayPurchaseTransportDetailData = () => {
                        createtable()
                        displayListData();
                    };

                    const createtable = () => {
                        $('#main-form-purchase-transport').append(`
                                    <x-table theadColor="danger" id="purchase-transport-detail-table">
                                        <x-slot name="table_head">
                                            <th>#</th>
                                            <th>{{ Str::headline('jumlah DO') }}</th>
                                            <th>{{ Str::headline('qty') }}</th>
                                            <th>{{ Str::headline('total') }}</th>
                                            <th>{{ Str::headline('jenis armada') }}</th>
                                            <th>{{ Str::headline('informasi armada') }}</th>
                                        </x-slot>
                                        <x-slot name="table_body">
                                        </x-slot>
                                        <x-slot name="table_foot">
                                        </x-slot>
                                    </x-table>
                                `);
                    };

                    const displayListData = () => {
                        TOTAL_SENDED = 0;
                        TOTAL_RECEIVED = 0;
                        TOTAL_ALL = 0;

                        purchase_transport_details.map((purchase_transport_detail, purchase_transport_detail_index) => {
                            let {
                                jumlah_do,
                                jumlah,
                                vehicle_type,
                                vehicle_info
                            } = purchase_transport_detail;

                            TOTAL_SENDED += parseFloat(jumlah);
                            TOTAL_RECEIVED += parseFloat(jumlah);
                            TOTAL_ALL += parseFloat(jumlah * jumlah_do);

                            $(`table#purchase-transport-detail-table tbody`).append(`
                                        <tr>
                                            <td>${purchase_transport_detail_index + 1 }</td>
                                            <td>${decimalFormatter(jumlah_do)}</td>
                                            <td>${decimalFormatter(jumlah)}</td>
                                            <td>${decimalFormatter(jumlah * jumlah_do)}</td>
                                            <td>${decimalFormatter(vehicle_type)}</td>
                                            <td>${decimalFormatter(vehicle_info)}</td>
                                        </tr>
                                    `);
                        });

                        $('#purchase-transport-detail-table tfoot').append(`
                                    <tr>
                                        <td colspan="2" class="text-end">Total</td>
                                        <td class="bg-success" id="total-load-quantity">${decimalFormatter(TOTAL_SENDED)} ${unit_global}</td>
                                        <td class="bg-success" id="total-load-quantity">${decimalFormatter(TOTAL_ALL)} ${unit_global}</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                `);
                    };

                    initializeDisplayPurchaseTransportDetailData();
                };

                initilizeDisplayData();
            };

            const calculateTotalSended = () => {
                TOTAL_SEND = 0;
                TOTAL_SENDED = 0;
                TOTAL_RECEIVED = 0;

                data_delivery_orders.map((delivery_order, delivery_order_index) => {

                    console.log('==============================');
                    console.log($(`#delivery-order-checkbox-${delivery_order_index}`).is(':checked'));
                    console.log('==============================');

                    if ($(`#delivery-order-checkbox-${delivery_order_index}`).is(':checked')) {
                        let {
                            load_quantity,
                            load_quantity_realization,
                            unload_quantity_realization
                        } = delivery_order;

                        TOTAL_SEND += parseFloat(load_quantity);
                        TOTAL_SENDED += parseFloat(load_quantity_realization);
                        TOTAL_RECEIVED += parseFloat(unload_quantity_realization);
                    }
                });

                $('#total-send-quantity').html(decimalFormatter(TOTAL_SEND));
                $('#total-load-quantity').html(decimalFormatter(TOTAL_SENDED));
                $('#total-unload-quantity').html(decimalFormatter(TOTAL_RECEIVED));
            };

            init();
        });
    </script>
    <script>
        sidebarMenuOpen('#purchase-menu')
        sidebarActive('#item-receiving-report');
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
