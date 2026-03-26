const initProjectSelect = (element, branchEelement = null) => {
    var select2Option = {
        placeholder: "Pilih project",
        allowClear: true,
        width: "100%",
        language: {
            noResults: () => {
                return "Data tidak ditemukan";
            },
        },
        ajax: {
            url: `${base_url}/select/project`,
            dataType: "json",
            delay: 250,
            type: "get",
            data: ({ term }) => {
                let result = {};
                result["search"] = term;
                if (branchEelement) {
                    result["branch_id"] = function () {
                        return $(branchEelement).val();
                    }
                }
                return result;
            },
            processResults: ({ data }) => {
                let final_data = data.map((data, key) => {
                    return {
                        id: data.id,
                        text: `${data.code} - ${data.name}`,
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
