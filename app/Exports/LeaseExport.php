<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class LeaseExport implements WithColumnFormatting , FromView
{
    public function __construct(public $view, public $data)
    {
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
            'E' => NumberFormat::FORMAT_NUMBER,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
            'H' => NumberFormat::FORMAT_NUMBER,
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
            'J' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
            'K' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
        ];
    }

    public function view() : View
    {
        return view($this->view, $this->data);
    }
}
