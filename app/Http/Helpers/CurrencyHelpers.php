<?php

use App\Models\Currency;

function get_local_currency()
{
    return Currency::where('is_local', true)->first();
}

function get_local_currency_symbol()
{
    return get_local_currency()->simbol;
}

function get_currency_symbol($id)
{
    return Currency::find($id)?->simbol;
}
