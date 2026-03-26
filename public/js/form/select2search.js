const initSelect2Search = (
    target,
    route,
    selector,
    min_char = 0,
    filter = [],
    dropdownParent = "",
    readonly = false
) => {
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
        disabled: readonly,
        placeholder: "Pilih Data",
        minimumInputLength: min_char,
        allowClear: true,
        width: "100%",
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

                $.each(filter, function (key, value) {
                    result[key] = value;
                });

                return result;
            },
            processResults: ({ data }) => {
                let final_data = data.map((data, key) => {
                    let return_text = "";
                    let split_text = selector.text.split(",");
                    $.each(split_text, function (index, value) {
                        if (index != 0) {
                            return_text += ` - ${data[value]}`;
                        } else {
                            return_text += data[value];
                        }
                    });
                    return {
                        id: data[selector.id],
                        text: return_text,
                    };
                });
                return {
                    results: final_data,
                };
            },
            cache: true,
        },
    };

    if (dropdownParent != "") {
        itemSelect.dropdownParent = $(dropdownParent);
    }

    $(`#${target}`).select2(itemSelect);
    return;
};

const initSelect2SearchCurrencyWithCondition = (
    target,
    route,
    selector,
    allow_foreign,
    selected_id
) => {
    let selected_item = [];

    $(`select[id="#${target}"]`)
        .toArray()
        .map(function () {
            if ($(this).val() != null) {
                selected_item.push($(this).val());
            }
        });

    var itemSelect = {
        placeholder: "Pilih Data",
        minimumInputLength: 0,
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
                result["selected_id"] = selected_id;
                result["allow_foreign"] = allow_foreign;

                return result;
            },
            processResults: ({ data }) => {
                let final_data = data.map((data, key) => {
                    let return_text = "";
                    let split_text = selector.text.split(",");
                    $.each(split_text, function (index, value) {
                        if (index != 0) {
                            return_text += ` - ${data[value]}`;
                        } else {
                            return_text += data[value];
                        }
                    });
                    return {
                        id: data[selector.id],
                        text: return_text,
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

const initSelect2SearchPagination = (
    target,
    route,
    selector,
    min_char = 0,
    filter = [],
    dropdownParent = "",
    readonly = false
) => {
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
        disabled: readonly,
        placeholder: "Pilih Data",
        minimumInputLength: min_char,
        allowClear: true,
        width: "100%",
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
            data: (params) => {
                let result = {};
                result["search"] = params.term;
                result["selected_item"] = selected_item;
                result["page_limit"] = 10;
                result["page"] = params.page;
                result[target] = target_value;

                $.each(filter, function (key, value) {
                    result[key] = value;
                });

                return result;
            },
            processResults: (data, params) => {
                params.page = params.page || 1;
                let final_data = data.data.data.map((data, key) => {
                    let return_text = "";
                    let split_text = selector.text.split(",");
                    $.each(split_text, function (index, value) {
                        if (index != 0) {
                            return_text += ` - ${data[value]}`;
                        } else {
                            return_text += data[value];
                        }
                    });
                    return {
                        id: data[selector.id],
                        text: return_text,
                    };
                });
                return {
                    results: final_data,
                    pagination: {
                        more: params.page * 10 < data.data.total,
                    },
                };
            },
            cache: true,
        },
    };

    if (dropdownParent != "") {
        itemSelect.dropdownParent = $(dropdownParent);
    }

    $(`#${target}`).select2(itemSelect);
    return;
};
const initSelect2SearchPaginationData = (
    target,
    route,
    selector,
    min_char = 0,
    filter = [],
    dropdownParent = "",
    readonly = false,
) => {
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
        disabled: readonly,
        placeholder: "Pilih Data",
        minimumInputLength: min_char,
        allowClear: true,
        width: "100%",
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
            data: (params) => {
                let result = {};
                result["search"] = params.term;
                result["selected_item"] = selected_item;
                result["page_limit"] = 10;
                result["page"] = params.page;
                result[target] = target_value;

                $.each(filter, function (key, value) {
                    result[key] = value;
                });

                return result;
            },
            processResults: (data, params) => {
                params.page = params.page || 1;
                let final_data = data.data.map((data, key) => {
                    let return_text = "";
                    let split_text = selector.text.split(",");
                    $.each(split_text, function (index, value) {
                        if (index != 0) {
                            return_text += ` - ${data[value]}`;
                        } else {
                            return_text += data[value];
                        }
                    });
                    return {
                        id: data[selector.id],
                        text: return_text,
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

    if (dropdownParent != "") {
        itemSelect.dropdownParent = $(dropdownParent);
    }

    $(`#${target}`).select2(itemSelect);
    return;
};
