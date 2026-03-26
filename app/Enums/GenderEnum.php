<?php

namespace App\Enums;

class GenderEnum
{
    public static function cases()
    {
        return [
            'male' => [
                'name' => 'male',
                'value' => 'Laki-Laki'
            ],
            'female' => [
                'name' => 'female',
                'value' => 'Perempuan'
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
