<?php

namespace App\Providers;

use App\Models\Attendance\DailyDetail;
use App\Models\Attendance\Leave;
use App\Models\Sys\Calendar;
use App\Models\Sys\HolidayConfig;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Redis;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Event'                     => [
            'App\Listeners\EventListener',
        ],

        //用户权限模块
        'Illuminate\Auth\Events\Authenticated' => [
            'App\Listeners\LogAuthenticated',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //当以下数据表发生添加或修改时,触发删除redis缓存的操作
        Leave::saved(function ($a) {
            list($y, $m) = explode('-', $a->start_time ?? $a->end_time);
            $this->delRedis("att-$y-$m");
        });

        Calendar::saved(function ($a) {
            $y = $a->year;
            $m = $a->month;
            $this->delRedis("att-$y-$m");
        });

        DailyDetail::saved(function ($a) {
            list($y, $m) = explode('-', $a->day);
            $this->delRedis("att-$y-$m");
        });

        HolidayConfig::saved(function () {
            $keys = Redis::command('keys', ['att-*']);
            foreach ($keys as $key) {
                Redis::del($key);
            }
        });
    }

    public function delRedis($key)
    {
        if (Redis::exists($key)) {
            Redis::del($key);
        }
    }
}
