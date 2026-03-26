<?php

namespace App\Enums;

class ReligionEnum
{
    public static function cases()
    {
        return [
            'islam' => [
                'name' => 'islam',
                'value' => 'islam'
            ],
            'kristen' => [
                'name' => 'kristen',
                'value' => 'kristen'
            ],
            'katolik' => [
                'name' => 'katolik',
                'value' => 'katolik'
            ],
            'hindu' => [
                'name' => 'hindu',
                'value' => 'hindu'
            ],
            'budha' => [
                'name' => 'budha',
                'value' => 'budha'
            ],
            'konghucu' => [
                'name' => 'konghucu',
                'value' => 'konghucu'
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
