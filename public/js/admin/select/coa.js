function initCoaSelect(element) {
    var select2Option = {
        placeholder: "Pilih Data",
        minimumInputLength: 3,
        allowClear: true,
        width: "100%",
        language: {
            inputTooShort: () => {
                return "Ketik minimal 3 karakter";
            },
            noResults: () => {
                return "Data tidak ditemukan";
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
                        text: `${data.account_code} - ${data.name}`,
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

    let elements = $(element);
    if (elements.length > 1) {
        $.each(elements, function (e) {
            $(this).select2(select2Option);
        });
    } else {
        $(element).select2(select2Option);
    }
}
