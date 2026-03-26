<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class HarianKasBankExport implements FromView, ShouldAutoSize, WithColumnFormatting
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
        ];
    }
}
