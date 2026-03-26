<?php

use App\Models\Period;

function generate_period($year)
{
    $months = [
        'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Jull',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember',
    ];

    foreach ($months as $month_key => $month_value) {
        for ($i = 0; $i < 2; $i++) {
            $start_date = $i == 0 ? date('Y-m-d', strtotime($year.'/'.($month_key + 1).'/'.'01')) : date('Y-m-d', strtotime($year.'/'.($month_key + 1).'/'.'15'));
            $last_date = $i == 0 ? date('Y-m-d', strtotime($year.'/'.($month_key + 1).'/'.'14')) : date('Y-m-d', strtotime($year.'/'.($month_key + 1).'/'.explode('-', date('Y-m-t', strtotime($start_date)))[2]));

            $end = explode('-', date('Y-m-t', strtotime($start_date)))[2];
            Period::create([
                'tahun' => $year,
                'value' => ($i == 0 ? '1-14 ' : ' 15-'.$end).' '.$month_value.' '.$year,
                'tanggal_mulai' => $start_date,
                'tanggal_akhir' => $last_date,
            ]);
        }
    }
}
