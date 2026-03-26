const initSelect2SearchSO = (element, route) => {
    let selected_item = [];

    $(`select[name="#${element}"]`)
        .toArray()
        .map(function () {
            if ($(this).val() != null) {
                selected_item.push($(this).val());
            }
        });

    let target_value = $(`#${element}`).val();

    var itemSelect = {
        placeholder: "Pilih Data",
        allowClear: false,
        width: "100%",
        language: {
            noResults: () => {
                return "Data can't be found";
            },
        },
        ajax: {
            url: route,
            dataType: "json",
            delay: 250,
            type: "get",
            data: (params) => {
                let result = {};
                result["search"] = params.term;
                result["selected_item"] = selected_item;
                result["${element}"] = target_value;
                result["page_limit"] = 10;
                result["page"] = params.page;
                return result;
            },
            processResults: (data, params) => {
                params.page = params.page || 1;
                let final_data = data.data.map((data, key) => {
                    return {
                        id: data.id,
                        text: `${data.nomor_so} - ${data.customer.nama}`,
                    };
                });
                return {
                    results: final_data,
                    pagination: {
                        more: params.page * 10 < data.total,
                    },
                };
            },
            cache: true,
        },
    };

    $(`#${element}`).select2(itemSelect);
    return;
};
