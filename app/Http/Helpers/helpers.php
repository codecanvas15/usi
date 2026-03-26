<?php

use App\Models\BankCodeMutation;
use App\Models\ClosingPeriod;
use App\Models\Coa;
use App\Models\DefaultCoa;
use App\Models\Tax;
use App\Supports\CompanyProfileSupport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

function replaceComma($amount)
{
    return number_format((float) str_replace(',', '', $amount), 2, '.', '');
}

function floatDotFormat($amount)
{
    return number_format($amount, 2, '.', ',');
}


function floatFormat($amount)
{
    return number_format($amount, 2, '.', '');
}

function checkNumber($number)
{
    $ex_number = explode('.', $number);

    if (isset($ex_number[1])) {
        if ($ex_number[1] == 00) {
            return number_format($number);
        } else {
            return floatDotFormat($number);
        }
    }

    return number_format($number);
}

function replaceDot($amount)
{
    return str_replace('.', '', $amount);
}

/**
 * commas separator
 *
 * @return int
 */
function commas_separator($num)
{
    return number_format($num, 2, ',', '.');
}

/**
 * thousand formater
 *
 * @param  int  $number
 * @return int
 */
function thousand_formater(int $number)
{
    $units = ['', 'K', 'M', 'B', 'T'];
    for ($i = 0; $number >= 1000; $i++) {
        $number /= 1000;
    }

    return round($number, 1) . $units[$i];
}

/**
 * decimal to percent formater
 *
 * @param  int  $number
 * @return int
 */
function decimal_to_percent($number)
{
    return $number * 100 . '%';
}

/**
 * thousand formater
 *
 * @param  int  $num
 * @return string|int
 */
function thousands_currency_format($num): int|string
{
    if ($num > 1000) {
        $x = round($num);
        $x_number_format = number_format($x);
        $x_array = explode(',', $x_number_format);
        $x_parts = ['k', 'm', 'b', 't'];
        $x_count_parts = count($x_array) - 1;
        $x_display = $x;
        $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
        $x_display .= $x_parts[$x_count_parts - 1];

        return $x_display;
    }

    return $num;
}

/**
 * transform thousand to float
 * 100.000.000,98 => 100000000.98
 * 100.000.000 => 100000000
 *
 * @param  string  $value
 * @return float
 */
function thousand_to_float($value)
{
    // ! OLD
    // $explode = explode(',', $value);
    // if (array_key_exists(1, $explode)) {
    //     return (float) implode('', explode('.', $explode[0])) . '.' . $explode[1];
    // } else {
    //     return (float) implode('', explode('.', $explode[0]));
    // }

    // check if value is numeric
    if (is_numeric($value)) {
        return $value;
    }


    // * NEW
    $value = str_replace('.', '', $value);
    $value = str_replace(',', '.', $value);
    return (float) $value;
}

/**
 * transform thousand to float
 * 100.000.000,98 => 100000000.98
 * 100.000.000 => 100000000
 *
 * @param  string  $value
 * @return float
 */
function thousand_to_float_commas(string $value): float
{
    $explode = explode('.', $value);
    if (array_key_exists(1, $explode)) {
        return (float) implode('', explode('.', $explode[0])) . '.' . $explode[1];
    } else {
        return (float) implode('', explode('.', $explode[0]));
    }
}


/**
 * 10000.00 => 10.000,000
 *
 * @param string|int|float $amount
 * @return float|string
 */
function floatDotThreeDigitsFormat($amount)
{
    return number_format($amount, 3, ',', '.');
}


/**
 * Format a numeric value with two decimal places using a comma as the decimal
 * separator and a dot as the thousands separator. Optionally, negative values
 * can be displayed in parentheses instead of using a leading minus sign.
 *
 * Example:
 *  - 10000.000 => "10.000,00"
 *  - 10000.123 => "10.000,12"
 *  - -10000.00 with $alternate_negative_display = true => "(10.000,00)"
 *
 * @param float|int|string $number The numeric value to format.
 * @param bool $alternate_negative_display When true, negative numbers are
 *                                         displayed with parentheses instead
 *                                         of a minus sign.
 *
 * @return string The formatted number as a string.
 */
function formatNumber($number, $alternate_negative_display = false)
{
    $number = (float) $number;
    $is_negative = $number < 0;
    $formatted = number_format(abs($number), 2, ',', '.');

    if ($is_negative) {
        if ($alternate_negative_display) {
            $formatted = '(' . $formatted . ')';
        } else {
            $formatted = '-' . $formatted;
        }
    }

    return $formatted;
}

function localDate($date, $format = 'd-m-Y')
{
    try {
        if ($date) {
            return Carbon::parse($date)->format($format);
        }

        return '';
    } catch (\Throwable $th) {
        return '';
    }
}

function localDateTime($date)
{
    try {
        if ($date) {
            return Carbon::parse($date)->format('d/m/Y H:i');
        }

        return '';
    } catch (\Throwable $th) {
        return '';
    }
}

function get_default_coa($type, $name = null)
{
    $default_coa = DefaultCoa::with('coa')
        ->where('type', $type);

    if ($name) {
        $default_coa->where('name', $name);
        return $default_coa = $default_coa->first();
    }

    return $default_coa = $default_coa->get();
}

function formatRating($id)
{
    if ($id == 1) {
        return $id . ' - Unacceptable';
    } elseif ($id == 2) {
        return $id . ' - Poor';
    } elseif ($id == 3) {
        return $id . ' - Fair';
    } elseif ($id == 4) {
        return $id . ' - Good';
    } else {
        return $id . ' - Excellent';
    }
}

function formatRecommendStatus($status)
{
    if ($status == 'y') {
        return "1st Choice";
    } elseif ($status == 'r') {
        return "2nd Choice";
    } else {
        return "Not A Fit";
    }
}

function generate_bank_code($ref_model = '', $ref_id = null, $coa_id = null, $type = 'in', $date = null, $is_save = false, $code = null)
{
    if ($code) {
        $return_code = $code;
    } else {
        $year_month = Carbon::parse($date)->format('Ym');
        $last_code = BankCodeMutation::where('coa_id', $coa_id)
            ->where('type', $type)
            ->whereMonth('date', Carbon::parse($date))
            ->whereYear('date', Carbon::parse($date))
            ->where('is_generate', 1)
            ->max('code');

        if ($last_code) {
            $explode_code = explode('-', $last_code)[2];
            $sequence = sprintf("%04s", abs($explode_code) + 1);
        } else {
            $sequence = '0001';
        }

        $bank_code = Coa::find($coa_id)->bank_internal->code ?? '';

        if (!$bank_code) {
            return false;
        }

        if ($type == "in") {
            $return_code = "{$bank_code}M-{$year_month}-$sequence";
        } else {
            $return_code = "{$bank_code}K-{$year_month}-$sequence";
        }
    }

    $find_same_code = BankCodeMutation::where('code', $return_code)
        ->whereMonth('date', Carbon::parse($date))
        ->whereYear('date', Carbon::parse($date))
        ->first();

    if ($find_same_code) {
        return false;
    }

    if ($is_save) {
        BankCodeMutation::create(
            [
                'coa_id' => $coa_id,
                'date' => Carbon::parse($date),
                'ref_model' => $ref_model,
                'ref_id' => $ref_id,
                'type' => $type,
                'code' => $return_code,
                'is_generate' => $code ? 0 : 1,
            ]
        );
    }

    return $return_code;
}


/**
 * Generate journal order column
 *
 * @param string $date
 * @return string
 */
function generate_journal_order($date)
{
    $max_ordering = DB::table('journal_details')
        ->join('journals', 'journals.id', 'journal_details.journal_id')
        ->whereDate('journal_details.timestamp', Carbon::parse($date))
        ->max('ordering');

    if (!$max_ordering) {
        $new_ordering = Carbon::parse($date)->format('Ymd') . "-" . sprintf("%05s", 1);
    } else {
        $explode_ordering = explode("-", $max_ordering)[1];
        $new_ordering = Carbon::parse($date)->format('Ymd') . "-" . sprintf("%05s", $explode_ordering + 1);
    }
    return $new_ordering;
}

/**
 * Generate stock mutation order column
 *
 * @param string $date
 * @return string
 */
function generate_stock_mutation_order($date)
{
    $max_ordering = DB::table('stock_mutations')
        ->whereDate('stock_mutations.date', Carbon::parse($date))
        ->max('ordering');

    if (!$max_ordering) {
        $new_ordering = Carbon::parse($date)->format('Ymd') . "-" . sprintf("%05s", 1);
    } else {
        $explode_ordering = explode("-", $max_ordering)[1];
        $new_ordering = Carbon::parse($date)->format('Ymd') . "-" . sprintf("%05s", $explode_ordering + 1);
    }
    return $new_ordering;
}

/**
 * Check available date for generated journal
 *
 * @return bool
 */
function checkAvailableDate($date): bool
{
    $closing = ClosingPeriod::whereDate('to_date', '>=', Carbon::parse($date))
        ->first();

    $data = true;
    if ($closing && $closing->status == "close") {
        $data = false;
    }

    return $data;
}

// remove all special char
function removeSpecialChar($value)
{
    $result = preg_replace('/[^a-zA-Z0-9_ -]/s', '', $value);

    return $result;
}

// add 3 0 to front id
function addThreeZeroOnFront($value)
{
    if ($value) {
        $str_to_array = str_split($value);
        $changeValue = '';
        switch (count($str_to_array)) {
            case 1:
                $changeValue = '000' . $value;
                break;

            case 2:
                $changeValue = '00' . $value;
                break;

            case 3:
                $changeValue = '0' . $value;
                break;

            default:
                $changeValue = $value;
                break;
                break;
        }

        return $changeValue;
    }
}

function encryptId($id)
{
    return Crypt::encrypt($id);
}

function decryptId($id)
{
    return Crypt::decrypt($id);
}

function toDayDateTimeString($timestamp)
{
    return Carbon::parse($timestamp)->translatedFormat('l, d F Y H:i');
}

function getBaseUrlFromLink($link)
{
    $parsedUrl = parse_url($link);

    // Check if the scheme (http, https) is set, otherwise default to http
    $scheme = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] : 'http';

    // Check if the port is set, otherwise default to the standard ports (80 for http, 443 for https)
    $port = isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '';

    // Build the base URL
    $baseUrl = $scheme . '://' . $parsedUrl['host'] . $port;

    return $baseUrl;
}

function getCompany()
{
    $companySupport = new CompanyProfileSupport();
    return $companySupport->company();
}

function getDafaultTaxes()
{
    $taxes = Tax::query()
        ->where('is_default', 1)
        ->get();

    return $taxes;
}

function authorizePrint($type)
{
    return \App\Models\MasterPrintAuthorization::where('type', $type)->where('can_print', 1)->exists();
}

function toLocalLink($link)
{
    try {
        if (!$link) {
            return $link;
        }

        $parsedUrl = parse_url($link);
        $baseUrl = rtrim(config('app.url') ?: url('/'), '/');

        $path = $parsedUrl['path'] ?? '';
        $path = preg_replace('#^(\./|\.\./)+#', '', $path);
        $path = '/' . ltrim($path, '/');

        $localUrl = $baseUrl . $path;

        if (isset($parsedUrl['query']) && $parsedUrl['query'] !== '') {
            $localUrl .= '?' . $parsedUrl['query'];
        }

        if (isset($parsedUrl['fragment']) && $parsedUrl['fragment'] !== '') {
            $localUrl .= '#' . $parsedUrl['fragment'];
        }

        return $localUrl;
    } catch (\Throwable $th) {
        return $link;
    }
}
