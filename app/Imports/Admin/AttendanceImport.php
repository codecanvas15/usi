<?php

namespace App\Imports\Admin;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AttendanceImport implements ToModel, WithHeadingRow
{
    /**
     * Format date time to valid excel data
     *
     * @param string $date_time
     * @param string $format
     * @return null|date|string
     */
    private function formaDateTimeExcelData($date_time, $format = 'Y-m-d')
    {
        $date = null;
        try {
            $date = Date::excelToDateTimeObject(trim($date_time))->format($format);
        } catch (\Exception $exception) {
            $date = null;
        }

        return $date;
    }

    /**
     * @param array
     */
    public function model(array $row)
    {
        if (!is_null($row['nik'])) {
            $employee = \App\Models\Employee::where('NIK', $row['nik'])->first();

            if (is_null($employee)) {
                throw new \Exception("Employee with NIK {$row['nik']} not found");
            }

            try {
                return new \App\Models\Attendance([
                    'employee_id' => $employee->id,
                    'branch_id' => $employee->branch_id ?? null,
                    'date' => \Carbon\Carbon::parse($this->formaDateTimeExcelData($row['date']))->format('Y-m-d'),
                    'in_time' => $row['in_time'] ? \Carbon\Carbon::parse($this->formaDateTimeExcelData($row['in_time'], "H:i:s"))->format('H:i:s') : null,
                    'out_time' => $row['out_time'] ? \Carbon\Carbon::parse($this->formaDateTimeExcelData($row['out_time'], "H:i:s"))->format('H:i:s') : null,
                    'go_home_early' => $row['go_home_early'] ? \Carbon\Carbon::parse($this->formaDateTimeExcelData($row['go_home_early']))->format('H:i:s') : null,
                    'late' => $row['late'] ? \Carbon\Carbon::parse($this->formaDateTimeExcelData($row['late']))->format('H:i:s') : null,
                    'overtime' => $row['overtime'] ? \Carbon\Carbon::parse($this->formaDateTimeExcelData($row['overtime']))->format('H:i:s') : null,
                    'work_hours' => $row['work_hours'] ? \Carbon\Carbon::parse($this->formaDateTimeExcelData($row['work_hours']))->format('H:i:s') : null,
                    'attendance_hours' => $row['attendance_hours'] ? \Carbon\Carbon::parse($this->formaDateTimeExcelData($row['attendance_hours']))->format('H:i:s') : null,
                    'description' => $row['description'] ?? null,
                ]);
            } catch (\Throwable $th) {
                throw new \Exception("Failed {$th->getMessage()}");
            }
        }

        return;
    }
}
