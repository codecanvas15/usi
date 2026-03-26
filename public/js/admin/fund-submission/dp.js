let totalCashAdvanceHtml = $("#total-cash-advance");
var item = $("#item");
var fund_submission_forms = $(".fund-submission-forms");

var to_model = $("#to_model");
var to_id = $("#to_id");
var form_item = $("#form_item");

var cash_advance_coa_id = $(".cash_advance_coa_id");

var cash_bank_amount = $(".cash_bank_amount");
var cash_advance_amount = $(".cash_advance_amount");

initSelect2SearchPaginationData(`to_id`, `${base_url}/select/vendor`, {
    id: "id",
    text: "nama",
});

var key = $("#count_rows").val();

function addOtherCost() {
    $('#empty-row').remove();
    key += 1;
    var append_element = `<tr id="cash-advance-detail-${key}">
                            <td>
                                <input type="hidden" name="type[]" value="other">
                                <input type="hidden" name="position[]" value="debit">
                                <input type="hidden" name="fund_submission_cash_advance_id[]" value="" />
                                <select name="coa_detail_id[]" id="coa_detail_id_${key}" class="form-control other_coa_id" required autofocus style="width:100%">
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control" name="note[]" id="note_${key}" placeholder="Masukkan Keterangan" />
                            </td>
                            <td>
                                <input type="text" class="form-control commas-form text-end other_amount" name="amount[]" id="amount_${key}" required placeholder="Masukkan Nominal" onkeyup="countTotal()" value="" />
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm" onclick="$('#cash-advance-detail-${key}').remove();countTotal()"><i class="fa fa-times"></i></button>
                            </td>
                        </tr>`;

    $("#cash-advance-detail-data").append(append_element);

    initCoaSelect(`#coa_detail_id_${key}`);
    initCommasForm();
}

function countTotal() {
    let cash_bank_amount = thousandToFloat($(".cash_advance_amount").val());
    let tax_value = $('#tax_value').val();
    let tax_amount = cash_bank_amount * parseFloat(tax_value);

    $('.tax-amount').val(formatRupiahWithDecimal(tax_amount));

    let total_debit_elements = $(".other_amount");
    $.each(total_debit_elements, function (e) {
        cash_bank_amount += thousandToFloat($(this).val());
    });

    $(".cash_bank_amount").val(formatRupiahWithDecimal(cash_bank_amount));

    totalCashAdvanceHtml.text(formatRupiahWithDecimal(cash_bank_amount));
}

$("#form-data").submit(function (e) {
    let empty_note_detail = $('input[name="note[]"]');
    $.each(empty_note_detail, function (e) {
        if ($(this).val() == "") {
            $(this).val($("#referensi").val());
        }
    });

    var find_zero_amount = $('input[name="amount[]"]').filter(function () {
        return !$(this).prop('readonly') && parseFloat(thousandToFloat(this.value)) == 0;
    }).length;

    if (find_zero_amount > 0) {
        e.preventDefault();

        setTimeout(() => {
            $(this).find("input[type=submit]").prop("disabled", false);
            $(this).find("button[type=submit]").prop("disabled", false);
        }, 1000);

        showAlert("", "Ada jumlah bayar yang masih 0", "warning");
        return false;
    }
});

function getInitialCoa() {
    if (to_model.val() == "App\\Models\\Vendor") {
        $.ajax({
            type: "post",
            url: `${base_url}/fund-submission/vendor-coa/${to_id.val()}`,
            data: {
                _token: token,
                type: "Vendor Deposite Coa",
            },
            success: function ({ data }) {
                cash_advance_coa_id.append(
                    `<option value="${data.coa_id}" selected>${data.coa.account_code} - ${data.coa.name}</option>`
                );
            },
        });
    }
}

const toggleGiroForm = (e) => {
    if ($(e).is(":checked")) {
        $("#giro-form").removeClass("d-none");
        $("#giro-form").find("input").attr("required", true);
    } else {
        $("#giro-form").addClass("d-none");
        $("#giro-form").find("input").attr("required", false).val("");
    }
};

const get_coa_detail = (e) => {
    $.ajax({
        type: "get",
        url: `${base_url}/coa/${$(e).val()}`,
        success: function (response) {
            if (
                response.data.bank_internal != null &&
                response.data.bank_internal.type == "bank"
            ) {
                $(".giro-checkbox").removeClass("d-none");
            } else {
                $(".giro-checkbox").addClass("d-none");
                $("#giro-form").addClass("d-none");
                $("#is_giro").prop("checked", false);
                $("#giro-form").find("input").attr("required", false).val("");
            }

            // if (response.data.currency) {
            //     $('#currency-select').append(`<option selected value="${response.data.currency.id}">${response.data.currency.kode} - ${response.data.currency.nama}</option>`);
            // }

        },
    });
};

function addOtherCostTax(el) {
    if (el.val()) {
        $('.tax_number_input').removeClass('d-none');
        $.ajax({
            type: "get",
            url: `${base_url}/tax/${el.val()}`,
            success: function (response) {
                key += 1;
                let tax_value = response.data.value;
                let tax_amount = thousandToFloat($('.cash_advance_amount').val()) * tax_value;
                $('#tax_value').val(tax_value);

                var append_element = `<tr id="cash-advance-detail-${key}" class="row-tax">
                                        <td>
                                            <input type="hidden" name="type[]" value="tax">
                                            <input type="hidden" name="position[]" value="debit">
                                            <input type="hidden" name="fund_submission_cash_advance_id[]" value="" />
                                            <select name="coa_detail_id[]" id="coa_detail_id_${key}" class="form-control other_coa_id" required autofocus style="width:100%">
                                                <option value="${response.data.coa_purchase}" selected>${response.data.coa_purchase_data.account_code} - ${response.data.coa_purchase_data.name}</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="note[]" id="note_${key}" placeholder="Masukkan Keterangan" value="${response.data.name} ${formatRupiahWithDecimal(tax_value * 100)}%" />
                                        </td>
                                        <td>
                                            <input type="text" class="form-control commas-form text-end other_amount tax-amount" name="amount[]" id="amount_${key}" required placeholder="Masukkan Nominal" onkeyup="countTotal()" value="${formatRupiahWithDecimal(tax_amount)}" readonly />
                                        </td>
                                        <td>
                                        </td>
                                    </tr>`;

                $('#cash-advance-detail-data').html(append_element);

                initCommasForm();

                countTotal();
            },
        })

    } else {
        $('.row-tax').remove();
        $('.tax_number_input').addClass('d-none');
        countTotal();
    }
}
