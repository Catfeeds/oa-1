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

        Validator::extend('identitycards', function($attribute, $value, $parameters) {
            if (preg_match("/(^[1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$)|(^[1-9]\d{5}\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}$)/", $value)) {
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
