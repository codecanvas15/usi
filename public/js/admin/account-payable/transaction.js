let vendor_id = $("#vendor_id");
let fund_submission_id = $("#fund_submission_id");
let branch = $("#branch_name");
let date = $("#fund_submission_date");
let dataFundSubmission = "";
let supplier_invoice_currency = $("#supplier_invoice_currency");
let currency = $("#currency_name");
let currency_id = $("#currency_id");
let exchange_rate = $("#exchange_rate");
let reference = $("#reference");
let project = $("#project");
let coa_id = $("#coa_id");
let selected_coa_id = $("#selected_coa_id");
let parent_note = $("#parent_note");
let selected_supplier_invoice_table = $("#selected_supplier_invoice_table");
let fund_submission_supplier_other_data = $(
    "#fund_submission_supplier_other_data"
);
let debit_total = $("#debit_total");
let debit_total_hide = $("#debit_total_hide");
let credit_total = $("#credit_total");
let credit_total_hide = $("#credit_total_hide");
let local_currency_id = $("#local_currency_id");
let supplier_invoice_currency_id_code = $(".supplier_invoice_currency_id_code");
let currency_id_code = $(".currency_id_code");
let giro_information = $("#giro_information");
let giro_number = $("#giro_number");
let giro_liquid_date = $("#giro_liquid_date");
totalAllPembayaran = $("#total-all-pembayaran");
totalAllAdjustment = $("#total-all-adjustment");
totalAllTotal = $("#total-all-total");

$("#vendor_id").change(function () {
    fund_submission_id.val("").trigger("change");
    fund_submission_supplier_other_data.html("");

    $("#data-detail").addClass("d-none");
});

var key = 0;

function checkFundSubmissionDate(element) {
    let formatDateElement = element.val();
    if (dataFundSubmission?.id) {
        if (element.val()) {
            $.ajax({
                url: `${base_url}/fund-submission/check-date/${dataFundSubmission?.id}`,
                data: {
                    _token: token,
                    date: formatDateElement,
                },
                success: ({ data }) => {
                    if (!data?.status) {
                        showAlert(
                            "Peringatan",
                            "Tanggal Tidak boleh kurang dari tanggal pengajuan dana",
                            "error"
                        );
                        element.val("");
                    }
                },
            });
        }
    }
}

//get fundsubmission
function getFundSubmission(e) {
    $(document).find("input[type=submit]").prop("disabled", false);
    $(document).find("button[type=submit]").prop("disabled", false);
    $("#giro-information").html("");

    if ($(e).val() == undefined) {
        fund_submission_supplier_other_data.html("");
        $("#data-detail").addClass("d-none");

        return false;
    }
    $.ajax({
        type: "get",
        url: `${base_url}/account-payable/fund-submission/${$(e).val()}`,
        data: {
            date: function () {
                return $("#date_input").val();
            },
        },
        success: function (data) {
            $("#data-detail").removeClass("d-none");

            branch.text(data.data.branch.name);
            dataFundSubmission = data.data;
            date.text(localDate(data.data.date));
            supplier_invoice_currency.text(
                `${data.data.fund_submission_supplier.currency.kode} - ${data.data.fund_submission_supplier.currency.nama}`
            );
            currency.text(
                `${data.data.currency.kode} - ${data.data.currency.nama}`
            );
            currency_id.val(data.data.currency.id);
            exchange_rate.val(formatRupiahWithDecimal(data.data.exchange_rate));
            if (data.data.currency.is_local) {
                $("#exchange_rate").prop("readonly", true);
            } else {
                $("#exchange_rate").prop("readonly", false);
            }

            reference.text(data.data.reference);

            if (data.data.project) {
                project.text(data.data.project.name);
            }

            if (data.data.is_giro) {
                if (data.data.send_payment != null) {
                    $("#giro-information").html(data.html);
                    if (!data.data.send_payment.due_status_by_date.is_due) {
                        $(document)
                            .find("input[type=submit]")
                            .prop("disabled", true);
                        $(document)
                            .find("button[type=submit]")
                            .prop("disabled", true);
                        $("#giro-information")
                            .append(`<div class="badge badge-lg badge-danger">
                            ${data.data.send_payment.due_status_by_date.message}
                        </div>`);
                    }
                } else {
                    $(document)
                        .find("input[type=submit]")
                        .prop("disabled", true);
                    $(document)
                        .find("button[type=submit]")
                        .prop("disabled", true);
                    $("#giro-information").html(`<h3>Informasi Giro</h3>
                    <div class="badge badge-lg badge-danger">
                        Giro batal cair, silahkan perbarui informasi giro!
                    </div>`);
                }
            } else {
                $("#giro-information").html("");
            }

            coa_id.text(
                `${data.data.fund_submission_supplier.coa.account_code} - ${data.data.fund_submission_supplier.coa.name}`
            );
            selected_coa_id.val(data.data.fund_submission_supplier.coa.id);
            parent_note.text(data.data.fund_submission_supplier.note);

            let sum_debit_total = 0;
            let sum_credit_total = 0;
            $.each(
                data.data.fund_submission_supplier_others,
                function (index, value) {
                    sum_debit_total += value.debit;
                    sum_credit_total += value.credit;
                }
            );

            debit_total.text(formatRupiahWithDecimal(sum_debit_total));
            credit_total.text(sum_credit_total);
            supplier_invoice_currency_id_code.text(
                data.data.fund_submission_supplier.currency.kode
            );
            currency_id_code.text(data.data.currency.kode);

            selected_supplier_invoice_table.html(data.html_detail);

            let append_supplier_others = "";
            key = 0;
            $.each(
                data.data.fund_submission_supplier_others,
                function (index, value) {
                    append_supplier_others = `<tr id="fund-submission-supplier-other-${key}">
                                        <td>
                                            <input type="hidden" name="account_payable_other_id[]" value="" />
                                            <select name="coa_detail_id[]" id="coa_detail_id_${key}" class="form-control" required autofocus style="width:100%">
                                                <option value="${value.coa_id}">${value.coa.account_code} - ${value.coa.name}</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="note_other[]" id="note_other_${key}" required placeholder="Masukkan Keterangan" value="${value.note}" readonly />
                                        </td>
                                        <td>
                                            <input type="text" class="form-control commas-form text-end" name="debit[]" id="debit_${key}" required placeholder="Masukkan Nominal" onkeyup="countTotal()" value="${formatRupiahWithDecimal(value.debit)}" readonly />
                                        </td>
                                        <td>

                                        </td>
                                    </tr>`;

                    fund_submission_supplier_other_data.append(
                        append_supplier_others
                    );

                    // initCoaSelect(`#coa_detail_id_${key}`);
                    initCommasForm();
                    key += 1;
                }
            );

            $(".currency-symbol").text(data.data.currency.simbol);
            $(".supplier-invoice-currency-symbol").text(
                data.data.fund_submission_supplier.currency.simbol
            );
            $(".local-currency-symbol").text($("#local_currency_symbol").val());

            if (data.data.currency.id == $("#local_currency_id").val()) {
                $(".multi-currency-column").addClass("d-none");
            } else {
                $(".multi-currency-column").removeClass("d-none");
            }

            initSelect2SearchPagination(
                `coa-bank-edit-select`,
                `${base_url}/select/coa`,
                {
                    id: "id",
                    text: "account_code,name",
                },
                0,
                {
                    account_type: "Cash & Bank",
                    currency_id: data.data.currency.id,
                },
                "#edit-bank-modal"
            );

            $("#save-edit-bank-modal").click(function (e) {
                e.preventDefault();

                // * check if the old coa bank is same or not
                // * if not same then validate the new coa bank
                let parent_coa = data.data.fund_submission_supplier.coa;
                if (parent_coa.id != $("#coa-bank-edit-select").val()) {
                    if (!$("#change-bank-reason").val()) {
                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            text: "Tolong masukkan alasan ganti bank!",
                        });

                        return;
                    }

                    if (!$("#coa-bank-edit-select").val()) {
                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            text: "Tolong pilih bank baru!",
                        });

                        return;
                    }
                }

                selected_coa_id.val($("#coa-bank-edit-select").val());
                let coaData = $("#coa-bank-edit-select").select2("data");

                $("#coa_id").html(`
                            ${coaData[0].text}
                        `);

                $("#change-bank-reason-input").val(
                    $("#change-bank-reason").val()
                );

                $("#sequence_code").val(null);
                $("#sequence_code").trigger("blur");

                $("#edit-bank-modal").modal("hide");
            });

            countTotal();
        },
    });
}

function countExchangeRateGap() {
    let fund_submission_supplier_detail_ids = $(
        'input[name="fund_submission_supplier_detail_id[]"]'
    );
    $.each(fund_submission_supplier_detail_ids, function (e) {
        let each_id = $(this).val();
        let each_amount = $("#amount_" + each_id);
        let each_amount_foreign = $("#amount_foreign_" + each_id);
        let each_amount_foreign_text = $("#amount_foreign_text_" + each_id);
        let each_currency_id = $("#currency_id_" + each_id);
        let each_exchange_rate = $("#exchange_rate_" + each_id);
        let each_exchange_rate_gap = $("#exchange_rate_gap_" + each_id);
        let each_exchange_rate_gap_text = $(
            "#exchange_rate_gap_text_" + each_id
        );

        let each_gap =
            thousandToFloat(exchange_rate.val()) - each_exchange_rate.val();

        let get_each_amount_foreign =
            each_amount.val() / thousandToFloat(exchange_rate.val());
        // SI currency is local
        if (local_currency_id.val() == each_currency_id.val()) {
            get_each_amount_foreign =
                each_amount.val() * thousandToFloat(exchange_rate.val());
        }

        each_amount_foreign.val(get_each_amount_foreign);
        each_amount_foreign_text.text(
            formatRupiahWithDecimal(get_each_amount_foreign)
        );

        // if currency same or SI currency is local
        let get_each_exchange_rate_gap = 0;
        if (
            currency_id.val() == each_currency_id.val() ||
            each_currency_id.val() == local_currency_id.val()
        ) {
            get_each_exchange_rate_gap = 0;
        } else {
            get_each_exchange_rate_gap = each_gap * get_each_amount_foreign;
        }

        each_exchange_rate_gap.val(get_each_exchange_rate_gap);
        each_exchange_rate_gap_text.text(
            formatRupiahWithDecimal(get_each_exchange_rate_gap)
        );
    });
}

function addAccountPayableOtherRow() {
    key += 1;
    var append_element = `<tr id="fund-submission-supplier-other-${key}">
                            <td>

                                <input type="hidden" name="account_payable_other_id[]" value="" />
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

    $("#fund_submission_supplier_other_data").append(append_element);

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

    totalAllPembayaran.text();
    totalAllAdjustment.text(formatRupiahWithDecimal(count_debit_total));
    totalAllTotal.text();
}
