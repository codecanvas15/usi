var outgoing_payment_detail_data = $("#outgoing-payment-detail-data");
var form_fund_submission = $("#form-fund-submission");
var fund_submission_id = $("#fund_submission_id");
var form_detail = $("#form_detail");

var general_form = ``;

let fund_submission_date = null;

function addNewOutgoingPaymentDetailRow() {
    key += 1;
    var append_element = `<tr id="outgoing-payment-detail-${key}">
                            <td>
                                <input type="hidden" name="is_return[]" value="" />
                                <input type="hidden" name="type[]" value="outgoing_payment" />
                                <input type="hidden" name="outgoing_payment_detail_id[]" value="" />
                                <select name="coa_detail_id[]" id="coa_detail_id_${key}" class="form-control" required autofocus style="width:100%">
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control" name="note[]" id="note_${key}" placeholder="Masukkan Keterangan" />
                            </td>
                            <td>
                                <input type="text" class="form-control commas-form text-end" name="debit[]" id="debit_${key}" required placeholder="Masukkan Nominal" onkeyup="countTotal()" value="" />
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm" onclick="$('#outgoing-payment-detail-${key}').remove();countTotal()"><i class="fa fa-times"></i></button>
                            </td>
                        </tr>`;

    $("#outgoing-payment-detail-data").append(append_element);

    initCoaSelect(`#coa_detail_id_${key}`);
    initCommasForm();
}

function countTotal() {
    let count_debit_total = 0;
    let total_debit_elements = $('input[name="debit[]"]');

    $.each(total_debit_elements, function (e) {
        count_debit_total += thousandToFloat($(this).val());
    });

    $("#debit_total").text(formatRupiahWithDecimal(count_debit_total));
    $("#debit_total_hide").val(count_debit_total);
}

function initReference() {
    var get_from = $("#from").val();

    form_detail.html("");
    if (get_from == "general") {
        form_fund_submission.addClass("d-none");
        fund_submission_id.attr("required", false).val("").trigger("change");

        $.ajax({
            type: "post",
            url: `${base_url}/outgoing-payment/general-form`,
            data: {
                _token: token,
            },
            dataType: "html",
            success: function (data) {
                form_detail.html(data);

                initSelect2SearchPagination(`coa_detail_id_0`, `${base_url}/select/coa`, {
                    id: "id",
                    text: "account_code,name",
                });

                initSelect2SearchPagination(
                    `coa_id`,
                    `${base_url}/select/coa`,
                    {
                        id: "id",
                        text: "account_code,name",
                    },
                    0,
                    {
                        account_type: "Cash & Bank",
                        currency_id: function () {
                            return $("#currency_id").val();
                        }
                    }
                );

                initSelect2Search(
                    `currency_id`,
                    `${base_url}/select/currency`,
                    {
                        id: "id",
                        text: "kode,nama",
                    }
                );

                initSelect2Search(
                    `project_id`,
                    `${base_url}/select/project`,
                    {
                        id: "id",
                        text: "code,name",
                    },
                    2,
                    {
                        branch_id: function () {
                            return $("#branch_id").val();
                        },
                    }
                );

                if (branchIsPrimary == 1) {
                    initSelect2Search(
                        `branch_id`,
                        `${base_url}/select/branch`,
                        {
                            id: "id",
                            text: "name",
                        }
                    );
                }
                initCommasForm();
                initCurrencyOnChange();
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

            },
        });
    } else {
        form_fund_submission.removeClass("d-none");
        fund_submission_id.attr("required", true);

        initSelect2Search(
            "fund_submission_id",
            `${base_url}/select/fund-submission`,
            {
                id: "id",
                text: "code,to_name,total"
            },
            0,
            {
                item: "general",
                available: true,
                date: function () {
                    return $("#date_input").val();
                }
            }
        );
    }
}

function getFundSubmission(e) {
    $(document).find("input[type=submit]").prop("disabled", false);
    $(document).find("button[type=submit]").prop("disabled", false);
    $.ajax({
        type: "post",
        url: `${base_url}/outgoing-payment/fund-submission`,
        data: {
            _token: token,
            fund_submission_id: $(e).val(),
            date: function () {
                return $("#date_input").val();
            }
        },
        success: function (data) {
            // * validate date fund submission can't less than current selected date
            if (
                parseDate($("#date_input").val()) < parseDate(data.data.date)
            ) {
                fund_submission_date = null;
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Tanggal pengajuan dana tidak boleh kurang dari tanggal di pilih",
                });

                $("#fund_submission_id").val(null).trigger("change");

                return;
            }

            fund_submission_date = parseDate(data.data.date);

            form_detail.html(data.html);
            if (!data.data.send_payment.due_status_by_date.is_due) {
                $(document).find("input[type=submit]").prop("disabled", true);
                $(document).find("button[type=submit]").prop("disabled", true);
            }
            initCommasForm();
            initCurrencyOnChange();
            $("#sequence_code").on("blur", function () {
                check_bank_code(
                    "#coa_id",
                    "#sequence_code",
                    "#date_input",
                    "out"
                );
            });
        },
    });
}

function initCurrencyOnChange() {
    $("#currency_id").change(function (e) {
        e.preventDefault();
        $.ajax({
            type: "get",
            url: `${base_url}/currency/detail/${this.value}`,
            success: function ({ data }) {
                if (data.is_local) {
                    $("#exchange_rate").val(1);
                    $("#exchange_rate").attr("readonly", "readonly");
                } else {
                    $("#exchange_rate").removeAttr("readonly");
                    $("#exchange_rate").attr("readonly", false);
                }
            },
        });
    });
}

$("#form-data").submit(function (e) {
    let empty_note_detail = $('input[name="note[]"]');
    $.each(empty_note_detail, function (e) {
        if ($(this).val() == "") {
            $(this).val($("#reference").val());
        }
    });
});

$("#date_input").change(function (e) {
    e.preventDefault();

    if (fund_submission_date == null) return;
    if (parseDate(this.value) < fund_submission_date) {
        $(this).val("");
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Tanggal pengajuan dana tidak boleh kurang dari tanggal di pilih",
        });

        return;
    }
});


const get_coa_detail = (e) => {
    $.ajax({
        type: "get",
        url: `${base_url}/coa/${$(e).val()}`,
        success: function (response) {
            if (response.data.currency) {
                $('#currency_id').append(`<option selected value="${response.data.currency.id}">${response.data.currency.kode} - ${response.data.currency.nama}</option>`);
            }
        },
    });
}

function validate_return_amount(e) {
    let pay_amount = thousandToFloat($(e).val());
    let return_amount = $('#return_amount').val();

    if (pay_amount > return_amount) {
        alert('Nominal tidak boleh melebihi sisa retur');
        $(e).val(formatRupiahWithDecimal(return_amount));
    }
}
