var incoming_payment_detail_data = $('#incoming-payment-detail-data')

function addNewIncomingPaymentDetailRow() {
    key += 1;
    var append_element = `<tr id="incoming-payment-detail-${key}">
                            <td>
                                <input type="hidden" name="is_return[]" value="" />
                                <input type="hidden" name="type[]" value="incoming_payment" />
                                <input type="hidden" name="incoming_payment_detail_id[]" value="" />
                                <select name="coa_detail_id[]" id="coa_detail_id_${key}" class="form-control" required autofocus style="width:100%">
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control" name="note[]" id="note_${key}" placeholder="Masukkan Keterangan" />
                            </td>
                            <td>
                                <input type="text" class="form-control commas-form text-end" name="credit[]" id="credit_${key}" required placeholder="Masukkan Nominal" onkeyup="countTotal()" value="" />
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm" onclick="$('#incoming-payment-detail-${key}').remove();countTotal()"><i class="fa fa-times"></i></button>
                            </td>
                        </tr>`;

    $('#incoming-payment-detail-data').append(append_element);

    initCoaSelect(`#coa_detail_id_${key}`);
    initCommasForm();
}

function countTotal() {
    let count_credit_total = 0;
    let total_credit_elements = $('input[name="credit[]"]');

    $.each(total_credit_elements, function (e) {
        count_credit_total += thousandToFloat($(this).val());
    });

    $('#credit_total').text(formatRupiahWithDecimal(count_credit_total));
    $('#credit_total_hide').val(count_credit_total);
}

$('#form-data').submit(function (e) {
    let empty_note_detail = $('input[name="note[]"]');
    $.each(empty_note_detail, function (e) {
        if ($(this).val() == "") {
            $(this).val($('#reference').val());
        }
    })
});

const toggleGiroForm = (e) => {
    if ($(e).is(":checked")) {
        $(".giro-form").removeClass("d-none");
        $('#receive_payment_id').attr('required', true);
    } else {
        $(".giro-form").addClass("d-none");
        $('#receive_payment_id').attr('required', false);
        $('#receive_payment_id').val('').trigger('change');
        $('#cheque_no').text('');
        $('#due_date').html('');
        $('#from_bank').text('');
        $('#realization_bank').text('');
        $('#giro_amount').text('');
        $('#giro_outstanding_amount_text').text('');
        $('#giro_outstanding_amount').val('');
    }
};

const get_receive_payment = (e) => {
    $('#cheque_no').text('');
    $('#due_date').html('');
    $('#from_bank').text('');
    $('#realization_bank').text('');
    $('#giro_amount').text('');
    $('#giro_outstanding_amount_text').text('');
    $('#giro_outstanding_amount').val('');
    $.ajax({
        type: "get",
        url: `${base_url}/receive-payment/${$(e).val()}`,
        data: {
            date: function () {
                return $("#date").val();
            },
        },
        success: function (data) {
            let due_badge = "";
            if (data.data.due_status_by_date.is_due) {
                due_badge = `<br><span class="badge badge-success">${data.data.due_status_by_date.message}</span>`;
            } else {
                due_badge = `<br><span class="badge badge-danger">${data.data.due_status_by_date.message}</span>`;
            }

            $('#from_name').val(data.data.from_name);
            $('#cheque_no').text(data.data.cheque_no);
            $('#due_date').html(localDate(data.data.due_date) + due_badge);
            $('#from_bank').text(data.data.from_bank);
            $('#realization_bank').text(data.data.realization_bank);
            $('#giro_amount').text(data.data.currency.kode + ' ' + formatRupiahWithDecimal(data.data.amount));
            $('#giro_outstanding_amount_text').text(data.data.currency.kode + ' ' + formatRupiahWithDecimal(data.data.outstanding_amount));
            $('#giro_outstanding_amount').val(data.data.outstanding_amount);
        },
    });
}

const get_coa_detail = (e) => {
    $.ajax({
        type: "get",
        url: `${base_url}/coa/${$(e).val()}`,
        success: function (response) {
            if (response.data.bank_internal != null && response.data.bank_internal.type == "bank") {
                $('.giro-checkbox').removeClass('d-none');
            } else {
                $('.giro-checkbox').addClass('d-none');
                $(".giro-form").addClass("d-none");
                $('#receive_payment_id').attr('required', false);
                $('#receive_payment_id').val('').trigger('change');
                $('#cheque_no').text('');
                $('#due_date').html('');
                $('#from_bank').text('');
                $('#realization_bank').text('');
                $('#giro_amount').text('');
                $('#giro_outstanding_amount_text').text('');
                $('#giro_outstanding_amount').val('');
            }

            if (response.data.currency) {
                $('#currency_id').append(`<option selected value="${response.data.currency.id}">${response.data.currency.kode} - ${response.data.currency.nama}</option>`);
            }

        },
    });
}

initSelect2Search('purchase_return_id', `${base_url}/incoming-payment-purchase-return-select`, {
    id: "id",
    text: "code,vendor_name,remaining_amount"
}, 0, {
    currency_id: function () {
        return $('#currency_id').val();
    },
    branch_id: function () {
        return $('#branch_id').val();
    },
});

$('#purchase_return_id').change(function () {
    if ($(this).val()) {
        $('#cash_advance_payment_id').val('').trigger('change').prop('disabled', true);
    } else {
        $('#cash_advance_payment_id').prop('disabled', false);
    }
    $('#from_name').val('').attr('readonly', false);
    $('.return_row').remove();
    if ($('input[name="credit[]"]').length == 1) {
        $('#incoming-payment-detail-data').html('');
    }
    countTotal();
    $.ajax({
        type: "get",
        url: `${base_url}/incoming-payment-purchase-return/${$(this).val()}`,
        success: function (response) {
            $('#from_name').val(response.vendor.nama).attr('readonly', true);
            key += 1;
            var append_element = `<tr id="incoming-payment-detail-${key}" class="return_row">
                                    <td>
                                        <input type="hidden" name="is_return[]" value="true" />
                                        <input type="hidden" name="type[]" value="return" />
                                        <input type="hidden" name="incoming_payment_detail_id[]" value="" />
                                        <select name="coa_detail_id[]" id="coa_detail_id_${key}" class="form-control" required autofocus style="width:100%">
                                            <option selected value="${response.vendor_account_payable_coa.id}">${response.vendor_account_payable_coa.account_code} - ${response.vendor_account_payable_coa.name}</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="note[]" id="note_${key}" placeholder="Masukkan Keterangan" />
                                    </td>
                                    <td>
                                        <input type="hidden" id="return_amount" value="${response.purchase_return.remaining_amount}">
                                        <input type="text" class="form-control commas-form text-end" name="credit[]" id="credit_${key}" required placeholder="Masukkan Nominal" onkeyup="countTotal();validate_return_amount($(this))" value="${formatRupiahWithDecimal(response.purchase_return.remaining_amount)}" />
                                    </td>
                                    <td>
                                    </td>
                                </tr>`;

            $('#incoming-payment-detail-data').append(append_element);

            initCommasForm();
        },
    });
});

function validate_return_amount(e) {
    let pay_amount = thousandToFloat($(e).val());
    let return_amount = $('#return_amount').val();

    if (pay_amount > return_amount) {
        alert('Nominal tidak boleh melebihi sisa retur');
        $(e).val(formatRupiahWithDecimal(return_amount));
    }
}


$('form').submit(function (e) {
    if ($('input[name="credit[]"]').length == 0) {
        $(this).find('input[type=submit]').prop('disabled', false);
        $(this).find('button[type=submit]').prop('disabled', false);
        e.preventDefault();

        alert('Detail tidak boleh kosong');

        return false;
    }
})

$('#cash_advance_payment_id').change(function () {
    if ($(this).val()) {
        $('#purchase_return_id').val('').trigger('change').prop('disabled', true);
    } else {
        $('#purchase_return_id').prop('disabled', false);
    }
    $('#from_name').val('').attr('readonly', false);
    $('.cash_advance_row').remove();
    if ($('input[name="credit[]"]').length == 1) {
        $('#incoming-payment-detail-data').html('');
    }
    countTotal();
    $.ajax({
        type: "get",
        url: `${base_url}/incoming-payment-cash-advance/${$(this).val()}`,
        success: function (response) {
            $('#from_name').val(response.to_name).attr('readonly', true);
            key += 1;
            var append_element = `<tr id="incoming-payment-detail-${key}" class="cash_advance_row">
                                    <td>
                                        <input type="hidden" name="is_return[]" value="false" />
                                        <input type="hidden" name="type[]" value="cash_advance" />
                                        <input type="hidden" name="incoming_payment_detail_id[]" value="" />
                                        <select name="coa_detail_id[]" id="coa_detail_id_${key}" class="form-control" required autofocus style="width:100%">
                                            <option selected value="${response.cash_advance_coa.id}">${response.cash_advance_coa.account_code} - ${response.cash_advance_coa.name}</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="note[]" id="note_${key}" placeholder="Masukkan Keterangan" />
                                    </td>
                                    <td>
                                        <input type="hidden" id="cash_advance_amount" value="${response.outstanding_amount}">
                                        <input type="text" class="form-control commas-form text-end" name="credit[]" id="credit_${key}" required placeholder="Masukkan Nominal" onkeyup="countTotal();validate_cash_advance_amount($(this))" value="${formatRupiahWithDecimal(response.outstanding_amount)}" />
                                    </td>
                                    <td>
                                    </td>
                                </tr>`;

            $('#incoming-payment-detail-data').append(append_element);

            initCommasForm();
        },
    });
});

function validate_cash_advance_amount(e) {
    let pay_amount = thousandToFloat($(e).val());
    let cash_advance_amount = $('#cash_advance_amount').val();

    if (pay_amount > cash_advance_amount) {
        alert('Nominal tidak boleh melebihi sisa uang muka');
        $(e).val(formatRupiahWithDecimal(cash_advance_amount));
    }
}
