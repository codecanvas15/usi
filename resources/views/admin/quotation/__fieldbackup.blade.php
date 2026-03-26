<form action="{{ !$model ? route("admin.$main.store") : route("admin.$main.update", $model) }}" method="post">
    @csrf
    @if ($model)
        @method('PUT')
    @endif
    <div class="row">
        <div class="col-md-4">
            <x-select name="customer_id" id="customer_id" label="customer" value="{{ $model->customer ?? '' }}" required>

            </x-select>
        </div>
        <div class="col-md-4">
            <x-select name="item_id" id="item_id" label="item" value="{{ $model->item_id ?? '' }}" required>

            </x-select>
        </div>
        <div class="col-md-4">
            <x-select name="price_id" id="price_id" label="price" value="{{ $model->price_id ?? '' }}" helpers="harga jual" required>

            </x-select>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" id="jumlah_barang" name="jumlah_barang" value="{{ $model->jumlah_barang ?? '' }}" required autofucus />
            </div>
        </div>
        {{-- <input type="hidden" id="harga" name="harga" value="{{ $model->harga ?? '' }}"> --}}
    </div>

    <div class="mt-3">
        <h3>Add on</h3>
        <x-button link="#" color="info" icon="plus" size="sm" id="add-form" class="mb-3" />

        <div id="add-on-type">

        </div>

    </div>
    <div class="box-footer">
        <div class="d-flex justify-content-end gap-3">
            <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
            <x-button type="submit" color="primary" label="Save data" />
        </div>
    </div>
</form>

<option value=""></option>

@push('script')
    <script src="{{ asset('js/admin/select/itemSelect.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script>
        $(document).ready(function() {

            let form_add_on_type_count = 1;
            let data_add_on_type = [];

            $('#price_id').prop('disabled', true);

            // get item price when item select form updated or selected
            $('#item_id').change(function(e) {
                $('#price_id').prop('disabled', false)
                e.preventDefault();
                let value_item = $('#item_id').val()
                initSelect2Search('price_id', `{{ route('admin.select.price') }}/${value_item}`, {
                    id: "id",
                    text: "harga_jual"
                });
            });

            inititemSelect('item_id')

            initSelect2Search('customer_id', "{{ route('admin.select.customer') }}", {
                id: "id",
                text: "nama"
            });

            $('#price_id').change(function(e) {
                e.preventDefault();
                $('#harga').val($('#price_id').find(":selected").text());
            });

            const displayFormAddOnType = () => {

                let btn = '';
                if (form_add_on_type_count != 1) {
                    btn = `<div class="col-md">
                                <div class="form-group">
                                    <x-button color="danger" label='delete' class="fw-bold" link="#" size="sm" id='btn-add-on-delete-${form_add_on_type_count}' dataTarget='${form_add_on_type_count}' />
                                </div>
                            </div>`;
                }

                let main = `
                    <div class="row align-items-end" id="form-add-on-type-${form_add_on_type_count}">
                        <div class="col-md-5">
                            <x-select name="quotation_add_on_type_id[]" id="quotation_add_on_type_id_${form_add_on_type_count}" label="Type" required>
                            </x-select>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <x-input type="text" id="value" name="value[]" label="Value" value="{{ $model->nama ?? '' }}" required/>
                            </div>
                        </div>
                        ${btn}
                    </div>`;
                $('#add-on-type').append(main);

                initSelect2Search(`quotation_add_on_type_id_${form_add_on_type_count}`, "{{ route('admin.select.quotation-add-on-type') }}", {
                    id: "id",
                    text: "nama"
                });

                $(`#btn-add-on-delete-${form_add_on_type_count}`).click(function(e) {
                    e.preventDefault();
                    let id = this.getAttribute('data-bs-target');
                    $(`#form-add-on-type-${id}`).remove();
                });

                form_add_on_type_count += 1;
                return;
            }

            const addFormAddOn = () => {
                displayFormAddOnType();
            }

            $('#btn-add-on-delete').on('click', (e) => {
                console.log('click');
                e.preventDefault();
                $(`#form-add-on-type-${$('#btn-add-on-delete').data('bs-target')}`).remove();
            });

            $('#add-form').click(function(e) {
                e.preventDefault();
                addFormAddOn();
            });

            addFormAddOn();
        });
    </script>
@endpush



<script>
    $(document).ready(function() {
        checkClosingPeriod($('date'))

        let form_add_on_type_count = 1;
        let data_add_on_type = [];


        // ##################  VARIBALE TAX  ##################
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



        $('#price_id').prop('disabled', true);

        // get item price when item select form updated or selected
        $('#item_id').change(function(e) {
            $('#price_id').prop('disabled', false)
            e.preventDefault();
            let value_item = $('#item_id').val()
            initSelect2Search('price_id', `{{ route('admin.select.price') }}/${value_item}`, {
                id: "id",
                text: "harga_jual,created_at"
            });
        });

        const updateValuePrices = () => {
            $('#tax_data').html(tax);
            if (tax != 0) {
                $('#tax_total').html(numberWithCommas(sub_total * (tax / 100)));
            }

            $('#total').html(numberWithCommas(total));
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
            $('#total').html(numberWithCommas(total));
        }

        inititemSelect('item_id')

        initSelect2Search('customer_id', "{{ route('admin.select.customer') }}", {
            id: "id",
            text: "nama"
        });

        $('#price_id').change(function(e) {
            e.preventDefault();
            $('#harga').val($('#price_id').find(":selected").text());
        });

        $('#date').change(function(e) {
            $.ajax({
                type: "get",
                url: `{{ route('admin.quotation.code') }}?date=${e.target.value}`,
                success: ({ data }) => {
                    console.log(data);
                    $('#code').val(data)
                }
            })
        })


        // Tax function for get list tax
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

            tax_list.map((tax, index) => {
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
                            <tr id="tax-table-${index}">
                                <td colspan="3" class="fw-bold text-end">${data.name} - ${data.value * 100}%</td>
                                <td>
                                    <span class="d-flex">
                                        <p class="me-10" id="currency-simbol">${currency_symbol}</p>
                                        <h5 id="tax-${index}" class="text-end w-100"></h5>
                                    </span>
                                </td>
                            </tr>`;
                            $(new_html).insertAfter(
                                `table#table-total tbody tr:nth-child(${num})`
                            );
                            updateCurrencySymbol();
                            calculateTotal();
                            updateValuePrices();

                            num++;
                        }
                    });
                }, 500);
            })

            $('#tax_list').html(html);
        }


        const displayFormAddOnType = () => {

            let btn = '';
            if (form_add_on_type_count != 1) {
                btn = `<div class="col-md">
                            <div class="form-group">
                                <x-button color="danger" label='delete' class="fw-bold" link="#" size="sm" id='btn-add-on-delete-${form_add_on_type_count}' dataTarget='${form_add_on_type_count}' />
                            </div>
                        </div>`;
            }

            let main = `
                <div class="row align-items-end" id="form-add-on-type-${form_add_on_type_count}">
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-select name="quotation_add_on_type_id[]" id="quotation_add_on_type_id_${form_add_on_type_count}" label="Type" required>
                                    <option value="">Pilih Item</option>
                                    <option value="general">General</option>
                                    <option value="service">Service</option>
                                    <option value="transport">Transport</option>
                            </x-select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-select name="additional_item[]" id="additional-item-id-${form_add_on_type_count}" label="item" disabled>

                            </x-select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" name="harga" label="harga" id="additional-harga-${form_add_on_type_count}" class="text-end commas-form" disabled />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-select name="additional_tax_id[]" id="additional-tax-id-${form_add_on_type_count}" label="Tax" multiple disabled>

                            </x-select>
                            <input type="hidden" name="additional_tax[]" id="additional-tax-value-${form_add_on_type_count}" value="">
                        </div>
                    </div>
                    <div class="col-md-1 row align-items-end">
                        <div class="form-group">
                            ${btn}
                        </div>
                    </div>
                </div>`;
            $('#add-on-type').append(main);

            // initSelect2Search(`quotation_add_on_type_id_${form_add_on_type_count}`,
            //     "{{ route('admin.select.quotation-add-on-type') }}", {
            //         id: "id",
            //         text: "nama"
            //     });

            $(`#quotation_add_on_type_id_${form_add_on_type_count}`).change(function(e) {
                    e.preventDefault();
                    console.log(form_add_on_type_count);
                    if (this.value) {
                        $(`#additional-item-id-${form_add_on_type_count}`).removeAttr('disabled');
                        $(`#additional-jumlah`).removeAttr('disabled');
                        $(`#additional-tax-id-${form_add_on_type_count}`).removeAttr('disabled');
                        $(`#additional-harga-${form_add_on_type_count}`).removeAttr('disabled');
                        $(`#jumlah`).removeAttr('disabled');

                        inititemSelect(`additional-item-id-${form_add_on_type_count}`, this.value);

                        initSelect2Search(`additional-tax-id-${form_add_on_type_count}`, `{{ route('admin.select.tax') }}`, {
                            id: "id",
                            text: "name"
                        });

                        $(`#additional-item-id-${form_add_on_type_count}`).change(function(e) {
                            e.preventDefault();
                            $(`#additional-jumlah-display-${form_add_on_type_count}`).html(numberWithCommas($('#jumlah').val()));

                            if ($(this).val()) {
                                $(`#additiona-item-name-${form_add_on_type_count}`).html($(`#additional-item-id-${form_add_on_type_count}`).select2('data')[0].text);
                                $.ajax({
                                    type: "get",
                                    url: `{{ route('admin.item.price-latest') }}/${this.value}`,
                                    success: function({
                                        data
                                    }) {
                                        $(`#additional-harga-${form_add_on_type_count}`).val(numberWithDot(decimalFormatterWithOuNumberWithCommas(data.harga_jual)));
                                        $(`#additional-harga-${form_add_on_type_count}`).trigger('focus');
                                    }
                                });
                            } else {
                                $(`#additiona-item-name-${form_add_on_type_count}`).html();
                                $(`#additional-harga-${form_add_on_type_count}`).val(0);
                                $(`#price-${index}`).trigger('focus');
                            }
                        });

                        const TaxOnChange = () => {
                            additional_tax_list_value[form_add_on_type_count] = [];
                            tax_list = [];
                            [...document.getElementById(`additional-tax-id-${form_add_on_type_count}`).options].map((option, selected_index) => {
                                if (option.selected) {
                                    tax_list[selected_index] = option.value;
                                }
                            });

                            tax_list_value[form_add_on_type_count] = tax_list;
                            $(`#additional-tax-value-${form_add_on_type_count}`).val(tax_list.toString());

                            $(`#additional_tax_data_detail_${form_add_on_type_count}`).html('');
                            $(`#additional_tax_value_detail_${form_add_on_type_count}`).html('')
                            tax_list.map((tax, tax_index) => {
                                $.ajax({
                                    type: "get",
                                    url: "{{ route('admin.tax.detail') }}/" + tax,
                                    success: ({
                                        data
                                    }) => {
                                        additional_tax_list_value[form_add_on_type_count][tax_index] = data.value;
                                        let new_html = `
                                                <p>
                                                    <span>${data.name} - ${data.value * 100}%</span>
                                                </p>`;
                                        $(`#additional_tax_data_detail_${form_add_on_type_count}`).append(new_html);
                                        $(`#additional_tax_value_detail_${form_add_on_type_count}`).append(`
                                            <p>
                                                <span id="currency-simbol" class="me-10">${currency_symbol}</span>
                                                <span class="fw-700" id="tax-${tax_index}-${form_add_on_type_count}">${numberWithCommas(additional_sub_total_list[form_add_on_type_count] * data.value)}</span>
                                            </p>
                                        `);

                                        calculateSingleAdditional();
                                    }
                                });
                            });

                            calculateSingleAdditional();

                        };

                        $(`#additional-tax-id-${form_add_on_type_count}`).change(function(e) {
                            tax_list[index] = this.value ?? null;
                            TaxOnChange();
                        });

                    } else {
                        $(`#additional-item-id-${form_add_on_type_count}`).attr('disabled');
                        $(`#additional-jumlah`).attr('disabled');
                        $(`#additional-tax-id-${form_add_on_type_count}`).attr('disabled');
                        $(`#additional-harga-${form_add_on_type_count}`).attr('disabled', 'disabled');

                        $(`#additional-item-id-${form_add_on_type_count}`).select2('destroy');
                        $(`#additional-tax-id-${form_add_on_type_count}`).select2('destroy');
                        $(`#additional-tax-id-${form_add_on_type_count}`).select2();
                    }
                });

                

            $(`#btn-add-on-delete-${form_add_on_type_count}`).click(function(e) {
                e.preventDefault();
                let id = this.getAttribute('data-bs-target');
                $(`#form-add-on-type-${id}`).remove();
            });

            $(`#additional-type-${form_add_on_type_count}`).select2();
            $(`#additional-item-id-${form_add_on_type_count}`).select2();
            $(`#additional-jumlah`).select2();
            $(`#additional-tax-id-${form_add_on_type_count}`).select2();

            $(`#additional-type-${form_add_on_type_count}`).change(function(e) {
                    e.preventDefault();

                    if (this.value) {
                        $(`#additional-item-id-${form_add_on_type_count}`).removeAttr('disabled');
                        $(`#additional-jumlah`).removeAttr('disabled');
                        $(`#additional-tax-id-${form_add_on_type_count}`).removeAttr('disabled');
                        $(`#additional-harga-${form_add_on_type_count}`).removeAttr('disabled');
                        $(`#jumlah`).removeAttr('disabled');

                        inititemSelect(`additional-item-id-${form_add_on_type_count}`, this.value);

                        initSelect2Search(`additional-tax-id-${form_add_on_type_count}`, `{{ route('admin.select.tax') }}`, {
                            id: "id",
                            text: "name"
                        });

                        $(`#additional-item-id-${form_add_on_type_count}`).change(function(e) {
                            e.preventDefault();
                            $(`#additional-jumlah-display-${form_add_on_type_count}`).html(numberWithCommas($('#jumlah').val()));

                            if ($(this).val()) {
                                $(`#additiona-item-name-${form_add_on_type_count}`).html($(`#additional-item-id-${form_add_on_type_count}`).select2('data')[0].text);
                                $.ajax({
                                    type: "get",
                                    url: `{{ route('admin.item.price-latest') }}/${this.value}`,
                                    success: function({
                                        data
                                    }) {
                                        $(`#additional-harga-${form_add_on_type_count}`).val(numberWithDot(decimalFormatterWithOuNumberWithCommas(data.harga_jual)));
                                        $(`#additional-harga-${form_add_on_type_count}`).trigger('focus');
                                    }
                                });
                            } else {
                                $(`#additiona-item-name-${form_add_on_type_count}`).html();
                                $(`#additional-harga-${form_add_on_type_count}`).val(0);
                                $(`#price-${index}`).trigger('focus');
                            }
                        });

                        const TaxOnChange = () => {
                            additional_tax_list_value[form_add_on_type_count] = [];
                            tax_list = [];
                            [...document.getElementById(`additional-tax-id-${form_add_on_type_count}`).options].map((option, selected_index) => {
                                if (option.selected) {
                                    tax_list[selected_index] = option.value;
                                }
                            });

                            tax_list_value[form_add_on_type_count] = tax_list;
                            $(`#additional-tax-value-${form_add_on_type_count}`).val(tax_list.toString());

                            $(`#additional_tax_data_detail_${form_add_on_type_count}`).html('');
                            $(`#additional_tax_value_detail_${form_add_on_type_count}`).html('')
                            tax_list.map((tax, tax_index) => {
                                $.ajax({
                                    type: "get",
                                    url: "{{ route('admin.tax.detail') }}/" + tax,
                                    success: ({
                                        data
                                    }) => {
                                        additional_tax_list_value[form_add_on_type_count][tax_index] = data.value;
                                        let new_html = `
                                                <p>
                                                    <span>${data.name} - ${data.value * 100}%</span>
                                                </p>`;
                                        $(`#additional_tax_data_detail_${form_add_on_type_count}`).append(new_html);
                                        $(`#additional_tax_value_detail_${form_add_on_type_count}`).append(`
                                            <p>
                                                <span id="currency-simbol" class="me-10">${currency_symbol}</span>
                                                <span class="fw-700" id="tax-${tax_index}-${form_add_on_type_count}">${numberWithCommas(additional_sub_total_list[form_add_on_type_count] * data.value)}</span>
                                            </p>
                                        `);

                                        calculateSingleAdditional();
                                    }
                                });
                            });

                            calculateSingleAdditional();

                        };

                        $(`#additional-tax-id-${form_add_on_type_count}`).change(function(e) {
                            tax_list[index] = this.value ?? null;
                            TaxOnChange();
                        });

                    } else {
                        $(`#additional-item-id-${form_add_on_type_count}`).attr('disabled');
                        $(`#additional-jumlah`).attr('disabled');
                        $(`#additional-tax-id-${form_add_on_type_count}`).attr('disabled');
                        $(`#additional-harga-${form_add_on_type_count}`).attr('disabled', 'disabled');

                        $(`#additional-item-id-${form_add_on_type_count}`).select2('destroy');
                        $(`#additional-tax-id-${form_add_on_type_count}`).select2('destroy');
                        $(`#additional-tax-id-${form_add_on_type_count}`).select2();
                    }
                });

            form_add_on_type_count += 1;
            return;
        }

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


        const addFormAddOn = () => {
            displayFormAddOnType();
        }

        $('#btn-add-on-delete').on('click', (e) => {
            console.log('click');
            e.preventDefault();
            $(`#form-add-on-type-${$('#btn-add-on-delete').data('bs-target')}`).remove();
        });

        $('#add-form').click(function(e) {
            e.preventDefault();
            addFormAddOn();
        });

        $('#tax_id').on('change click', function(e) {
            tax_function();
            calculateTotal();
            updateValuePrices();
        })

        addFormAddOn();
    });
</script>