<?php

namespace App\Providers;

use App\Models\MasterPrintAuthorization;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        \Illuminate\Support\Facades\Schema::defaultStringLength(100);

        Blade::if('authorize_print', function ($type) {
            return MasterPrintAuthorization::where('type', $type)->where('can_print', 1)->exists();
        });
    }
}
