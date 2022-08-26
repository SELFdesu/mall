<?php

namespace App\Providers;

use App\Facades\Express\Express;
use Illuminate\Support\ServiceProvider;

class ExpressServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton('Express',Express::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
