<?php

namespace App\Imports\Admin;

use App\Models\Vendor as ModelsVendor;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;

class Vendor implements ToModel, WithHeadingRow, WithStartRow
{
    /**
     * model
     *
     * @param mixed $row
     * @return mixed
     */
    public function model($row)
    {
        return new ModelsVendor([
            'nama' => $row['nama'],
            'alamat' => $row['alamat'],
            'npwp' => $row['npwp'],
            'email' => $row['email'],
            'mobile_phone' => $row['mobile_phone'],
            'business_phone' => $row['business_phone'],
            'whatsapp' => $row['whatsapp'],
            'fax' => $row['fax'],
            'nomor_rekening' => $row['nomor_rekening'],
            'jenis_bank' => $row['jenis_bank'],
            'bussiness_bank_name' => $row['bussiness_bank_name'],
            'term_of_payment' => $row['term_of_payment'],
            'top_days' => $row['top_days'],
        ]);
    }

    /**
     * start row
     *
     * @return int
     */
    public function startRow(): int
    {
        return 4;
    }
}
