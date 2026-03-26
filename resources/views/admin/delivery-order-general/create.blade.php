@extends('layouts.admin.layout.index')

@php
    $main = 'delivery-order-general';
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
                        <a href="{{ route('admin.delivery-order.index') }}">{{ Str::headline($main) }}</a>
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
    <form action='{{ route("admin.$main.store") }}' method="post" id="form-create">
        @csrf

        <x-card-data-table title="{{ 'Pilih Sales Order' }}">
            <x-slot name="header_content">

            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-select name="sale_order_general_id" label="sale_order_general" id="sale-order-general-id-select" required>

                            </x-select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" name="total_item" label="total_item" id="sale-order-general-total-item" required readonly />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" name="created_at" label="dibuat-pada" id="sale-order-general-created-at" required readonly />
                        </div>
                    </div>
                </div>
            </x-slot>
        </x-card-data-table>

        <x-card-data-table title="{{ 'Detail Delivery order general' }}" id="delivery-order-general-detail-card">
            <x-slot name="header_content">

            </x-slot>
            <x-slot name="table_content">
                <div id="delivery-order-detail-card-parent">

                </div>

                <div id="delivery-order-detail-card-warehouse" class="border-top border-primary mt-20 py-20">

                </div>

                <div id="delivery-order-detail-card-child" class="border-top border-primary mt-20 py-20">

                </div>

                <div id="delivery-order-detail-card-description">

                </div>
            </x-slot>

            <x-slot name="footer">
                <div id="btn-submit" class="mt-10">
                    <div class="d-flex justify-content-end gap-3">
                        <x-button type="submit" color="primary" icon="plus" fontawesome label="Save data" />
                    </div>
                </div>
            </x-slot>
        </x-card-data-table>

    </form>
@endsection

@section('js')
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/admin/select/SaleOrderGeneral.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script>
        $(document).ready(function() {

            const init = () => {

                let DATA_SALE_ORDER_GENERAL = [];
                let date_sale_order = null;
                let item_stocks = [];

                const resetFormChild = () => {
                    $('#delivery-order-general-detail-card').fadeOut(500);
                    $('#delivery-order-detail-card-parent').html('');
                    $('#delivery-order-detail-card-child').html('');
                    $('#delivery-order-detail-card-description').html('');
                    $('#sale-order-general-total-item').val('');
                    $('#sale-order-general-created-at').val('');
                };

                const firstStep = () => {
                    $('#delivery-order-general-detail-card').hide();

                    initSelectDeliveryOrderGeneralSelect('#sale-order-general-id-select', '{{ route('admin.sales-order-general.select-for-delivery-order') }}');

                    $('#sale-order-general-id-select').change(function(e) {
                        e.preventDefault();

                        resetFormChild();

                        if ($(this).val()) {
                            $.ajax({
                                type: "get",
                                url: `{{ route('admin.sales-order-general.detail-for-delivery-order') }}/${this.value}`,
                                success: function({
                                    data
                                }) {
                                    DATA_SALE_ORDER_GENERAL = data;
                                    date_sale_order = data.model.tanggal;
                                    secondStep();
                                }
                            });
                        }
                    });
                };

                const secondStep = () => {

                    $('#delivery-order-general-detail-card').fadeIn(500);
                    $('#delivery-order-detail-card-parent').html('');
                    $('#delivery-order-detail-card-child').html('');

                    const initializeSecondStep = () => {
                        displayParent();
                        displayItemForDelivery();
                        displayDescription();
                    };

                    const displayParent = () => {
                        $('#sale-order-general-created-at').val(DATA_SALE_ORDER_GENERAL.model.created_at);
                        $('#sale-order-general-total-item').val(DATA_SALE_ORDER_GENERAL.items.length);
                    };

                    const displayItemForDelivery = () => {

                        const initializeDisplayItem = () => {
                            displayForm();
                            displayItems();
                        };

                        const displayForm = () => {
                            $('#delivery-order-detail-card-parent').html(`
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <x-input class="datepicker-input" name="date" id="date" value="" onchange="checkClosingPeriod($(this))" required />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <x-input class="datepicker-input" name="target_delivery" id="target_delivery" label="target_delivery" value="{{ \Carbon\Carbon::now()->format('d-m-Y') }}" required />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <x-input class="datepicker-input" name="date_send" id="date_send" label="tanggal dikirim" required />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <x-input class="datepicker-input" name="date_receive" id="date_receive" label="tanggal diterima" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row border-top pt-20 mt-20">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <x-input type="text" name="drop" label="drop-poin / ship to" id="" required value="${DATA_SALE_ORDER_GENERAL.model.drop_point ?? ''}" />
                                        </div>
                                    </div>
                                </div>
                            `);

                            initDatePicker();

                            checkClosingPeriod($('#date'))
                            $('#date').change(function(event) {

                                if (parseDate(date_sale_order) > parseDate(this.value)) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Tanggal tidak boleh kurang dari tanggal SO',
                                    });

                                    this.value = null;
                                    return;
                                }
                            });

                            $('#target_delivery').change(function(event) {

                                if (parseDate(date_sale_order) > parseDate(this.value)) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Tanggal tidak boleh kurang dari tanggal SO',
                                    });

                                    this.value = null;
                                    return;
                                }
                            });

                            $('#date_send').change(function(event) {

                                if (parseDate(date_sale_order) > parseDate(this.value)) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Tanggal tidak boleh kurang dari tanggal SO',
                                    });

                                    this.value = null;
                                    return;
                                }
                            });

                            $('#date_receive').change(function(event) {

                                if (parseDate(date_sale_order) > parseDate(this.value)) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Tanggal tidak boleh kurang dari tanggal SO',
                                    });

                                    this.value = null;
                                    return;
                                }
                            });

                            $('#delivery-order-detail-card-warehouse').html(`<x-select name="ware_house_id" label="gudang" id="warehouse-select" required></x-select>`);

                            initSelect2Search('warehouse-select', `{{ route('admin.select.ware-house') }}?type=general`, {
                                id: "id",
                                text: "nama"
                            });

                            $('#warehouse-select').change(function(e) {

                                let items = DATA_SALE_ORDER_GENERAL.items;
                                items.map(function(item) {
                                    $(`.stock-left-${item.item_id}`).val(formatRupiahWithDecimal(0));
                                });

                                item_stocks = [];
                                if (this.value) {
                                    $.ajax({
                                        type: "get",
                                        url: `{{ route('admin.delivery-order-general.create.check-stock') }}`,
                                        data: {
                                            id: DATA_SALE_ORDER_GENERAL.model.id,
                                            ware_house_id: this.value
                                        },
                                        success: function({
                                            data
                                        }) {
                                            data.map(function(item) {
                                                $(`.stock-left-${item.item}`).val(formatRupiahWithDecimal(item.stock));
                                            });

                                            item_stocks = data;

                                            $('input[name="quantity[]"]').each(function() {
                                                $(this).trigger('keyup');
                                            });
                                        }
                                    });
                                    return;
                                }
                            });

                        };

                        const displayItems = () => {
                            let data_items = DATA_SALE_ORDER_GENERAL.items;

                            data_items.map((data_item, item_index) => {

                                let {
                                    item,
                                    unit
                                } = data_item;

                                $('#delivery-order-detail-card-child').append(`
                                    <div class="row ${item_index != 0 ? "mt-20 pt-20" : ''}">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-input type="text" name="item_id" id="" value="${item.nama} - ${item.kode}" label="Item" helpers="${unit?.name}" required readonly />
                                                <input type="hidden" name="sale_order_general_detail_id[]" value="${data_item.id}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-input type="text" name="quantity_order[]" label="quantity dipesan" value="${decimalFormatterCommasWithOuNumberWithCommas(data_item.amount)} / ${decimalFormatterCommasWithOuNumberWithCommas(data_item.sended)}" id="" class="commas-form" helpers="dipesan / sudah dikirim - ${unit.name}" required readonly/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-input type="text" name="stock-left" label="sisa_stock" value="" id="" class="commas-form stock-left-${data_item.item_id}" helpers="${unit?.name}" required readonly/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-input type="text" name="quantity[]" label="quantity dikirim" value="0" id="quantity-sended-${item_index}" class="commas-form" helpers="${unit?.name}" required/>
                                            </div>
                                        </div>
                                    </div>
                                `);

                                $(`#quantity-sended-${item_index}`).keyup(function(e) {
                                    e.preventDefault();

                                    let value = thousandToFloat($(this).val());
                                    let quantity_order = data_item.amount;
                                    let quantity_sended = data_item.sended;
                                    let stock_left = thousandToFloat($(`.stock-left-${data_item.item_id}`).val());

                                    if (data_item.item.item_category.item_type.nama == "purchase item") {
                                        if (value > stock_left) {
                                            alert('quantity dikirim tidak boleh melebihi stock');
                                            $(this).val(stock_left);
                                        }
                                    }

                                    if (value > (quantity_order - quantity_sended)) {
                                        alert('quantity dikirim tidak boleh melebihi quantity dipesan');
                                        $(this).val(quantity_order - quantity_sended);
                                    }
                                });
                            });

                            initCommasForm();
                        };

                        initializeDisplayItem();
                    };

                    const displayDescription = () => {
                        $('#delivery-order-detail-card-description').html(`
                            <div class="row justify-content-end">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <x-text-area name="description" id="" cols="30" rows="10"></x-text-area>
                                    </div>
                                </div>
                            </div>
                        `);
                    };

                    initializeSecondStep();
                };

                const handleForm = () => {

                    $('#form-create').submit(function(e) {
                        e.preventDefault();

                        let result = false;

                        if (item_stocks.length <= 0) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Stock Item kosong',
                            });

                            result = true;
                        }

                        // item_stocks.map(function(item) {
                        //     if (item.stock <= 0) {
                        //         Swal.fire({
                        //             icon: 'error',
                        //             title: 'Oops...',
                        //             text: 'Stock Item kosong',
                        //         });

                        //         result = true;
                        //     }
                        // })

                        if (result) {
                            $('form').each(function() {
                                $(this).find('input[type=submit]').prop('disabled', false);
                                $(this).find('button[type=submit]').prop('disabled', false);
                            });
                            return;
                        }

                        $(this).unbind('submit').submit();
                    })
                }

                firstStep();
                handleForm();
            };

            init();
        });
    </script>
    <script>
        sidebarMenuOpen('#trading');
        sidebarActive('#delivery-order')
    </script>
@endsection
