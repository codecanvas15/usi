const initSaleOrderGeneralSelect = (element, route) => {
    var select2Option = {
        placeholder: "Pilih delivery order",
        allowClear: false,
        width: "100%",
        language: {
            noResults: () => {
                return "Data tidak ditemukan";
            },
        },
        ajax: {
            url: route,
            dataType: "json",
            delay: 250,
            type: "get",
            data: ({
                term
            }) => {
                let result = {};
                result["search"] = term;
                return result;
            },
            processResults: ({
                data
            }) => {
                let final_data = data.map((data, key) => {
                    return {
                        id: data.id,
                        text: `${data?.code} - ${data.sale_order_general?.kode}`,
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
