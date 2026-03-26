<?php

namespace App\Exports\Admin;

use App\Models\Vendor as ModelsVendor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Str;

class Vendor implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return ModelsVendor::all();
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->code,
            $row->nama,
            $row->alamat,
            $row->npwp,
            $row->email,
            $row->mobile_phone,
            $row->business_phone,
            $row->whatsapp,
            $row->fax,
            $row->nomor_rekening,
            $row->jenis_bank,
            $row->bussiness_bank_name,
            $row->term_of_payment,
            $row->top_days,
            toDayDateTimeString($row->created_at),
            toDayDateTimeString($row->updated_at),
        ];
    }

    public function headings(): array
    {
        return [
            Str::headline('#'),
            Str::headline('code'),
            Str::headline('nama'),
            Str::headline('alamat'),
            Str::headline('npwp'),
            Str::headline('email'),
            Str::headline('mobile_phone'),
            Str::headline('business_phone'),
            Str::headline('whatsapp'),
            Str::headline('fax'),
            Str::headline('nomor_rekening'),
            Str::headline('jenis_bank'),
            Str::headline('bussiness_bank_name'),
            Str::headline('term_of_payment'),
            Str::headline('top_days'),
            Str::headline('created at'),
            Str::headline('updated_at'),
        ];
    }
}
