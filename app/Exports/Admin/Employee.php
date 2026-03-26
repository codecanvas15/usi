<?php

namespace App\Exports\Admin;

use App\Models\Employee as ModelsEmployee;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class Employee implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return ModelsEmployee::all();
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->NIK,
            $row->name,
            $row->email,
            $row->nomor_telepone,
            $row->alamat,
            $row->alamat_domisili,
            $row->tempat_lahir,
            $row->tanggal_lahir,
            $row->jenis_kelamin,
            $row->non_taxable_income->note ?? '-',
            $row->nomor_ktp,
            $row->npwp,
            Storage::url($row->foto_id),
            $row->join_date,
            $row->end_date,
            $row->nomor_bpjs,
            $row->bpjs_dues,
            $row->leave,
            $row->branch?->name ?? "Undifined",
            $row->division?->name ?? "Undifined",
            $row->position?->nama ?? "Undifined",
            $row->employment_status?->name ?? "Undifined",
            $row->education?->name ?? "Undifined",
            $row->degree?->name ?? "Undifined",
            $row->employee_status,
            $row->start_contract ?? "Tidak ada",
            $row->end_contract ?? "Tidak ada",
            $row->deposit_asset_employee,
            $row->deposit_asset_company,
            $row->exit_interview,
            toDayDateTimeString($row->created_at),
            toDayDateTimeString($row->updated_at),
        ];
    }

    public function headings(): array
    {
        return [
            Str::headline('id'),
            Str::headline('NIK'),
            Str::headline('nama'),
            Str::headline('email'),
            Str::headline('nomor hp'),
            Str::headline('alamat '),
            Str::headline('alamat_domisili'),
            Str::headline('tempat_lahir'),
            Str::headline('tanggal_lahir'),
            Str::headline('jenis_kelamin'),
            Str::headline('status_pernikahan'),
            Str::headline('nomor_ktp'),
            Str::headline('npwp'),
            Str::headline('foto identitas'),
            Str::headline('tanggal masuk'),
            Str::headline('tanggal keluar'),
            Str::headline('nomor_bpjs'),
            Str::headline('bpjs_dues'),
            Str::headline('jatah cuti'),
            Str::headline('cabang'),
            Str::headline('divisi'),
            Str::headline('posisi'),
            Str::headline('status kepegawaian'),
            Str::headline('pendidikan'),
            Str::headline('jurusan'),
            Str::headline('status pegawai'),
            Str::headline('mulai kontrak'),
            Str::headline('selesai kontrak'),
            Str::headline('deposit_asset_employee'),
            Str::headline('deposit_asset_company'),
            Str::headline('exit_interview'),
            Str::headline('created_at'),
            Str::headline('updated_at'),
        ];
    }
}
