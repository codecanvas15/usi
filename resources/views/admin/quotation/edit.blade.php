@extends('layouts.admin.layout.index')

@php
    $main = 'quotation';
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
                    <li class="breadcrumb-item active">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($main) }}</a>
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
    @can("edit $main")
        @include('components.validate-error')
        <form action="{{ !$model ? route("admin.$main.store") : route("admin.$main.update", $model) }}" method="post">
            @csrf
            @if ($model)
                @method('PUT')
            @endif
            <form action="{{ route("admin.$main.update", $model) }}" method="post">
                @csrf
                @method('PUT')
                <x-card-data-table title="{{ 'Update ' . $main }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input class="datepicker-input" id="date" name="date-preview" label="tanggal" value="{{ $model->date ?? \Carbon\Carbon::today()->format('d-m-Y') }}" onchange="checkClosingPeriod($(this))" disabled />
                                    <input type="hidden" name="date" value="{{ $model->date }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <x-input type="text" id="kode" name="kode" value="{{ $model->code }}" disabled />
                            </div>
                            <div class="col-md-4">
                                <x-select name="customer_id" id="customer_id" label="customer" required>
                                    <option value="{{ $model->customer_id }}">{{ $model->customer?->nama }}</option>
                                </x-select>
                            </div>
                        </div>
                        <div id="currency-data">

                        </div>
                    </x-slot>
                </x-card-data-table>

                <x-card-data-table title="{{ 'Trading Item' }}">
                    <x-slot name="header_content">

                    </x-slot>
                    @foreach ($model->quotationItems as $item)
                        @if ($item->type == 'main')
                            <x-slot name="table_content">
                                <input type="hidden" name="id_main" id="main_id" value="{{ $item->id }}">
                                <div class="row mt-10">
                                    <div class="col-md-3">
                                        <x-select name="item_id" id="item_id" label="item" required>
                                            <option value="{{ $item->item_id }}" selected>{{ $item->item?->nama }}</option>
                                        </x-select>
                                    </div>
                                    <div class="col-md-3">
                                        <x-input type="text" id="price_id" name="price" class="text-end commas-form" lable="Price" value="{{ old('price') ?? formatNumber($item->price) }}" required />
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="text" id="jumlah" name="quantity" class="text-end" label="jumlah" value="{{ formatNumber($item->quantity) }}" required autofucus />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <x-select name="tax_id_[]" id="tax_id" label="Pajak" multiple>
                                            @foreach ($item->itemTax as $tax)
                                                <option value="{{ $tax->tax_id }}" selected>
                                                    {{ $tax->tax->name }}</option>
                                            @endforeach
                                        </x-select>
                                        <div id="tax_list">

                                        </div>
                                    </div>
                                </div>

                            </x-slot>
                        @endif
                    @endforeach
                </x-card-data-table>

                <x-card-data-table title="Aditional Items">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        <div id="additional-from-db"></div>
                        @foreach ($model->quotationItems as $item)
                            @if ($item->type == 'additional')
                                <input type="hidden" name="id_add[]" value="{{ $item->id }}">
                                <div class="row align-items-end" id="additional-item-{{ $item->id + 1000 }}-default">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-select name="quotation_add_on_type_id[]" id="additional-type-{{ $item->id + 1000 }}-default" label="Type" value="{{ $item->item_type }}">
                                                <option value="">Pilih Item</option>

                                                <option value="general" @if ($item->item_type == 'general') selected @endif>
                                                    General</option>

                                                <option value="service" @if ($item->item_type == 'service') selected @endif>
                                                    Service</option>

                                                <option value="transport" @if ($item->item_type == 'transport') selected @endif>
                                                    Transport</option>
                                            </x-select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            @if ($item->item_type)
                                                <x-select name="additional_item[]" id="additional-item-id-default{{ $item->id + 1000 }}" label="item">
                                                    <option value="{{ $item->item_id }}">{{ $item->item->nama }}</option>
                                                </x-select>
                                            @else
                                                <x-select name="additional_item[]" id="additional-item-id-default{{ $item->id + 1000 }}" label="item" disabled>
                                                    <option value="">{{ $item->item->nama }}</option>
                                                </x-select>
                                            @endif

                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            @if ($item->item_type)
                                                <x-input type="text" name="additional_price[]" label="harga" id="additional-harga-deafult{{ $item->id + 1000 }}" value="{{ formatNumber($item->price) }}" class="text-end commas-form" />
                                            @else
                                                <x-input type="text" name="additional_price[]" label="harga" id="additional-harga-deafult{{ $item->id + 1000 }}" value="{{ formatNumber($item->price) }}" class="text-end commas-form" disabled />
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            @if ($item->item_type)
                                                <x-input type="text" name="additional_quantity[]" label="Qty" id="additional-quantity-default{{ $item->id + 1000 }}" class="text-end commas-form" value="{{ formatNumber($item->quantity) }}" />
                                            @else
                                                <x-input type="text" name="additional_quantity[]" label="Qty" id="additional-quantity-default{{ $item->id + 1000 }}" class="text-end commas-form" value="{{ formatNumber($item->quantity) }}" disabled />
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            @if ($item->item_type)
                                                <x-select name="additional_tax_id[]" id="additional-tax-id-{{ $item->id + 1000 }}" label="Tax" multiple>
                                                    @if (!is_null($item->itemTax))
                                                        @foreach ($item->itemTax as $tax)
                                                            <option value="{{ $tax->tax_id }}" selected>
                                                                {{ $tax->tax->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </x-select>
                                            @else
                                                <x-select name="additional_tax_id[]" id="additional-tax-id-{{ $item->id + 1000 }}" label="Tax" multiple disabled>
                                                    @if (!is_null($item->itemTax))
                                                        <option value="{{ $item->itemTax->tax_id }}" selected>
                                                            {{ $item->itemTax->tax->name }}</option>
                                                    @endif
                                                </x-select>
                                            @endif

                                            <input type="hidden" name="additional_tax_value[]" id="additional-tax-value-{{ $item->id + 1000 }}">
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                        <div id="additiional-list">

                        </div>

                    </x-slot>
                </x-card-data-table>

                <x-card-data-table title="Keterangan">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                <x-text-area id="keterangan" name="information" label="Keterangan">{{ $model->information }}
                                </x-text-area>
                            </div>
                        </div>

                    </x-slot>
                </x-card-data-table>

                <x-card-data-table>
                    <x-slot name="table_content">
                        <div class="mt-30">

                            @foreach ($model->quotationItems as $item)
                                @if ($item->type == 'main')
                                    <x-table theadColor='danger' id="table-total">
                                        <x-slot name="table_head">
                                            <th>Item</th>
                                            <th>Harga</th>
                                            <th>Qty</th>
                                            <th>-</th>
                                        </x-slot>
                                        <x-slot name="table_body">
                                            <tr>
                                                <td id="item-name">{{ $item->item?->nama }}</td>
                                                <td>
                                                    <span class="d-flex">
                                                        <p class="me-10 currency-simbol"></p>
                                                        <p class="fw-bold" id="display-harga">
                                                            {{ formatNumber($item->price) }}</p>
                                                    </span>
                                                </td>
                                                <td>
                                                    <p id="total-item-liter">
                                                        {{ formatNumber($item->quantity) }}
                                                    </p>
                                                    <p></p>
                                                    <!--- for layout dont remove --->
                                                </td>
                                                <td>
                                                    <span class="d-flex justify-content-between">
                                                        <span class="me-10 currency-simbol">{{ '' }}</span>
                                                        <span class="fw-bold w-100 text-end" id="sub_total">Rp.
                                                            {{ formatNumber($item->sub_total) }}</span>
                                                    </span>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td colspan="3" class="fw-bold text-end">Total</td>
                                                <td>
                                                    <div class="align-self-end">
                                                        <span class="d-flex justify-content-between">
                                                            <p class="me-10 currency-simbol">{{ '' }}</p>
                                                            <p class="fw-bold text-end w-100" id="total">Rp.
                                                                {{ formatNumber($item->total) }}</p>
                                                        </span>
                                                    </div>
                                                </td>
                                            </tr>

                                        </x-slot>
                                    </x-table>
                                @endif
                            @endforeach

                            <div class="mt-30">
                                <h4 class="fw-bold">Additional item</h4>
                                <x-table theadColor='danger' id="calculate-general-additional">
                                    <x-slot name="table_head">
                                        <th>#</th>
                                        <th>Item</th>
                                        <th>Harga</th>
                                        <th>Jumlah</th>
                                        <th>DPP</th>
                                        <th>Tax</th>
                                        <th>Value</th>
                                        <th>Sub Total</th>
                                    </x-slot>
                                    <x-slot name="table_body">

                                    </x-slot>
                                    <x-slot name="table_foot">
                                        <tr>
                                            <td class="text-end" colspan="7">DPP</td>
                                            <td class="d-flex text-end">
                                                <p class="currency-simbol me-10 mb-0">{{ get_local_currency()->simbol }}
                                                </p>
                                                <h5 class="mb-0 ms-auto" id="additional-dpp-total">{{ formatNumber($model->additional_subtotal) }}</h5>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-end" colspan="7">Total Pajak</td>
                                            <td class="d-flex text-end">
                                                <p class="currency-simbol me-10 mb-0">{{ get_local_currency()->simbol }}
                                                </p>
                                                <h5 class="mb-0 ms-auto" id="additional-tax-total">0</h5>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-end" colspan="7">Total</td>
                                            <td class="bg-success text-white d-flex text-end">
                                                <p class="currency-simbol me-10 mb-0">{{ get_local_currency()->simbol }}
                                                </p>
                                                <h5 class="fw-bold mb-0 ms-auto" id="additiional-total">0</h5>
                                            </td>
                                        </tr>
                                    </x-slot>
                                </x-table>
                            </div>

                            <div class="row justify-content-end mt-20">
                                <div class="col-12 col-md-4">
                                    <x-table theadColor='danger' id="">
                                        <x-slot name="table_head">
                                            <th></th>
                                            <th></th>
                                        </x-slot>
                                        <x-slot name="table_body">
                                            <tr>
                                                <td>Trading Total</td>
                                                <td>
                                                    <div class="d-flex text-end">
                                                        <p class="currency-simbol me-10 mb-0">
                                                            {{ get_local_currency()->simbol }}</p>
                                                        <h5 class="mb-0 ms-auto" id="trading-item-total">0</h5>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Additional Item Total</td>
                                                <td>
                                                    <div class="d-flex text-end">
                                                        <p class="currency-simbol me-10 mb-0">
                                                            {{ get_local_currency()->simbol }}</p>
                                                        <h5 class="mb-0 ms-auto" id="additional-item-total">0</h5>
                                                    </div>
                                                </td>
                                            </tr>
                                        </x-slot>
                                        <x-slot name="table_foot">
                                            <tr>
                                                <td class="text-end">Total</td>
                                                <td class="bg-success text-white d-flex text-end">
                                                    <p class="currency-simbol me-10 mb-0">
                                                        {{ get_local_currency()->simbol }}
                                                    </p>
                                                    <h5 class="fw-bold mb-0 ms-auto" id="total-all">0</h5>
                                                </td>
                                            </tr>
                                        </x-slot>
                                    </x-table>
                                </div>
                            </div>
                        </div>
                    </x-slot>
                    <x-slot name="footer">
                        <div class="d-flex justify-content-end gap-3">
                            <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
                            <x-button type="submit" color="primary" label="Save data" />
                        </div>
                    </x-slot>
                </x-card-data-table>
            </form>
        @endcan
    @endsection

    @push('script')
        <script src="{{ asset('js/admin/select/itemSelect.js') }}"></script>
        <script src="{{ asset('js/form/select2search.js') }}"></script>
        <script src="{{ asset('js/helpers/helpers.js') }}"></script>

        <script>
            $(document).ready(function() {
                const updateCurrencySymbol = () => {
                    $('.currency-simbol').each(function() {
                        $(this).html(currency_symbol);
                    });
                }

                let currency_symbol = 'Rp';

                let price_data = []

                // * ============================ price, sub total, tax =======================

                let total = 0,
                    sub_total = 0,
                    tax = 0;

                let price_list = [],
                    sub_total_list = [],
                    tax_list = [],
                    tax_list_value = [];

                // additional calculattion variables
                let additional_tax_list_value = [],
                    additional_jumlah_list = [],
                    additional_price_list = [],
                    additional_total = 0,
                    additional_sub_total = 0,
                    additional_tax_total = 0,
                    additional_sub_total_list = [];

                let valueTax = [];
                let taxValue = [];


                // get item price when item select form updated or selected
                $('#item_id').change(function(e) {
                    e.preventDefault();
                    let value_item = $('#item_id').val()
                });

                // Additional Item from db
                $('#price_id').on('load keyup focus change', function(e) {
                    if (this.value) {
                        price_list[0] = $(this).val()
                        $('#display-harga').html(`${numberWithCommas(price_list[0])}`);

                        let value = thousandToFloat($('#jumlah').val());
                        sub_total_list[0] = thousandToFloat($(this).val()) * value;
                        updateValuePrices();
                        calculateTotal()
                    }
                })

                $('#price_id').on('load blur', function(e) {
                    if (tax_list.length > 0) {
                        tax_function()
                    }
                })


                const calculateAllAdditional = () => {
                    let total_all_additional = 0;
                    additional_sub_total = additional_sub_total_list.reduce((a, b) => a + b, 0);

                    additional_tax_total = 0;
                    additional_sub_total_list.map((sub_total_data, sub_total_index) => {
                        total_all_additional += sub_total_data;

                        let total_single = sub_total_data;
                        $(`#additional-sub_total_${sub_total_index}`).html(numberWithCommas(
                            total_single));

                        additional_tax_list_value[sub_total_index].map((tax, tax_index) => {
                            total_all_additional += sub_total_data * tax;

                            additional_tax_total += sub_total_data * tax;
                            total_single += sub_total_data * tax;
                            $(`#tax-${sub_total_index}-${tax_index}`).html(numberWithCommas(
                                sub_total_data * tax));
                        })

                        $(`#additiona-total-item-${sub_total_index}`).html(numberWithCommas(
                            total_single));
                    });

                    additional_total = total_all_additional;
                    $(`#additiional-total`).html(numberWithCommas(total_all_additional));
                    calculateTotalAdditionalAndTradingItem()
                }

                @foreach ($model->quotationItems as $item)
                    initSelect2Search(`additional-tax-id-{{ $item->id + 100 }}`,
                        `{{ route('admin.select.tax') }}`, {
                            id: "id",
                            text: "name"
                        });

                    inititemSelect(`additional-item-id-default{{ $item->id }}`, this.value);

                    $(`#additional-tax-id-{{ $item->id + 100 }}`).on('change', function(e) {

                        let value = '';
                        $(this).val().map((res, i) => {
                            value += (i === 0) ? `${res}` : `,${res}`
                        })

                        $(`#additional-tax-value-{{ $item->id + 1000 }}`).val(value);
                    })

                    if (typeof $(`#additional-tax-id-{{ $item->id + 100 }}`).val() !== 'undefined') {
                        let value = '';
                        $(`#additional-tax-id-{{ $item->id + 100 }}`).val().map((res, i) => {
                            value += (i === 0) ? `${res}` : `,${res}`
                        })

                        $(`#additional-tax-value-{{ $item->id + 1000 }}`).val(value);
                    }

                    @if ($item->type === 'main')
                        sub_total_list[0] = parseFloat('{{ $item->sub_total }}')
                        total = parseFloat('{{ $item->total }}')
                        sub_total = parseFloat('{{ $item->sub_total }}')
                        $tax_value_data = []
                        @foreach ($item->itemTax as $tax)
                            $tax_value_data.push({{ $tax->value }})
                        @endforeach
                        tax_list_value = $tax_value_data;
                    @elseif ($item->type === 'additional')
                        $('#calculate-general-additional tbody').append(`
                            <tr id="additional-resume-{{ $item->id + 1000 }}">
                                <th></th>
                                <td>
                                    <span id="additiona-item-name-{{ $item->id + 1000 }}">{{ $item->item?->nama }}</span
                                </td>
                                <td>
                                    <div class="d-flex">
                                        <p class="currency-simbol me-10 mb-0">${currency_symbol}</p>
                                        <p class="mb-0 ms-auto" id="additiona-item-price-{{ $item->id + 1000 }}">{{ formatNumber($item->price) }}</p>
                                    </div
                                </td>
                                <td id="additional-jumlah-display-{{ $item->id + 1000 }}">{{ $item->quantity }}</td>
                                <td>
                                    <div class="d-flex">
                                        <p class="currency-simbol me-10 mb-0">${currency_symbol}</p>
                                        <p class="mb-0 ms-auto" id="additional-sub_total_{{ $item->id + 1000 }}">{{ formatNumber($model->additional_subtotal) }}</p>
                                    </div
                                </td>
                                <td id="additional_tax_data_detail_{{ $item->id + 1000 }}">
                                    @forelse($item->itemTax as $tax)
                                        <p>
                                            {{ $tax->tax?->name }} - {{ $tax->tax?->value * 100 }}%
                                        </p>
                                    @empty
                                    -
                                    @endforelse
                                </td>
                                <td id="additional_tax_value_detail_{{ $item->id + 1000 }}">
                                    @forelse($item->itemTax as $tax)
                                        <div class="d-flex">
                                            <p class="currency-simbol me-10 mb-0">${currency_symbol}</p>
                                            {{ formatNumber($tax->tax?->value * $item->sub_total) }}
                                        </div>
                                    @empty
                                    -
                                    @endforelse
                                </td>
                                <td>
                                    <div class="d-flex text-end">
                                        <p class="currency-simbol me-10 mb-0">${currency_symbol}</p>
                                        <p class="mb-0 ms-auto" id="additiona-total-item-{{ $item->id + 1000 }}">{{ formatNumber($item->sub_total) }}</p>
                                    </div>
                                </td>
                            </tr>
                        `);

                        valueTax[{{ $item->id }}] = [];
                        taxValue[{{ $item->id }}] = '';
                        @foreach ($item->itemTax as $tax)
                            additional_tax_total = ({{ $tax->total }} + additional_tax_total)
                            valueTax[{{ $item->id }}].push({{ $tax->tax?->value }})
                            taxValue[{{ $item->id }}] += `{{ $loop->index == 0 ? $tax->tax_id : ',' . $tax->tax_id }}`
                        @endforeach
                        $(`#additional-tax-value-{{ $item->id + 1000 }}`).val(taxValue[{{ $item->id }}]);

                        additional_sub_total_list[{{ $item->id + 1000 }}] = {{ $item->sub_total }}
                        additional_tax_list_value[{{ $item->id + 1000 }}] = valueTax[{{ $item->id }}]
                        initSelect2Search(`additional-item-id-default{{ $item->id + 1000 }}`,
                            `{{ route('admin.select.item') }}`, {
                                id: "id",
                                text: "name"
                            });


                        $(`#additional-item-id-default{{ $item->id + 1000 }}`).select2();
                        $(`#additional-item-id-default{{ $item->id + 1000 }}`).select2();
                        $(`#additional-jumlah`).select2();
                        $(`#additional-tax-id-{{ $item->id + 1000 }}`).select2();

                        if ($(`#additional-type-{{ $item->id + 1000 }}-default`).val()) {
                            $(`#additional-item-id-default{{ $item->id + 1000 }}`).removeAttr('disabled');
                            $(`#additional-jumlah`).removeAttr('disabled');
                            $(`#additional-tax-id-{{ $item->id + 1000 }}`).removeAttr('disabled');
                            $(`#additional-quantity-default{{ $item->id + 1000 }}`).removeAttr('disabled');
                            $(`#additional-harga-deafult{{ $item->id + 1000 }}`).removeAttr('disabled');
                            $(`#jumlah`).removeAttr('disabled');


                            inititemSelect(`additional-item-id-default{{ $item->id + 1000 }}`, `${$(`#additional-type-{{ $item->id + 1000 }}-default`).val()}`);

                            initSelect2Search(`additional-tax-id-{{ $item->id + 1000 }}`,
                                `{{ route('admin.select.tax') }}`, {
                                    id: "id",
                                    text: "name"
                                });

                            $(`#additional-item-id-default{{ $item->id + 1000 }}`).change(function(e) {
                                e.preventDefault();
                                $(`#additional-jumlah-display-{{ $item->id + 1000 }}`).html(
                                    numberWithCommas($('#jumlah').val()));

                                if ($(this).val()) {
                                    $(`#additiona-item-name-{{ $item->id + 1000 }}`).html($(
                                            `#additional-item-id-default{{ $item->id + 1000 }}`)
                                        .select2('data')[0].text);
                                    $.ajax({
                                        type: "get",
                                        url: `{{ route('admin.item.price-latest') }}/${this.value}`,
                                        success: function({
                                            data
                                        }) {
                                            $(`#additional-harga-default{{ $item->id + 1000 }}`)
                                                .val(numberWithDot(
                                                    decimalFormatterWithOuNumberWithCommas(
                                                        data.harga_jual)));
                                            $(`#additional-harga-default{{ $item->id + 1000 }}`)
                                                .trigger('focus');
                                        }
                                    });
                                } else {
                                    $(`#additiona-item-name-{{ $item->id + 1000 }}`).html();
                                    $(`#additional-harga-default{{ $item->id + 1000 }}`).val(0);
                                    $(`#price-{{ $item->id + 1000 }}`).trigger('focus');
                                }
                            });

                            const TaxOnChange{{ $item->id }} = () => {
                                additional_tax_list_value[{{ $item->id + 1000 }}] = [];
                                tax_list = [];
                                [...document.getElementById(`additional-tax-id-{{ $item->id + 1000 }}`)
                                    .options
                                ].map((option, selected_index) => {
                                    if (option.selected) {
                                        tax_list[selected_index] = option.value;
                                    }
                                });

                                tax_list_value[{{ $item->id + 1000 }}] = tax_list;
                                $(`#additional-tax-value-{{ $item->id + 1000 }}`).val(tax_list
                                    .toString());

                                $(`#additional_tax_data_detail_{{ $item->id + 1000 }}`).html('');
                                $(`#additional_tax_value_detail_{{ $item->id + 1000 }}`).html('')
                                tax_list.map((tax, tax_index) => {
                                    $.ajax({
                                        type: "get",
                                        url: "{{ route('admin.tax.detail') }}/" +
                                            tax,
                                        success: ({
                                            data
                                        }) => {
                                            additional_tax_list_value[{{ $item->id + 1000 }}][tax_index] =
                                                data.value;
                                            let new_html = `
                                                    <p>
                                                        <span>${data.name} - ${(data.value * 100).toFixed(2)}%</span>
                                                    </p>`;
                                            $(`#additional_tax_data_detail_{{ $item->id + 1000 }}`)
                                                .append(new_html);
                                            $(`#additional_tax_value_detail_{{ $item->id + 1000 }}`)
                                                .append(`
                                                <p>
                                                    <span class="currency-simbol me-10">${currency_symbol}</span>
                                                    <span class="fw-700" id="tax-${tax_index}-{{ $item->id + 1000 }}">${numberWithCommas(additional_sub_total_list[{{ $item->id + 1000 }}] * data.value)}</span>
                                                </p>
                                            `);

                                            calculateSingleAdditional{{ $item->id }}();
                                        }
                                    });
                                });

                                calculateSingleAdditional{{ $item->id }}();

                            };

                            $(`#additional-tax-id-{{ $item->id + 1000 }}`).change(function(e) {
                                tax_list[{{ $item->id + 1000 }}] = this.value ?? null;
                                TaxOnChange{{ $item->id }}();
                            });

                        } else {
                            $(`#additional-item-id-default{{ $item->id + 1000 }}`).attr('disabled');
                            $(`#additional-jumlah`).attr('disabled');
                            $(`#additional-tax-id-{{ $item->id + 1000 }}`).attr('disabled');
                            $(`#additional-quantity-default{{ $item->id + 1000 }}`).attr('disabled');
                            $(`#additional-harga-deafult{{ $item->id + 1000 }}`).attr('disabled', 'disabled');

                            $(`#additional-item-id-default{{ $item->id + 1000 }}`).select2('destroy');
                            $(`#additional-tax-id-{{ $item->id + 1000 }}`).select2('destroy');
                            $(`#additional-tax-id-{{ $item->id + 1000 }}`).select2();
                        }

                        const calculateSingleAdditional{{ $item->id }} = () => {
                            let single_total = additional_sub_total_list[{{ $item->id + 1000 }}];
                            $(`#additionaL-sub_total_{{ $item->id + 1000 }}`).val(single_total);

                            additional_tax_list_value[{{ $item->id + 1000 }}].map((value, index) => {
                                single_total += additional_sub_total_list[{{ $item->id + 1000 }}] * value;
                            })
                            $(`#additional-total-item-{{ $item->id + 1000 }}`).html(numberWithCommas(single_total));

                            calculateAllAdditional();
                        };

                        $(`#additional-harga-deafult{{ $item->id + 1000 }}`).on('change keyup focus', function(e) {
                            $(`#additiona-item-price-{{ $item->id + 1000 }}`).html(numberWithCommas($(this)
                                .val() ?? 0));

                            let amount = thousandToFloat($(`#additional-quantity-default{{ $item->id + 1000 }}`).val() ?? 0);
                            let price = thousandToFloat($(this).val() ?? 0);

                            if (price) {
                                additional_sub_total_list[{{ $item->id + 1000 }}] = amount * price;
                            } else {
                                additional_sub_total_list[{{ $item->id + 1000 }}] = 0;
                            }

                            calculateSingleAdditional{{ $item->id }}();
                        });

                        $(`#additional-quantity-default{{ $item->id + 1000 }}`).on('change keyup focus', function(e) {
                            $(`#additional-jumlah-display-{{ $item->id + 1000 }}`).html(numberWithCommas(this
                                .value));

                            let amount = thousandToFloat($(this).val());
                            let price = thousandToFloat($(`#additional-harga-{{ $item->id + 1000 }}`).val());

                            if (amount) {
                                additional_sub_total_list[{{ $item->id + 1000 }}] = amount * price;
                            } else {
                                additional_sub_total_list[{{ $item->id + 1000 }}] = 0;
                            }
                            calculateSingleAdditional{{ $item->id }}();
                        });
                    @endif
                @endforeach

                additional_sub_total = {{ $model->additional_subtotal }}

                additional_total = {{ $model->additional_total }}

                $('#additiional-total').html(`${additional_total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}`)


                $('#date').change(function(e) {
                    $.ajax({
                        type: "get",
                        url: `{{ route('admin.quotation.code') }}?date=${e.target.value}`,
                        success: ({
                            data
                        }) => {
                            $('#kode').val(data)
                            $('#code').val(data)
                        }
                    })
                })

                $.ajax({
                    type: "get",
                    url: "{{ route('admin.currency.detail') }}/" + ($('#currency_id').val() ?? {{ $model->currency_id }}),
                    success: function({
                        data
                    }) {
                        currency_symbol = data.simbol;

                        if (data.is_local) {
                            $('#exchange_rate').val(1);
                            $('#exchange_rate').trigger('change');
                            $('#exchange_rate').attr('readonly', true);
                        } else {
                            $('#exchange_rate').val('');
                            $('#exchange_rate').trigger('change');
                            $('#exchange_rate').attr('readonly', false);
                        }
                        updateCurrencySymbol();
                        calculateTotal();
                    }
                });

                $('.money').mask('000.000.000.000.000,00', {
                    reverse: true
                });

                checkClosingPeriod($('#tanggal_data'));

                const calculateTotalAdditionalAndTradingItem = () => {
                    $('#additional-dpp-total').html(numberWithCommas(additional_sub_total))
                    $('#additional-tax-total').html(numberWithCommas(additional_tax_total))
                    $('#additional-item-total').html(numberWithCommas(additional_total));
                    $('#total').html(numberWithCommas(total));
                    $('#trading-item-total').html(numberWithCommas(total));
                    $('#total-all').html(numberWithCommas(total + additional_total));
                };

                const numberWithCommas = (x) => {
                    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                }

                const displayCurrency = () => {
                    let {
                        id,
                        nama,
                        simbol,
                        is_local
                    } = currency;

                    $('#currency-data').append(`
                    <div class="row mt-10">
                        <div class="col-md-4">
                            <x-select name="currency_id" id="currency_id" label="Currency" value="" required>
                                <option value="{{ $model->currency_id }}" selected>{{ $model->currency?->kode . ' - ' . $model->currency?->nama }}</option>
                            </x-select>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" name="exchange_rate" class="commas-form" label="kurs" id="exchange_rate_trading" value="{{ formatNumber($model->exchange_rate) }}" required />
                            </div>
                        </div>
                    </div>
                `);

                    currency_symbol = simbol;

                    if (is_local) {
                        $('#exchange_rate_trading').attr('readonly', true);
                    }

                    initSelect2Search('currency_id', "{{ route('admin.select.currency') }}", {
                        id: "id",
                        text: "kode,nama,negara"
                    });

                    $('#currency_id').change(function(e) {
                        e.preventDefault();
                        $.ajax({
                            type: "get",
                            url: `{{ route('admin.currency.detail') }}/${this.value}`,
                            success: function({
                                data
                            }) {
                                if (data.is_local) {
                                    $('#exchange_rate').val(1);
                                    $('#exchange_rate').attr('readonly', 'readonly');
                                } else {
                                    $('#exchange_rate').removeAttr('readonly');
                                    $('#exchange_rate').attr('readonly', false);
                                }

                                currency_symbol = data.simbol;
                                updateCurrencySymbol();
                            }
                        });
                    });
                };

                displayCurrency()

                const updateValuePrices = () => {
                    $('#tax_data').html(tax);
                    if (tax != 0) {
                        $('#tax_total').html(numberWithCommas(sub_total * (tax / 100)));
                    }

                    $('#total').html(numberWithCommas(total));
                    $('#total_harga').html(numberWithCommas(total));
                }

                const calculateTotal = () => {
                    let with_tax = 0;
                    total = 0;
                    if (sub_total_list.length > 0) {
                        sub_total = sub_total_list.reduce((a, b) => a + b);
                    } else {
                        sub_total = 0;
                    }

                    $('#sub_total').html(numberWithCommas(sub_total));

                    let total_tax = 1;
                    if (tax_list_value.length > 0) {
                        total_tax = tax_list_value.reduce((a, b) => parseFloat(a) + parseFloat(b));

                        tax_list_value.map((data_tax, index) => {
                            $(`#tax-${index}`).html(numberWithCommas(sub_total * data_tax));
                        })

                    }

                    if (total_tax != 1) {
                        sub_total += sub_total * total_tax;
                    }

                    total = sub_total;
                    calculateTotalAdditionalAndTradingItem();
                    $('#total_harga').html(numberWithCommas(total));

                    updateCurrencySymbol();
                }

                calculateTotal()

                const tax_function = () => {
                    tax_list.map((tax, index) => {
                        $(`#tax-table-${index}`).remove();
                    });

                    tax_list = [];
                    tax_list_value = [];
                    let html = '';

                    [...document.getElementById('tax_id').options].map((option, index) => {
                        if (option.selected) {
                            tax_list.push(option.value);
                        }
                    });

                    $('.tax-main-table').remove();
                    tax_list.map((tax, index) => {
                        console.log(tax);
                        let num = 1;
                        html += `<input type="hidden" name="tax_id[]" value="${tax}"/>`;

                        setTimeout(() => {
                            $.ajax({
                                type: "get",
                                url: "{{ route('admin.tax.detail') }}/" + tax,
                                success: ({
                                    data
                                }) => {
                                    tax_list_value.push(data.value)
                                    let new_html = `
                                    <tr id="tax-table-${index}" class="tax-main-table">
                                        <td colspan="3" class="fw-bold text-end">${data.name} - ${(data.value * 100).toFixed(2)}%</td>
                                        <td>
                                            <span class="d-flex justify-content-between">
                                                <p class="me-10 currency-simbol">${currency_symbol}</p>
                                                <h6 id="tax-${index}" class="text-end w-100"></h6>
                                            </span>
                                        </td>
                                    </tr>`;
                                    $(new_html).insertAfter(
                                        `table#table-total tbody tr:nth-child(${num})`
                                    );
                                    if (sub_total_list[0]) {
                                        updateCurrencySymbol();
                                        calculateTotal();
                                        updateValuePrices();
                                    }

                                    num++;
                                }
                            });
                        }, 1000);
                    })

                    if (sub_total_list[0]) {
                        $('#tax_list').html(html);
                    }

                    calculateTotal();
                }

                tax_function();

                $('#tax_id').on('change click', function(e) {
                    tax_function();
                    if (sub_total_list[0]) {
                        calculateTotal();
                        updateValuePrices();
                    }
                })

                $('#jumlah').keyup(function(e) {
                    let value = thousandToFloat(this.value);
                    $('#total-item-liter').html(`${numberWithCommas(value)}`);

                    if (value != '') {
                        sub_total_list[0] = price_list[0] * value;
                    } else {
                        sub_total_list[0] = 0;
                    }

                    if (sub_total_list[0]) {
                        calculateTotal();
                        updateValuePrices();
                    }
                });

                $('#harga').keyup(function(e) {
                    price_list[0] = thousandToFloat(this.value);
                    $('#display-harga').html(`${numberWithCommas(price_list[0])}`);

                    let value = thousandToFloat($('#jumlah').val());
                    sub_total_list[0] = price_list[0] * value;
                    calculateTotal()
                    updateValuePrices();
                });
                // * ============================ price, sub total, tax =======================

                initSelect2Search('tax_id', "{{ route('admin.select.tax') }}", {
                    id: "id",
                    text: "name"
                });

                inititemSelect('item_id', 'trading')

                // get item price when item select form updated or selected
                $('#item_id').change(function(e) {
                    e.preventDefault();
                    updatePrice();

                    if (this.value) {
                        var data = $(this).select2('data');
                        $('#item-name').html(data[0].text);
                        return;
                    }

                    $('#item-name').html('-');
                });

                const getTermsOfPayment = () => {
                    $.ajax({
                        type: "get",
                        url: `{{ route('admin.select.customer-detail') }}/${$('#customer_id').val()}`,
                        success: function({
                            data
                        }) {
                            let customer_top = data.term_of_payment;
                            if (customer_top != "cash") {
                                customer_top += ` - ${data.top_days} hari`;
                            }
                            $('#term_of_payment').val(customer_top);
                        }
                    });
                }

                const updatePrice = () => {
                    $.ajax({
                        type: "get",
                        url: `{{ route('admin.select.select-with-period-and-sh-number-and-search-harga-jual') }}/${$('#item_id').val()}/${$('#sh_number_id').val()}/${$('#tanggal_data').val()}`,
                        success: function({
                            data
                        }) {
                            price_list[0] = data.harga_jual;
                            $('#harga').val(numberWithDot(decimalFormatterCommasWithOuNumberWithCommas(
                                data.harga_jual)));
                            $('#harga').trigger('keyup');
                        }
                    });
                };


                initSelect2Search('customer_id', "{{ route('admin.select.customer') }}", {
                    id: "id",
                    text: "nama"
                });

                $('#tanggal_data').change(function(e) {
                    e.preventDefault();
                    updatePrice();
                });

                $('#sh_number_id').change(function(e) {
                    e.preventDefault();
                    updatePrice();
                    $.ajax({
                        type: "get",
                        url: `{{ route('admin.sh-number.detail') }}/${this.value}`,
                        success: function({
                            data
                        }) {

                            data.sh_number_details.map((item, index) => {
                                if (item.type == 'Supply Point') {
                                    $('#supply_point').val(item.alamat);
                                } else if (item.type == 'Drop Point') {
                                    $('#drop_point').val(item.alamat);
                                }
                            });
                        }
                    });
                });

                $('#customer_id').change(function(e) {

                    updatePrice();
                    getTermsOfPayment();
                    $('#sh_number_id').removeAttr('disabled');
                    $('#sh_number_id').select2('close');

                    initSelect2Search('sh_number_id',
                        `{{ route('admin.select.customer.sh-numbers') }}/${this.value}`, {
                            id: "id",
                            text: "kode,supply_point,drop_point"
                        });

                    $('#drop_point').val('');
                    $('#supply_point').val('');
                });

                // additioan item ===========================================================================================
                const initAdditionalItem = () => {
                    let index = 0;


                    const calculateAllAdditional = () => {
                        let total_all_additional = 0;
                        additional_sub_total = additional_sub_total_list.reduce((a, b) => a + b, 0);

                        additional_tax_total = 0;
                        additional_sub_total_list.map((sub_total_data, sub_total_index) => {
                            total_all_additional += sub_total_data;

                            let total_single = sub_total_data;
                            $(`#additional-sub_total_${sub_total_index}`).html(numberWithCommas(
                                total_single));

                            additional_tax_list_value[sub_total_index].map((tax, tax_index) => {
                                total_all_additional += sub_total_data * tax;

                                additional_tax_total += sub_total_data * tax;
                                total_single += sub_total_data * tax;
                                $(`#tax-${sub_total_index}-${tax_index}`).html(numberWithCommas(
                                    sub_total_data * tax));
                            })

                            $(`#additiona-total-item-${sub_total_index}`).html(numberWithCommas(
                                total_single));
                        });

                        additional_total = total_all_additional;
                        $(`#additiional-total`).html(numberWithCommas(total_all_additional));
                        calculateTotalAdditionalAndTradingItem()
                    }

                    const deleteItem = (item_index) => {
                        $(`#additional-item-${item_index}`).remove();
                        $(`#additional-resume-${item_index}`).remove();

                        additional_jumlah_list[item_index] = 0;
                        additional_price_list[item_index] = 0;
                        additional_tax_list_value[item_index] = [];
                        additional_sub_total_list[item_index] = 0;
                    };

                    const addItem = (item_index) => {
                        let btn = '';
                        let tax_list = [];
                        additional_jumlah_list[item_index] = 0;
                        additional_price_list[item_index] = 0;
                        additional_tax_list_value[item_index] = [];
                        additional_sub_total_list[item_index] = 0;

                        if (item_index == 0) {
                            btn =
                                `<x-button color="primary" icon="plus" fontawesome size="sm" id="add-additional-item" />`;
                        } else {
                            btn =
                                `<x-button color="danger" icon="trash" fontawesome size="sm" id="delete-additional-item-${index}" />`;
                        }

                        let html = `
                                 <input type="hidden" name="id_add[]">

                                    <div class="row align-items-end" id="additional-item-${item_index}">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <x-select name="quotation_add_on_type_id[]" id="additional-type-${item_index}" label="Type">
                                                    <option value="">Pilih Item</option>
                                                    <option value="general">General</option>
                                                    <option value="service">Service</option>
                                                    <option value="transport">Transport</option>
                                                </x-select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-select name="additional_item[]" id="additional-item-id-${item_index}" label="item" disabled>

                                                </x-select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-input type="text" name="additional_price[]" label="harga" id="additional-harga-${item_index}" class="text-end commas-form" disabled />
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-input type="text" name="additional_quantity[]" label="Qty" id="additional-quantity-${item_index}" class="text-end commas-form" disabled />
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-select name="additional_tax_id[]" id="additional-tax-id-${item_index}" label="Tax" multiple disabled>

                                                </x-select>
                                                <input type="hidden" name="additional_tax_value[]" id="additional-tax-value-${item_index}" value="${$(`additional-tax-id-${item_index}`).val() ?? null}">
                                            </div>
                                        </div>
                                        <div class="col-md-1 row align-items-end">
                                            <div class="form-group">
                                                ${btn}
                                            </div>
                                        </div>
                                    </div>
                                `;

                        $('#additiional-list').append(html)



                        // * table =============================================================================================================================
                        $('#calculate-general-additional tbody').append(`
                        <tr id="additional-resume-${item_index}">
                            <th></th>
                            <td>
                                <span id="additiona-item-name-${item_index}">-</span
                            </td>
                            <td>
                                <div class="d-flex">
                                    <p class="currency-simbol me-10 mb-0">${currency_symbol}</p>
                                    <h5 class="mb-0 ms-auto" id="additiona-item-price-${item_index}">0</h5>
                                </div
                            </td>
                            <td id="additional-jumlah-display-${item_index}">0</td>
                            <td>
                                <div class="d-flex">
                                    <p class="currency-simbol me-10 mb-0">${currency_symbol}</p>
                                    <h5 class="mb-0 ms-auto" id="additional-sub_total_${item_index}">${additional_price_list[item_index] ?? 0}</h5>
                                </div
                            </td>
                            <td id="additional_tax_data_detail_${item_index}">-</td>
                            <td id="additional_tax_value_detail_${item_index}">-</td>
                            <td>
                                <div class="d-flex text-end">
                                    <p class="currency-simbol me-10 mb-0">${currency_symbol}</p>
                                    <h5 class="mb-0 ms-auto" id="additiona-total-item-${item_index}">${additional_sub_total_list[item_index]}</h5>
                                </div>
                            </td>
                        </tr>
                    `);


                        // * /table =============================================================================================================================

                        // delete item
                        if (item_index == 0) {
                            $('#add-additional-item').click(function(e) {
                                e.preventDefault();
                                addItem(++index);
                            });
                        } else {
                            $(`#delete-additional-item-${item_index}`).click(function(e) {
                                e.preventDefault();
                                deleteItem(item_index);
                            });
                        }

                        $(`#additional-type-${item_index}`).select2();
                        $(`#additional-item-id-${item_index}`).select2();
                        $(`#additional-jumlah`).select2();
                        $(`#additional-tax-id-${item_index}`).select2();

                        $(`#additional-type-${item_index}`).change(function(e) {
                            e.preventDefault();

                            if (this.value) {
                                $(`#additional-item-id-${item_index}`).removeAttr('disabled');
                                $(`#additional-jumlah`).removeAttr('disabled');
                                $(`#additional-tax-id-${item_index}`).removeAttr('disabled');
                                $(`#additional-quantity-${item_index}`).removeAttr('disabled');
                                $(`#additional-harga-${item_index}`).removeAttr('disabled');
                                $(`#jumlah`).removeAttr('disabled');

                                inititemSelect(`additional-item-id-${item_index}`, this.value);

                                initSelect2Search(`additional-tax-id-${item_index}`,
                                    `{{ route('admin.select.tax') }}`, {
                                        id: "id",
                                        text: "name"
                                    });

                                $(`#additional-item-id-${item_index}`).change(function(e) {
                                    e.preventDefault();
                                    $(`#additional-jumlah-display-${item_index}`).html(
                                        numberWithCommas($('#jumlah').val()));

                                    if ($(this).val()) {
                                        $(`#additiona-item-name-${item_index}`).html($(
                                                `#additional-item-id-${item_index}`)
                                            .select2('data')[0].text);
                                        $.ajax({
                                            type: "get",
                                            url: `{{ route('admin.item.price-latest') }}/${this.value}`,
                                            success: function({
                                                data
                                            }) {
                                                $(`#additional-harga-${item_index}`)
                                                    .val(numberWithDot(
                                                        decimalFormatterWithOuNumberWithCommas(
                                                            data.harga_jual)));
                                                $(`#additional-harga-${item_index}`)
                                                    .trigger('focus');
                                            }
                                        });
                                    } else {
                                        $(`#additiona-item-name-${item_index}`).html();
                                        $(`#additional-harga-${item_index}`).val(0);
                                        $(`#price-${index}`).trigger('focus');
                                    }
                                });

                                const TaxOnChange = () => {
                                    additional_tax_list_value[item_index] = [];
                                    tax_list = [];
                                    [...document.getElementById(`additional-tax-id-${item_index}`)
                                        .options
                                    ].map((option, selected_index) => {
                                        if (option.selected) {
                                            tax_list[selected_index] = option.value;
                                        }
                                    });

                                    tax_list_value[item_index] = tax_list;
                                    $(`#additional-tax-value-${item_index}`).val(tax_list
                                        .toString());

                                    $(`#additional_tax_data_detail_${item_index}`).html('');
                                    $(`#additional_tax_value_detail_${item_index}`).html('')
                                    tax_list.map((tax, tax_index) => {
                                        $.ajax({
                                            type: "get",
                                            url: "{{ route('admin.tax.detail') }}/" +
                                                tax,
                                            success: ({
                                                data
                                            }) => {
                                                additional_tax_list_value[
                                                        item_index][tax_index] =
                                                    data.value;
                                                let new_html = `
                                                    <p>
                                                        <span>${data.name} - ${data.value * 100}%</span>
                                                    </p>`;
                                                $(`#additional_tax_data_detail_${item_index}`)
                                                    .append(new_html);
                                                $(`#additional_tax_value_detail_${item_index}`)
                                                    .append(`
                                                <p>
                                                    <span class="currency-simbol me-10">${currency_symbol}</span>
                                                    <span class="fw-700" id="tax-${tax_index}-${item_index}">${numberWithCommas(additional_sub_total_list[item_index] * data.value)}</span>
                                                </p>
                                            `);

                                                calculateSingleAdditional();
                                            }
                                        });
                                    });

                                    calculateSingleAdditional();

                                };

                                $(`#additional-tax-id-${item_index}`).change(function(e) {
                                    tax_list[index] = this.value ?? null;
                                    TaxOnChange();
                                });

                            } else {
                                $(`#additional-item-id-${item_index}`).attr('disabled');
                                $(`#additional-jumlah`).attr('disabled');
                                $(`#additional-tax-id-${item_index}`).attr('disabled');
                                $(`#additional-quantity-${item_index}`).attr('disabled');
                                $(`#additional-harga-${item_index}`).attr('disabled', 'disabled');

                                $(`#additional-item-id-${item_index}`).select2('destroy');
                                $(`#additional-tax-id-${item_index}`).select2('destroy');
                                $(`#additional-tax-id-${item_index}`).select2();
                            }
                        });

                        const calculateSingleAdditional = () => {
                            let single_total = additional_sub_total_list[item_index];
                            $(`#additionaL-sub_total_${item_index}`).val(single_total);

                            additional_tax_list_value[item_index].map((value, index) => {
                                single_total += additional_sub_total_list[item_index] * value;
                            })
                            $(`#additional-total-item-${item_index}`).html(numberWithCommas(single_total));

                            calculateAllAdditional();
                        };

                        initCommasForm();

                        $(`#additional-harga-${item_index}`).on('change keyup focus', function(e) {
                            $(`#additiona-item-price-${item_index}`).html(numberWithCommas($(this)
                                .val() ?? 0));

                            let amount = thousandToFloat($(`#jumlah`).val() ?? 0);
                            let price = thousandToFloat($(this).val() ?? 0);

                            if (price) {
                                additional_sub_total_list[item_index] = amount * price;
                            } else {
                                additional_sub_total_list[item_index] = 0;
                            }

                            calculateSingleAdditional();
                        });

                        $(`#jumlah`).on('change keyup focus', function(e) {
                            $(`#additional-jumlah-display-${item_index}`).html(numberWithCommas(this
                                .value));

                            let amount = thousandToFloat($(this).val());
                            let price = thousandToFloat($(`#additional-harga-${item_index}`).val());

                            if (amount) {
                                additional_sub_total_list[item_index] = amount * price;
                            } else {
                                additional_sub_total_list[item_index] = 0;
                            }
                            calculateSingleAdditional();
                        });

                        index++;
                    };

                    addItem(index);
                };

                updateCurrencySymbol();
                initAdditionalItem();
                // additioan item ===========================================================================================
            });
        </script>
    @endpush
