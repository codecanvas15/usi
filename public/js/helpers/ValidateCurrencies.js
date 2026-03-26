/**
 * Validates the currencies and updates the target exchange rate form input accordingly.
 *
 * @param {object} CurrencyOne - The first currency object.
 * @param {object} CurrencyTwo - The second currency object.
 * @param {string} TargetExchangeRateFormInput - The target exchange rate form input selector.
 * @param {string} TargetCurrencyOne - The target currency one selector.
 * @param {string} TargetCurrencyTwo - The target currency two selector.
 * @returns {boolean} - The validation result. True if the currencies are valid, true otherwise.
 */
const validateCurrencies = (
    CurrencyOne,
    CurrencyTwo,
    TargetExchangeRateFormInput,
    TargetCurrencyOne,
    TargetCurrencyTwo
) => {
    let result = true;

    // Check if both currencies are local or both are not local
    if (CurrencyOne.is_local === CurrencyTwo.is_local) {
        result = true;
    }
    // Check if one currency is local and the other is not local
    else {
        result = false;
    }

    $(TargetExchangeRateFormInput).attr("readonly", result);

    // If both currencies are not local
    if (!CurrencyOne.is_local && !CurrencyTwo.is_local) {
        // Check if they have the same ID
        if (CurrencyOne.id === CurrencyTwo.id) {
            result = false;
        }
        // If they have different IDs
        else {
            result = true;
            showAlert({
                title: "Error",
                text: "Salah satu mata uang harus local / rupiah",
                type: "error",
            });

            $(TargetCurrencyOne).val(null).trigger("change");
            $(TargetCurrencyTwo).val(null).trigger("change");
        }
    }

    $(TargetExchangeRateFormInput).attr("readonly", result);

    console.log("==============================");
    console.log(result);
    console.log("==============================");
    return result;
};
