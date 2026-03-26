function getDODetail(e) {
    $('#return-data').html('');
    calculateTotal();

    if ($(e).val() == "undefined" || $(e).val() == "" || $(e).val() == null) {
        return false;
    }

    let type = $(e).select2('data')[0].text;
    type = type.split('#')[1];
    $('#type').val(type);

    $.ajax({
        type: "get",
        url: `${base_url}/invoice-return/${$(e).val()}/get-do-detail?type=${type}`,
        success: function (data) {
            var html = '';
            $('#delivery_date').val(data.data.delivery_date);
            $('.currency_kode').text(data.data.currency.kode);
            $('#currency_id').html(`<option value="${data.data.currency.id}" selected>${data.data.currency.kode} - ${data.data.currency.nama}</option>`);
            if (data.data.ware_house != null) {
                if ($('#ware_house_id').data('select2')) {
                    $('#ware_house_id').select2('destroy');
                }
                $('#ware_house_id').html(`<option value="${data.data.ware_house.id}" selected>${data.data.ware_house.nama}</option>`);
            } else {
                initSelect2Search('ware_house_id', `${base_url}/select/ware-house`, {
                    id: "id",
                    text: "nama"
                });
            }
            $('#exchange_rate').val(formatRupiahWithDecimal(data.data.exchange_rate));

            $.each(data.data.details, function (index, value) {
                let taxes = '';
                $.each(value.taxes, function (index_tax, value_tax) {
                    taxes += `<div class="mb-1">
                                <input class="tax_id_${index}" type="hidden" id="tax_id_${index}_${index_tax}" name="tax_id[${value.reference_id}][]" value="${value_tax.tax_id}" data-index="${index_tax}">
                                <input class="tax_value_${index}" type="hidden" id="tax_value_${index}_${index_tax}" name="tax_value[${value.reference_id}][]" value="${value_tax.value}" data-index="${index_tax}">
                                <div class="row">
                                    <div class="col-md-12">
                                        ${value_tax.tax.name} ${parseFloat(value_tax.value * 100)}%
                                    </div>
                                    <div class="col-md-12">
                                        <input class="text-end form-control tax_amount_${index}" type="text" id="tax_amount_${index}_${index_tax}" name="tax_amount[${value.reference_id}][]" value="0" readonly data-index="${index_tax}">
                                    </div>
                                </div>
                            </div>`;
                });

                html += ` <tr id="row-${index}">
                                <td class="align-top">
                                    <input type="hidden" name="invoice_return_detail_id[]" value="">
                                    <input type="hidden" name="reference_model[]" value="${value.reference_model}">
                                    <input type="hidden" name="reference_id[]" value="${value.reference_id}">
                                    <input type="hidden" name="item_id[]" value="${value.item.id}">
                                    <input type="hidden" name="unit_id[]" value="${value.unit.id}">
                                    ${value.item.nama} <br>
                                    ${value.item.kode}
                                </td>
                                <td class="align-top">
                                    ${value.unit.name}
                                </td>
                                <td class="text-end align-top">
                                    <input type="text" id="do_qty_${index}" name="do_qty[]" value="${formatRupiahWithDecimal(value.qty)}" class="form-control text-end" readonly>
                                    <br>
                                    <span><b>QTY Retur :</b> ${formatRupiahWithDecimal(value.return_qty)}</span>
                                    <input type="hidden" id="return_qty_${index}" name="return_qty[]" value="${parseFloat(value.return_qty)}">
                                    <input type="hidden" id="rest_qty_${index}" name="rest_qty[]" value="${parseFloat(value.qty - value.return_qty)}">
                                </td>
                                <td class="text-end align-top">
                                    <input type="text" id="qty_${index}" name="qty[]" value="0" class="form-control text-end commas-form" onkeyup="countRowTotal(${index})">
                                </td>
                                <td class="text-end align-top">
                                    <input type="text" id="price_${index}" name="price[]" value="${formatRupiahWithDecimal(value.price)}" class="form-control text-end" readonly>
                                    <input type="hidden" id="hpp_${index}" name="hpp[]" value="${parseFloat(value.hpp)}">
                                </td>
                                <td class="text-end align-top">
                                    <input type="text" id="subtotal_${index}" name="subtotal[]" value="0" class="form-control text-end" readonly>
                                    </td>
                                    <td>
                                    ${taxes}
                                    <input type="hidden" id="subtotal_tax_amount_${index}" name="subtotal_tax_amount[]" value="0" class="form-control text-end" readonly>
                                </td>
                                <td class="text-end align-top">
                                    <input type="text" id="total_${index}" name="total[]" value="0" class="form-control text-end" readonly>
                                </td>
                                <td>
                                    <button type="button" onclick="$('#row-${index}').remove();calculateTotal()" class="btn btn-sm btn-danger"><i class="fa fa-minus"></i></button>
                                </td>
                            </tr>`;
            })

            $('#return-data').html(html);

            initCommasForm();
        }
    });
}

function countRowTotal(id) {
    let row_do_qty = $(`#do_qty_${id}`);
    let row_qty = $(`#qty_${id}`);
    let row_rest_qty = $(`#rest_qty_${id}`);
    let row_price = $(`#price_${id}`);
    let row_subtotal = $(`#subtotal_${id}`);
    let row_total = $(`#total_${id}`);
    let row_subtotal_tax_amount = $(`#subtotal_tax_amount_${id}`);

    if (thousandToFloat(row_qty.val()) > thousandToFloat(row_rest_qty.val())) {
        showAlert('', 'Qty tidak boleh melebihi jumlah barang', 'warning');

        row_qty.val(thousandToFloat(row_rest_qty.val()));
    }

    let calculate_subtotal = thousandToFloat(row_qty.val()) * thousandToFloat(row_price.val());
    row_subtotal.val(formatRupiahWithDecimal(calculate_subtotal));

    let tax_amount = 0;
    let tax_elements = $(`.tax_id_${id}`);
    $.each(tax_elements, function (e) {
        let el_tax_index = $(this).data('index');
        let el_tax_value = $(`#tax_value_${id}_${el_tax_index}`);
        let el_tax_amount = $(`#tax_amount_${id}_${el_tax_index}`);
        let count_el_tax_amount = calculate_subtotal * parseFloat(el_tax_value.val());
        el_tax_amount.val(formatRupiahWithDecimal(count_el_tax_amount));

        tax_amount += count_el_tax_amount;
    });

    row_total.val(formatRupiahWithDecimal((calculate_subtotal + tax_amount)));
    row_subtotal_tax_amount.val(formatRupiahWithDecimal(tax_amount));

    calculateTotal();
}

function calculateTotal() {
    var elements = $('input[name="total[]"]');
    let calculate_grand_total = 0;
    $.each(elements, function (e) {
        calculate_grand_total += thousandToFloat($(this).val());
    });

    var elements = $('input[name="subtotal[]"]');
    let calculate_grand_subtotal = 0;
    $.each(elements, function (e) {
        calculate_grand_subtotal += thousandToFloat($(this).val());
    });

    var elements = $('input[name="subtotal_tax_amount[]"]');
    let calculate_grand_tax_amount = 0;
    $.each(elements, function (e) {
        calculate_grand_tax_amount += thousandToFloat($(this).val());
    });

    if (calculate_grand_tax_amount > 0) {
        $('#tax_number').attr('required', true);
    } else {
        $('#tax_number').attr('required', false);
    }

    $('#grand_subtotal_text').text(formatRupiahWithDecimal(calculate_grand_subtotal));
    $('#grand_tax_amount_text').text(formatRupiahWithDecimal(calculate_grand_tax_amount));
    $('#grand_total_text').text(formatRupiahWithDecimal(calculate_grand_total));
}


$('#form-data').submit(function (e) {
    var zero_row = $('input[name="reference_model[]"]').length;
    var find_zero_amount = $('input[name="qty[]"]').filter(function () {
        return parseInt(this.value, 10) == 0;
    }).length;

    let is_date_valid = validateDate();

    if (!is_date_valid) {
        e.preventDefault();
    }

    if (zero_row == 0 || find_zero_amount > 0) {
        e.preventDefault();

        setTimeout(() => {
            $(this).find('input[type=submit]').prop('disabled', false);
            $(this).find('button[type=submit]').prop('disabled', false);
        }, 1000);

        showAlert('', 'Belum ada item retur yang dipilih/ pastikan qty tidak 0', 'warning');
        return false;
    }
});


function resetForm() {
    $('#return-data').html('');
    $('#reference_parent_id').val('').trigger('change');
}


// check unique tax number
function check_unique_tax_number(except_id) {
    $.ajax({
        type: "post",
        url: `${base_url}/invoice-return/check-unique-tax-number`,
        data: {
            _token: token,
            tax_number: $('#tax_number').val(),
            except_id: except_id
        },
        success: function (data) {
            if (!data.status) {
                showAlert('', data.message, 'warning');
                $('#tax_number').val('');
            }
        }
    });
}

// validate date and delivery date
function validateDate() {
    let date = $('#date').val();
    date = date.split('-').reverse().join('');
    let delivery_date = $('#delivery_date').val();
    delivery_date = delivery_date.split('-').reverse().join('');

    if (date != '' && delivery_date != '') {
        if (date < delivery_date) {
            showAlert('', 'Tanggal retur tidak boleh lebih kecil dari tanggal DO', 'warning');
            $('#date').val('');

            setTimeout(() => {
                $(document).find('input[type=submit]').prop('disabled', false);
                $(document).find('button[type=submit]').prop('disabled', false);
            }, 1000);

            return false;
        }

    }

    return true;
}
