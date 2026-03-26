<?php

namespace App\Imports\Admin;

use App\Models\Customer as ModelsCustomer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class Customer implements ToModel, WithHeadingRow
{
    public function model($row)
    {
        return new ModelsCustomer([
            'nama' => $row['nama'],
            'alamat' => $row['alamat'],
            'npwp' => $row['npwp'],
            'email' => $row['email'],
            'mobile_phone' => $row['mobile_phone'],
            'bussiness_phone' => $row['bussiness_phone'],
            'whatsapp_number' => $row['whatsapp_number'],
            'fax' => $row['fax'],
            'lost_tolerance' => $row['lost_tolerance'],
            'lost_tolerance_type' => $row['lost_tolerance_type'],
            'website' => $row['website'],
            'term_of_payment' => $row['term_of_payment'],
            'top_days' => $row['top_days'],
        ]);
    }
}
