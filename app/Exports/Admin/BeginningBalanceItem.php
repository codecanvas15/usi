<?php

namespace App\Exports\Admin;

use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class BeginningBalanceItem implements FromView, ShouldAutoSize, WithTitle, WithColumnFormatting
{
    public function __construct(public $data)
    {
        //
    }

    public function view(): View
    {
        return view('admin.item-beginning-balance.export', $this->data);
    }

    public function title(): string
    {
        return 'Saldo Awal COA';
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}
