<?php

namespace App\Enums;

class EmployeeTypeEnum
{
    public static function cases()
    {
        return [
            'staff' => [
                'name' => 'staff',
                'value' => 'staff'
            ],
            'crew' => [
                'name' => 'crew',
                'value' => 'crew'
            ],
            'driver' => [
                'name' => 'driver',
                'value' => 'driver'
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
