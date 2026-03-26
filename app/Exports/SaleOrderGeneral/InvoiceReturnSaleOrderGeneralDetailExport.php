<?php

namespace App\Exports\SaleOrderGeneral;

use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class InvoiceReturnSaleOrderGeneralDetailExport implements FromView, ShouldAutoSize, WithColumnFormatting
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
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'J' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'K' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'l' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}
