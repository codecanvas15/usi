var data_return_select = $("#data_return_select");
var selected_return_table = $("#selected_return_table");
var returnSelectModal = $("#returnSelectModal");
var returnEditModal = $("#returnEditModal");

let returnIndex = 0;

function getReturnSelect() {
    var except_id = [];
    $("input[name='purchase_return_id[]']").each(function () {
        except_id.push($(this).val());
    });

    if (vendor_id.val() == undefined) {
        showAlert("", "Pilih vendor terlebih dahulu", "warning");
        return false;
    }

    data_return_select.html("");
    $.ajax({
        type: "post",
        url: `${base_url}/fund-submission/purchase-return-select`,
        data: {
            _token: token,
            vendor_id: vendor_id.val(),
            branch_id: branch_id.val(),
            currency_id: supplier_invoice_currency_id.val(),
            except_id: except_id,
            fund_submission_id: $("#fund_submission_id").val(),
            date: $('#date').val(),

        },
        success: function (data) {
            $.each(data, function (index, value) {
                if (value.outstanding != 0) {
                    var html = `<tr>
                                    <td>${localDate(value.date)}</td>
                                    <td>${value.code}</td>
                                    <td class="text-end">${value.currency.kode}</td>
                                    <td class="text-end">${formatRupiahWithDecimal(value.exchange_rate)}</td>
                                    <td class="text-end">${formatRupiahWithDecimal(value.total)}</td>
                                    <td class="text-end">${formatRupiahWithDecimal(value.outstanding)}</td>
                                    <td>
                                        <input type="checkboxs" class="ichack-input" name="check_purchase_return_id[]" value="${value.id}" id="purchase_return_id${value.id}">
                                    </td>
                                </tr>`;

                    data_return_select.append(html);
                }

            });

            $('input[type="checkboxs"]').each(function (e) {
                $(this)
                    .attr("type", "checkbox")
                    .css("position", "unset")
                    .css("opacity", "unset")
                    .css("position", "unset");
            });
            returnSelectModal.modal("show");
        },
    });
}

function saveSelectedReturn() {
    var array = [];
    $("input:checkbox[name='check_purchase_return_id[]']:checked").each(
        function () {
            array.push($(this).val());
        }
    );

    if (array.length == 0) {
        showAlert("", "Pilih Retur Untuk Dibayarkan!", "warning");
        return false;
    }

    returnSelectModal.modal("hide");

    $.ajax({
        type: "post",
        url: `${base_url}/fund-submission/purchase-return-select`,
        data: {
            _token: token,
            vendor_id: vendor_id.val(),
            branch_id: branch_id.val(),
            selected_id: array,
            currency_id: supplier_invoice_currency_id.val(),
            fund_submission_id: $("#fund_submission_id").val()
        },
        success: function (data) {
            if (data.length > 0) {
                $("#row_return_empty").addClass("d-none");
            }

            $.each(data, function (index, value) {
                var html = `<tr id="selected_return_row_${value.id}">
                                <td>
                                    ${value.date}
                                    <input type="hidden" name="fund_submission_return_id[]" value="">
                                    <input type="hidden" name="purchase_return_id[]" value="${value.id}">
                                    <input type="hidden" id="return_total_${value.id}" name="return_total[]" value="${value.total}">
                                </td>
                                <td>${value.code}</td>
                                <td>${value.currency.nama}</td>
                                <td class="text-end">
                                    <input type="text" class="form-control commas-form text-end" id="return_exchange_rate_${value.id}" value="${formatRupiahWithDecimal(value.exchange_rate ?? 0)}" readonly>
                                </td>
                                <td class="text-end">
                                    <input type="text" class="form-control commas-form text-end" id="return_outstanding_amount_${value.id}" name="return_outstanding_amount[]" value="${formatRupiahWithDecimal(value.outstanding ?? 0)}" readonly>
                                </td>
                                <td class="text-end">
                                    <input type="text" class="form-control commas-form text-end" id="return_amount_${value.id}" name="return_amount[]" value="${0}" onkeyup="calculate_row_return(${value.id}, false)">
                                </td>
                                <td class="text-end d-none column-multi-currency">
                                    <input type="text" class="form-control commas-form text-end" id="return_amount_foreign_${value.id}" name="return_amount_foreign[]" value="${0}" onkeyup="calculate_row_return(${value.id}, true)">
                                </td>
                                <td class="text-end">
                                    <input type="text" class="form-control commas-form text-end" id="return_exchange_rate_gap_${value.id}" name="return_exchange_rate_gap[]" value="${0}" readonly>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="$('#selected_return_row_${value.id}').remove();">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>`;

                selected_return_table.append(html);

                initCommasForm();
                calculateData();
            });
        },
    });
}


function calculate_row_return(index, is_foreign = false) {
    let row_return_outstanding_amount_element = $("#return_outstanding_amount_" + index);
    let row_return_amount_element = $("#return_amount_" + index);
    let row_return_amount_foreign_element = $("#return_amount_foreign_" + index);
    let row_return_exchange_rate_gap_element = $("#return_exchange_rate_gap_" + index);
    let row_return_exchange_rate_element = $("#return_exchange_rate_" + index);
    let row_return_total_element = $("#return_total_" + index);

    let exchange_rate = thousandToFloat($('#exchange_rate').val());
    let row_return_exchange_rate = thousandToFloat(row_return_exchange_rate_element.val());
    let return_amount = thousandToFloat(row_return_amount_element.val());
    let return_amount_foreign = thousandToFloat(row_return_amount_foreign_element.val());
    let return_outstanding_amount = thousandToFloat(row_return_outstanding_amount_element.val());

    if (currency_id.val() == supplier_invoice_currency_id.val()) {
        if (is_foreign) {
            return_amount = return_amount_foreign;
            row_return_amount_element.val(formatRupiahWithDecimal(return_amount));
            if (return_amount_foreign > return_outstanding_amount) {
                showAlert("", "Nilai Retur Tidak Boleh Melebihi Sisa Tagihan!", "warning");
                return_amount = return_outstanding_amount;
                row_return_amount_foreign_element.val(formatRupiahWithDecimal(return_outstanding_amount));
                row_return_amount_element.val(formatRupiahWithDecimal(return_outstanding_amount));
            }
        } else {
            return_amount_foreign = return_amount;
            row_return_amount_foreign_element.val(formatRupiahWithDecimal(return_amount_foreign));
            if (return_amount_foreign > return_outstanding_amount) {
                showAlert("", "Nilai Retur Tidak Boleh Melebihi Sisa Tagihan!", "warning");
                return_amount_foreign = return_outstanding_amount;
                row_return_amount_foreign_element.val(formatRupiahWithDecimal(return_outstanding_amount));
                row_return_amount_element.val(formatRupiahWithDecimal(return_outstanding_amount));
            }
        }
    } else {
        if (!is_foreign && currency_id.val() == local_currency_id.val()) {
            return_amount_foreign = return_amount / exchange_rate;
            row_return_amount_foreign_element.val(formatRupiahWithDecimal(return_amount_foreign));
            if (return_amount_foreign > return_outstanding_amount) {
                showAlert("", "Nilai Retur Tidak Boleh Melebihi Sisa Tagihan!", "warning");
                return_amount_foreign = return_outstanding_amount;
                row_return_amount_foreign_element.val(formatRupiahWithDecimal(return_outstanding_amount));
                row_return_amount_element.val(formatRupiahWithDecimal(return_outstanding_amount * exchange_rate));
            }
        } else if (!is_foreign && currency_id.val() != local_currency_id.val()) {
            return_amount_foreign = return_amount * exchange_rate;
            row_return_amount_foreign_element.val(formatRupiahWithDecimal(return_amount_foreign));
            if (return_amount_foreign > return_outstanding_amount) {
                showAlert("", "Nilai Retur Tidak Boleh Melebihi Sisa Tagihan!", "warning");
                return_amount_foreign = return_outstanding_amount;
                row_return_amount_foreign_element.val(formatRupiahWithDecimal(return_outstanding_amount));
                row_return_amount_element.val(formatRupiahWithDecimal(return_outstanding_amount / exchange_rate));
            }
        } else if (is_foreign && currency_id.val() == local_currency_id.val()) {
            return_amount = return_amount_foreign * exchange_rate;
            row_return_amount_element.val(formatRupiahWithDecimal(return_amount));
            if (return_amount_foreign > return_outstanding_amount) {
                showAlert("", "Nilai Retur Tidak Boleh Melebihi Sisa Tagihan!", "warning");
                return_amount = return_outstanding_amount;
                row_return_amount_foreign_element.val(formatRupiahWithDecimal(return_outstanding_amount));
                row_return_amount_element.val(formatRupiahWithDecimal(return_outstanding_amount * exchange_rate));
            }
        } else if (is_foreign && currency_id.val() != local_currency_id.val()) {
            return_amount = return_amount_foreign / exchange_rate;
            row_return_amount_element.val(formatRupiahWithDecimal(return_amount));
            if (return_amount_foreign > return_outstanding_amount) {
                showAlert("", "Nilai Retur Tidak Boleh Melebihi Sisa Tagihan!", "warning");
                return_amount = return_outstanding_amount;
                row_return_amount_foreign_element.val(formatRupiahWithDecimal(return_outstanding_amount));
                row_return_amount_element.val(formatRupiahWithDecimal(return_outstanding_amount / exchange_rate));
            }
        }
    }

    // var gap = (exchange_rate - row_return_exchange_rate) * return_amount_foreign.toFixed(2);
    var gap = row_return_total_element * row_return_exchange_rate - row_return_total_element * exchange_rate;
    if (supplier_invoice_currency_id.val() == local_currency_id.val()) {
        gap = 0;
    }
    row_return_exchange_rate_gap_element.val(formatRupiahWithDecimal(gap));

    calculate_return_total();
}

function calculate_return_total() {
    let return_outstanding_amount_total = 0;
    $("input[name='return_outstanding_amount[]']").each(function () {
        return_outstanding_amount_total += thousandToFloat($(this).val());
    });

    let return_amount_total = 0;
    $("input[name='return_amount[]']").each(function () {
        return_amount_total += thousandToFloat($(this).val());
    });

    let return_amount_foreign_total = 0;
    $("input[name='return_amount_foreign[]']").each(function () {
        return_amount_foreign_total += thousandToFloat($(this).val());
    });

    let return_exchange_rate_gap_total = 0;
    $("input[name='return_exchange_rate_gap[]']").each(function () {
        return_exchange_rate_gap_total += thousandToFloat($(this).val());
    });

    $("#return_outstanding_amount_total").text(formatRupiahWithDecimal(return_outstanding_amount_total));
    $("#return_amount_total").text(formatRupiahWithDecimal(return_amount_total));
    $("#return_amount_foreign_total").text(formatRupiahWithDecimal(return_amount_foreign_total));
    $("#return_exchange_rate_gap_total").text(formatRupiahWithDecimal(return_exchange_rate_gap_total));

    calculate_final_total();
}
