const initVehicleSelect2Search = (target, route, min_char = 0) => {
    let selected_item = [];

    $(`select[id="#${target}"]`)
        .toArray()
        .map(function () {
            if ($(this).val() != null) {
                selected_item.push($(this).val());
            }
        });

    let target_value = $(`#${target}`).val();

    var itemSelect = {
        placeholder: "Pilih Data",
        minimumInputLength: min_char,
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
            url: route,
            dataType: "json",
            delay: 250,
            type: "get",
            data: ({ term }) => {
                let result = {};
                result["search"] = term;
                result["selected_item"] = selected_item;
                result[target] = target_value;

                return result;
            },
            processResults: ({ data }) => {
                let final_data = data.map((data, key) => {
                    return {
                        id: data.id,
                        text: `${data.name} - ${numberWithDot(
                            decimalFormatterWithOuNumberWithCommas(
                                data.quantity
                            )
                        )}`,
                    };
                });
                return {
                    results: final_data,
                };
            },
            cache: true,
        },
    };

    $(`#${target}`).select2(itemSelect);
    return;
};
