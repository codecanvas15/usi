@extends('layouts.admin.layout.index')

@php
    $main = 'purchase-transport';
@endphp

@section('title', Str::headline("Tambah $main") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.purchase.index') }}">{{ Str::headline('Purchase') }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Tambah ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can('create purchase-transport')
        <form action="{{ route('admin.purchase-order-transport.store') }}" id="po_transport" method="post">
            @csrf
            <x-card-data-table title="{{ 'Tambah Purchase Transport' }}">
                <x-slot name="table_content">
                    @include('components.validate-error')
                    <div class="row col-md-3">
                        <x-select name="type" label='tipe' id="purchaseTransprt-selectForm" required>
                            <option value="">-----------------------</option>
                            <option value="not_double_handling">Single Handling</option>
                            <option value="double_handling">Double Handling</option>
                        </x-select>
                    </div>
                </x-slot>
            </x-card-data-table>

            <div id="slot-data"></div>
        </form>
    @endcan
@endsection

@section('js')
    @can('create purchase-transport')
        <script src="{{ asset('js/admin/select/saleOrderTradingForDelivery.js') }}"></script>
        <script src="{{ asset('js/admin/select/itemSelect.js') }}"></script>
        <script src="{{ asset('js/admin/select/warehouse.js') }}"></script>
        <script src="{{ asset('js/form/select2search.js') }}"></script>
        <script src="{{ asset('js/helpers/helpers.js') }}"></script>

        <script>
            $(document).ready(function() {

                let QUANTITY_DELIVERY_ORDER = 0,
                    QUANTITY_LEFT_DELIVERY_ORDER = 0,
                    PRICE = 0,
                    QTY = 0,
                    TAX_LIST_ID = [],
                    TAX_LIST_VALUE = [],
                    VENDOR_TWO_TAX_LIST_ID = [],
                    VENDOR_TWO_TAX_LIST_VALUE = [],
                    ITEM_ID = null,
                    STOCK_LEFT = 0;

                let AMOUNT_QUANTITY_VENDOR_ONE = 0,
                    AMOUNT_DO_LIST = [],
                    QTY_LIST = [];

                let DATA_INDEX = 0;

                let currency_symbol = '{{ get_local_currency()->simbol }}';

                const reset = () => {
                    QUANTITY_DELIVERY_ORDER = 0;
                    QUANTITY_LEFT_DELIVERY_ORDER = 0;
                    QTY = 0;
                    STOCK_LEFT = 0;
                    AMOUNT_QUANTITY_VENDOR_ONE = 0;
                    AMOUNT_DO_LIST = [];
                    QTY_LIST = [];
                    DATA_INDEX = 0;

                    $('#slot-data').find('*').off();
                    $('#slot-data').html('');
                };

                const handleFirstCard = () => {
                    $('#purchaseTransprt-selectForm').change(function(e) {
                        e.preventDefault();
                        reset();
                        if (this.value == 'double_handling') {
                            handleDoubleHandling();
                        } else if (this.value == 'not_double_handling') {
                            handleNotDuobleHandling();
                        }
                    });
                };

                const handleNotDuobleHandling = () => {

                    const inititalizeNotDoubleHandling = () => {
                        initializeSelectSaleOrderCard();
                        initializeCalculationForm();
                    };

                    const resetData = () => {
                        QUANTITY_DELIVERY_ORDER = 0;
                        QUANTITY_LEFT_DELIVERY_ORDER = 0;
                        QTY = 0;
                        STOCK_LEFT = 0;
                        AMOUNT_QUANTITY_VENDOR_ONE = 0;
                        AMOUNT_DO_LIST = [];
                        QTY_LIST = [];
                        DATA_INDEX = 0;

                        $('#customerDate-saleOrder-inputForm').val(null);
                        $('#item-saleOrder-inputForm').val(null);
                        $('#quantity-saleOrder-inputForm').val(null);
                        $('#shNumber-saleOrder-inputForm').val(null);
                        $('#supplyPoint-saleOrder-inputForm').val(null);
                        $('#dropPoint-saleOrder-inputForm').val(null);

                        $('#slot-data-2').find('*').off();
                        $('#slot-data-2').html('');
                    };

                    const handleFormSelectOrder = () => {
                        $('#saleOrderSelect-selectForm').change(function(e) {
                            e.preventDefault();

                            resetData();
                            if (this.value) {

                                $.ajax({
                                    type: "GET",
                                    url: `{{ route('admin.sales-order.detail-for-delivery') }}/${this.value}`,
                                    success: function({
                                        data
                                    }) {
                                        let {
                                            customer,
                                            sh_number,
                                            so_trading_detail,
                                            nomor_so,
                                            tanggal
                                        } = data;

                                        resetData();

                                        $('#customerDate-saleOrder-inputForm').val(`${customer.nama} / ${tanggal}`);
                                        $('#item-saleOrder-inputForm').val(`${so_trading_detail.item.nama} - ${so_trading_detail.item.kode}`);
                                        $('#quantity-saleOrder-inputForm').val(`${formatRupiahWithDecimal(so_trading_detail.sudah_dialokasikan - so_trading_detail.sudah_dikirim)} / ${formatRupiahWithDecimal(so_trading_detail.jumlah)}`);
                                        $('#shNumber-saleOrder-inputForm').val(`${sh_number.kode}`);
                                        $('#supplyPoint-saleOrder-inputForm').val(`${sh_number.sh_number_details.find((detail) => {
                                            if (detail.type == 'Supply Point') {
                                                return detail;
                                            }
                                        }).alamat}`);
                                        $('#dropPoint-saleOrder-inputForm').val(`${sh_number.sh_number_details.find((detail) => {
                                            if (detail.type == 'Drop Point') {
                                                return detail;
                                            }
                                        }).alamat}`);

                                        QUANTITY_DELIVERY_ORDER = so_trading_detail.sudah_dialokasikan - so_trading_detail.sudah_dikirim;
                                        ITEM_ID = so_trading_detail.item_id;

                                        initializeInputAmountDeliveryOrder();
                                    }
                                });
                            }
                        });
                    };

                    const initializeSelectSaleOrderCard = () => {

                        $('#slot-data').append(`
                            <x-card-data-table>
                                <x-slot name="table_content">

                                    <div class="row">
                                        <div class="col-md-3">
                                            <x-select name="vendor_id" id="vendor-SelectForm" label="vendor" required>

                                            </x-select>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-input class="datepicker-input" id="target-inputForm" name="target_delivery" label="target pengiriman" value="{{ \Carbon\Carbon::now()->format('d-m-Y') }}" onchange="checkClosingPeriod($(this))" required />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row border-primary border-top mt-20 pt-10">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-select name="so_trading_id" label="sale order" id="saleOrderSelect-selectForm" required>

                                                </x-select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-input name="" label="Customer / date" id="customerDate-saleOrder-inputForm" required disabled/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-input name="" label="Item" id="item-saleOrder-inputForm" required disabled/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-input name="" label="Qty" id="quantity-saleOrder-inputForm" required disabled/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-input name="" label="sh number" id="shNumber-saleOrder-inputForm" required disabled/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-input name="" label="Supply" id="supplyPoint-saleOrder-inputForm" required disabled/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-input name="" label="drop" id="dropPoint-saleOrder-inputForm" required disabled/>
                                            </div>
                                        </div>
                                    </div>

                                </x-slot>
                            </x-card-data-table>

                            <div id="slot-data-2"></div>
                        `);

                        initDatePicker();
                        initSelect2SearchSO('saleOrderSelect-selectForm', "{{ route('admin.select.sos-for-do') }}")
                        initCommasForm();
                        initSelect2SearchPaginationData(`vendor-SelectForm`, `{{ route('admin.select.vendor') }}`, {
                            id: 'id',
                            text: 'nama'
                        })

                        handleFormSelectOrder();
                    };

                    const initializeInputAmountDeliveryOrder = () => {

                        const initWarehouseForm = () => {
                            $('#slot-data-2').append(`
                                <x-card-data-table>
                                    <x-slot name="table_content">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <x-select name="ware_house_id" label="gudang" id="wareHouse-selectForm" required>

                                                    </x-select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <x-input name="stock_left" label="sisa stock" id="stockLeft-selectForm" required disabled />
                                                </div>
                                            </div>
                                        </div>

                                        <div id="row-quantity">
                                        </div>
                                    </x-slot>
                                </x-card-data-table>
                            `)

                            initSelect2SearchPaginationData(`wareHouse-selectForm`, `{{ route('admin.select.ware-house.type') }}/trading`, {
                                id: 'id',
                                text: 'nama'
                            })
                            $('#wareHouse-selectForm').change(function(e) {
                                e.preventDefault();

                                $.ajax({
                                    type: "post",
                                    url: "{{ route('admin.purchase-order-transport.check-stock') }}",
                                    data: {
                                        _token: "{{ csrf_token() }}",
                                        ware_house_id: $(this).val(),
                                        item_id: ITEM_ID,
                                    },
                                    success: function({
                                        data
                                    }) {
                                        STOCK_LEFT = data.stock;
                                        $('#stockLeft-selectForm').val(formatRupiahWithDecimal(data.stock));
                                    }
                                });
                            });
                        };

                        const addAmountForm = (index) => {
                            DATA_INDEX++;

                            let last_html = '';
                            if (index == 0) {
                                last_html = `
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <x-input type="text" id="quantityLeft-deliveryOrderForm" name="" label="kuantitas belum dikirim" value="${formatRupiahWithDecimal(QUANTITY_DELIVERY_ORDER)}" required readonly />
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-center">
                                        <div class="form-group">
                                            <x-button color="info" icon="plus" fontawesome id="add-deliveryForm-btn" size="sm" />
                                        </div>
                                    </div>
                                `;
                            } else {
                                last_html = `
                                    <div class="col-md-2 d-flex align-items-center">
                                        <div class="form-group">
                                            <x-button color="danger" icon="trash" fontawesome id="delete-deliveryForm-btn-${index}" size="sm" />
                                        </div>
                                    </div>
                                `;
                            }

                            let html = `
                                <div class="row ${index == 0 ? "border-top border-primary mt-20 pt-20" : "mt-10"}" id="row-deliveryOrderForm-${index}">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <x-input type="text" class="commas-form" id="amountDO-deliveryOrderForm-${index}" name="amount_do[]" label="jumlah kendaraan" required />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <x-input type="text" class="commas-form" id="quantity-deliveryOrderForm-${index}" name="quantity[]" helpers="" label="kapasitas kendaraan" required />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <x-select name="vehicle_type[]" id="vehicle-type-${index}" label="jenis kendaraan" required class="form-select">
                                                <option value="">-- pilih --</option>
                                                <option value="darat">Darat</option>
                                                <option value="laut">Laut</option>
                                            </x-select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <x-input type="text" id="vehicle-info-${index}" name="vehicle_info[]"  label="informasi kendaraan" required />
                                        </div>
                                    </div>
                                    ${last_html}
                                </div>
                            `;

                            AMOUNT_DO_LIST[index] = 0;
                            QTY_LIST[index] = 0;
                            $('#row-quantity').append(html);
                            handleEventAddDeliveryForm(index);
                            initCommasForm();
                        };

                        const removeDeliveryForm = (index) => {
                            AMOUNT_DO_LIST[index] = 0;
                            QTY_LIST[index] = 0;

                            $(`#row-deliveryOrderForm-${index}`).remove();
                        };

                        const calculateSingleRealTime = (index) => {
                            let total_quantity = QTY_LIST.reduce((a, b, currentIndex) => {
                                return (AMOUNT_DO_LIST[currentIndex] * parseInt(b)) + parseInt(a)
                            }, 0);

                            if (total_quantity > STOCK_LEFT) {
                                alert('Kuantitas melebihi sisa stock');

                                AMOUNT_DO_LIST[index] = 0;
                                QTY_LIST[index] = 0;
                                $(`#amountDO-deliveryOrderForm-${index}`).val('0');
                                $(`#quantity-deliveryOrderForm-${index}`).val('0');
                                $('#quantityLeft-deliveryOrderForm').val(formatRupiahWithDecimal(QUANTITY_DELIVERY_ORDER));
                            }

                            if (total_quantity > QUANTITY_DELIVERY_ORDER) {
                                alert('Kuantitas melebihi sisa kuantitas DO');

                                AMOUNT_DO_LIST[index] = 0;
                                QTY_LIST[index] = 0;
                                $(`#amountDO-deliveryOrderForm-${index}`).val('0');
                                $(`#quantity-deliveryOrderForm-${index}`).val('0');
                                $('#quantityLeft-deliveryOrderForm').val(formatRupiahWithDecimal(QUANTITY_DELIVERY_ORDER));
                            } else {
                                QUANTITY_LEFT_DELIVERY_ORDER = QUANTITY_DELIVERY_ORDER;
                                $('#quantityLeft-deliveryOrderForm').val(formatRupiahWithDecimal(QUANTITY_LEFT_DELIVERY_ORDER - total_quantity));
                            }
                        };

                        const handleEventAddDeliveryForm = (index) => {
                            if (index == 0) {
                                $('#add-deliveryForm-btn').click(function(e) {
                                    e.preventDefault();
                                    addAmountForm(DATA_INDEX);
                                });
                            } else {
                                $(`#delete-deliveryForm-btn-${index}`).click(function(e) {
                                    e.preventDefault();
                                    removeDeliveryForm(index);
                                });
                            }

                            $(`#amountDO-deliveryOrderForm-${index}`).keyup(debounce(function(e) {
                                AMOUNT_DO_LIST[index] = thousandToFloat($(this).val());
                                calculateSingleRealTime(index);
                            }, 500));

                            $(`#quantity-deliveryOrderForm-${index}`).keyup(debounce(function(e) {
                                QTY_LIST[index] = thousandToFloat($(this).val());
                                calculateSingleRealTime(index);
                            }, 500));

                            $(`#quantityLeft-deliveryOrderForm-${index}`).val(formatRupiahWithDecimal(QUANTITY_DELIVERY_ORDER));
                        };

                        initWarehouseForm();
                        addAmountForm(DATA_INDEX);
                    };

                    const initializeCalculationForm = () => {

                        const initialize = () => {
                            addForm();
                            initializeFormThirdCard();
                            $('#recalculate-btn').click(function(e) {
                                e.preventDefault();
                                calculateFuction()
                            });
                        };

                        const addForm = () => {
                            $('#slot-data').append(`
                                <x-card-data-table>
                                    <x-slot name="table_content">
                                        <div  id="supplier_data_form_transport">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <x-select name="item_id" id="item-inputForm" label="Item" required>

                                                    </x-select>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <x-input type="text" name="price" id="price-inputForm" label="harga" class="commas-form text-end" required />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <x-select name="tax_id_" id="tax-inputForm" label="Tax" multiple>

                                                        </x-select>
                                                    </div>
                                                    <div id="tax_list">

                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <x-select name="currency_id" id="currency-inputForm" label="Currency" required>
                                                            <option value="{{ get_local_currency()->id }}" selected>{{ get_local_currency()->nama }}</option>
                                                        </x-select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <x-input name="exchange_rate" id="exchangerate-inputForm" value="1" label="kurs" required readonly />
                                                    </div>
                                                </div>
                                                <div class="col-md-3 d-flex align-items-end">
                                                    <div class="form-group">
                                                        <x-button color='info' icon='refresh' fontawesome id="recalculate-btn" label="calculate" size="sm" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-30">
                                            <x-table theadColor='danger' id="table-total">
                                                <x-slot name="table_head">
                                                    <th>Item</th>
                                                    <th>Harga</th>
                                                    <th width="32%">-</th>
                                                </x-slot>
                                                <x-slot name="table_body">
                                                    <tr>
                                                        <td id="item-name">-</td>
                                                        <td>
                                                            <span class="d-flex">
                                                                <p class="me-10" id="currency-simbol">{{ get_local_currency()->simbol }}</p>
                                                                <p class="fw-bold" id="item-price">0</p>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="d-flex">
                                                                <p class="me-10" id="currency-simbol">{{ get_local_currency()->simbol }}</p>
                                                                <p class="fw-bold ms-auto" id="display-subtotal"></p>
                                                            </span>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td colspan="2" class="fw-bold text-end">Total</td>
                                                        <td>
                                                            <div class=" align-self-end">
                                                                <span class="d-flex">
                                                                    <p class="me-10" id="currency-simbol">{{ get_local_currency()->simbol }}</p>
                                                                    <p class="fw-bold ms-auto" id="display-total">0</p>
                                                                </span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </x-slot>
                                            </x-table>
                                        </div>
                                    </x-slot>

                                    <x-slot name="footer">
                                        <div class="float-end mt-0" id="button_submit_here">
                                            <x-button type="reset" color="secondary" class="w-auto" label="cancel" size="sm" icon="backward" fontawesome link="{{ url()->previous() }}" />
                                            <x-button type="submit" color="primary" size="sm" icon="save" fontawesome class="w-auto" label="Save data" />
                                        </div>
                                    </x-slot>
                                </x-card-data-table>
                            `);
                        };

                        const initializeFormThirdCard = () => {

                            initSelect2SearchPaginationData(`item-inputForm`, `{{ route('admin.select.item.type') }}/transport`, {
                                id: 'id',
                                text: 'kode,nama'
                            })
                            $('#item-inputForm').change(function(e) {
                                e.preventDefault();

                                if (this.value) {
                                    $('#item-name').html($(this).select2('data')[0].text);

                                    $.ajax({
                                        type: "get",
                                        url: `{{ route('admin.item.price-latest') }}/${this.value}`,
                                        success: function({
                                            data
                                        }) {
                                            $('#price-inputForm').val(formatRupiahWithDecimal(data.harga_beli));
                                            $('#price-inputForm').trigger('keyup');
                                        }
                                    });

                                } else {
                                    $('#item-name').html('-');
                                    $('#price-inputForm').val('0');
                                    $('#price-inputForm').trigger('keyup');
                                }
                            });

                            $('#price-inputForm').keyup(debounce(function(e) {
                                e.preventDefault();

                                PRICE = thousandToFloat($(this).val());
                                $('#item-price').html(formatRupiahWithDecimal(PRICE));
                            }, 500));

                            initSelect2SearchPaginationData(`tax-inputForm`, `{{ route('admin.select.tax') }}`, {
                                id: 'id',
                                text: 'name'
                            })
                            $('#tax-inputForm').change(debounce(function(e) {
                                e.preventDefault();

                                $('#tax_list').html('');

                                TAX_LIST_ID.map((tax, index) => {
                                    $(`#tax-table-${index}`).remove();
                                });

                                TAX_LIST_ID = $('#tax-inputForm').val();
                                TAX_LIST_VALUE = [];

                                VENDOR_TWO_TAX_LIST_ID = [];
                                VENDOR_TWO_TAX_LIST_VALUE = [];

                                TAX_LIST_ID.map((tax, index) => setTimeout(() => {
                                    let num = 1;

                                    $('#tax_list').append(`<input type="hidden" name="tax_id[]" value="${tax}">`);

                                    $.ajax({
                                        type: "get",
                                        url: "{{ route('admin.tax.detail') }}/" + tax,
                                        success: ({
                                            data
                                        }) => {
                                            TAX_LIST_VALUE[index] = data;

                                            let new_html = `
                                                <tr id="tax-table-${index}">
                                                    <td colspan="2" class="fw-bold text-end">${data.name} - ${data.value * 100}%</td>
                                                    <td>
                                                        <span class="d-flex">
                                                            <p class="me-10" id="currency-simbol">${currency_symbol}</p>
                                                            <p id="single-taxValue-${index}" class="text-end ms-auto"></p>
                                                        </span>
                                                    </td>
                                                </tr>`;
                                            $(new_html).insertAfter(`table#table-total tbody tr:nth-child(1)`);
                                        }
                                    });
                                }, 500));
                            }));

                            initSelect2SearchPaginationData(`currency-inputForm`, `{{ route('admin.select.currency') }}`, {
                                id: 'id',
                                text: 'nama'
                            })
                            $('#currency-inputForm').change(function(e) {
                                e.preventDefault();
                                $.ajax({
                                    type: "get",
                                    url: `{{ route('admin.currency.detail') }}/${this.value}`,
                                    success: function({
                                        data
                                    }) {
                                        currency_symbol = data.simbol

                                        if (data.is_local) {
                                            $('#exchangerate-inputForm').val(1);
                                            $('#exchangerate-inputForm').attr('readonly', 'readonly');
                                        } else {
                                            $('#exchangerate-inputForm').removeAttr('readonly');
                                            $('#exchangerate-inputForm').attr('readonly', false);
                                        }
                                        currency_symbol = data.simbol;
                                        updateCurrencySymbol();
                                    }
                                });
                            });
                        };

                        const calculateFuction = () => {
                            let total_quantity = QTY_LIST.reduce((a, b, currentIndex) => {
                                return (AMOUNT_DO_LIST[currentIndex] * parseInt(b)) + parseInt(a)
                            }, 0);

                            let subtotal = total_quantity * PRICE;
                            let total = subtotal;

                            TAX_LIST_VALUE.map((tax, index) => {
                                $(`#single-taxValue-${index}`).html(formatRupiahWithDecimal(subtotal * tax.value));
                                total += subtotal * tax.value;
                            });

                            $('#display-subtotal').html(formatRupiahWithDecimal(subtotal));
                            $('#display-total').html(formatRupiahWithDecimal(total));

                        };

                        initialize();
                    };

                    inititalizeNotDoubleHandling();
                };

                const handleDoubleHandling = () => {

                    const initializeDouleHandlinng = () => {
                        $('#slot-data').append(`
                            <div id="slot-vendor-one"></div>
                            <div id="slot-vendor-two"></div>

                            <div id="slot-item-one"></div>
                            <div id="slot-item-two"></div>

                            <div id="slot-submit-btn"></div>
                        `);
                        vendorOne();
                        itemOne();
                        itemTwo();
                    };

                    const vendorOne = () => {

                        const initializeVendorOne = () => {
                            addVendorOneForm();
                        };

                        const addVendorOneForm = () => {
                            $('#slot-vendor-one').append(`
                                <x-card-data-table title="vendor 1">
                                    <x-slot name="table_content">

                                        <div class="row">
                                            <div class="col-md-3">
                                                <x-select name="one_vendor_id" id="one-vendor-delivery-SelectForm" label="vendor 1" required>

                                                </x-select>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <x-input class="datepicker-input" id="one-target-delivery-inputForm" name="one_target_delivery" label="target pengiriman 1" value="{{ \Carbon\Carbon::now()->format('d-m-Y') }}" onchange="checkClosingPeriod($(this))" required />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row border-primary border-top mt-20 pt-10">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <x-select name="one_so_trading_id" label="sale order" id="one-saleOrderSelect-selectForm" required>

                                                    </x-select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <x-input name="" label="Customer / date" id="one-customerDate-saleOrder-inputForm" required disabled/>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <x-input name="" label="Item" id="one-item-saleOrder-inputForm" required disabled/>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <x-input name="" label="Qty" id="one-quantity-saleOrder-inputForm" required disabled/>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <x-input name="" label="sh number" id="one-shNumber-saleOrder-inputForm" required disabled/>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <x-input name="" label="Supply" id="one-supplyPoint-saleOrder-inputForm" required disabled/>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <x-input name="" label="drop" id="one-dropPoint-saleOrder-inputForm" required disabled/>
                                                </div>
                                            </div>
                                        </div>

                                        <div id='amountDeliveryOne-data'>
                                        </div>

                                    </x-slot>
                                </x-card-data-table>
                            `);

                            initDatePicker();

                            initSelect2SearchSO('one-saleOrderSelect-selectForm', "{{ route('admin.select.sos-for-do') }}")
                            initSelect2SearchPaginationData(`one-vendor-delivery-SelectForm`, `{{ route('admin.select.vendor') }}`, {
                                id: 'id',
                                text: 'nama'
                            })

                            vendorOneEventListener();
                        };

                        const resetData = () => {
                            QUANTITY_DELIVERY_ORDER = 0;
                            QUANTITY_LEFT_DELIVERY_ORDER = 0;
                            QTY = 0;
                            STOCK_LEFT = 0;
                            AMOUNT_QUANTITY_VENDOR_ONE = 0;
                            AMOUNT_DO_LIST = [];
                            QTY_LIST = [];
                            DATA_INDEX = 0;

                            $('#customerDate-saleOrder-inputForm').val(null);
                            $('#item-saleOrder-inputForm').val(null);
                            $('#quantity-saleOrder-inputForm').val(null);
                            $('#shNumber-saleOrder-inputForm').val(null);
                            $('#supplyPoint-saleOrder-inputForm').val(null);
                            $('#dropPoint-saleOrder-inputForm').val(null);

                            $('#amountDeliveryOne-data').find('*').off();
                            $('#amountDeliveryOne-data').empty();

                            $('#slot-vendor-two').find('*').off();
                            $('#slot-vendor-two').empty();
                        };

                        const vendorOneEventListener = () => {

                            $('#one-saleOrderSelect-selectForm').change(function(e) {
                                e.preventDefault();

                                if (this.value) {

                                    $.ajax({
                                        type: "GET",
                                        url: `{{ route('admin.sales-order.detail-for-delivery') }}/${this.value}`,
                                        success: function({
                                            data
                                        }) {
                                            let {
                                                customer,
                                                sh_number,
                                                so_trading_detail,
                                                nomor_so,
                                                tanggal
                                            } = data;

                                            resetData();
                                            vendorTwo();

                                            $('#one-customerDate-saleOrder-inputForm').val(`${customer.nama} / ${tanggal}`);
                                            $('#one-item-saleOrder-inputForm').val(`${so_trading_detail.item.nama} - ${so_trading_detail.item.kode}`);
                                            $('#one-quantity-saleOrder-inputForm').val(`${formatRupiahWithDecimal(so_trading_detail.sudah_dialokasikan - so_trading_detail.sudah_dikirim)} / ${formatRupiahWithDecimal(so_trading_detail.jumlah)}`);
                                            $('#one-shNumber-saleOrder-inputForm').val(`${sh_number.kode}`);
                                            $('#one-supplyPoint-saleOrder-inputForm').val(`${sh_number.sh_number_details.find((detail) => {
                                                if (detail.type == 'Supply Point') {
                                                    return detail;
                                                }
                                            }).alamat}`);
                                            $('#dropPoint-saleOrder-inputForm').val(`${sh_number.sh_number_details.find((detail) => {
                                                if (detail.type == 'Drop Point') {
                                                    return detail;
                                                }
                                            }).alamat}`);

                                            QUANTITY_DELIVERY_ORDER = so_trading_detail.sudah_dialokasikan - so_trading_detail.sudah_dikirim;
                                            ITEM_ID = so_trading_detail.item_id;

                                            amountDeliveryOrderVendorOne();
                                        }
                                    });
                                }
                            });

                            const amountDeliveryOrderVendorOne = () => {
                                $('#amountDeliveryOne-data').append(`
                                    <div class="row border-primary border-top mt-20 pt-10">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-select name="one_ware_house_id" label="gudang" id="one-wareHouse-selectForm" required>

                                                </x-select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-input name="one_stock_left" label="sisa stock" id="one-stockLeft-selectForm" required disabled />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-20 pt-10">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-input name="one_quantity" label="Jumlah Pengiriman" class="commas-form" id="one-quantity-selectForm" required/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-input name="" label="Sisa" id="one-quantityLeft-selectForm" required disabled/>
                                            </div>
                                        </div>
                                    </div>
                                `);

                                initSelect2SearchPaginationData(`one-wareHouse-selectForm`, `{{ route('admin.select.ware-house.type') }}/trading`, {
                                    id: 'id',
                                    text: 'nama'
                                })
                                $('#one-wareHouse-selectForm').change(function(e) {
                                    e.preventDefault();

                                    $.ajax({
                                        type: "post",
                                        url: "{{ route('admin.purchase-order-transport.check-stock') }}",
                                        data: {
                                            _token: "{{ csrf_token() }}",
                                            ware_house_id: $(this).val(),
                                            item_id: ITEM_ID,
                                        },
                                        success: function({
                                            data
                                        }) {
                                            STOCK_LEFT = data.stock;
                                            $('#one-stockLeft-selectForm').val(formatRupiahWithDecimal(data.stock));
                                        }
                                    });
                                });

                                $('#one-quantity-selectForm').keyup(debounce(function(e) {
                                    e.preventDefault();

                                    let value = thousandToFloat($(this).val());
                                    AMOUNT_QUANTITY_VENDOR_ONE = value;

                                    if (value > STOCK_LEFT) {
                                        alert('jumlah pengiriman tidak boleh melebihi stock');
                                        $(this).val(formatRupiahWithDecimal(0));
                                        $('#one-quantityLeft-selectForm').val(formatRupiahWithDecimal(STOCK_LEFT));
                                    } else {
                                        // QUANTITY_LEFT_DELIVERY_ORDER = ;
                                        $('#one-quantityLeft-selectForm').val(formatRupiahWithDecimal(STOCK_LEFT - AMOUNT_QUANTITY_VENDOR_ONE));
                                    }

                                }, 500));

                                initCommasForm();
                            };
                        };

                        initializeVendorOne();
                    };

                    const vendorTwo = () => {
                        const initializeVendorTwo = () => {
                            addFormVendorTwo();
                        };

                        const addFormVendorTwo = () => {
                            $('#slot-vendor-two').html(`
                                <x-card-data-table title="vendor 2">
                                    <x-slot name="table_content">

                                        <div class="row">
                                            <div class="col-md-3">
                                                <x-select name="two_vendor_id" id="two-vendor-delivery-SelectForm" label="vendor 2" required>

                                                </x-select>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <x-input class="datepicker-input" id="two-target-delivery-inputForm" name="two_target_delivery" label="target_pengiriman 2" value="{{ \Carbon\Carbon::now()->format('d-m-Y') }}" onchange="checkClosingPeriod($(this))" required />
                                                </div>
                                            </div>
                                        </div>

                                        <div id="row-quantity"></div>
                                    </x-slot>
                                </x-card-data-table>
                            `);

                            initDatePicker();

                            initSelect2SearchPaginationData(`two-vendor-delivery-SelectForm`, `{{ route('admin.select.vendor') }}`, {
                                id: 'id',
                                text: 'nama'
                            })

                            addFormDeliveryOrderForm(DATA_INDEX);
                        };

                        const addFormDeliveryOrderForm = (index) => {
                            DATA_INDEX++;

                            let last_html = '';
                            if (index == 0) {
                                last_html = `
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <x-input type="text" id="two-quantityLeft-deliveryOrderForm" name="" label="kuantitas belum dikirim" value="${formatRupiahWithDecimal(QUANTITY_DELIVERY_ORDER)}" required readonly />
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-center">
                                        <div class="form-group">
                                            <x-button color="info" icon="plus" fontawesome id="two-add-deliveryForm-btn" size="sm" />
                                        </div>
                                    </div>
                                `;
                            } else {
                                last_html = `
                                    <div class="col-md-2 d-flex align-items-center">
                                        <div class="form-group">
                                            <x-button color="danger" icon="trash" fontawesome id="two-delete-deliveryForm-btn-${index}" size="sm" />
                                        </div>
                                    </div>
                                `;
                            }

                            let html = `
                                <div class="row ${index == 0 ? "border-top border-primary mt-20 pt-20" : "mt-10"}" id="two-row-deliveryOrderForm-${index}">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <x-input type="text" class="commas-form" id="two-amountDO-deliveryOrderForm-${index}" name="two_amount_do[]" label="jumlah kendaraan" required />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <x-input type="text" class="commas-form" id="two-quantity-deliveryOrderForm-${index}" name="two_quantity[]" helpers="" label="kapasitas kendaraan" required />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <x-select name="vehicle_type[]" id="vehicle-type-${index}" label="jenis kendaraan" required class="form-select">
                                                <option value="">-- pilih --</option>
                                                <option value="darat">Darat</option>
                                                <option value="laut">Laut</option>
                                            </x-select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <x-input type="text" id="vehicle-info-${index}" name="vehicle_info[]"  label="informasi kendaraan" required />
                                        </div>
                                    </div>
                                    ${last_html}
                                </div>
                            `;

                            AMOUNT_DO_LIST[index] = 0;
                            QTY_LIST[index] = 0;
                            $('#row-quantity').append(html);
                            handleEventAddDeliveryForm(index);
                            initCommasForm();
                        };

                        const removeDeliveryForm = (index) => {
                            AMOUNT_DO_LIST[index] = 0;
                            QTY_LIST[index] = 0;

                            $(`#two-row-deliveryOrderForm-${index}`).remove();
                        };

                        const handleEventAddDeliveryForm = (index) => {
                            if (index == 0) {
                                $('#two-add-deliveryForm-btn').click(function(e) {
                                    e.preventDefault();
                                    addFormDeliveryOrderForm(DATA_INDEX);
                                });
                            } else {
                                $(`#two-delete-deliveryForm-btn-${index}`).click(function(e) {
                                    e.preventDefault();
                                    removeDeliveryForm(index);
                                });
                            }

                            $(`#two-amountDO-deliveryOrderForm-${index}`).keyup(debounce(function(e) {
                                AMOUNT_DO_LIST[index] = thousandToFloat($(this).val());
                                calculateSingleRealTime(index);
                            }, 500));

                            $(`#two-quantity-deliveryOrderForm-${index}`).keyup(debounce(function(e) {
                                QTY_LIST[index] = thousandToFloat($(this).val());
                                calculateSingleRealTime(index);
                            }, 500));

                            $(`#two-quantityLeft-deliveryOrderForm-${index}`).val(formatRupiahWithDecimal(QUANTITY_DELIVERY_ORDER));
                        };

                        initializeVendorTwo();
                    };

                    const calculateSingleRealTime = (index) => {
                        let total_quantity = QTY_LIST.reduce((a, b, currentIndex) => {
                            return (AMOUNT_DO_LIST[currentIndex] * parseInt(b)) + parseInt(a)
                        }, 0);

                        if (total_quantity > AMOUNT_QUANTITY_VENDOR_ONE) {
                            alert('Kuantitas melebihi sisa vendor 1');

                            AMOUNT_DO_LIST[index] = 0;
                            QTY_LIST[index] = 0;
                            $(`#two-amountDO-deliveryOrderForm-${index}`).val('0');
                            $(`#two-quantity-deliveryOrderForm-${index}`).val('0');
                            $('#two-quantityLeft-deliveryOrderForm').val(formatRupiahWithDecimal(QUANTITY_DELIVERY_ORDER));
                        }

                        if (total_quantity > QUANTITY_DELIVERY_ORDER) {
                            alert('Kuantitas melebihi sisa kuantitas DO');

                            AMOUNT_DO_LIST[index] = 0;
                            QTY_LIST[index] = 0;
                            $(`#two-amountDO-deliveryOrderForm-${index}`).val('0');
                            $(`#two-quantity-deliveryOrderForm-${index}`).val('0');
                            $('#two-quantityLeft-deliveryOrderForm').val(formatRupiahWithDecimal(QUANTITY_DELIVERY_ORDER));
                        } else {
                            QUANTITY_LEFT_DELIVERY_ORDER = QUANTITY_DELIVERY_ORDER;
                            $('#two-quantityLeft-deliveryOrderForm').val(formatRupiahWithDecimal(QUANTITY_LEFT_DELIVERY_ORDER - total_quantity));
                        }
                    };

                    const itemOne = () => {
                        const initItemOne = () => {
                            addFormItemOne();
                            handleEventAddItemOne();
                        };

                        const addFormItemOne = () => {
                            $('#slot-item-one').append(`
                                <x-card-data-table title="Item 1">
                                    <x-slot name="table_content">
                                        <div  id="supplier_data_form_transport">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <x-select name="one_item_id" id="one-item-inputForm" label="Item" required>

                                                    </x-select>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <x-input type="text" name="one_price" id="one-price-inputForm" label="harga" class="commas-form text-end" required />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <x-select name="one_tax_id_" id="one-tax-inputForm" label="Tax" multiple>

                                                        </x-select>
                                                    </div>
                                                    <div id="one-tax_list">

                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <x-select name="one_currency_id" id="one-currency-inputForm" label="Currency" required>
                                                            <option value="{{ get_local_currency()->id }}" selected>{{ get_local_currency()->nama }}</option>
                                                        </x-select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <x-input name="one_exchange_rate" id="one-exchangerate-inputForm" value="1" label="kurs" required readonly />
                                                    </div>
                                                </div>
                                                <div class="col-md-3 d-flex align-items-end">
                                                    <div class="form-group">
                                                        <x-button color='info' icon='refresh' fontawesome id="one-recalculate-btn" label="calculate" size="sm" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-30">
                                            <x-table theadColor='danger' id="one-table-total">
                                                <x-slot name="table_head">
                                                    <th>Item</th>
                                                    <th>Harga</th>
                                                    <th width="32%">-</th>
                                                </x-slot>
                                                <x-slot name="table_body">
                                                    <tr>
                                                        <td id="one-item-name">-</td>
                                                        <td>
                                                            <span class="d-flex">
                                                                <p class="me-10" id="one-currency-simbol">{{ get_local_currency()->simbol }}</p>
                                                                <p class="fw-bold" id="one-item-price">0</p>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="d-flex">
                                                                <p class="me-10" id="one-currency-simbol">{{ get_local_currency()->simbol }}</p>
                                                                <p class="fw-bold ms-auto" id="one-display-subtotal"></p>
                                                            </span>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td colspan="2" class="fw-bold text-end">Total</td>
                                                        <td>
                                                            <div class=" align-self-end">
                                                                <span class="d-flex">
                                                                    <p class="me-10" id="one-currency-simbol">{{ get_local_currency()->simbol }}</p>
                                                                    <p class="fw-bold ms-auto" id="one-display-total">0</p>
                                                                </span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </x-slot>
                                            </x-table>
                                        </div>
                                    </x-slot>
                                </x-card-data-table>
                            `);
                        };

                        const handleEventAddItemOne = () => {
                            $('#one-recalculate-btn').click(function(e) {
                                e.preventDefault();
                                calculateVendorOne();
                            });

                            initSelect2SearchPaginationData(`one-item-inputForm`, `{{ route('admin.select.item.type') }}/transport`, {
                                id: 'id',
                                text: 'kode,nama'
                            })
                            $('#one-item-inputForm').change(function(e) {
                                e.preventDefault();

                                if (this.value) {
                                    $('#one-item-name').html($(this).select2('data')[0].text);

                                    $.ajax({
                                        type: "get",
                                        url: `{{ route('admin.item.price-latest') }}/${this.value}`,
                                        success: function({
                                            data
                                        }) {
                                            $('#one-price-inputForm').val(formatRupiahWithDecimal(data.harga_beli));
                                            $('#one-price-inputForm').trigger('keyup');
                                        }
                                    });

                                } else {
                                    $('#one-item-name').html('-');
                                    $('#one-price-inputForm').val('0');
                                    $('#one-price-inputForm').trigger('keyup');
                                }
                            });

                            $('#one-price-inputForm').keyup(debounce(function(e) {
                                e.preventDefault();

                                $('#one-item-price').html(formatRupiahWithDecimal(thousandToFloat(this.value)));
                            }, 500));

                            initSelect2SearchPaginationData(`one-tax-inputForm`, `{{ route('admin.select.tax') }}`, {
                                id: 'id',
                                text: 'name'
                            })

                            $('#one-tax-inputForm').change(debounce(function(e) {
                                e.preventDefault();

                                $('#one-tax_list').html('');

                                TAX_LIST_ID.map((tax, index) => {
                                    $(`#one-tax-table-${index}`).remove();
                                });

                                TAX_LIST_ID = $('#one-tax-inputForm').val();
                                TAX_LIST_VALUE = [];

                                TAX_LIST_ID.map((tax, index) => setTimeout(() => {
                                    let num = 1;

                                    $('#one-tax_list').append(`<input type="hidden" name="one_tax_id[]" value="${tax}">`);

                                    $.ajax({
                                        type: "get",
                                        url: "{{ route('admin.tax.detail') }}/" + tax,
                                        success: ({
                                            data
                                        }) => {
                                            TAX_LIST_VALUE[index] = data;

                                            let new_html = `
                                                <tr id="one-tax-table-${index}">
                                                    <td colspan="2" class="fw-bold text-end">${data.name} - ${data.value * 100}%</td>
                                                    <td>
                                                        <span class="d-flex">
                                                            <p class="me-10" id="currency-simbol">${currency_symbol}</p>
                                                            <p id="one-single-taxValue-${index}" class="text-end ms-auto"></p>
                                                        </span>
                                                    </td>
                                                </tr>`;
                                            $(new_html).insertAfter(`table#one-table-total tbody tr:nth-child(1)`);
                                        }
                                    });
                                }, 500));
                            }));

                            initSelect2SearchPaginationData(`one-currency-inputForm`, `{{ route('admin.select.currency') }}`, {
                                id: 'id',
                                text: 'nama'
                            })

                            $('#one-currency-inputForm').change(function(e) {
                                e.preventDefault();
                                $.ajax({
                                    type: "get",
                                    url: `{{ route('admin.currency.detail') }}/${this.value}`,
                                    success: function({
                                        data
                                    }) {
                                        currency_symbol = data.simbol

                                        if (data.is_local) {
                                            $('#one-exchangerate-inputForm').val(1);
                                            $('#one-exchangerate-inputForm').attr('readonly', 'readonly');
                                        } else {
                                            $('#one-exchangerate-inputForm').removeAttr('readonly');
                                            $('#one-exchangerate-inputForm').attr('readonly', false);
                                        }
                                        currency_symbol = data.simbol;
                                        updateCurrencySymbol();
                                    }
                                });
                            });
                        };

                        const calculateVendorOne = () => {
                            let total_quantity = AMOUNT_QUANTITY_VENDOR_ONE;

                            let subtotal = total_quantity * thousandToFloat($('#one-price-inputForm').val());
                            let total = subtotal;

                            TAX_LIST_VALUE.map((tax, index) => {
                                $(`#one-single-taxValue-${index}`).html(formatRupiahWithDecimal(subtotal * tax.value));
                                total += subtotal * tax.value;
                            });

                            $('#one-display-subtotal').html(formatRupiahWithDecimal(subtotal));
                            $('#one-display-total').html(formatRupiahWithDecimal(total));
                        };

                        initItemOne();
                    };

                    const itemTwo = () => {
                        const initItemOne = () => {
                            addFormItemTwo();
                            handleEventAddItemTwo();
                        };

                        const addFormItemTwo = () => {
                            $('#slot-item-two').append(`
                                <x-card-data-table title="Item 2">
                                    <x-slot name="table_content">
                                        <div  id="supplier_data_form_transport">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <x-select name="two_item_id" id="two-item-inputForm" label="Item" required>

                                                    </x-select>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <x-input type="text" name="two_price" id="two-price-inputForm" label="harga" class="commas-form text-end" required />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <x-select name="two_tax_id_" id="two-tax-inputForm" label="Tax" multiple>

                                                        </x-select>
                                                    </div>
                                                    <div id="two-tax_list">

                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <x-select name="two_currency_id" id="two-currency-inputForm" label="Currency" required>
                                                            <option value="{{ get_local_currency()->id }}" selected>{{ get_local_currency()->nama }}</option>
                                                        </x-select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <x-input name="two_exchange_rate" id="two-exchangerate-inputForm" value="1" label="kurs" required readonly />
                                                    </div>
                                                </div>
                                                <div class="col-md-3 d-flex align-items-end">
                                                    <div class="form-group">
                                                        <x-button color='info' icon='refresh' fontawesome id="two-recalculate-btn" label="calculate" size="sm" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-30">
                                            <x-table theadColor='danger' id="two-table-total">
                                                <x-slot name="table_head">
                                                    <th>Item</th>
                                                    <th>Harga</th>
                                                    <th width="32%">-</th>
                                                </x-slot>
                                                <x-slot name="table_body">
                                                    <tr>
                                                        <td id="two-item-name">-</td>
                                                        <td>
                                                            <span class="d-flex">
                                                                <p class="me-10" id="two-currency-simbol">{{ get_local_currency()->simbol }}</p>
                                                                <p class="fw-bold" id="two-item-price">0</p>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="d-flex">
                                                                <p class="me-10" id="two-currency-simbol">{{ get_local_currency()->simbol }}</p>
                                                                <p class="fw-bold ms-auto" id="two-display-subtotal"></p>
                                                            </span>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td colspan="2" class="fw-bold text-end">Total</td>
                                                        <td>
                                                            <div class=" align-self-end">
                                                                <span class="d-flex">
                                                                    <p class="me-10" id="two-currency-simbol">{{ get_local_currency()->simbol }}</p>
                                                                    <p class="fw-bold ms-auto" id="two-display-total">0</p>
                                                                </span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </x-slot>
                                            </x-table>
                                        </div>
                                    </x-slot>

                                    <x-slot name="footer">
                                        <div class="float-end mt-0" id="button_submit_here">
                                            <x-button type="reset" color="secondary" class="w-auto" label="cancel" size="sm" icon="backward" fontawesome link="{{ url()->previous() }}" />
                                            <x-button type="submit" color="primary" size="sm" icon="save" fontawesome class="w-auto" label="Save data" />
                                        </div>
                                    </x-slot>
                                </x-card-data-table>
                            `);
                        };

                        const handleEventAddItemTwo = () => {
                            $('#two-recalculate-btn').click(function(e) {
                                e.preventDefault();
                                calculateVendorTwo();
                            });
                            initSelect2SearchPaginationData(`two-item-inputForm`, `{{ route('admin.select.item.type') }}/transport`, {
                                id: 'id',
                                text: 'kode,nama'
                            })
                            $('#two-item-inputForm').change(function(e) {
                                e.preventDefault();

                                if (this.value) {
                                    $('#two-item-name').html($(this).select2('data')[0].text);

                                    $.ajax({
                                        type: "get",
                                        url: `{{ route('admin.item.price-latest') }}/${this.value}`,
                                        success: function({
                                            data
                                        }) {
                                            $('#two-price-inputForm').val(formatRupiahWithDecimal(data.harga_beli));
                                            $('#two-price-inputForm').trigger('keyup');
                                        }
                                    });

                                } else {
                                    $('#two-item-name').html('-');
                                    $('#two-price-inputForm').val('0');
                                    $('#two-price-inputForm').trigger('keyup');
                                }
                            });

                            $('#two-price-inputForm').keyup(debounce(function(e) {
                                e.preventDefault();
                                $('#two-item-price').html(formatRupiahWithDecimal(this.value));
                            }, 500));

                            initSelect2SearchPaginationData(`two-tax-inputForm`, `{{ route('admin.select.tax') }}`, {
                                id: 'id',
                                text: 'name'
                            })

                            $('#two-tax-inputForm').change(debounce(function(e) {
                                e.preventDefault();

                                $('#two-tax_list').html('');

                                VENDOR_TWO_TAX_LIST_ID.map((tax, index) => {
                                    $(`#two-tax-table-${index}`).remove();
                                });

                                VENDOR_TWO_TAX_LIST_ID = $('#two-tax-inputForm').val();
                                VENDOR_TWO_TAX_LIST_VALUE = [];

                                VENDOR_TWO_TAX_LIST_ID.map((tax, index) => setTimeout(() => {
                                    let num = 1;

                                    $('#two-tax_list').append(`<input type="hidden" name="two_tax_id[]" value="${tax}">`);

                                    $.ajax({
                                        type: "get",
                                        url: "{{ route('admin.tax.detail') }}/" + tax,
                                        success: ({
                                            data
                                        }) => {
                                            VENDOR_TWO_TAX_LIST_VALUE[index] = data;

                                            let new_html = `
                                                <tr id="two-tax-table-${index}">
                                                    <td colspan="2" class="fw-bold text-end">${data.name} - ${data.value * 100}%</td>
                                                    <td>
                                                        <span class="d-flex">
                                                            <p class="me-10" id="currency-simbol">${currency_symbol}</p>
                                                            <p id="two-single-taxValue-${index}" class="text-end ms-auto"></p>
                                                        </span>
                                                    </td>
                                                </tr>`;
                                            $(new_html).insertAfter(`table#two-table-total tbody tr:nth-child(1)`);
                                        }
                                    });
                                }, 500));
                            }, 500));

                            initSelect2SearchPaginationData(`two-currency-inputForm`, `{{ route('admin.select.currency') }}`, {
                                id: 'id',
                                text: 'nama'
                            })

                            $('#two-currency-inputForm').change(function(e) {
                                e.preventDefault();
                                $.ajax({
                                    type: "get",
                                    url: `{{ route('admin.currency.detail') }}/${this.value}`,
                                    success: function({
                                        data
                                    }) {
                                        currency_symbol = data.simbol

                                        if (data.is_local) {
                                            $('#two-exchangerate-inputForm').val(1);
                                            $('#two-exchangerate-inputForm').attr('readonly', 'readonly');
                                        } else {
                                            $('#two-exchangerate-inputForm').removeAttr('readonly');
                                            $('#two-exchangerate-inputForm').attr('readonly', false);
                                        }
                                        currency_symbol = data.simbol;
                                        updateCurrencySymbol();
                                    }
                                });
                            });
                        };

                        const calculateVendorTwo = () => {
                            let total_quantity = QTY_LIST.reduce((a, b, currentIndex) => {
                                return (AMOUNT_DO_LIST[currentIndex] * parseInt(b)) + parseInt(a)
                            }, 0);

                            let subtotal = total_quantity * thousandToFloat($('#two-price-inputForm').val());
                            let total = subtotal;

                            VENDOR_TWO_TAX_LIST_VALUE.map((tax, index) => {
                                $(`#two-single-taxValue-${index}`).html(formatRupiahWithDecimal(subtotal * tax.value));
                                total += subtotal * tax.value;
                            });

                            $('#two-display-subtotal').html(formatRupiahWithDecimal(subtotal));
                            $('#two-display-total').html(formatRupiahWithDecimal(total));
                        };

                        initItemOne()
                    };

                    initializeDouleHandlinng();
                };

                handleFirstCard();
            });
        </script>
    @endcan
    <script>
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#purchase-menu');
        sidebarActive('#purchasse');
    </script>
@endsection
