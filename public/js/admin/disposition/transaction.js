let last_journal_date = $('#last_journal_date');
let last_book_value = $('#last_book_value');

function get_asset_detail(e) {
    last_journal_date.val('');
    last_book_value.val('');

    if ($(e).val() == null) {
        return false;
    }
    $.ajax({
        type: "get",
        url: `${base_url}/asset/${$(e).val()}`,
        success: function (data) {
            let journal_date = data.data.depreciations[(data.data.depreciations.length - 1)];
            if (journal_date != undefined) {
                last_journal_date.val(localDate(journal_date.date));
            }
            last_book_value.val(formatRupiahWithDecimal(data.data.outstanding_value));
        }
    });
}

function show_selling_form(e) {
    if ($(e).is(':checked')) {
        $('#selling_price').attr('required', true);
        $('#selling_coa_id').attr('required', true);
    } else {
        $('#selling_price').attr('required', false);
        $('#selling_coa_id').attr('required', false);

    }

}
