const ucfirst = (str) => {
    return str.toLowerCase().toUpperCase();
}

const ucwords = (str) => {
    return str.toLowerCase().replace(/\b[a-z]/g, function (letter) {
        return letter.toUpperCase();
    });
}

const numberWithCommas = (x) => {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
};

const numberWithDot = (x) => {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
};

const thousandToFloat = (value) => {
    if (value == "") {
        return 0;
    }
    let split = value.split(",");
    return parseFloat(split[0].split(".").join("") + "." + split[1]);
};

const explode_commas = (x) => {
    let split = x.split(",");
    return numberWithCommas(split[0]) + "," + split[1];
};

const replaceComma2 = (text) => {
    if (text != 0) {
        return text.replace(/\./g, "");
    } else {
        return null;
    }
};

const KFormatter = (num, digits) => {
    const lookup = [{
        value: 1,
        symbol: "",
    },
    {
        value: 1e3,
        symbol: "k",
    },
    {
        value: 1e6,
        symbol: "M",
    },
    {
        value: 1e9,
        symbol: "G",
    },
    {
        value: 1e12,
        symbol: "T",
    },
    {
        value: 1e15,
        symbol: "P",
    },
    {
        value: 1e18,
        symbol: "E",
    },
    ];
    const rx = /\.0+$|(\.[0-9]*[1-9])0+$/;
    var item = lookup
        .slice()
        .reverse()
        .find(function (item) {
            return num >= item.value;
        });
    return item ?
        (num / item.value).toFixed(digits).replace(rx, "$1") + item.symbol :
        "0";
};

const replaceComma = (x) => {
    return x.replace(/\./g, ",");
};

const npwpFormatter = (value) => {
    return value.replace(
        /(\d{2})(\d{3})(\d{3})(\d{1})(\d{3})(\d{3})/,
        "$1.$2.$3.$4-$5.$6"
    );
};

const initNpwpInputForm = () => {
    $(".npwp-form-input").change(function (e) {
        $(this).val(npwpFormatter($(this).val()));
    });
};

const initCommasForm = () => {
    $(".commas-form").blur(function (e) {
        let value = $(this).val();
        if (value !== '') {
            $(this).val(formatRupiahWithDecimal(value));
        }
    });
    $(".commas-form").focus(function (e) {
        let value = $(this).val();
        if (value !== '') {
            $(this).val(formatRupiahWithDecimal(value));
        }
    });
};
initCommasForm();

const initCommasFormThreeDigits = () => {
    $(".commas-form-three").blur(function (e) {
        let value = $(this).val();
        if (value !== '') {
            $(this).val(formatRupiahWithDecimal(value, 3));
        }
    });
    $(".commas-form-three").focus(function (e) {
        let value = $(this).val();
        if (value !== '') {
            $(this).val(formatRupiahWithDecimal(value, 3));
        }
    });
};
initCommasFormThreeDigits();

const initCommasFormFiveDigits = () => {
    $(".commas-form-five").blur(function (e) {
        let value = $(this).val();
        if (value !== '') {
            $(this).val(formatRupiahWithDecimal(value, 5));
        }
    });
    $(".commas-form-five").focus(function (e) {
        let value = $(this).val();
        if (value !== '') {
            $(this).val(formatRupiahWithDecimal(value, 5));
        }
    });
};
initCommasFormFiveDigits();

const formatThousandToFloat = (value) => {
    value = value.toString();
    value = value.replace(/\./g, "");
    value = value.replace(/\,/g, ".");
    return parseFloat(value);
}

const formatRupiahWithDecimal = (value, digits = 2) => {
    if (value === '' || value === undefined || value === null) {
        return '';
    }

    value = value.toString();
    if (value == '' || value == '0') {
        return '0';
    } else {
        if (value.includes('.') && value.includes(',')) {
            value = formatThousandToFloat(value);
        } else if (value.includes(',')) {
            value = value.replace(/\,/g, ".");
            value = parseFloat(value);
        } else {
            value = parseFloat(value);
        }
        return Number(value).toLocaleString("id-ID", {
            minimumFractionDigits: digits,
            maximumFractionDigits: digits
        }); // 100.000,00 (string)
    }
}

const debounce = (func, wait, immediate) => {
    var timeout;
    return function () {
        var context = this,
            args = arguments;
        var later = function () {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
}

const decimalFormatter = (x) => {
    let result = "";
    let split_number = x.toString().split(".");
    result = numberWithCommas(split_number[0]);
    if (split_number[1] !== undefined) {
        let index_1 = split_number[1];
        if (index_1 > 0) {
            split_number[0] = numberWithCommas(split_number[0]);
            result = split_number.join(".");
        }
    }
    return result;
};

const decimalFormatterWithOuNumberWithCommas = (x) => {
    let result = "";
    let split_number = x.toString().split(".");
    result = split_number[0];
    if (split_number[1] !== undefined) {
        let index_1 = split_number[1];
        if (index_1 > 0) {
            split_number[0] = split_number[0];
            result = split_number.join(".");
        }
    }
    return result;
};

const decimalFormatterCommasWithOuNumberWithCommas = (x) => {
    let result = "";
    let split_number = x.toString().split(".");
    result = split_number[0];
    if (split_number[1] !== undefined) {
        let index_1 = split_number[1];
        if (index_1 > 0) {
            split_number[0] = numberWithDot(split_number[0]);
            result = split_number.join(",");
        }
    }
    return result;
};

const generateRandomString = (length = 4) => {
    var result = "";
    var characters =
        "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    var charactersLength = characters.length;
    for (var i = 0; i < length; i++) {
        result += characters.charAt(
            Math.floor(Math.random() * charactersLength)
        );
    }
    return result;
};

const strSnake = (value) => {
    return value.replace(/\-/g, ' ');
}

const showAlert = (title, text, type) => {
    Swal.fire(title, text, type);
};

const check_bank_code = (coa_el, code_el, date_el, type) => {
    $.ajax({
        type: "post",
        url: `${base_url}/check-bank-code`,
        data: {
            _token: token,
            coa_id: $(coa_el).val(),
            type: type,
            date: $(date_el).val(),
            code: $(code_el).val(),
        },
        success: function (data) {
            if (!data) {
                showAlert('', 'Nomor urut telah digunakan', 'warning');
                $(code_el).val('');
            }
        }
    });
}

function reformatDate(dateString) {
    if (dateString != null && dateString != "" && dateString != undefined) {
        var parts = dateString.split("-");
        var year = parts[0];
        var month = parts[1];
        var day = parts[2];
        var reformattedDate = day + "-" + month + "-" + year;

        return reformattedDate;
    }

    return "";
}

function copyToClipBoard(text) {
    var textarea = document.createElement('textarea');
    textarea.value = text;
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);

    alert('Text berhasil dicopy');
}

function localDate(date) {
    if (date != null && date != "" && date != undefined) {
        var parts = date.split("-");
        var year = parts[0];
        var month = parts[1];
        var day = parts[2];

        return day + "-" + month + "-" + year;
    }

    return "";
}

function convertLocalDate(date) {
    if (date != null && date != "" && date != undefined) {
        var parts = date.split("-");
        var year = parts[2];
        var month = parts[1];
        var day = parts[0];

        return year + "-" + month + "-" + day;
    }

    return "";
}

function parseDate(date) {
    if (date != null && date != "" && date != undefined) {
        var parts = date.split("-");
        var year = parts[2];
        var month = parts[1];
        var day = parts[0];

        if (year.length > day.length) {
            return Date.parse(`${year}-${month}-${day}`);
        } else {
            return Date.parse(`${day}-${month}-${year}`);
        }

    }

    return "";
}

function initMaskTaxReference() {
    $('.tax-reference-mask').each(function () {
        $(this).mask('000.000-00.000000000', {
            reverse: false,
            placeholder: "___.___-__._________",
            clearIfNotMatch: false,
        });
    })
}

function debounceGlobal(func, wait) {
    let timeout;
    return function () {
        const context = this,
            args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(context, args), wait);
    };
}


const get_data_journal = (model, id) => {
    $.ajax({
        type: "post",
        url: `${base_url}/journal/get-data`,
        data: {
            _token: token,
            model: model,
            id: id
        },
        success: function (data) {
            let html = "";
            let total_debit = 0;
            let total_credit = 0;
            $.each(data.journal_details, function (key, value) {
                html += `<tr>
                            <td>${value.coa.account_code} - ${value.coa.name}</td>
                            <td class="text-end">${formatRupiahWithDecimal(value.debit_exchanged)}</td>
                            <td class="text-end">${formatRupiahWithDecimal(value.credit_exchanged)}</td>
                        </tr>`

                total_debit += parseFloat(value.debit_exchanged);
                total_credit += parseFloat(value.credit_exchanged);
            });

            $("#table-journal tfoot").html(`<tr>
                <th class="text-end">Total</th>
                <th class="text-end">${formatRupiahWithDecimal(total_debit)}</th>
                <th class="text-end">${formatRupiahWithDecimal(total_credit)}</th>
            </tr>`)

            $("#table-journal tbody").html(html);
        }
    })
}
