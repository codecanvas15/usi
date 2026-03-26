<?php

namespace App\Http\Helpers;

class CurrenciesValidator
{
    public function checkCurrencies(\App\Models\Currency $currencyOne, \App\Models\Currency $currencyTwo)
    {
        // * if both currency is local
        if ($currencyOne->is_local && $currencyTwo->is_local) {
            return true;
        }

        // * if one of those not local
        if (($currencyOne->is_local && !$currencyTwo->is_local) || (!$currencyOne->is_local && $currencyTwo->is_local)) {
            return false;
        }

        // * if both not local and same currency
        if ($currencyOne->id == $currencyTwo->id) {
            return false;
        }

        // * if both not local and different currency
        throw new \Exception('Invalid currency combination');
    }
}
