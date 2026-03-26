<?php

namespace App\Exports\Admin\SaleOrderGeneral;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class HistorySalesOrderExport implements FromView, ShouldAutoSize, WithColumnFormatting, WithEvents
{
    public $data;
    public $view;

    public function __construct($view, $data)
    {
        $this->data = $data;
        $this->view = $view;
    }

    public function view(): View
    {
        return view($this->view, $this->data);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Additional styles and logic can be added here if needed
            }
        ];
    }

    public function columnFormats(): array
    {
        return [
            'M' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'N' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'O' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}

