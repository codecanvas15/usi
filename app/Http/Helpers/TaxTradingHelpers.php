<?php

/**
 * get tax trading data
 *
 * @return \App\Models\TaxTrading
 */
function get_tax_trading()
{
    return \App\Models\TaxTrading::orderByDesc('created_at')
        ->orderByDesc('id')
        ->first();
}
