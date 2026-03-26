function getItemTypeCoa() {
    let item_type_id = $("#item_type_id").val();
    let is_edit = $("#is_edit").val();

    $.ajax({
        type: "get",
        url: `${base_url}/item-category-get-item-type-coa/${item_type_id}?is_edit=${is_edit}`,
        dataType: "html",
        success: function (data) {
            $("#item-type-coa-data").html(data);

            initCoaSelect();
        },
    });
}

var itemSelect = {
    placeholder: "Pilih Data",
    minimumInputLength: 3,
    allowClear: true,
    language: {
        inputTooShort: () => {
            return "Insert at least 3 characters";
        },
        noResults: () => {
            return "Data can't be found";
        },
    },
    ajax: {
        url: `${base_url}/select/coa`,
        dataType: "json",
        delay: 250,
        type: "get",
        data: (params) => {
            let result = {};
            result["search"] = params.term;
            result['page_limit'] = 10;
            result['page'] = params.page;
            return result;
        },
        processResults: (data, params) => {
            params.page = params.page || 1;
            let final_data = data.data.data.map((data, key) => {
                return {
                    id: data.id,
                    text: `${data.name} - ${data.account_code}`,
                };
            });
            return {
                results: final_data,
                pagination: {
                    more: (params.page * 10) < data.data.total
                }
            };
        },
        cache: true,
    },
};

function initCoaSelect() {
    let elements = $('select[name="coa_id[]"]');

    $.each(elements, function (e) {
        $(this).select2(itemSelect);
    });
}
