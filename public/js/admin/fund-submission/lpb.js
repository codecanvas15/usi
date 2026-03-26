var data_supplier_invoice_select = $("#data_supplier_invoice_select");
var selected_supplier_invoice_table = $("#selected_supplier_invoice_table");
var branch_id = $("#branch_id");
var vendor_id = $("#to_id");
var currency_id = $("#currency_id");
var local_currency_id = $("#local_currency_id");
var supplier_invoice_currency_id = $("#supplier_invoice_currency_id");
var supplierInvoiceSelectModal = $("#supplierInvoiceSelectModal");
var supplierInvoiceEditModal = $("#supplierInvoiceEditModal");

let sipiId = 0;

function getSupplierInvoiceSelect() {
    var except_id = [];
    $("input[name='supplier_invoice_parent_id[]']").each(function () {
        except_id.push($(this).val());
    });

    if (vendor_id.val() == undefined) {
        showAlert("", "Pilih vendor terlebih dahulu", "warning");
        return false;
    }

    data_supplier_invoice_select.html("");
    $.ajax({
        type: "post",
        url: `${base_url}/fund-submission/supplier-invoice-select`,
        data: {
            _token: token,
            vendor_id: vendor_id.val(),
            branch_id: branch_id.val(),
            currency_id: supplier_invoice_currency_id.val(),
            except_id: except_id,
            fund_submission_id: $("#fund_submission_id").val(),
            date: function () {
                return $('#date').val();
            }
        },
        success: function (data) {
            $.each(data, function (index, value) {
                if (value.temp_outstanding != 0) {
                    var html = `<tr>
                                    <td>${localDate(value.date)}</td>
                                    <td>${value.code}</td>
                                    <td>${localDate(value.due_date)}</td>
                                    <td class="text-end">${value.currency.kode}</td>
                                    <td class="text-end">${formatRupiahWithDecimal(value.exchange_rate)}</td>
                                    <td class="text-end">${formatRupiahWithDecimal(value.total)}</td>
                                    <td class="text-end">${formatRupiahWithDecimal(value.temp_outstanding)}</td>
                                    <td>
                                        <input type="checkboxs" class="ichack-input" name="check_supplier_invoice_parent_id[]" value="${value.id}" id="supplier_invoice_parent_id${value.id}">
                                    </td>
                                </tr>`;

                    data_supplier_invoice_select.append(html);
                }

            });

            $('input[type="checkboxs"]').each(function (e) {
                $(this)
                    .attr("type", "checkbox")
                    .css("position", "unset")
                    .css("opacity", "unset")
                    .css("position", "unset");
            });
            supplierInvoiceSelectModal.modal("show");
        },
    });
}

function saveSelectedSupplierInvoice() {
    var array = [];
    $("input:checkbox[name='check_supplier_invoice_parent_id[]']:checked").each(
        function () {
            array.push($(this).val());
        }
    );

    if (array.length == 0) {
        showAlert("", "Pilih Purchase Invoice Untuk Dibayarkan!", "warning");
        return false;
    }

    supplierInvoiceSelectModal.modal("hide");

    $.ajax({
        type: "post",
        url: `${base_url}/fund-submission/supplier-invoice-select`,
        data: {
            _token: token,
            vendor_id: vendor_id.val(),
            branch_id: branch_id.val(),
            selected_id: array,
            currency_id: supplier_invoice_currency_id.val(),
            fund_submission_id: $("#fund_submission_id").val()
        },
        success: function (data) {
            $("#accordionPaymentInformation").html("");

            if (data.length > 0) {
                $("#row_supplier_invoice_empty").addClass("d-none");
            }

            $.each(data, function (index, value) {
                var item_receiving_reports = [];
                $.each(
                    value.item_receiving_reports,
                    function (index, item_receiving_report) {
                        if (item_receiving_report.outstanding_temp > 0) {
                            item_receiving_reports.push({
                                id: item_receiving_report.id,
                                code: item_receiving_report.kode,
                                outstanding: item_receiving_report.outstanding_temp,
                                amount: '',
                                amount_foreign: '',
                                is_has_cash_advance: item_receiving_report.is_has_cash_advance ?? false,
                            });
                        }
                    }
                );

                var html = `<tr id="selected_supplier_invoice_row_${value.id}">
                                <td>
                                    ${localDate(value.date)}
                                    <input type="hidden" name="fund_submission_supplier_detail_id[]" value="">
                                    <input type="hidden" name="supplier_invoice_parent_id[]" value="${value.id}">
                                    <input type="hidden" id="item_receiving_reports_${value.id}" name="item_receiving_reports[]" value='${JSON.stringify(item_receiving_reports)}'>
                                </td>
                                <td>${value.code}</td>
                                <td>${value.currency.nama}</td>
                                <td class="text-end">
                                    ${formatRupiahWithDecimal(value.exchange_rate ?? 0)}
                                    <input type="hidden" id="exchange_rate_${value.id}" value="${value.exchange_rate ?? 0}">
                                </td>
                                <td class="text-end">
                                    <span id="outstanding_amount_text_${value.id}">${formatRupiahWithDecimal(value.temp_outstanding)}</span>
                                    <input type="hidden" id="outstanding_amount_${value.id}" name="outstanding_amount[]" value="${parseFloat(value.temp_outstanding)}">
                                </td>
                                <td class="text-end">
                                    <span id="amount_text_${value.id}">${formatRupiahWithDecimal(0)}</span>
                                    <input type="hidden" id="amount_${value.id}" name="amount[]" value="${0}">
                                </td>
                                <td class="text-end column-multi-currency">
                                    <span id="amount_foreign_text_${value.id}">${formatRupiahWithDecimal(0)}</span>
                                    <input type="hidden" id="amount_foreign_${value.id}" name="amount_foreign[]" value="${0}">
                                    <input type="hidden" id="amount_gap_foreign_${value.id}" name="amount_gap_foreign[]" value="${0}">
                                    <input type="hidden" id="is_clearing_${value.id}" name="is_clearing[]" value="0">
                                    <input type="hidden" id="clearing_coa_id_${value.id}" name="clearing_coa_id[]" value="">
                                    <input type="hidden" id="clearing_coa_name_${value.id}" value="">
                                </td>
                                <td class="text-end">
                                    <span id="amount_gap_foreign_text_${value.id}">0</span>
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
                                    <button type="button" class="btn btn-sm btn-primary" onclick="editSelectedSupplierInvoice(${value.id})">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="$('#selected_supplier_invoice_row_${value.id}').remove();calculateData();$('#payment-info-${value.id}').remove()">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>`;

                // add payment information
                let payment_html = `<div class="accordion-item" id="payment-info-${value.id}">
                                        <h2 class="accordion-header" id="si-${sipiId}">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#si${sipiId}" aria-expanded="false" aria-controls="si${sipiId}">
                                                ${value.code}
                                            </button>
                                        </h2>
                                        <div id="si${sipiId}" class="accordion-collapse collapse" aria-labelledby="si-${sipiId}" data-bs-parent="#accordionPaymentInformation">
                                            <div class="accordion-body">
                                                <table class="table table-striped" id="supplier-invoice-${value.code}-table">
                                                    <thead>
                                                        <th>Tanggal</th>
                                                        <th>LPB</th>
                                                        <th>Total</th>
                                                        <th>Bayar</th>
                                                        <th>Keterangan</th>
                                                    </thead>
                                                    <tbody id="supplier-invoice-${value.code}-table-body">`;

                $.each(
                    value.supplier_invoice_payment,
                    function (payment_index, payment) {
                        payment_html += `<tr>
                                        <td>${payment.date}</td>
                                        <td>${payment.item_receiving_report?.kode ?? "-"}</td>
                                        <td>${value.currency.simbol} ${formatRupiahWithDecimal(payment.amount_to_pay)}</td>
                                        <td>${value.currency.simbol} ${formatRupiahWithDecimal(payment.pay_amount)}</td>
                                        <td>${payment.note}</td>
                                    </tr>`;
                        sipiId++;
                    }
                );
                payment_html += `</tbody>
                                </table>
                            </div>
                        </div>
                    </div>`;

                $("#accordionPaymentInformation").append(payment_html);

                selected_supplier_invoice_table.append(html);
                calculateData();
            });
        },
    });
}

var item_receiving_report_saved = [];

function editSelectedSupplierInvoice(id) {
    $("#btn-update-selected-supplier-invoice").attr(
        "onclick",
        `updateSelectedSupplierInvoice(${id})`
    );
    $("#edited_supplier_invoice_parent_id").val(id);
    var date_edit = $("#date_edit");
    var code_edit = $("#code_edit");
    var project_edit = $("#project_edit");
    var reference_edit = $("#reference_edit");
    var coa_edit = $("#coa_edit");
    var currency_edit = $("#currency_edit");
    var exchange_rate_edit = $("#exchange_rate_edit");
    var exchange_rate_gap_edit = $("#exchange_rate_gap_edit");
    var total_amount_edit = $("#total_amount_edit");
    var paid_amount_edit = $("#paid_amount_edit");
    var outstanding_amount_edit = $("#outstanding_amount_edit");
    var amount_saved = $("#amount_" + id);
    var amount_edit = $("#amount_edit");
    var amount_foreign_saved = $("#amount_foreign_" + id);
    var amount_foreign_edit = $("#amount_foreign_edit");
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
    var amount_gap_foreign_edit = $("#amount_gap_foreign_edit");

    var is_clearing = $("#is_clearing_" + id);
    var clearing_coa_id = $("#clearing_coa_id_" + id);
    var clearing_coa_name = $("#clearing_coa_name_" + id);
    var amount_gap_foreign = $("#amount_gap_foreign_" + id);

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

    if (amount_saved.val() != 0) {
        amount_edit.val(formatRupiahWithDecimal(amount_saved.val()));
    } else {
        amount_edit.val("");
    }
    amount_foreign_edit.val(
        formatRupiahWithDecimal(amount_foreign_saved.val())
    );
    amount_gap_foreign_edit.val(
        formatRupiahWithDecimal(amount_gap_foreign.val())
    );
    note_edit.val(note_saved.val());
    clearing_note_edit.val(clearing_note_saved.val());
    exchange_rate_gap_note_edit.val(exchange_rate_gap_note_saved.val());

    item_receiving_report_saved = $("#item_receiving_reports_" + id).val();
    item_receiving_report_saved = JSON.parse(item_receiving_report_saved);

    var lpb_rows = "";
    item_receiving_report_saved.forEach(function (value, index) {
        var cash_advance_badge = '';
        if (value.is_has_cash_advance) {
            cash_advance_badge = `<br>
            <span class="badge bg-warning"><i class="fa fa-exclamation-circle"></i> PO Memiliki Uang Muka</span>`;
        }
        lpb_rows += `<tr>
                        <td>${value.code} ${cash_advance_badge}</td>
                        <td>
                            <input class="form-control commas-form text-end" id="lpb_outstanding_${index}" value="${formatRupiahWithDecimal(value.outstanding)}" readonly/>
                        </td>
                        <td>
                            <input class="form-control commas-form text-end" id="lpb_amount_${index}" value="${formatRupiahWithDecimal(value.amount)}" onkeyup="updateLpbAmount(${index}, ${id}, 'local')"/>
                        </td>
                        <td class="column-multi-currency">
                            <input class="form-control commas-form text-end" id="lpb_amount_foreign_${index}" value="${formatRupiahWithDecimal(value.amount_foreign)}" onkeyup="updateLpbAmount(${index}, ${id}, 'foreign')"/>
                        </td>
                    </tr>`;
    });

    if (item_receiving_report_saved.length > 0) {
        $("#lpb-table-body").html(lpb_rows);
        initCommasForm();
        $("#lpb-table").removeClass("d-none");
        amount_edit.attr("readonly", true);
        amount_foreign_edit.attr("readonly", true);
    } else {
        $("#lpb-table-body").html("");
        $("#lpb-table").addClass("d-none");
        amount_edit.attr("readonly", false);
        amount_foreign_edit.attr("readonly", false);
    }

    $.ajax({
        type: "post",
        url: `${base_url}/fund-submission/supplier-invoice-select`,
        data: {
            _token: token,
            vendor_id: vendor_id.val(),
            branch_id: branch_id.val(),
            supplier_invoice_parent_id: id,
            currency_id: supplier_invoice_currency_id.val(),
            fund_submission_id: $("#fund_submission_id").val()
        },
        success: function (data) {
            date_edit.val(data.date);
            code_edit.val(data.code);
            reference_edit.val(data.reference);
            project_edit.val(data.project);
            $.each(data.vendor.vendor_coas, function (index, value) {
                if (value.type == "Account Payable Coa") {
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
                formatRupiahWithDecimal(data.temp_outstanding)
            );
            $('label[for="amount_edit"]').text(
                `Jumlah Bayar ${$("#currency_id_symbol").val()}`
            );
            $('label[for="amount_foreign_edit"]').text(
                `Jumlah Bayar ${data.currency.kode}`
            );
            $(".label-lpb-amount").text(
                `Jumlah Bayar ${$("#currency_id_symbol").val()}`
            );
            $(".label-lpb-foreign").text(`Jumlah Bayar ${data.currency.kode}`);
            supplierInvoiceEditModal.modal("show");

            if (
                currency_id.val() == local_currency_id.val() &&
                currency_id.val() == supplier_invoice_currency_id.val()
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

function updateLpbAmount(index, id, type) {
    var lpb_amount = $("#lpb_amount_" + index);
    var lpb_amount_foreign = $("#lpb_amount_foreign_" + index);
    var exchange_rate = thousandToFloat($("#exchange_rate").val());

    if (type == "local") {
        var lpb_amount_foreign_x =
            thousandToFloat(lpb_amount.val()) / exchange_rate;
        // CURRENCY AR = CURRENCY INV
        if (currency_id.val() == supplier_invoice_currency_id.val()) {
            lpb_amount_foreign_x = thousandToFloat(lpb_amount.val());
        }
        // CURRENCY LOCAL = CURRENCY INVOICE
        if (supplier_invoice_currency_id.val() == local_currency_id.val()) {
            lpb_amount_foreign_x =
                thousandToFloat(lpb_amount.val()) * exchange_rate;
        }
        lpb_amount_foreign.val(formatRupiahWithDecimal(lpb_amount_foreign_x));
    } else {
        var lpb_amount_x =
            thousandToFloat(lpb_amount_foreign.val()) * exchange_rate;
        // CURRENCY AR = CURRENCY INV
        if (currency_id.val() == supplier_invoice_currency_id.val()) {
            lpb_amount_x = thousandToFloat(lpb_amount_foreign.val());
        }
        // CURRENCY LOCAL = CURRENCY INVOICE
        if (supplier_invoice_currency_id.val() == local_currency_id.val()) {
            lpb_amount_x =
                thousandToFloat(lpb_amount_foreign.val()) / exchange_rate;
        }
        lpb_amount.val(formatRupiahWithDecimal(lpb_amount_x));
    }

    item_receiving_report_saved[index].amount_foreign = thousandToFloat(
        lpb_amount_foreign.val()
    );
    item_receiving_report_saved[index].amount = thousandToFloat(
        lpb_amount.val()
    );

    if (type == "local") {
        $("#amount_edit").val(
            formatRupiahWithDecimal(
                item_receiving_report_saved.reduce(
                    (total, item) => parseFloat(item.amount) + total,
                    0
                )
            )
        );
        calculateGapEdit("local");
    } else {
        $("#amount_foreign_edit").val(
            formatRupiahWithDecimal(
                item_receiving_report_saved.reduce(
                    (total_foreign, item) =>
                        parseFloat(item.amount_foreign) + total_foreign,
                    0
                )
            )
        );
        calculateGapEdit("foreign");
    }
}

function updateSelectedSupplierInvoice(id) {
    var outstanding_amount_saved = $("#outstanding_amount_" + id);
    var outstanding_amount_text_saved = $("#outstanding_amount_text_" + id);
    var outstanding_amount_edit = $("#outstanding_amount_edit");

    var amount_saved = $("#amount_" + id);
    var amount_saved_text = $("#amount_text_" + id);
    var amount_edit = $("#amount_edit");

    var amount_foreign_saved = $("#amount_foreign_" + id);
    var amount_foreign_saved_text = $("#amount_foreign_text_" + id);
    var amount_foreign_edit = $("#amount_foreign_edit");

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

    var amount_gap_foreign_saved = $("#amount_gap_foreign_" + id);
    var amount_gap_foreign_text_saved = $("#amount_gap_foreign_text_" + id);
    var amount_gap_foreign_edit = $("#amount_gap_foreign_edit");

    var clearing_edit = $("#clearing");
    var clearing_coa_id_edit = $("#clearing_coa_id");

    var is_clearing = $("#is_clearing_" + id);
    var clearing_coa_id = $("#clearing_coa_id_" + id);
    var clearing_coa_name = $("#clearing_coa_name_" + id);

    var empty_input = $("#supplierInvoiceEditModal")
        .find(":input[required]:visible")
        .filter(function () {
            return this.value == "";
        });

    if (empty_input.length > 0) {
        showAlert("", "Periksa kembali inputan yang masih kosong!", "warning");
        return false;
    }

    outstanding_amount_saved.val(thousandToFloat(outstanding_amount_edit.val()));
    outstanding_amount_text_saved.text(outstanding_amount_edit.val());
    amount_saved.val(thousandToFloat(amount_edit.val()));
    amount_saved_text.text(amount_edit.val());
    amount_foreign_saved.val(thousandToFloat(amount_foreign_edit.val()));
    amount_foreign_saved_text.text(amount_foreign_edit.val());
    note_saved.val(note_edit.val());
    note_text_saved.text(note_edit.val());
    clearing_note_saved.val(clearing_note_edit.val());
    exchange_rate_gap_note_saved.val(exchange_rate_gap_note_edit.val());
    exchange_rate_saved.val(thousandToFloat(exchange_rate_edit.val()));
    exchange_rate_text_saved.text(exchange_rate_edit.val());
    exchange_rate_gap_saved.val(thousandToFloat(exchange_rate_gap_edit.val()));
    exchange_rate_gap_saved_text.text(exchange_rate_gap_edit.val());
    amount_gap_foreign_saved.val(
        thousandToFloat(amount_gap_foreign_edit.val())
    );
    amount_gap_foreign_text_saved.text(amount_gap_foreign_edit.val());

    if (clearing_edit.is(":checked")) {
        is_clearing.val(1);
        clearing_coa_id.val(clearing_coa_id_edit.val());
        clearing_coa_name.val($("#clearing_coa_id option:selected").text());
    } else {
        is_clearing.val(0);
    }

    $("#item_receiving_reports_" + id).val(
        JSON.stringify(item_receiving_report_saved)
    );
    item_receiving_report_saved = [];

    $("#date_edit").val("");
    $("#code_edit").val("");
    $("#reference_edit").val("");
    $("#coa_edit").val("");
    $("#currency_edit").val("");
    $("#exchange_rate_edit").val("");
    $("#total_amount_edit").val("");
    $("#paid_amount_edit").val("");
    $("#outstanding_amount_edit").val("");
    $("#amount_foreign_edit").val("");
    $("#exchange_rate_gap_edit").val("0");
    $("#amount_foreign_edit").val("");
    $("#amount_gap_foreign_edit").val("");
    $("#clearing").prop("checked", false);
    $("#lpb-table").addClass("d-none");
    $("#lpb-table-body").html("");

    supplierInvoiceEditModal.modal("hide");

    calculateData();
}

function calculateGapEdit(currency) {
    // var id = $('#edited_supplier_invoice_id');
    var exchange_rate = thousandToFloat($("#exchange_rate").val());
    var outstanding_amount_edit = thousandToFloat(
        $("#outstanding_amount_edit").val()
    );
    var exchange_rate_edit = thousandToFloat($("#exchange_rate_edit").val());
    var amount_edit = $("#amount_edit");
    var amount_edit_val = thousandToFloat($("#amount_edit").val());
    var amount_foreign_edit = $("#amount_foreign_edit");
    var amount_foreign_edit_val = thousandToFloat(
        $("#amount_foreign_edit").val()
    );
    var amount_gap_foreign_edit = $("#amount_gap_foreign_edit");
    var total_amount_edit = thousandToFloat($("#total_amount_edit").val());

    if (currency == "local") {
        var amount_foreign = amount_edit_val / exchange_rate;
        // CURRENCY AR = CURRENCY INV
        if (currency_id.val() == supplier_invoice_currency_id.val()) {
            amount_foreign = amount_edit_val;
        }
        // CURRENCY LOCAL = CURRENCY INVOICE
        if (supplier_invoice_currency_id.val() == local_currency_id.val()) {
            amount_foreign = amount_edit_val * exchange_rate;
        }
        amount_foreign_edit.val(formatRupiahWithDecimal(amount_foreign));
    } else {
        var amount = amount_foreign_edit_val * exchange_rate;
        // CURRENCY AR = CURRENCY INV
        if (currency_id.val() == supplier_invoice_currency_id.val()) {
            amount = amount_foreign_edit_val;
        }
        // CURRENCY LOCAL = CURRENCY INVOICE
        if (supplier_invoice_currency_id.val() == local_currency_id.val()) {
            amount = amount_foreign_edit_val / exchange_rate;
        }
        amount_edit.val(formatRupiahWithDecimal(amount));
    }

    var final_foreign = thousandToFloat($("#amount_foreign_edit").val());

    // var gap = (exchange_rate - exchange_rate_edit) * final_foreign.toFixed(2);
    var gap = total_amount_edit * exchange_rate_edit - total_amount_edit * exchange_rate;
    // CURRENCY LOCAL = CURRENCY INVOICE
    if (supplier_invoice_currency_id.val() == local_currency_id.val()) {
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
        $("#amount_gap_foreign_edit").after('<span class="text-danger amount-alert">Jumlah bayar melebihi nilai sisa</span>');
    } else {
        $(".amount-alert").remove();
    }

    amount_gap_foreign_edit.val(
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
    var amount_total = 0;
    var amount_foreign_total = 0;
    var exchange_rate_gap_total = 0;

    if (
        currency_id.val() == local_currency_id.val() &&
        currency_id.val() == supplier_invoice_currency_id.val()
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
        $("#selected_supplier_invoice_table").html(`<tr class="empty_invoice_row">
            <td colspan="10" class="text-center">Belum ada invoice</td>
        </tr>`);
    }

    $('input[name="outstanding_amount[]"]').each(function () {
        outstanding_amount_total += parseFloat($(this).val());
    });

    $('input[name="amount[]"]').each(function () {
        amount_total += parseFloat($(this).val());
    });

    let count_debit_total = 0;
    let total_debit_elements = $('input[name="debit[]"]');

    $.each(total_debit_elements, function (e) {
        count_debit_total += thousandToFloat($(this).val());
    });

    $("#debit_total").text(formatRupiahWithDecimal(count_debit_total));
    $("#debit_total_hide").val(count_debit_total);

    $("#total-cash-advance").html(
        formatRupiahWithDecimal(count_debit_total + amount_total)
    );

    $('input[name="amount_foreign[]"]').each(function () {
        amount_foreign_total += parseFloat($(this).val());
    });

    $('input[name="exchange_rate_gap[]"]').each(function () {
        exchange_rate_gap_total += parseFloat($(this).val());
    });

    $("#outstanding_amount_total").text(
        formatRupiahWithDecimal(outstanding_amount_total)
    );
    $("#amount_total").text(formatRupiahWithDecimal(amount_total));
    $("#amount_foreign_total").text(
        formatRupiahWithDecimal(amount_foreign_total.toFixed(2))
    );
    $("#exchange_rate_gap_total").text(
        formatRupiahWithDecimal(exchange_rate_gap_total.toFixed(2))
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
    var find_zero_amount = $('input[name="amount[]"]').filter(function () {
        return parseFloat(thousandToFloat(this.value)) == 0;
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

function clearing() {
    if ($("#clearing").is(":checked")) {
        $("#clearing_coa_form").removeClass("d-none");
    } else {
        $("#clearing_coa_form").addClass("d-none");
    }
}

const getVendorBank = (e) => {
    selected_supplier_invoice_table.html("");
    $("#accordionPaymentInformation").html("");

    $.ajax({
        type: "get",
        url: `${base_url}/vendor/${$(e).val()}`,
        success: function (data) {
            let html = ``;
            data.data.vendor_banks.forEach((value) => {
                html += `<tr>
                            <td class="border-0">${value.name} - </td>
                            <td class="border-0">${value.account_number}</td>
                            <td class="border-0">A/N ${value.behalf_of}</td>
                        </tr>`;
            });

            if (data.data.vendor_banks.length == 0) {
                html = `<tr>
                            <td class="border-0 text-center" colspan="3">Tidak ada informasi bank</td>
                        </tr>`;
            }

            $("#bank-info-detail").html(html);
            $("#bank-info-card").removeClass("d-none");
        },
    });
};

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
                $("#is_giro").prop("checked", false);
                $("#giro-form").find("input").attr("required", false).val("");
            }

            if (response.data.currency) {
                $('#currency_id').append(`<option selected value="${response.data.currency.id}">${response.data.currency.kode} - ${response.data.currency.nama}</option>`);
            }
        },
    });
};

const calculate_final_total = () => {
    // calculate invoice
    let total_invoice_payment = 0;
    $('input[name="receive_amount_customer[]"]').each(function () {
        total_invoice_payment += parseFloat($(this).val());
    });
    total_invoice_payment *= -1;

    // calculate adjustment
    let total_adjustment_payment = 0;
    $('input[name="debit[]"]').each(function () {
        total_adjustment_payment += thousandToFloat($(this).val());
    });

    // calculate supplier invoice
    let total_supplier_invoice_payment = 0;
    $('input[name="amount[]"]').each(function () {
        total_supplier_invoice_payment += parseFloat($(this).val());
    });

    // calculate return
    let total_return_amount = 0;
    $('input[name="return_amount[]"]').each(function () {
        total_return_amount += thousandToFloat($(this).val());
    });
    total_return_amount *= -1;

    // final total payment
    let final_total_payment = total_supplier_invoice_payment + total_adjustment_payment + total_invoice_payment + total_return_amount;
    totalDataInvoice.text(formatRupiahWithDecimal(total_invoice_payment));
    totalDataAdjustment.text(formatRupiahWithDecimal(total_adjustment_payment));
    totalDataSupplierInvoice.text(formatRupiahWithDecimal(total_supplier_invoice_payment));
    totalDataReturn.text(formatRupiahWithDecimal(total_return_amount));
    totalDataTotal.text(formatRupiahWithDecimal(final_total_payment));

    if (
        $('input[name="receive_amount_customer[]"]').length > 0 ||
        $('input[name="amount[]"]').length > 0 ||
        $('input[name="return_amount[]"]').length > 0
    ) {
        $('#date').attr('readonly', true);
    } else {
        $('#date').attr('readonly', false);
    }
}

const get_vendor_customer = (e) => {
    $.ajax({
        type: "get",
        url: `${base_url}/vendor-find-customer/${$(e).val()}`,
        success: function (data) {
            if (data.data != null) {
                $("#customer_id").val(data.data.id);
                $("#customer-section").removeClass("d-none");
            } else {
                $("#customer_id").val("");
                $("#customer-section").addClass("d-none");
            }
        },
        error: function (xhr) {
            $("#customer_id").val("");
            $("#customer-section").addClass("d-none");
        }
    });
};
