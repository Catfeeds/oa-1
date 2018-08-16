<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //手机号码验证
        Validator::extend('phone_number', function ($attribute, $value, $parameters, $validator) {
            if (preg_match("/(^(13\d|15[^4\D]|17[135678]|18\d)\d{8}|170[^346\D]\d{7})$/", $value)) {
                return true;
            }
            return false;
        });

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
