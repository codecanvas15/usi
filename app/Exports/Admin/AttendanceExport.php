<?php

namespace App\Exports\Admin;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Str;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Where clause for employee
     *
     * @var string|null
     */
    private $employee_id = null;

    /**
     * Where clause for date
     *
     * @var string|string
     */
    private $from_date = null;

    /**
     * Where clause for date
     *
     * @var string
     */
    private $to_date = null;

    /**
     * initial
     *
     * @param arguments
     * @return arguments
     */
    public function __construct($employee = null, $from = null, $to = null)
    {
        $this->employee_id = $employee;
        $this->from_date = $from;
        $this->to_date = $to;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return \App\Models\Attendance::with(['employee', 'branch'])
            ->when($this->employee_id, function ($q) {
                return $q->where('employee_id', $this->employee_id);
            })
            ->when($this->from_date, function ($q) {
                return $q->whereDate('date', '>=', $this->from_date);
            })
            ->when($this->to_date, function ($q) {
                return $q->whereDate('date', '<=', $this->to_date);
            })
            ->get();
    }

    public function map($row): array
    {
        return [
            $row->id,
            "{$row->employee->name} - {$row->employee->NIK}",
            $row->branch?->name,
            $row->date,
            $row->in_time,
            $row->out_time,
            $row->go_home_early,
            $row->late,
            $row->overtime,
            $row->work_hours,
            $row->attendance_hours,
            $row->description,
            toDayDateTimeString($row->created_at),
            toDayDateTimeString($row->updated_at),
        ];
    }

    public function headings(): array
    {
        return [
            Str::headline('#'),
            Str::headline('employee'),
            Str::headline('branch'),
            Str::headline('tanggal'),
            Str::headline('masuk'),
            Str::headline('keluar'),
            Str::headline('pulang lebih cepat'),
            Str::headline('telambat'),
            Str::headline('lembur'),
            Str::headline('jam kerja'),
            Str::headline('jam hadir'),
            Str::headline('deskripsi'),
            Str::headline('created at'),
            Str::headline('updated_at'),
        ];
    }
}
