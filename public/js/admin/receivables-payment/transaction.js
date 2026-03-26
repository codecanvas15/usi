var data_invoice_select = $("#data_invoice_select");
var selected_invoice_table = $("#selected_invoice_table");
var branch_id = $("#branch_id");
var customer_id = $("#customer_id");
var currency_id = $("#currency_id");
var local_currency_id = $("#local_currency_id");
var invoice_currency_id = $("#invoice_currency_id");
var invoiceSelectModal = $("#invoiceSelectModal");
var invoiceEditModal = $("#invoiceEditModal");

let totalDataInvoice = $("#total-data-invoice");
let totalDataSupplierInvoice = $("#total-data-supplier-invoice");
let totalDataAdjustment = $("#total-data-adjustment");
let totalDataTotal = $("#total-data-total");
let totalDataReturn = $("#total-data-return");

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
                return $("#date").val();
            }
        },
        success: function (data) {
            $.each(data, function (index, value) {
                if (value.outstanding_amount_temp != 0) {
                    var html = `<tr>
                                    <td>${localDate(value.date)}</td>
                                    <td>${value.code} (${value.type})</td>
                                    <td>${localDate(value.due_date)}</td>
                                    <td class="text-end">${value.currency.kode}</td>
                                    <td class="text-end">${formatRupiahWithDecimal(value.exchange_rate)}</td>
                                    <td class="text-end">${formatRupiahWithDecimal(value.total)}</td>
                                    <td class="text-end">${formatRupiahWithDecimal(value.outstanding_amount_temp)}</td>
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
        showAlert("", "Pilih Purchase Invoice Untuk Dibayarkan!", "warning");
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
                                    ${localDate(value.date)}
                                    <input type="hidden" id="type_${value.id}" value="${value.type}">
                                    <input type="hidden" name="invoice_id[]" value="${value.id}">
                                    <input type="hidden" name="receivables_payment_detail_id[]" value="">
                                </td>
                                <td>${value.code} (${value.type})</td>
                                <td>${value.currency.kode}</td>
                                <td class="text-end">${formatRupiahWithDecimal(value.exchange_rate)}
                                    <input type="hidden" id="exchange_rate_${value.id}" value="${value.exchange_rate}">
                                </td>
                                <td class="text-end">
                                    <span id="outstanding_amount_text_${value.id}">${formatRupiahWithDecimal(value.outstanding_amount_temp)}</span>
                                    <input type="hidden" id="outstanding_amount_${value.id}" name="outstanding_amount[]" value="${parseFloat(value.outstanding_amount_temp)}">
                                </td>
                                <td class="text-end">
                                    <span id="receive_amount_text_${value.id}">${formatRupiahWithDecimal(0)}</span>
                                    <input type="hidden" id="receive_amount_${value.id}" name="receive_amount[]" value="${0}">
                                </td>
                                <td class="text-end column-multi-currency">
                                    <span id="receive_amount_foreign_text_${value.id}">${formatRupiahWithDecimal(0)}</span>
                                    <input type="hidden" id="receive_amount_foreign_${value.id}" name="receive_amount_foreign[]" value="${0}">
                                    <input type="hidden" id="receive_amount_gap_foreign_${value.id}" name="receive_amount_gap_foreign[]" value="${0}">
                                    <input type="hidden" id="is_clearing_${value.id}" name="is_clearing[]" value="0">
                                    <input type="hidden" id="clearing_coa_id_${value.id}" name="clearing_coa_id[]" value="">
                                    <input type="hidden" id="clearing_coa_name_${value.id}" value="">
                                </td>
                                <td class="text-end">
                                    <span id="receive_amount_gap_foreign_text_${value.id}">${formatRupiahWithDecimal(0)}</span>
                                </td>
                                <td class="text-end">
                                    <span id="exchange_rate_gap_text_${value.id}">${formatRupiahWithDecimal(0)}</span>
                                    <input type="hidden" id="exchange_rate_gap_${value.id}" name="exchange_rate_gap[]" value="${0}">
                                </td>
                                <td>
                                    <span id="note_text_${value.id}"></span>
                                    <input type="hidden" id="note_${value.id}" name="note[]" value="">
                                    <input type="hidden" id="clearing_note_${value.id}" name="clearing_note[]" value="">
                                    <input type="hidden" id="exchange_rate_gap_note_${value.id}" name="exchange_rate_gap_note[]" value="">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="editSelectedInvoice(${value.id})">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="$('#selected_invoice_row_${value.id}').remove();calculateData()">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>`;

                selected_invoice_table.append(html);
            });
            calculateData();
        },
    });
}

function editSelectedInvoice(id) {
    $("#btn-update-selected-invoice").attr(
        "onclick",
        `updateSelectedInvoice(${id})`
    );
    $("#edited_invoice_id").val(id);
    var date_edit = $("#date_edit");
    var kode_edit = $("#kode_edit");
    var coa_edit = $("#coa_edit");
    var currency_edit = $("#currency_edit");
    var exchange_rate_edit = $("#exchange_rate_edit");
    var exchange_rate_gap_edit = $("#exchange_rate_gap_edit");
    var total_amount_edit = $("#total_amount_edit");
    var paid_amount_edit = $("#paid_amount_edit");
    var outstanding_amount_edit = $("#outstanding_amount_edit");
    var receive_amount_saved = $("#receive_amount_" + id);
    var receive_amount_edit = $("#receive_amount_edit");
    var receive_amount_foreign_saved = $("#receive_amount_foreign_" + id);
    var receive_amount_foreign_edit = $("#receive_amount_foreign_edit");
    var note_saved = $("#note_" + id);
    var note_edit = $("#note_edit");
    var clearing_note_saved = $("#clearing_note_" + id);
    var clearing_note_edit = $("#clearing_note_edit");
    var exchange_rate_gap_note_saved = $("#exchange_rate_gap_note_" + id);
    var exchange_rate_gap_note_edit = $("#exchange_rate_gap_note_edit");
    var exchange_rate_gap_saved = $("#exchange_rate_gap_" + id);
    exchange_rate_gap_edit.val(
        formatRupiahWithDecimal(exchange_rate_gap_saved.val())
    );

    var clearing_edit = $("#clearing");
    var clearing_coa_form = $("#clearing_coa_form");
    var clearing_coa_id_edit = $("#clearing_coa_id");
    var receive_amount_gap_foreign_edit = $("#receive_amount_gap_foreign_edit");

    var is_clearing = $("#is_clearing_" + id);
    var clearing_coa_id = $("#clearing_coa_id_" + id);
    var clearing_coa_name = $("#clearing_coa_name_" + id);
    var receive_amount_gap_foreign = $("#receive_amount_gap_foreign_" + id);

    if (is_clearing.val() == 1) {
        clearing_edit.prop("checked", true);
        clearing_coa_form.removeClass("d-none");
    } else {
        clearing_edit.prop("checked", false);
        clearing_coa_form.addClass("d-none");
        clearing_coa_id_edit.html(``);
    }

    if (exchange_rate_gap_note_saved.val() != 0) {
        $("#exchange_rate_gap_form").removeClass("d-none");
    } else {
        $("#exchange_rate_gap_form").addClass("d-none");
    }

    if (clearing_coa_name.val() != "") {
        clearing_coa_id_edit.append(
            `<option value="${clearing_coa_id.val()}" selected> ${clearing_coa_name.val()}</option>`
        );
    } else {
        clearing_coa_id_edit.append(
            `<option value="${$(
                "#default_clearing_coa_id"
            ).val()}" selected>${$(
                "#default_clearing_coa_name"
            ).val()}</option>`
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
            receivable_payment_id: $('#receivables_payment_id').val(),
        },
        success: function (data) {
            date_edit.val(localDate(data.date));
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
                formatRupiahWithDecimal(data.outstanding_amount_temp)
            );
            $('label[for="receive_amount_edit"]').text(
                `Jumlah Bayar ${$("#currency_id_symbol").val()}`
            );
            $('label[for="receive_amount_foreign_edit"]').text(
                `Jumlah Bayar ${data.currency.kode}`
            );
            invoiceEditModal.modal("show");

            if (
                currency_id.val() == local_currency_id.val() &&
                currency_id.val() == invoice_currency_id.val()
            ) {
                $(".column-multi-currency").addClass("d-none");
                $("#multi-currency-form").hide();
            } else {
                $(".column-multi-currency").removeClass("d-none");
                $("#multi-currency-form").show();
            }
        },
    });
}

function updateSelectedInvoice(id) {
    var type_saved = $("#type_" + id);
    var outstanding_amount_edit = $("#outstanding_amount_edit");

    var receive_amount_saved = $("#receive_amount_" + id);
    var receive_amount_saved_text = $("#receive_amount_text_" + id);
    var receive_amount_edit = $("#receive_amount_edit");

    var receive_amount_foreign_saved = $("#receive_amount_foreign_" + id);
    var receive_amount_foreign_saved_text = $(
        "#receive_amount_foreign_text_" + id
    );
    var receive_amount_foreign_edit = $("#receive_amount_foreign_edit");

    var note_saved = $("#note_" + id);
    var note_text_saved = $("#note_text_" + id);
    var note_edit = $("#note_edit");
    var clearing_note_saved = $("#clearing_note_" + id);
    var clearing_note_edit = $("#clearing_note_edit");

    var exchange_rate_gap_note_saved = $("#exchange_rate_gap_note_" + id);
    var exchange_rate_gap_note_edit = $("#exchange_rate_gap_note_edit");

    var exchange_rate_saved = $("#exchange_rate_" + id);
    var exchange_rate_text_saved = $("#exchange_rate_text_" + id);
    var exchange_rate_edit = $("#exchange_rate_edit");

    var exchange_rate_gap_saved = $("#exchange_rate_gap_" + id);
    var exchange_rate_gap_saved_text = $("#exchange_rate_gap_text_" + id);
    var exchange_rate_gap_edit = $("#exchange_rate_gap_edit");

    var receive_amount_gap_foreign_saved = $(
        "#receive_amount_gap_foreign_" + id
    );
    var receive_amount_gap_foreign_text_saved = $(
        "#receive_amount_gap_foreign_text_" + id
    )
    var receive_amount_gap_foreign_edit = $("#receive_amount_gap_foreign_edit");

    var clearing_edit = $("#clearing");
    var clearing_coa_id_edit = $("#clearing_coa_id");

    var is_clearing = $("#is_clearing_" + id);
    var clearing_coa_id = $("#clearing_coa_id_" + id);
    var clearing_coa_name = $("#clearing_coa_name_" + id);

    if (type_saved.val() == 'down_payment') {
        if (thousandToFloat(outstanding_amount_edit.val()) != thousandToFloat(receive_amount_edit.val())) {
            alert('Jumlah bayar tidak sama dengan outstanding. Silahkan periksa kembali');

            return false;
        }
    }

    var empty_input = $("#invoiceEditModal")
        .find(":input[required]:visible")
        .filter(function () {
            return this.value == "";
        });

    if (empty_input.length > 0) {
        showAlert("", "Periksa kembali inputan yang masih kosong!", "warning");
        return false;
    }

    if (type_saved == 'down_payment') {
        if (condition) {

        }
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
    receive_amount_gap_foreign_text_saved.text(
        receive_amount_gap_foreign_edit.val()
    );

    if (clearing_edit.is(":checked")) {
        is_clearing.val(1);
        clearing_coa_id.val(clearing_coa_id_edit.val());
        clearing_coa_name.val($("#clearing_coa_id option:selected").text());
    } else {
        is_clearing.val(0);
    }

    $("#date_edit").val("");
    $("#kode_edit").val("");
    $("#coa_edit").val("");
    $("#currency_edit").val("");
    $("#exchange_rate_edit").val("");
    $("#total_amount_edit").val("");
    $("#paid_amount_edit").val("");
    $("#outstanding_amount_edit").val("");
    $("#exchange_rate_gap_edit").val("0");
    $("#receive_amount_foreign_edit").val("");
    $("#receive_amount_gap_foreign_edit").val("");
    $("#clearing").prop("checked", false);

    invoiceEditModal.modal("hide");

    calculateData();
}

function calculateGapEdit(currency) {
    // var id = $('#edited_invoice_id');
    var exchange_rate = thousandToFloat($("#exchange_rate").val());
    var outstanding_amount_edit = thousandToFloat(
        $("#outstanding_amount_edit").val()
    );
    var exchange_rate_edit = thousandToFloat($("#exchange_rate_edit").val());
    var receive_amount_edit = $("#receive_amount_edit");
    var receive_amount_edit_val = thousandToFloat(
        $("#receive_amount_edit").val()
    );
    var receive_amount_foreign_edit = $("#receive_amount_foreign_edit");
    var receive_amount_foreign_edit_val = $(
        "#receive_amount_foreign_edit"
    ).val();
    var receive_amount_gap_foreign_edit = $("#receive_amount_gap_foreign_edit");
    var total_amount_edit = thousandToFloat($("#total_amount_edit").val());

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
        $("#receive_amount_foreign_edit").val()
    );

    // var gap = (exchange_rate - exchange_rate_edit) * final_foreign.toFixed(2);
    var gap = total_amount_edit * exchange_rate_edit - total_amount_edit * exchange_rate;
    // CURRENCY LOCAL = CURRENCY INVOICE
    if (invoice_currency_id.val() == local_currency_id.val()) {
        gap = 0;
    }

    if (outstanding_amount_edit == final_foreign) {
        $("#clearing").attr("disabled", true).prop("checked", false);
        $("#clearing_coa_form").addClass("d-none");
    } else {
        $("#clearing").attr("disabled", false);
    }

    if (outstanding_amount_edit < final_foreign) {
        $(".amount-alert").remove();
        $("#receive_amount_gap_foreign_edit").after('<span class="text-danger amount-alert">Jumlah bayar melebihi nilai sisa</span>');
    } else {
        $(".amount-alert").remove();
    }

    receive_amount_gap_foreign_edit.val(
        formatRupiahWithDecimal(outstanding_amount_edit - final_foreign)
    );
    $("#exchange_rate_gap_edit").val(formatRupiahWithDecimal(gap));
    if (gap != 0) {
        $("#exchange_rate_gap_form").removeClass("d-none");
    } else {
        $("#exchange_rate_gap_form").addClass("d-none");
    }
}

function calculateData() {
    var outstanding_amount_total = 0;
    var receive_amount_total = 0;
    var receive_amount_foreign_total = 0;
    var exchange_rate_gap_total = 0;

    if (
        currency_id.val() == local_currency_id.val() &&
        currency_id.val() == invoice_currency_id.val()
    ) {
        $(".column-multi-currency").addClass("d-none");
        $("#multi-currency-form").hide();
    } else {
        $(".column-multi-currency").removeClass("d-none");
        $("#multi-currency-form").show();
    }

    if ($('input[name="outstanding_amount[]"]').length > 0) {
        $(".empty_invoice_row").remove();
    } else {
        $("#selected_invoice_table").html(`<tr class="empty_invoice_row">
            <td colspan="10" class="text-center">Belum ada invoice</td>
        </tr>`);
    }

    $('input[name="outstanding_amount[]"]').each(function () {
        outstanding_amount_total += parseFloat($(this).val());
    });

    $('input[name="receive_amount[]"]').each(function () {
        receive_amount_total += parseFloat($(this).val());
    });

    $('input[name="receive_amount_foreign[]"]').each(function () {
        receive_amount_foreign_total += parseFloat($(this).val());
    });

    $('input[name="exchange_rate_gap[]"]').each(function () {
        exchange_rate_gap_total += parseFloat($(this).val());
    });

    $("#outstanding_amount_total").text(
        formatRupiahWithDecimal(outstanding_amount_total)
    );
    $("#receive_amount_total").text(
        formatRupiahWithDecimal(receive_amount_total)
    );
    $("#receive_amount_foreign_total").text(
        formatRupiahWithDecimal(receive_amount_foreign_total)
    );
    $("#exchange_rate_gap_total").text(
        formatRupiahWithDecimal(exchange_rate_gap_total)
    );

    let check_selected_data_length = $('input[name="outstanding_amount[]"]').length;
    if ((check_selected_data_length > 0) || ($('#exchange_rate').data('is-both-local') == "true")) {
        $('#exchange_rate').attr('readonly', true);
    } else {
        $('#exchange_rate').attr('readonly', false);
    }

    calculate_final_total();
}

$("#form-data").submit(function (e) {
    var find_zero_amount = $('input[name="receive_amount[]"]').filter(
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

    // if ($('input[name="invoice_id[]"]').length == 0) {
    //     setTimeout(() => {
    //         $(this).find("input[type=submit]").prop("disabled", false);
    //         $(this).find("button[type=submit]").prop("disabled", false);
    //     }, 1000);

    //     e.preventDefault();
    //     showAlert("", "Pilih data invoice terlebih dahulu", "warning");

    //     return false;
    // }

    if ($("#is_giro").is(":checked")) {
        let total_amount = thousandToFloat($("#receive_amount_total").text());
        let other_total = parseFloat($("#credit_total_hide").val());
        let giro_outstanding_amount = parseFloat(
            $("#giro_outstanding_amount").val()
        );

        if (total_amount + other_total > giro_outstanding_amount) {
            setTimeout(() => {
                $(this).find("input[type=submit]").prop("disabled", false);
                $(this).find("button[type=submit]").prop("disabled", false);
            }, 1000);

            showAlert("", "Jumlah giro tidak cukup!", "warning");
            return false;
        }
    }
});

function clearing() {
    if ($("#clearing").is(":checked")) {
        $("#clearing_coa_form").removeClass("d-none");
    } else {
        $("#clearing_coa_form").addClass("d-none");
    }
}

const toggleGiroForm = (e) => {
    if ($(e).is(":checked")) {
        $(".giro-form").removeClass("d-none");
        $("#receive_payment_id").attr("required", true);
    } else {
        $(".giro-form").addClass("d-none");
        $("#receive_payment_id").attr("required", false);
        $("#receive_payment_id").val("").trigger("change");
        $("#cheque_no").text("");
        $("#due_date").html("");
        $("#from_bank").text("");
        $("#realization_bank").text("");
        $("#giro_amount").text("");
        $("#giro_outstanding_amount_text").text("");
        $("#giro_outstanding_amount").val("");
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
                $("#is_giro").prop("checked", false);
                $(".giro-form").find("input").attr("required", false).val("");
            }

            if (response.data.currency) {
                $('#currency_id').append(`<option selected value="${response.data.currency.id}">${response.data.currency.kode} - ${response.data.currency.nama}</option>`);
            }
        },
    });
};

const get_receive_payment = (e) => {
    $("#cheque_no").text("");
    $("#due_date").html("");
    $("#from_bank").text("");
    $("#realization_bank").text("");
    $("#giro_amount").text("");
    $("#giro_outstanding_amount_text").text("");
    $("#giro_outstanding_amount").val("");
    if ($(e).val() != "") {
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

                $("#cheque_no").text(data.data.cheque_no);
                $("#due_date").html(localDate(data.data.due_date) + due_badge);
                $("#from_bank").text(data.data.from_bank);
                $("#realization_bank").text(data.data.realization_bank);
                $("#giro_amount").text(
                    data.data.currency.kode +
                    " " +
                    formatRupiahWithDecimal(data.data.amount)
                );
                $("#giro_outstanding_amount_text").text(
                    data.data.currency.kode +
                    " " +
                    formatRupiahWithDecimal(data.data.outstanding_amount)
                );
                $("#giro_outstanding_amount").val(data.data.outstanding_amount);
            },
        });
    }
};

$("#customer_id").on("change", function () {
    $("#receive_payment_id").val("").trigger("change");
    $("#cheque_no").text("");
    $("#due_date").html("");
    $("#from_bank").text("");
    $("#realization_bank").text("");
    $("#giro_amount").text("");
    $("#giro_outstanding_amount_text").text("");
    $("#giro_outstanding_amount").val("");
});

const calculate_final_total = () => {
    // calculate invoice
    let total_invoice_payment = 0;
    $('input[name="receive_amount[]"]').each(function () {
        total_invoice_payment += parseFloat($(this).val());
    });

    // calculate adjustment
    let total_adjustment_payment = 0;
    $('input[name="credit[]"]').each(function () {
        total_adjustment_payment += thousandToFloat($(this).val());
    });

    // calculate supplier invoice
    let total_supplier_invoice_payment = 0;
    $('input[name="amount_vendor[]"]').each(function () {
        total_supplier_invoice_payment += parseFloat($(this).val());
    });
    total_supplier_invoice_payment *= -1;

    // calculate supplier invoice
    let total_return_amount = 0;
    $('input[name="return_amount[]"]').each(function () {
        total_return_amount += thousandToFloat($(this).val());
    });
    total_return_amount *= -1;

    // final total payment
    let final_total_payment = total_invoice_payment + total_adjustment_payment + total_supplier_invoice_payment + total_return_amount;
    totalDataInvoice.text(formatRupiahWithDecimal(total_invoice_payment));
    totalDataAdjustment.text(formatRupiahWithDecimal(total_adjustment_payment));
    totalDataSupplierInvoice.text(formatRupiahWithDecimal(total_supplier_invoice_payment));
    totalDataTotal.text(formatRupiahWithDecimal(final_total_payment));
    totalDataReturn.text(formatRupiahWithDecimal(total_return_amount));

    if (
        $('input[name="receive_amount[]"]').length > 0 ||
        $('input[name="amount_vendor[]"]').length > 0 ||
        $('input[name="return_amount[]"]').length > 0
    ) {
        $('#date').attr('readonly', true);
    } else {
        $('#date').attr('readonly', false);
    }
}

const get_customer_vendor = (e) => {
    $.ajax({
        type: "get",
        url: `${base_url}/customer-find-vendor/${$(e).val()}`,
        success: function (data) {
            if (data.data != null) {
                $("#vendor_id").val(data.data.id);
                $("#vendor-section").removeClass("d-none");
            } else {
                $("#vendor_id").val("");
                $("#vendor-section").addClass("d-none");
            }
        },
        error: function (xhr) {
            $("#vendor_id").val("");
            $("#vendor-section").addClass("d-none");
        }
    });
};
