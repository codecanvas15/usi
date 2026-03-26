var data_invoice_select = $("#data_invoice_select");
var selected_invoice_table = $("#selected_invoice_table");
var branch_id = $("#branch_id");
var customer_id = $("#customer_id");
var currency_id = $("#currency_id");
var local_currency_id = $("#local_currency_id");
var invoice_currency_id = $("#supplier_invoice_currency_id");
var invoiceSelectModal = $("#invoiceSelectModal");
var invoiceEditModal = $("#invoiceEditModal");

let totalDataInvoice = $("#total-data-invoice");
let totalDataSupplierInvoice = $("#total-data-supplier-invoice");
let totalDataReturn = $("#total-data-return");
let totalDataAdjustment = $("#total-data-adjustment");
let totalDataTotal = $("#total-data-total");

function getInvoiceSelect() {
    var except_id = [];
    $("input[name='invoice_id[]']").each(function () {
        except_id.push($(this).val());
    });

    if (customer_id.val() == undefined) {
        showAlert("", "Silahkan pilih customer terlebih dahulu", "warning");
        return false;
    }

    data_invoice_select.html("");
    $.ajax({
        type: "post",
        url: `${base_url}/receivables-payment/invoice-select`,
        data: {
            _token: token,
            customer_id: customer_id.val(),
            branch_id: branch_id.val(),
            currency_id: invoice_currency_id.val(),
            except_id: except_id,
            date: function () {
                return $('#date').val();
            }
        },
        success: function (data) {
            $.each(data, function (index, value) {
                if (value.outstanding_amount != 0) {
                    var html = `<tr>
                                    <td>${localDate(value.date)}</td>
                                    <td>${value.code} (${value.type})</td>
                                    <td>${value.due_date}</td>
                                    <td class="text-end">${value.currency.kode}</td>
                                    <td class="text-end">${formatRupiahWithDecimal(value.exchange_rate)}</td>
                                    <td class="text-end">${formatRupiahWithDecimal(value.total)}</td>
                                    <td class="text-end">${formatRupiahWithDecimal(value.outstanding_amount)}</td>
                                    <td>
                                        <input type="checkboxs" class="ichack-input" name="check_invoice_id[]" value="${value.id}" id="invoice_id${value.id}">
                                    </td>
                                </tr>`;

                    data_invoice_select.append(html);
                }
            });

            $('input[type="checkboxs"]').each(function (e) {
                $(this)
                    .attr("type", "checkbox")
                    .css("position", "unset")
                    .css("opacity", "unset")
                    .css("position", "unset");
            });
            invoiceSelectModal.modal("show");
        },
    });
}

function saveSelectedInvoice() {
    var array = [];
    $("input:checkbox[name='check_invoice_id[]']:checked").each(function () {
        array.push($(this).val());
    });

    if (array.length == 0) {
        showAlert("", "Pilih Invoice Untuk Dibayarkan!", "warning");
        return false;
    }

    invoiceSelectModal.modal("hide");

    $.ajax({
        type: "post",
        url: `${base_url}/receivables-payment/invoice-select`,
        data: {
            _token: token,
            customer_id: customer_id.val(),
            selected_id: array,
            currency_id: invoice_currency_id.val(),
        },
        success: function (data) {
            $.each(data, function (index, value) {
                var html = `<tr id="selected_invoice_row_${value.id}">
                                <td>
                                    ${value.date}
                                    <input type="hidden" name="invoice_id[]" value="${value.id}">
                                    <input type="hidden" name="fund_submission_customer_id[]" value="">
                                </td>
                                <td>${value.code} (${value.type})</td>
                                <td>${value.currency.kode}</td>
                                <td class="text-end">${formatRupiahWithDecimal(value.exchange_rate)}
                                    <input type="hidden" id="exchange_rate_customer_${value.id}" value="${value.exchange_rate}">
                                </td>
                                <td class="text-end">
                                    <span id="outstanding_amount_text_customer_${value.id}">${formatRupiahWithDecimal(value.outstanding_amount)}</span>
                                    <input type="hidden" id="outstanding_amount_customer_${value.id}" name="outstanding_amount_customer[]" value="${parseFloat(value.outstanding_amount)}">
                                </td>
                                <td class="text-end">
                                    <span id="receive_amount_text_customer_${value.id}">${formatRupiahWithDecimal(0)}</span>
                                    <input type="hidden" id="receive_amount_customer_${value.id}" name="receive_amount_customer[]" value="${0}">
                                </td>
                                <td class="text-end column-multi-currency">
                                    <span id="receive_amount_foreign_text_customer_${value.id}">${formatRupiahWithDecimal(0)}</span>
                                    <input type="hidden" id="receive_amount_foreign_customer_${value.id}" name="receive_amount_foreign_customer[]" value="${0}">
                                    <input type="hidden" id="receive_amount_gap_foreign_customer_${value.id}" name="receive_amount_gap_foreign_customer[]" value="${0}">
                                    <input type="hidden" id="is_clearing_customer_${value.id}" name="is_clearing_customer[]" value="0">
                                    <input type="hidden" id="clearing_coa_id_customer_${value.id}" name="clearing_coa_id_customer[]" value="">
                                    <input type="hidden" id="clearing_coa_name_customer_${value.id}" value="">
                                </td>
                                <td class="text-end">
                                    <span id="receive_amount_gap_foreign_text_${value.id}">0</span>
                                </td>
                                <td class="text-end">
                                    <span id="exchange_rate_gap_text_customer_${value.id}">${formatRupiahWithDecimal(0)}</span>
                                    <input type="hidden" id="exchange_rate_gap_customer_${value.id}" name="exchange_rate_gap_customer[]" value="${0}">
                                </td>
                                <td>
                                    <span id="note_text_customer_${value.id}"></span>
                                    <input type="hidden" id="note_customer_${value.id}" name="note_customer[]" value="">
                                    <input type="hidden" id="clearing_note_customer_${value.id}" name="clearing_note_customer[]" value="">
                                    <input type="hidden" id="exchange_rate_gap_note_customer_${value.id}" name="exchange_rate_gap_note_customer[]" value="">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="editSelectedInvoice(${value.id})">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="$('#selected_invoice_row_${value.id}').remove();calculateDataCustomer()">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>`;

                selected_invoice_table.append(html);
            });
            calculateDataCustomer();
        },
    });
}

function editSelectedInvoice(id) {
    $("#btn-update-selected-invoice").attr(
        "onclick",
        `updateSelectedInvoice(${id})`
    );
    $("#edited_invoice_id").val(id);
    var date_edit = $("#date_edit_customer");
    var kode_edit = $("#kode_edit_customer");
    var coa_edit = $("#coa_edit_customer");
    var currency_edit = $("#currency_edit_customer");
    var exchange_rate_edit = $("#exchange_rate_edit_customer");
    var exchange_rate_gap_edit = $("#exchange_rate_gap_edit_customer");
    var total_amount_edit = $("#total_amount_edit_customer");
    var paid_amount_edit = $("#paid_amount_edit_customer");
    var outstanding_amount_edit = $("#outstanding_amount_edit_customer");
    var receive_amount_saved = $("#receive_amount_customer_" + id);
    var receive_amount_edit = $("#receive_amount_edit_customer");
    var receive_amount_foreign_saved = $("#receive_amount_foreign_customer_" + id);
    var receive_amount_foreign_edit = $("#receive_amount_foreign_edit_customer");
    var note_saved = $("#note_customer_" + id);
    var note_edit = $("#note_edit_customer");
    var clearing_note_saved = $("#clearing_note_customer_" + id);
    var clearing_note_edit = $("#clearing_note_edit_customer");
    var exchange_rate_gap_note_saved = $("#exchange_rate_gap_note_customer_" + id);
    var exchange_rate_gap_note_edit = $("#exchange_rate_gap_note_edit_customer");
    var exchange_rate_gap_saved = $("#exchange_rate_gap_customer_" + id);
    exchange_rate_gap_edit.val(
        formatRupiahWithDecimal(exchange_rate_gap_saved.val())
    );

    var clearing_edit = $("#clearing_customer");
    var clearing_coa_form = $("#clearing_coa_form_customer");
    var clearing_coa_id_edit = $("#clearing_coa_id_customer");
    var receive_amount_gap_foreign_edit = $("#receive_amount_gap_foreign_edit_customer");

    var is_clearing = $("#is_clearing_customer_" + id);
    var clearing_coa_id = $("#clearing_coa_id_customer_" + id);
    var clearing_coa_name = $("#clearing_coa_name_customer_" + id);
    var receive_amount_gap_foreign = $("#receive_amount_gap_foreign_customer_" + id);

    if (is_clearing.val() == 1) {
        clearing_edit.prop("checked", true);
        clearing_coa_form.removeClass("d-none");
    } else {
        clearing_edit.prop("checked", false);
        clearing_coa_form.addClass("d-none");
        clearing_coa_id_edit.html(``);
    }

    if (exchange_rate_gap_note_saved.val() != 0) {
        $("#exchange_rate_gap_form_customer").removeClass("d-none");
    } else {
        $("#exchange_rate_gap_form_customer").addClass("d-none");
    }

    if (clearing_coa_name.val() != "") {
        clearing_coa_id_edit.append(
            `<option value="${clearing_coa_id.val()}" selected> ${clearing_coa_name.val()}</option>`
        );
    } else {
        clearing_coa_id_edit.append(
            `<option value="${$("#default_clearing_coa_id_customer").val()}" selected>${$("#default_clearing_coa_name_customer").val()}</option>`
        );
    }

    if (receive_amount_saved.val() != 0) {
        receive_amount_edit.val(
            formatRupiahWithDecimal(receive_amount_saved.val())
        );
    } else {
        receive_amount_edit.val("");
    }
    receive_amount_foreign_edit.val(
        formatRupiahWithDecimal(receive_amount_foreign_saved.val())
    );
    receive_amount_gap_foreign_edit.val(
        formatRupiahWithDecimal(receive_amount_gap_foreign.val())
    );
    note_edit.val(note_saved.val());
    clearing_note_edit.val(clearing_note_saved.val());
    exchange_rate_gap_note_edit.val(exchange_rate_gap_note_saved.val());

    $.ajax({
        type: "post",
        url: `${base_url}/receivables-payment/invoice-select`,
        data: {
            _token: token,
            customer_id: customer_id.val(),
            invoice_id: id,
        },
        success: function (data) {
            date_edit.val(data.date);
            kode_edit.val(data.code);
            $.each(data.customer.customer_coas, function (index, value) {
                if (value.tipe == "Account Receivable Coa") {
                    coa_edit.val(
                        `${value.coa.account_code} - ${value.coa.name}`
                    );
                }
            });
            currency_edit.val(data.currency.nama);
            exchange_rate_edit.val(formatRupiahWithDecimal(data.exchange_rate));
            total_amount_edit.val(formatRupiahWithDecimal(data.total));
            paid_amount_edit.val(formatRupiahWithDecimal(data.paid_amount));
            outstanding_amount_edit.val(
                formatRupiahWithDecimal(data.outstanding_amount)
            );
            $('label[for="receive_amount_edit_customer"]').text(
                `Jumlah Bayar ${$("#currency_id_symbol").val()}`
            );
            $('label[for="receive_amount_foreign_edit_customer"]').text(
                `Jumlah Bayar ${data.currency.kode}`
            );
            invoiceEditModal.modal("show");

            if (
                currency_id.val() == local_currency_id.val() &&
                currency_id.val() == invoice_currency_id.val()
            ) {
                $(".column-multi-currency").addClass("d-none");
                $("#multi-currency-form-customer").hide();
            } else {
                $(".column-multi-currency").removeClass("d-none");
                $("#multi-currency-form-customer").show();
            }
        },
    });
}

function updateSelectedInvoice(id) {
    var receive_amount_saved = $("#receive_amount_customer_" + id);
    var receive_amount_saved_text = $("#receive_amount_text_customer_" + id);
    var receive_amount_edit = $("#receive_amount_edit_customer");

    var receive_amount_foreign_saved = $("#receive_amount_foreign_customer_" + id);
    var receive_amount_foreign_saved_text = $(
        "#receive_amount_foreign_text_customer_" + id
    );
    var receive_amount_foreign_edit = $("#receive_amount_foreign_edit_customer");

    var note_saved = $("#note_customer_" + id);
    var note_text_saved = $("#note_text_customer_" + id);
    var note_edit = $("#note_edit_customer");
    var clearing_note_saved = $("#clearing_note_customer_" + id);
    var clearing_note_edit = $("#clearing_note_edit_customer");

    var exchange_rate_gap_note_saved = $("#exchange_rate_gap_note_customer_" + id);
    var exchange_rate_gap_note_edit = $("#exchange_rate_gap_note_edit_customer");

    var exchange_rate_saved = $("#exchange_rate_customer_" + id);
    var exchange_rate_text_saved = $("#exchange_rate_text_customer_" + id);
    var exchange_rate_edit = $("#exchange_rate_edit_customer");

    var exchange_rate_gap_saved = $("#exchange_rate_gap_customer_" + id);
    var exchange_rate_gap_saved_text = $("#exchange_rate_gap_text_customer_" + id);
    var exchange_rate_gap_edit = $("#exchange_rate_gap_edit_customer");

    var receive_amount_gap_foreign_saved = $(
        "#receive_amount_gap_foreign_customer_" + id
    );
    var receive_amount_gap_foreign_text_saved = $("#receive_amount_gap_foreign_text_" + id);
    var receive_amount_gap_foreign_edit = $("#receive_amount_gap_foreign_edit_customer");

    var clearing_edit = $("#clearing_customer");
    var clearing_coa_id_edit = $("#clearing_coa_id_customer");

    var is_clearing = $("#is_clearing_customer_" + id);
    var clearing_coa_id = $("#clearing_coa_id_customer_" + id);
    var clearing_coa_name = $("#clearing_coa_name_customer_" + id);

    var empty_input = $("#invoiceEditModal")
        .find(":input[required]:visible")
        .filter(function () {
            return this.value == "";
        });

    if (empty_input.length > 0) {
        showAlert("", "Periksa kembali inputan yang masih kosong!", "warning");
        return false;
    }

    receive_amount_foreign_saved.val(
        thousandToFloat(receive_amount_foreign_edit.val())
    );
    receive_amount_foreign_saved_text.text(receive_amount_foreign_edit.val());
    receive_amount_saved.val(thousandToFloat(receive_amount_edit.val()));
    receive_amount_saved_text.text(receive_amount_edit.val());
    note_saved.val(note_edit.val());
    note_text_saved.text(note_edit.val());
    clearing_note_saved.val(clearing_note_edit.val());
    exchange_rate_gap_note_saved.val(exchange_rate_gap_note_edit.val());
    exchange_rate_saved.val(thousandToFloat(exchange_rate_edit.val()));
    exchange_rate_text_saved.text(exchange_rate_edit.val());
    exchange_rate_gap_saved.val(thousandToFloat(exchange_rate_gap_edit.val()));
    exchange_rate_gap_saved_text.text(exchange_rate_gap_edit.val());
    receive_amount_gap_foreign_saved.val(
        thousandToFloat(receive_amount_gap_foreign_edit.val())
    );
    receive_amount_gap_foreign_text_saved.text(receive_amount_gap_foreign_edit.val());

    if (clearing_edit.is(":checked")) {
        is_clearing.val(1);
        clearing_coa_id.val(clearing_coa_id_edit.val());
        clearing_coa_name.val($("#clearing_coa_id_customer option:selected").text());
    } else {
        is_clearing.val(0);
    }

    $("#date_edit_customer").val("");
    $("#kode_edit_customer").val("");
    $("#coa_edit_customer").val("");
    $("#currency_edit_customer").val("");
    $("#exchange_rate_edit_customer").val("");
    $("#total_amount_edit_customer").val("");
    $("#paid_amount_edit_customer").val("");
    $("#outstanding_amount_edit_customer").val("");
    $("#exchange_rate_gap_edit_customer").val("0");
    $("#receive_amount_foreign_edit_customer").val("");
    $("#receive_amount_gap_foreign_edit_customer").val("");
    $("#clearing_customer").prop("checked", false);

    invoiceEditModal.modal("hide");

    calculateDataCustomer();
}

function calculateGapEditCustomer(currency) {
    // var id = $('#edited_invoice_id');
    var exchange_rate = thousandToFloat($("#exchange_rate").val());
    var outstanding_amount_edit = thousandToFloat(
        $("#outstanding_amount_edit_customer").val()
    );
    var exchange_rate_edit = thousandToFloat($("#exchange_rate_edit_customer").val());
    var receive_amount_edit = $("#receive_amount_edit_customer");
    var receive_amount_edit_val = thousandToFloat(
        $("#receive_amount_edit_customer").val()
    );
    var receive_amount_foreign_edit = $("#receive_amount_foreign_edit_customer");
    var receive_amount_foreign_edit_val = $(
        "#receive_amount_foreign_edit_customer"
    ).val();
    var receive_amount_gap_foreign_edit = $("#receive_amount_gap_foreign_edit_customer");
    var total_amount_edit = thousandToFloat($("#total_amount_edit_customer").val());

    if (currency == "local") {
        var receive_amount_foreign = receive_amount_edit_val / exchange_rate;
        // CURRENCY AR = CURRENCY INV
        if (currency_id.val() == invoice_currency_id.val()) {
            receive_amount_foreign = receive_amount_edit_val;
        }
        // CURRENCY LOCAL = CURRENCY INVOICE
        if (invoice_currency_id.val() == local_currency_id.val()) {
            receive_amount_foreign = receive_amount_edit_val * exchange_rate;
        }
        receive_amount_foreign_edit.val(
            formatRupiahWithDecimal(receive_amount_foreign)
        );
    } else {
        var receive_amount = receive_amount_foreign_edit_val * exchange_rate;
        // CURRENCY AR = CURRENCY INV
        if (currency_id.val() == invoice_currency_id.val()) {
            receive_amount = receive_amount_foreign_edit_val;
        }
        // CURRENCY LOCAL = CURRENCY INVOICE
        if (invoice_currency_id.val() == local_currency_id.val()) {
            receive_amount = receive_amount_foreign_edit_val / exchange_rate;
        }
        receive_amount_edit.val(formatRupiahWithDecimal(receive_amount));
    }

    var final_foreign = thousandToFloat(
        $("#receive_amount_foreign_edit_customer").val()
    );

    // var gap = (exchange_rate - exchange_rate_edit) * final_foreign.toFixed(2);
    var gap = total_amount_edit * exchange_rate_edit - total_amount_edit * exchange_rate;
    // CURRENCY LOCAL = CURRENCY INVOICE
    if (invoice_currency_id.val() == local_currency_id.val()) {
        gap = 0;
    }

    if (outstanding_amount_edit == final_foreign) {
        $("#clearing_customer").attr("disabled", true).prop("checked", false);
        $("#clearing_coa_form_customer").addClass("d-none");
    } else {
        $("#clearing_customer").attr("disabled", false);
    }

    if (outstanding_amount_edit < final_foreign) {
        $(".amount-alert").remove();
        $("#receive_amount_gap_foreign_edit_customer").after('<span class="text-danger amount-alert">Jumlah bayar melebihi nilai sisa</span>');
    } else {
        $(".amount-alert").remove();
    }

    receive_amount_gap_foreign_edit.val(
        formatRupiahWithDecimal(outstanding_amount_edit - final_foreign)
    );
    $("#exchange_rate_gap_edit_customer").val(formatRupiahWithDecimal(gap));
    if (gap != 0) {
        $("#exchange_rate_gap_form_customer").removeClass("d-none");
    } else {
        $("#exchange_rate_gap_form_customer").addClass("d-none");
    }
}

function calculateDataCustomer() {
    var outstanding_amount_total = 0;
    var receive_amount_total = 0;
    var receive_amount_foreign_total = 0;
    var exchange_rate_gap_total = 0;

    if (
        currency_id.val() == local_currency_id.val() &&
        currency_id.val() == invoice_currency_id.val()
    ) {
        $(".column-multi-currency").addClass("d-none");
        $("#multi-currency-form-customer").hide();
    } else {
        $(".column-multi-currency").removeClass("d-none");
        $("#multi-currency-form-customer").show();
    }

    if ($('input[name="outstanding_amount_customer[]"]').length > 0) {
        $(".empty_invoice_row").remove();
    } else {
        $("#selected_invoice_table").html(`<tr class="empty_invoice_row">
            <td colspan="10" class="text-center">Belum ada invoice</td>
        </tr>`);
    }

    $('input[name="outstanding_amount_customer[]"]').each(function () {
        outstanding_amount_total += parseFloat($(this).val());
    });

    $('input[name="receive_amount_customer[]"]').each(function () {
        receive_amount_total += parseFloat($(this).val());
    });

    $('input[name="receive_amount_foreign_customer[]"]').each(function () {
        receive_amount_foreign_total += parseFloat($(this).val());
    });

    $('input[name="exchange_rate_gap_customer[]"]').each(function () {
        exchange_rate_gap_total += parseFloat($(this).val());
    });

    $("#outstanding_amount_total_customer").text(
        formatRupiahWithDecimal(outstanding_amount_total)
    );
    $("#receive_amount_total_customer").text(
        formatRupiahWithDecimal(receive_amount_total)
    );
    $("#receive_amount_foreign_total_customer").text(
        formatRupiahWithDecimal(receive_amount_foreign_total)
    );
    $("#exchange_rate_gap_total_customer").text(
        formatRupiahWithDecimal(exchange_rate_gap_total)
    );

    let check_selected_data_length = $('input[name="outstanding_amount_customer[]"]').length;
    if ((check_selected_data_length > 0) || ($('#exchange_rate').data('is-both-local') == "true")) {
        $('#exchange_rate').attr('readonly', true);
    } else {
        $('#exchange_rate').attr('readonly', false);
    }

    calculate_final_total();
}

$("#form-data").submit(function (e) {
    var find_zero_amount = $('input[name="receive_amount_customer[]"]').filter(
        function () {
            return parseFloat(thousandToFloat(this.value)) == 0;
        }
    ).length;

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

function clearingCustomer() {
    if ($("#clearing_customer").is(":checked")) {
        $("#clearing_coa_form_customer").removeClass("d-none");
    } else {
        $("#clearing_coa_form_customer").addClass("d-none");
    }
}

