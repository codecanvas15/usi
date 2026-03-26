<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class NeracaSaldoExport implements FromView, ShouldAutoSize, WithColumnFormatting
{
    public function __construct($view, $data)
    {
        $this->view = $view;
        $this->data = $data;
    }

    public function view(): View
    {
        return view($this->view, $this->data);
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'C' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'J' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'K' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'L' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'M' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'N' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'O' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'P' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'Q' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'R' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'S' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'T' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'U' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'V' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'W' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'Y' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'Z' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'AA' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'AB' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'AC' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'AD' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'AE' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'AF' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'AG' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'AH' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'AI' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'AJ' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'AK' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'AL' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}
