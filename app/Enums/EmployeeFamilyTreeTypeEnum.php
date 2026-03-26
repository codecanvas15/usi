<?php

namespace App\Enums;

class EmployeeFamilyTreeTypeEnum
{
    public static function cases()
    {
        return [
            'inti' => [
                'value' => 'inti',
                'name' => 'Keluarga inti'
            ],
            'besar' => [
                'value' => 'besar',
                'name' => 'Keluarga besar'
            ],
        ];
    }

    public static function values()
    {
        return array_map(function ($case) {
            return $case['value'];
        }, self::cases());
    }

    public static function names()
    {
        return array_map(function ($case) {
            return $case['name'];
        }, self::cases());
    }

    public static function name($value)
    {
        return self::cases()[$value]['name'];
    }
}
