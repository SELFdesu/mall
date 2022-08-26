<?php

namespace App\Providers;

use App\Facades\Express\Express;
use App\Models\Category;
use App\Observers\CategoryObserver;
use App\Observers\ProductObserver;
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
        //观察category模型事件
        Category::observe(CategoryObserver::class);

    }
}
