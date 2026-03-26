<?php

use App\Models\Branch;
use App\Models\Employee;
use App\Models\Position;
use App\Models\PurchaseRequest;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * get money format as roman number
 *
 */
function get_months_roman_number($month)
{
    $months = [
        1 => "I",
        2 => "II",
        3 => "III",
        4 => "IV",
        5 => "V",
        6 => "VI",
        7 => "VII",
        8  => "VIII",
        9 => "IX",
        10 => "X",
        11 => "XI",
        12 => "XII",
    ];

    if (Carbon::parse($month)->format('m') < 10) {
        return $months[substr(Carbon::parse($month)->format('m'), 1)];
    } else {
        return $months[Carbon::parse($month)->format('m')];
    }
}

/**
 * function_name
 *
 * @param $code
 * @param $last_code
 * @param $branch_id
 * @return string
 */
function generate_code_transaction($code, $last_code, $branch_sort = null, $date = null)
{
    $branch = $branch_sort ?? get_current_branch_sort();

    if (is_null($date)) {
        $date = date('Ymd');
    } else {
        $date = Carbon::parse($date)->format('Ymd');
    }

    try {
        $explode_code = (int) explode('-', $last_code)[3];
        $explode_code += 1;
    } catch (\Throwable $th) {
        $explode_code = 1;
    }

    if ($explode_code < 10) {
        $final_code = "000$explode_code";
    } elseif ($explode_code < 100) {
        $final_code = "00$explode_code";
    } elseif ($explode_code < 1000) {
        $final_code = "0$explode_code";
    } else {
        $final_code = "$explode_code";
    }

    return "$code-$date-$branch-$final_code";
}

/**
 * function_name
 *
 * @param $code
 * @param $last_code
 * @param $branch_id
 * @return string
 */
function generate_code_transaction_with_out_branch($code, $last_code, $date = null)
{
    if (is_null($date)) {
        $date = date('Ymd');
    } else {
        $date = Carbon::parse($date)->format('Ymd');
    }

    try {
        $explode_code = (int) explode('-', $last_code)[2];
        $explode_code += 1;
    } catch (\Throwable $th) {
        $explode_code = 1;
    }

    if ($explode_code < 10) {
        $final_code = "000$explode_code";
    } elseif ($explode_code < 100) {
        $final_code = "00$explode_code";
    } elseif ($explode_code < 1000) {
        $final_code = "0$explode_code";
    } else {
        $final_code = "$explode_code";
    }

    return "$code-$date-$final_code";
}


/**
 * generate_code_purchase_request
 *
 * @param $last_code
 * @param $branch_id
 * @return string
 */
function generate_code_purchase_request($last_code, $branch_sort = null, $year = null, $date = null)
{
    $branch = $branch_sort ?? get_current_branch_sort();

    $unChangedYear = $year;

    if ($date) {
        $unChangedYear = $date;
    }

    if (is_null($year)) {
        $year = date('Y');
    } else {
        $year = Carbon::parse($year)->format('Y');
    }

    try {
        $explode_code = (int) explode('/', $last_code ?? "0000/0000/00/0000")[1];
        $explode_code += 1;
    } catch (\Throwable) {
        $explode_code = 1;
    }

    if ($explode_code < 10) {
        $final_code = "000$explode_code";
    } elseif ($explode_code < 100) {
        $final_code = "00$explode_code";
    } elseif ($explode_code < 1000) {
        $final_code = "0$explode_code";
    } else {
        $final_code = "$explode_code";
    }

    $result =  "$branch/$final_code/" . get_months_roman_number($unChangedYear) . "/$year";
    return check_existing_code_pr($result, $final_code);
}

function check_existing_code_pr($code, $final_code)
{
    $pr = PurchaseRequest::withTrashed()->where('kode', $code)->first();
    if ($pr) {
        $explode_code = explode('/', $code);
        $new_final_code = abs($final_code) + 1;
        $new_code = $explode_code[0] . '/' . sprintf("%04s", $new_final_code) . '/' . $explode_code[2] . '/' . $explode_code[3];
        return check_existing_code_pr($new_code, $new_final_code);
    } else {
        return $code;
    }
}

/**
 * Generate code purchase request update
 *
 * @param string $code
 * @param string $
 */
function generate_code_purchase_request_update(string $code): string
{
    $explode = explode("/", $code);

    $final_code = '';
    if (array_key_exists(4, $explode)) {
        $revision_code = (int) ltrim($explode[4], "R");
        $revision_code_final = sprintf("%04s", abs($revision_code) + 1);

        unset($explode[4]);
        $final_code = join("/", $explode) . "/R$revision_code_final";
    } else {
        $final_code .= $code . "/";
        $final_code .= "R0001";
    }

    return $final_code;
}


/**
 * Generate code transaction
 *
 * @param class $model
 * @param string $column
 * @param string $date_column
 * @param string $code
 * @param string $branch_sort
 * @param string $date
 */
function generate_code($model, $column, $date_column, $code, $branch_sort = null, $date = null, $count = 0)
{
    $branch_sort = $branch_sort ?? get_current_branch_sort();
    $branch = Branch::where('sort', $branch_sort)->first();

    $last_code = $model::orderBy('id', 'desc')
        ->whereMonth($date_column, Carbon::parse($date)->format('m') ?? Carbon::now()->format('m'))
        ->whereYear($date_column, Carbon::parse($date)->format('Y') ?? Carbon::now()->format('Y'))
        ->when(in_array(SoftDeletes::class, class_uses($model)), function ($q) {
            return $q->withTrashed();
        })
        ->first();

    $explode_code = null;
    if ($last_code) {
        $explode_code = explode('-', $last_code->$column)[3] ?? null;
    }
    if ($last_code && $explode_code) {
        if (!isset($explode_code)) {
            $explode_code = explode('-', $last_code->$column)[4];
        }
        $sequence = sprintf("%04s", abs($explode_code) + 1 + $count);
    } else {
        $sequence = '0001';
    }

    if (is_null($date)) {
        $date = date('ymd');
    } else {
        $date = Carbon::parse($date)->format('ymd');
    }

    return "$code-$date-$branch_sort-$sequence";
}

/**
 * Generate code transaction update
 *
 * @param string $code
 * @return string
 */
function generate_code_update(string $code): string
{
    $explode = explode("-", $code);

    $final_code = '';
    if (array_key_exists(4, $explode)) {
        $revision_code = (int) ltrim($explode[4], "R") + 1;
        $revision_code_final = sprintf("%04s", abs($revision_code));

        unset($explode[4]);
        $final_code = join("-", $explode) . "-R$revision_code_final";
    } else {
        $final_code .= $code . "-";
        $final_code .= "R0001";
    }

    return $final_code;
}

/**
 * Generate code transaction trading
 *
 * @param class $model
 * @param string $code
 * @param string $code2
 * @param string $date
 * @return string
 */
function generate_trading_code($model, $code, $code2, $date_column, $date = null)
{
    // $code = "PO", "LPB", "SO", dan lain lain;
    // $code2 = VENDOR, WAREHOUSE, atau CUSTOMER; contoh: PERPAT

    if (is_null($date)) {
        $month = date('m');
        $year = date('y');
    } else {
        $month = Carbon::parse($date)->format('m');
        $year = Carbon::parse($date)->format('y');
    }

    $sequence = sprintf("%03s", abs(
        $model::whereMonth($date_column, Carbon::parse($date)->format('m') ?? Carbon::now()->format('m'))
            ->whereYear($date_column, Carbon::parse($date)->format('Y') ?? Carbon::now()->format('Y'))
            ->get()
            ->count()
    ) + 1);

    return $code . $sequence . "/" . config('app.short_name', 'USI') . "/" . explode('-', $code2)[1] . "/" . $month . "/" . $year;
}

/**
 * Generate code for trading
 *
 * @param $model
 * @return string $code
 *
 */
function generate_code_with_cus_name($model, $code, $code2, $date_column, $date = null, $code3 = null, $filter = [])
{
    // $code = "PO", "LPB", "SO", dan lain lain;
    // $code2 = VENDOR, WAREHOUSE, atau CUSTOMER; contoh: PERPAT

    $explode_customer = explode(' ', $code2?->nama);
    $explode_customer_code = explode('-', $code2?->code);
    $title = '';
    // Check If array
    if (is_array($explode_customer) && count($explode_customer) > 0) {
        // Check text if have text PT, CV, UD.
        if ($explode_customer[0] == 'PT.' || $explode_customer[0] == 'CV.' || $explode_customer[0] == 'UD.' || $explode_customer[0] == 'PT' || $explode_customer[0] == 'CV' || $explode_customer[0] == 'UD' || $explode_customer[0] == 'PT' || $explode_customer[0] == 'OB') {
            if (count($explode_customer) > 2) {
                foreach ($explode_customer as $key => $value) {
                    if ($key > 0 && $key < 4) {
                        $get_first_char = substr($value, 0, 1);
                        if ($get_first_char != '(') {
                            $title .= $get_first_char;
                        }
                    }
                }
            } else {
                foreach ($explode_customer as $key => $value) {
                    if ($key > 0 && $key < 4) {
                        $get_first_char = substr($value, 0, 1);
                        if ($get_first_char != '(') {
                            $title .= $get_first_char;
                        }
                    }
                }
            }
        } else {
            if (count($explode_customer) > 1) {
                foreach ($explode_customer as $key => $value) {
                    if ($key >= 0 && $key < 4) {
                        $get_first_char = substr($value, 0, 1);
                        if ($get_first_char != '(') {
                            $title .= $get_first_char;
                        }
                    }
                }
            } else {
                foreach ($explode_customer as $key => $value) {
                    if ($key >= 0 && $key < 4) {
                        $get_first_char = substr($value, 0, 1);
                        if ($get_first_char != '(') {
                            $title .= $get_first_char;
                        }
                    }
                }
            }
        }

        if (is_null($code3)) {
            $title = $title . $explode_customer_code[2];
        } else {
            $title = ($title == '') ? $code3 : $title . $code3;
        }
    } else {
        $title = is_null($code3) ? 'CUS' : $code3;
    }

    if (is_null($date)) {
        $month = date('m');
        $year = date('y');
    } else {
        $month = Carbon::parse($date)->format('m');
        $year = Carbon::parse($date)->format('y');
    }

    $count = $model::whereMonth($date_column, Carbon::parse($date) ?? Carbon::now())
        ->when(count($filter) > 0, function ($query) use ($filter) {
            $query->where($filter);
        })
        ->whereYear($date_column, Carbon::parse($date) ?? Carbon::now())
        ->when(in_array(SoftDeletes::class, class_uses($model)), function ($q) {
            return $q->withTrashed();
        })
        ->get()
        ->count();
    $sequence = sprintf("%03s", abs($count) + 1);

    $short_name = getCompany()->short_name ?? config('app.short_name') ?? 'USI';
    return $code . $sequence . "/" . $short_name . "/" . $title . "/" . $month . "/" . $year;
}

/**
 * Generate code transaction trading update
 *
 * @param string $code
 * @return string
 */
function generate_trading_code_update(string $code): string
{
    $explode = explode("/", $code);

    $final_code = '';
    if (array_key_exists(5, $explode)) {
        $revision_code = (int) ltrim($explode[5], "R") + 1;
        $revision_code_final = sprintf("%04s", abs($revision_code));

        unset($explode[5]);
        $final_code = join("/", $explode) . "/R$revision_code_final";
    } else {
        $final_code .= $code . "/";
        $final_code .= "R0001";
    }

    return $final_code;
}


/**
 * Generate code for vendor customer
 *
 * @param string $name
 */
function generate_vendor_customer_code($name)
{
    $except = [
        'PT',
        'PT.',
        'PT,',
        'TBK',
        'TBK.',
        'TBK,',
        'PERSERO',
        'PERSERO.',
        'PERSERO,',
        'CV',
        'CV.',
        'CV,',
        'PD',
        'PD.',
        'PD,',
        'UD',
        'UD.',
        'UD,',
        '(PERSERO)',
        '(PT)',
        '(TBK)',
        'PERUM',
        'PERURI',
    ];

    $blacklistChars = '(.,)';
    $pattern = preg_quote($blacklistChars, '/');

    $name_explode = explode(" ", $name);
    $tmpName = [];

    foreach ($name_explode as $word) {
        if (!preg_match('/[' . $pattern . ']/', $word) && !in_array($word, $except)) {
            array_push($tmpName, $word);
        }
    }

    // get first characther of first word
    $first_char = isset($tmpName[0]) ? substr($tmpName[0], 0, 1) : 'X';
    $second_char = isset($tmpName[1]) ? substr($tmpName[1], 0, 1) : 'X';
    $third_char = isset($tmpName[2]) ? substr($tmpName[2], 0, 1) : 'X';

    $output = $first_char . $second_char . $third_char;

    return $output;
}


function generate_receipt_code($last_code, $date, $code)
{
    $running_number = 0;
    if ($last_code) {
        try {
            $explode_code = explode('/', $last_code);
            $running_number = $explode_code[0];
        } catch (\Throwable $th) {
            $running_number = 0;
        }
    }

    $month = Carbon::parse($date)->format('m');
    $month = get_months_roman_number($date);
    $final_code = sprintf("%04s", abs($running_number) + 1);
    $company_short_name = getCompany()->short_name ?? 'USI';
    $result =  "$final_code/$code/$company_short_name/$month/" . Carbon::parse($date)->format('Y');

    return $result;
}

function generate_employee_code($position_id, $join_date)
{
    $position = Position::find($position_id);
    if (!$position->code) {
        throw new \Exception('Position code not found');
    }
    $count_registered_employee = Employee::whereHas('position', function ($q) use ($position) {
        $q->where('code', $position->code);
    })
        ->count();

    return "$position->code-" . Carbon::parse($join_date)->format('my') . sprintf("%04s", abs($count_registered_employee) + 1);
}
