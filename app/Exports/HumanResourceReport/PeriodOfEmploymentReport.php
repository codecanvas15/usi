<?php

namespace App\Exports\HumanResourceReport;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class PeriodOfEmploymentReport implements FromView, ShouldAutoSize, WithColumnFormatting
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
            // 'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            // 'J' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            // 'K' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}
