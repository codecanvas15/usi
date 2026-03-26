<?php

namespace App\Enums;

class EmployeeStatusEnum
{
    public static function cases()
    {
        return [
            'active' => [
                'name' => 'active',
                'value' => 'active'
            ],
            'non_active' => [
                'name' => 'non_active',
                'value' => 'non active'
            ]
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
