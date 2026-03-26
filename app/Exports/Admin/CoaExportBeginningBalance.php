<?php

namespace App\Exports\Admin;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class CoaExportBeginningBalance implements FromView, ShouldAutoSize, WithTitle, WithColumnFormatting
{
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('admin.coa-import-beginning-balance.export', $this->data);
    }

    public function title(): string
    {
        return 'Saldo Awal COA';
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}
