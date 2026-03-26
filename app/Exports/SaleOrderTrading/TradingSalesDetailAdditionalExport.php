<?php

namespace App\Exports\SaleOrderTrading;

use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class TradingSalesDetailAdditionalExport implements FromView, ShouldAutoSize, WithColumnFormatting
{
    public function __construct(public $view, public $data)
    {
        //
    }

    public function view(): View
    {
        return view($this->view, $this->data);
    }

    public function columnFormats(): array
    {
        return [
            'L' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'M' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'N' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'O' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'P' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'Q' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}
