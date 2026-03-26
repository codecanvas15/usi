<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class AssetExport implements FromView, WithColumnFormatting
{
    public function __construct(public $view, public $data) {}

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_NUMBER,
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
            'F' => NumberFormat::FORMAT_NUMBER,
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
            'J' => NumberFormat::FORMAT_NUMBER,
            'K' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
            'L' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
            'M' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
        ];
    }

    public function view(): View
    {
        return view($this->view, $this->data);
    }
}
