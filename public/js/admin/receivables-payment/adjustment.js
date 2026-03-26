var key = 0;

function addReceivablesPaymentOtherRow() {
    key += 1;
    var append_element = `<tr id="receivables-payment-other-${key}">
                            <td>
                                <input type="hidden" name="receivables_payment_other_id[]" value="" />
                                <select name="coa_detail_id[]" id="coa_detail_id_${key}" class="form-control" required autofocus style="width:100%">
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control" name="note_other[]" id="note_other_${key}" required placeholder="Masukkan Keterangan" />
                            </td>
                            <td>
                                <input type="text" class="form-control commas-form text-end" name="credit[]" id="credit_${key}" required placeholder="Masukkan Nominal" onkeyup="countTotal()" value="0" />
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm" onclick="$('#receivables-payment-other-${key}').remove();countTotal()"><i class="fa fa-times"></i></button>
                            </td>
                        </tr>`;

    $("#receivables-payment-other-data").append(append_element);

    initCoaSelect(`#coa_detail_id_${key}`);
    initCommasForm();
}

function countTotal() {
    let count_credit_total = 0;
    let total_credit_elements = $('input[name="credit[]"]');

    $.each(total_credit_elements, function (e) {
        count_credit_total += thousandToFloat($(this).val());
    });

    $("#credit_total").text(formatRupiahWithDecimal(count_credit_total));
    $("#credit_total_hide").val(count_credit_total);

    // * change total data
    calculate_final_total();
}
