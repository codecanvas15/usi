var key = 0;

function addFundSubmissionSupplierOtherRow() {
    key += 1;
    var append_element = `<tr id="fund-submission-supplier-other-${key}">
                            <td>
                                <input type="hidden" name="fund_submission_supplier_other_id[]" value="" />
                                <select name="coa_detail_id[]" id="coa_detail_id_${key}" class="form-control" required autofocus style="width:100%">
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control" name="note_other[]" id="note_other_${key}" required placeholder="Masukkan Keterangan" />
                            </td>
                            <td>
                                <input type="text" class="form-control commas-form text-end" name="debit[]" id="debit_${key}" required placeholder="Masukkan Nominal" onkeyup="countTotal()" value="" />
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm" onclick="$('#fund-submission-supplier-other-${key}').remove();countTotal()"><i class="fa fa-times"></i></button>
                            </td>
                        </tr>`;

    $("#fund-submission-supplier-other-data").append(append_element);

    initCoaSelect(`#coa_detail_id_${key}`);
    initCommasForm();
}

function countTotal() {
    let amount_total = 0;
    let count_debit_total = 0;
    let total_debit_elements = $('input[name="debit[]"]');

    $.each(total_debit_elements, function (e) {
        count_debit_total += thousandToFloat($(this).val());
    });

    $('input[name="amount[]"]').each(function () {
        amount_total += parseFloat($(this).val());
    });

    $("#debit_total").text(formatRupiahWithDecimal(count_debit_total));
    $("#debit_total_hide").val(count_debit_total);

    $("#total-cash-advance").html(
        formatRupiahWithDecimal(amount_total + count_debit_total)
    );

    calculate_final_total()
}
