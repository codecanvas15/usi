<?php

namespace App\Exports;

use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class EmployeeExport implements FromView, ShouldAutoSize, WithColumnFormatting
{
    public function __construct(public $view, public $data)
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
            'G' => NumberFormat::FORMAT_TEXT,
            'J' => NumberFormat::FORMAT_TEXT,
            'K' => NumberFormat::FORMAT_TEXT,
            'R' => NumberFormat::FORMAT_TEXT,
            'S' => NumberFormat::FORMAT_TEXT,
            'U' => NumberFormat::FORMAT_TEXT
        ];
    }
}
