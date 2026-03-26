const initSelectDeliveryOrderGeneralSelect = (element, route) => {
    var select2Option = {
        placeholder: "Pilih sale order general",
        allowClear: true,
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
            data: ({ term }) => {
                let result = {};
                result["search"] = term;
                return result;
            },
            processResults: ({ data }) => {
                let final_data = data.map((data, key) => {
                    return {
                        id: data.id,
                        text: `${data.kode} - ${data.customer.nama}`,
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
