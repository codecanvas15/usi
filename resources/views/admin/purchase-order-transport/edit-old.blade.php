@extends('layouts.admin.layout.index')

@php
    $main = 'purchase-transport';
@endphp

@section('title', Str::headline("Edit $main") . ' - ')

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
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.purchase-order-transport.show', $model) }}">{{ Str::headline('Purchase Transport') }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Edit ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can('edit purchase-transport')
        <form action="{{ route('admin.purchase-order-transport.update', $model) }}" id="po_transport" method="post">
            @csrf
            @method('PUT')
            <x-card-data-table id="loading-card">
                <x-slot name="table_content">
                    <h1 class="text-center">Loading....</h1>
                    {{-- @include('components.validate-error') --}}
                </x-slot>
            </x-card-data-table>

            <div id="main-section">

            </div>
        </form>
    @endcan
@endsection

@section('js')
    @can('edit purchase-transport')
        <script src="{{ asset('js/admin/select/saleOrderTradingForDelivery.js') }}"></script>
        <script src="{{ asset('js/admin/select/itemSelect.js') }}"></script>
        <script src="{{ asset('js/admin/select/warehouse.js') }}"></script>
        <script src="{{ asset('js/form/select2search.js') }}"></script>
        <script src="{{ asset('js/helpers/helpers.js') }}"></script>

        <script>
            $(document).ready(function() {

                let DATA_PURCHASE_TRANSPORT = null;

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

                let currency_symbol = '{{ $model->currency->simbol }}';

                const getDataPurchaseTransport = () => {
                    $.ajax({
                        type: "get",
                        url: "{{ route('admin.purchase-order-transport.data-for-edit', $model) }}",
                        success: function({
                            data
                        }) {
                            DATA_PURCHASE_TRANSPORT = data;

                            ITEM_ID = data.item_id;
                            QUANTITY_DELIVERY_ORDER = data.so_trading.so_trading_detail.sudah_dialokasikan - data.so_trading.so_trading_detail.sudah_dikirim;

                            $('#loading-card').hide(500);
                            $('#main-section').show(500);

                            $.ajax({
                                type: "post",
                                url: "{{ route('admin.purchase-order-transport.check-stock') }}",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    ware_house_id: data.ware_house.id,
                                    item_id: data.so_trading.so_trading_detail.item_id,
                                },
                                success: function({
                                    data
                                }) {
                                    STOCK_LEFT = data.stock;
                                    $('#stockLeft-selectForm').val(formatRupiahWithDecimal(data.stock));
                                }
                            });

                            displayDataPurchaseTransport();
                        }
                    });
                };

                const displayDataPurchaseTransport = () => {

                    let {
                        item,
                        so_trading,
                        vendor,
                        currency,
                        ware_house,
                        branch,
                        purchase_transport_details,
                        purchase_transport_taxes
                    } = DATA_PURCHASE_TRANSPORT;

                    const firstCard = () => {

                        let html = `
                            <x-card-data-table>
                                <x-slot name="table_content">

                                    <div class="row">
                                        <div class="col-md-3">
                                            <x-select name="vendor_id" id="vendor-SelectForm" label="vendor" required>
                                                <option value="${vendor.id}" selected>${vendor.nama}</option>
                                            </x-select>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-input class="datepicker-input" id="target-inputForm" name="target_delivery" label="target pengiriman" value="${localDate(DATA_PURCHASE_TRANSPORT.target_delivery)}" onchange="checkClosingPeriod($(this))" required />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row border-primary border-top mt-20 pt-10">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-select name="so_trading_id" label="sale order" id="saleOrderSelect-selectForm" disabled required>
                                                    <option value="${so_trading.id}">${so_trading.nomor_so} - ${so_trading.customer.nama}</option>
                                                </x-select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-input name="" value="${so_trading.customer.nama}" label="Customer / date" id="customerDate-saleOrder-inputForm" required disabled/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-input name="" value="${so_trading.so_trading_detail.item.nama} - ${so_trading.so_trading_detail.item.kode}" label="Item" id="item-saleOrder-inputForm" required disabled/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-input name="" value="${so_trading.so_trading_detail.jumlah}" label="Qty" id="quantity-saleOrder-inputForm" required disabled/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-input name="" label="sh no." value="${so_trading.sh_number.kode}" id="shNumber-saleOrder-inputForm" required disabled/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-input name="" label="Supply" value="${so_trading.sh_number.sh_number_details.filter((value) => value.type == 'Supply Point')[0].alamat}" id="supplyPoint-saleOrder-inputForm" required disabled/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-input name="" label="drop" value="${so_trading.sh_number.sh_number_details.filter((value) => value.type == 'Drop Point')[0].alamat}" id="dropPoint-saleOrder-inputForm" required disabled/>
                                            </div>
                                        </div>
                                    </div>

                                </x-slot>
                            </x-card-data-table>
                        `;

                        $('#main-section').append(html);

                        initDatePicker();
                        initCommasForm();
                        initSelect2SearchPaginationData(`vendor-SelectForm`, `{{ route('admin.select.vendor') }}`, {
                            id: 'id',
                            text: 'nama'
                        });
                    };

                    const secondCard = () => {
                        let html = `
                            <x-card-data-table>
                                <x-slot name="table_content">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-select name="ware_house_id" label="gudang" id="wareHouse-selectForm" required>
                                                    <option value="${ware_house.id}" selected>${ware_house.nama}</option>
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
                        `;

                        $('#main-section').append(html);

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
                                    item_id: so_trading.so_trading_detail.item_id,
                                },
                                success: function({
                                    data
                                }) {
                                    STOCK_LEFT = data.stock;
                                    $('#stockLeft-selectForm').val(formatRupiahWithDecimal(data.stock));
                                }
                            });
                        });

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
                                            <x-input type="text" class="commas-form" id="quantity-deliveryOrderForm-${index}" name="quantity[]" helpers="" label="kuantitas kendaraan" required />
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

                        purchase_transport_details.map((purchase_transport_detail, purchase_transport_detail_index) => {
                            DATA_INDEX++;

                            let last_html = '';
                            if (purchase_transport_detail_index == 0) {
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
                                            <x-button color="danger" icon="trash" fontawesome id="delete-deliveryForm-btn-${purchase_transport_detail_index}" size="sm" />
                                        </div>
                                    </div>
                                `;
                            }

                            let html = `
                                <div class="row ${purchase_transport_detail_index == 0 ? "border-top border-primary mt-20 pt-20" : "mt-10"}" id="row-deliveryOrderForm-${purchase_transport_detail_index}">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <x-input type="text" value="${purchase_transport_detail.jumlah_do}" class="commas-form" id="amountDO-deliveryOrderForm-${purchase_transport_detail_index}" name="amount_do[]" label="jumlah kendaraan" required />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <x-input type="text" value="${purchase_transport_detail.jumlah}" class="commas-form" id="quantity-deliveryOrderForm-${purchase_transport_detail_index}" name="quantity[]" helpers="" label="kuantitas kendaraan" required />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <x-select name="vehicle_type[]" id="vehicle-type-${purchase_transport_detail_index}" label="jenis kendaraan" required class="form-select">
                                                <option value="">-- pilih --</option>
                                                <option ${purchase_transport_detail.vehicle_type == 'darat' ? 'selected' : ''} value="darat">Darat</option>
                                                <option ${purchase_transport_detail.vehicle_type == 'laut' ? 'selected' : ''} value="laut">Laut</option>
                                            </x-select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <x-input type="text" id="vehicle-info-${purchase_transport_detail_index}" name="vehicle_info[]"  label="informasi kendaraan" required value="${purchase_transport_detail.vehicle_info}" />
                                        </div>
                                    </div>
                                    ${last_html}
                                </div>
                            `;

                            AMOUNT_DO_LIST[purchase_transport_detail_index] = purchase_transport_detail.jumlah_do;
                            QTY_LIST[purchase_transport_detail_index] = purchase_transport_detail.jumlah;
                            $('#row-quantity').append(html);
                            handleEventAddDeliveryForm(purchase_transport_detail_index);
                            initCommasForm();

                            setTimeout(() => {
                                $(`#quantity-deliveryOrderForm-${purchase_transport_detail_index}`).trigger('keyup');
                            }, 500);
                        });

                        $('#wareHouse-selectForm').trigger('change');
                    };

                    const thirdCard = () => {
                        const initialize = () => {
                            addForm();
                            initializeFormThirdCard();
                            $('#recalculate-btn').click(function(e) {
                                e.preventDefault();
                                calculateFuction()
                            });
                        };

                        const addForm = () => {
                            $('#main-section').append(`
                                <x-card-data-table>
                                    <x-slot name="table_content">
                                        <div  id="supplier_data_form_transport">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <x-select name="item_id" id="item-inputForm" label="Item" required>
                                                        <option value="${ITEM_ID}" selected>${item.nama} - ${item.kode}</option>
                                                    </x-select>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <x-input type="text" name="price" value="${DATA_PURCHASE_TRANSPORT.harga}" id="price-inputForm" label="harga" class="commas-form text-end" required />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <x-select name="tax_id_" id="tax-inputForm" label="Tax" multiple>
                                                            ${purchase_transport_taxes.map((tax, index) => {
                                                                TAX_LIST_ID[index] = tax.tax.id;
                                                                TAX_LIST_VALUE[index] = tax.tax.value;

                                                                return `<option value="${tax.tax.id}" selected>${tax.tax.name}</option>`
                                                            }).join('')}
                                                        </x-select>
                                                    </div>
                                                    <div id="tax_list">

                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <x-select name="currency_id" id="currency-inputForm" label="Currency" required>
                                                            <option value="${currency.id}" selected>${currency.nama}</option>
                                                        </x-select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <x-input name="exchange_rate" value="${DATA_PURCHASE_TRANSPORT.exchange_rate}" id="exchangerate-inputForm" value="1" label="kurs" required readonly />
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

                            $('#price-inputForm').trigger('keyup');
                            $('#item-inputForm').trigger('change');
                            $('#tax-inputForm').trigger('change');
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

                    const initDisplayData = () => {
                        firstCard();
                        secondCard();
                        thirdCard();
                    };

                    initDisplayData();
                };

                const init = () => {
                    $('#main-section').hide();

                    getDataPurchaseTransport();
                };

                init();
            });
        </script>
    @endcan

    <script>
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#purchase-menu');
        sidebarActive('#purchasse');
    </script>

@endsection
