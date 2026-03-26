@extends('layouts.admin.layout.index')

@php
    $main = 'delivery-order';
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
    <form action="{{ !$model ? route("admin.$main.store") : route("admin.$main.update", $model) }}" method="post" id="form-delivery">
        @csrf

        <x-card-data-table title="{{ 'Pilih Sales Order' }}">
            <x-slot name="header_content">

            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')
                <section id="wrapper-option-type-input">
                    <div class="row">
                        <div class="col-md-4">
                            <x-select id="option-type-input" name="option_type_input" label="Pilih delivery order" onchange="handleChangeTypeInput(event)">
                                <option value="">Pilih Item</option>
                                <option value="potp" {{ !empty($model?->purchase_transport_id) ? 'selected' : '' }}>PO Transport</option>
                                <option value="so" {{ empty($model?->purchase_transport_id) && !empty($model?->so_trading_id) ? 'selected' : '' }}>Sale Order</option>
                            </x-select>
                        </div>
                    </div>
                </section>

                <section id="wrapper-input-form-from-so" style="display: none">
                    <div class="row">
                        <div class="col-md-4">
                            <x-select name="so_trading_id" id="so_trading_id" label="kode sale order" value="{{ $model->so_number_id ?? '' }}">

                            </x-select>
                        </div>
                        <div class="col-md-4">
                            <x-input type="text" id="jumlah-so" name="Jumlah Sale Order" required disabled />
                            <small class="text-primary unit-info"></small>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" name="sales_order" label="sisa sales order" id="sale-order-values" readonly required helpers="" />
                                <small class="text-primary unit-info"></small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <x-input type="text" id="sh-number-kode" name="sh_number_id_" label="SH No." readonly />
                            <input type="hidden" name="sh_number_id" id="sh_number_id_data" />
                        </div>
                        <div class="col-md-4">
                            <x-input type="text" id="supply_point" name="supply_point" label="supply_point" value="" required disabled />
                        </div>
                        <div class="col-md-4">
                            <x-input type="text" id="drop_point" name="drop_point" label="drop_point / ship to" value="" required disabled />
                        </div>
                    </div>
                </section>

                <section id="wrapper-input-form-from-potp" style="display: none">
                    <div class="row">
                        <div class="col-md-4">
                            <x-select name="potp_trading_id" id="potp_trading_id" label="kode potp" :value="$model->purchase_transport_id ?? ''" required>

                            </x-select>
                        </div>
                        <div class="col-md-4">
                            <x-text-area id="sale_order_text_potp" name="so trading" disabled />
                            <input type="hidden" id="sale_order_id_potp" name="so_trading_potp_id" required />
                        </div>
                        <div class="col-md-4">
                            <x-input type="text" id="jumlah-so-potp" name="Jumlah POTOP" required disabled />
                            <small class="text-primary unit-info"></small>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" name="sales_order" label="sisa sales order" id="sale-order-values-potp" readonly required helpers="" />
                                <small class="text-primary unit-info"></small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <x-input type="text" id="sh-number-kode-potp" label="SH No." readonly />
                            <input type="hidden" name="sh_number_id_potp" id="sh_number_id_data_potp" />
                        </div>
                        <div class="col-md-4">
                            <x-input type="text" id="supply_point-potp" name="supply_point_potp" label="supply_point" value="" required disabled />
                        </div>
                        <div class="col-md-4">
                            <x-input type="text" id="drop_point-potp" name="drop_point_potp" label="drop_point / ship to" value="" required disabled />
                        </div>
                    </div>
                </section>
            </x-slot>

        </x-card-data-table>

        <x-card-data-table title="Generate delivery order" id="generate-card">

            <x-slot name="table_content">
                <div id="main-delivery-order">

                </div>

                <div class="mt-30">
                    <x-table id="table-resume">
                        @slot('table_head')
                            <th>Kapasitas</th>
                            <th>Jumlah</th>
                            <th>Total</th>
                        @endslot

                        @slot('table_body')
                        @endslot

                        @slot('table_foot')
                            <tr>
                                <td colspan="2" class="text-end">Total</td>
                                <td id="total-data-resume" class="bg-success"></td>
                            </tr>
                        @endslot
                    </x-table>
                </div>

            </x-slot>

            @slot('footer')
                <div class="row justify-content-end">
                    <div class="col-md-4">
                        <h5 class="text-danger">Note: Semakin banyak data maka akan semakin lama untuk generate data delivery order. pastikan koneksi internet stabil, cepat dan baterai mumpuni.</h5>
                    </div>
                </div>
                <div id="btn-submit" class="mt-10">
                    <div class="d-flex justify-content-end gap-3">
                        <x-button type="submit" color="primary" icon="folder-plus" fontawesome label="Generate Delivery Order" disabled />
                    </div>
                </div>
            @endslot

        </x-card-data-table>

    </form>

@endsection

@section('js')
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script>
        let JUMLAH_SO = 0,
            JUMLAH_DIKIRIM = 0,
            JUMLAH_SISA = 0,
            SISA = 0,
            KAPASITAS_LIST = [],
            JUMLAH_LIST = [],
            index_generated_data = 0,
            unit = '';

        let date = null;

        $('#generate-card').hide();

        $(document).ready(function() {
            const init = () => {
                firstCardDisplay();
            };

            init();
        });

        function firstCardDisplay() {
            const initSelect2SearchSO = () => {
                let selected_item = [];
                $(`select[name="#so_trading_id"]`)
                    .toArray()
                    .map(function() {
                        if ($(this).val() != null) {
                            selected_item.push($(this).val());
                        }
                    });
                let target_value = $(`#so_trading_id`).val();
                var itemSelect = {
                    placeholder: "Pilih Data",
                    allowClear: true,
                    language: {
                        noResults: () => {
                            return "Data can't be found";
                        },
                    },
                    ajax: {
                        url: "{{ route('admin.select.sos-for-do') }}",
                        dataType: "json",
                        delay: 250,
                        type: "get",
                        data: ({
                            term
                        }) => {
                            let result = {};
                            result["search"] = term;
                            result["selected_item"] = selected_item;
                            result["so_trading_id"] = target_value;
                            return result;
                        },
                        processResults: ({
                            data
                        }) => {
                            let final_data = data.map((data, key) => {
                                return {
                                    id: data.id,
                                    text: data.nomor_so + ' - ' + data.customer.nama,
                                };
                            });
                            return {
                                results: final_data,
                            };
                        },
                        cache: true,
                    },
                };
                $(`#so_trading_id`).select2(itemSelect);
                return;
            };

            const initSelect2SearchPOTP = () => {
                let selected_item = [];
                $(`select[name="#potp_trading_id"]`)
                    .toArray()
                    .map(function() {
                        if ($(this).val() != null) {
                            selected_item.push($(this).val());
                        }
                    });
                let target_value = $(`#potp_trading_id`).val();
                var itemSelect = {
                    placeholder: "Pilih Data",
                    allowClear: true,
                    language: {
                        noResults: () => {
                            return "Data can't be found";
                        },
                    },
                    ajax: {
                        url: "{{ route('admin.select.potp.for-do') }}",
                        dataType: "json",
                        delay: 250,
                        type: "get",
                        data: ({
                            term
                        }) => {
                            let result = {};
                            result["search"] = term;
                            result["selected_item"] = selected_item;
                            result["potp_trading_id"] = target_value;

                            return result;
                        },
                        processResults: (data) => {
                            var results = data.data.map((data, key) => {
                                return {
                                    id: data.id,
                                    text: data.kode + ' - ' + data.customer_name,
                                };
                            });

                            return {
                                results: results,
                                pagination: {
                                    more: data.current_page < data.last_page,
                                },
                            };
                        },
                        cache: true,
                    },
                };
                $(`#potp_trading_id`).select2(itemSelect);
                return;
            }

            $('#so_trading_id').off('change').change(function(e) {
                e.preventDefault();

                if (this.value) {

                    // get jumlah
                    $.ajax({
                        type: "get",
                        url: `{{ route('admin.select.so-jumlah') }}/${this.value}`,
                        success: function({
                            data
                        }) {

                            $('#jumlah-so').val(decimalFormatter(data.jumlah_int));

                            date = data.tanggal;

                            JUMLAH_SO = data.jumlah_int;
                            JUMLAH_DIKIRIM = data.jumlah_dikirim;
                            JUMLAH_SISA = JUMLAH_SO - JUMLAH_DIKIRIM;

                            SISA = 0;
                            KAPASITAS_LIST = [];
                            JUMLAH_LIST = [];
                            index_generated_data = 0;

                            $('#sale-order-values').val(numberWithCommas(JUMLAH_SISA));
                            $('#so_trading_id_data').val(data.id);
                            $('.unit-info').text(data.unit);
                            unit = data.unit;
                            secondCardDisplay();
                        }
                    });

                    // get sh number
                    $.ajax({
                        type: "get",
                        url: "{{ route('admin.select.sh-numbers-for-so') }}/" + $('#so_trading_id').val(),
                        success: function({
                            data
                        }) {

                            $('#sh_number_id_data').val(data.id);
                            $('#sh-number-kode').val(data.kode);

                            data.sh_number_details.map((detail, index) => {
                                if (detail.type == 'Drop Point') {
                                    $('#drop_point').val(detail.alamat);
                                }
                                if (detail.type == 'Supply Point') {
                                    $('#supply_point').val(detail.alamat);
                                }
                            })
                        }
                    });

                } else {
                    index_generated_data = 0;
                    $('#jumlah-so').val('');
                    $('#sale-order-values').val('');
                    $('#sh-number').val('');
                    $('#sh-number-kode').val('');
                    $('#drop_point').val('');
                    $('#supply_point').val('');

                    $('#generate-card').hide();
                    $('#main-delivery-order').html('');
                    $('table#table-resume tbody').html('');
                }
            });

            $('#potp_trading_id').off('change').change(async function(e) {
                e.preventDefault();

                if (this.value) {
                    let SO_ID;
                    // get jumlah
                    await $.ajax({
                        type: "get",
                        url: `{{ route('admin.select.potp.get-so', ':id') }}`.replace(':id', this.value),
                        success: function({
                            data
                        }) {
                            SO_ID = data.id;
                            $('.unit-info').text(data.unit);
                            unit = data.unit;

                            $('#jumlah-so-potp').val(decimalFormatter(data.potp_quota));

                            date = data.tanggal;

                            JUMLAH_SO = data.jumlah_int;
                            JUMLAH_DIKIRIM = data.jumlah_dikirim;
                            JUMLAH_SISA = data.potp_quota;

                            SISA = 0;
                            KAPASITAS_LIST = [];
                            JUMLAH_LIST = [];
                            index_generated_data = 0;

                            $('#sale-order-values-potp').val(numberWithCommas(JUMLAH_SISA));
                            $('#sale_order_id_potp').val(data.id);
                            $('#sale_order_text_potp').text(`${data.kode} - ${data.customer.nama}`);

                            secondCardDisplay(data.potp_data);
                        }
                    });

                    // get sh number
                    await $.ajax({
                        type: "get",
                        url: "{{ route('admin.select.sh-numbers-for-so') }}/" + SO_ID,
                        success: function({
                            data
                        }) {

                            $('#sh_number_id_data_potp').val(data.id);
                            $('#sh-number-kode-potp').val(data.kode);

                            data.sh_number_details.map((detail, index) => {
                                if (detail.type == 'Drop Point') {
                                    $('#drop_point-potp').val(detail.alamat);
                                }
                                if (detail.type == 'Supply Point') {
                                    $('#supply_point-potp').val(detail.alamat);
                                }
                            })
                        }
                    });

                } else {
                    index_generated_data = 0;
                    $('#jumlah-so-potp').val('');
                    $('#sale-order-values-potp').val('');
                    $('#sh-number-potp').val('');
                    $('#sh-number-kode-potp').val('');
                    $('#drop_point-potp').val('');
                    $('#supply_point-potp').val('');

                    $('#generate-card').hide();
                    $('#main-delivery-order').html('');
                }
            });

            firstCardDisplay.initSelect2SearchSO = initSelect2SearchSO;
            firstCardDisplay.initSelect2SearchPOTP = initSelect2SearchPOTP;
        };

        function handleChangeTypeInput(e) {
            e.preventDefault();

            $('#generate-card').hide();
            $('#main-delivery-order').html('');

            if (e.target.value == 'so') {
                $('#wrapper-input-form-from-potp').hide();
                $('#wrapper-input-form-from-so').fadeIn();

                $('#so_trading_id').val(null).trigger('change');

                firstCardDisplay();
                firstCardDisplay.initSelect2SearchSO();

                $('#so_trading_id').prop('required', true);
                $('#potp_trading_id').prop('required', false);
            } else if (e.target.value == 'potp') {
                $('#wrapper-input-form-from-so').hide();
                $('#wrapper-input-form-from-potp').fadeIn();

                $('#potp_trading_id').val(null).trigger('change');

                firstCardDisplay();
                firstCardDisplay.initSelect2SearchPOTP();

                $('#so_trading_id').prop('required', false);
                $('#potp_trading_id').prop('required', true);
            } else {
                $('#wrapper-input-form-from-so').fadeOut();
                $('#wrapper-input-form-from-potp').fadeOut();
            }
        }

        const CalculateData = () => {
            SISA = JUMLAH_SISA;
            let total = KAPASITAS_LIST.map((data, index) => {
                let value = data * JUMLAH_LIST[index];

                $(`#kapasitas-data-single-${index}`).html(numberWithCommas(data) + " " + unit);
                $(`#jumlah-data-single-${index}`).html(numberWithCommas(JUMLAH_LIST[index]) + " Kendaraan");
                $(`#total-data-single-${index}`).html(numberWithCommas(value) + " " + unit);

                return value;
            });

            total = total.reduce((a, b) => {
                return a + b;
            }, 0);

            if (total > SISA) {
                alert("Jumlah Kapasitas dan Jumlah tidak boleh melebihi jumlah SO yang tersisa");
                $('button[type="submit"]').prop('disabled', true);
            } else {
                $('button[type="submit"]').prop('disabled', false);
            }

            SISA -= total;
            $('#sisa-form-value').val(numberWithCommas(SISA));
            $('#total-data-resume').html(numberWithCommas(total) + " " + unit);

            return;
        };

        function secondCardDisplay(data = null) {
            $('#generate-card').show();
            $('#main-delivery-order').html('');

            if (data) {
                if (data && data?.length > 0) {
                    data.map((detail, index) => {
                        addDeliveryOrder(index, detail)
                    })
                }
            } else {
                addDeliveryOrder(index_generated_data)
            }
        };

        const addDeliveryOrder = (index_data, data = null) => {
            let form_extra = '';
            KAPASITAS_LIST[index_data] = data?.jumlah ?? 0;
            JUMLAH_LIST[index_data] = data?.jumlah_do ?? 0;

            const initDeliveryOrderForm = (data = null) => {
                addFormSo();
                FormEventListerners();
                addTableContent();

                CalculateData();
            };

            const addFormSo = () => {
                if (index_data == 0) {
                    form_extra = `
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <x-input type="text" name="sisa" id="sisa-form-value" class="commas-form" value="${numberWithCommas(JUMLAH_SISA)}" helpers="${unit}" required disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-2 row align-self-center">
                                        <div >
                                            <x-button block color="info" icon="plus" fontawesome size="sm" id="add-delivery-order" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                } else {
                    form_extra = `
                            <div class="col-md-3 row align-items-center">
                                <div >
                                    <x-button block color="danger" icon="trash" fontawesome size="sm" id="delete-delivery-order-${index_data}" />
                                </div>
                            </div>
                        `;
                }

                let delivery_order_element = `
                        <div class="row mt-20" id="delivery-order-generate-${index_data}">
                            <div class="col-md-3">
                                <x-input class="datepicker-input" id="target_delivery_${index_data}" name="target_delivery[]" label="target_delivery" id="target-delivery-input-${index_data}" value="${data?.target_delivery??''}" onchange="checkClosingPeriod($(this))" required />
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" name="kapasitas[]" label="kapasitas kendaraan" id="kapasitas-${index_data}" class="commas-form" required helpers="Kapasitas kendaraan (${unit})" value="${formatRupiahWithDecimal(data?.jumlah)}"/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" name="jumlah[]" label="jumlah delivery order" id="jumlah-${index_data}" class="commas-form" helpers="Jumlah Kendaraan" required  value="${formatRupiahWithDecimal(data?.jumlah_do)}"/>
                                </div>
                            </div>
                            ${form_extra}
                        </div>`;

                $('#main-delivery-order').append(delivery_order_element);

                initDatePicker();
                checkClosingPeriod($(`#target_delivery_${index_data}`))

                $(`#target-delivery-input-${index_data}`).change(function(e) {
                    if (parseDate(date) > parseDate(this.value)) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Tanggal do tidak boleh kurang dari tanggal so',
                        });

                        this.value = null;
                        return;
                    }
                });

                index_generated_data += 1;
                if (index_data == 0) {
                    $('#add-delivery-order').click(function(e) {
                        e.preventDefault();
                        addDeliveryOrder(index_generated_data);
                    });
                } else {
                    $(`#delete-delivery-order-${index_data}`).click(function(e) {
                        e.preventDefault();
                        KAPASITAS_LIST[index_data] = 0;
                        JUMLAH_LIST[index_data] = 0;

                        $(`#delivery-order-generate-${index_data}`).remove();
                        $(`#data-resume-single-${index_data}`).remove();
                        CalculateData();
                    });
                }

                initCommasForm();
            };

            const addTableContent = () => {
                $('table#table-resume tbody').append(`
                        <tr id="data-resume-single-${index_data}">
                            <td id="kapasitas-data-single-${index_data}"></td>
                            <td id="jumlah-data-single-${index_data}"></td>
                            <td id="total-data-single-${index_data}"></td>
                        </tr>
                    `);
            };

            const FormEventListerners = () => {
                $(`#kapasitas-${index_data}`).keyup(function(e) {
                    KAPASITAS_LIST[index_data] = thousandToFloat($(this).val());
                    CalculateData();
                });

                $(`#jumlah-${index_data}`).keyup(function(e) {
                    JUMLAH_LIST[index_data] = thousandToFloat($(this).val());
                    CalculateData();
                });
            };

            if (data) {
                initDeliveryOrderForm(data);
            } else {
                initDeliveryOrderForm();
            }
        };

        $('#form-delivery').submit(function(e) {
            if (thousandToFloat($('#sisa-form-value').val()) < 0) {
                e.preventDefault();

                alert("Jumlah Kapasitas dan Jumlah tidak boleh melebihi SO yang tersisa");
                setTimeout(() => {
                    $('button[type="submit"]').prop('disabled', false);
                }, 1000);
            }
        });
    </script>
    <script>
        sidebarMenuOpen('#trading');
        sidebarActive('#delivery-order')
    </script>
@endsection
