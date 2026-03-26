var form_detail = $("#form_detail");
let dataFundSubmission = "";

initSelect2Search(
    "fund_submission_id",
    `${base_url}/select/fund-submission`,
    {
        id: "id",
        text: "code,to_name,total",
    },
    0,
    {
        item: "dp",
        available: true,
        date: function () {
            return $("#date").val();
        },
    }
);

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

function getFundSubmission(e) {
    $(document).find("input[type=submit]").prop("disabled", false);
    $(document).find("button[type=submit]").prop("disabled", false);
    form_detail.html("");
    $.ajax({
        type: "post",
        url: `${base_url}/cash-advance-payment/fund-submission`,
        data: {
            _token: token,
            fund_submission_id: $(e).val(),
            date: function () {
                return $("#date").val();
            }
        },
        success: function (data) {
            form_detail.html(data.html);
            initMaskTaxReference();
            key = $("#count_rows").val();
            dataFundSubmission = data.data;
            if (data.data.is_giro) {
                if (data.data.send_payment != null) {
                    if (!data.data.send_payment.due_status_by_date.is_due) {
                        $(document)
                            .find("input[type=submit]")
                            .prop("disabled", true);
                        $(document)
                            .find("button[type=submit]")
                            .prop("disabled", true);
                    }
                } else {
                    $(document)
                        .find("input[type=submit]")
                        .prop("disabled", true);
                    $(document)
                        .find("button[type=submit]")
                        .prop("disabled", true);
                }
            }

            $("#sequence_code").on("blur", function () {
                check_bank_code(
                    "#coa_detail_id_0",
                    "#sequence_code",
                    "#date",
                    "out"
                );
            });

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
                    currency_id: data.data.currency_id,
                },
                "#edit-bank-modal"
            );

            let parent_coa = data.data.fund_submission_cash_advances.filter(
                (fund_submission_cash_advance) => {
                    return fund_submission_cash_advance.type == "cash_bank";
                }
            )[0];

            $("#save-edit-bank-modal").click(function (e) {
                e.preventDefault();

                // * check if the old coa bank is same or not
                // * if not same then validate the new coa bank

                if (parent_coa.coa_id != $("#coa-bank-edit-select").val()) {
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

                $("#coa_detail_id_0").val($("#coa-bank-edit-select").val());
                let coaData = $("#coa-bank-edit-select").select2("data");

                $("#coa_detail_id_0").html(
                    `<option value='${$("#coa-bank-edit-select").val()}'>${coaData[0].text
                    }</option>
                    `
                );

                $("#change-bank-reason-input").val(
                    $("#change-bank-reason").val()
                );

                $("#sequence_code").val(null);
                $("#sequence_code").trigger("blur");

                $("#edit-bank-modal").modal("hide");
            });
        },
    });
}
