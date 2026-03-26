<?php

namespace App\Imports\Admin;

use App\Models\User as ModelsUser;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class User implements ToModel, WithHeadingRow
{
    public function model($row)
    {
        $model = new ModelsUser([
            'name' => $row['name'],
            'email' => $row['email'],
            'password' => $row['password'],
        ]);

        $model->syncRoles($row['role']);

        return $model;
    }
}
