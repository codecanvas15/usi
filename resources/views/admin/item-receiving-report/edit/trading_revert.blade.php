@extends('layouts.admin.layout.index')

@php
    $main = 'item-receiving-report-trading';
    $title = 'Laporan Penerimaan barang trading';
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
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($title) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('edit ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can('create item-receiving-report-trading')
        <form action="{{ route("admin.$main.update", $model) }}" method="post" id="form-lpb" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <x-card-data-table title="edit {{ $title }}">
                <x-slot name="table_content">
                    @include('components.validate-error')

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-select name="branch_id" id="branch_id" label="branch" required aria-disabled="">
                                    <option value="{{ $model->branch?->id }}" selected>
                                        {{ $model->branch?->name }}</option>
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-select name="purchase_order_trading_id" id="purchase_order_trading_id" label="purchase_order_trading" required>
                                    <option value="{{ $model->reference->id }}">{{ $model->reference->nomor_po }} - {{ $model->reference->customer->nama }}</option>
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" name="customer" id="customer_name" label="customer" required readonly value="{{ $model->reference->customer->nama ?? '' }}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" name="tanggal_dibuat" id="tanggal_dibuat" label="Purchase dibuat pada" required readonly />
                            </div>
                        </div>
                        <div class="col-md-12"></div>
                        @php
                            $data_sh_number = $model->reference->sh_number;
                            if ($model->reference->sale_order) {
                                $data_sh_number = $model->reference->sale_order->sh_number;
                            }
                        @endphp
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">SH No.</label>
                                <p id="sh_number">{{ $data_sh_number->kode }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Supply Point</label>
                                <p id="supply_point">{{ $data_sh_number->sh_number_details[0]->alamat ?? '' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Drop Point</label>
                                <p id="drop_point">{{ $data_sh_number->sh_number_details[1]->alamat ?? '' }}</p>
                            </div>
                        </div>
                        <div class="col-md-12"></div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" name="loading_order" id="loading_order" label="loading_order" required value="{{ $model->item_receiving_report_po_trading->loading_order ?? '' }}" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <x-input class="datepicker-input" name="date_receive" id="date_receive" label="tanggal diterima" value="{{ \Carbon\Carbon::today()->format('d-m-Y') }}" required onchange="checkClosingPeriod($(this))" />
                            </div>
                        </div>
                    </div>

                    <div class="row border-bottom border-primary">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" name="sale_confirmation" id="sale-confirmation" label="SCO Supplier" helpers="sale confirmation dari purchase order" required value="" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="file" name="file" label="file" id="" accept=".jpg, .jpeg, .png, .pdf, .doc, .docx, .xls, .xlsx" helpers="jpg/jpeg/png/pdf/doc/docx/xls/xlsx | max. 5 MB" />
                            </div>
                        </div>
                        <div class="col-md-2 align-items-center d-flex">
                            @if ($model->file)
                                <x-button color="info" link="{{ url('storage/' . $model->file) }}" size="sm" label="Show File" fontawesome target="_blank" />
                            @endif
                        </div>
                    </div>

                    <div class="row mt-10">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-select name="ware_house_id" label="warehouse" id="warehouse-select" required>
                                    @if ($model->ware_house)
                                        <option value="{{ $model->ware_house->id }}">{{ $model->ware_house->nama }}</option>
                                    @endif
                                </x-select>
                            </div>
                        </div>
                    </div>

                </x-slot>
            </x-card-data-table>

            <x-card-data-table title="item" id="details-card">
                <x-slot name="table_content">
                    <div id="details-item">

                    </div>
                </x-slot>
            </x-card-data-table>

            <x-card-data-table title="additional item" id="details-card">
                <x-slot name="table_content">
                    <div id="additionals-item">

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

    @can('create item-receiving-report-trading')
        <script>
            $(document).ready(function() {
                let purchase_order_data = [],
                    sale_confimation = null,
                    unit_global = '';

                checkClosingPeriod($('#date_receive'));


                const init = () => {
                    initForm();
                    handleSubmit();

                    $('#purchase_order_trading_id').trigger('change');
                };

                const initForm = () => {
                    const initSelect2SearchPoTrading = () => {
                        let selected_item = [];

                        $(`select[name="#purchase_order_trading_id"]`)
                            .toArray()
                            .map(function() {
                                if ($(this).val() != null) {
                                    selected_item.push($(this).val());
                                }
                            });

                        let target_value = $(`#purchase_order_trading_id`).val();

                        var itemSelect = {
                            placeholder: "Pilih Data",
                            minimumInputLength: 0,
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
                                url: "{{ route('admin.select.purchase-order-lpbs') }}",
                                dataType: "json",
                                delay: 250,
                                type: "get",
                                data: (params) => {
                                    let result = {};
                                    result["search"] = params.term;
                                    result["selected_item"] = selected_item;
                                    result["page_limit"] = 10;
                                    result["page"] = params.page;
                                    result[purchase_order_trading_id] = target_value;
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
                                            text: `${data.nomor_po} - ${data.customer.nama}`,
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

                        $(`#purchase_order_trading_id`).select2(itemSelect);
                        return;
                    };
                    initSelect2SearchPoTrading();

                    initSelect2SearchPaginationData(`warehouse-select`, `{{ route('admin.select.ware-house.type') }}/trading`, {
                        id: 'id',
                        text: 'nama'
                    })
                    handleFormPurchaseTrading();
                };

                $('#branch_id').change(function(e) {
                    $('#purchase_order_trading_id').val(null).trigger('change');
                });

                const handleFormPurchaseTrading = () => {
                    $('#purchase_order_trading_id').change(function(e) {
                        e.preventDefault();

                        if (this.value) {
                            $.ajax({
                                type: "get",
                                url: `{{ route('admin.purchase-order.detail') }}/${$(this).val()}?item_receiving_report_id={{ $model->id }}`,
                                success: function({
                                    data
                                }) {
                                    let ItemReceivingReportDate = parseDate($('#date_receive').val());
                                    let PurchaseOrderDate = parseDate(data?.tanggal);

                                    if (ItemReceivingReportDate < PurchaseOrderDate) {
                                        alert('Tanggal terima barang tidak boleh kurang dari tanggal PO');
                                        $('#purchase_order_trading_id').val(null).trigger('change');
                                        return;
                                    }

                                    unit_global = data.unit;

                                    purchase_order_data = data;
                                    sale_confimation = data.sale_confirmation;
                                    $('#customer_name').val(data.customer.nama);
                                    $('#tanggal_dibuat').val(data.created_at);
                                    $('#details-item').html('');
                                    $('#additionals-item').html('');

                                    displayItem();
                                    displayAdditional();

                                    var data_sh_number = data.sh_number;
                                    if (data.sale_order) {
                                        data_sh_number = data.sale_order.sh_number;
                                    }
                                    $('#sh_number').text(data_sh_number.kode);
                                    $('#supply_point').text(data_sh_number.sh_number_details[0].alamat);
                                    $('#drop_point').text(data_sh_number.sh_number_details[1].alamat);
                                }
                            });
                        } else {
                            purchase_order_data = [];
                            $('#customer_name').val('');
                            $('#tanggal_dibuat').val('');

                            $('#details-item').html('');
                            $('#additionals-item').html('');
                        }
                    });
                };

                const displayItem = () => {
                    let jumlah = purchase_order_data.po_trading_detail.jumlah;
                    let jumlah_diterima = purchase_order_data.po_trading_detail.jumlah_lpbs;
                    let type = purchase_order_data.po_trading_detail.type;
                    let sisa = (type == 'Kilo Liter' ? jumlah * 1000 : jumlah) - purchase_order_data.jumlah_lpbs;

                    let html = `
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input type="text" name="item" id="item" label="item" value="${purchase_order_data.po_trading_detail.item.nama} - ${purchase_order_data.po_trading_detail.item.kode}" required readonly />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input type="text" name="jumlah" id="jumlah" label="quantity" value="${formatRupiahWithDecimal(jumlah)} - ${type}" required readonly />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input type="text" name="jumlah_tersedia" class="commas-form" value="${formatRupiahWithDecimal(sisa)}" label="jumlah tersedia" readonly/>
                                        <small class="text-primary">${unit_global}</small>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input type="text" name="liter_15" class="commas-form" value="${formatRupiahWithDecimal(sisa)}" label="liter 15" />
                                        <small class="text-primary">${unit_global}</small>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input type="text" name="liter_obs" class="commas-form" label="liter obs" required />
                                        <small class="text-primary">${unit_global}</small>
                                    </div>
                                </div>
                            </div>
                        `;

                    $('#details-item').html(html);
                    initCommasForm();
                };

                const displayAdditional = () => {
                    let additional_items = purchase_order_data.po_trading_details_additional;
                    let html = '';
                    $.each(additional_items, function(key, additional) {
                        html += `<div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="hidden" name="additional_item_id[]" value="${additional.id}"/>
                                        <x-input type="text" name="additional_item[]" id="item" label="item" value="${additional.item.nama} - ${additional.item.kode}" required readonly />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input type="text" id="additional_jumlah_${key}" name="additional_jumlah[]" id="jumlah" label="quantity" value="${formatRupiahWithDecimal(additional.jumlah)} - ${additional.item.unit.name}" required readonly />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input type="text" id="additional_jumlah_tersedia_${key}" name="additional_jumlah_tersedia[]" class="commas-form" value="${formatRupiahWithDecimal(additional.outstanding)}" label="jumlah tersedia" readonly/>
                                        <small class="text-primary">${additional.item.unit.name}</small>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <x-input type="text" id="additional_receive_qty_${key}" name="additional_receive_qty[]" class="commas-form" value="${formatRupiahWithDecimal(additional.outstanding)}" label="jumlah diterima" />
                                        <small class="text-primary">${additional.item.unit.name}</small>
                                    </div>
                                </div>
                            </div>`;
                    });

                    $('#additionals-item').html(html);

                    initCommasForm();

                    $.each(additional_items, function(key, additional) {
                        $(`#additional_receive_qty_${key}`).on('keyup', function() {
                            let receive_qty = $(this).val();
                            let jumlah_tersedia = $(`#additional_jumlah_tersedia_${key}`).val();

                            if (formatThousandToFloat(receive_qty) > formatThousandToFloat(jumlah_tersedia)) {
                                alert('Jumlah diterima tidak boleh melebihi jumlah tersedia');
                                $(this).val(null);
                                return;
                            }
                        });
                    });

                };

                const handleSubmit = () => {
                    $('form#form-lpb').submit(function(e) {
                        e.preventDefault();

                        if (sale_confimation != $('#sale-confirmation').val()) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Nomor sale confirmation tidak sesuai!',
                            });

                            $(this).find('input[type=submit]').prop('disabled', false);
                            $(this).find('button[type=submit]').prop('disabled', false);

                            return;
                        } else {
                            $.ajax({
                                type: "post",
                                url: `${base_url}/rate-limiter/ajax`,
                                data: {
                                    _token: token,
                                    key: "create: " + "{{ $main }}, " + $('#type_data').val(),
                                    attempts: 2, // default is 2 attempts
                                    decay_seconds: 3,
                                },
                                success: function(response) {
                                    if (response.is_too_many_requests == true) {
                                        let waitingTime = parseInt(response.available_at_time);

                                        $('.errorRl').show();
                                        $('.errorRlMessage').text('Terlalu banyak permintaan menyimpan data, harap tunggu ' + waitingTime + " detik lagi");

                                        let showError = setInterval(() => {
                                            waitingTime--;

                                            if (waitingTime > 0 && waitingTime <= 60) {
                                                $('.errorRlMessage').text('Terlalu banyak permintaan menyimpan data, harap tunggu ' + waitingTime + " detik lagi");
                                            }

                                            if (waitingTime == 0) {
                                                $('.errorRl').hide();
                                                $('.save-data').prop('disabled', false);
                                                clearInterval(showError);
                                            }
                                        }, 1000);
                                    } else {
                                        $('#form-lpb').unbind('submit').submit();
                                    }
                                }
                            });
                        }
                    });
                };

                init();
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
        sidebarMenuOpen('#purchase-menu');
        sidebarActive('#item-receiving-report');
    </script>
@endsection
