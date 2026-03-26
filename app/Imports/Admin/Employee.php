<?php

namespace App\Imports\Admin;

use App\Models\Employee as ModelsEmployee;
use App\Models\EmploymentStatus;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Str;



class Employee implements ToModel, WithHeadingRow
{
    public function __construct()
    {
        HeadingRowFormatter::default('none');
    }

    public function transformDate($value)
    {
        try {
            if ($value) {
                $rtrn = Date::excelToDateTimeObject(trim($value))->format('Y-m-d');
            } else {
                $rtrn = null;
            }
        } catch (\Exception $exception) {
            $rtrn = null;
        }

        return $rtrn;
    }

    public function model($row)
    {
        $branch = \App\Models\Branch::where('name', $row['Cabang'])->first();
        if (!$branch) {
            $branch = \App\Models\Branch::first();
        }

        $division = \App\Models\Division::where('name', $row['Divisi'])->first();
        if (!$division && $row['Divisi'] != '') {
            $division = \App\Models\Division::create(
                [
                    'name' => $row['Divisi'],
                ]
            );
        }

        $position = \App\Models\Position::where('nama', $row['Posisi'])->first();
        if (!$position && $row['Posisi'] != '') {
            $position = \App\Models\Position::create(
                [
                    'nama' => $row['Posisi'],
                ]
            );
        }

        $employment_status = \App\Models\EmploymentStatus::where('name', $row['Status Kepegawaian'])->first();
        if (!$employment_status && $row['Status Kepegawaian'] != '') {
            $employment_status = EmploymentStatus::create([
                'name' => $row['Status Kepegawaian'],
            ]);
        }

        $education = \App\Models\Education::where('name', $row['Pendidikan Terakhir'])->first();
        if (!$education && $row['Pendidikan Terakhir'] != '') {
            $education = \App\Models\Education::create([
                'name' => $row['Pendidikan Terakhir'],
            ]);
        }

        $degree = \App\Models\Degree::where('name', $row['Gelar'])->first();
        if (!$degree && $row['Gelar'] != '') {
            $degree = \App\Models\Degree::create([
                'name' => $row['Gelar'],
            ]);
        }

        $non_taxable_income = \App\Models\NonTaxableIncome::where('note', $row['Status Pernikahan'])->first();
        if (!$non_taxable_income) {
            $non_taxable_income = \App\Models\NonTaxableIncome::first();
        }

        return new ModelsEmployee([
            'branch_id' => $branch->id ?? null,
            'division_id' => $division->id ?? null,
            'employment_status_id' => $employment_status->id ?? null,
            'education_id' => $education->id ?? null,
            'degree_id' => $degree->id ?? null,
            'email' => $row['Email'],
            'name' => $row['Nama'],
            'NIK' => $row['NIK'],
            'alamat' => $row['Alamat KTP'],
            'alamat_domisili' => $row['Alamat Domisili'],
            'nomor_telepone' => $row['Nomor HP'],
            'tempat_lahir' => $row['Tempat Lahir'],
            'tanggal_lahir' => $this->transformDate($row['Tanggal Lahir']),
            'jenis_kelamin' => $row['Gender'],
            'non_taxable_income_id'  => $non_taxable_income->id ?? null,
            'join_date' => $this->transformDate($row['Tanggal Masuk']),
            'end_date' => $this->transformDate($row['Tanggal Selesai']),
            'npwp' => $row['NPWP'],
            'start_contract' => $this->transformDate($row['Mulai Kontrak']),
            'end_contract' => $this->transformDate($row['Selesai Kontrak']),
            'employee_status' => Str::snake($row['Status']),
            'leave' => $row['Jatah Cuti'],
            'position_id' => $position->id ?? null,
            'staff_type' => $row['Staff Type'],
            'religion' => $row['Agama'],
            'weight' => $row['Berat Badan'],
            'height' => $row['Tinggi'],
            'blood_type' => $row['Golongan Darah'],
            'hobby' => $row['Hobby'],
            'marriage_date' => $this->transformDate($row['Tanggal Pernikahan (Opsional)']),
            'vehicle' => $row['Kendaraan'],
            'parents_phone_number' => $row['Nomor HP Ortu'],
        ]);
    }
}
