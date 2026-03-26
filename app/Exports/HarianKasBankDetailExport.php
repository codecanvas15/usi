<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class HarianKasBankDetailExport implements FromView, ShouldAutoSize, WithColumnFormatting
{
    public function __construct($view, $data)
    {
        $this->data = $data;
        $this->view = $view;
    }

    public function view(): View
    {
        return view($this->view, $this->data);
    }

    public function columnFormats(): array
    {
        return [
            'C' => '#,##0.00;(#,##0.00)',
            'D' => '#,##0.00;(#,##0.00)',
            'E' => '#,##0.00;(#,##0.00)',
            'F' => '#,##0.00;(#,##0.00)',
            'G' => '#,##0.00;(#,##0.00)',
            'H' => '#,##0.00;(#,##0.00)',
            'I' => '#,##0.00;(#,##0.00)',
            'J' => '#,##0.00;(#,##0.00)',
            'K' => '#,##0.00;(#,##0.00)',
            'L' => '#,##0.00;(#,##0.00)',
            'M' => '#,##0.00;(#,##0.00)',
            'N' => '#,##0.00;(#,##0.00)',
            'O' => '#,##0.00;(#,##0.00)',
            'P' => '#,##0.00;(#,##0.00)',
            'Q' => '#,##0.00;(#,##0.00)',
            'R' => '#,##0.00;(#,##0.00)',
            'S' => '#,##0.00;(#,##0.00)',
            'T' => '#,##0.00;(#,##0.00)',
            'U' => '#,##0.00;(#,##0.00)',
            'V' => '#,##0.00;(#,##0.00)',
            'W' => '#,##0.00;(#,##0.00)',
            'X' => '#,##0.00;(#,##0.00)',
            'Y' => '#,##0.00;(#,##0.00)',
            'Z' => '#,##0.00;(#,##0.00)',
        ];
    }
}
