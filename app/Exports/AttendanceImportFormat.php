<?php

namespace App\Exports;

use App\Models\Attendance;
use App\Models\PermissionLetterEmployee;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class AttendanceImportFormat implements FromView, ShouldAutoSize, WithTitle, WithColumnFormatting
{
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        $request = $this->data;
        $employees = DB::table('employees')
            ->when($request->employee_id, function ($query) use ($request) {
                $query->where('id', $request->employee_id);
            })
            ->whereNull('deleted_at')
            ->orderBy('name', 'asc')
            ->get();

        $date_start = Carbon::parse($request->from_date)->format('j');
        $date_end = Carbon::parse($request->to_date)->format('j');
        $month = Carbon::parse($request->from_date)->format('m');
        $year = Carbon::parse($request->to_date)->format('Y');

        $attendances = Attendance::whereDate('date', '>=', Carbon::parse($request->from_date))
            ->whereDate('date', '<=', Carbon::parse($request->to_date))
            ->whereIn('employee_id', $employees->pluck('id'))
            ->get();

        $dates = [];
        $date_strings = [];
        for ($i = $date_start; $i <= $date_end; $i++) {
            $dates[] = (int) Date::timestampToExcel(Carbon::parse($year . '-' . $month . '-' . $i . ' 07:00:00')->timestamp);
            $date_strings[] = Carbon::parse($year . '-' . $month . '-' . $i)->format('Y-m-d');
        }

        return view('admin.attendance.import-format', compact('employees', 'date_start', 'date_end', 'month', 'year', 'dates', 'attendances', 'date_strings'));
    }

    public function title(): string
    {
        return 'PRESENSI KARYAWAN';
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'D' => NumberFormat::FORMAT_DATE_TIME4,
            'E' => NumberFormat::FORMAT_DATE_TIME4,
            'F' => NumberFormat::FORMAT_DATE_TIME4,
            'G' => NumberFormat::FORMAT_DATE_TIME4,
            'H' => NumberFormat::FORMAT_DATE_TIME4,
            'I' => NumberFormat::FORMAT_DATE_TIME4,
            'J' => NumberFormat::FORMAT_DATE_TIME4,
        ];
    }
}
