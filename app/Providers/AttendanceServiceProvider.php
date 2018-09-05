<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2017/12/22
 * Time: 11:33
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AttendanceServiceProvider extends ServiceProvider
{
    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton(
            'attendanceservice',
            'App\Components\AttendanceService\AttendanceService'
        );
    }

    /**
     * @return array
     */
    public function provides()
    {
        return ['attendanceservice'];
    }

}