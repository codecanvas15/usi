<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class LeaveExport implements FromView, ShouldAutoSize, WithTitle
{
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('leave.excel', $this->data);
    }

    public function title(): string
    {
        return 'CUTI KARYAWAN';
    }
}
