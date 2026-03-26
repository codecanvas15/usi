$(document).ready(function () {
    $("#sequence_code").on("blur", function () {
        check_bank_code(
            "#coa_id",
            "#sequence_code",
            "#date_input",
            "out"
        );
    });

    initSelect2Search('invoice_return_id', `${base_url}/fund-submission-invoice-return-select`, {
        id: "id",
        text: "code,customer_name,remaining_amount"
    }, 0, {
        currency_id: function () {
            return $('#currency_id').val();
        },
        branch_id: function () {
            return $('#branch_id').val();
        },
    });

    $('#invoice_return_id').change(function () {
        $('#to_name').attr('readonly', false);
        if ($('input[name="debit[]"]').length == 1) {
            $('#outgoing-payment-detail-data').html('');
        }
        $('.return_row').remove();
        countTotal();
        $.ajax({
            type: "get",
            url: `${base_url}/fund-submission-invoice-return/${$(this).val()}`,
            success: function (response) {
                $('#to_name').val(response.customer.nama).attr('readonly', true);
                key += 1;
                var append_element = `<tr id="fund-submission-detail-${key}" class="return_row">
                                        <td>
                                            <input type="hidden" name="is_return[]" value="true" />
                                            <input type="hidden" name="type[]" value="return" />
                                            <input type="hidden" name="outgoing_payment_detail_id[]" value="" />
                                            <select name="coa_detail_id[]" id="coa_detail_id_${key}" class="form-control" required autofocus style="width:100%">
                                                <option selected value="${response.customer_account_receivable_coa.id}">${response.customer_account_receivable_coa.account_code} - ${response.customer_account_receivable_coa.name}</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="note[]" id="note_${key}" placeholder="Masukkan Keterangan" />
                                        </td>
                                        <td>
                                            <input type="hidden" id="return_amount" value="${response.invoice_return.remaining_amount}">
                                            <input type="text" class="form-control commas-form text-end" name="debit[]" id="debit_${key}" required placeholder="Masukkan Nominal" onkeyup="countTotal();validate_return_amount($(this))" value="${formatRupiahWithDecimal(response.invoice_return.remaining_amount)}" />
                                        </td>
                                        <td></td>
                                    </tr>`;

                $('#outgoing-payment-detail-data').append(append_element);

                initCommasForm();
                countTotal()
            },
        });
    });

    initSelect2SearchPaginationData('cash_advance_receive_id', `${base_url}/select/cash-advance-receive`, {
        id: "id",
        text: "code,customer_name"
    }, 0, {
        currency_id: function () {
            return $('#currency_id').val();
        },
        branch_id: function () {
            return $('#branch_id').val();
        },
    });


    $('#cash_advance_receive_id').change(function () {
        $('#to_name').attr('readonly', false);
        if ($('input[name="debit[]"]').length == 1) {
            $('#outgoing-payment-detail-data').html('');
        }
        $('.cash_advance_row').remove();
        countTotal();
        $.ajax({
            type: "get",
            url: `${base_url}/fund-submission-cash-advance-detail/${$(this).val()}`,
            success: function (response) {
                $('#to_name').val(response.customer.nama).attr('readonly', true);
                key += 1;
                var append_element = `<tr id="fund-submission-detail-${key}" class="cash_advance_row">
                        <td>
                            <input type="hidden" name="is_return[]" value="false" />
                            <input type="hidden" name="type[]" value="cash_advance" />
                            <input type="hidden" name="outgoing_payment_detail_id[]" value="" />
                            <select name="coa_detail_id[]" id="coa_detail_id_${key}" class="form-control" required autofocus style="width:100%">
                                <option selected value="${response.cash_advance_coa.id}">${response.cash_advance_coa.account_code} - ${response.cash_advance_coa.name}</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control" name="note[]" id="note_${key}" placeholder="Masukkan Keterangan" />
                        </td>
                        <td>
                            <input type="hidden" id="cash_advance_amount" value="${response.outstanding_amount}">
                            <input type="text" class="form-control commas-form text-end" name="debit[]" id="debit_${key}" required placeholder="Masukkan Nominal" onkeyup="countTotal();validate_cash_advance_amount($(this))" value="${formatRupiahWithDecimal(response.outstanding_amount)}" />
                        </td>
                        <td></td>
                    </tr>`;

                $('#outgoing-payment-detail-data').append(append_element);

                initCommasForm();
                countTotal()
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

})
