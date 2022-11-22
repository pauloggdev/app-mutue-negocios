<?php

namespace App\Providers;

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
        if($this->app['request']->server->get('HTTP_HOST')=="127.0.0.1"){
            config(['app.url'=>"http://127.0.0.1"]);
         }else{

             config(['app.url'=>"http://".$this->app['request']->server->get('HTTP_HOST')]); 
        
         }
    }
}
