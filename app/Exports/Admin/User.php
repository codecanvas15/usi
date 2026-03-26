<?php

namespace App\Exports\Admin;

use App\Models\User as ModelsUser;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class User implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return ModelsUser::all();
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->name,
            $row->email,
            $row->getRoleNames(),
            toDayDateTimeString($row->created_at),
            toDayDateTimeString($row->updated_at),
        ];
    }

    public function headings(): array
    {
        return [
            Str::headline('#'),
            Str::headline('name'),
            Str::headline('email'),
            Str::headline('role'),
            Str::headline('created at'),
            Str::headline('updated_at'),
        ];
    }
}
