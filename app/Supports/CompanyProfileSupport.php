<?php

namespace App\Supports;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class CompanyProfileSupport
{
    /**
     * Get the company from database
     */
    private static function getCompany()
    {
        return \App\Models\Company::first();
    }

    /**
     * Set the company from database to cache
     */
    private static function setCompany()
    {
        $company = self::getCompany();

        // if (is_null($company)) {
        //     Artisan::call('db:seed', ['--class' => 'CompanySeeder']);
        //     $company = self::getCompany();
        // }

        Cache::put('companyProfile', $company);
    }

    /**
     * re new company cache
     */
    public static function renewCompany()
    {
        self::setCompany();
    }

    /**
     * Get the company name cache
     */
    public static function company()
    {
        if (!Cache::has('companyProfile')) {
            self::setCompany();
        }

        return Cache::get('companyProfile');
    }
}
