let MASUKAN = "";
let PENGELUARAN = "";

// GET DATA PPN MASUKAN AND PPN KELUARAN
const getData = () => {
    $.ajax({
        type: "post",
        url: `${base_url}/tax-reconciliation/get-data`,
        data: {
            _token: token,
            branch_id: $("#branch_id").val(),
            date: $("#tax_period").val(),
        },
        success: function (data) {
            data = data.data;

            let ppn_keluaran = ``;
            // GET DATA PPN KELUARAN
            data.ppn_keluaran.forEach((value, index) => {
                if (value.data.amount != 0) {
                    ppn_keluaran += `<tr>
                                        <td class="align-top">
                                            <b> <i class="fa fa-file"></i> ${value.reference_parent.kode ?? value.reference_parent.code}</b><br>
                                            <span class="text-light">Sales Invoice</span><br>
                                            <span class="text-light">${localDate(value.data.date)}</span><br>
                                            <span class="text-light">${value.customer?.nama ?? 'Tidak Ada Customer'}</span><br>
                                            <span class="text-light">${value.customer?.npwp ?? "Tidak Ada NPWP"}</span><br>
                                            <span class="text-light">${value.faktur_pajak ?? "Tidak Ada Faktur Pajak"}</span><br>
                                        </td>
                                        <td class="align-top">
                                            <span>DPP : <b>${formatRupiahWithDecimal(value.data.dpp)}</b></span><br>
                                            <span>${value.data.tax.name} ${value.data.value * 100}% : <b>${formatRupiahWithDecimal(value.data.amount)}</b></span><br>
                                            <span>NILAI SISA : <b>${formatRupiahWithDecimal(value.data.outstanding)}</b></span><br>
                                        </td>
                                        <td class="align-top text-end">
                                            <input type="checkbox" id="out_checkbox_${index}" class="out_checkbox" name="out_checkbox[${index}]" onclick="calculcateData();checkboxOutCheck($(this))" class="filled-in chk-col-primary" value="1" data-out="${value.data.outstanding}" data-dpp="${value.data.dpp}" data-index="${index}" ${value.is_disabled ? "disabled" : ""} ${!value.is_disabled ? "checked" : ""}>
                                            <label for="out_checkbox_${index}"></label>
                                            <input type="hidden" class="out_is_checked" name="out_is_checked[${index}]" value="true" id="out_is_checked_${index}">
                                            <input type="hidden" class="out" name="out[${index}]" value="${value.data.outstanding}">
                                            <input type="hidden" class="out_type" name="out_type[${index}]" value="journal">
                                            <input type="hidden" class="out_id" name="out_id[${index}]" value="${value.data.id}">
                                            <input type="hidden" class="out_tax_number" name="out_tax_number[${index}]" value="${value.faktur_pajak}">
                                        </td>
                                    </tr>`;
                }
            });

            let ppn_masukan = ``;
            // GET DATA PPN MASUKAN
            data.ppn_masukan.forEach((value, index) => {
                if (value.data.amount != 0) {
                    if (value.vendor.nama) {
                        ppn_masukan += `<tr>
                                            <td class="align-top">
                                                <b> <i class="fa fa-file"></i> ${value.reference_parent.kode ?? value.reference_parent.code}</b><br>
                                                <span class="text-light">Vendor Invoice</span><br>
                                                <span class="text-light">${localDate(value.data.date)}</span><br>
                                                <span class="text-light">${value.vendor.name ?? value.vendor.nama ?? ""}</span><br>
                                                <span class="text-light">${value.vendor.npwp ?? "Tidak Ada NPWP"}</span><br>
                                                <span class="text-light">${value.faktur_pajak ?? "Tidak Ada Faktur Pajak"}</span><br>
                                            </td>
                                            <td class="align-top">
                                                <span>DPP : <b>${formatRupiahWithDecimal(value.data.dpp)}</b></span><br>
                                                <span>${value.data.tax.name} ${value.data.value * 100}% : <b>${formatRupiahWithDecimal(value.data.amount)}</b></span><br>
                                                <span>NILAI SISA : <b>${formatRupiahWithDecimal(value.data.outstanding)}</b></span><br>
                                            </td>
                                            <td class="align-top text-end">
                                                <input type="checkbox" id="in_checkbox_${index}" class="in_checkbox" name="in_checkbox[${index}]" onclick="calculcateData();checkboxInCheck($(this))" class="filled-in chk-col-primary" value="1" data-in="${value.data.outstanding}" data-dpp="${value.data.dpp}" data-index="${index}" ${value.is_disabled ? "disabled" : ""}>
                                                <label for="in_checkbox_${index}"></label>
                                                <input type="hidden" class="in_is_checked" name="in_is_checked[${index}]" value="false" id="in_is_checked_${index}">
                                                <input type="hidden" class="in" name="in[${index}]" value="${value.data.outstanding}">
                                                <input type="hidden" class="in_type" name="in_type[${index}]" value="journal">
                                                <input type="hidden" class="in_id" name="in_id[${index}]" value="${value.data.id}">
                                                <input type="hidden" class="in_tax_number" name="in_tax_number[${index}]" value="${value.faktur_pajak}">
                                            </td>
                                        </tr>`;
                    } else {
                        ppn_masukan += `<tr>
                                            <td class="align-top">
                                                <b> <i class="fa fa-file"></i> ${value.vendor}</b><br>
                                                <span class="text-light">Lebih Bayar</span><br>
                                                <span class="text-light">${value.data.date}</span><br>
                                                <span class="text-light"></span><br>
                                                <span class="text-light"></span><br>
                                                <span class="text-light"></span><br>
                                            </td>
                                            <td class="align-top">
                                                <span>NILAI : <b>${formatRupiahWithDecimal(value.data.outstanding)}</b></span><br>
                                            </td>
                                            <td class="align-top text-end">
                                                <input type="checkbox" id="in_checkbox_${index}" name="in_checkbox[${index}]" onclick="calculcateData();checkboxInCheck($(this))" class="filled-in chk-col-primary" value="1" data-in="${value.data.outstanding}" data-dpp="${value.data.dpp}" data-index="${index}">
                                                <label for="in_checkbox_${index}"></label>
                                                <input type="hidden" name="in_is_checked[${index}]" value="false" id="in_is_checked_${index}">
                                                <input type="hidden" name="in[${index}]" value="${value.data.outstanding}">
                                                <input type="hidden" name="in_type[${index}]" value="journal">
                                                <input type="hidden" name="in_id[${index}]" value="${value.data.id}">
                                                <input type="hidden" name="in_tax_number[${index}]" value="${value.faktur_pajak}">
                                            </td>
                                        </tr>`;
                    }
                }
            });

            if ($.fn.DataTable.isDataTable("#table-invoice-tax")) {
                $("#table-invoice-tax").DataTable().destroy();
            }
            if ($.fn.DataTable.isDataTable("#table-purchase-tax")) {
                $("#table-purchase-tax").DataTable().destroy();
            }

            MASUKAN = ppn_masukan;
            PENGELUARAN = ppn_keluaran;

            setTimeout(() => {
                $("#in-data").html(ppn_masukan);
                $("#out-data").html(ppn_keluaran);
                loadTable(ppn_masukan, ppn_keluaran);
            }, 1000);
        },
    });
};

const loadTable = (ppn_masukan, ppn_keluaran) => {
    $("#table-invoice-tax").dataTable({
        searching: true,
        paging: true,
        destroy: true,
        responsive: true,
        ordering: false,
        stateSave: true,
    });

    $("#table-purchase-tax").dataTable({
        searching: true,
        paging: true,
        destroy: true,
        responsive: true,
        ordering: false,
        stateSave: true,
    });

    // IF PPN MASUKAN IS EMPTY
    if (ppn_masukan == "") {
        $("#in-data").html(`<tr>
                                <td colspan="3" class="text-center">Tidak ada data</td>
                            </tr>`);
    }

    // IF PPN KELUARAN IS EMPTY
    if (ppn_keluaran == "") {
        $("#out-data").html(`<tr>
                                <td colspan="3" class="text-center">Tidak ada data</td>
                            </tr>`);
    }

    calculcateData();
};

const checkboxInCheck = (e) => {
    if ($(e).is(":checked")) {
        $("#in_is_checked_" + $(e).data("index")).val(true);
    } else {
        $("#in_is_checked_" + $(e).data("index")).val(false);
    }
};

const checkboxOutCheck = (e) => {
    if ($(e).is(":checked")) {
        $("#out_is_checked_" + $(e).data("index")).val(true);
    } else {
        $("#out_is_checked_" + $(e).data("index")).val(false);
    }
};

const calculcateData = () => {
    // DISPLAY ELEMENT
    let checked_out_total_text_el = $("#checked_out_total_text");
    let checked_out_total_el = $("#checked_out_total");
    let checked_dpp_out_total_text_el = $("#checked_dpp_out_total_text");
    let checked_in_total_text_el = $("#checked_in_total_text");
    let checked_in_total_el = $("#checked_in_total");
    let checked_dpp_in_total_text_el = $("#checked_dpp_in_total_text");
    let gap_text_el = $("#gap_text");
    let gap_el = $("#gap");

    // GET CHECKED DATA
    var allPagesOut = $("#table-invoice-tax").DataTable().cells().nodes();
    var allPagesIn = $("#table-purchase-tax").DataTable().cells().nodes();

    let checked_data_out = $(allPagesOut).find(
        'input[class="out_checkbox"]:checked'
    );
    let checked_data_in = $(allPagesIn).find(
        'input[class="in_checkbox"]:checked'
    );

    // GET TOTAL
    let out_total = 0;
    let dpp_out_total = 0;
    $.each(checked_data_out, function (e) {
        out_total += parseFloat($(this).data("out"));
        dpp_out_total += parseFloat($(this).data("dpp"));
    });
    checked_out_total_text_el.text(formatRupiahWithDecimal(out_total));
    checked_out_total_el.val(out_total);
    checked_dpp_out_total_text_el.text(formatRupiahWithDecimal(dpp_out_total));

    let in_total = 0;
    let dpp_in_total = 0;
    $.each(checked_data_in, function (e) {
        in_total += parseFloat($(this).data("in"));
        dpp_in_total += parseFloat($(this).data("dpp"));
    });
    checked_in_total_text_el.text(formatRupiahWithDecimal(in_total));
    checked_in_total_el.val(in_total);
    checked_dpp_in_total_text_el.text(formatRupiahWithDecimal(dpp_in_total));

    let gap = in_total - out_total;
    gap_text_el.text(formatRupiahWithDecimal(gap * -1));
    gap_el.val(gap);

    if (gap == 0) {
        $("#coa_id_form").addClass("d-none");
        $("#coa_id").attr("required", false);
    } else {
        $("#coa_id_form").removeClass("d-none");
        if (gap < 0) {
            $("#coa_id").attr("required", true);
            $("#gap_alert").html("");
        } else {
            $("#coa_id").attr("required", false);
            $("#gap_alert").html(
                "Jika kosong selisih pajak akan dimasukkan <br>di rekonsiliasi berikutnya"
            );
        }
    }
};

document.getElementById("in-check-all").addEventListener("click", function (e) {
    if ($.fn.DataTable.isDataTable("#table-invoice-tax")) {
        $("#table-invoice-tax").DataTable().destroy();
    }
    if ($.fn.DataTable.isDataTable("#table-purchase-tax")) {
        $("#table-purchase-tax").DataTable().destroy();
    }

    // $("#in-data").html(MASUKAN);
    // $("#out-data").html(PENGELUARAN);

    let checked = e.target.checked;
    let inCheckboxes = document.querySelectorAll('input[class="in_checkbox"]:not([disabled])');
    inCheckboxes.forEach((checkbox) => {
        checkbox.checked = checked;
        $('#in_is_checked_' + checkbox.dataset.index).val(checked);
    });


    loadTable(MASUKAN, PENGELUARAN);
});
