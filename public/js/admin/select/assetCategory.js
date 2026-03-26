const initAssetCategorySelect = (element) => {
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
            url: `${base_url}/select/asset-category`,
            dataType: "json",
            delay: 250,
            type: "get",
            data: ({ term }) => {
                let result = {};
                result["search"] = term;
                return result;
            },
            processResults: ({ data }) => {
                let final_data = data.map((data, key) => {
                    return {
                        id: data.id,
                        text: `${data.name}`,
                        percentage: data.percentage,
                    };
                });
                return {
                    results: final_data,
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
};
