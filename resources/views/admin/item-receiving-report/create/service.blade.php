@extends('layouts.admin.layout.index')

@php
    $main = 'item-receiving-report-service';
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
    @can('create item-receiving-report-service')
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
                                <x-select name="purchase_order_service_id" label="Purchase Order service" required id="purchaseOrderservice-select"></x-select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input name="vendor_id" label="vendor" required id="vendor-form" disabled />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input name="created_at" label="dateCreated" required id="dateCreated-form" disabled />
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
                </x-slot>
            </x-card-data-table>

            <x-card-data-table title="item" id="details-card">
                <x-slot name="table_content">
                    <div id="details-item"></div>
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

    @can('create item-receiving-report-service')
        <script>
            $(document).ready(function() {
                $('#details-card').hide();
                checkClosingPeriod($('#dateReceive-form'));
                const initialize = () => {

                    let purchaseOrderservice = [],
                        purchaseOrderserviceDetails = [];

                    const initSelectForm = () => {
                        const selectPurchaseOrder = () => {
                            let selected_item = [];

                            $(`select[name="#purchaseOrderservice-select"]`)
                                .toArray()
                                .map(function() {
                                    if ($(this).val() != null) {
                                        selected_item.push($(this).val());
                                    }
                                });

                            let target_value = $(`#purchaseOrderservice-select`).val();

                            var itemSelect = {
                                placeholder: "Pilih Data",
                                minimumInputLength: 0,
                                width: "100%",
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
                                    url: "{{ route('admin.purchase-order-service.select-for-receiving') }}",
                                    dataType: "json",
                                    delay: 250,
                                    type: "get",
                                    data: (params) => {
                                        let result = {};
                                        result["search"] = params.term;
                                        result["selected_item"] = selected_item;
                                        result["purchaseOrderservice-select"] = target_value;
                                        result["page_limit"] = 10;
                                        result["page"] = params.page;
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
                                                text: `${data.final_code} - ${data.nama}`,
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

                            $(`#purchaseOrderservice-select`).select2(itemSelect);
                            return;
                        };

                        selectPurchaseOrder();
                        handleOnchangePurchaseOrderForm();
                    };

                    $('#branch_id').change(function(e) {
                        $('#purchaseOrderservice-select').val(null).trigger('change');
                    });

                    const handleOnchangePurchaseOrderForm = () => {
                        $('#purchaseOrderservice-select').change(function(e) {
                            e.preventDefault();
                            if (this.value) {
                                $.ajax({
                                    type: "get",
                                    url: "{{ route('admin.purchase-order-service.detail-for-receiving') }}" + '/' + $(this).val(),
                                    success: function({
                                        data
                                    }) {
                                        let ItemReceivingReportDate = parseDate($('#dateReceive-form').val());
                                        let PurchaseOrderDate = parseDate(data.model?.date);

                                        if (ItemReceivingReportDate < PurchaseOrderDate) {
                                            alert('Tanggal terima barang tidak boleh kurang dari tanggal PO');
                                            $('#purchase_order_service_id').val(null).trigger('change');
                                            return;
                                        }

                                        purchaseOrderservice = data.model;
                                        purchaseOrderserviceDetails = data.details;

                                        $('#vendor-form').val(data.model?.vendor?.nama);
                                        $('#dateCreated-form').val(data.model?.created_at);

                                        displayItems();
                                    }
                                });
                            } else {
                                $('#vendor-form').val('');
                                $('#dateCreated-form').val('');

                                $('#details-card').hide();
                                $('#detils-item').html('');
                            }
                        });
                    };

                    const displayItems = () => {
                        $('#details-card').show();
                        $('#details-item').html('');

                        let data_index = 0;
                        purchaseOrderserviceDetails.map((parent_detail, parent_index) => {

                            parent_detail.purchase_order_service_detail_items.map((detail, index) => {
                                let available = detail.quantity - detail.quantity_received;

                                let html = `<div class="row" id="row-data-${data_index}">
                                        <input type="hidden" name="reference_id[]" value="${detail.id}" />
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <x-input type="text" id="item_id" name="item" label="item" value="${detail.item.nama} - ${detail.item.kode}" required readonly />
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <x-input type="text" id="jumlah-data-${data_index}" name="jumlah" label="jumlah" value="${formatRupiahWithDecimal(detail.quantity_received)} / ${formatRupiahWithDecimal(detail.quantity)} - ${detail.unit?.name}" required helpers="sudah diterima / dipesan" readonly />
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <x-input type="text" class="commas-form" id="jumlah-diterima-${data_index}" class="commas-form" name='jumlah_diterima[]' value="${formatRupiahWithDecimal(available)}" label="jumlah_diterima" helpers="${detail.unit?.name}" required />
                                            </div>
                                        </div>
                                        <div class="col-md-2 align-self-center">
                                            <div class="form-group">
                                                <button type="button" class="btn btn-danger" id="delete-row-${data_index}" data-index="${data_index}">Hapus</button>
                                            </div>
                                        </div>
                                    </div>`;

                                $('#details-item').append(html);
                                $(`#jumlah-diterima-${data_index}`).trigger('blur');

                                $('#delete-row-' + data_index).on('click', function(e) {
                                    $(`#row-data-${$(this).data('index')}`).remove();
                                });

                                handleSubmit(data_index)

                                $(`#jumlah-diterima-${data_index}`).blur(function(e) {
                                    let this_quantity = $(this).val();
                                    let quantity = detail.quantity;
                                    let quantity_received = detail.quantity_received;

                                    if (thousandToFloat($(this).val()) > quantity - quantity_received) {
                                        $(this).val(quantity - quantity_received);

                                        alert('Jumlah diterima tidak boleh melebihi jumlah yang belum diterima');
                                    }
                                });

                                data_index++;
                            });
                        });

                        initCommasForm();
                    };

                    initSelectForm();
                };

                initialize();

                const handleSubmit = (data_index = 0) => {
                    $(`#lpb_general_submit`).submit(function(e) {
                        if ($(`#jumlah-diterima-${data_index}`).val() == 0) {
                            e.preventDefault()
                            showAlert('', 'Jumlah tidak boleh 0');
                            $('#button_submit').html('')
                            $('#button_submit').html(`
                                <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
                                <x-button class="save-data" type="submit" color="primary" label="Save data" />
                            `)

                        } else if ($(`#jumlah-diterima-${data_index}`).val() == "NaN") {
                            e.preventDefault()
                            showAlert('', 'Jumlah tidak boleh string');
                            $('#button_submit').html('')
                            $('#button_submit').html(`
                                <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
                                <x-button class="save-data" type="submit" color="primary" label="Save data" />
                            `)
                        }
                    })
                }

                handleSubmit()
            });
        </script>
    @endcan

    @if (get_current_branch()->is_primary == 1)
        <script>
            initSelect2Search(`branch_id`, `{{ route('admin.select.branch') }}`, {
                id: "id",
                text: "name"
            });
        </script>
    @endif

    <script>
        sidebarMenuOpen('#purchase-menu')
        sidebarActive('#item-receiving-report');
    </script>
@endsection
