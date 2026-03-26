var residual_value = $('#residual_value');
var estimated_life = $('#estimated_life');
var usage_date = $('#usage_date');
var depreciation_end_date = $('#depreciation_end_date');
var depreciation_value = $('#depreciation_value');
var depreciation_percentage = $('#depreciation_percentage');
var depreciated_value = $('#depreciated_value');

function calculateDepreciationValue(type) {
    if (type == "month") {
        if (thousandToFloat(estimated_life.val()) != 0 && thousandToFloat(estimated_life.val()) != "") {
            var count_depreciation_percentage = (100 / thousandToFloat(estimated_life.val())) * 12;

            depreciation_percentage.val(formatRupiahWithDecimal(count_depreciation_percentage.toFixed(2)))
            var calculate_depreciation_value = thousandToFloat(depreciated_value.val()) / thousandToFloat(estimated_life.val());
            depreciation_value.val(formatRupiahWithDecimal(calculate_depreciation_value));
        } else {
            depreciation_value.val(0);
        }
    } else {
        if (thousandToFloat(depreciation_percentage.val()) != 0 && thousandToFloat(depreciation_percentage.val()) != "") {
            var count_estimated_life = (100 / thousandToFloat(depreciation_percentage.val())) * 12;
            console.log(count_estimated_life);

            estimated_life.val(count_estimated_life.toFixed())
            var calculate_depreciation_value = thousandToFloat(depreciated_value.val()) / thousandToFloat(estimated_life.val());
            depreciation_value.val(formatRupiahWithDecimal(calculate_depreciation_value));
        } else {
            depreciation_value.val(0);
        }
    }

    setEndDepreciation();
}


function setEndDepreciation() {
    var get_start_usage_date = new Date(convertLocalDate(usage_date.val()));
    var get_estimated_life = parseInt(estimated_life.val());

    get_start_usage_date.setMonth(get_start_usage_date.getMonth() + get_estimated_life);

    get_start_usage_date.toISOString().split('T')[0];

    depreciation_end_date.val(formatDate(get_start_usage_date));

}

function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2)
        month = '0' + month;
    if (day.length < 2)
        day = '0' + day;

    return [day, month, year].join('-');
}

const calculate_depreciated_value = () => {
    let value = $('#value').val();
    let residual_value = $('#residual_value').val();

    let get_depreciated_value = thousandToFloat(value) - thousandToFloat(residual_value);
    depreciated_value.val(formatRupiahWithDecimal(get_depreciated_value));

    calculateDepreciationValue('month');
}
