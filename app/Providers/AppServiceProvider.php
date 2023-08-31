<?php

namespace App\Providers;

use App\Models\CmsModule;
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
        view()->composer('admin/*', function ($view) {
            $getCmsModules = CmsModule::getUserModules();
            $view->with('cmsModules',$getCmsModules);
        });
    }
}
