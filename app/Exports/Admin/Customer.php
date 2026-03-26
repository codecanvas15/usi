<?php

namespace App\Exports\Admin;

use App\Models\Customer as ModelsCustomer;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class Customer implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return ModelsCustomer::all();
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
            $row->bussiness_phone,
            $row->whatsapp_number,
            $row->fax,
            $row->lost_tolerance,
            $row->lost_tolerance_type,
            $row->website,
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
            Str::headline('Nama'),
            Str::headline('alamat'),
            Str::headline('npwp'),
            Str::headline('email'),
            Str::headline('mobile_phone'),
            Str::headline('bussiness_phone'),
            Str::headline('whatsapp_number'),
            Str::headline('fax'),
            Str::headline('lost_tolerance'),
            Str::headline('lost_tolerance_type'),
            Str::headline('website'),
            Str::headline('term of payment'),
            Str::headline('top days'),
            Str::headline('created at'),
            Str::headline('updated_at'),
        ];
    }
}
