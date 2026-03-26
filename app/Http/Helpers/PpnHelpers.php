<?php

const PPN = .11;

/**
 * get value ppn
 *
 * @return float
 */
function get_ppn(): float
{
    return PPN;
}

/**
 * get value ppn as string
 *
 * @return string
 */
function get_ppn_as_percent(): string
{
    return PPN * 100 . "%";
}


/**
 * get value ppn as string
 *
 * @param int|float|string
 * @return string
 */
function get_ppn_as_percent_from_value($value): string
{
    return $value * 100 . "%";
}


/**
 * calculate ppn with amount
 *
 * @param float|int|double $amount
 */
function calculate_ppn(float|int $amount): float|int
{
    return $amount * PPN;
}
